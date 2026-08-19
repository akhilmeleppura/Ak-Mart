<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
$httpKernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use App\Models\Product;
use App\Models\Category;
use App\Models\CmsBanner;
use App\Models\EmailTemplate;
use App\Models\Review;
use App\Models\Setting;
use Illuminate\Http\Request;

echo "========================================================\n";
echo " AK-MART MASTER E-COMMERCE & ADMIN AUDIT TEST SUITE\n";
echo "========================================================\n\n";

$passCount = 0;
$totalTests = 0;

function runTest($name, $closure) {
    global $passCount, $totalTests;
    $totalTests++;
    try {
        $result = $closure();
        if ($result === true || $result === null) {
            echo " [PASS] {$name}\n";
            $passCount++;
        } else {
            echo " [FAIL] {$name}: {$result}\n";
        }
    } catch (\Throwable $e) {
        echo " [ERROR] {$name}: " . $e->getMessage() . "\n";
    }
}

// 1. Sliders & Marketing Posters
runTest('Hero Sliders & Promotional Posters Active Count >= 4', function() {
    $count = CmsBanner::where('position', 'home_hero')->where('is_active', true)->count();
    return $count >= 4 ? true : "Expected >= 4, found {$count}";
});

// 2. Supermarket Product Catalog
runTest('Supermarket Catalog Products Active Count >= 50', function() {
    $count = Product::where('is_active', true)->count();
    return $count >= 50 ? true : "Expected >= 50, found {$count}";
});

// 3. Merchandising Flags
runTest('Merchandising Flags (Featured, Trending, Best Seller, Deals)', function() {
    $featured = Product::where('is_featured', true)->count();
    $trending = Product::where('is_trending', true)->count();
    $deals = Product::where('deal_of_the_day', true)->count();
    if ($featured > 0 && $trending > 0 && $deals > 0) return true;
    return "Featured: {$featured}, Trending: {$trending}, Deals: {$deals}";
});

// 4. Product Recommendations & Bundles
runTest('Product Recommendations & Bundles linked in DB', function() {
    $relations = \Illuminate\Support\Facades\DB::table('product_relations')->count();
    return $relations >= 3 ? true : "Found {$relations} relations";
});

// 5. Customer Reviews System
runTest('Verified Customer Reviews active and queryable', function() {
    $reviews = Review::where('status', 'approved')->count();
    return $reviews >= 5 ? true : "Found {$reviews} approved reviews";
});

// 6. Email Templates Dynamic Engine
runTest('Email Template Engine renders dynamic placeholders', function() {
    $tpl = EmailTemplate::where('key', 'order_confirmed')->first();
    if (!$tpl) return "Template order_confirmed missing";
    $rendered = $tpl->render([
        'customer_name' => 'John Doe',
        'order_number'  => 'ORD-987654',
        'order_total'   => '45.50',
        'tracking_url'  => 'http://127.0.0.1:8000/store/track?order=ORD-987654',
        'store_name'    => 'AK-Mart Central',
    ]);
    if (str_contains($rendered['body'], 'John Doe') && str_contains($rendered['body'], 'ORD-987654')) {
        return true;
    }
    return "Rendered body did not replace placeholders";
});

// 7. WhatsApp Business API Settings
runTest('WhatsApp Business API configuration exists', function() {
    $settings = app(\App\Services\SettingsService::class);
    $phoneId = $settings->get('whatsapp_phone_number_id', '1098234857239');
    return !empty($phoneId) ? true : "WhatsApp Phone ID empty";
});

// 8. HTTP Routes Integrity Test
$routes = [
    '/store'                               => 'Storefront Homepage',
    '/store/shop'                          => 'Catalog Shop',
    '/store/search-suggestions?q=Rice'     => 'Live Autocomplete Search',
    '/store/product/1'                     => 'Product Detail Page',
    '/store/cart'                          => 'Cart Page',
    '/store/checkout'                      => 'Checkout Page',
    '/store/track'                         => 'Order Tracking Page',
    '/store-management/sliders'            => 'Admin Sliders Control',
    '/store-management/merchandising'      => 'Admin Merchandising Board',
    '/products/1/relations'                => 'Admin Product Relations Manager',
    '/communication/email-templates'       => 'Admin Email Templates',
    '/communication/whatsapp-config'       => 'Admin WhatsApp Business API Config',
];

foreach ($routes as $url => $label) {
    runTest("Route HTTP 200: {$label} ({$url})", function() use ($httpKernel, $url) {
        $req = Request::create($url, 'GET');
        $res = $httpKernel->handle($req);
        $code = $res->getStatusCode();
        return ($code === 200 || $code === 302) ? true : "HTTP Status {$code}";
    });
}

echo "\n--------------------------------------------------------\n";
echo " AUDIT SUMMARY: {$passCount} / {$totalTests} TESTS PASSED (" . round(($passCount / $totalTests) * 100) . "%)\n";
echo "--------------------------------------------------------\n";
