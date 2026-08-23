<?php

namespace App\Services\Ai;

use App\Models\Product;
use App\Models\SearchQueryLog;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class SemanticSearchService
{
    /**
     * Common typo dictionary and normalization.
     */
    protected static array $typoDict = [
        'samsng'    => 'Samsung',
        'samung'    => 'Samsung',
        'moble'     => 'Mobile',
        'phon'      => 'Phone',
        'iphne'     => 'iPhone',
        'lapotp'    => 'Laptop',
        'shooes'    => 'Shoes',
        'shose'     => 'Shoes',
        'tshrt'     => 'T-Shirt',
        'headfones' => 'Headphones',
        'earfones'  => 'Earphones',
        'powrbank'  => 'Powerbank',
        'watar'     => 'Water',
        'bottel'    => 'Bottle',
        'backpak'   => 'Backpack',
    ];

    /**
     * Commerce Synonyms Dictionary
     */
    protected static array $synonyms = [
        'mobile'       => 'Phone',
        'cell phone'   => 'Phone',
        'smartphone'   => 'Phone',
        'trainers'     => 'Shoes',
        'sneakers'     => 'Shoes',
        'tv'           => 'Television',
        'fridge'       => 'Refrigerator',
        'earphones'    => 'Headphones',
        'earbuds'      => 'Headphones',
        'tshirt'       => 'Shirt',
        'tee'          => 'Shirt',
    ];

    /**
     * Parse natural language search into structured intent.
     */
    public function parseNaturalQuery(string $input): array
    {
        $normalized = strtolower(trim($input));

        // 1. Correct Typos
        foreach (self::$typoDict as $typo => $correction) {
            $normalized = preg_replace('/\b' . preg_quote($typo, '/') . '\b/i', strtolower($correction), $normalized);
        }

        // 2. Apply Synonyms
        foreach (self::$synonyms as $term => $standard) {
            $normalized = preg_replace('/\b' . preg_quote($term, '/') . '\b/i', strtolower($standard), $normalized);
        }

        $minPrice = null;
        $maxPrice = null;

        // 3. Extract Price Ranges (e.g. "between 5000 and 10000", "between ₹3,000 and ₹7,000")
        if (preg_match('/between\s*(?:₹|\$|rs\.?|inr)?\s*([\d,]+)\s*and\s*(?:₹|\$|rs\.?|inr)?\s*([\d,]+)/i', $normalized, $rangeMatches)) {
            $minPrice = (float)str_replace(',', '', $rangeMatches[1]);
            $maxPrice = (float)str_replace(',', '', $rangeMatches[2]);
            $normalized = str_replace($rangeMatches[0], '', $normalized);
        }
        // 4. Extract "10k max" or "under 15000"
        elseif (preg_match('/(?:₹|\$|rs\.?|inr)?\s*(\d+)k\s*max/i', $normalized, $kMatches)) {
            $maxPrice = (float)$kMatches[1] * 1000;
            $normalized = str_replace($kMatches[0], '', $normalized);
        }
        elseif (preg_match('/(under|below|less than|within|around)\s*(?:₹|\$|rs\.?|inr)?\s*([\d,]+)/i', $normalized, $matches)) {
            $maxPrice = (float)str_replace(',', '', $matches[2]);
            $normalized = str_replace($matches[0], '', $normalized);
        }

        // 5. Clean remaining keywords
        $cleanKeywords = trim(preg_replace('/\s+/', ' ', $normalized));

        return [
            'raw_query'   => $input,
            'clean_query' => $cleanKeywords,
            'min_price'   => $minPrice,
            'max_price'   => $maxPrice,
        ];
    }

    /**
     * Search products with parsed natural language intent and analytics logging.
     */
    public function search(string $input, int $limit = 8, ?int $userId = null): Collection
    {
        $intent = $this->parseNaturalQuery($input);

        $query = Product::where('is_active', true)->with('category');

        if (!empty($intent['clean_query'])) {
            $words = explode(' ', $intent['clean_query']);
            $query->where(function ($sub) use ($words) {
                foreach ($words as $w) {
                    if (strlen($w) >= 2) {
                        $sub->orWhere('name', 'LIKE', "%{$w}%")
                            ->orWhere('description', 'LIKE', "%{$w}%")
                            ->orWhere('brand', 'LIKE', "%{$w}%");
                    }
                }
            });
        }

        if ($intent['min_price']) {
            $query->where('price', '>=', $intent['min_price']);
        }

        if ($intent['max_price']) {
            $query->where('price', '<=', $intent['max_price']);
        }

        // Ranking: In-Stock items first, then featured, then highest rated
        $results = $query->orderByRaw('CASE WHEN qty > 0 THEN 1 ELSE 0 END DESC')
            ->orderBy('is_featured', 'desc')
            ->take($limit)
            ->get();

        // Log search analytics
        try {
            SearchQueryLog::create([
                'query'          => substr($input, 0, 255),
                'cleaned_query'  => substr($intent['clean_query'], 0, 255),
                'results_count'  => $results->count(),
                'is_zero_result' => $results->count() === 0,
                'user_id'        => $userId ?: auth()->id(),
                'locale'         => app()->getLocale() ?: 'en',
            ]);
        } catch (\Exception $e) {
            // Non-blocking log failure
        }

        return $results;
    }

    /**
     * Find in-stock alternatives if a product is out of stock.
     */
    public function getAlternatives(Product $product, int $limit = 3): Collection
    {
        return Product::where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where('qty', '>', 0)
            ->where(function ($q) use ($product) {
                if ($product->category_id) {
                    $q->where('category_id', $product->category_id);
                }
                if ($product->brand) {
                    $q->orWhere('brand', $product->brand);
                }
            })
            ->take($limit)
            ->get();
    }

    /**
     * Generate structured comparison matrix between two products.
     */
    public function compareProducts(Product $p1, Product $p2): array
    {
        return [
            'product_1' => [
                'name'         => $p1->name,
                'brand'        => $p1->brand ?: 'Not specified',
                'price'        => '$' . number_format($p1->price, 2),
                'stock_status' => $p1->qty > 0 ? "In Stock ({$p1->qty} units)" : 'Out of Stock',
                'rating'       => ($p1->rating_cache ?? 5.0) . ' ★',
                'sku'          => $p1->sku,
                'category'     => $p1->category?->name ?? 'General',
                'warranty'     => '1 Year Standard',
            ],
            'product_2' => [
                'name'         => $p2->name,
                'brand'        => $p2->brand ?: 'Not specified',
                'price'        => '$' . number_format($p2->price, 2),
                'stock_status' => $p2->qty > 0 ? "In Stock ({$p2->qty} units)" : 'Out of Stock',
                'rating'       => ($p2->rating_cache ?? 5.0) . ' ★',
                'sku'          => $p2->sku,
                'category'     => $p2->category?->name ?? 'General',
                'warranty'     => '1 Year Standard',
            ],
        ];
    }
}
