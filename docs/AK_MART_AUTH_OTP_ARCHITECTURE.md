# AK-Mart — Authentication & OTP Architecture

## Overview
AK-Mart features a production-grade, cryptographically secure OTP authentication layer built cleanly on top of Laravel 12, Jetstream, and Sanctum.

---

## 1. OTP Security Design Principles

1. **Zero Plaintext Storage**: OTPs are generated cryptographically using `random_int(0, 9)` and stored as Bcrypt hashes via `Hash::make()`.
2. **Single-Use Enforcement**: Upon successful verification or when max attempts are exceeded, `is_invalidated` is set to `true` and `verified_at` is timestamped. Replay attacks are rejected.
3. **Purpose Isolation**: Every OTP is strictly bound to a purpose (`LOGIN`, `PASSWORD_RESET`, `EMAIL_VERIFICATION`, `PHONE_VERIFICATION`). An OTP issued for `LOGIN` can never verify a `PASSWORD_RESET` flow.
4. **Session Token Binding**: A random cryptographic session token binds the OTP verification to the current browser session.
5. **Rate Limiting & Anti-Brute-Force**:
   - Verification attempts per OTP: 5 maximum (`otp.max_attempts`)
   - Generation / Resend rate limiting: 10 per IP per minute via `RateLimiter`
   - Resend cooldown: 60 seconds (`otp.resend_cooldown`)
   - Maximum resends per flow: 3 (`otp.max_resends`)
6. **No Leakage**: OTPs are never included in URLs, localStorage, HTML comments, or server debug logs.

---

## 2. Authentication Flows

### A. Login with OTP
```mermaid
sequenceDiagram
    actor User
    participant Login as Login Controller
    participant Otp as OtpService
    participant DB as OtpVerification (DB)
    participant UI as Verify OTP View
    participant Dash as Dashboard

    User->>Login: Submit email & password
    Login->>Login: Validate credentials via Hash::check()
    Login->>Otp: createOtp(email, 'LOGIN')
    Otp->>DB: Save hashed OTP & session token
    Otp-->>User: Dispatch OtpNotification (Email)
    Login-->>UI: Redirect to /auth/otp
    User->>UI: Enter 6-digit OTP
    UI->>Login: POST /auth/otp
    Login->>Otp: verifyOtp(email, 'LOGIN', inputOtp)
    Otp->>DB: Check hash & invalidate record
    Login->>Login: Session::regenerate() & Auth::login()
    Login-->>Dash: Redirect to intended page / dashboard
```

### B. Forgot Password via OTP
```mermaid
sequenceDiagram
    actor User
    participant FP as ForgotPasswordOtpController
    participant Otp as OtpService
    participant DB as OtpVerification (DB)

    User->>FP: Enter email address
    FP->>Otp: createOtp(email, 'PASSWORD_RESET')
    Otp-->>User: Dispatch reset OTP email
    FP-->>User: Redirect to /auth/forgot-password/otp/verify
    User->>FP: Submit 6-digit reset code
    FP->>Otp: verifyOtp(email, 'PASSWORD_RESET', otp)
    Otp->>DB: Invalidate OTP
    FP->>FP: Issue short-lived reset authorization token (10 min)
    FP-->>User: Redirect to /auth/password/reset-otp
    User->>FP: Enter new password & confirm
    FP->>DB: Hash & save new password
    FP-->>User: Redirect to /auth/login-basic with success
```

---

## 3. Database Schema (`otp_verifications`)

| Column | Type | Description |
|---|---|---|
| `id` | `BIGINT UNSIGNED` | Primary Key |
| `user_id` | `BIGINT UNSIGNED (NULL)` | Foreign key to `users.id` |
| `identifier` | `VARCHAR(255)` | Email or phone number |
| `purpose` | `VARCHAR(50)` | `LOGIN`, `PASSWORD_RESET`, etc. |
| `otp_hash` | `VARCHAR(255)` | Bcrypt hash of OTP |
| `session_token` | `VARCHAR(255) (NULL)` | Session binding token |
| `expires_at` | `TIMESTAMP` | Expiration datetime (default: 5 min) |
| `verified_at` | `TIMESTAMP (NULL)` | When verified |
| `attempts` | `TINYINT UNSIGNED` | Failed verification attempts count |
| `max_attempts` | `TINYINT UNSIGNED` | Threshold (default: 5) |
| `resend_count` | `TINYINT UNSIGNED` | Number of times resent |
| `max_resends` | `TINYINT UNSIGNED` | Threshold (default: 3) |
| `last_sent_at` | `TIMESTAMP (NULL)` | Timestamp of last dispatch |
| `ip_address` | `VARCHAR(45)` | Client IP address |
| `user_agent` | `VARCHAR(512)` | Client user agent |
| `is_invalidated`| `BOOLEAN` | Hard invalidation flag |

---

## 4. Configuration (`config/otp.php`)

```php
return [
    'length'                 => (int) env('OTP_LENGTH', 6),
    'expiration'             => (int) env('OTP_EXPIRATION', 5),      // minutes
    'resend_cooldown'        => (int) env('OTP_RESEND_COOLDOWN', 60), // seconds
    'max_attempts'           => (int) env('OTP_MAX_ATTEMPTS', 5),
    'max_resends'            => (int) env('OTP_MAX_RESENDS', 3),
    'rate_limit'             => (int) env('OTP_RATE_LIMIT', 10),
    'login_enabled'          => (bool) env('OTP_LOGIN_ENABLED', true),
    'password_reset_enabled' => (bool) env('OTP_PASSWORD_RESET_ENABLED', true),
    'default_channel'        => env('OTP_DEFAULT_CHANNEL', 'email'),
    'reset_auth_expiry'      => (int) env('OTP_RESET_AUTH_EXPIRY', 10), // minutes
];
```
