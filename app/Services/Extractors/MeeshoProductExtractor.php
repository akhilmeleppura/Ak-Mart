<?php

namespace App\Services\Extractors;

use Illuminate\Support\Str;

class MeeshoProductExtractor
{
    /**
     * Determine if a given URL is from Meesho.
     */
    public function isMeeshoUrl(string $url): bool
    {
        $host = parse_url($url, PHP_URL_HOST);
        if (!$host) return false;
        $host = strtolower(preg_replace('/^www\./', '', $host));
        return $host === 'meesho.com' || str_ends_with($host, '.meesho.com');
    }

    /**
     * Extract structured product data from Meesho HTML or __NEXT_DATA__ JSON.
     */
    public function extract(string $html, string $url): array
    {
        $parsedUrl = parse_url($url);
        $path = $parsedUrl['path'] ?? '';
        $productId = null;
        if (preg_match('/\/p\/([a-zA-Z0-9]+)/i', $path, $m)) {
            $productId = $m[1];
        }

        $sources = [];
        $confidenceScores = [];
        $warnings = [];

        // 1. Next.js Hydration State extraction (__NEXT_DATA__)
        $nextData = [];
        if (preg_match('/<script\s+id=["\']__NEXT_DATA__ banner_product_data["\'][^>]*>(.*?)<\/script>/is', $html, $m) ||
            preg_match('/<script\s+id=["\']__NEXT_DATA__["\'][^>]*>(.*?)<\/script>/is', $html, $m)) {
            $decoded = json_decode(trim($m[1]), true);
            if (is_array($decoded)) {
                $nextData = $decoded['props']['pageProps']['initialState']['product']['details'] ??
                            $decoded['props']['pageProps']['productDetails'] ?? [];
            }
        }

        // 2. Title Extraction
        $title = $nextData['name'] ?? $nextData['title'] ?? '';
        if (!$title && preg_match('/<h1[^>]*class=["\'][^"\']*(?:sc-|ProductTitle)[^"\']*["\'][^>]*>(.*?)<\/h1>/is', $html, $m)) {
            $title = trim(html_entity_decode(strip_tags($m[1])));
            $sources['title'] = 'dom_meesho_h1';
            $confidenceScores['title'] = 98;
        } elseif (!$title && preg_match('/<meta\s+property=["\']og:title["\']\s+content=["\'](.*?)["\']/i', $html, $m)) {
            $title = trim(html_entity_decode($m[1]));
            $sources['title'] = 'og_title';
            $confidenceScores['title'] = 88;
        }

        if (empty($title)) {
            $title = 'Meesho Product ' . ($productId ? "#{$productId}" : '');
            $confidenceScores['title'] = 50;
        }

        // 3. Price & Discount Extraction
        $price = 0.00;
        $mrp = 0.00;
        $currency = 'INR';

        if (isset($nextData['price'])) {
            $price = (float) $nextData['price'];
            $mrp = (float) ($nextData['mrp'] ?? $nextData['original_price'] ?? 0);
            $sources['price'] = 'next_data_price';
            $confidenceScores['price'] = 98;
        }

        if ($price <= 0 && preg_match('/<h4[^>]*class=["\'][^"\']*(?:sc-|Price)[^"\']*["\'][^>]*>.*?₹\s*([\d,]+)/is', $html, $m)) {
            $price = (float) str_replace(',', '', $m[1]);
            $sources['price'] = 'dom_meesho_price';
            $confidenceScores['price'] = 95;
        } elseif ($price <= 0 && preg_match('/₹\s*([\d,]+)/u', $html, $m)) {
            $price = (float) str_replace(',', '', $m[1]);
            $sources['price'] = 'text_currency_price';
            $confidenceScores['price'] = 80;
        }

        if ($mrp <= 0 && preg_match('/<p[^>]*class=["\'][^"\']*(?:sc-|Strikethrough|mrp)[^"\']*["\'][^>]*>.*?₹\s*([\d,]+)/is', $html, $m)) {
            $mrp = (float) str_replace(',', '', $m[1]);
            $sources['mrp'] = 'dom_meesho_mrp';
        }

        if ($mrp <= $price) {
            $mrp = 0.00;
        }

        $discountPercent = 0;
        if ($mrp > 0 && $price > 0 && $mrp > $price) {
            $discountPercent = round((($mrp - $price) / $mrp) * 100);
        }

        // 4. Gallery Images Extraction
        $images = [];
        if (!empty($nextData['images']) && is_array($nextData['images'])) {
            $images = $nextData['images'];
            $sources['images'] = 'next_data_images';
            $confidenceScores['images'] = 98;
        }

        if (empty($images) && preg_match_all('/<img[^>]+src=["\'](https:\/\/[^"\']*images\.meesho\.com\/images\/products\/[^"\']*)["\']/i', $html, $imgMatches)) {
            $images = array_values(array_unique($imgMatches[1]));
            $sources['images'] = 'dom_meesho_cdn_images';
            $confidenceScores['images'] = 95;
        }

        if (empty($images) && preg_match('/<meta\s+property=["\']og:image["\']\s+content=["\'](.*?)["\']/i', $html, $m)) {
            $images[] = $m[1];
            $sources['images'] = 'og_image';
            $confidenceScores['images'] = 80;
        }

        $primaryImage = count($images) > 0 ? $images[0] : 'assets/img/ecommerce-images/product-1.png';

        // 5. Specifications & Attributes
        $specifications = [];
        if (!empty($nextData['product_attributes'])) {
            foreach ($nextData['product_attributes'] as $attr) {
                if (!empty($attr['name']) && !empty($attr['value'])) {
                    $specifications[$attr['name']] = $attr['value'];
                }
            }
        }

        if (empty($specifications) && preg_match_all('/<span[^>]*class=["\'][^"\']*sc-[^"\']*["\'][^>]*>([^<:]+):\s*<\/span>\s*<span[^>]*>([^<]+)<\/span>/is', $html, $specMatches)) {
            foreach ($specMatches[1] as $idx => $label) {
                $k = trim(html_entity_decode(strip_tags($label)));
                $v = trim(html_entity_decode(strip_tags($specMatches[2][$idx])));
                if (!empty($k) && !empty($v)) {
                    $specifications[$k] = $v;
                }
            }
        }

        // 6. Description & Highlights
        $description = $nextData['description'] ?? '';
        if (!$description && preg_match('/<div[^>]*class=["\'][^"\']*(?:ProductDescription|sc-)[^"\']*["\'][^>]*>(.*?)<\/div>/is', $html, $m)) {
            $formatted = preg_replace('/<\/(?:p|div|li|tr)>/i', "\n\n", $m[1]);
            $description = trim(html_entity_decode(strip_tags($formatted)));
        }

        if (!$description && count($specifications) > 0) {
            $descLines = ["Product Details:"];
            foreach ($specifications as $k => $v) {
                $descLines[] = "• {$k}: {$v}";
            }
            $description = implode("\n", $descLines);
        }

        // 7. Rating & Reviews
        $rating = (float) ($nextData['rating'] ?? 4.2);
        $reviewCount = (int) ($nextData['review_count'] ?? 120);

        return [
            'name'                => $title,
            'platform'            => 'meesho',
            'asin'                => $productId,
            'sku'                 => $productId ? "MSH-{$productId}" : ('AKM-' . strtoupper(Str::random(6))),
            'barcode'             => (string) rand(100000000000, 999999999999),
            'brand'               => $nextData['brand'] ?? 'Meesho Assured',
            'category_name'       => $nextData['category_name'] ?? 'Clothing & Apparel',
            'price'               => $price > 0 ? $price : 399.00,
            'compare_at_price'    => $mrp,
            'discount_percent'    => $discountPercent,
            'currency'            => $currency,
            'image'               => $primaryImage,
            'gallery_images'      => $images,
            'description'         => $description,
            'bullet_points'       => [],
            'specifications'      => $specifications,
            'rating'              => $rating,
            'review_count'        => $reviewCount,
            'availability'        => 'In Stock',
            'confidence_score'    => 90,
            'sources'             => $sources,
            'warnings'            => $warnings,
            'qty'                 => 15,
            'canonical_url'       => $url,
            'domain'              => 'meesho.com',
        ];
    }
}
