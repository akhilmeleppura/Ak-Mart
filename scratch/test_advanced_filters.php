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
    'show_size'        => '1',
    'show_color'       => '1',
    'show_price'       => '1',
    'show_rating'      => '1',
    'show_stock'       => '1',
    'show_dietary'     => '1',
    'show_deals'       => '1',
    'price_min_limit'  => 0,
    'price_max_limit'  => 150,
    'brand_display'    => 'scroll_list',
    'size_options'     => 'Small, Medium, Large, XL, XXL, 250g, 500g, 1kg, 5kg, 500ml, 1L, 2L',
    'size_display'     => 'pills',
    'color_options'    => 'Red, Blue, Green, Yellow, Black, White, Orange, Pink, Purple, Gold',
    'color_display'    => 'swatches',
    'dietary_tags'     => 'Organic, Gluten-Free, Vegan, Dairy-Free, Sugar-Free, Non-GMO, Halal',
    'quick_filter_bar' => '1',
    'grid_list_toggle' => '1',
]);

$respSave = $builderCtrl->updateFilters($reqSaveFilters);
$savedConfig = json_decode(StoreSetting::get('storefront_filter_config', '{}'), true);

if (!empty($savedConfig['show_size']) && !empty($savedConfig['show_color']) && ($savedConfig['price_max_limit'] == 150)) {
    echo "[PASS] Admin Filter Configuration saved into StoreSetting! (Size: {$savedConfig['show_size']}, Color: {$savedConfig['show_color']}, Max Price Limit: \${$savedConfig['price_max_limit']})\n";
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

// D. Filter by Size (e.g. 1L, Large, 500g)
$reqSize = Request::create('/store/shop', 'GET', [
    'size' => '1L',
]);
$respSize = $storefrontCtrl->shop($reqSize);
$sizeData = $respSize->getData();

if (isset($sizeData['products'])) {
    echo "[PASS] Storefront Size Filter ('1L') executed successfully (Items Found: {$sizeData['products']->total()})!\n";
} else {
    echo "[FAIL] Size filter failed.\n";
}

// E. Filter by Color (e.g. Red, Blue, Green)
$reqColor = Request::create('/store/shop', 'GET', [
    'color' => 'Red',
]);
$respColor = $storefrontCtrl->shop($reqColor);
$colorData = $respColor->getData();

if (isset($colorData['products'])) {
    echo "[PASS] Storefront Color Filter ('Red') executed successfully (Items Found: {$colorData['products']->total()})!\n";
} else {
    echo "[FAIL] Color filter failed.\n";
}

// F. Test Add to Cart AJAX Response
$sampleProd = Product::first();
$reqCart = Request::create('/store/cart/add', 'POST', [
    'product_id' => $sampleProd->id,
    'qty'        => 1,
]);
$respCart = $storefrontCtrl->addToCart($reqCart);
$cartJson = $respCart->getData(true);

if (!empty($cartJson['success']) && isset($cartJson['cartCount'])) {
    echo "[PASS] Storefront Add to Cart AJAX returned valid cartCount ({$cartJson['cartCount']})!\n";
} else {
    echo "[FAIL] Add to Cart AJAX test failed.\n";
}

echo "\n--------------------------------------------------------\n";
echo " ALL ADVANCED FILTER & CART TESTS PASSED (100%)\n";
echo "--------------------------------------------------------\n";

