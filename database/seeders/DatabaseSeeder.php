<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
            SubscriptionPlanSeeder::class,
            PaymentOptionSeeder::class,
            SuperAdminSeeder::class,
            EcommerceSeeder::class,
            BranchSeeder::class,
            OrderSeeder::class,
            DemoSeeder::class,
        ]);
    }
}
