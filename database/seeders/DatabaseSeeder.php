<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesPermissionsSeeder::class,
            BranchSeeder::class,
            SuperAdminSeeder::class,
            EcommerceSeeder::class,
            SupplierAndPurchaseSeeder::class,
            OrderSeeder::class,
            SubscriptionPlanSeeder::class,
            PaymentOptionSeeder::class,
            DemoSeeder::class,
            AdvancedCommerceSeeder::class,
            NextGenCommerceSeeder::class,
        ]);
    }
}
