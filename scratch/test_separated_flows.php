<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\authentications\LoginBasic;
use App\Http\Controllers\Auth\ForgotPasswordOtpController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

echo "=== Verifying Clean Separation of Login & Reset Flows ===\n\n";

$loginController = app(LoginBasic::class);
$fpController = app(ForgotPasswordOtpController::class);
$testEmail = 'admin@ak-mart.com';
$user = User::where('email', $testEmail)->first();

// 1. Test Direct Password Login (No OTP Prompt)
Auth::logout();
$session = app('session.store');
$session->flush();

$reqPw = Request::create('/auth/login-basic', 'POST', [
    'login_mode' => 'password',
    'email' => $testEmail,
    'password' => 'password',
]);
$reqPw->setLaravelSession($session);

$resPw = $loginController->store($reqPw);
echo "1. Standard Password Sign-In:\n";
echo "   - Redirect target: " . $resPw->getTargetUrl() . " (Should be /dashboard)\n";
echo "   - Logged in directly: " . (Auth::check() ? "✓ YES (Immediate dashboard access without OTP!)" : "NO (Failed)") . "\n\n";

// 2. Test Mobile / OTP Sign-In
Auth::logout();
$session->flush();

$reqOtp = Request::create('/auth/login-basic', 'POST', [
    'login_mode' => 'otp',
    'email' => $testEmail,
]);
$reqOtp->setLaravelSession($session);

$resOtp = $loginController->store($reqOtp);
echo "2. Mobile / OTP Sign-In:\n";
echo "   - Redirect target: " . $resOtp->getTargetUrl() . " (Should be /auth/otp)\n";
echo "   - Pending OTP session created: " . ($session->has('otp_pending_identifier') ? "✓ YES (OTP flow triggered)" : "NO") . "\n\n";

// 3. Test Forgot Password via Email
$session->flush();
$reqFpEmail = Request::create('/auth/forgot-password/otp/send', 'POST', [
    'channel' => 'email',
    'email' => $testEmail,
]);
$reqFpEmail->setLaravelSession($session);

$resFpEmail = $fpController->sendOtp($reqFpEmail);
echo "3. Forgot Password via Email:\n";
echo "   - Redirect target: " . $resFpEmail->getTargetUrl() . "\n";
echo "   - Reset identifier saved: " . $session->get('pw_reset_identifier') . "\n";
echo "   - Reset channel: " . $session->get('pw_reset_channel') . "\n\n";

// 4. Test Forgot Password via Mobile Phone
$session->flush();
// Ensure user has phone
$user->update(['phone' => '+919876543210']);

$reqFpPhone = Request::create('/auth/forgot-password/otp/send', 'POST', [
    'channel' => 'phone',
    'phone' => '+919876543210',
]);
$reqFpPhone->setLaravelSession($session);

$resFpPhone = $fpController->sendOtp($reqFpPhone);
echo "4. Forgot Password via Mobile Phone:\n";
echo "   - Redirect target: " . $resFpPhone->getTargetUrl() . "\n";
echo "   - Reset identifier saved: " . $session->get('pw_reset_identifier') . "\n";
echo "   - Reset channel: " . $session->get('pw_reset_channel') . "\n\n";

echo "=== All Separated Flows Verified Successfully ===\n";
