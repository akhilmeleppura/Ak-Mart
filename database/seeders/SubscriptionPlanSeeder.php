<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\SubscriptionPlan::updateOrCreate(
            ['slug' => 'starter'],
            [
                'name' => 'Starter Plan',
                'description' => 'Perfect for new stores.',
                'price' => 29.00,
                'currency' => 'USD',
                'billing_cycle_days' => 30,
                'trial_days' => 14,
                'features' => ['products_limit' => 100, 'staff_accounts' => 2, 'custom_domain' => false]
            ]
        );

        \App\Models\SubscriptionPlan::updateOrCreate(
            ['slug' => 'pro'],
            [
                'name' => 'Pro Plan',
                'description' => 'For growing businesses.',
                'price' => 79.00,
                'currency' => 'USD',
                'billing_cycle_days' => 30,
                'trial_days' => 14,
                'features' => ['products_limit' => 1000, 'staff_accounts' => 10, 'custom_domain' => true]
            ]
        );

        \App\Models\SubscriptionPlan::updateOrCreate(
            ['slug' => 'enterprise'],
            [
                'name' => 'Enterprise Plan',
                'description' => 'Unlimited scaling for high-volume merchants.',
                'price' => 299.00,
                'currency' => 'USD',
                'billing_cycle_days' => 30,
                'trial_days' => 0,
                'features' => ['products_limit' => -1, 'staff_accounts' => -1, 'custom_domain' => true]
            ]
        );
    }
}
