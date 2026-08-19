<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $pageConfigs = ['myLayout' => 'blank'];
    $html = view('content.authentications.auth-login-basic', ['pageConfigs' => $pageConfigs])->render();
    echo "Rendered length: " . strlen($html) . " bytes\n";
    echo "First 500 characters:\n" . substr($html, 0, 500) . "\n...\n";
    echo "Last 500 characters:\n" . substr($html, -500) . "\n";
} catch (\Throwable $e) {
    echo "ERROR rendering view: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
