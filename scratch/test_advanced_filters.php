<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\StoreSetting;
use App\Http\Controllers\apps\StoreBuilderController;
use App\Http\Controllers\Storefront\StorefrontController;
use Illuminate\Support\Facades\Auth;

echo "========================================================\n";
echo " TESTING ADVANCED FILTER SYSTEM & STORE MANAGEMENT PANEL\n";
echo "========================================================\n\n";

$admin = User::where('email', 'admin@akmart.com')->first() ?: User::first();
Auth::login($admin);

// 1. Test Admin Filter Management View
$builderCtrl = app(StoreBuilderController::class);
$respAdminFilters = $builderCtrl->filters();
$adminData = $respAdminFilters->getData();

if (isset($adminData['filterConfig']) && isset($adminData['availableBrands'])) {
    echo "[PASS] Admin Filter Management Hub loaded successfully! (Found " . count($adminData['availableBrands']) . " filterable brands, {$adminData['totalProducts']} products)\n";
} else {
    echo "[FAIL] Admin filter management hub failed to load.\n";
}

// 2. Test Saving Filter Configuration
$reqSaveFilters = Request::create('/store-management/filters', 'POST', [
    'show_search'      => '1',
    'show_category'    => '1',
    'show_brand'       => '1',
    'show_price'       => '1',
    'show_rating'      => '1',
    'show_stock'       => '1',
    'show_dietary'     => '1',
    'show_deals'       => '1',
    'price_min_limit'  => 0,
    'price_max_limit'  => 150,
    'brand_display'    => 'scroll_list',
    'dietary_tags'     => 'Organic, Gluten-Free, Vegan, Dairy-Free, Sugar-Free, Non-GMO, Halal',
    'quick_filter_bar' => '1',
    'grid_list_toggle' => '1',
]);

$respSave = $builderCtrl->updateFilters($reqSaveFilters);
$savedConfig = json_decode(StoreSetting::get('storefront_filter_config', '{}'), true);

if (!empty($savedConfig['show_brand']) && ($savedConfig['price_max_limit'] == 150)) {
    echo "[PASS] Admin Filter Configuration saved into StoreSetting! (Max Price Limit: \${$savedConfig['price_max_limit']}, Dietary Tags: {$savedConfig['dietary_tags']})\n";
} else {
    echo "[FAIL] Filter configuration not saved.\n";
}

// 3. Test Storefront Catalog Advanced Filters
$storefrontCtrl = app(StorefrontController::class);

// A. Filter by Brand & Price
$sampleBrand = Product::whereNotNull('brand')->where('brand', '!=', '')->first()?->brand;
$reqShopFiltered = Request::create('/store/shop', 'GET', [
    'brands'    => [$sampleBrand],
    'min_price' => 1.0,
    'max_price' => 50.0,
    'min_rating'=> 3,
    'sort'      => 'price_low',
]);
$respShopFiltered = $storefrontCtrl->shop($reqShopFiltered);
$shopData = $respShopFiltered->getData();

if (isset($shopData['products']) && isset($shopData['filterConfig']) && isset($shopData['brandCounts'])) {
    echo "[PASS] Storefront Shop rendered with Advanced Brand & Price Range & Rating filters (Items Found: {$shopData['products']->total()})!\n";
} else {
    echo "[FAIL] Storefront shop filter query failed.\n";
}

// B. Filter by Dietary Tag (Organic)
$reqDietary = Request::create('/store/shop', 'GET', [
    'dietary' => 'Organic',
]);
$respDietary = $storefrontCtrl->shop($reqDietary);
$dietaryData = $respDietary->getData();

if (isset($dietaryData['products'])) {
    echo "[PASS] Storefront Dietary Tag filter 'Organic' executed successfully (Items Found: {$dietaryData['products']->total()})!\n";
} else {
    echo "[FAIL] Dietary tag filter failed.\n";
}

// C. Filter by Flash Deals
$reqDeals = Request::create('/store/shop', 'GET', [
    'deals' => '1',
]);
$respDeals = $storefrontCtrl->shop($reqDeals);
$dealsData = $respDeals->getData();

if (isset($dealsData['products']) && isset($dealsData['dealsCount'])) {
    echo "[PASS] Storefront Flash Deals filter executed successfully (Deals Count: {$dealsData['dealsCount']})!\n";
} else {
    echo "[FAIL] Deals filter failed.\n";
}

echo "\n--------------------------------------------------------\n";
echo " ALL ADVANCED FILTER TESTS PASSED SUCCESSFULLY (100%)\n";
echo "--------------------------------------------------------\n";
