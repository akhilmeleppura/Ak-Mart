<?php

namespace App\Http\Controllers\apps\SaaS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\TenantSubscription;
use App\Models\SubscriptionPlan;
use App\Models\OrderTransaction;
use App\Models\User;
use Modules\General\App\Models\Branch;
use Illuminate\Support\Facades\DB;

class PlatformAnalyticsController extends Controller
{
    public function index()
    {
        // ─── GMV (Gross Merchandise Value) ──────────────────────────────
        $totalGMV      = Order::sum('total_amount') ?? 0;
        $thisMonthGMV  = Order::whereMonth('created_at', now()->month)->sum('total_amount') ?? 0;
        $lastMonthGMV  = Order::whereMonth('created_at', now()->subMonth()->month)->sum('total_amount') ?? 0;
        $gmvGrowth     = $lastMonthGMV > 0 ? round((($thisMonthGMV - $lastMonthGMV) / $lastMonthGMV) * 100, 1) : 100;

        // ─── MRR (Monthly Recurring Revenue from subscriptions) ─────────
        $activeSubs    = TenantSubscription::where('status', 'active')->with('plan')->get();
        $mrr           = $activeSubs->sum(fn($s) => $s->plan ? $s->plan->price : 0);
        $arr           = round($mrr * 12, 2);

        // ─── Churn Rate ──────────────────────────────────────────────────
        $canceledThisMonth = TenantSubscription::where('status', 'canceled')
            ->whereMonth('canceled_at', now()->month)->count();
        $totalAtStartOfMonth = TenantSubscription::whereDate('created_at', '<=', now()->startOfMonth())->count();
        $churnRate = $totalAtStartOfMonth > 0 ? round(($canceledThisMonth / $totalAtStartOfMonth) * 100, 2) : 0;

        // ─── Store / Tenant Stats ────────────────────────────────────────
        $totalStores   = Branch::count();
        $activeStores  = $activeSubs->count();
        $trialStores   = TenantSubscription::where('status', 'trialing')->count();
        $canceledStores = TenantSubscription::where('status', 'canceled')->count();

        // ─── User Stats ──────────────────────────────────────────────────
        $totalUsers      = User::count();
        $newUsersThisMonth = User::whereMonth('created_at', now()->month)->count();

        // ─── Platform Fees Collected ─────────────────────────────────────
        $totalPlatformFees = OrderTransaction::sum('platform_fee') ?? 0;
        $feesThisMonth     = OrderTransaction::whereMonth('created_at', now()->month)->sum('platform_fee') ?? 0;

        // ─── Revenue chart - last 6 months GMV ───────────────────────────
        $revenueChart = [];
        $revenueLabels = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $revenueLabels[] = $month->format('M Y');
            $revenueChart['gmv'][]  = round(Order::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)->sum('total_amount'), 2);
            $revenueChart['fees'][] = round(OrderTransaction::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)->sum('platform_fee'), 2);
        }

        // ─── Store growth - new stores per month (last 6) ─────────────────
        $storeGrowth = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $storeGrowth[] = Branch::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)->count();
        }

        // ─── Subscription plan distribution ──────────────────────────────
        $planDistribution = TenantSubscription::where('status', 'active')
            ->select('subscription_plan_id', DB::raw('count(*) as count'))
            ->groupBy('subscription_plan_id')
            ->with('plan')
            ->get()
            ->map(fn($s) => ['name' => $s->plan->name ?? 'Unknown', 'count' => $s->count]);

        // ─── Top revenue orders (last 10) ─────────────────────────────────
        $topOrders = Order::orderBy('total_amount', 'desc')->take(10)->get();

        // ─── Recent subscriptions ─────────────────────────────────────────
        $recentSubscriptions = TenantSubscription::with(['branch', 'plan'])
            ->latest()->take(8)->get();

        return view('content.apps.saas.analytics', compact(
            'totalGMV', 'thisMonthGMV', 'gmvGrowth',
            'mrr', 'arr', 'churnRate',
            'totalStores', 'activeStores', 'trialStores', 'canceledStores',
            'totalUsers', 'newUsersThisMonth',
            'totalPlatformFees', 'feesThisMonth',
            'revenueChart', 'revenueLabels', 'storeGrowth',
            'planDistribution', 'topOrders', 'recentSubscriptions'
        ));
    }
}
