<?php

namespace App\Http\Controllers\authentications;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Auth\OtpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LoginBasic extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        $pageConfigs = ['myLayout' => 'blank'];

        return view('content.authentications.auth-login-basic', ['pageConfigs' => $pageConfigs]);
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $fieldType = filter_var($credentials['email'], FILTER_VALIDATE_EMAIL) ? 'email' : 'name';

        // Verify credentials manually (without fully logging in yet when OTP is enabled)
        $user = User::where($fieldType, $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], $user->password)) {
            return back()->withErrors([
                'email' => __('The provided credentials do not match our records.'),
            ])->onlyInput('email');
        }

        // --- OTP Flow ---
        if (config('otp.login_enabled', true)) {
            return $this->initiateOtpFlow($request, $user);
        }

        // --- Direct Login (OTP disabled) ---
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('auth-login-basic')
            ->with('success', __('You have been successfully logged out.'));
    }

    // -----------------------------------------------------------------
    // OTP Flow Initiation
    // -----------------------------------------------------------------

    private function initiateOtpFlow(Request $request, User $user): \Illuminate\Http\RedirectResponse
    {
        $otpService = app(OtpService::class);
        $identifier = $user->email;
        $remember   = $request->boolean('remember');

        // Rate limit OTP generation
        if (! $otpService->checkRateLimit($request->ip(), 'LOGIN')) {
            return back()->withErrors([
                'email' => __('Too many login attempts. Please wait a moment before trying again.'),
            ]);
        }

        // Generate and store OTP
        ['otp' => $plainOtp, 'record' => $record] = $otpService->createOtp(
            $identifier,
            'LOGIN',
            $user,
            Str::random(32),     // session token for binding
            $request->ip(),
            $request->userAgent()
        );

        // Send OTP
        $otpService->sendOtp($plainOtp, $identifier, 'LOGIN', $user);

        // Store pending session data (NOT the OTP itself)
        $request->session()->put([
            'otp_pending_identifier' => $identifier,
            'otp_pending_user_id'    => $user->id,
            'otp_pending_remember'   => $remember,
            'otp_session_token'      => $record->session_token,
        ]);

        return redirect()->route('auth.otp.show');
    }
}
