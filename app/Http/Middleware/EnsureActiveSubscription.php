<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureActiveSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Super Admins bypass subscription checks
        if ($user && $user->hasRole('Super Admin')) {
            return $next($request);
        }

        // Get active branch ID
        $branchId = session('branch_id') ?? ($user ? $user->branch_id : null);

        if (!$branchId) {
            return $next($request); // Handled by other middlewares if no branch
        }

        $subscription = \App\Models\TenantSubscription::where('branch_id', $branchId)->first();

        // If no subscription or it is not active, redirect to billing
        if (!$subscription || !$subscription->isActive()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => 'Subscription inactive or expired. Please update your billing.'], 403);
            }
            // For now, redirect to user billing page. Later we can make a dedicated SaaS billing page.
            return redirect()->route('app-user-view-billing')->with('error', 'Your store subscription is inactive. Please upgrade or renew your plan to continue.');
        }

        return $next($request);
    }
}
