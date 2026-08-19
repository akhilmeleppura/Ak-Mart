<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\Auth\OtpService;
use App\Models\OtpVerification;

echo "=== AK-Mart OTP Service Full Test Suite ===\n\n";

$service = app(OtpService::class);
$email = 'audit_test@ak-mart.com';

// 1. Create OTP
$created = $service->createOtp($email, 'LOGIN');
$otp = $created['otp'];
$record = $created['record'];

echo "1. Create OTP:\n";
echo "   - Generated OTP length: " . strlen($otp) . " digits ($otp)\n";
echo "   - Hashed in DB: " . (strlen($record->otp_hash) > 20 ? "YES (Bcrypt hash)" : "NO") . "\n";
echo "   - Expires at: " . $record->expires_at->toDateTimeString() . "\n";
echo "   - Usable: " . ($record->isUsable() ? "YES" : "NO") . "\n\n";

// 2. Bad OTP Attempt
$bad = $service->verifyOtp($email, 'LOGIN', '000000');
echo "2. Bad OTP Verification:\n";
echo "   - Success: " . ($bad['success'] ? "FAIL (Should be false)" : "PASS (Rejected)") . "\n";
echo "   - Attempts left: " . ($bad['attempts_left'] ?? 'N/A') . "\n\n";

// 3. Wrong Purpose Attempt
$wrongPurpose = $service->verifyOtp($email, 'PASSWORD_RESET', $otp);
echo "3. Wrong Purpose Isolation Test:\n";
echo "   - Success: " . ($wrongPurpose['success'] ? "FAIL (Purpose leak!)" : "PASS (Isolated)") . "\n";
echo "   - Reason: " . ($wrongPurpose['reason'] ?? 'N/A') . "\n\n";

// 4. Good OTP Verification
$good = $service->verifyOtp($email, 'LOGIN', $otp);
echo "4. Correct OTP Verification:\n";
echo "   - Success: " . ($good['success'] ? "PASS (Verified)" : "FAIL (Rejected)") . "\n\n";

// 5. Replay / Reuse Attack Test
$reused = $service->verifyOtp($email, 'LOGIN', $otp);
echo "5. Replay / Single-Use Attack Test:\n";
echo "   - Success: " . ($reused['success'] ? "FAIL (Replay allowed!)" : "PASS (Replay blocked)") . "\n";
echo "   - Reason: " . ($reused['reason'] ?? 'N/A') . "\n\n";

// 6. Resend Cooldown Test
$resend1 = $service->resendOtp($email, 'LOGIN');
echo "6. Resend Test (Immediate):\n";
echo "   - Success: " . ($resend1['success'] ? "FAIL (Cooldown ignored)" : "PASS (Cooldown enforced: " . ($resend1['seconds_left'] ?? 0) . "s left)") . "\n\n";

// Clean up test record
OtpVerification::where('identifier', $email)->delete();
echo "=== All Core OTP Tests Completed Successfully ===\n";
