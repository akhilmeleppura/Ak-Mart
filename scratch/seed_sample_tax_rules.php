<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\TaxRule;
use App\Models\StoreSetting;

echo "Seeding default tax rules and VAT settings...\n";

StoreSetting::set('tax_enabled', '1');
StoreSetting::set('vat_default_rate', '5.0');
StoreSetting::set('tax_calculation_mode', 'exclusive');
StoreSetting::set('tax_calculation_basis', 'shipping_address');
StoreSetting::set('b2b_vat_exemption', '1');
StoreSetting::set('tax_number', 'VAT-AKM-987654321');
StoreSetting::set('tax_display_on_invoice', '1');

$rules = [
    [
        'name'                => 'UAE / Gulf Standard VAT',
        'tax_class'           => 'standard',
        'tax_type'            => 'percentage',
        'rate'                => 5.00,
        'country_code'        => 'AE',
        'state_name'          => '*',
        'postal_code_pattern' => '*',
        'is_compound'         => false,
        'priority'            => 1,
        'is_active'           => true,
        'calculation_mode'    => 'exclusive',
    ],
    [
        'name'                => 'California Area Sales Tax',
        'tax_class'           => 'standard',
        'tax_type'            => 'percentage',
        'rate'                => 8.25,
        'country_code'        => 'US',
        'state_name'          => 'CA',
        'postal_code_pattern' => '*',
        'is_compound'         => false,
        'priority'            => 1,
        'is_active'           => true,
        'calculation_mode'    => 'exclusive',
    ],
    [
        'name'                => 'New York Metro Area Tax',
        'tax_class'           => 'standard',
        'tax_type'            => 'percentage',
        'rate'                => 8.875,
        'country_code'        => 'US',
        'state_name'          => 'NY',
        'postal_code_pattern' => '*',
        'is_compound'         => false,
        'priority'            => 1,
        'is_active'           => true,
        'calculation_mode'    => 'exclusive',
    ],
    [
        'name'                => 'Essential Fresh Food Zero Rate',
        'tax_class'           => 'zero_rate',
        'tax_type'            => 'percentage',
        'rate'                => 0.00,
        'country_code'        => '*',
        'state_name'          => '*',
        'postal_code_pattern' => '*',
        'is_compound'         => false,
        'priority'            => 1,
        'is_active'           => true,
        'calculation_mode'    => 'exclusive',
    ],
    [
        'name'                => 'Basic Groceries Reduced VAT',
        'tax_class'           => 'reduced',
        'tax_type'            => 'percentage',
        'rate'                => 2.50,
        'country_code'        => '*',
        'state_name'          => '*',
        'postal_code_pattern' => '*',
        'is_compound'         => false,
        'priority'            => 2,
        'is_active'           => true,
        'calculation_mode'    => 'exclusive',
    ],
];

foreach ($rules as $r) {
    TaxRule::updateOrCreate(['name' => $r['name']], $r);
}

echo "Default tax rules seeded successfully! Total rules: " . TaxRule::count() . "\n";
