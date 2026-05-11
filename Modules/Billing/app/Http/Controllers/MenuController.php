<?php

namespace Modules\Billing\App\Http\Controllers;

use App\Http\Controllers\Controller;

class MenuController extends Controller
{
    public function getMenu()
    {
        return [
            // Master switch to enable/disable the entire module's menu
            'is_active' => true,

            // Define where the menu appears and what it contains
            'placement' => [
                // Configuration for the "General Settings" menu
                'general' => [
                    'is_active' => true, // Show in General Settings
                    'menu' => [
                        [
                            'name' => 'Billing',
                            'slug' => 'billing.settings.general',
                            'url'  => 'billing/settings/general',
                            'icon' => 'bx bx-cog',
                            'permissions' => 'billing.settings.view',
                            'submenu' => [
                                [
                                    'name' => 'Payment Options',
                                    'slug' => 'billing.payment-options.index',
                                    'url'  => '/payment-options',
                                    'permissions' => 'billing.settings.invoice-numbering.view'
                                ]
                            ]
                        ]
                    ]
                ],

                // Configuration for the "Settings" menu
                'settings' => [
                    'is_active' => true, // Show in Settings
                    'menu' => [
                        [
                            'name' => 'Billings',
                            'slug' => 'billing.index',
                            'url'  => 'billing',
                            'icon' => 'bx bx-calculator',
                            'permissions' => 'billing.view',
                            'submenu' => [
                                [
                                    'name' => 'Invoices',
                                    'slug' => 'accounting.billings.index',
                                    'url'  => 'accounting/billings',
                                    'permissions' => 'user.view'
                                ],
                                [
                                    'name' => 'Debit Notes',
                                    'slug' => 'accounting.debit-notes.index',
                                    'url'  => 'accounting/billings/debit-notes',
                                    'permissions' => 'user.view'
                                ],
                                [
                                    'name' => 'Credit Notes',
                                    'slug' => 'accounting.credit-notes.index',
                                    'url'  => 'accounting/billings/credit-notes',
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
