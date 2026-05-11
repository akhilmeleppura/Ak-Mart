<?php

namespace Modules\General\App\Http\Controllers;

use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    /**
     * Defines the menu structure and placement for the General module.
     */
    public function getMenu()
    {
        return [
            // Master switch to enable/disable the entire module's menu
            'is_active' => true,

            // Define where the menu appears and what it contains
            'placement' => [
                // Configuration for the "General Settings" menu
                'general' => [
                    'is_active' => true, // Show in General Settings?
                    'menu' => [
                        [
                            'name' => 'General',
                            'slug' => 'general.configuration.index',
                            'url'  => 'general/configuration',
                            'icon' => 'bx bx-file', // Icon changed to better represent templates
                            'permissions' => 'general.configuration.view',
                            'submenu' => [
                                [
                                    'name' => 'Document Templates',
                                    'slug' => 'general.templates.index',
                                    'url'  => 'general/document/templates',
                                    'permissions' => 'general.templates.view'
                                ]
                            ]
                        ]
                    ]
                ],

                // Configuration for the "Settings" menu
                'settings' => [
                    'is_active' => true, // Show in Settings?
                    'menu' => [
                        [
                            'name' => 'General',
                            'slug' => 'general.system.index',
                            'url'  => 'general/system',
                            'icon' => 'bx bx-desktop',
                            'permissions' => 'general.system.view',
                            'submenu' => [
                                [
                                    'name' => 'System Preferences',
                                    'slug' => 'general.system.preferences.index',
                                    'url'  => 'samplemodule/sample-page-1',
                                    'permissions' => 'general.system.preferences.view'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}