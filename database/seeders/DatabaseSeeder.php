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
            WarehouseSeeder::class,
            SuperAdminSeeder::class,
            CustomerSeeder::class,
            SupplierAndPurchaseSeeder::class,
            EcommerceSeeder::class,
            SubscriptionPlanSeeder::class,
            SettingsSeeder::class,
            PaymentOptionSeeder::class,
            NotificationSeeder::class,
            AdvancedCommerceSeeder::class,
            NextGenCommerceSeeder::class,
        ]);
    }
}
