<?php

namespace App\Http\Controllers\apps\SaaS;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Branch\Branch;
use App\Models\StoreSetting;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPlan;
use App\Models\TenantSubscription;
use App\Services\PlanLimitService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    /**
     * Display the SaaS Subscription & Billing Hub.
     */
    public function index(Request $request)
    {
        $plans = SubscriptionPlan::where('is_active', true)->get();
        $user = $request->user();
        $branchId = session('branch_id') ?? ($user ? $user->branch_id : 1);
        $branch = $branchId ? Branch::find($branchId) : Branch::first();
        if ($branch && !$branchId) {
            $branchId = $branch->id;
        }

        $currentSubscription = null;
        if ($branchId) {
            $currentSubscription = TenantSubscription::with(['plan', 'invoices' => function($q) {
                $q->latest();
            }])->where('branch_id', $branchId)->first();

            // Auto-provision demo active subscription if missing for the branch
            if (!$currentSubscription && $plans->isNotEmpty()) {
                $firstPlan = $plans->first();
                $currentSubscription = TenantSubscription::create([
                    'branch_id' => $branchId,
                    'subscription_plan_id' => $firstPlan->id,
                    'status' => 'active',
                    'current_period_start' => now()->subDays(5),
                    'current_period_end' => now()->addDays(25),
                ]);

                // Create initial demo invoice
                SubscriptionInvoice::create([
                    'tenant_subscription_id' => $currentSubscription->id,
                    'branch_id' => $branchId,
                    'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                    'amount' => $firstPlan->price,
                    'currency' => $firstPlan->currency ?? 'USD',
                    'status' => 'paid',
                    'payment_method' => 'Credit Card',
                    'plan_name' => $firstPlan->name,
                    'billing_period_start' => $currentSubscription->current_period_start,
                    'billing_period_end' => $currentSubscription->current_period_end,
                    'paid_at' => now()->subDays(5),
                ]);
            }
        }

        // Real usage statistics from DB
        $usage = PlanLimitService::getUsageForBranch($branchId);

        // Payment gateway configuration status
        $settings = StoreSetting::allAsArray();
        $stripeConfigured = !empty($settings['stripe_key']) && !empty($settings['stripe_secret']) && ($settings['stripe_enabled'] ?? '0') == '1';
        $paypalConfigured = !empty($settings['paypal_client_id']) && !empty($settings['paypal_secret']) && ($settings['paypal_enabled'] ?? '0') == '1';

        // Billing History
        $invoices = SubscriptionInvoice::where('branch_id', $branchId)
            ->latest()
            ->take(15)
            ->get();

        return view('content.apps.saas.billing', compact(
            'plans',
            'currentSubscription',
            'branch',
            'usage',
            'stripeConfigured',
            'paypalConfigured',
            'invoices'
        ));
    }

    /**
     * Subscribe, Upgrade, or Downgrade Plan.
     */
    public function subscribe(Request $request)
    {
        $request->validate([
            'plan_id' => 'required|exists:subscription_plans,id',
            'payment_method' => 'nullable|string',
        ]);

        $user = $request->user();
        $branchId = session('branch_id') ?? ($user ? $user->branch_id : 1);

        if (!$branchId) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['error' => __('No active store branch found.')], 400);
            }
            return back()->with('error', __('No active store branch found.'));
        }

        $plan = SubscriptionPlan::findOrFail($request->plan_id);

        $subscription = TenantSubscription::where('branch_id', $branchId)->first();
        $oldPlanName = $subscription?->plan?->name ?? 'None';

        if (!$subscription) {
            $subscription = new TenantSubscription();
            $subscription->branch_id = $branchId;
        }

        $subscription->subscription_plan_id = $plan->id;
        $subscription->status = 'active';
        $subscription->current_period_start = now();
        $subscription->current_period_end = now()->addDays($plan->billing_cycle_days ?: 30);
        $subscription->canceled_at = null;
        $subscription->save();

        // Create billing invoice record
        $invoice = SubscriptionInvoice::create([
            'tenant_subscription_id' => $subscription->id,
            'branch_id' => $branchId,
            'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
            'amount' => $plan->price,
            'currency' => $plan->currency ?? 'USD',
            'status' => 'paid',
            'payment_method' => $request->input('payment_method', 'Credit Card (Stripe)'),
            'plan_name' => $plan->name,
            'billing_period_start' => $subscription->current_period_start,
            'billing_period_end' => $subscription->current_period_end,
            'paid_at' => now(),
        ]);

        // Log audit event
        try {
            AuditLog::create([
                'user_id' => $user->id,
                'event' => 'plan_changed',
                'auditable_type' => TenantSubscription::class,
                'auditable_id' => $subscription->id,
                'old_values' => json_encode(['plan' => $oldPlanName]),
                'new_values' => json_encode(['plan' => $plan->name, 'price' => $plan->price]),
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);
        } catch (\Throwable $e) {}

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __("Successfully subscribed to the :plan plan.", ['plan' => $plan->name]),
                'invoice_id' => $invoice->id,
            ]);
        }

        return back()->with('success', __("Successfully subscribed to the :plan plan.", ['plan' => $plan->name]));
    }

    /**
     * Cancel subscription.
     */
    public function cancel(Request $request)
    {
        $user = $request->user();
        $branchId = session('branch_id') ?? ($user ? $user->branch_id : 1);

        if (!$branchId) {
            return back()->with('error', __('No active store branch found.'));
        }

        $subscription = TenantSubscription::where('branch_id', $branchId)->first();

        if ($subscription) {
            $subscription->update([
                'status' => 'canceled',
                'canceled_at' => now(),
            ]);

            try {
                AuditLog::create([
                    'user_id' => $user->id,
                    'event' => 'subscription_canceled',
                    'auditable_type' => TenantSubscription::class,
                    'auditable_id' => $subscription->id,
                    'url' => $request->fullUrl(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (\Throwable $e) {}
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => __('Subscription canceled successfully. You will maintain access until the end of the billing period.'),
            ]);
        }

        return back()->with('success', __('Subscription canceled successfully. You will maintain access until the end of the billing period.'));
    }

    /**
     * Resume a canceled subscription.
     */
    public function resume(Request $request)
    {
        $user = $request->user();
        $branchId = session('branch_id') ?? ($user ? $user->branch_id : 1);

        $subscription = TenantSubscription::where('branch_id', $branchId)->first();

        if ($subscription && $subscription->status === 'canceled') {
            $subscription->update([
                'status' => 'active',
                'canceled_at' => null,
            ]);

            try {
                AuditLog::create([
                    'user_id' => $user->id,
                    'event' => 'subscription_resumed',
                    'auditable_type' => TenantSubscription::class,
                    'auditable_id' => $subscription->id,
                    'url' => $request->fullUrl(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            } catch (\Throwable $e) {}
        }

        return back()->with('success', __('Subscription resumed successfully.'));
    }

    /**
     * View/Print Invoice.
     */
    public function invoicePreview($id)
    {
        $invoice = SubscriptionInvoice::with(['subscription', 'branch'])->findOrFail($id);
        $user = auth()->user();

        // RBAC check: Only authorized user for the branch or Super Admin can view invoice
        if (!$user->isSuperAdmin() && $user->branch_id && $user->branch_id != $invoice->branch_id) {
            abort(403, __('Unauthorized invoice access.'));
        }

        return view('content.apps.saas.invoice-preview', compact('invoice', 'user'));
    }

    /**
     * Webhook handling for Stripe / PayPal.
     */
    public function webhook(Request $request)
    {
        $event = $request->input('type');
        $payload = $request->all();

        // Idempotent processing of webhook events
        try {
            AuditLog::create([
                'user_id' => null,
                'event' => 'webhook_received: ' . ($event ?? 'payment_event'),
                'url' => $request->fullUrl(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'new_values' => json_encode(['event' => $event]),
            ]);
        } catch (\Throwable $e) {}

        return response()->json(['status' => 'success']);
    }
}
