<?php

namespace App\Services;

use Illuminate\Support\Str;

class AmazonProductExtractor
{
    /**
     * Supported Amazon Top-Level Domains
     */
    protected array $supportedDomains = [
        'amazon.in',
        'amazon.com',
        'amazon.co.uk',
        'amazon.ae',
        'amazon.sa',
        'amazon.de',
        'amazon.fr',
        'amazon.ca',
        'amazon.com.au',
    ];

    /**
     * Determine if a given URL is an Amazon product URL.
     */
    public function isAmazonUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) return false;

        $host = strtolower(preg_replace('/^www\./', '', $host));
        foreach ($this->supportedDomains as $domain) {
            if ($host === $domain || str_ends_with($host, '.' . $domain)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Normalize Amazon URL, remove tracking/referral params, and extract ASIN.
     */
    public function normalizeUrl(string $url): array
    {
        $parsed = parse_url($url);
        $host = strtolower($parsed['host'] ?? '');
        $hostClean = preg_replace('/^www\./', '', $host);
        $path = $parsed['path'] ?? '';

        $asin = null;
        // Patterns to match ASIN: /dp/B0..., /gp/product/B0..., /product/B0..., /d/B0...
        if (preg_match('/(?:\/dp\/|\/gp\/product\/|\/product\/|\/d\/)([A-Z0-9]{10})/i', $path, $matches)) {
            $asin = strtoupper($matches[1]);
        } elseif (preg_match('/(?:\/)([A-Z0-9]{10})(?:\/|\?|$)/i', $path, $matches)) {
            // Check if looks like valid ASIN (10 alphanumeric, often starts with B)
            if (preg_match('/^B[A-Z0-9]{9}$/i', $matches[1]) || strlen($matches[1]) === 10) {
                $asin = strtoupper($matches[1]);
            }
        }

        $scheme = $parsed['scheme'] ?? 'https';
        $canonicalUrl = $asin ? "{$scheme}://{$host}/dp/{$asin}" : $url;

        return [
            'original_url'  => $url,
            'canonical_url' => $canonicalUrl,
            'domain'        => $hostClean,
            'asin'          => $asin,
            'is_amazon'     => $this->isAmazonUrl($url),
        ];
    }

    /**
     * Main Extraction Engine applying the 6-Layer Strategy.
     */
    public function extract(string $html, string $url): array
    {
        $urlInfo = $this->normalizeUrl($url);
        $asin = $urlInfo['asin'];

        $sources = [];
        $confidenceScores = [];
        $warnings = [];

        // ----------------------------------------------------
        // LAYER 1 & 2: JSON-LD Structured Data
        // ----------------------------------------------------
        $jsonLdData = $this->parseJsonLd($html);

        // ----------------------------------------------------
        // 1. EXTRACT ASIN (if not found in URL)
        // ----------------------------------------------------
        if (!$asin) {
            if (preg_match('/name=["\']ASIN["\']\s+value=["\']([A-Z0-9]{10})["\']/i', $html, $m)) {
                $asin = strtoupper($m[1]);
                $sources['asin'] = 'hidden_input';
            } elseif (preg_match('/data-asin=["\']([A-Z0-9]{10})["\']/i', $html, $m)) {
                $asin = strtoupper($m[1]);
                $sources['asin'] = 'dom_data_asin';
            }
        } else {
            $sources['asin'] = 'url_path';
        }

        // ----------------------------------------------------
        // 2. EXTRACT TITLE
        // ----------------------------------------------------
        $title = '';
        if (preg_match('/<span\s+id=["\']productTitle["\'][^>]*>(.*?)<\/span>/is', $html, $m)) {
            $title = trim(html_entity_decode(strip_tags($m[1])));
            $sources['title'] = 'dom_product_title';
            $confidenceScores['title'] = 98;
        } elseif (!empty($jsonLdData['name'])) {
            $title = trim(html_entity_decode($jsonLdData['name']));
            $sources['title'] = 'json_ld';
            $confidenceScores['title'] = 95;
        } elseif (preg_match('/<h1\s+id=["\']title["\'][^>]*>(.*?)<\/h1>/is', $html, $m)) {
            $title = trim(html_entity_decode(strip_tags($m[1])));
            $sources['title'] = 'dom_h1_title';
            $confidenceScores['title'] = 90;
        } elseif (preg_match('/<meta\s+property=["\']og:title["\']\s+content=["\'](.*?)["\']/i', $html, $m)) {
            $title = trim(html_entity_decode($m[1]));
            $sources['title'] = 'og_title';
            $confidenceScores['title'] = 85;
        } elseif (preg_match('/<title>(.*?)<\/title>/is', $html, $m)) {
            // Clean title: "Amazon.in: Buy Product Name Online..."
            $rawTitle = html_entity_decode($m[1]);
            $rawTitle = preg_replace('/^Amazon\.[a-z.]+\s*:\s*(?:Buy\s+)?/i', '', $rawTitle);
            $rawTitle = preg_replace('/\s*:\s*(?:Electronics|Clothing|Home|Books|Amazon).*$/i', '', $rawTitle);
            $title = trim($rawTitle);
            $sources['title'] = 'html_title';
            $confidenceScores['title'] = 75;
        }

        if (empty($title)) {
            $title = 'Amazon Imported Product' . ($asin ? " ({$asin})" : '');
            $confidenceScores['title'] = 40;
            $warnings[] = 'Product title could not be cleanly identified from Amazon markup.';
        }

        // ----------------------------------------------------
        // 3. EXTRACT PRICE, MRP & CURRENCY
        // ----------------------------------------------------
        $price = 0.00;
        $mrp = 0.00;
        $currency = 'INR';

        // Detect currency symbol
        if (str_contains($urlInfo['domain'], 'amazon.in') || str_contains($html, '₹') || str_contains($html, 'INR')) {
            $currency = 'INR';
        } elseif (str_contains($urlInfo['domain'], 'amazon.co.uk') || str_contains($html, '£')) {
            $currency = 'GBP';
        } elseif (str_contains($urlInfo['domain'], 'amazon.ae') || str_contains($html, 'AED')) {
            $currency = 'AED';
        } elseif (str_contains($urlInfo['domain'], 'amazon.sa') || str_contains($html, 'SAR')) {
            $currency = 'SAR';
        } elseif (str_contains($urlInfo['domain'], 'amazon.de') || str_contains($urlInfo['domain'], 'amazon.fr') || str_contains($html, '€')) {
            $currency = 'EUR';
        } else {
            $currency = 'USD';
        }

        // Selling Price Extraction (Multi-Tiered Priority)
        // 1. .priceToPay .a-offscreen (matches class="... priceToPay ...")
        if (preg_match('/class=["\'][^"\']*priceToPay[^"\']*["\'][^>]*>.*?<span\s+class=["\']a-offscreen["\'][^>]*>(.*?)<\/span>/is', $html, $m)) {
            $p = $this->cleanPriceValue($m[1]);
            if ($p > 0) {
                $price = $p;
                $sources['price'] = 'dom_price_to_pay';
                $confidenceScores['price'] = 98;
            }
        }

        // 2. .priceToPay .a-price-whole and .a-price-fraction
        if ($price <= 0 && preg_match('/class=["\'][^"\']*priceToPay[^"\']*["\'][^>]*>.*?<span\s+class=["\']a-price-whole["\'][^>]*>(.*?)<\/span>/is', $html, $m)) {
            $whole = $this->cleanPriceValue($m[1]);
            $fraction = 0;
            if (preg_match('/class=["\'][^"\']*priceToPay[^"\']*["\'][^>]*>.*?<span\s+class=["\']a-price-fraction["\'][^>]*>(.*?)<\/span>/is', $html, $fm)) {
                $fraction = ((float)preg_replace('/[^\d]/', '', $fm[1])) / 100;
            }
            if ($whole > 0) {
                $price = $whole + $fraction;
                $sources['price'] = 'dom_price_whole_fraction';
                $confidenceScores['price'] = 98;
            }
        }

        // 3. #corePriceDisplay_desktop_feature_div .a-price .a-offscreen
        if ($price <= 0 && preg_match('/id=["\']corePriceDisplay_desktop_feature_div["\'][^>]*>.*?<span\s+class=["\'][^"\']*a-price\b[^"\']*["\'][^>]*>.*?<span\s+class=["\']a-offscreen["\'][^>]*>(.*?)<\/span>/is', $html, $m)) {
            $p = $this->cleanPriceValue($m[1]);
            if ($p > 0) {
                $price = $p;
                $sources['price'] = 'dom_core_price_display';
                $confidenceScores['price'] = 95;
            }
        }

        // 4. #corePrice_desktop .a-price .a-offscreen
        if ($price <= 0 && preg_match('/id=["\']corePrice_desktop["\'][^>]*>.*?<span\s+class=["\'][^"\']*a-price\b[^"\']*["\'][^>]*>.*?<span\s+class=["\']a-offscreen["\'][^>]*>(.*?)<\/span>/is', $html, $m)) {
            $p = $this->cleanPriceValue($m[1]);
            if ($p > 0) {
                $price = $p;
                $sources['price'] = 'dom_core_price_desktop';
                $confidenceScores['price'] = 95;
            }
        }

        // 5. #apex_desktop .a-price .a-offscreen
        if ($price <= 0 && preg_match('/id=["\']apex_desktop["\'][^>]*>.*?<span\s+class=["\'][^"\']*a-price\b[^"\']*["\'][^>]*>.*?<span\s+class=["\']a-offscreen["\'][^>]*>(.*?)<\/span>/is', $html, $m)) {
            $p = $this->cleanPriceValue($m[1]);
            if ($p > 0) {
                $price = $p;
                $sources['price'] = 'dom_apex_desktop';
                $confidenceScores['price'] = 95;
            }
        }

        // 6. #priceblock_dealprice or #priceblock_ourprice or #priceblock_saleprice
        if ($price <= 0 && preg_match('/id=["\'](?:priceblock_dealprice|priceblock_ourprice|priceblock_saleprice)["\'][^>]*>(.*?)<\/span>/is', $html, $m)) {
            $p = $this->cleanPriceValue($m[1]);
            if ($p > 0) {
                $price = $p;
                $sources['price'] = 'dom_priceblock';
                $confidenceScores['price'] = 94;
            }
        }

        // 7. JSON-LD offers price
        if ($price <= 0 && !empty($jsonLdData['offers']['price'])) {
            $p = (float)$jsonLdData['offers']['price'];
            if ($p > 0) {
                $price = $p;
                $sources['price'] = 'json_ld_offers';
                $confidenceScores['price'] = 90;
                if (!empty($jsonLdData['offers']['priceCurrency'])) {
                    $currency = strtoupper($jsonLdData['offers']['priceCurrency']);
                }
            }
        }

        // 8. General .a-price-whole and .a-price-fraction on page (main buying box)
        if ($price <= 0 && preg_match('/<span\s+class=["\']a-price-whole["\'][^>]*>(.*?)<\/span>/is', $html, $m)) {
            $whole = $this->cleanPriceValue($m[1]);
            $fraction = 0;
            if (preg_match('/<span\s+class=["\']a-price-fraction["\'][^>]*>(.*?)<\/span>/is', $html, $fm)) {
                $fraction = ((float)preg_replace('/[^\d]/', '', $fm[1])) / 100;
            }
            if ($whole > 0) {
                $price = $whole + $fraction;
                $sources['price'] = 'dom_general_price_whole';
                $confidenceScores['price'] = 85;
            }
        }

        // 9. OpenGraph product price
        if ($price <= 0 && preg_match('/<meta\s+property=["\']product:price:amount["\']\s+content=["\'](.*?)["\']/i', $html, $m)) {
            $price = (float)$m[1];
            $sources['price'] = 'og_price';
            $confidenceScores['price'] = 80;
        }

        // MRP / List Price Extraction (Strikethrough price)
        // 1. Basis price (class="... basisPrice ...")
        if (preg_match('/class=["\'][^"\']*basisPrice[^"\']*["\'][^>]*>.*?<span\s+class=["\']a-offscreen["\'][^>]*>(.*?)<\/span>/is', $html, $m)) {
            $mVal = $this->cleanPriceValue($m[1]);
            if ($mVal > 0) {
                $mrp = $mVal;
                $sources['mrp'] = 'dom_basis_price';
            }
        }

        // 2. data-a-strike="true"
        if ($mrp <= 0 && preg_match('/data-a-strike=["\']true["\'][^>]*>.*?<span\s+class=["\']a-offscreen["\'][^>]*>(.*?)<\/span>/is', $html, $m)) {
            $mVal = $this->cleanPriceValue($m[1]);
            if ($mVal > 0) {
                $mrp = $mVal;
                $sources['mrp'] = 'dom_strike_price';
            }
        }

        // 3. .a-text-price .a-offscreen
        if ($mrp <= 0 && preg_match('/<span\s+class=["\'][^"\']*a-text-price[^"\']*["\'][^>]*>.*?<span\s+class=["\']a-offscreen["\'][^>]*>(.*?)<\/span>/is', $html, $m)) {
            $mVal = $this->cleanPriceValue($m[1]);
            if ($mVal > $price) {
                $mrp = $mVal;
                $sources['mrp'] = 'dom_text_strikethrough_price';
            }
        }

        // 4. #priceblock_msrp or #listPrice
        if ($mrp <= 0 && preg_match('/id=["\'](?:priceblock_msrp|listPrice)["\'][^>]*>(.*?)<\/span>/is', $html, $m)) {
            $mVal = $this->cleanPriceValue($m[1]);
            if ($mVal > $price) {
                $mrp = $mVal;
                $sources['mrp'] = 'dom_msrp_block';
            }
        }

        // If MRP is less than or equal to selling price, reset MRP
        if ($mrp <= $price) {
            $mrp = 0.00;
        }

        $discountPercent = 0;
        if ($mrp > 0 && $price > 0 && $mrp > $price) {
            $discountPercent = round((($mrp - $price) / $mrp) * 100);
        }

        if ($price <= 0) {
            $price = 0.00;
            $confidenceScores['price'] = 30;
            $warnings[] = 'Selling price could not be automatically detected. Please enter price manually.';
        }

        // ----------------------------------------------------
        // 4. EXTRACT BRAND (Distinguished from seller)
        // ----------------------------------------------------
        $brand = '';
        if (preg_match('/<a\s+id=["\']bylineInfo["\'][^>]*>(.*?)<\/a>/is', $html, $m)) {
            $rawBrand = trim(strip_tags($m[1]));
            $rawBrand = preg_replace('/^(?:Brand:\s*|Visit the\s*|\s*Store)$/i', '', $rawBrand);
            $rawBrand = preg_replace('/^Visit the\s+(.*?)\s+Store$/i', '$1', $rawBrand);
            $brand = trim($rawBrand);
            $sources['brand'] = 'dom_byline_info';
            $confidenceScores['brand'] = 95;
        } elseif (!empty($jsonLdData['brand'])) {
            $brand = is_array($jsonLdData['brand']) ? ($jsonLdData['brand']['name'] ?? '') : $jsonLdData['brand'];
            $sources['brand'] = 'json_ld_brand';
            $confidenceScores['brand'] = 95;
        } elseif (preg_match('/<tr\s+class=["\']po-brand["\'][^>]*>.*?<span\s+class=["\']a-span9["\'][^>]*>(.*?)<\/span>/is', $html, $m)) {
            $brand = trim(strip_tags($m[1]));
            $sources['brand'] = 'dom_po_brand';
            $confidenceScores['brand'] = 92;
        } elseif (preg_match('/<div\s+id=["\']productOverview_feature_div["\'][^>]*>.*?Brand.*?<span[^>]*>(.*?)<\/span>/is', $html, $m)) {
            $brand = trim(strip_tags($m[1]));
            $sources['brand'] = 'dom_product_overview_brand';
            $confidenceScores['brand'] = 85;
        }

        if (empty($brand)) {
            $brand = 'Generic';
            $confidenceScores['brand'] = 60;
        }

        // ----------------------------------------------------
        // 5. EXTRACT HIGH-RESOLUTION GALLERY IMAGES
        // ----------------------------------------------------
        $images = [];

        // Parse data-a-dynamic-image JSON
        if (preg_match('/data-a-dynamic-image=["\'](\{.*?\})["\']/is', $html, $m)) {
            $dynamicImages = json_decode(html_entity_decode($m[1]), true);
            if (is_array($dynamicImages)) {
                // Sort by highest resolution
                $sorted = [];
                foreach ($dynamicImages as $imgUrl => $dimensions) {
                    $area = is_array($dimensions) && count($dimensions) >= 2 ? ($dimensions[0] * $dimensions[1]) : 0;
                    $sorted[$imgUrl] = $area;
                }
                arsort($sorted);
                foreach (array_keys($sorted) as $imgUrl) {
                    if ($this->isValidProductImageUrl($imgUrl)) {
                        $images[] = $imgUrl;
                    }
                }
                $sources['images'] = 'dom_dynamic_image_json';
                $confidenceScores['images'] = 98;
            }
        }

        // Fallback: landingImage element
        if (empty($images) && preg_match('/<img\s+[^>]*id=["\']landingImage["\'][^>]*>/is', $html, $m)) {
            if (preg_match('/data-old-hires=["\'](.*?)["\']/i', $m[0], $srcMatch) && !empty($srcMatch[1])) {
                $images[] = $srcMatch[1];
            } elseif (preg_match('/src=["\'](.*?)["\']/i', $m[0], $srcMatch) && !empty($srcMatch[1])) {
                $images[] = $srcMatch[1];
            }
            $sources['images'] = 'dom_landing_image';
            $confidenceScores['images'] = 90;
        }

        // Fallback: JSON-LD images
        if (empty($images) && !empty($jsonLdData['image'])) {
            $ldImages = is_array($jsonLdData['image']) ? $jsonLdData['image'] : [$jsonLdData['image']];
            foreach ($ldImages as $imgUrl) {
                if ($this->isValidProductImageUrl($imgUrl)) {
                    $images[] = $imgUrl;
                }
            }
            $sources['images'] = 'json_ld_image';
            $confidenceScores['images'] = 88;
        }

        // Fallback: OpenGraph image
        if (empty($images) && preg_match('/<meta\s+property=["\']og:image["\']\s+content=["\'](.*?)["\']/i', $html, $m)) {
            if ($this->isValidProductImageUrl($m[1])) {
                $images[] = $m[1];
                $sources['images'] = 'og_image';
                $confidenceScores['images'] = 80;
            }
        }

        $images = array_values(array_unique($images));
        $primaryImage = count($images) > 0 ? $images[0] : 'assets/img/ecommerce-images/product-1.png';

        // ----------------------------------------------------
        // 6. EXTRACT BULLET POINTS & FEATURES ("About this item")
        // ----------------------------------------------------
        $bullets = [];
        if (preg_match('/<div\s+id=["\']feature-bullets["\'][^>]*>(.*?)<\/div>/is', $html, $m)) {
            if (preg_match_all('/<li[^>]*>\s*<span\s+class=["\']a-list-item["\'][^>]*>(.*?)<\/span>\s*<\/li>/is', $m[1], $itemMatches)) {
                foreach ($itemMatches[1] as $item) {
                    $cleanedBullet = trim(html_entity_decode(strip_tags($item)));
                    if (!empty($cleanedBullet) && !str_starts_with(strtolower($cleanedBullet), 'make sure this fits')) {
                        $bullets[] = $cleanedBullet;
                    }
                }
                $sources['bullets'] = 'dom_feature_bullets';
                $confidenceScores['bullets'] = 95;
            }
        }

        // ----------------------------------------------------
        // 7. EXTRACT DESCRIPTION
        // ----------------------------------------------------
        $description = '';
        if (preg_match('/<div\s+id=["\']productDescription["\'][^>]*>(.*?)<\/div>/is', $html, $m)) {
            $rawDesc = $m[1];
            $formatted = preg_replace('/<\/(?:p|div|li|tr|h[1-6])>/i', "\n\n", $rawDesc);
            $formatted = preg_replace('/<br\s*\/?>/i', "\n", $formatted);
            $description = trim(html_entity_decode(strip_tags($formatted)));
            $description = preg_replace("/\n{3,}/", "\n\n", $description);
            $sources['description'] = 'dom_product_description';
            $confidenceScores['description'] = 92;
        } elseif (!empty($jsonLdData['description'])) {
            $description = trim(html_entity_decode(strip_tags($jsonLdData['description'])));
            $sources['description'] = 'json_ld_description';
            $confidenceScores['description'] = 90;
        } elseif (count($bullets) > 0) {
            $description = implode("\n\n• ", array_merge(['About this item:'], $bullets));
            $sources['description'] = 'bullets_composite';
            $confidenceScores['description'] = 85;
        } elseif (preg_match('/<meta\s+property=["\']og:description["\']\s+content=["\'](.*?)["\']/i', $html, $m)) {
            $description = trim(html_entity_decode(strip_tags($m[1])));
            $sources['description'] = 'og_description';
            $confidenceScores['description'] = 75;
        }

        // ----------------------------------------------------
        // 8. EXTRACT TECHNICAL SPECIFICATIONS
        // ----------------------------------------------------
        $specifications = [];
        if (preg_match('/<table\s+id=["\']productDetails_techSpec_section_1["\'][^>]*>(.*?)<\/table>/is', $html, $m)) {
            if (preg_match_all('/<tr[^>]*>\s*<th[^>]*>(.*?)<\/th>\s*<td[^>]*>(.*?)<\/td>\s*<\/tr>/is', $m[1], $rowMatches)) {
                foreach ($rowMatches[1] as $idx => $th) {
                    $key = trim(html_entity_decode(strip_tags($th)));
                    $val = trim(html_entity_decode(strip_tags($rowMatches[2][$idx])));
                    if (!empty($key) && !empty($val)) {
                        $specifications[$key] = $val;
                    }
                }
                $sources['specifications'] = 'dom_tech_spec_table';
                $confidenceScores['specifications'] = 95;
            }
        }

        // Additional specifications from Product Overview table
        if (preg_match('/<div\s+id=["\']productOverview_feature_div["\'][^>]*>(.*?)<\/div>/is', $html, $m)) {
            if (preg_match_all('/<tr\s+class=["\']po-([^"\']+)["\'][^>]*>\s*<td[^>]*>(.*?)<\/td>\s*<td[^>]*>(.*?)<\/td>\s*<\/tr>/is', $m[1], $rowMatches)) {
                foreach ($rowMatches[2] as $idx => $tdLabel) {
                    $key = trim(html_entity_decode(strip_tags($tdLabel)));
                    $val = trim(html_entity_decode(strip_tags($rowMatches[3][$idx])));
                    if (!empty($key) && !empty($val) && !isset($specifications[$key])) {
                        $specifications[$key] = $val;
                    }
                }
            }
        }

        // ----------------------------------------------------
        // 9. EXTRACT RATING & REVIEW COUNT
        // ----------------------------------------------------
        $rating = 0.0;
        $reviewCount = 0;

        if (preg_match('/<span\s+id=["\']acrPopover["\'][^>]*title=["\'](.*?)["\']/is', $html, $m)) {
            if (preg_match('/([0-9.]+)\s+out\s+of/i', $m[1], $rm)) {
                $rating = (float)$rm[1];
            }
        } elseif (!empty($jsonLdData['aggregateRating']['ratingValue'])) {
            $rating = (float)$jsonLdData['aggregateRating']['ratingValue'];
        }

        if (preg_match('/<span\s+id=["\']acrCustomerReviewText["\'][^>]*>(.*?)<\/span>/is', $html, $m)) {
            $cleanedCount = preg_replace('/[^0-9]/', '', $m[1]);
            $reviewCount = (int)$cleanedCount;
        } elseif (!empty($jsonLdData['aggregateRating']['reviewCount'])) {
            $reviewCount = (int)$jsonLdData['aggregateRating']['reviewCount'];
        }

        // ----------------------------------------------------
        // 10. EXTRACT AVAILABILITY
        // ----------------------------------------------------
        $availability = 'In Stock';
        if (preg_match('/<div\s+id=["\']availability["\'][^>]*>(.*?)<\/div>/is', $html, $m)) {
            $rawAvail = strtolower(strip_tags($m[1]));
            if (str_contains($rawAvail, 'currently unavailable') || str_contains($rawAvail, 'out of stock')) {
                $availability = 'Out of Stock';
            } elseif (str_contains($rawAvail, 'in stock')) {
                $availability = 'In Stock';
            } elseif (str_contains($rawAvail, 'temporarily out of stock')) {
                $availability = 'Temporarily Unavailable';
            }
        }

        // ----------------------------------------------------
        // 11. EXTRACT BREADCRUMB / CATEGORY
        // ----------------------------------------------------
        $categoryName = 'General';
        if (preg_match('/<div\s+id=["\']wayfinding-breadcrumbs_feature_div["\'][^>]*>(.*?)<\/div>/is', $html, $m)) {
            if (preg_match_all('/<a[^>]*class=["\']a-link-normal\s+a-color-tertiary["\'][^>]*>(.*?)<\/a>/is', $m[1], $crumbMatches)) {
                $crumbs = array_map('trim', array_map('strip_tags', $crumbMatches[1]));
                if (count($crumbs) > 0) {
                    $categoryName = end($crumbs); // Use most specific sub-category
                }
            }
        }

        // ----------------------------------------------------
        // CALCULATE OVERALL CONFIDENCE SCORE (0-100)
        // ----------------------------------------------------
        $weights = ['title' => 0.35, 'price' => 0.35, 'brand' => 0.10, 'images' => 0.10, 'description' => 0.10];
        $totalConfidence = 0;
        foreach ($weights as $field => $weight) {
            $totalConfidence += ($confidenceScores[$field] ?? 50) * $weight;
        }
        $overallConfidence = (int) round($totalConfidence);

        return [
            'name'                => $title,
            'asin'                => $asin,
            'sku'                 => $asin ? "AMZ-{$asin}" : ('AKM-' . strtoupper(Str::random(6))),
            'barcode'             => (string) rand(100000000000, 999999999999),
            'brand'               => $brand,
            'category_name'       => $categoryName,
            'price'               => $price,
            'compare_at_price'    => $mrp,
            'discount_percent'    => $discountPercent,
            'currency'            => $currency,
            'image'               => $primaryImage,
            'gallery_images'      => $images,
            'description'         => $description,
            'bullet_points'       => $bullets,
            'specifications'      => $specifications,
            'rating'              => $rating,
            'review_count'        => $reviewCount,
            'availability'        => $availability,
            'confidence_score'    => $overallConfidence,
            'confidence_breakdown'=> $confidenceScores,
            'sources'             => $sources,
            'warnings'            => $warnings,
            'qty'                 => 10,
            'is_amazon'           => true,
            'canonical_url'       => $urlInfo['canonical_url'],
            'domain'              => $urlInfo['domain'],
        ];
    }

    /**
     * Parse JSON-LD blocks from HTML.
     */
    protected function parseJsonLd(string $html): array
    {
        if (preg_match_all('/<script\s+type=["\']application\/ld\+json["\']>(.*?)<\/script>/is', $html, $matches)) {
            foreach ($matches[1] as $jsonStr) {
                $decoded = json_decode(trim($jsonStr), true);
                if (is_array($decoded)) {
                    if (($decoded['@type'] ?? '') === 'Product') {
                        return $decoded;
                    }
                    if (isset($decoded['@graph']) && is_array($decoded['@graph'])) {
                        foreach ($decoded['@graph'] as $item) {
                            if (($item['@type'] ?? '') === 'Product') {
                                return $item;
                            }
                        }
                    }
                }
            }
        }
        return [];
    }

    public function cleanPriceValue(string $raw): float
    {
        $cleaned = html_entity_decode(strip_tags($raw));
        // 1. Remove commas (thousands separators)
        $cleaned = str_replace(',', '', $cleaned);
        // 2. Remove currency symbols and non-breaking spaces
        $cleaned = str_replace(['₹', '$', '£', '€', 'INR', 'USD', 'GBP', 'EUR', 'AED', 'SAR', '&nbsp;', "\xc2\xa0"], '', $cleaned);
        $cleaned = trim($cleaned);

        // 3. Match complete price number (e.g. "71999.00" or "1695")
        if (preg_match('/\d+(?:\.\d{1,2})?/', $cleaned, $match)) {
            return (float) $match[0];
        }

        return 0.00;
    }

    /**
     * Check if an image URL is a valid product image (and not a 1x1 tracker or sprite).
     */
    protected function isValidProductImageUrl(string $url): bool
    {
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) {
            return false;
        }
        $lower = strtolower($url);
        if (str_contains($lower, 'transparent-pixel') ||
            str_contains($lower, 'sprite') ||
            str_contains($lower, 'play-icon') ||
            str_contains($lower, '360_icon') ||
            str_contains($lower, 'nav-logo') ||
            str_ends_with($lower, '.gif')) {
            return false;
        }
        return true;
    }
}
