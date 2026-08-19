<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$request = Illuminate\Http\Request::create('/auth/login-basic', 'GET');
$response = $kernel->handle($request);

echo "HTTP Status: " . $response->getStatusCode() . "\n";
echo "Content length: " . strlen($response->getContent()) . "\n";
echo "\n--- First 600 chars of HTML ---\n";
echo substr($response->getContent(), 0, 600) . "\n";
