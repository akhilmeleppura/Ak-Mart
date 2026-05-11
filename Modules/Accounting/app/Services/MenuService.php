<?php

namespace Modules\Accounting\Services;

class MenuService
{
    public static function getMenu($moduleName, $currentRoute)
{
    $menuPath = module_path($moduleName, 'Resources/json/submenu.json');

    if (!file_exists($menuPath)) {
        return [];
    }

    $menuJson = file_get_contents($menuPath);
    $menuData = json_decode($menuJson, true);

    return collect($menuData['menu'])->map(function ($item) use ($currentRoute) {
        $item['active'] = request()->is(trim($item['url'], '/'));
        if (!isset($item['identifier'])) {
            $item['identifier'] = strtolower(str_replace(' ', '_', $item['label']));
        }
        return $item;
    })->toArray();
}

}
