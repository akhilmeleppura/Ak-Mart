<?php

namespace App\Services\Ai;

use App\Models\Category;
use App\Models\Expense;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class BusinessIntelligenceService
{
    /**
     * 1. Centralized KPI Registry
     */
    public function getKpiRegistry(): array
    {
        return [
            'gross_revenue' => [
                'name'        => 'Gross Revenue',
                'formula'     => 'SUM(orders.total_amount WHERE payment_status = "paid")',
                'description' => 'Total collected transaction revenue before deductions.',
                'data_source' => 'orders',
                'format'      => 'currency',
            ],
            'aov' => [
                'name'        => 'Average Order Value (AOV)',
                'formula'     => 'Gross Revenue / Total Paid Orders',
                'description' => 'Average spend per completed customer transaction.',
                'data_source' => 'orders',
                'format'      => 'currency',
            ],
            'net_profit' => [
                'name'        => 'Net Profit',
                'formula'     => 'Gross Revenue - COGS - Total Expenses - Refunds',
                'description' => 'Net commercial earnings after direct and operational costs.',
                'data_source' => 'orders, order_items, expenses, order_returns',
                'format'      => 'currency',
            ],
            'profit_margin' => [
                'name'        => 'Net Profit Margin %',
                'formula'     => '(Net Profit / Gross Revenue) * 100',
                'description' => 'Percentage of top-line revenue converted to net profit.',
                'data_source' => 'orders, expenses',
                'format'      => 'percentage',
            ],
            'return_rate' => [
                'name'        => 'Return Rate %',
                'formula'     => '(Total Returns / Total Orders) * 100',
                'description' => 'Percentage of completed orders returned by customers.',
                'data_source' => 'order_returns, orders',
                'format'      => 'percentage',
            ],
        ];
    }

    /**
     * 2. Comprehensive Executive Daily Business Brief
     */
    public function getExecutiveDailyBrief(): array
    {
        $todayStart = Carbon::today();

        // 1. Sales
        $todayOrders = Order::withoutGlobalScopes()
            ->where('payment_status', 'paid')
            ->where('created_at', '>=', $todayStart)
            ->get();

        $todayRevenue = (float)$todayOrders->sum('total_amount');
        $todayOrderCount = $todayOrders->count();
        $todayAov = $todayOrderCount > 0 ? round($todayRevenue / $todayOrderCount, 2) : 0;

        // 2. Profit & Expenses
        $todayExpenses = (float)Expense::where('date', '>=', $todayStart->toDateString())->sum('amount');
        $estimatedCogs = $todayRevenue * 0.60;
        $netProfit = max(0, $todayRevenue - $estimatedCogs - $todayExpenses);
        $marginPct = $todayRevenue > 0 ? round(($netProfit / $todayRevenue) * 100, 1) : 0;

        // 3. Customers
        $newCustomersToday = User::where('created_at', '>=', $todayStart)->count();

        // 4. Inventory Runway
        $lowStockCount = Product::where('is_active', true)->where('qty', '<=', 5)->count();
        $outOfStockCount = Product::where('is_active', true)->where('qty', '<=', 0)->count();

        return [
            'brief_date'         => Carbon::now()->toFormattedDateString(),
            'executive_summary'  => "Today AKMart recorded \${$todayRevenue} in gross revenue across {$todayOrderCount} paid orders with an AOV of \${$todayAov}.",
            'sales' => [
                'revenue'        => $todayRevenue,
                'revenue_formatted' => '$' . number_format($todayRevenue, 2),
                'order_count'    => $todayOrderCount,
                'aov'            => $todayAov,
                'aov_formatted'  => '$' . number_format($todayAov, 2),
            ],
            'profit' => [
                'net_profit'     => $netProfit,
                'net_profit_formatted' => '$' . number_format($netProfit, 2),
                'margin_pct'     => $marginPct,
                'expenses'       => $todayExpenses,
            ],
            'customers' => [
                'new_acquisitions' => $newCustomersToday,
            ],
            'inventory' => [
                'low_stock_skus'   => $lowStockCount,
                'out_of_stock_skus'=> $outOfStockCount,
            ],
            'recommendations' => [
                $lowStockCount > 0 ? "Generate draft purchase orders for {$lowStockCount} low-stock SKUs." : "Inventory runway is stable across all active categories.",
                "Review daily conversion and promote high-margin accessories.",
            ],
            'generated_at'       => Carbon::now()->toDateTimeString(),
        ];
    }

    /**
     * 3. Natural Language Period Comparison Engine
     */
    public function comparePeriods(string $period = 'month'): array
    {
        if ($period === 'week') {
            $currStart = Carbon::now()->startOfWeek();
            $currEnd = Carbon::now();
            $prevStart = Carbon::now()->subWeek()->startOfWeek();
            $prevEnd = Carbon::now()->subWeek()->endOfWeek();
            $label = 'This Week vs Last Week';
        } else {
            $currStart = Carbon::now()->startOfMonth();
            $currEnd = Carbon::now();
            $prevStart = Carbon::now()->subMonth()->startOfMonth();
            $prevEnd = Carbon::now()->subMonth()->endOfMonth();
            $label = 'This Month vs Last Month';
        }

        $currRevenue = (float)Order::withoutGlobalScopes()
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$currStart, $currEnd])
            ->sum('total_amount');

        $prevRevenue = (float)Order::withoutGlobalScopes()
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->sum('total_amount');

        $currOrders = Order::withoutGlobalScopes()
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$currStart, $currEnd])
            ->count();

        $prevOrders = Order::withoutGlobalScopes()
            ->where('payment_status', 'paid')
            ->whereBetween('created_at', [$prevStart, $prevEnd])
            ->count();

        $deltaRevenue = $currRevenue - $prevRevenue;
        $growthPct = $prevRevenue > 0 ? round(($deltaRevenue / $prevRevenue) * 100, 1) : 0;

        return [
            'comparison_type'      => $label,
            'current_period_rev'   => $currRevenue,
            'current_period_rev_fmt' => '$' . number_format($currRevenue, 2),
            'previous_period_rev'  => $prevRevenue,
            'previous_period_rev_fmt' => '$' . number_format($prevRevenue, 2),
            'revenue_delta'        => $deltaRevenue,
            'revenue_delta_fmt'    => ($deltaRevenue >= 0 ? '+$' : '-$') . number_format(abs($deltaRevenue), 2),
            'growth_percentage'    => $growthPct,
            'current_orders'       => $currOrders,
            'previous_orders'      => $prevOrders,
            'growth_status'        => $deltaRevenue >= 0 ? 'Positive Growth' : 'Decline',
        ];
    }

    /**
     * 4. Revenue Decomposition by Category
     */
    public function decomposeRevenueByCategory(): array
    {
        $breakdown = OrderItem::whereHas('order', fn($q) => $q->where('payment_status', 'paid'))
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(order_items.total) as revenue'), DB::raw('COUNT(*) as count'))
            ->groupBy('categories.name')
            ->orderByDesc('revenue')
            ->get();

        $total = $breakdown->sum('revenue');

        $results = [];
        foreach ($breakdown as $row) {
            $share = $total > 0 ? round(($row->revenue / $total) * 100, 1) : 0;
            $results[] = [
                'category'     => $row->name,
                'revenue'      => (float)$row->revenue,
                'revenue_fmt'  => '$' . number_format($row->revenue, 2),
                'market_share' => "{$share}%",
            ];
        }

        return [
            'total_revenue' => (float)$total,
            'categories'    => $results,
        ];
    }

    /**
     * 5. Read-Only Scenario & What-If Simulation
     */
    public function runScenarioSimulation(string $scenarioType, array $params = []): array
    {
        $discountPct = (float)($params['discount_pct'] ?? 10);
        $expectedVolumeIncrease = (float)($params['volume_increase_pct'] ?? 15);

        $baselineOrders = Order::withoutGlobalScopes()->where('payment_status', 'paid')->get();
        $baselineRevenue = (float)$baselineOrders->sum('total_amount');
        $baselineCount = $baselineOrders->count();

        $simulatedPriceFactor = (100 - $discountPct) / 100;
        $simulatedVolumeFactor = (100 + $expectedVolumeIncrease) / 100;

        $projectedRevenue = round($baselineRevenue * $simulatedPriceFactor * $simulatedVolumeFactor, 2);
        $projectedDelta = round($projectedRevenue - $baselineRevenue, 2);

        return [
            'type'                   => 'SIMULATION_NOT_GUARANTEED',
            'scenario'               => "{$discountPct}% Price Discount with {$expectedVolumeIncrease}% Projected Volume Lift",
            'baseline_revenue'       => $baselineRevenue,
            'baseline_revenue_fmt'   => '$' . number_format($baselineRevenue, 2),
            'projected_revenue'      => $projectedRevenue,
            'projected_revenue_fmt'  => '$' . number_format($projectedRevenue, 2),
            'projected_delta'        => $projectedDelta,
            'projected_delta_fmt'    => ($projectedDelta >= 0 ? '+$' : '-$') . number_format(abs($projectedDelta), 2),
            'feasibility_verdict'    => $projectedDelta >= 0 ? 'Favorable Scenario' : 'Unfavorable — Volume lift insufficient to offset margin discount',
            'disclaimer'             => 'Statistical simulation model based on price elasticity assumptions. Actual market outcomes may vary.',
        ];
    }
}
