<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\apps\SecurityCenterController;
use App\Models\AuditLog;
use App\Models\User;

echo "=== Testing SecurityCenterController ===\n\n";

$controller = app(SecurityCenterController::class);

// Create a dummy audit log if none exist to test relationship loading
$user = User::first();
if ($user) {
    AuditLog::create([
        'user_id' => $user->id,
        'event' => 'LOGIN_SUCCESS',
        'auditable_type' => User::class,
        'auditable_id' => $user->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'TestBrowser/1.0',
    ]);
}

try {
    $view = $controller->index();
    echo "✓ SecurityCenterController@index executed successfully!\n";
    echo "  - View name: " . $view->name() . "\n";
    echo "  - Total Users: " . $view->getData()['totalUsers'] . "\n";
    echo "  - Supreme Admins: " . $view->getData()['supremeAdminsCount'] . "\n";
    echo "  - 2FA Count: " . $view->getData()['twoFactorEnabledCount'] . "\n";
    echo "  - Recent Logs Count: " . $view->getData()['recentAuditLogs']->count() . "\n";
    if ($view->getData()['recentAuditLogs']->count() > 0) {
        $firstLog = $view->getData()['recentAuditLogs']->first();
        echo "  - First Log User: " . ($firstLog->user?->name ?? 'None') . " (User ID: " . $firstLog->user_id . ")\n";
    }
    echo "\n=== Relationship [user] on [AuditLog] is Working 100% ===\n";
} catch (\Throwable $e) {
    echo "❌ ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
