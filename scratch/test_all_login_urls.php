<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$urls = ['/login', '/auth/login-basic'];

foreach ($urls as $url) {
    $request = Illuminate\Http\Request::create($url, 'GET');
    $response = $kernel->handle($request);
    $html = $response->getContent();
    echo "URL: {$url}\n";
    echo "  - Status: " . $response->getStatusCode() . "\n";
    echo "  - HTML length: " . strlen($html) . " bytes\n";
    echo "  - Has 'ak-login-card': " . (str_contains($html, 'ak-login-card') ? "YES" : "NO") . "\n";
    echo "  - Has 'Instant OTP Sign In': " . (str_contains($html, 'Instant OTP Sign In') ? "YES" : "NO") . "\n";
    echo "  - Has 'Password + OTP': " . (str_contains($html, 'Password + OTP') ? "YES" : "NO") . "\n\n";
}
