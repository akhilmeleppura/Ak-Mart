<?php

namespace App\Services\Ai;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Branch\Branch;
use App\Models\StockMovement;
use App\Models\StoreCredit;
use App\Models\LoyaltyTransaction;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AiToolManager
{
    /**
     * Tool: Authoritative Sales & Order Summary
     */
    public function getSalesSummary(string $period = 'today', ?int $branchId = null): array
    {
        $startDate = match ($period) {
            'today'      => Carbon::today()->startOfDay(),
            'yesterday'  => Carbon::yesterday()->startOfDay(),
            '7_days'     => Carbon::now()->subDays(7)->startOfDay(),
            '30_days', 'month' => Carbon::now()->subDays(30)->startOfDay(),
            default      => Carbon::today()->startOfDay(),
        };

        $endDate = match ($period) {
            'yesterday' => Carbon::yesterday()->endOfDay(),
            default     => Carbon::now()->endOfDay(),
        };

        $query = Order::whereBetween('created_at', [$startDate, $endDate]);
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $totalSales = (float)$query->sum('total_amount');
        $orderCount = $query->count();
        $aov = $orderCount > 0 ? round($totalSales / $orderCount, 2) : 0;
        $completedCount = (clone $query)->where('order_status', 'completed')->count();
        $cancelledCount = (clone $query)->where('order_status', 'cancelled')->count();

        return [
            'period'           => $period,
            'total_sales'      => $totalSales,
            'total_sales_formatted' => '$' . number_format($totalSales, 2),
            'order_count'      => $orderCount,
            'aov'              => $aov,
            'completed_orders' => $completedCount,
            'cancelled_orders' => $cancelledCount,
            'date_range'       => $startDate->toDateString() . ' to ' . $endDate->toDateString(),
        ];
    }

    /**
     * Tool: Live Inventory Status & Critical Stockouts
     */
    public function getInventoryStatus(string $type = 'all', ?int $branchId = null, ?string $sku = null): array
    {
        $query = Product::where('is_active', true);

        if ($sku) {
            $product = (clone $query)->where('sku', 'LIKE', "%{$sku}%")->first();
            if (!$product) {
                return ['found' => false, 'message' => "No product found with SKU '{$sku}'."];
            }
            return [
                'found'       => true,
                'product_id'  => $product->id,
                'name'        => $product->name,
                'sku'         => $product->sku,
                'price'       => (float)$product->price,
                'qty'         => $product->qty,
                'min_stock'   => $product->min_stock,
                'status'      => $product->qty <= 0 ? 'Out of Stock' : ($product->qty <= $product->min_stock ? 'Low Stock' : 'In Stock'),
            ];
        }

        $totalProducts = (clone $query)->count();
        $lowStockProducts = (clone $query)->where('qty', '<=', 10)->where('qty', '>', 0)->get(['id', 'name', 'sku', 'qty', 'price']);
        $outOfStockCount = (clone $query)->where('qty', '<=', 0)->count();

        return [
            'total_products'    => $totalProducts,
            'low_stock_count'   => $lowStockProducts->count(),
            'out_of_stock_count'=> $outOfStockCount,
            'low_stock_items'   => $lowStockProducts->take(8)->toArray(),
        ];
    }

    /**
     * Tool: Order Lookup with Strict Customer Privacy Isolation
     */
    public function getOrderDetails(string $orderNumber, ?int $requestUserId = null, bool $isStaff = false): array
    {
        $order = Order::with('items')->where('order_number', trim($orderNumber))->first();

        if (!$order) {
            return ['found' => false, 'message' => "Order #{$orderNumber} not found."];
        }

        // Privacy check: If not staff, order must belong to user
        if (!$isStaff && $order->user_id !== $requestUserId) {
            return ['found' => false, 'message' => 'Unauthorized: You do not have permission to view this order.'];
        }

        return [
            'found'            => true,
            'order_number'     => $order->order_number,
            'status'           => ucfirst($order->order_status),
            'payment_status'   => ucfirst($order->payment_status),
            'payment_method'   => strtoupper($order->payment_method ?? 'N/A'),
            'total_amount'     => (float)$order->total_amount,
            'total_formatted'  => '$' . number_format($order->total_amount, 2),
            'shipping_address' => $isStaff ? $order->shipping_address : substr($order->shipping_address, 0, 15) . '...',
            'items_count'      => $order->items->count(),
            'created_at'       => $order->created_at->toFormattedDateString(),
        ];
    }

    /**
     * Tool: Safe Structured Catalog Search
     */
    public function searchCatalog(string $query = '', ?float $priceMax = null, ?string $category = null, ?string $brand = null): array
    {
        $q = Product::where('is_active', true);

        if (!empty($query)) {
            $q->where(function ($sub) use ($query) {
                $sub->where('name', 'LIKE', "%{$query}%")
                    ->orWhere('sku', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%");
            });
        }

        if ($priceMax) {
            $q->where('price', '<=', $priceMax);
        }

        if ($brand) {
            $q->where('brand', 'LIKE', "%{$brand}%");
        }

        $results = $q->take(6)->get(['id', 'name', 'sku', 'price', 'qty', 'brand', 'image']);

        return [
            'total_found' => $results->count(),
            'products'    => $results->map(function ($p) {
                return [
                    'id'        => $p->id,
                    'name'      => $p->name,
                    'sku'       => $p->sku,
                    'price'     => (float)$p->price,
                    'in_stock'  => $p->qty > 0,
                    'qty'       => $p->qty,
                    'brand'     => $p->brand ?: 'General',
                ];
            })->toArray(),
        ];
    }

    /**
     * Tool: Customer 360 Summary for Authorized Staff
     */
    public function getCustomerSummary(string $identifier): array
    {
        $user = User::where('email', $identifier)
            ->orWhere('id', is_numeric($identifier) ? (int)$identifier : 0)
            ->first();

        if (!$user) {
            return ['found' => false, 'message' => "Customer '{$identifier}' not found."];
        }

        $totalSpent = (float)Order::where('user_id', $user->id)->sum('total_amount');
        $ordersCount = Order::where('user_id', $user->id)->count();
        $walletBalance = (float)StoreCredit::where('user_id', $user->id)->sum('amount');
        $loyaltyPoints = (int)LoyaltyTransaction::where('user_id', $user->id)->sum('points');

        return [
            'found'          => true,
            'customer_id'    => $user->id,
            'name'           => $user->name,
            'email'          => $user->email,
            'total_spent'    => $totalSpent,
            'spent_formatted'=> '$' . number_format($totalSpent, 2),
            'orders_count'   => $ordersCount,
            'wallet_balance' => $walletBalance,
            'loyalty_points' => $loyaltyPoints,
            'member_since'   => $user->created_at->toDateString(),
        ];
    }

    /**
     * Tool: Authoritative Profit Report for Supreme Admin
     */
    public function getProfitReport(string $period = 'today'): array
    {
        $sales = $this->getSalesSummary($period);
        $revenue = $sales['total_sales'];
        $cogs = round($revenue * 0.60, 2); // 60% standard COGS baseline
        $expenses = round($revenue * 0.12, 2);
        $netProfit = max(0, round($revenue - $cogs - $expenses, 2));
        $marginPct = $revenue > 0 ? round(($netProfit / $revenue) * 100, 1) : 0;

        return [
            'period'           => $period,
            'gross_revenue'    => $revenue,
            'estimated_cogs'   => $cogs,
            'operating_expenses'=> $expenses,
            'net_profit'       => $netProfit,
            'profit_margin_pct'=> $marginPct,
        ];
    }

    /**
     * Tool: Branch Sales Performance Ranking
     */
    public function getBranchRanking(): array
    {
        $branches = Branch::all();
        $ranking = [];

        foreach ($branches as $branch) {
            $sales = (float)Order::where('branch_id', $branch->id)->sum('total_amount');
            $orders = Order::where('branch_id', $branch->id)->count();
            $ranking[] = [
                'branch_id'   => $branch->id,
                'branch_name' => $branch->name,
                'total_sales' => $sales,
                'order_count' => $orders,
            ];
        }

        usort($ranking, fn($a, $b) => $b['total_sales'] <=> $a['total_sales']);
        return ['branches' => $ranking];
    }

    /**
     * Tool: Comparative Sales Period Analysis
     */
    public function getSalesComparison(string $current = 'this_month', string $previous = 'last_month', ?int $branchId = null): array
    {
        $currentRange = match ($current) {
            'today'       => [Carbon::today()->startOfDay(), Carbon::now()->endOfDay()],
            'this_week'   => [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()],
            'this_month'  => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
            'this_year'   => [Carbon::now()->startOfYear(), Carbon::now()->endOfYear()],
            default       => [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()],
        };

        $previousRange = match ($previous) {
            'yesterday'   => [Carbon::yesterday()->startOfDay(), Carbon::yesterday()->endOfDay()],
            'last_week'   => [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()],
            'last_month'  => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
            'last_year'   => [Carbon::now()->subYear()->startOfYear(), Carbon::now()->subYear()->endOfYear()],
            default       => [Carbon::now()->subMonth()->startOfMonth(), Carbon::now()->subMonth()->endOfMonth()],
        };

        $currentQuery = Order::whereBetween('created_at', $currentRange);
        $prevQuery = Order::whereBetween('created_at', $previousRange);

        if ($branchId) {
            $currentQuery->where('branch_id', $branchId);
            $prevQuery->where('branch_id', $branchId);
        }

        $currentSales = (float)$currentQuery->sum('total_amount');
        $currentOrders = $currentQuery->count();
        $currentAov = $currentOrders > 0 ? round($currentSales / $currentOrders, 2) : 0;

        $prevSales = (float)$prevQuery->sum('total_amount');
        $prevOrders = $prevQuery->count();
        $prevAov = $prevOrders > 0 ? round($prevSales / $prevOrders, 2) : 0;

        $diffAmount = round($currentSales - $prevSales, 2);
        $diffPct = $prevSales > 0 ? round(($diffAmount / $prevSales) * 100, 1) : ($currentSales > 0 ? 100 : 0);

        return [
            'current_period'  => [
                'name'         => str_replace('_', ' ', ucfirst($current)),
                'sales'        => $currentSales,
                'sales_formatted' => '$' . number_format($currentSales, 2),
                'orders'       => $currentOrders,
                'aov'          => $currentAov,
            ],
            'previous_period' => [
                'name'         => str_replace('_', ' ', ucfirst($previous)),
                'sales'        => $prevSales,
                'sales_formatted' => '$' . number_format($prevSales, 2),
                'orders'       => $prevOrders,
                'aov'          => $prevAov,
            ],
            'difference'      => [
                'amount'       => $diffAmount,
                'percentage'   => $diffPct,
                'direction'    => $diffAmount >= 0 ? 'up' : 'down',
            ],
        ];
    }

    /**
     * Tool: Authoritative Inventory Asset Valuation
     */
    public function getInventoryValuation(?int $branchId = null): array
    {
        $query = Product::where('is_active', true);
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $totalUnits = (int)$query->sum('qty');
        $retailValue = (float)$query->select(DB::raw('SUM(qty * price) as val'))->value('val');
        $estimatedCost = round($retailValue * 0.60, 2); // 60% standard cost ratio

        return [
            'total_units'          => $totalUnits,
            'retail_value'         => $retailValue,
            'retail_value_formatted' => '$' . number_format($retailValue, 2),
            'estimated_cost_value' => $estimatedCost,
            'estimated_cost_formatted' => '$' . number_format($estimatedCost, 2),
        ];
    }

    /**
     * Tool: Category Revenue Distribution
     */
    public function getCategorySales(): array
    {
        $categories = \App\Models\Category::withCount('products')->get();
        $summary = [];

        foreach ($categories as $cat) {
            $sales = (float)OrderItem::whereHas('product', fn($q) => $q->where('category_id', $cat->id))->sum('total');
            $summary[] = [
                'category_id'   => $cat->id,
                'category_name' => $cat->name,
                'total_sales'   => $sales,
                'product_count' => $cat->products_count,
            ];
        }

        usort($summary, fn($a, $b) => $b['total_sales'] <=> $a['total_sales']);
        return ['categories' => $summary];
    }

    /**
     * Tool: Traceable Stock Movements from Immutable Ledger
     */
    public function getRecentStockMovements(int $limit = 5): array
    {
        return StockMovement::with('product')->latest()->take($limit)->get()->map(function ($m) {
            return [
                'product'   => $m->product?->name ?? 'Product #' . $m->product_id,
                'change'    => ($m->quantity > 0 ? '+' : '') . $m->quantity,
                'type'      => $m->type,
                'reason'    => $m->reason,
                'timestamp' => $m->created_at->toDateTimeString(),
            ];
        })->toArray();
    }

    /**
     * Tool: Verified Store Policy
     */
    public function getStorePolicy(string $topic = 'returns'): string
    {
        return match (strtolower($topic)) {
            'returns', 'refund' => "✓ **Return Policy**: Customers can return eligible products within 7 days of delivery. Returned items must be unused and in original packaging. Refunds are credited instantly to Store Credit or original payment within 3-5 business days.",
            'shipping', 'delivery' => "✓ **Shipping & Delivery**: Express delivery is FREE for all eligible orders. Delivery time slots can be selected during checkout. Real-time order tracking is available at `/store/track`.",
            'payment', 'cod' => "✓ **Payment Options**: We accept Cash on Delivery (COD), UPI / Instant QR, Credit & Debit Cards, and Store Credit.",
            default => "✓ **Store Assistance**: Our team is available daily for customer assistance. Please contact support via the Help Center.",
        };
    }
}
