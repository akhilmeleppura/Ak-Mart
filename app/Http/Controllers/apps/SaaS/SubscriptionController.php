<?php

namespace App\Http\Controllers\apps\SaaS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\SubscriptionPlan;
use App\Models\TenantSubscription;

class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        
        $user = auth()->user();
        $branchId = session('branch_id') ?? ($user ? $user->branch_id : null);
        
        $currentSubscription = null;
        if ($branchId) {
            $currentSubscription = TenantSubscription::with('plan')->where('branch_id', $branchId)->first();
        }

        return view('content.apps.saas.billing', compact('plans', 'currentSubscription'));
    }

    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'payment_method_id' => 'required|string', // Stripe Payment Method ID
        ]);

        $user = auth()->user();
        $branchId = session('branch_id') ?? ($user ? $user->branch_id : null);

        if (!$branchId) {
            return response()->json(['error' => 'No active store selected.'], 400);
        }

        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        // Mock Stripe Subscription Logic for now
        // In a real scenario, you'd use Laravel Cashier: $user->newSubscription('default', $plan->stripe_price_id)->create($request->payment_method_id);
        
        $subscription = TenantSubscription::updateOrCreate(
            ['branch_id' => $branchId],
            [
                'subscription_plan_id' => $plan->id,
                'status' => 'active',
                'current_period_start' => now(),
                'current_period_end' => now()->addDays($plan->billing_cycle_days),
                'stripe_subscription_id' => 'sub_mock_' . uniqid(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => "Successfully subscribed to the {$plan->name} plan."
        ]);
    }
    
    public function cancel()
    {
        $user = auth()->user();
        $branchId = session('branch_id') ?? ($user ? $user->branch_id : null);

        if (!$branchId) {
            return back()->with('error', 'No active store selected.');
        }

        $subscription = TenantSubscription::where('branch_id', $branchId)->first();
        
        if ($subscription) {
            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);
        }

        return back()->with('success', 'Subscription canceled successfully. You will lose access at the end of your billing period.');
    }
}
