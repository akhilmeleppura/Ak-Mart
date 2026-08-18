<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        // 1. Primary AK-Mart Admin (Supreme Admin)
        $admin = User::updateOrCreate(
            ['email' => 'admin@ak-mart.com'],
            [
                'name' => 'AK-Mart Admin',
                'password' => Hash::make('password'),
                'user_type' => 'super_admin',
                'is_supreme_admin' => 1,
                'is_super_admin' => 1,
                'branch_id' => 1,
            ]
        );
        try { $admin->assignRole('Super Admin'); } catch (\Throwable $e) {}

        // 2. Legacy / Dedicated Super Admin (Supreme Admin)
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'user_type' => 'super_admin',
                'is_supreme_admin' => 1,
                'is_super_admin' => 1,
                'branch_id' => null,
            ]
        );
        try { $superAdmin->assignRole('Super Admin'); } catch (\Throwable $e) {}

        // 3. Dedicated Supreme Admin Account
        $supremeAdmin = User::updateOrCreate(
            ['email' => 'supreme@ak-mart.com'],
            [
                'name' => 'Supreme Admin',
                'password' => Hash::make('supreme123'),
                'user_type' => 'super_admin',
                'is_supreme_admin' => 1,
                'is_super_admin' => 1,
                'branch_id' => null,
            ]
        );
        try { $supremeAdmin->assignRole('Super Admin'); } catch (\Throwable $e) {}

        // 4. Demo Manager
        $manager = User::updateOrCreate(
            ['email' => 'manager@ak-mart.com'],
            [
                'name' => 'Store Manager',
                'password' => Hash::make('password'),
                'user_type' => 'customer',
                'is_supreme_admin' => 0,
                'is_super_admin' => 0,
                'branch_id' => 1,
            ]
        );
        try { $manager->assignRole('Branch Manager'); } catch (\Throwable $e) {}

        // 5. Demo Cashier
        $cashier = User::updateOrCreate(
            ['email' => 'cashier@ak-mart.com'],
            [
                'name' => 'Store Cashier',
                'password' => Hash::make('password'),
                'user_type' => 'customer',
                'is_supreme_admin' => 0,
                'is_super_admin' => 0,
                'branch_id' => 1,
            ]
        );
        try { $cashier->assignRole('User'); } catch (\Throwable $e) {}
    }
}
