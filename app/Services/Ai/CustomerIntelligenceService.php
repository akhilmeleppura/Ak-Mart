<?php

namespace App\Services\Ai;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturn;
use App\Models\StoreCredit;
use App\Models\LoyaltyTransaction;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CustomerIntelligenceService
{
    /**
     * 1. Customer 360 Feature Aggregation
     */
    public function getCustomerProfileMetrics(User $user): array
    {
        $orders = Order::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        $orderCount = $orders->count();
        $totalSpent = (float)$orders->where('payment_status', 'paid')->sum('total_amount');
        $aov = $orderCount > 0 ? round($totalSpent / $orderCount, 2) : 0;

        $lastOrder = $orders->first();
        $daysSinceLastOrder = $lastOrder ? (int)abs(Carbon::now()->diffInDays(Carbon::parse($lastOrder->created_at))) : null;

        // Return rate
        $returnsCount = OrderReturn::withoutGlobalScopes()->where('user_id', $user->id)->count();
        $returnRate = $orderCount > 0 ? round(($returnsCount / $orderCount) * 100, 1) : 0;

        // Preferred categories
        $topCategories = OrderItem::whereHas('order', fn($q) => $q->where('user_id', $user->id))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('COUNT(*) as count'))
            ->groupBy('categories.name')
            ->orderByDesc('count')
            ->take(3)
            ->pluck('name')
            ->toArray();

        // Wallet & Loyalty balances
        $walletBalance = (float)StoreCredit::where('user_id', $user->id)->sum('amount');
        $loyaltyPoints = (int)LoyaltyTransaction::where('user_id', $user->id)->sum('points');

        return [
            'customer_id'          => $user->id,
            'name'                 => $user->name,
            'email'                => $user->email,
            'total_spent'          => $totalSpent,
            'total_spent_formatted'=> '$' . number_format($totalSpent, 2),
            'order_count'          => $orderCount,
            'aov'                  => $aov,
            'aov_formatted'        => '$' . number_format($aov, 2),
            'last_order_date'      => $lastOrder?->created_at?->toDateString(),
            'days_since_last_order'=> $daysSinceLastOrder,
            'return_rate_pct'      => $returnRate,
            'top_categories'       => !empty($topCategories) ? $topCategories : ['General'],
            'wallet_balance'       => $walletBalance,
            'loyalty_points'       => $loyaltyPoints,
            'member_since'         => $user->created_at->toDateString(),
        ];
    }

    /**
     * 2. Explainable Customer Segmentation
     */
    public function calculateCustomerLifecycleSegment(User $user): array
    {
        $metrics = $this->getCustomerProfileMetrics($user);
        $orderCount = $metrics['order_count'];
        $spend = $metrics['total_spent'];
        $days = $metrics['days_since_last_order'];

        if ($spend >= 1000 || $orderCount >= 10) {
            $segment = 'VIP';
            $explanation = 'High lifetime value with over $1,000 spend or 10+ completed orders.';
        } elseif ($spend >= 500) {
            $segment = 'High Value';
            $explanation = 'Substantial customer spend exceeding $500.';
        } elseif ($orderCount >= 2 && $days !== null && $days > 90) {
            $segment = 'At Risk';
            $explanation = "Previous repeat buyer with no purchase for {$days} days.";
        } elseif ($orderCount === 0 || ($days !== null && $days > 180)) {
            $segment = 'Inactive';
            $explanation = 'No purchase activity in the past 180+ days.';
        } elseif ($orderCount >= 2) {
            $segment = 'Returning Customer';
            $explanation = 'Active repeat buyer with recent purchase history.';
        } else {
            $segment = 'New Customer';
            $explanation = 'Recent registration or single first-time purchase.';
        }

        return [
            'segment'     => $segment,
            'explanation' => $explanation,
        ];
    }

    /**
     * 3. Customer Lifetime Value (CLV) Model
     */
    public function calculateCustomerLifetimeValue(User $user): array
    {
        $metrics = $this->getCustomerProfileMetrics($user);
        $historicalSpend = $metrics['total_spent'];
        $orderCount = $metrics['order_count'];
        $aov = $metrics['aov'];

        if ($orderCount === 0) {
            return [
                'historical_spend'           => 0.0,
                'predicted_12m_value'        => 0.0,
                'total_predicted_clv'        => 0.0,
                'confidence'                 => 'Insufficient Data',
                'methodology'                => 'Customer has no historical orders.',
            ];
        }

        // Annualized frequency estimate
        $tenureDays = max(30, (int)Carbon::now()->diffInDays($user->created_at));
        $annualFrequency = ($orderCount / $tenureDays) * 365;
        $retentionFactor = 0.85; // 85% benchmark repeat probability

        $predicted12m = round($annualFrequency * $aov * $retentionFactor, 2);
        $totalCLV = round($historicalSpend + $predicted12m, 2);

        $confidence = match (true) {
            $orderCount >= 5 => 'High',
            $orderCount >= 2 => 'Medium',
            default          => 'Low',
        };

        return [
            'historical_spend'           => $historicalSpend,
            'historical_spend_formatted' => '$' . number_format($historicalSpend, 2),
            'predicted_12m_value'        => $predicted12m,
            'predicted_12m_formatted'    => '$' . number_format($predicted12m, 2),
            'total_predicted_clv'        => $totalCLV,
            'total_predicted_clv_formatted' => '$' . number_format($totalCLV, 2),
            'confidence'                 => $confidence,
            'methodology'                => "Based on {$orderCount} orders over {$tenureDays} days with AOV \${$aov}.",
        ];
    }

    /**
     * 4. Explainable Churn Risk Scoring
     */
    public function calculateChurnRisk(User $user): array
    {
        $metrics = $this->getCustomerProfileMetrics($user);
        $days = $metrics['days_since_last_order'];
        $orderCount = $metrics['order_count'];

        if ($orderCount === 0) {
            return [
                'risk_level' => 'Low',
                'signals'    => ['New or prospective account with no churn history.'],
            ];
        }

        $signals = [];
        $risk = 'Low';

        if ($days !== null && $days >= 90) {
            $risk = 'High';
            $signals[] = "No orders placed in the last {$days} days.";
        } elseif ($days !== null && $days >= 45) {
            $risk = 'Medium';
            $signals[] = "Purchase gap of {$days} days exceeds typical reorder window.";
        } else {
            $signals[] = "Active order history within the last {$days} days.";
        }

        if ($metrics['return_rate_pct'] > 30) {
            $signals[] = "High return rate of {$metrics['return_rate_pct']}% may indicate dissatisfaction.";
        }

        return [
            'risk_level' => $risk,
            'signals'    => $signals,
        ];
    }

    /**
     * 5. Next-Best-Action Recommendation
     */
    public function getNextBestAction(User $user): array
    {
        $seg = $this->calculateCustomerLifecycleSegment($user);
        $churn = $this->calculateChurnRisk($user);

        if ($churn['risk_level'] === 'High') {
            return [
                'action'      => 'Send Win-Back Campaign',
                'description' => 'Offer a 15% discount coupon or free delivery to re-engage this dormant customer.',
                'channel'     => 'Email / WhatsApp',
            ];
        }

        if ($seg['segment'] === 'VIP' || $seg['segment'] === 'High Value') {
            return [
                'action'      => 'VIP Loyalty Reward & Exclusive Preview',
                'description' => 'Grant bonus loyalty points and early access to new product releases.',
                'channel'     => 'Email / Direct Notification',
            ];
        }

        return [
            'action'      => 'Cross-Sell & Category Discovery',
            'description' => 'Recommend trending items matching preferred categories.',
            'channel'     => 'Storefront Recommendations',
        ];
    }

    /**
     * 6. Storewide Segment Distribution
     */
    public function getStoreSegmentSummary(): array
    {
        $users = User::all();
        $total = $users->count();
        $summary = [
            'VIP'               => 0,
            'High Value'        => 0,
            'Returning Customer'=> 0,
            'New Customer'      => 0,
            'At Risk'           => 0,
            'Inactive'          => 0,
        ];

        foreach ($users as $u) {
            $res = $this->calculateCustomerLifecycleSegment($u);
            $seg = $res['segment'];
            if (isset($summary[$seg])) {
                $summary[$seg]++;
            } else {
                $summary['New Customer']++;
            }
        }

        return [
            'total_customers' => $total,
            'segments'        => $summary,
        ];
    }
}
