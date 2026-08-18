<?php

namespace App\Services;

use App\Models\AbandonedCart;
use Illuminate\Support\Str;

class MarketingService
{
    /**
     * Track customer cart state to capture abandoned checkouts
     */
    public function trackCart(?int $userId, ?string $email, ?string $phone, array $cartData, float $totalAmount): AbandonedCart
    {
        $existing = null;
        if ($userId) {
            $existing = AbandonedCart::where('user_id', $userId)->whereNull('recovered_at')->first();
        } elseif ($email) {
            $existing = AbandonedCart::where('email', $email)->whereNull('recovered_at')->first();
        }

        if ($existing) {
            $existing->update([
                'cart_data'    => $cartData,
                'total_amount' => $totalAmount,
                'phone'        => $phone ?? $existing->phone,
            ]);
            return $existing;
        }

        return AbandonedCart::create([
            'user_id'        => $userId,
            'email'          => $email,
            'phone'          => $phone,
            'cart_data'      => $cartData,
            'total_amount'   => $totalAmount,
            'recovery_token' => Str::random(32),
        ]);
    }

    /**
     * Mark cart as recovered upon successful checkout
     */
    public function markRecovered(string $token): bool
    {
        $cart = AbandonedCart::where('recovery_token', $token)->first();
        if ($cart) {
            $cart->update(['recovered_at' => now()]);
            return true;
        }
        return false;
    }
}
