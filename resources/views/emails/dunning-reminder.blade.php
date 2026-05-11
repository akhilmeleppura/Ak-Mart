<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Payment Reminder</title>
<style>
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f4f5f7; margin: 0; padding: 20px; color: #333; }
  .container { max-width: 580px; margin: 0 auto; background: #fff; border-radius: 8px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
  .header { background: {{ $type === 'grace_period_warning' ? '#ff4444' : '#7367f0' }}; padding: 30px; text-align: center; }
  .header h1 { color: #fff; margin: 0; font-size: 22px; }
  .body { padding: 35px; }
  .body h2 { font-size: 18px; margin-bottom: 12px; }
  .body p { line-height: 1.7; color: #555; margin-bottom: 16px; }
  .cta { display: block; text-align: center; background: #7367f0; color: #fff; padding: 14px 28px; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 15px; margin: 24px 0; }
  .box { background: #f8f9fa; border-left: 4px solid {{ $type === 'grace_period_warning' ? '#ff4444' : '#7367f0' }}; padding: 15px 20px; border-radius: 4px; margin-bottom: 20px; }
  .footer { text-align: center; padding: 20px; font-size: 12px; color: #999; background: #f4f5f7; }
</style>
</head>
<body>
<div class="container">
  <div class="header">
    <h1>{{ $type === 'grace_period_warning' ? '🚨 Urgent Notice' : '⚠️ Payment Reminder' }}</h1>
  </div>
  <div class="body">
    @if($type === 'email_reminder')
      <h2>Your payment didn't go through.</h2>
      <p>Hi there,</p>
      <p>We attempted to charge your payment method for your <strong>{{ $planName }}</strong> subscription, but the payment was unsuccessful.</p>
      <div class="box">
        <strong>Your subscription is past due by {{ $daysPastDue }} day{{ $daysPastDue > 1 ? 's' : '' }}.</strong><br>
        Please update your payment method to avoid any service interruption.
      </div>
      <p>Your store will remain accessible for the next few days while we retry the payment.</p>
    @else
      <h2>Your store access is at risk!</h2>
      <p>Hi there,</p>
      <p>Your <strong>{{ $planName }}</strong> subscription payment is now <strong>{{ $daysPastDue }} days overdue</strong>. We've tried multiple times but haven't been able to process your payment.</p>
      <div class="box" style="border-color:#ff4444; background:#fff5f5;">
        <strong>⚠️ Warning:</strong> If payment is not resolved soon, your store access will be suspended and all active products will be taken offline.
      </div>
    @endif

    <a href="{{ $billingUrl }}" class="cta">Update Payment Method →</a>

    <p>If you believe this is a mistake or need help, please contact our support team immediately.</p>
    <p>— The Platform Team</p>
  </div>
  <div class="footer">
    You're receiving this because you have an active subscription. &copy; {{ date('Y') }} Platform
  </div>
</div>
</body>
</html>
