<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class OtpController extends Controller
{
    public function __construct(private OtpService $otpService) {}

    // -----------------------------------------------------------------
    // Show OTP verification screen
    // GET /auth/otp
    // -----------------------------------------------------------------

    public function show(Request $request)
    {
        // Ensure there's a pending OTP session
        if (! $request->session()->has('otp_pending_identifier')) {
            return redirect()->route('auth-login-basic')
                ->withErrors(['email' => __('Session expired. Please login again.')]);
        }

        $identifier = $request->session()->get('otp_pending_identifier');
        $record     = $this->otpService->getActiveRecord($identifier, 'LOGIN');

        $pageConfigs = ['myLayout' => 'blank'];

        return view('auth.verify-otp', [
            'pageConfigs'   => $pageConfigs,
            'identifier'    => $identifier,
            'secondsLeft'   => $record?->secondsUntilResend(config('otp.resend_cooldown', 60)) ?? 0,
            'resendCount'   => $record?->resend_count ?? 0,
            'maxResends'    => config('otp.max_resends', 3),
            'expiresAt'     => $record?->expires_at,
            'canResend'     => $record?->canResend(config('otp.resend_cooldown', 60)) ?? false,
        ]);
    }

    // -----------------------------------------------------------------
    // Verify submitted OTP
    // POST /auth/otp
    // -----------------------------------------------------------------

    public function verify(Request $request)
    {
        $request->validate([
            'otp' => ['required', 'string', 'digits:' . config('otp.length', 6)],
        ]);

        if (! $request->session()->has('otp_pending_identifier')) {
            return redirect()->route('auth-login-basic')
                ->withErrors(['email' => __('Session expired. Please login again.')]);
        }

        $identifier   = $request->session()->get('otp_pending_identifier');
        $sessionToken = $request->session()->get('otp_session_token');

        // Rate limit verify attempts
        $rateLimitKey = 'otp-verify:' . $request->ip() . ':' . $identifier;
        if (RateLimiter::tooManyAttempts($rateLimitKey, 10)) {
            throw ValidationException::withMessages([
                'otp' => [__('Too many attempts. Please wait a moment before trying again.')],
            ]);
        }
        RateLimiter::hit($rateLimitKey, 60);

        $result = $this->otpService->verifyOtp(
            $identifier,
            'LOGIN',
            $request->input('otp'),
            $sessionToken
        );

        if (! $result['success']) {
            return match ($result['reason']) {
                'expired'      => $this->handleExpiredOtp($request, $identifier),
                'max_attempts' => $this->handleMaxAttempts($request),
                'not_found'    => $this->handleNotFound($request),
                default        => back()->withErrors([
                    'otp' => __('Invalid code. :left attempt(s) remaining.', [
                        'left' => $result['attempts_left'] ?? '?',
                    ]),
                ]),
            };
        }

        // OTP verified — complete the login
        $userId = $request->session()->pull('otp_pending_user_id');
        $remember = $request->session()->pull('otp_pending_remember', false);

        $user = User::find($userId);

        if (! $user) {
            return redirect()->route('auth-login-basic')
                ->withErrors(['email' => __('Authentication failed. Please login again.')]);
        }

        // Clean up OTP session data
        $request->session()->forget([
            'otp_pending_identifier',
            'otp_pending_user_id',
            'otp_pending_remember',
            'otp_session_token',
        ]);

        // Regenerate session before auth to prevent session fixation
        $request->session()->regenerate();

        Auth::login($user, $remember);

        RateLimiter::clear($rateLimitKey);

        return redirect()->intended(route('dashboard'));
    }

    // -----------------------------------------------------------------
    // Resend OTP
    // POST /auth/otp/resend
    // -----------------------------------------------------------------

    public function resend(Request $request)
    {
        if (! $request->session()->has('otp_pending_identifier')) {
            return response()->json(['success' => false, 'message' => __('Session expired.')], 422);
        }

        $identifier = $request->session()->get('otp_pending_identifier');
        $userId     = $request->session()->get('otp_pending_user_id');
        $user       = $userId ? User::find($userId) : null;

        // Rate limit
        if (! $this->otpService->checkRateLimit($request->ip(), 'LOGIN')) {
            return response()->json([
                'success' => false,
                'message' => __('Too many OTP requests. Please wait before trying again.'),
            ], 429);
        }

        $result = $this->otpService->resendOtp(
            $identifier,
            'LOGIN',
            $user,
            $request->ip(),
            $request->userAgent()
        );

        if (! $result['success']) {
            $message = match ($result['reason']) {
                'max_resends' => __('Maximum resend limit reached.'),
                'cooldown'    => __('Please wait :seconds seconds before resending.', [
                    'seconds' => $result['seconds_left'] ?? config('otp.resend_cooldown'),
                ]),
                default       => __('Failed to resend OTP. Please try again.'),
            };

            return response()->json([
                'success'      => false,
                'message'      => $message,
                'seconds_left' => $result['seconds_left'] ?? 0,
            ], 422);
        }

        // Update session token if changed
        $newRecord = $this->otpService->getActiveRecord($identifier, 'LOGIN');
        if ($newRecord?->session_token) {
            $request->session()->put('otp_session_token', $newRecord->session_token);
        }

        return response()->json([
            'success'      => true,
            'message'      => __('A new code has been sent to :identifier', ['identifier' => $identifier]),
            'resend_count' => $result['resend_count'],
            'max_resends'  => $result['max_resends'],
            'cooldown'     => config('otp.resend_cooldown', 60),
        ]);
    }

    // -----------------------------------------------------------------
    // Private helpers
    // -----------------------------------------------------------------

    private function handleExpiredOtp(Request $request, string $identifier): \Illuminate\Http\RedirectResponse
    {
        return back()->withErrors([
            'otp' => __('Your code has expired. Please request a new one.'),
        ])->with('show_resend', true);
    }

    private function handleMaxAttempts(Request $request): \Illuminate\Http\RedirectResponse
    {
        // Clear the pending session — force a fresh login
        $request->session()->forget([
            'otp_pending_identifier',
            'otp_pending_user_id',
            'otp_pending_remember',
            'otp_session_token',
        ]);

        return redirect()->route('auth-login-basic')
            ->withErrors(['email' => __('Too many incorrect attempts. Please login again.')]);
    }

    private function handleNotFound(Request $request): \Illuminate\Http\RedirectResponse
    {
        return redirect()->route('auth-login-basic')
            ->withErrors(['email' => __('No active verification code found. Please login again.')]);
    }
}
