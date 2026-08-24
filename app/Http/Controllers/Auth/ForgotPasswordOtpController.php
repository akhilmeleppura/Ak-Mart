<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ForgotPasswordOtpController extends Controller
{
    public function __construct(private OtpService $otpService) {}

    // -----------------------------------------------------------------
    // Step 1: Show forgot-password form (with 2 options: Email / Mobile)
    // GET /auth/forgot-password/otp
    // -----------------------------------------------------------------

    public function showRequestForm(Request $request)
    {
        $pageConfigs = ['myLayout' => 'blank'];
        $channel = $request->query('channel', 'email');

        return view('auth.forgot-password-otp', [
            'pageConfigs' => $pageConfigs,
            'step'        => 'request',
            'channel'     => $channel,
        ]);
    }

    // -----------------------------------------------------------------
    // Step 1 Submit: Send OTP via Email or Mobile
    // POST /auth/forgot-password/otp/send
    // -----------------------------------------------------------------

    public function sendOtp(Request $request)
    {
        $channel = $request->input('channel', 'email');

        if ($channel === 'phone') {
            $request->validate([
                'phone' => ['required', 'string', 'min:7', 'max:20'],
            ]);
            $rawIdentifier = trim($request->input('phone'));
            $user = User::where('phone', $rawIdentifier)
                ->orWhere('phone', 'LIKE', "%{$rawIdentifier}%")
                ->first();
            $identifier = $rawIdentifier;
        } else {
            $request->validate([
                'email' => ['required', 'email', 'max:255'],
            ]);
            $rawIdentifier = strtolower(trim($request->input('email')));
            $user = User::where('email', $rawIdentifier)->first();
            $identifier = $rawIdentifier;
        }

        // Rate limit
        $rateLimitKey = 'pw-reset-otp:' . $request->ip();
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            throw ValidationException::withMessages([
                $channel === 'phone' ? 'phone' : 'email' => [__('Too many password reset attempts. Please wait before trying again.')],
            ]);
        }
        RateLimiter::hit($rateLimitKey, 60);

        if ($user) {
            if (! config('otp.password_reset_enabled', true)) {
                return back()->withErrors([
                    $channel === 'phone' ? 'phone' : 'email' => __('Password reset via OTP is currently disabled.')
                ]);
            }

            ['otp' => $plainOtp] = $this->otpService->createOtp(
                $identifier,
                'PASSWORD_RESET',
                $user,
                null,
                $request->ip(),
                $request->userAgent()
            );

            // Send via email or sms/whatsapp
            $this->otpService->sendOtp($plainOtp, $identifier, 'PASSWORD_RESET', $user);
        }

        // Store identifier in session
        $request->session()->put('pw_reset_identifier', $identifier);
        $request->session()->put('pw_reset_channel', $channel);

        return redirect()->route('auth.forgot-password-otp.verify-form')
            ->with('status', __('A 6-digit reset code has been sent to :identifier', ['identifier' => $identifier]));
    }

    // -----------------------------------------------------------------
    // Step 2: Show OTP verification form
    // GET /auth/forgot-password/otp/verify
    // -----------------------------------------------------------------

    public function showVerifyForm(Request $request)
    {
        if (! $request->session()->has('pw_reset_identifier')) {
            return redirect()->route('auth.forgot-password-otp.request');
        }

        $identifier = $request->session()->get('pw_reset_identifier');
        $channel    = $request->session()->get('pw_reset_channel', 'email');
        $record     = $this->otpService->getActiveRecord($identifier, 'PASSWORD_RESET');

        $pageConfigs = ['myLayout' => 'blank'];

        return view('auth.forgot-password-otp', [
            'pageConfigs' => $pageConfigs,
            'step'        => 'verify',
            'channel'     => $channel,
            'identifier'  => $identifier,
            'expiresAt'   => $record?->expires_at,
            'canResend'   => $record?->canResend(config('otp.resend_cooldown', 60)) ?? false,
            'secondsLeft' => $record?->secondsUntilResend(config('otp.resend_cooldown', 60)) ?? 0,
        ]);
    }

    // -----------------------------------------------------------------
    // Step 2 Submit: Verify OTP → grant reset authorization
    // POST /auth/forgot-password/otp/verify
    // -----------------------------------------------------------------

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'digits:' . config('otp.length', 6)],
        ]);

        if (! $request->session()->has('pw_reset_identifier')) {
            return redirect()->route('auth.forgot-password-otp.request')
                ->withErrors(['otp' => __('Session expired. Please start again.')]);
        }

        $identifier = $request->session()->get('pw_reset_identifier');

        $result = $this->otpService->verifyOtp($identifier, 'PASSWORD_RESET', $request->input('otp'));

        if (! $result['success']) {
            $message = match ($result['reason']) {
                'expired'      => __('Your code has expired. Please request a new one.'),
                'max_attempts' => __('Too many incorrect attempts. Please request a new reset code.'),
                'not_found'    => __('No valid reset code found. Please request a new one.'),
                default        => __('Invalid code. :left attempt(s) remaining.', [
                    'left' => $result['attempts_left'] ?? '?',
                ]),
            };

            if (in_array($result['reason'], ['max_attempts', 'not_found'], true)) {
                $request->session()->forget('pw_reset_identifier');
                return redirect()->route('auth.forgot-password-otp.request')
                    ->withErrors(['email' => $message]);
            }

            return back()->withErrors(['otp' => $message]);
        }

        // OTP verified — grant short-lived reset authorization
        $resetToken = Str::random(64);
        $request->session()->put('pw_reset_authorized', [
            'identifier'    => $identifier,
            'token'         => $resetToken,
            'authorized_at' => now()->toISOString(),
            'expires_at'    => now()->addMinutes(config('otp.reset_auth_expiry', 10))->toISOString(),
        ]);

        return redirect()->route('auth.password-reset-otp.form');
    }

    // -----------------------------------------------------------------
    // Step 3: Show new password form
    // GET /auth/password/reset-otp
    // -----------------------------------------------------------------

    public function showResetForm(Request $request)
    {
        $auth = $request->session()->get('pw_reset_authorized');

        if (! $this->isValidResetAuth($auth)) {
            $request->session()->forget(['pw_reset_identifier', 'pw_reset_authorized']);
            return redirect()->route('auth.forgot-password-otp.request')
                ->withErrors(['email' => __('Reset authorization expired. Please start again.')]);
        }

        $pageConfigs = ['myLayout' => 'blank'];

        return view('auth.forgot-password-otp', [
            'pageConfigs' => $pageConfigs,
            'step'        => 'reset',
            'identifier'  => $auth['identifier'],
        ]);
    }

    // -----------------------------------------------------------------
    // Step 3 Submit: Reset password
    // POST /auth/password/reset-otp
    // -----------------------------------------------------------------

    public function resetPassword(Request $request)
    {
        $auth = $request->session()->get('pw_reset_authorized');

        if (! $this->isValidResetAuth($auth)) {
            $request->session()->forget(['pw_reset_identifier', 'pw_reset_authorized']);
            return redirect()->route('auth.forgot-password-otp.request')
                ->withErrors(['email' => __('Reset authorization expired. Please start again.')]);
        }

        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $identifier = $auth['identifier'];
        $user = User::where('email', $identifier)
            ->orWhere('phone', $identifier)
            ->first();

        if (! $user) {
            return redirect()->route('auth.forgot-password-otp.request')
                ->withErrors(['email' => __('Account not found.')]);
        }

        $user->update([
            'password' => Hash::make($request->input('password')),
        ]);

        // Invalidate all existing password reset OTPs for this user
        $this->otpService->invalidateExistingOtps($identifier, 'PASSWORD_RESET');

        // Clear all reset session data
        $request->session()->forget(['pw_reset_identifier', 'pw_reset_authorized', 'pw_reset_channel']);

        return redirect()->route('login')
            ->with('success', __('Password reset successfully. You can now sign in with your new password.'));
    }

    // -----------------------------------------------------------------
    // Resend OTP for password reset
    // POST /auth/forgot-password/otp/resend
    // -----------------------------------------------------------------

    public function resendOtp(Request $request)
    {
        if (! $request->session()->has('pw_reset_identifier')) {
            return response()->json(['success' => false, 'message' => __('Session expired.')], 422);
        }

        $identifier = $request->session()->get('pw_reset_identifier');
        $user       = User::where('email', $identifier)->orWhere('phone', $identifier)->first();

        if (! $this->otpService->checkRateLimit($request->ip(), 'PASSWORD_RESET')) {
            return response()->json([
                'success' => false,
                'message' => __('Too many requests. Please wait before trying again.'),
            ], 429);
        }

        $result = $this->otpService->resendOtp(
            $identifier,
            'PASSWORD_RESET',
            $user,
            $request->ip(),
            $request->userAgent()
        );

        if (! $result['success']) {
            $message = match ($result['reason']) {
                'max_resends' => __('Maximum resend limit reached.'),
                'cooldown'    => __('Please wait :seconds seconds.', ['seconds' => $result['seconds_left'] ?? 60]),
                default       => __('Failed to resend. Please try again.'),
            };
            return response()->json(['success' => false, 'message' => $message, 'seconds_left' => $result['seconds_left'] ?? 0], 422);
        }

        return response()->json([
            'success'      => true,
            'message'      => __('Reset code resent to :identifier', ['identifier' => $identifier]),
            'resend_count' => $result['resend_count'],
            'max_resends'  => $result['max_resends'],
            'cooldown'     => config('otp.resend_cooldown', 60),
        ]);
    }

    // -----------------------------------------------------------------
    // Validate reset authorization structure and expiry
    // -----------------------------------------------------------------

    private function isValidResetAuth(?array $auth): bool
    {
        if (! $auth || ! isset($auth['identifier'], $auth['token'], $auth['expires_at'])) {
            return false;
        }

        return now()->isBefore(\Carbon\Carbon::parse($auth['expires_at']));
    }
}
