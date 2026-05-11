<?php

namespace Modules\Permission\Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\File;


class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $path = base_path('Modules/Permission/permission.json');

        if (!File::exists($path)) {
            $this->command->error("Permission ACL file not found!");
            return;
        }

        $data = json_decode(File::get($path), true);

        foreach ($data as $module) {
            foreach ($module['permissions'] as $permission) {
                Permission::updateOrCreate(
                    ['name' => $permission['name']],
                    [
                        'guard_name' => 'web',
                        'module' => $module['module'],
                        'label' => $permission['label'] ?? ucfirst(explode('.', $permission['name'])[1] ?? $permission['name'])
                    ]
                );
            }
        }

        $this->command->info("Permissions seeded with labels.");
    }
}

