<?php

namespace App\Services\Ai;

use App\Models\Product;
use Illuminate\Support\Collection;

class SemanticSearchService
{
    /**
     * Common typo dictionary and slang normalization.
     */
    protected static array $typoDict = [
        'samsng'    => 'Samsung',
        'samung'    => 'Samsung',
        'moble'     => 'Mobile',
        'phon'      => 'Phone',
        'lapotp'    => 'Laptop',
        'shooes'    => 'Shoes',
        'shose'     => 'Shoes',
        'tshrt'     => 'T-Shirt',
        'headfones' => 'Headphones',
        'earfones'  => 'Earphones',
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

        // 2. Extract Budget Constraint (e.g., "under 15000", "below 500", "under ₹2,000")
        $maxPrice = null;
        if (preg_match('/(under|below|less than|within)\s*(?:₹|\$|rs\.?|inr)?\s*([\d,]+)/i', $normalized, $matches)) {
            $maxPrice = (float)str_replace(',', '', $matches[2]);
            // Remove budget phrase from search keywords
            $normalized = str_replace($matches[0], '', $normalized);
        }

        // 3. Clean remaining keywords
        $cleanKeywords = trim(preg_replace('/\s+/', ' ', $normalized));

        return [
            'raw_query'   => $input,
            'clean_query' => $cleanKeywords,
            'max_price'   => $maxPrice,
        ];
    }

    /**
     * Search products with parsed natural language intent.
     */
    public function search(string $input, int $limit = 8): Collection
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

        if ($intent['max_price']) {
            $query->where('price', '<=', $intent['max_price']);
        }

        return $query->orderBy('is_featured', 'desc')->take($limit)->get();
    }
}
