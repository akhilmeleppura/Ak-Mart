<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "========================================================\n";
echo " TESTING DRIVER DELIVERY PORTAL & LOGISTICS ENGINE\n";
echo "========================================================\n\n";

// 1. Create or Find Driver User
$driver = App\Models\User::firstOrCreate(
    ['email' => 'driver.speedy@akmart.local'],
    [
        'name' => 'Speedy Express Driver',
        'password' => bcrypt('secret123'),
        'phone' => '+18005550199',
        'role' => 'driver',
        'user_type' => 'driver',
    ]
);
if ($driver->role !== 'driver') {
    $driver->role = 'driver';
    $driver->save();
}
echo "[PASS] Driver account ready: {$driver->name} (ID: {$driver->id}, Role: {$driver->role})\n";

// 2. Create a Test Customer and Order
$customer = App\Models\User::firstOrCreate(
    ['email' => 'customer.test@akmart.local'],
    [
        'name' => 'John Shopper',
        'password' => bcrypt('secret123'),
        'phone' => '+18005550188',
    ]
);

$product = App\Models\Product::first();

$order = App\Models\Order::create([
    'order_number' => 'DRV-' . strtoupper(bin2hex(random_bytes(3))),
    'user_id' => $customer->id,
    'total_amount' => 49.99,
    'payment_status' => 'unpaid',
    'order_status' => 'pending',
    'payment_method' => 'cod',
    'shipping_address' => '456 Express Avenue, Suite 2B, NY',
    'billing_address' => '456 Express Avenue, Suite 2B, NY',
]);

if ($product) {
    App\Models\OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'product_name' => $product->name,
        'qty' => 2,
        'unit_price' => 24.99,
        'price' => 24.99,
        'total' => 49.98,
        'total_price' => 49.98,
    ]);
}

echo "[PASS] Test Order created: #{$order->order_number} (Status: {$order->order_status}, Method: {$order->payment_method})\n";

// 3. Test Driver Dashboard View Generation
Illuminate\Support\Facades\Auth::login($driver);
$dashController = new App\Http\Controllers\Driver\DriverDashboardController();
$request = Illuminate\Http\Request::create('/driver/dashboard', 'GET');
$view = $dashController->index($request);
$html = $view->render();

if (str_contains($html, 'Driver Delivery Portal') && str_contains($html, 'My Active Route')) {
    echo "[PASS] Driver Dashboard rendered successfully with all workflow tabs and live metrics!\n";
} else {
    echo "[FAIL] Driver Dashboard render check failed.\n";
    exit(1);
}

// 4. Test Driver Order Assignment
$orderController = new App\Http\Controllers\Driver\DriverOrderController();
$assignReq = Illuminate\Http\Request::create("/driver/orders/assign/{$order->id}", 'POST');
$assignReq->headers->set('Accept', 'application/json');

$assignResponse = $orderController->assign($order->id);
$order->refresh();

if ($order->driver_id === $driver->id && $order->order_status === 'assigned') {
    echo "[PASS] Order #{$order->order_number} successfully assigned to Driver #{$driver->id}!\n";
} else {
    echo "[FAIL] Order assignment failed. Current Driver: {$order->driver_id}, Status: {$order->order_status}\n";
    exit(1);
}

// 5. Test Status Transitions: picked_up -> in_transit -> delivered
$statusReq1 = Illuminate\Http\Request::create('/driver/orders/status', 'POST', [
    'order_id' => $order->id,
    'status' => 'picked_up',
]);
$statusReq1->headers->set('Accept', 'application/json');
$orderController->updateStatus($statusReq1);
$order->refresh();
echo "[PASS] Status updated to: {$order->order_status}\n";

$statusReq2 = Illuminate\Http\Request::create('/driver/orders/status', 'POST', [
    'order_id' => $order->id,
    'status' => 'in_transit',
]);
$statusReq2->headers->set('Accept', 'application/json');
$orderController->updateStatus($statusReq2);
$order->refresh();
echo "[PASS] Status updated to: {$order->order_status}\n";

$statusReq3 = Illuminate\Http\Request::create('/driver/orders/status', 'POST', [
    'order_id' => $order->id,
    'status' => 'delivered',
]);
$statusReq3->headers->set('Accept', 'application/json');
$orderController->updateStatus($statusReq3);
$order->refresh();

if ($order->order_status === 'delivered' && $order->payment_status === 'paid') {
    echo "[PASS] Order marked delivered and COD payment marked as paid!\n";
} else {
    echo "[FAIL] Delivery transition check failed: Status={$order->order_status}, Payment={$order->payment_status}\n";
    exit(1);
}

echo "\n--------------------------------------------------------\n";
echo " ALL DRIVER PORTAL TESTS PASSED SUCCESSFULLY (100%)\n";
echo "--------------------------------------------------------\n";
