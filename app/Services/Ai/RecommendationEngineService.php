<?php

namespace App\Services\Ai;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class RecommendationEngineService
{
    /**
     * Cross-category complementary affinity map
     */
    protected static array $categoryAffinity = [
        'smartphones' => ['accessories', 'audio', 'headphones', 'powerbanks', 'cables'],
        'phones'      => ['accessories', 'audio', 'headphones', 'powerbanks'],
        'laptops'     => ['accessories', 'peripherals', 'bags', 'mice', 'keyboards'],
        'computers'   => ['peripherals', 'monitors', 'keyboards', 'mice'],
        'footwear'    => ['apparel', 'accessories', 'socks'],
        'cameras'     => ['accessories', 'tripods', 'memory-cards', 'lenses'],
    ];

    /**
     * 1. Similar Products
     */
    public function getSimilarProducts(Product $product, int $limit = 4): Collection
    {
        $minPrice = $product->price * 0.70;
        $maxPrice = $product->price * 1.30;

        return Product::where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where(function ($q) use ($product, $minPrice, $maxPrice) {
                if ($product->category_id) {
                    $q->where('category_id', $product->category_id);
                }
                if ($product->brand) {
                    $q->orWhere('brand', $product->brand);
                }
                $q->orWhereBetween('price', [$minPrice, $maxPrice]);
            })
            ->orderByRaw('CASE WHEN qty > 0 THEN 1 ELSE 0 END DESC')
            ->orderByRaw('CASE WHEN brand = ? THEN 1 ELSE 0 END DESC', [$product->brand ?? ''])
            ->take($limit)
            ->get();
    }

    /**
     * 2. Frequently Bought Together (Order Co-Occurrence)
     */
    public function getFrequentlyBoughtTogether(Product $product, int $limit = 3): Collection
    {
        // Find orders containing this product
        $orderIds = OrderItem::where('product_id', $product->id)
            ->pluck('order_id')
            ->toArray();

        if (!empty($orderIds)) {
            $coOccurringProductIds = OrderItem::whereIn('order_id', $orderIds)
                ->where('product_id', '!=', $product->id)
                ->select('product_id', DB::raw('COUNT(*) as frequency'))
                ->groupBy('product_id')
                ->orderByDesc('frequency')
                ->take($limit)
                ->pluck('product_id')
                ->toArray();

            if (!empty($coOccurringProductIds)) {
                $fbtProducts = Product::where('is_active', true)
                    ->whereIn('id', $coOccurringProductIds)
                    ->get();

                if ($fbtProducts->isNotEmpty()) {
                    return $fbtProducts;
                }
            }
        }

        // Fallback: Complementary or Same-Category Items
        return $this->getComplementaryProducts($product, $limit);
    }

    /**
     * 3. Complementary Products
     */
    public function getComplementaryProducts(Product $product, int $limit = 3): Collection
    {
        $catSlug = strtolower($product->category?->slug ?? '');
        $affinitySlugs = self::$categoryAffinity[$catSlug] ?? [];

        $query = Product::where('is_active', true)
            ->where('id', '!=', $product->id);

        if (!empty($affinitySlugs)) {
            $query->whereHas('category', function ($q) use ($affinitySlugs) {
                $q->whereIn('slug', $affinitySlugs);
            });
        } elseif ($product->category_id) {
            $query->where('category_id', $product->category_id);
        }

        return $query->orderByRaw('CASE WHEN qty > 0 THEN 1 ELSE 0 END DESC')
            ->orderBy('rating_cache', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * 4. Budget Alternatives (Cheaper options in same category)
     */
    public function getBudgetAlternatives(Product $product, int $limit = 3): Collection
    {
        return Product::where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where('price', '<', $product->price)
            ->where(function ($q) use ($product) {
                if ($product->category_id) {
                    $q->where('category_id', $product->category_id);
                }
            })
            ->orderByRaw('CASE WHEN qty > 0 THEN 1 ELSE 0 END DESC')
            ->orderBy('price', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * 5. Upgrade Recommendations (Higher-spec options in same category)
     */
    public function getUpgradeRecommendations(Product $product, int $limit = 3): Collection
    {
        return Product::where('is_active', true)
            ->where('id', '!=', $product->id)
            ->where('price', '>', $product->price)
            ->where(function ($q) use ($product) {
                if ($product->category_id) {
                    $q->where('category_id', $product->category_id);
                }
            })
            ->orderByRaw('CASE WHEN qty > 0 THEN 1 ELSE 0 END DESC')
            ->orderBy('price', 'asc')
            ->take($limit)
            ->get();
    }

    /**
     * 6. Trending Products
     */
    public function getTrendingProducts(int $limit = 8): Collection
    {
        return Product::where('is_active', true)
            ->where(function ($q) {
                $q->where('is_trending', true)
                    ->orWhere('is_best_seller', true)
                    ->orWhere('rating_cache', '>=', 4.5);
            })
            ->orderByRaw('CASE WHEN qty > 0 THEN 1 ELSE 0 END DESC')
            ->orderBy('rating_cache', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * 7. Personalized Recommendations
     */
    public function getPersonalizedForUser(?User $user, int $limit = 8): Collection
    {
        if (!$user) {
            return $this->getTrendingProducts($limit);
        }

        // Extract customer's preferred categories from orders and wishlist
        $categoryIds = OrderItem::whereHas('order', fn($q) => $q->where('user_id', $user->id))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->whereNotNull('products.category_id')
            ->pluck('products.category_id')
            ->toArray();

        $wishlistCategoryIds = Wishlist::where('user_id', $user->id)
            ->join('products', 'wishlists.product_id', '=', 'products.id')
            ->whereNotNull('products.category_id')
            ->pluck('products.category_id')
            ->toArray();

        $mergedCatIds = array_unique(array_merge($categoryIds, $wishlistCategoryIds));

        if (empty($mergedCatIds)) {
            return $this->getTrendingProducts($limit);
        }

        return Product::where('is_active', true)
            ->whereIn('category_id', $mergedCatIds)
            ->orderByRaw('CASE WHEN qty > 0 THEN 1 ELSE 0 END DESC')
            ->orderBy('rating_cache', 'desc')
            ->take($limit)
            ->get();
    }
}
