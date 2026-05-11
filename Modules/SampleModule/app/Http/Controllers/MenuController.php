<?php

namespace Modules\SampleModule\App\Http\Controllers;

use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    /**
     * Defines the menu structure and placement for the SampleModule.
     */
    public function getMenu()
    {
        return [
            'is_active' => true,

            'placement' => [
                // Configuration for the "General Settings" menu
                'general' => [
                    'is_active' => true, // Show in General Settings?
                    'menu' => [
                        [
                            'name' => 'Sample Module Settings',
                            'slug' => 'samplemodule.settings.index',
                            'url'  => 'samplemodule/settings',
                            'icon' => 'bx bx-cog',
                            'permissions' => 'samplemodule.settings.view',
                            'submenu' => [
                                [
                                    'name' => 'General Configuration',
                                    'slug' => 'samplemodule.settings.general.index',
                                    'url'  => 'samplemodule/sample-page-1',
                                    'permissions' => 'samplemodule.settings.general.view'
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
                            'name' => 'Sample Module',
                            'slug' => 'samplemodule.index',
                            'url'  => 'samplemodule',
                            'icon' => 'bx bx-book',
                            'permissions' => 'samplemodule.view',
                            'submenu' => [
                                [
                                    'name' => 'Demo Submenu',
                                    'slug' => 'samplemodule.demo.index',
                                    'url'  => 'samplemodule/sample-page-1',
                                    'permissions' => 'samplemodule.demo.view'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
