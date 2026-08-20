<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

$admin = User::first();
Auth::login($admin);

$routes = [
    '/store',
    '/store/shop',
    '/store/search-suggestions?q=rice',
    '/store/buy-again',
    '/store/referral',
    '/store-management/filters',
];

echo "========================================================\n";
echo " TESTING ALL STOREFRONT & STORE BUILDER WEB ROUTES     \n";
echo "========================================================\n";

foreach ($routes as $uri) {
    $req = Request::create($uri, 'GET');
    $res = $app->handle($req);
    $status = $res->getStatusCode();
    if ($status === 200) {
        echo "[PASS] {$uri} => HTTP 200 OK\n";
    } else {
        echo "[FAIL] {$uri} => HTTP {$status}\n";
    }
}
