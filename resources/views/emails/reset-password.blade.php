<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Password Reset Request</title>
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
    .button-wrap { text-align: center; margin: 30px 0; }
    .button { background: #154360; color: white; padding: 12px 40px; text-decoration: none; border-radius: 3px; display: inline-block; font-weight: bold; }
    .button:hover { background: #1a6fa8; }
    .expiry-notice { background: #fff3cd; border-left: 4px solid #e67e22; padding: 12px; margin: 20px 0; border-radius: 2px; font-size: 12px; color: #7d6608; }
    .direct-link { background: white; padding: 15px; margin: 20px 0; border: 1px solid #ddd; border-radius: 2px; }
    .direct-link p { font-size: 12px; color: #666; margin-bottom: 8px; }
    .direct-link .link { font-size: 12px; word-break: break-all; color: #154360; text-decoration: none; }
    .footer { background: #154360; color: white; padding: 20px; text-align: center; font-size: 12px; }
    .footer p { margin: 5px 0; }
    .security-info { background: #e3f2fd; border-left: 4px solid #2196f3; padding: 12px; margin: 20px 0; border-radius: 2px; font-size: 12px; color: #1565c0; }
  </style>
</head>
<body>

<div class="container">

  {{-- Header --}}
  <div class="header">
    <h1>Password Reset Request</h1>
    <p>Land Record Digitalization System</p>
  </div>

  {{-- Content --}}
  <div class="content">

    <div class="greeting">
      Hello {{ $notifiable->name }},
    </div>

    <div class="message">
      <p>We received a request to reset the password for your account. Click the button below to reset your password:</p>
    </div>

    {{-- Reset Button --}}
    <div class="button-wrap">
      <a href="{{ $actionUrl }}" class="button" style="color:white">Reset Password</a>
    </div>

    {{-- Expiry Notice --}}
    <div class="expiry-notice">
      ⏱️ <strong>This link expires in 60 minutes</strong><br>
      If you don't reset your password within 60 minutes, you'll need to request a new reset link.
    </div>

    {{-- Direct Link Alternative --}}
    <div class="direct-link">
      <p><strong>Can't click the button? Copy and paste this link in your browser:</strong></p>
      <a href="{{ $actionUrl }}" class="link">{{ $actionUrl }}</a>
    </div>

    {{-- Security Info --}}
    <div class="security-info">
      <strong>🔒 Security Notice:</strong> If you didn't request this password reset, please ignore this email. Your account will remain secure unless the link is used. Never share this link with anyone else.
    </div>

    <div class="message">
      <p><strong>What happens next:</strong></p>
      <ul style="margin-left:20px;margin-top:10px">
        <li>Click the reset link above</li>
        <li>Enter your email address (pre-filled)</li>
        <li>Create a new strong password</li>
        <li>Confirm your new password</li>
        <li>You'll be logged out automatically</li>
        <li>Log in with your new password</li>
      </ul>
    </div>

    <div class="message">
      <p><strong>Password Requirements:</strong></p>
      <ul style="margin-left:20px;margin-top:10px">
        <li>At least 8 characters long</li>
        <li>Include uppercase letters (A-Z)</li>
        <li>Include lowercase letters (a-z)</li>
        <li>Include numbers (0-9)</li>
        <li>Include special characters (!@#$%^&*)</li>
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