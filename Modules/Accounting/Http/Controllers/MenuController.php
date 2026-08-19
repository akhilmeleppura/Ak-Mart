<?php

namespace Modules\Accounting\App\Http\Controllers;

use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    /**
     * Defines the menu structure and placement for the Accounting module.
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
                            'name' => 'Accounting',
                            'slug' => 'accounting.general.index',
                            'url'  => 'accounting/chart-of-accounts',
                            'icon' => 'bx bx-book-alt',
                            'permissions' => 'accounting.view',
                            'submenu' => [
                                [
                                    'name' => 'Accounting',
                                    'slug' => 'accounting.index',
                                    'url'  => '/accounting/chart-of-accounts',
                                    'permissions' => 'user.view'
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
                            'name' => 'Accounting',
                            'slug' => 'accounting.settings.index',
                            'url'  => 'accounting/settings',
                            'icon' => 'bx bx-cog',
                            'permissions' => 'accounting.settings.view',
                            'submenu' => [
                                [
                                    'name' => 'Journal',
                                    'slug' => 'accounting.Journal.index',
                                    'url'  => 'accounting/journal',
                                    'permissions' => 'user.view'
                                ],
                                [
                                    'name' => 'Ledger',
                                    'slug' => 'accounting.ledger.index',
                                    'url'  => 'accounting/ledger',
                                    'permissions' => 'user.view'
                                ],
                                [
                                    'name' => 'Customer Ledger',
                                    'slug' => 'accounting.customer-ledger.index',
                                    'url'  => 'accounting/customer-ledger',
                                    'permissions' => 'user.view'
                                ],
                                [
                                    'name' => 'Trial Balance',
                                    'slug' => 'accounting.trial-balance.index',
                                    'url'  => 'accounting/trial-balance',
                                    'permissions' => 'user.view'
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }
}
