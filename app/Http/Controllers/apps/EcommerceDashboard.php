<?php

namespace App\Http\Controllers\apps;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class EcommerceDashboard extends Controller
{
  public function index()
  {
      // Real Financial KPIs
      $totalSales = Order::sum('total_amount');
      $totalOrders = Order::count();
      $pendingOrders = Order::whereIn('order_status', ['pending', 'processing'])->count();
      $completedOrders = Order::where('order_status', 'completed')->count();

      // Recent orders (last 7)
      $recentOrders = Order::with('customer')
          ->latest()
          ->take(7)
          ->get();

      // Comparison metrics
      $todaySales = Order::whereDate('created_at', now())->sum('total_amount');
      $yesterdaySales = Order::whereDate('created_at', now()->yesterday())->sum('total_amount');
      $dailyGrowth = $yesterdaySales > 0 ? (($todaySales - $yesterdaySales) / $yesterdaySales) * 100 : ($todaySales > 0 ? 100 : 0);

      $thisWeekSales = Order::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('total_amount');
      $lastWeekSales = Order::whereBetween('created_at', [now()->subWeek()->startOfWeek(), now()->subWeek()->endOfWeek()])->sum('total_amount');
      $weeklyGrowth = $lastWeekSales > 0 ? (($thisWeekSales - $lastWeekSales) / $lastWeekSales) * 100 : ($thisWeekSales > 0 ? 100 : 0);

      // Financial Estimates based on orders
      $totalTransactions = $totalOrders;
      $totalProfit = round($totalSales * 0.28, 2); 
      $totalExpenses = round($totalSales * 0.12, 2);
      $totalRevenue = round($totalSales - $totalExpenses, 2);
      
      $walletBalance = round($totalRevenue * 0.65, 2);
      $paypalBalance = round($totalRevenue * 0.35, 2);

      // 7-day Sales & Expense trends for ApexCharts
      $chartData = [
          'profit' => [],
          'expenses' => [],
          'performance' => [],
          'categories' => []
      ];

      for ($i = 6; $i >= 0; $i--) {
          $date = now()->subDays($i);
          $dailySales = Order::whereDate('created_at', $date)->sum('total_amount');
          $chartData['profit'][] = round($dailySales * 0.28, 2);
          $chartData['expenses'][] = round($dailySales * 0.12, 2);
      }
      
      for ($i = 5; $i >= 0; $i--) {
          $month = now()->subMonths($i);
          $monthlySales = Order::whereMonth('created_at', $month->month)->whereYear('created_at', $month->year)->sum('total_amount');
          $chartData['performance'][] = round($monthlySales, 2);
          $chartData['categories'][] = $month->format('M');
      }

      // Catalog & Stock Metrics
      $totalProducts = Product::count();
      $lowStockProducts = Product::where('qty', '<', 10)->where('qty', '>', 0)->take(5)->get();
      $lowStockCount = Product::where('qty', '<', 10)->where('qty', '>', 0)->count();
      $outOfStockCount = Product::where('qty', '<=', 0)->count();
      $totalCustomers = User::count();

      return view('content.apps.app-ecommerce-dashboard', compact(
          'totalSales',
          'totalOrders',
          'pendingOrders',
          'completedOrders',
          'totalTransactions',
          'recentOrders',
          'todaySales',
          'dailyGrowth',
          'thisWeekSales',
          'weeklyGrowth',
          'totalProfit',
          'totalExpenses',
          'totalRevenue',
          'walletBalance',
          'paypalBalance',
          'chartData',
          'totalProducts',
          'lowStockProducts',
          'lowStockCount',
          'outOfStockCount',
          'totalCustomers'
      ));
  }
}
