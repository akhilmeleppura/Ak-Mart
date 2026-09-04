<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\User;
use Illuminate\Support\Collection;

class CouponService
{
    /**
     * Get all active and visible coupons with eligibility and savings preview for a given subtotal.
     */
    public function getAvailableCoupons(float $subtotal, ?User $user = null): Collection
    {
        $coupons = Coupon::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->get();

        return $coupons->map(function ($coupon) use ($subtotal, $user) {
            $minSpend = (float)($coupon->min_spend ?? 0);
            $isEligible = true;
            $reason = null;

            // Check usage limit
            if ($coupon->usage_limit && $coupon->usage_count >= $coupon->usage_limit) {
                $isEligible = false;
                $reason = 'Coupon usage limit reached.';
            }

            // Check min spend
            if ($minSpend > 0 && $subtotal < $minSpend) {
                $isEligible = false;
                $diff = round($minSpend - $subtotal, 2);
                $reason = "Add $" . number_format($diff, 2) . " more to unlock this coupon.";
            }

            $discountAmount = $this->calculateDiscount($coupon, $subtotal);

            $progressPct = 100;
            if ($minSpend > 0 && $subtotal < $minSpend) {
                $progressPct = min(100, max(5, round(($subtotal / $minSpend) * 100)));
            }

            return [
                'id'              => $coupon->id,
                'code'            => $coupon->code,
                'type'            => $coupon->type,
                'value'           => (float)$coupon->value,
                'value_formatted' => $coupon->type === 'percentage' ? "{$coupon->value}% OFF" : "\${$coupon->value} OFF",
                'min_spend'       => $minSpend,
                'max_spend'       => $coupon->max_spend ? (float)$coupon->max_spend : null,
                'max_discount'    => $coupon->max_discount ? (float)$coupon->max_discount : null,
                'is_eligible'     => $isEligible,
                'reason'          => $reason,
                'discount_amount' => $discountAmount,
                'discount_formatted' => '$' . number_format($discountAmount, 2),
                'progress_pct'    => $progressPct,
                'expires_at'      => $coupon->end_date ? $coupon->end_date->format('M d, Y') : 'No Expiry',
                'description'     => $coupon->description ?: ($coupon->type === 'percentage' ? "Get {$coupon->value}% discount on orders above \${$minSpend}" : "Flat \${$coupon->value} discount on minimum spend of \${$minSpend}"),
            ];
        })->sortByDesc('discount_amount')->values();
    }

    /**
     * Find and return the coupon that provides the highest dollar discount for the current subtotal.
     */
    public function getBestCoupon(float $subtotal, ?User $user = null): ?array
    {
        $available = $this->getAvailableCoupons($subtotal, $user);
        $eligible = $available->where('is_eligible', true);

        return $eligible->first() ?: null;
    }

    /**
     * Calculate absolute dollar discount for a coupon against a subtotal.
     */
    public function calculateDiscount(Coupon $coupon, float $subtotal): float
    {
        if ($coupon->min_spend && $subtotal < $coupon->min_spend) {
            return 0.0;
        }

        if ($coupon->max_spend && $subtotal > $coupon->max_spend) {
            return 0.0;
        }

        if ($coupon->type === 'percentage') {
            $discount = round(($subtotal * (float)$coupon->value) / 100, 2);
            if (!empty($coupon->max_discount) && $discount > (float)$coupon->max_discount) {
                $discount = (float)$coupon->max_discount;
            }
            return min($subtotal, $discount);
        }

        // Fixed discount
        return min($subtotal, (float)$coupon->value);
    }
}
