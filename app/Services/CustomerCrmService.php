<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\CustomerNote;
use Illuminate\Support\Facades\DB;

class CustomerCrmService
{
    /**
     * Recalculate customer RFM score and segment
     */
    public function recalculateSegment(User $customer): User
    {
        $orders = Order::where('user_id', $customer->id)
            ->whereIn('order_status', ['completed', 'delivered', 'processing', 'dispatched'])
            ->get();

        $totalOrders = $orders->count();
        $lifetimeSpend = (float)$orders->sum('total_amount');
        $lastOrder = $orders->sortByDesc('created_at')->first();
        $daysSinceLastOrder = $lastOrder ? now()->diffInDays($lastOrder->created_at) : 999;

        // Recency Score (1-5)
        $r = $daysSinceLastOrder <= 14 ? 5 : ($daysSinceLastOrder <= 30 ? 4 : ($daysSinceLastOrder <= 60 ? 3 : ($daysSinceLastOrder <= 120 ? 2 : 1)));

        // Frequency Score (1-5)
        $f = $totalOrders >= 15 ? 5 : ($totalOrders >= 8 ? 4 : ($totalOrders >= 4 ? 3 : ($totalOrders >= 2 ? 2 : 1)));

        // Monetary Score (1-5)
        $m = $lifetimeSpend >= 1000 ? 5 : ($lifetimeSpend >= 500 ? 4 : ($lifetimeSpend >= 200 ? 3 : ($lifetimeSpend >= 75 ? 2 : 1)));

        $rfmScore = ($r * 100) + ($f * 10) + $m;

        // Determine Segment Label
        $segment = 'NEW';
        if ($lifetimeSpend >= 500 || $totalOrders >= 10 || $rfmScore >= 444) {
            $segment = 'VIP';
        } elseif ($totalOrders >= 4 && $daysSinceLastOrder <= 45) {
            $segment = 'LOYAL';
        } elseif ($daysSinceLastOrder <= 30 && $totalOrders >= 1) {
            $segment = 'ACTIVE';
        } elseif ($totalOrders >= 2 && $daysSinceLastOrder > 60) {
            $segment = 'AT_RISK';
        } elseif ($totalOrders == 0 && now()->diffInDays($customer->created_at) > 30 || $daysSinceLastOrder > 120) {
            $segment = 'INACTIVE';
        }

        $customer->update([
            'rfm_score'          => $rfmScore,
            'customer_segment'   => $segment,
            'lifetime_spend'     => $lifetimeSpend,
            'total_orders_count' => $totalOrders,
            'last_ordered_at'    => $lastOrder?->created_at,
        ]);

        return $customer->fresh();
    }

    /**
     * Add an internal CRM note
     */
    public function addCustomerNote(User $customer, string $noteText, ?int $authorId = null, bool $isPinned = false): CustomerNote
    {
        return CustomerNote::create([
            'user_id'   => $customer->id,
            'author_id' => $authorId,
            'note'      => $noteText,
            'is_pinned' => $isPinned,
        ]);
    }
}
