<?php

namespace App\Services\Extractors;

use Illuminate\Support\Str;

class FlipkartProductExtractor
{
    /**
     * Determine if a given URL is from Flipkart.
     */
    public function isFlipkartUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) return false;
        $host = strtolower(preg_replace('/^www\./', '', $host));
        return $host === 'flipkart.com' || str_ends_with($host, '.flipkart.com');
    }

    /**
     * Normalize Flipkart URL and extract FSN / product ID.
     */
    public function normalizeUrl(string $url): array
    {
        $parsed = parse_url($url);
        $host = strtolower($parsed['host'] ?? 'www.flipkart.com');
        $path = $parsed['path'] ?? '';
        parse_str($parsed['query'] ?? '', $queryParams);

        $pid = $queryParams['pid'] ?? null;
        if (!$pid && preg_match('/(?:itm|p\/)([A-Za-z0-9]+)/i', $path, $m)) {
            $pid = $m[1];
        }

        $canonicalUrl = $pid ? "https://www.flipkart.com/product/p/itme?pid={$pid}" : $url;

        return [
            'original_url'  => $url,
            'canonical_url' => $canonicalUrl,
            'domain'        => 'flipkart.com',
            'pid'           => $pid,
            'platform'      => 'flipkart',
        ];
    }

    /**
     * Extract structured product data from Flipkart HTML.
     */
    public function extract(string $html, string $url): array
    {
        $urlInfo = $this->normalizeUrl($url);
        $pid = $urlInfo['pid'];

        $sources = [];
        $confidenceScores = [];
        $warnings = [];

        // 1. JSON-LD parsing
        $jsonLd = $this->parseJsonLd($html);

        // 2. Title Extraction
        $title = '';
        if (preg_match('/<span\s+class=["\'][^"\']*B_NuCI[^"\']*["\'][^>]*>(.*?)<\/span>/is', $html, $m)) {
            $title = trim(html_entity_decode(strip_tags($m[1])));
            $sources['title'] = 'dom_b_nuci_title';
            $confidenceScores['title'] = 98;
        } elseif (preg_match('/<h1\s+class=["\'][^"\']*_6EBuvT[^"\']*["\'][^>]*>(.*?)<\/h1>/is', $html, $m)) {
            $title = trim(html_entity_decode(strip_tags($m[1])));
            $sources['title'] = 'dom_6ebuvt_title';
            $confidenceScores['title'] = 95;
        } elseif (!empty($jsonLd['name'])) {
            $title = trim(html_entity_decode($jsonLd['name']));
            $sources['title'] = 'json_ld';
            $confidenceScores['title'] = 92;
        } elseif (preg_match('/<meta\s+property=["\']og:title["\']\s+content=["\'](.*?)["\']/i', $html, $m)) {
            $title = trim(html_entity_decode(explode('|', $m[1])[0]));
            $sources['title'] = 'og_title';
            $confidenceScores['title'] = 85;
        }

        if (empty($title)) {
            $title = 'Flipkart Imported Product' . ($pid ? " ({$pid})" : '');
            $confidenceScores['title'] = 40;
            $warnings[] = 'Product title could not be cleanly identified from Flipkart markup.';
        }

        // 3. Price & MRP Extraction
        $price = 0.00;
        $mrp = 0.00;
        $currency = 'INR';

        // Selling Price (.Nx9bqj or ._30jeq3 or ._16Jk6d)
        if (preg_match('/<div\s+class=["\'][^"\']*(?:Nx9bqj|_30jeq3|_16Jk6d)[^"\']*["\'][^>]*>(.*?)<\/div>/is', $html, $m)) {
            $price = $this->cleanPriceValue($m[1]);
            $sources['price'] = 'dom_flipkart_price_class';
            $confidenceScores['price'] = 98;
        } elseif (!empty($jsonLd['offers']['price'])) {
            $price = (float)$jsonLd['offers']['price'];
            $sources['price'] = 'json_ld_offers';
            $confidenceScores['price'] = 90;
        }

        // List Price / Strikethrough MRP (.yRaY8j or ._3I9_wc)
        if (preg_match('/<div\s+class=["\'][^"\']*(?:yRaY8j|_3I9_wc)[^"\']*["\'][^>]*>(.*?)<\/div>/is', $html, $m)) {
            $mVal = $this->cleanPriceValue($m[1]);
            if ($mVal > $price) {
                $mrp = $mVal;
                $sources['mrp'] = 'dom_flipkart_mrp';
            }
        }

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
            $warnings[] = 'Selling price could not be automatically detected. Please enter manually.';
        }

        // 4. Brand Extraction
        $brand = '';
        if (preg_match('/<span\s+class=["\'][^"\']*G6XhRU[^"\']*["\'][^>]*>(.*?)<\/span>/is', $html, $m)) {
            $brand = trim(strip_tags($m[1]));
            $sources['brand'] = 'dom_flipkart_brand';
            $confidenceScores['brand'] = 95;
        } elseif (!empty($jsonLd['brand'])) {
            $brand = is_array($jsonLd['brand']) ? ($jsonLd['brand']['name'] ?? '') : $jsonLd['brand'];
            $sources['brand'] = 'json_ld_brand';
            $confidenceScores['brand'] = 90;
        }

        if (empty($brand)) {
            // Extract from title first word if capitalized
            $words = explode(' ', $title);
            $brand = count($words) > 0 && strlen($words[0]) > 2 ? $words[0] : 'Generic';
            $confidenceScores['brand'] = 60;
        }

        // 5. Gallery Images Extraction (Upscaling Flipkart CDN images)
        $images = [];
        if (preg_match_all('/<img[^>]+src=["\'](https:\/\/[^"\']*rukminim[^"\']*)["\']/i', $html, $imgMatches)) {
            foreach ($imgMatches[1] as $imgUrl) {
                // Upscale Flipkart image thumbnail (e.g. replace 128/128 with 832/832)
                $highRes = preg_replace('/\/image\/\d+\/\d+\//', '/image/832/832/', $imgUrl);
                if (!str_contains($highRes, 'placeholder') && !str_contains($highRes, 'spinner')) {
                    $images[] = $highRes;
                }
            }
            $sources['images'] = 'dom_flipkart_gallery';
            $confidenceScores['images'] = 95;
        } elseif (preg_match('/<meta\s+property=["\']og:image["\']\s+content=["\'](.*?)["\']/i', $html, $m)) {
            $images[] = $m[1];
            $sources['images'] = 'og_image';
            $confidenceScores['images'] = 80;
        }

        $images = array_values(array_unique($images));
        $primaryImage = count($images) > 0 ? $images[0] : 'assets/img/ecommerce-images/product-1.png';

        // 6. Specifications Table Extraction
        $specifications = [];
        if (preg_match_all('/<tr\s+class=["\'][^"\']*_1s52Kn[^"\']*["\'][^>]*>\s*<td\s+class=["\'][^"\']*_1hKmbr[^"\']*["\'][^>]*>(.*?)<\/td>\s*<td\s+class=["\'][^"\']*_21lJal[^"\']*["\'][^>]*>(.*?)<\/td>\s*<\/tr>/is', $html, $specMatches)) {
            foreach ($specMatches[1] as $idx => $label) {
                $k = trim(html_entity_decode(strip_tags($label)));
                $v = trim(html_entity_decode(strip_tags($specMatches[2][$idx])));
                if (!empty($k) && !empty($v)) {
                    $specifications[$k] = $v;
                }
            }
            $sources['specifications'] = 'dom_flipkart_specs';
            $confidenceScores['specifications'] = 95;
        }

        // 7. Bullet Points & Highlights
        $bullets = [];
        if (preg_match('/<div\s+class=["\'][^"\']*(?:_241VTa|_21Azi0)[^"\']*["\'][^>]*>(.*?)<\/div>/is', $html, $m)) {
            if (preg_match_all('/<li[^>]*>(.*?)<\/li>/is', $m[1], $bulletMatches)) {
                foreach ($bulletMatches[1] as $b) {
                    $cleaned = trim(html_entity_decode(strip_tags($b)));
                    if (!empty($cleaned)) {
                        $bullets[] = $cleaned;
                    }
                }
            }
        }

        // 8. Description Extraction
        $description = '';
        if (preg_match('/<div\s+class=["\'][^"\']*_1mXwrf[^"\']*["\'][^>]*>(.*?)<\/div>/is', $html, $m)) {
            $formatted = preg_replace('/<\/(?:p|div|li|tr)>/i', "\n\n", $m[1]);
            $description = trim(html_entity_decode(strip_tags($formatted)));
            $sources['description'] = 'dom_flipkart_description';
            $confidenceScores['description'] = 92;
        } elseif (count($bullets) > 0) {
            $description = implode("\n\n• ", array_merge(['Key Highlights:'], $bullets));
            $sources['description'] = 'bullets_composite';
            $confidenceScores['description'] = 85;
        } elseif (!empty($jsonLd['description'])) {
            $description = trim(html_entity_decode(strip_tags($jsonLd['description'])));
            $sources['description'] = 'json_ld_description';
            $confidenceScores['description'] = 80;
        }

        // 9. Ratings & Reviews
        $rating = 0.0;
        $reviewCount = 0;
        if (preg_match('/<div\s+class=["\'][^"\']*(?:_3LWZlK|XDXdRw)[^"\']*["\'][^>]*>(.*?)<\/div>/is', $html, $m)) {
            $rating = (float) trim(strip_tags($m[1]));
        }
        if (preg_match('/<span\s+class=["\'][^"\']*_2_R_DZ[^"\']*["\'][^>]*>.*?([\d,]+)\s+Ratings/is', $html, $m)) {
            $reviewCount = (int) str_replace(',', '', $m[1]);
        }

        // 10. Category
        $categoryName = 'Electronics';
        if (preg_match('/<div\s+class=["\'][^"\']*_1MR4o5[^"\']*["\'][^>]*>(.*?)<\/div>/is', $html, $m)) {
            if (preg_match_all('/<a[^>]*>(.*?)<\/a>/is', $m[1], $crumbMatches)) {
                $crumbs = array_map('trim', array_map('strip_tags', $crumbMatches[1]));
                if (count($crumbs) > 1) {
                    $categoryName = end($crumbs);
                }
            }
        }

        $weights = ['title' => 0.35, 'price' => 0.35, 'brand' => 0.10, 'images' => 0.10, 'description' => 0.10];
        $totalConfidence = 0;
        foreach ($weights as $field => $weight) {
            $totalConfidence += ($confidenceScores[$field] ?? 50) * $weight;
        }

        return [
            'name'                => $title,
            'platform'            => 'flipkart',
            'asin'                => $pid,
            'sku'                 => $pid ? "FLP-{$pid}" : ('AKM-' . strtoupper(Str::random(6))),
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
            'availability'        => 'In Stock',
            'confidence_score'    => (int) round($totalConfidence),
            'confidence_breakdown'=> $confidenceScores,
            'sources'             => $sources,
            'warnings'            => $warnings,
            'qty'                 => 10,
            'canonical_url'       => $urlInfo['canonical_url'],
            'domain'              => 'flipkart.com',
        ];
    }

    protected function parseJsonLd(string $html): array
    {
        if (preg_match_all('/<script\s+type=["\']application\/ld\+json["\']>(.*?)<\/script>/is', $html, $matches)) {
            foreach ($matches[1] as $jsonStr) {
                $decoded = json_decode(trim($jsonStr), true);
                if (is_array($decoded) && (($decoded['@type'] ?? '') === 'Product')) {
                    return $decoded;
                }
            }
        }
        return [];
    }

    public function cleanPriceValue(string $raw): float
    {
        $cleaned = html_entity_decode(strip_tags($raw));
        $cleaned = str_replace([',', '₹', '$', '£', '€', 'INR', 'USD', '&nbsp;', "\xc2\xa0"], '', $cleaned);
        $cleaned = trim($cleaned);
        if (preg_match('/\d+(?:\.\d{1,2})?/', $cleaned, $match)) {
            return (float) $match[0];
        }
        return 0.00;
    }
}
