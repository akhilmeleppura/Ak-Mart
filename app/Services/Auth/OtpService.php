<?php

namespace App\Services\Auth;

use App\Models\OtpVerification;
use App\Models\User;
use App\Notifications\Auth\OtpNotification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class OtpService
{
    // -----------------------------------------------------------------
    // Generate a cryptographically secure OTP (digits only)
    // -----------------------------------------------------------------

    public function generateOtp(): string
    {
        $length = config('otp.length', 6);
        $digits = '';

        for ($i = 0; $i < $length; $i++) {
            $digits .= random_int(0, 9);
        }

        return $digits;
    }

    // -----------------------------------------------------------------
    // Create (and store) a new OTP record. Invalidates any prior OTPs
    // for the same identifier + purpose.
    // -----------------------------------------------------------------

    public function createOtp(
        string $identifier,
        string $purpose,
        ?User $user = null,
        ?string $sessionToken = null,
        ?string $ip = null,
        ?string $userAgent = null
    ): array {
        $this->validatePurpose($purpose);

        // Invalidate all existing active OTPs for this identifier + purpose
        $this->invalidateExistingOtps($identifier, $purpose);

        $plainOtp = $this->generateOtp();
        $hash     = Hash::make($plainOtp);

        $otpRecord = OtpVerification::create([
            'user_id'       => $user?->id,
            'identifier'    => $identifier,
            'purpose'       => $purpose,
            'otp_hash'      => $hash,
            'session_token' => $sessionToken,
            'expires_at'    => now()->addMinutes(config('otp.expiration', 5)),
            'attempts'      => 0,
            'max_attempts'  => config('otp.max_attempts', 5),
            'resend_count'  => 0,
            'max_resends'   => config('otp.max_resends', 3),
            'last_sent_at'  => now(),
            'ip_address'    => $ip,
            'user_agent'    => $userAgent,
            'is_invalidated'=> false,
        ]);

        // Return the plaintext OTP only at creation — never store it
        return [
            'otp'    => $plainOtp,
            'record' => $otpRecord,
        ];
    }

    // -----------------------------------------------------------------
    // Send OTP via configured channel
    // -----------------------------------------------------------------

    public function sendOtp(string $plainOtp, string $identifier, string $purpose, ?User $user = null): bool
    {
        try {
            $notifiable = $user ?? new \App\Notifications\AnonymousNotifiable($identifier);

            // For logged-in users or when email is identifier, use Laravel notification
            if ($user) {
                $user->notify(new OtpNotification($plainOtp, $purpose));
            } else {
                // Send to email address directly
                \Illuminate\Support\Facades\Notification::route('mail', $identifier)
                    ->notify(new OtpNotification($plainOtp, $purpose));
            }

            return true;
        } catch (\Throwable $e) {
            // Log the error but NOT the OTP
            Log::error('[OtpService] Failed to send OTP', [
                'identifier' => $identifier,
                'purpose'    => $purpose,
                'error'      => $e->getMessage(),
            ]);

            return false;
        }
    }

    // -----------------------------------------------------------------
    // Verify an OTP submission
    // -----------------------------------------------------------------

    public function verifyOtp(
        string $identifier,
        string $purpose,
        string $submittedOtp,
        ?string $sessionToken = null
    ): array {
        $record = OtpVerification::query()
            ->forIdentifier($identifier)
            ->forPurpose($purpose)
            ->where('is_invalidated', false)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $record) {
            return ['success' => false, 'reason' => 'not_found'];
        }

        if ($record->isExpired()) {
            return ['success' => false, 'reason' => 'expired', 'record' => $record];
        }

        if ($record->hasExceededAttempts()) {
            return ['success' => false, 'reason' => 'max_attempts', 'record' => $record];
        }

        // Session token binding — if provided, verify it matches
        if ($sessionToken && $record->session_token && $record->session_token !== $sessionToken) {
            return ['success' => false, 'reason' => 'session_mismatch'];
        }

        // Increment attempts before checking hash (prevents timing attacks on attempt count)
        $record->increment('attempts');

        if (! Hash::check($submittedOtp, $record->otp_hash)) {
            if ($record->fresh()->hasExceededAttempts()) {
                $record->update(['is_invalidated' => true]);
                return ['success' => false, 'reason' => 'max_attempts', 'record' => $record];
            }

            return [
                'success'           => false,
                'reason'            => 'invalid',
                'attempts_left'     => $record->max_attempts - $record->fresh()->attempts,
                'record'            => $record,
            ];
        }

        // Mark as verified and invalidate (single-use)
        $record->update([
            'verified_at'    => now(),
            'is_invalidated' => true,
        ]);

        return ['success' => true, 'record' => $record];
    }

    // -----------------------------------------------------------------
    // Resend OTP
    // -----------------------------------------------------------------

    public function resendOtp(
        string $identifier,
        string $purpose,
        ?User $user = null,
        ?string $ip = null,
        ?string $userAgent = null
    ): array {
        $existing = OtpVerification::query()
            ->forIdentifier($identifier)
            ->forPurpose($purpose)
            ->where('is_invalidated', false)
            ->whereNull('verified_at')
            ->latest()
            ->first();

        if (! $existing) {
            return ['success' => false, 'reason' => 'no_active_otp'];
        }

        $cooldown = config('otp.resend_cooldown', 60);

        if (! $existing->canResend($cooldown)) {
            if ($existing->hasExceededResends()) {
                return ['success' => false, 'reason' => 'max_resends'];
            }

            return [
                'success'         => false,
                'reason'          => 'cooldown',
                'seconds_left'    => $existing->secondsUntilResend($cooldown),
            ];
        }

        // Create a fresh OTP (invalidates existing)
        ['otp' => $plainOtp, 'record' => $newRecord] = $this->createOtp(
            $identifier,
            $purpose,
            $user,
            $existing->session_token,
            $ip,
            $userAgent
        );

        // Transfer resend count
        $newRecord->update([
            'resend_count' => $existing->resend_count + 1,
        ]);

        $sent = $this->sendOtp($plainOtp, $identifier, $purpose, $user);

        return [
            'success'      => $sent,
            'reason'       => $sent ? null : 'send_failed',
            'resend_count' => $newRecord->resend_count,
            'max_resends'  => $newRecord->max_resends,
        ];
    }

    // -----------------------------------------------------------------
    // Invalidate all active OTPs for identifier + purpose
    // -----------------------------------------------------------------

    public function invalidateExistingOtps(string $identifier, string $purpose): void
    {
        OtpVerification::query()
            ->forIdentifier($identifier)
            ->forPurpose($purpose)
            ->where('is_invalidated', false)
            ->whereNull('verified_at')
            ->update(['is_invalidated' => true]);
    }

    // -----------------------------------------------------------------
    // Rate Limiting check
    // -----------------------------------------------------------------

    public function checkRateLimit(string $ip, string $purpose): bool
    {
        $key   = "otp_generate:{$ip}:{$purpose}";
        $limit = config('otp.rate_limit', 10);

        if (RateLimiter::tooManyAttempts($key, $limit)) {
            return false;
        }

        RateLimiter::hit($key, 60);

        return true;
    }

    // -----------------------------------------------------------------
    // Get active (non-expired, non-invalidated) OTP record
    // -----------------------------------------------------------------

    public function getActiveRecord(string $identifier, string $purpose): ?OtpVerification
    {
        return OtpVerification::query()
            ->forIdentifier($identifier)
            ->forPurpose($purpose)
            ->active()
            ->latest()
            ->first();
    }

    // -----------------------------------------------------------------
    // Clean up expired OTPs (can be called from a scheduled command)
    // -----------------------------------------------------------------

    public function cleanupExpiredOtps(): int
    {
        return OtpVerification::query()
            ->where('expires_at', '<', now()->subHours(24))
            ->delete();
    }

    // -----------------------------------------------------------------
    // Purpose validation
    // -----------------------------------------------------------------

    private function validatePurpose(string $purpose): void
    {
        $allowed = config('otp.purposes', []);

        if (! in_array($purpose, $allowed, true)) {
            throw new \InvalidArgumentException("Invalid OTP purpose: {$purpose}");
        }
    }
}
