<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class GroceryProductService
{
    /**
     * Calculate price for a weight or unit based quantity
     */
    public function calculateLineTotal(Product $product, float $quantity): float
    {
        $unitPrice = (float)$product->price;

        if ($product->is_weight_based && $product->unit_price_ratio > 0) {
            $unitPrice = (float)$product->unit_price_ratio;
        }

        return round($unitPrice * $quantity, 2);
    }

    /**
     * Format display price per standard unit (e.g. "$3.50 / kg")
     */
    public function formatPricePerUnit(Product $product): string
    {
        if (!empty($product->price_per_unit_label)) {
            return $product->price_per_unit_label;
        }

        $unit = $product->unit ?? 'piece';
        $price = number_format((float)$product->price, 2);
        return "\${$price} / {$unit}";
    }

    /**
     * Validate perishable shelf life
     */
    public function isNearExpiry(Product $product, ?string $expiryDate = null): bool
    {
        if (!$product->is_perishable || empty($expiryDate)) {
            return false;
        }

        $alertDays = $product->expiry_shelf_life_days ?? 7;
        $expiry = \Carbon\Carbon::parse($expiryDate);
        return $expiry->isFuture() && $expiry->diffInDays(now()) <= $alertDays;
    }

    /**
     * Suggest eligible grocery substitutions if out of stock
     */
    public function suggestSubstitutions(Product $product, int $limit = 3): Collection
    {
        if (!$product->allow_substitution) {
            return collect();
        }

        $minPrice = max(0, (float)$product->price * 0.70);
        $maxPrice = (float)$product->price * 1.30;

        return Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->where('qty', '>', 0)
            ->whereBetween('price', [$minPrice, $maxPrice])
            ->when($product->is_weight_based, fn($q) => $q->where('is_weight_based', true))
            ->latest()
            ->take($limit)
            ->get();
    }
}
