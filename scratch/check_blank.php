<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/auth/login-basic', 'GET');
$response = $kernel->handle($request);
$html = $response->getContent();

echo "Does HTML contain 'ak-login-card'? " . (str_contains($html, 'ak-login-card') ? "YES" : "NO (BLANK BODY!)") . "\n";
echo "Does HTML contain 'formAuthentication'? " . (str_contains($html, 'formAuthentication') ? "YES" : "NO (BLANK BODY!)") . "\n";
echo "Does HTML contain 'Instant OTP Sign In'? " . (str_contains($html, 'Instant OTP Sign In') ? "YES" : "NO") . "\n";
