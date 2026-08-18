<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\File;

class RolesPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $json = File::get(database_path('data/roles_permissions.json'));
        $data = json_decode($json, true);

        // 1. Create Base Permissions from database data
        foreach ($data['permissions'] as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        // 2. Create Module Permissions if permission.json exists
        $modulePath = base_path('Modules/Permission/permission.json');
        if (File::exists($modulePath)) {
            $moduleData = json_decode(File::get($modulePath), true);
            foreach ($moduleData as $module) {
                foreach ($module['permissions'] as $permission) {
                    Permission::findOrCreate($permission['name'], 'web');
                }
            }
        }

        // 3. Create Additional App-level Permissions
        $allSystemPerms = [
            'view_dashboard',
            'manage_products',
            'manage_orders',
            'view_reports',
            'manage_branches',
            'manage_users',
            'manage_roles',
            'manage_permissions',
            'manage_saas',
            'manage_settings',
            'manage_pos',
            'manage_coupons',
            'manage_logistics',
            'manage_inventory',
            'manage_suppliers',
            'manage_purchases',
            'access_ai_assistant',
            'use_ai_chat',
            'samplemodule.view',
            'samplemodule.demo.view',
            'samplemodule.settings.view',
            'samplemodule.settings.general.view',
        ];

        foreach ($allSystemPerms as $perm) {
            Permission::findOrCreate($perm, 'web');
        }

        // 4. Create Roles and Assign Permissions
        foreach ($data['roles'] as $roleData) {
            $role = Role::findOrCreate($roleData['name'], 'web');
            
            if (in_array('all', $roleData['permissions']) || $roleData['name'] === 'Super Admin') {
                $role->syncPermissions(Permission::all());
            } else {
                $role->syncPermissions($roleData['permissions']);
            }
        }

        // Always ensure Super Admin has all permissions
        $superAdminRole = Role::findOrCreate('Super Admin', 'web');
        $superAdminRole->syncPermissions(Permission::all());
    }
}
