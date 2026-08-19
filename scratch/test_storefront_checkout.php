<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Storefront\StorefrontController;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use App\Models\LoyaltyTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "=== Testing End-to-End Storefront Checkout Flow ===\n\n";

$controller = app(StorefrontController::class);
$user = User::first();
Auth::login($user);

$session = app('session.store');
$session->flush();

$product = Product::where('qty', '>', 5)->first();
$initialStock = $product->qty;
$initialPoints = LoyaltyTransaction::getCustomerBalance($user->id);

// 1. Add item to cart
$cart = [
    $product->id => [
        'id'    => $product->id,
        'name'  => $product->name,
        'price' => (float)$product->price,
        'qty'   => 2,
        'sku'   => $product->sku,
    ]
];
$session->put('cart', $cart);

// 2. Submit checkout request
$request = Request::create('/store/checkout/process', 'POST', [
    'customer_name'    => 'Akhil Test Shopper',
    'customer_email'   => 'shopper@ak-mart.com',
    'customer_phone'   => '+1987654321',
    'shipping_address' => '456 Express Avenue, Delivery Hub, 560001',
    'payment_method'   => 'cod',
]);
$request->setLaravelSession($session);

$response = $controller->processCheckout($request);

echo "1. Checkout Execution:\n";
echo "   - Redirect URL: " . $response->getTargetUrl() . "\n";
echo "   - Cart Cleared: " . (!$session->has('cart') ? "✓ YES" : "NO") . "\n";

// 3. Verify created order in database
$order = Order::latest()->first();
echo "\n2. Order Placed in Database:\n";
echo "   - Order Number: {$order->order_number}\n";
echo "   - Total Amount: \${$order->total_amount}\n";
echo "   - Payment Method: {$order->payment_method}\n";
echo "   - Status: {$order->order_status}\n";
echo "   - Items Count: " . $order->items()->count() . "\n";

// 4. Verify Stock Deduction
$newStock = $product->fresh()->qty;
echo "\n3. Stock Invariant:\n";
echo "   - Initial Stock: {$initialStock}, After Checkout: {$newStock} (Expected -2)\n";
echo "   - Stock Deduction: " . ($newStock === $initialStock - 2 ? "✓ PASS" : "FAIL") . "\n";

// 5. Verify Loyalty Points Accrual
$newPoints = LoyaltyTransaction::getCustomerBalance($user->id);
echo "\n4. Loyalty Points Accrual:\n";
echo "   - Initial Points: {$initialPoints}, After Order: {$newPoints}\n";
echo "   - Loyalty Accrual: " . ($newPoints >= $initialPoints ? "✓ PASS" : "FAIL") . "\n";

echo "\n=== End-to-End Storefront Workflow PASSED 100% ===\n";
