<?php

namespace Database\Seeders;

use App\Models\AuditLog;
use App\Models\Branch\Branch;
use App\Models\SubscriptionInvoice;
use App\Models\SubscriptionPlan;
use App\Models\TenantSubscription;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $starter = SubscriptionPlan::updateOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter Plan',
                'description' => 'Perfect for new stores.',
                'price' => 29.00,
                'currency' => 'USD',
                'billing_cycle_days' => 30,
                'trial_days' => 14,
                'is_active' => true,
                'features' => ['products_limit' => 100, 'staff_accounts' => 2, 'custom_domain' => false]
            ]
        );

        $pro = SubscriptionPlan::updateOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro Plan',
                'description' => 'For growing businesses.',
                'price' => 79.00,
                'currency' => 'USD',
                'billing_cycle_days' => 30,
                'trial_days' => 14,
                'is_active' => true,
                'features' => ['products_limit' => 1000, 'staff_accounts' => 10, 'custom_domain' => true]
            ]
        );

        $enterprise = SubscriptionPlan::updateOrCreate(
            ['slug' => 'enterprise'],
            [
                'name' => 'Enterprise Plan',
                'description' => 'Unlimited scaling for high-volume merchants.',
                'price' => 299.00,
                'currency' => 'USD',
                'billing_cycle_days' => 30,
                'trial_days' => 0,
                'is_active' => true,
                'features' => ['products_limit' => -1, 'staff_accounts' => -1, 'custom_domain' => true]
            ]
        );

        // Seed Tenant Subscription for Branch 1 (Main Flagship)
        $sub1 = TenantSubscription::updateOrCreate(
            ['branch_id' => 1],
            [
                'subscription_plan_id' => $pro->id,
                'status' => 'active',
                'current_period_start' => now()->subDays(10),
                'current_period_end' => now()->addDays(20),
                'stripe_subscription_id' => 'sub_live_' . Str::random(12),
            ]
        );

        // Seed Subscription Invoices for Branch 1
        SubscriptionInvoice::updateOrCreate(
            ['invoice_number' => 'INV-2026-0801'],
            [
                'tenant_subscription_id' => $sub1->id,
                'branch_id' => 1,
                'amount' => 79.00,
                'currency' => 'USD',
                'status' => 'paid',
                'payment_method' => 'Credit Card (Stripe)',
                'plan_name' => 'Pro Plan',
                'billing_period_start' => now()->subDays(10),
                'billing_period_end' => now()->addDays(20),
                'paid_at' => now()->subDays(10),
            ]
        );

        SubscriptionInvoice::updateOrCreate(
            ['invoice_number' => 'INV-2026-0701'],
            [
                'tenant_subscription_id' => $sub1->id,
                'branch_id' => 1,
                'amount' => 79.00,
                'currency' => 'USD',
                'status' => 'paid',
                'payment_method' => 'Credit Card (Stripe)',
                'plan_name' => 'Pro Plan',
                'billing_period_start' => now()->subDays(40),
                'billing_period_end' => now()->subDays(10),
                'paid_at' => now()->subDays(40),
            ]
        );

        // Seed sample audit logs for demo user
        $admin = User::where('email', 'admin@ak-mart.com')->first();
        if ($admin) {
            AuditLog::firstOrCreate(
                ['user_id' => $admin->id, 'event' => 'profile_updated'],
                [
                    'auditable_type' => User::class,
                    'auditable_id' => $admin->id,
                    'new_values' => json_encode(['status' => 'active', 'locale' => 'en']),
                    'url' => '/account/settings',
                    'ip_address' => '127.0.0.1',
                    'created_at' => now()->subHours(2),
                ]
            );

            AuditLog::firstOrCreate(
                ['user_id' => $admin->id, 'event' => 'plan_subscribed'],
                [
                    'auditable_type' => TenantSubscription::class,
                    'auditable_id' => $sub1->id,
                    'new_values' => json_encode(['plan' => 'Pro Plan', 'price' => 79.00]),
                    'url' => '/saas/billing',
                    'ip_address' => '127.0.0.1',
                    'created_at' => now()->subDays(10),
                ]
            );
        }
    }
}
