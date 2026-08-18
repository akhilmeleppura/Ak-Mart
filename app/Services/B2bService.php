<?php

namespace App\Services;

use App\Models\Product;
use App\Models\B2bCompany;
use App\Models\B2bTierPrice;
use App\Models\B2bQuote;

class B2bService
{
    /**
     * Resolve best unit price based on customer company, tier pricing, and quantity breaks
     */
    public function resolvePrice(int $productId, ?int $companyId = null, int $qty = 1): float
    {
        $product = Product::find($productId);
        if (!$product) {
            return 0.00;
        }

        $basePrice = (float)$product->price;

        if ($companyId) {
            // Check company-specific quantity breaks
            $tierPrice = B2bTierPrice::where('product_id', $productId)
                ->where('b2b_company_id', $companyId)
                ->where('min_qty', '<=', $qty)
                ->orderByDesc('min_qty')
                ->first();

            if ($tierPrice) {
                return (float)$tierPrice->unit_price;
            }
        }

        // Check global volume tier price
        $globalTier = B2bTierPrice::where('product_id', $productId)
            ->whereNull('b2b_company_id')
            ->where('min_qty', '<=', $qty)
            ->orderByDesc('min_qty')
            ->first();

        if ($globalTier) {
            return (float)$globalTier->unit_price;
        }

        return $basePrice;
    }

    /**
     * Check if company has sufficient credit line available
     */
    public function checkCreditAvailable(int $companyId, float $orderAmount): bool
    {
        $company = B2bCompany::find($companyId);
        if (!$company || $company->status !== 'active') {
            return false;
        }

        return $company->available_credit >= $orderAmount;
    }

    /**
     * Calculate and format quote items
     */
    public function calculateQuote(array $items, float $discountPercent = 0): array
    {
        $subtotal = 0;
        $processedItems = [];

        foreach ($items as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) continue;

            $qty = max(1, (int)$item['qty']);
            $unitPrice = isset($item['requested_price']) ? (float)$item['requested_price'] : (float)$product->price;
            $lineTotal = $qty * $unitPrice;
            $subtotal += $lineTotal;

            $processedItems[] = [
                'product_id'      => $product->id,
                'product_name'    => $product->name,
                'qty'             => $qty,
                'requested_price' => $unitPrice,
                'subtotal'        => $lineTotal,
            ];
        }

        $discountAmount = ($subtotal * ($discountPercent / 100));
        $total = max(0, $subtotal - $discountAmount);

        return [
            'subtotal'        => round($subtotal, 2),
            'discount'        => round($discountAmount, 2),
            'total'           => round($total, 2),
            'items'           => $processedItems,
        ];
    }
}
