<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $purposeLabel }} Code — {{ $appName }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #f0f2f5; font-family: 'Segoe UI', Arial, sans-serif; color: #333; }
        .wrapper { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #696cff 0%, #9155fd 100%); padding: 40px 32px; text-align: center; }
        .header h1 { color: #ffffff; font-size: 28px; font-weight: 700; letter-spacing: -0.5px; }
        .header p { color: rgba(255,255,255,0.8); font-size: 14px; margin-top: 6px; }
        .body { padding: 40px 32px; }
        .greeting { font-size: 16px; color: #555; margin-bottom: 20px; }
        .purpose-tag { display: inline-block; background: #f0efff; color: #696cff; border-radius: 6px; padding: 4px 12px; font-size: 13px; font-weight: 600; margin-bottom: 24px; }
        .otp-container { background: #f8f7ff; border: 2px dashed #696cff; border-radius: 12px; padding: 32px; text-align: center; margin: 24px 0; }
        .otp-label { font-size: 13px; color: #888; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px; }
        .otp-code { font-size: 48px; font-weight: 800; letter-spacing: 12px; color: #696cff; font-family: 'Courier New', monospace; }
        .otp-expiry { font-size: 13px; color: #e74c3c; margin-top: 12px; font-weight: 500; }
        .security-box { background: #fff8e1; border-left: 4px solid #f59e0b; border-radius: 8px; padding: 16px 20px; margin: 24px 0; }
        .security-box p { font-size: 13px; color: #856404; line-height: 1.6; }
        .security-box strong { color: #92400e; }
        .divider { border: none; border-top: 1px solid #eee; margin: 24px 0; }
        .footer { text-align: center; padding: 24px 32px 32px; background: #f8f9fa; }
        .footer p { font-size: 12px; color: #aaa; line-height: 1.6; }
        .footer a { color: #696cff; text-decoration: none; }
        .support { text-align: center; margin-top: 20px; }
        .support p { font-size: 13px; color: #666; }
        .support a { color: #696cff; font-weight: 600; }
    </style>
</head>
<body>
<div class="wrapper">
    <div class="header">
        <h1>🛒 {{ $appName }}</h1>
        <p>Secure Verification Code</p>
    </div>

    <div class="body">
        <p class="greeting">Hello,</p>
        <p style="color:#555; font-size:15px; line-height:1.7; margin-bottom:16px;">
            You requested a verification code for your <strong>{{ $appName }}</strong> account.
        </p>

        <span class="purpose-tag">{{ $purposeLabel }}</span>

        <div class="otp-container">
            <p class="otp-label">Your One-Time Code</p>
            <div class="otp-code">{{ $otp }}</div>
            <p class="otp-expiry">⏱ Expires in {{ $expiry }} minutes</p>
        </div>

        <div class="security-box">
            <p>
                <strong>🔒 Security Notice:</strong><br>
                Never share this code with anyone — including {{ $appName }} staff.
                This code is <strong>single-use</strong> and will expire automatically.
                If you did not request this code, please ignore this email and consider changing your password.
            </p>
        </div>

        <div class="support">
            <p>Need help? <a href="mailto:{{ config('mail.from.address') }}">Contact Support</a></p>
        </div>
    </div>

    <div class="footer">
        <p>
            This is an automated message from {{ $appName }}.<br>
            Please do not reply to this email.<br>
            &copy; {{ date('Y') }} {{ $appName }}. All rights reserved.
        </p>
    </div>
</div>
</body>
</html>
