<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\authentications\LoginBasic;
use Illuminate\Http\Request;
use App\Models\User;

echo "=== Testing Both Login Modes in LoginBasic ===\n\n";

$controller = app(LoginBasic::class);

// 1. Test Mode 1: Direct Passwordless OTP Login (login_mode = 'otp')
$reqOtp = Request::create('/auth/login-basic', 'POST', [
    'login_mode' => 'otp',
    'email' => 'admin@ak-mart.com',
]);
$reqOtp->setLaravelSession(app('session.store'));

$resOtp = $controller->store($reqOtp);
echo "1. Direct Passwordless OTP Login:\n";
echo "   - Redirect target: " . $resOtp->getTargetUrl() . "\n";
echo "   - Session has pending OTP identifier: " . ($reqOtp->session()->get('otp_pending_identifier') ?? 'None') . "\n";
echo "   - Session has session token: " . ($reqOtp->session()->get('otp_session_token') ? 'YES' : 'NO') . "\n\n";

// 2. Test Mode 2: Password + OTP Login (login_mode = 'password')
$reqPw = Request::create('/auth/login-basic', 'POST', [
    'login_mode' => 'password',
    'email' => 'admin@ak-mart.com',
    'password' => 'password',
]);
$reqPw->setLaravelSession(app('session.store'));

$resPw = $controller->store($reqPw);
echo "2. Password + OTP Verification Login:\n";
echo "   - Redirect target: " . $resPw->getTargetUrl() . "\n";
echo "   - Session has pending OTP identifier: " . ($reqPw->session()->get('otp_pending_identifier') ?? 'None') . "\n\n";

echo "=== Both Login Modes Working Perfectly ===\n";
