<?php

namespace App\Services;

use App\Models\B2bTierPrice;
use App\Models\Coupon;
use App\Models\GiftCard;
use App\Models\Product;
use App\Models\StoreCredit;
use Illuminate\Support\Collection;

class PricingEngine
{
    /**
     * Calculate item price considering quantity tiers, B2B wholesale rules, and sale discounts.
     *
     * @param Product $product
     * @param int $quantity
     * @param int|null $b2bCompanyId
     * @return float
     */
    public function calculateUnitPrice(Product $product, int $quantity = 1, ?int $b2bCompanyId = null): float
    {
        $basePrice = (float) $product->price;

        // 1. Check B2B Volume Tier Pricing
        if ($b2bCompanyId) {
            $tier = B2bTierPrice::where('product_id', $product->id)
                ->where(function ($q) use ($b2bCompanyId) {
                    $q->where('b2b_company_id', $b2bCompanyId)->orWhereNull('b2b_company_id');
                })
                ->where('min_qty', '<=', $quantity)
                ->orderBy('min_qty', 'desc')
                ->first();

            if ($tier) {
                return round((float) $tier->unit_price, 2);
            }
        }

        // 2. Quantity Volume Brackets (Standard Retail)
        if ($quantity >= 50 && $product->compare_at_price > 0) {
            // Volume discount fallback (10% off for 50+ units)
            return round($basePrice * 0.90, 2);
        }

        return round($basePrice, 2);
    }

    /**
     * Calculate comprehensive checkout cart totals server-side.
     *
     * @param array $cartItems [['product_id' => 1, 'qty' => 2, ...]]
     * @param string|null $couponCode
     * @param float $shippingRate
     * @param float $taxPercent (e.g. 18 for 18% GST)
     * @param float $storeCreditApplied
     * @param string|null $giftCardCode
     * @param int|null $b2bCompanyId
     * @return array
     */
    public function calculateCart(
        array $cartItems,
        ?string $couponCode = null,
        float $shippingRate = 0.00,
        float $taxPercent = 18.00,
        float $storeCreditApplied = 0.00,
        ?string $giftCardCode = null,
        ?int $b2bCompanyId = null
    ): array {
        $subtotal = 0.00;
        $itemsBreakdown = [];

        foreach ($cartItems as $item) {
            $productId = $item['product_id'] ?? ($item['id'] ?? null);
            $qty = max(1, (int) ($item['qty'] ?? 1));

            $product = Product::find($productId);
            if (!$product) continue;

            $unitPrice = $this->calculateUnitPrice($product, $qty, $b2bCompanyId);
            $lineTotal = round($unitPrice * $qty, 2);
            $subtotal += $lineTotal;

            $itemsBreakdown[] = [
                'product_id'   => $product->id,
                'name'         => $product->name,
                'sku'          => $product->sku,
                'qty'          => $qty,
                'unit_price'   => $unitPrice,
                'line_total'   => $lineTotal,
            ];
        }

        // Coupon calculation
        $discountAmount = 0.00;
        $couponDetails = null;
        if ($couponCode) {
            $coupon = Coupon::where('code', $couponCode)
                ->where('is_active', true)
                ->where(function ($q) {
                    $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
                })
                ->first();

            if ($coupon) {
                if ($coupon->type === 'percent' || $coupon->type === 'percentage') {
                    $discountAmount = round(($subtotal * ($coupon->value / 100)), 2);
                } else {
                    $discountAmount = min($subtotal, (float) $coupon->value);
                }
                $couponDetails = ['code' => $coupon->code, 'discount' => $discountAmount];
            }
        }

        $taxableAmount = max(0, $subtotal - $discountAmount);
        $taxAmount = round($taxableAmount * ($taxPercent / 100), 2);
        $cgst = round($taxAmount / 2, 2);
        $sgst = round($taxAmount / 2, 2);

        $grossTotal = round($taxableAmount + $taxAmount + $shippingRate, 2);

        // Gift Card redemption
        $giftCardDeduction = 0.00;
        if ($giftCardCode) {
            $giftCard = GiftCard::where('code', $giftCardCode)
                ->where('is_active', true)
                ->where('current_balance', '>', 0)
                ->first();

            if ($giftCard && $giftCard->isValid()) {
                $giftCardDeduction = min($grossTotal, (float) $giftCard->current_balance);
            }
        }

        $remainingAfterGC = max(0, $grossTotal - $giftCardDeduction);

        // Store credit deduction (cannot exceed balance or remaining)
        $actualCreditDeducted = min($remainingAfterGC, max(0, $storeCreditApplied));

        $netPayable = max(0, round($remainingAfterGC - $actualCreditDeducted, 2));

        return [
            'subtotal'            => $subtotal,
            'discount_amount'     => $discountAmount,
            'coupon'              => $couponDetails,
            'taxable_amount'      => $taxableAmount,
            'tax_amount'          => $taxAmount,
            'cgst'                => $cgst,
            'sgst'                => $sgst,
            'shipping_rate'       => $shippingRate,
            'gross_total'         => $grossTotal,
            'gift_card_deducted'  => $giftCardDeduction,
            'store_credit_deducted'=> $actualCreditDeducted,
            'net_payable'         => $netPayable,
            'items'               => $itemsBreakdown,
        ];
    }
}
