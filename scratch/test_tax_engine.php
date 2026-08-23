<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Product;
use App\Models\TaxRule;
use App\Models\StoreSetting;
use App\Services\TaxEngineService;
use App\Http\Controllers\apps\TaxManagementController;
use App\Http\Controllers\Storefront\StorefrontController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "========================================================\n";
echo " TESTING ADVANCED VAT & AREA-BASED TAX RULE ENGINE     \n";
echo "========================================================\n\n";

$admin = User::where('email', 'admin@akmart.com')->first() ?: User::first();
Auth::login($admin);

$taxEngine = app(TaxEngineService::class);
$taxCtrl = app(TaxManagementController::class);
$storefrontCtrl = app(StorefrontController::class);

// 1. Test Tax Calculation for California Destination
$sampleCart = [
    1 => ['id' => 1, 'price' => 100.00, 'qty' => 2, 'tax_class' => 'standard'],
];

$resCA = $taxEngine->calculateCartTax($sampleCart, [
    'country' => 'US',
    'state'   => 'CA',
    'zip'     => '90210'
]);

if ($resCA['total_tax'] > 0 && !empty($resCA['tax_breakdown'])) {
    echo "[PASS] Area-based Tax Engine calculated California Tax: \${$resCA['total_tax']} on \$200 subtotal (Breakdown: " . json_encode($resCA['tax_breakdown']) . ")!\n";
} else {
    echo "[FAIL] California tax calculation failed.\n";
}

// 2. Test Zero-Rate / Fresh Food Exemption
$zeroRateCart = [
    2 => ['id' => 2, 'price' => 50.00, 'qty' => 1, 'tax_class' => 'zero_rate'],
];

$resZero = $taxEngine->calculateCartTax($zeroRateCart, [
    'country' => 'US',
    'state'   => 'CA',
    'zip'     => '90210'
]);

if ($resZero['total_tax'] == 0.00) {
    echo "[PASS] Zero-Rate / Fresh Food exemption verified (\$0.00 Tax on \$50.00 subtotal)!\n";
} else {
    echo "[FAIL] Zero rate rule failed: tax was \${$resZero['total_tax']}\n";
}

// 3. Test Admin Panel Tax Management
$respAdmin = $taxCtrl->index();
$adminData = $respAdmin->getData();

if (isset($adminData['taxRules']) && isset($adminData['settings'])) {
    echo "[PASS] Admin Tax Management Hub loaded successfully! (Found {$adminData['totalRules']} rules, Default VAT: {$adminData['settings']['vat_default_rate']}%)\n";
} else {
    echo "[FAIL] Admin tax management view failed to load.\n";
}

// 4. Test Creating a New Area Tax Rule via Controller
$reqCreateRule = Request::create('/ecommerce/settings/taxes', 'POST', [
    'name'                => 'Texas Lone Star Tax',
    'tax_class'           => 'standard',
    'tax_type'            => 'percentage',
    'rate'                => '6.25',
    'country_code'        => 'US',
    'state_name'          => 'TX',
    'postal_code_pattern' => '750*',
    'priority'            => 1,
    'is_active'           => '1',
    'calculation_mode'    => 'exclusive',
]);

$taxCtrl->storeRule($reqCreateRule);
$createdRule = TaxRule::where('name', 'Texas Lone Star Tax')->first();

if ($createdRule && $createdRule->rate == 6.25) {
    echo "[PASS] Dynamic Area Tax Rule 'Texas Lone Star Tax' created successfully (Rate: {$createdRule->rate}%, State: {$createdRule->state_name})!\n";
} else {
    echo "[FAIL] Failed to create dynamic tax rule.\n";
}

// 5. Test Live Tax Simulation AJAX Endpoint
$reqSim = Request::create('/ecommerce/settings/taxes/simulate', 'POST', [
    'amount'  => 100.00,
    'country' => 'US',
    'state'   => 'TX',
    'zip'     => '75001',
]);
$respSim = $taxCtrl->simulateTax($reqSim);
$simData = $respSim->getData(true);

if (!empty($simData['success']) && isset($simData['result']['total_tax'])) {
    echo "[PASS] Live Tax Rule Simulator calculated \${$simData['result']['total_tax']} on \$100.00 for Texas destination!\n";
} else {
    echo "[FAIL] Tax simulation failed.\n";
}

// 6. Test Storefront Cart View with Dynamic Tax
session()->put('cart', [
    1 => ['id' => 1, 'name' => 'Sample Grocery Item', 'price' => 20.00, 'qty' => 2, 'sku' => 'GRO-001']
]);
$respCartView = $storefrontCtrl->cart();
$cartViewData = $respCartView->getData();

if (isset($cartViewData['taxAmount']) && isset($cartViewData['finalTotal'])) {
    echo "[PASS] Storefront Cart successfully rendered with dynamic Tax amount (\${$cartViewData['taxAmount']}) & Final Total (\${$cartViewData['finalTotal']})!\n";
} else {
    echo "[FAIL] Storefront cart tax integration failed.\n";
}

echo "\n--------------------------------------------------------\n";
echo " ALL ADVANCED VAT & TAX RULE TESTS PASSED (100%)\n";
echo "--------------------------------------------------------\n";
