<?php

return [

    /*
    |--------------------------------------------------------------------------
    | OTP Length
    |--------------------------------------------------------------------------
    | Number of digits in the OTP code.
    */
    'length' => (int) env('OTP_LENGTH', 6),

    /*
    |--------------------------------------------------------------------------
    | OTP Expiration (minutes)
    |--------------------------------------------------------------------------
    | How long the OTP remains valid after generation.
    */
    'expiration' => (int) env('OTP_EXPIRATION', 5),

    /*
    |--------------------------------------------------------------------------
    | Resend Cooldown (seconds)
    |--------------------------------------------------------------------------
    | Minimum time a user must wait before requesting another OTP.
    */
    'resend_cooldown' => (int) env('OTP_RESEND_COOLDOWN', 60),

    /*
    |--------------------------------------------------------------------------
    | Max Verification Attempts
    |--------------------------------------------------------------------------
    | Number of wrong attempts before the OTP is invalidated.
    */
    'max_attempts' => (int) env('OTP_MAX_ATTEMPTS', 5),

    /*
    |--------------------------------------------------------------------------
    | Max Resend Count
    |--------------------------------------------------------------------------
    | Number of times a user can resend OTP for the same flow.
    */
    'max_resends' => (int) env('OTP_MAX_RESENDS', 3),

    /*
    |--------------------------------------------------------------------------
    | Rate Limit (per minute, per IP)
    |--------------------------------------------------------------------------
    | Maximum OTP generation attempts per IP per minute.
    */
    'rate_limit' => (int) env('OTP_RATE_LIMIT', 10),

    /*
    |--------------------------------------------------------------------------
    | OTP Purposes
    |--------------------------------------------------------------------------
    | Allowed purpose identifiers. Adding a new purpose here is all that's
    | needed to enable a new OTP flow; the service enforces isolation.
    */
    'purposes' => [
        'LOGIN',
        'PASSWORD_RESET',
        'EMAIL_VERIFICATION',
        'PHONE_VERIFICATION',
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    */
    'login_enabled'          => (bool) env('OTP_LOGIN_ENABLED', true),
    'password_reset_enabled' => (bool) env('OTP_PASSWORD_RESET_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Delivery Channels
    |--------------------------------------------------------------------------
    | Priority: email first. WhatsApp / SMS can be added when credentials
    | are available. Set to 'email' | 'sms' | 'whatsapp'
    */
    'default_channel' => env('OTP_DEFAULT_CHANNEL', 'email'),

    /*
    |--------------------------------------------------------------------------
    | Reset Authorization Expiry (minutes)
    |--------------------------------------------------------------------------
    | After OTP is verified for password reset, how long the reset session
    | authorization is valid before the user must start over.
    */
    'reset_auth_expiry' => (int) env('OTP_RESET_AUTH_EXPIRY', 10),

];
