<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class OtpVerified
{
    /**
     * If OTP login is enabled and a pending OTP session exists,
     * redirect the user to the OTP verification screen rather than
     * allowing them through to protected routes.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('otp.login_enabled', true)) {
            return $next($request);
        }

        // If there's a pending OTP session, user must verify first
        if ($request->session()->has('otp_pending_identifier')) {
            // Don't redirect if already on OTP-related routes to prevent loops
            $otpRoutes = [
                route('auth.otp.show', [], false),
                route('auth.otp.verify', [], false),
                route('auth.otp.resend', [], false),
                route('logout', [], false),
            ];

            if (! in_array($request->path(), array_map(fn($r) => ltrim($r, '/'), $otpRoutes), true)) {
                return redirect()->route('auth.otp.show');
            }
        }

        return $next($request);
    }
}
