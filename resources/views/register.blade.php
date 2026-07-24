<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Register — Indiramma Indlu Management System</title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:Arial,sans-serif;font-size:12px;background:#f0f4f8;color:#1a1a2e}
    .portal-wrap{min-height:100vh;display:flex;flex-direction:column}

    /* ── HEADER ── */
    .gov-header{background:linear-gradient(135deg,#154360 0%,#1a5276 50%,#1e618f 100%);color:white;border-bottom:4px solid #f39c12}
    .gov-top-bar{background:#0d2d47;display:flex;align-items:center;justify-content:space-between;padding:6px 20px;font-size:10px;color:#b8cdd9}
    .gov-top-bar span{display:flex;align-items:center;gap:6px}
    .gov-logo-row{display:flex;align-items:center;gap:16px;padding:12px 20px 10px}
    .emblem{width:56px;height:56px;background:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:28px;border:2px solid #f39c12;flex-shrink:0}
    .gov-title-block{flex:1}
    .gov-title-block .dept-name{font-size:18px;font-weight:bold;color:white;line-height:1.2;text-transform:uppercase;letter-spacing:1px}
    .gov-title-block .dept-sub{font-size:11px;color:#aed6f1;margin-top:2px}
    .gov-subtitle-bar{background:#1a6fa8;padding:7px 20px;font-size:11px;color:#d6eaf8;border-top:1px solid rgba(255,255,255,0.15);text-align:center;letter-spacing:0.3px}

    /* ── AUTH LAYOUT ── */
    .auth-body{flex:1;display:flex;align-items:center;justify-content:center;padding:32px 16px}
    .auth-card{background:white;border:1px solid #d0dde8;border-radius:2px;overflow:hidden;width:100%;max-width:520px;box-shadow:0 2px 10px rgba(21,67,96,0.08)}
    .section-header{background:#154360;color:white;padding:10px 16px;font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;display:flex;align-items:center;gap:8px}
    .section-header .sec-num{background:#f39c12;color:#1a1a2e;width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:bold;flex-shrink:0}
    .section-body{padding:20px}

    /* ── ALERTS ── */
    .alert{padding:9px 12px;border-radius:2px;font-size:11px;margin-bottom:12px;border-left:4px solid}
    .alert-success{background:#e8f5e9;border-color:#27ae60;color:#1b5e20}
    .alert-error{background:#fdecea;border-color:#c0392b;color:#7f0000}
    .notice-bar{background:#fff3cd;border:1px solid #ffc107;border-left:4px solid #e67e22;padding:8px 12px;font-size:10px;color:#7d6608;margin-bottom:14px;display:flex;align-items:flex-start;gap:8px}

    /* ── FORM FIELDS ── */
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .field-group{display:flex;flex-direction:column;gap:4px;margin-bottom:14px}
    .field-label{font-size:11px;font-weight:bold;color:#1a3a5c;text-transform:uppercase;letter-spacing:0.3px}
    .field-label .req{color:#c0392b;margin-left:2px}
    .field-hint{font-size:10px;color:#888}
    input[type=text],input[type=email],input[type=password],input[type=tel]{width:100%;padding:8px 10px;border:1px solid #b0c4d8;border-radius:2px;font-size:12px;color:#1a1a2e;background:#f8fbfd;outline:none;transition:border-color 0.15s}
    input[type=text]:focus,input[type=email]:focus,input[type=password]:focus,input[type=tel]:focus{border-color:#154360;background:white;box-shadow:0 0 0 2px rgba(21,67,96,0.1)}
    .password-wrap{position:relative}
    .password-wrap .toggle-pw{position:absolute;right:10px;top:50%;transform:translateY(-50%);cursor:pointer;font-size:12px;color:#154360;background:none;border:none;padding:0}

    .check-label{display:flex;align-items:flex-start;gap:6px;color:#333;cursor:pointer;font-size:11px;margin-bottom:16px}
    .check-label input[type=checkbox]{cursor:pointer;accent-color:#154360;margin-top:2px}
    .link-muted{color:#154360;text-decoration:none;font-weight:bold}
    .link-muted:hover{text-decoration:underline}

    /* ── BUTTONS ── */
    .btn-primary{width:100%;background:#154360;color:white;border:none;padding:10px 24px;font-size:12px;font-weight:bold;cursor:pointer;text-transform:uppercase;letter-spacing:0.5px;border-radius:2px;transition:background 0.15s}
    .btn-primary:hover{background:#1a6fa8}
    .btn-primary:disabled{background:#aaa;cursor:not-allowed}

    /* ── FOOTER OF CARD ── */
    .auth-footer{background:#eaf2f8;border-top:1px solid #c8dce9;padding:12px 16px;text-align:center;font-size:11px;color:#444}

    .portal-footer{text-align:center;font-size:10px;color:#888;padding:14px}

    @media(max-width:600px){.form-row{grid-template-columns:1fr}.gov-title-block .dept-name{font-size:14px}}
  </style>
</head>
<body>
<div class="portal-wrap">

  {{-- ── HEADER ── --}}
  <div class="gov-header">
    <div class="gov-top-bar">
      <span>📝 New Registration &nbsp;|&nbsp; {{ date('d-M-Y') }}</span>
    </div>
    <div class="gov-logo-row">
      <div class="emblem">🏠</div>
      <div class="gov-title-block">
        <div class="dept-name">Indiramma Indlu Management System</div>
        <div class="dept-sub">Housing Scheme Administration Portal</div>
      </div>
    </div>
    <div class="gov-subtitle-bar">
      Create an account to access the management portal
    </div>
  </div>

  <div class="auth-body">
    <div class="auth-card">
      <div class="section-header">
        <span class="sec-num">1</span>
        User Registration
      </div>
      <div class="section-body">

        @if($errors->any())
          <div class="alert alert-error">
            <strong>Please fix the following errors:</strong>
            <ul style="margin-top:5px;padding-left:16px">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        <div class="notice-bar">
          ℹ️&nbsp;<span>Fields marked with <strong>*</strong> are mandatory. Use a valid email address — a verification link may be sent to it.</span>
        </div>

        <form method="POST" action="{{ route('register') }}">
          @csrf

          <div class="form-row">
            <div class="field-group">
              <label class="field-label" for="name">Full Name <span class="req">*</span></label>
              <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="Enter full name" required autofocus>
            </div>
            <div class="field-group">
              <label class="field-label" for="phone">Mobile Number <span class="req">*</span></label>
              <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" placeholder="10-digit mobile number" pattern="[0-9]{10}" required>
            </div>
          </div>

          <div class="field-group">
            <label class="field-label" for="email">Email Address <span class="req">*</span></label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="you@example.com" required>
          </div>

          <div class="form-row">
            <div class="field-group">
              <label class="field-label" for="password">Password <span class="req">*</span></label>
              <div class="password-wrap">
                <input type="password" id="password" name="password" placeholder="Create a password" required>
                <button type="button" class="toggle-pw" onclick="togglePassword('password', this)">👁️</button>
              </div>
              <div class="field-hint">Minimum 8 characters</div>
            </div>
            <div class="field-group">
              <label class="field-label" for="password_confirmation">Confirm Password <span class="req">*</span></label>
              <div class="password-wrap">
                <input type="password" id="password_confirmation" name="password_confirmation" placeholder="Re-enter password" required>
                <button type="button" class="toggle-pw" onclick="togglePassword('password_confirmation', this)">👁️</button>
              </div>
            </div>
          </div>

          <label class="check-label">
            <input type="checkbox" name="terms" required>
            <span>I agree to the terms of use and confirm the information provided above is accurate.</span>
          </label>

          <button type="submit" class="btn-primary">Create Account</button>
        </form>

      </div>
      <div class="auth-footer">
        Already have an account? <a href="{{ route('login') }}" class="link-muted">Sign in here</a>
      </div>
    </div>
  </div>

  <div class="portal-footer">
    &copy; {{ date('Y') }} Indiramma Indlu Management System. All rights reserved.
  </div>

</div>

<script>
function togglePassword(id, btn) {
  const input = document.getElementById(id);
  const isPw = input.type === 'password';
  input.type = isPw ? 'text' : 'password';
  btn.textContent = isPw ? '🙈' : '👁️';
}
</script>

</body>
</html>
