<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Models\Expense;
use App\Models\PurchaseOrder;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', now()->subDays(30)->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->format('Y-m-d'));
        $tab = $request->input('tab', 'sales');

        $branchId = session('branch_id');
        $ordersQuery = Order::whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        if ($branchId && !auth()->user()?->isSupremeAdmin()) {
            $ordersQuery->where('branch_id', $branchId);
        }

        // 1. Sales Metrics
        $totalSales = (float) $ordersQuery->sum('total_amount');
        $totalOrders = (int) $ordersQuery->count();
        $avgOrderValue = $totalOrders > 0 ? round($totalSales / $totalOrders, 2) : 0;

        // 2. Expenses & Net Profit
        $expensesQuery = Expense::whereBetween('expense_date', [$startDate, $endDate]);
        if ($branchId && !auth()->user()?->isSupremeAdmin()) {
            $expensesQuery->where('branch_id', $branchId);
        }
        $totalExpenses = (float) $expensesQuery->sum('amount');
        $cogs = round($totalSales * 0.65, 2); // Estimated Cost of Goods Sold (65%)
        $grossProfit = round($totalSales - $cogs, 2);
        $netProfit = round($grossProfit - $totalExpenses, 2);
        $estimatedTax = round($totalSales * 0.05, 2);

        // 3. Product Analytics
        $products = Product::all();
        $totalInventoryValuation = $products->sum(function($p) {
            return $p->qty * $p->price;
        });

        $topSellingItems = DB::table('order_items')
            ->select('product_name', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(total_price) as total_revenue'))
            ->groupBy('product_name')
            ->orderByDesc('total_qty')
            ->take(8)
            ->get();

        $slowMovers = Product::where('qty', '>', 10)->orderBy('updated_at', 'asc')->take(5)->get();

        // 4. Deterministic 7-Day and 30-Day Sales Forecasting
        $dailyHistoricalSales = Order::where('created_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(total_amount) as daily_total'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'asc')
            ->pluck('daily_total', 'date')
            ->toArray();

        $daysCount = count($dailyHistoricalSales);
        $historicalSum = array_sum($dailyHistoricalSales);
        $dailyAverage = $daysCount > 0 ? ($historicalSum / $daysCount) : ($totalSales / 30);

        // 7-day forecast
        $forecast7Day = [];
        $forecast7DayTotal = 0;
        for ($i = 1; $i <= 7; $i++) {
            $dayDate = now()->addDays($i);
            // Day of week multiplier (Weekends ~1.2x, Weekdays ~0.95x)
            $dow = $dayDate->dayOfWeek;
            $multiplier = ($dow === 0 || $dow === 6) ? 1.18 : 0.96;
            $predicted = round($dailyAverage * $multiplier, 2);
            $forecast7Day[] = [
                'date'      => $dayDate->format('D, d M'),
                'predicted' => $predicted,
            ];
            $forecast7DayTotal += $predicted;
        }

        // 30-day forecast total
        $forecast30DayTotal = round($dailyAverage * 30, 2);

        // 5. Purchases & Suppliers
        $purchases = PurchaseOrder::with('supplier')->latest()->take(10)->get();
        $totalPurchasesAmount = PurchaseOrder::sum('total_amount');

        // 6. Recent Stock Movements
        $stockMovements = StockMovement::with(['product', 'user'])->latest()->take(15)->get();

        // 7. Customers Summary
        $customersCount = User::where('user_type', 'customer')->count();
        $topCustomers = User::where('user_type', 'customer')->withCount('orders')->take(8)->get();

        return view('content.apps.reports.index', compact(
            'startDate',
            'endDate',
            'tab',
            'totalSales',
            'totalOrders',
            'avgOrderValue',
            'totalExpenses',
            'cogs',
            'grossProfit',
            'netProfit',
            'estimatedTax',
            'totalInventoryValuation',
            'topSellingItems',
            'slowMovers',
            'forecast7Day',
            'forecast7DayTotal',
            'forecast30DayTotal',
            'purchases',
            'totalPurchasesAmount',
            'stockMovements',
            'customersCount',
            'topCustomers'
        ));
    }

    public function exportCsv(Request $request)
    {
        $fileName = 'AK_Mart_Sales_Report_' . date('Y-m-d') . '.csv';
        $orders = Order::with('customer')->latest()->take(1000)->get();

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = ['Order Number', 'Customer', 'Date', 'Total Amount', 'Payment Method', 'Payment Status', 'Order Status'];

        $callback = function() use($orders, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->customer?->name ?? 'Guest Customer',
                    $order->created_at->format('Y-m-d H:i'),
                    '$' . number_format($order->total_amount, 2),
                    ucfirst($order->payment_method),
                    ucfirst($order->payment_status),
                    ucfirst($order->order_status),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
