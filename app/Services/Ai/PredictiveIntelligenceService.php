<?php

namespace App\Services\Ai;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\StockMovement;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class PredictiveIntelligenceService
{
    /**
     * Generate comprehensive Daily Business Brief
     */
    public function getDailyBusinessBrief(?int $branchId = null): array
    {
        $yesterdayStart = Carbon::yesterday()->startOfDay();
        $yesterdayEnd = Carbon::yesterday()->endOfDay();
        $dayBeforeYesterdayStart = Carbon::yesterday()->subDay()->startOfDay();
        $dayBeforeYesterdayEnd = Carbon::yesterday()->subDay()->endOfDay();

        // Query yesterday metrics
        $queryYesterday = Order::whereBetween('created_at', [$yesterdayStart, $yesterdayEnd]);
        if ($branchId) {
            $queryYesterday->where('branch_id', $branchId);
        }
        $yesterdaySales = (float)$queryYesterday->sum('total_amount');
        $yesterdayOrders = $queryYesterday->count();
        $yesterdayAov = $yesterdayOrders > 0 ? ($yesterdaySales / $yesterdayOrders) : 0;

        // Query day before yesterday for delta calculations
        $queryPrev = Order::whereBetween('created_at', [$dayBeforeYesterdayStart, $dayBeforeYesterdayEnd]);
        if ($branchId) {
            $queryPrev->where('branch_id', $branchId);
        }
        $prevSales = (float)$queryPrev->sum('total_amount');
        $salesDelta = $prevSales > 0 ? (($yesterdaySales - $prevSales) / $prevSales) * 100 : 0;

        // Critical stockouts (items with < 5 days of runway)
        $stockoutRisks = $this->predictStockoutRisks($branchId, 5);

        // Top moving products yesterday
        $topProducts = OrderItem::whereHas('order', function ($q) use ($yesterdayStart, $yesterdayEnd, $branchId) {
                $q->whereBetween('created_at', [$yesterdayStart, $yesterdayEnd]);
                if ($branchId) $q->where('branch_id', $branchId);
            })
            ->select('product_id', DB::raw('SUM(qty) as total_units'), DB::raw('SUM(total) as total_revenue'))
            ->groupBy('product_id')
            ->orderByDesc('total_units')
            ->with('product:id,name,price,image')
            ->take(5)
            ->get();

        return [
            'date'              => Carbon::yesterday()->format('Y-m-d'),
            'yesterday_sales'   => $yesterdaySales,
            'yesterday_orders'  => $yesterdayOrders,
            'yesterday_aov'     => $yesterdayAov,
            'sales_delta_pct'   => round($salesDelta, 1),
            'top_products'      => $topProducts,
            'stockout_risks'    => $stockoutRisks,
            'critical_skus_count' => count($stockoutRisks),
            'ai_summary_text'   => $this->generateBriefNarrative($yesterdaySales, $salesDelta, count($stockoutRisks)),
        ];
    }

    /**
     * Calculate 30-day velocity and predict stockout risk
     */
    public function predictStockoutRisks(?int $branchId = null, int $thresholdDays = 7): array
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Fetch sales units per product in last 30 days
        $salesVelocity = OrderItem::whereHas('order', function ($q) use ($thirtyDaysAgo, $branchId) {
                $q->where('created_at', '>=', $thirtyDaysAgo);
                if ($branchId) $q->where('branch_id', $branchId);
            })
            ->select('product_id', DB::raw('SUM(qty) as units_sold'))
            ->groupBy('product_id')
            ->pluck('units_sold', 'product_id');

        $products = Product::where('is_active', true)->get();
        $risks = [];

        foreach ($products as $p) {
            $sold30Days = $salesVelocity[$p->id] ?? 0;
            $dailyVelocity = $sold30Days > 0 ? ($sold30Days / 30.0) : 0.05; // minimum baseline velocity
            $daysRemaining = $p->qty > 0 ? floor($p->qty / $dailyVelocity) : 0;

            if ($daysRemaining <= $thresholdDays || $p->qty <= $p->min_stock) {
                $recommendedReorder = max($p->max_stock - $p->qty, ceil($dailyVelocity * 21)); // 3 weeks of stock
                $risks[] = [
                    'product_id'          => $p->id,
                    'name'                => $p->name,
                    'sku'                 => $p->sku,
                    'current_qty'         => $p->qty,
                    'daily_velocity'      => round($dailyVelocity, 2),
                    'days_runway'         => $daysRemaining,
                    'is_out_of_stock'     => $p->qty <= 0,
                    'recommended_reorder' => (int)$recommendedReorder,
                ];
            }
        }

        usort($risks, fn($a, $b) => $a['days_runway'] <=> $b['days_runway']);
        return $risks;
    }

    /**
     * Calculate Fraud Risk Score (0-100) for an order
     */
    public function calculateOrderFraudRisk(Order $order): array
    {
        $riskScore = 0;
        $reasons = [];

        // 1. Unusually large order value
        $avgOrderValue = (float)Order::where('order_status', '!=', 'cancelled')->avg('total_amount') ?: 50.0;
        if ($order->total_amount > ($avgOrderValue * 4)) {
            $riskScore += 25;
            $reasons[] = 'Order total is 4x higher than platform average.';
        }

        // 2. High frequency / Velocity check from same user
        if ($order->user_id) {
            $recentOrdersCount = Order::where('user_id', $order->user_id)
                ->where('created_at', '>=', Carbon::now()->subHours(2))
                ->count();
            if ($recentOrdersCount >= 3) {
                $riskScore += 30;
                $reasons[] = "High velocity: {$recentOrdersCount} orders placed in the last 2 hours.";
            }
        }

        // 3. High value COD order
        if (strtolower($order->payment_method) === 'cod' && $order->total_amount > 200) {
            $riskScore += 20;
            $reasons[] = 'High-value Cash on Delivery (COD) order.';
        }

        // 4. Incomplete or suspicious address length
        if (strlen(trim($order->shipping_address ?? '')) < 15) {
            $riskScore += 15;
            $reasons[] = 'Shipping address is unusually brief.';
        }

        $riskLevel = $riskScore >= 60 ? 'HIGH' : ($riskScore >= 30 ? 'MEDIUM' : 'LOW');

        return [
            'risk_score' => min(100, $riskScore),
            'risk_level' => $riskLevel,
            'reasons'    => $reasons,
            'action'     => $riskLevel === 'HIGH' ? 'Flag for manual verification' : 'Auto-fulfill approved',
        ];
    }

    /**
     * Generate narrative brief summary
     */
    private function generateBriefNarrative(float $yesterdaySales, float $deltaPct, int $stockoutCount): string
    {
        $trend = $deltaPct >= 0 ? "up {$deltaPct}%" : "down " . abs($deltaPct) . "%";
        $stockNote = $stockoutCount > 0 ? " Attention needed: {$stockoutCount} SKUs are approaching stockout within 5 days." : " Inventory levels remain stable across branches.";

        return "Yesterday's store revenue reached $" . number_format($yesterdaySales, 2) . " ({$trend} vs previous day).{$stockNote}";
    }
}
