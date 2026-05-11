<?php

namespace App\Http\Controllers\module_menu;

use App\Http\Controllers\Controller;
use App\Helpers\HS\ModuleHelper;
use Illuminate\Support\Facades\Auth;

class ModuleMenuController extends Controller
{
    /**
     * Gets all module menus, categorized based on the 'placement' key in each module's MenuController.
     *
     * @return array
     */
    public function getAllCategorizedMenus()
    {
        $allModules = ModuleHelper::getSettingsModules();
        $categorizedMenus = [
            'general' => [],
            'settings' => []
        ];

        foreach ($allModules as $module) {
            $controllerClass = "\\Modules\\" . $module->slug . "\\App\\Http\\Controllers\\MenuController";
            if (!class_exists($controllerClass) || !method_exists($controllerClass, 'getMenu')) {
                continue;
            }

            $menuData = (new $controllerClass)->getMenu();

            // 1. Check the master 'is_active' flag for the entire module
            if (!isset($menuData['is_active']) || !$menuData['is_active']) {
                continue;
            }

            // 2. Check for the 'placement' configuration
            if (!isset($menuData['placement']) || !is_array($menuData['placement'])) {
                continue;
            }

            // 3. Loop through each defined placement (e.g., 'general', 'settings')
            foreach ($menuData['placement'] as $category => $placementData) {
                // 4. Check if this category is valid and if it's active
                if (!array_key_exists($category, $categorizedMenus) || 
                    !isset($placementData['is_active']) || 
                    !$placementData['is_active']) {
                    continue;
                }

                // 5. Filter the menu for this specific placement by permissions
                $filteredMenu = $this->filterMenuByPermissions($placementData['menu'] ?? []);

                // 6. If items remain, add them to the correct category
                if (!empty($filteredMenu)) {
                    $categorizedMenus[$category][] = [
                        'module' => $module,
                        'menu' => $filteredMenu
                    ];
                }
            }
        }

        return $categorizedMenus;
    }

    /**
     * Filters menu items based on user permissions.
     */
    private function filterMenuByPermissions(array $menus)
    {
        $user = Auth::user();
        if ($user->is_supreme_admin == 1) {
            return $menus;
        }

        $filteredMenus = [];
        foreach ($menus as $menu) {
            $hasPermission = true;
            if (isset($menu['permissions'])) {
                $hasPermission = $user->can($menu['permissions']);
            }

            if (!empty($menu['submenu']) && is_array($menu['submenu'])) {
                $menu['submenu'] = array_filter($menu['submenu'], function ($submenu) use ($user) {
                    return !isset($submenu['permissions']) || $user->can($submenu['permissions']);
                });
            }

            if ($hasPermission || !empty($menu['submenu'])) {
                $filteredMenus[] = $menu;
            }
        }

        return $filteredMenus;
    }
}