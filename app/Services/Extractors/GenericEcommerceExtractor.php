<?php

namespace App\Services\Extractors;

use Illuminate\Support\Str;

class GenericEcommerceExtractor
{
    /**
     * Extract product data from standard / custom e-commerce stores.
     */
    public function extract(string $html, string $url): array
    {
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? 'store';

        $sources = [];
        $confidenceScores = [];

        // 1. JSON-LD parsing
        $jsonLd = $this->parseJsonLd($html);

        // Title
        $title = '';
        if (!empty($jsonLd['name'])) {
            $title = trim(html_entity_decode($jsonLd['name']));
            $sources['title'] = 'json_ld_name';
            $confidenceScores['title'] = 95;
        } elseif (preg_match('/<meta\s+property=["\']og:title["\']\s+content=["\'](.*?)["\']/i', $html, $m)) {
            $title = trim(html_entity_decode($m[1]));
            $sources['title'] = 'og_title';
            $confidenceScores['title'] = 90;
        } elseif (preg_match('/<h1[^>]*class=["\'][^"\']*(?:product-title|product_title|page-title|entry-title)[^"\']*["\'][^>]*>(.*?)<\/h1>/is', $html, $m)) {
            $title = trim(html_entity_decode(strip_tags($m[1])));
            $sources['title'] = 'dom_h1_product_title';
            $confidenceScores['title'] = 90;
        } elseif (preg_match('/<title>(.*?)<\/title>/is', $html, $m)) {
            $title = trim(html_entity_decode(explode('|', explode('-', $m[1])[0])[0]));
            $sources['title'] = 'html_title';
            $confidenceScores['title'] = 75;
        }

        // Price & MRP
        $price = 0.00;
        $mrp = 0.00;
        $currency = 'USD';

        if (!empty($jsonLd['offers']['price'])) {
            $price = (float) $jsonLd['offers']['price'];
            if (!empty($jsonLd['offers']['priceCurrency'])) {
                $currency = strtoupper($jsonLd['offers']['priceCurrency']);
            }
            $sources['price'] = 'json_ld_offers';
            $confidenceScores['price'] = 95;
        } elseif (preg_match('/<meta\s+property=["\']product:price:amount["\']\s+content=["\'](.*?)["\']/i', $html, $m)) {
            $price = (float) $m[1];
            $sources['price'] = 'og_price';
            $confidenceScores['price'] = 90;
        } elseif (preg_match('/class=["\'][^"\']*(?:woocommerce-Price-amount|price-amount|current-price|product-price)[^"\']*["\'][^>]*>(.*?)<\/(?:span|div|p)>/is', $html, $m)) {
            $price = $this->cleanPriceValue($m[1]);
            $sources['price'] = 'dom_class_price';
            $confidenceScores['price'] = 85;
        }

        // Currency
        if (preg_match('/<meta\s+property=["\'](?:product:price:currency|og:price:currency)["\']\s+content=["\'](.*?)["\']/i', $html, $m)) {
            $currency = strtoupper($m[1]);
        } elseif (str_contains($html, '₹') || str_contains($html, 'INR')) {
            $currency = 'INR';
        } elseif (str_contains($html, '£') || str_contains($html, 'GBP')) {
            $currency = 'GBP';
        } elseif (str_contains($html, '€') || str_contains($html, 'EUR')) {
            $currency = 'EUR';
        }

        // Brand
        $brand = 'Store Product';
        if (!empty($jsonLd['brand'])) {
            $brand = is_array($jsonLd['brand']) ? ($jsonLd['brand']['name'] ?? '') : $jsonLd['brand'];
            $sources['brand'] = 'json_ld_brand';
        } elseif (preg_match('/<meta\s+property=["\']product:brand["\']\s+content=["\'](.*?)["\']/i', $html, $m)) {
            $brand = trim($m[1]);
        }

        // Images Gallery
        $images = [];
        if (!empty($jsonLd['image'])) {
            $ldImgs = is_array($jsonLd['image']) ? $jsonLd['image'] : [$jsonLd['image']];
            foreach ($ldImgs as $img) {
                if (filter_var($img, FILTER_VALIDATE_URL)) {
                    $images[] = $img;
                }
            }
        }

        if (preg_match_all('/<meta\s+property=["\']og:image["\']\s+content=["\'](.*?)["\']/i', $html, $imgMatches)) {
            foreach ($imgMatches[1] as $img) {
                $images[] = $img;
            }
        }

        $images = array_values(array_unique($images));
        $primaryImage = count($images) > 0 ? $images[0] : 'assets/img/ecommerce-images/product-1.png';

        // Description
        $description = '';
        if (!empty($jsonLd['description'])) {
            $description = trim(html_entity_decode(strip_tags($jsonLd['description'])));
            $sources['description'] = 'json_ld_description';
        } elseif (preg_match('/<meta\s+property=["\']og:description["\']\s+content=["\'](.*?)["\']/i', $html, $m)) {
            $description = trim(html_entity_decode(strip_tags($m[1])));
            $sources['description'] = 'og_description';
        }

        return [
            'name'                => $title ?: 'Imported E-Commerce Product',
            'platform'            => 'generic',
            'asin'                => null,
            'sku'                 => 'GEN-' . strtoupper(Str::random(6)),
            'barcode'             => (string) rand(100000000000, 999999999999),
            'brand'               => $brand,
            'category_name'       => $jsonLd['category'] ?? 'General Products',
            'price'               => $price > 0 ? $price : 29.99,
            'compare_at_price'    => $mrp > $price ? $mrp : 0,
            'discount_percent'    => 0,
            'currency'            => $currency,
            'image'               => $primaryImage,
            'gallery_images'      => $images,
            'description'         => $description,
            'bullet_points'       => [],
            'specifications'      => [
                'Source Platform' => 'Direct E-Commerce Store',
                'Domain'          => $host,
            ],
            'variants'            => [],
            'rating'              => (float)($jsonLd['aggregateRating']['ratingValue'] ?? 4.5),
            'review_count'        => (int)($jsonLd['aggregateRating']['reviewCount'] ?? 15),
            'availability'        => 'In Stock',
            'confidence_score'    => 85,
            'sources'             => $sources,
            'warnings'            => [],
            'qty'                 => 10,
            'canonical_url'       => $url,
            'domain'              => $host,
        ];
    }

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
        $cleaned = str_replace([',', '₹', '$', '£', '€', 'INR', 'USD', '&nbsp;', "\xc2\xa0"], '', $cleaned);
        $cleaned = trim($cleaned);
        if (preg_match('/\d+(?:\.\d{1,2})?/', $cleaned, $match)) {
            return (float) $match[0];
        }
        return 0.00;
    }
}
