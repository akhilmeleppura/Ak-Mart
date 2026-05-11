<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run()
    {
        // Create the ultimate Super Admin
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'branch_id' => null, // Super Admin has access to all branches
            ]
        );

        // Create a Branch Manager for testing branch lock
        User::updateOrCreate(
            ['email' => 'manager@branch.com'],
            [
                'name' => 'Branch Manager',
                'password' => Hash::make('manager123'),
                'branch_id' => 1, // Restricted to first branch
            ]
        );
    }
}
