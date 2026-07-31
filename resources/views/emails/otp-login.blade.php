<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login OTP</title>
  <style>
    * { margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; color: #333; }
    .container { max-width: 600px; margin: 20px auto; border: 1px solid #ddd; border-radius: 5px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
    .header { background: linear-gradient(135deg, #154360 0%, #1a5276 100%); color: white; padding: 30px 20px; text-align: center; }
    .header h1 { font-size: 24px; margin-bottom: 5px; }
    .header p { font-size: 12px; opacity: 0.9; }
    .content { padding: 30px 20px; background: #f9f9f9; }
    .greeting { font-size: 16px; font-weight: bold; color: #154360; margin-bottom: 15px; }
    .message { margin-bottom: 20px; line-height: 1.8; }
    .otp-box { background: white; border: 2px solid #154360; border-radius: 5px; padding: 20px; text-align: center; margin: 25px 0; }
    .otp-code { font-size: 32px; font-weight: bold; letter-spacing: 5px; color: #154360; font-family: monospace; }
    .otp-subtext { font-size: 12px; color: #888; margin-top: 10px; }
    .warning-box { background: #fff3cd; border-left: 4px solid #e67e22; padding: 12px; margin: 20px 0; border-radius: 2px; font-size: 12px; color: #7d6608; }
    .footer { background: #154360; color: white; padding: 20px; text-align: center; font-size: 12px; }
    .footer p { margin: 5px 0; }
    .security-info { background: #e3f2fd; border-left: 4px solid #2196f3; padding: 12px; margin: 20px 0; border-radius: 2px; font-size: 12px; color: #1565c0; }
  </style>
</head>
<body>

<div class="container">

  {{-- Header --}}
  <div class="header">
    <h1>Login OTP</h1>
    <p>Land Record Digitalization System</p>
  </div>

  {{-- Content --}}
  <div class="content">

    <div class="greeting">
      Hello {{ $user->name }},
    </div>

    <div class="message">
      <p>You've requested to log in to your account. Here's your One-Time Password (OTP):</p>
    </div>

    {{-- OTP Display --}}
    <div class="otp-box">
      <div class="otp-code">{{ $otp }}</div>
      <div class="otp-subtext">This OTP is valid for 5 minutes</div>
    </div>

    {{-- Steps --}}
    <div class="message">
      <p><strong>Next Steps:</strong></p>
      <ol style="margin-left:20px;margin-top:10px">
        <li>Go to the OTP verification page</li>
        <li>Enter the 6-digit code above</li>
        <li>Click "Verify OTP"</li>
        <li>You'll be logged in to your account</li>
      </ol>
    </div>

    {{-- Warning --}}
    <div class="warning-box">
      ⏱️ <strong>This OTP expires in 5 minutes</strong><br>
      If you don't verify within 5 minutes, you'll need to request a new OTP.
    </div>

    {{-- Security Info --}}
    <div class="security-info">
      <strong>🔒 Security Notice:</strong> If you didn't request this login, please ignore this email. Someone tried to access your account, but they won't be able to without this OTP. Never share this code with anyone.
    </div>

    <div class="message">
      <p><strong>Important:</strong></p>
      <ul style="margin-left:20px;margin-top:10px">
        <li>Never share this OTP with anyone</li>
        <li>This is a one-time code - it can only be used once</li>
        <li>We'll never ask for your OTP via email again after this</li>
        <li>If you didn't request this, change your password immediately</li>
      </ul>
    </div>

  </div>

  {{-- Footer --}}
  <div class="footer">
    <p>
      <strong>Land Record Digitalization System</strong><br>
      Housing Scheme Administration<br>
      Sangareddy, Telangana
    </p>
    <p style="margin-top:15px;border-top:1px solid rgba(255,255,255,0.2);padding-top:10px;font-size:11px">
      This is an automated email. Please do not reply directly to this message.
    </p>
    <p style="margin-top:10px;font-size:10px;opacity:0.8">
      &copy; {{ date('Y') }} All rights reserved.
    </p>
  </div>

</div>

</body>
</html>
