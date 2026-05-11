<?php

namespace App\Helpers\HS;

use Illuminate\Support\Str;

class ModuleHelper
{
    /**
     * Gets all enabled settings modules.
     * The category is now determined by the module's MenuController, not here.
     *
     * @return array
     */
    public static function getSettingsModules()
    {
        $modules = [];
        $modulesPath = base_path('Modules');

        foreach (scandir($modulesPath) as $moduleFolder) {
            if ($moduleFolder === '.' || $moduleFolder === '..') continue;

            $jsonPath = $modulesPath . '/' . $moduleFolder . '/module.json';
            if (!file_exists($jsonPath)) continue;

            $json = json_decode(file_get_contents($jsonPath));
            if (!isset($json->enabled) || !$json->enabled) continue;

            $modules[] = (object)[
                'name' => $json->name ?? $moduleFolder,
                'url'  => url($json->slug ?? Str::slug($moduleFolder)),
                'slug' => $moduleFolder,
            ];
        }

        return $modules;
    }
}