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

        // 1. Create Permissions
        foreach ($data['permissions'] as $permissionName) {
            Permission::findOrCreate($permissionName);
        }

        // 2. Create Roles and Assign Permissions
        foreach ($data['roles'] as $roleData) {
            $role = Role::findOrCreate($roleData['name']);
            
            if (in_array('all', $roleData['permissions'])) {
                $role->syncPermissions(Permission::all());
            } else {
                $role->syncPermissions($roleData['permissions']);
            }
        }
    }
}
