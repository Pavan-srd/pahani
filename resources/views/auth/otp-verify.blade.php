<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Verify OTP — Land Record Digitalization</title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:Arial,sans-serif;font-size:12px;background:#f0f4f8;color:#1a1a2e}
    .portal-wrap{min-height:100vh;display:flex;flex-direction:column}

    /* ── HEADER ── */
    .gov-header{background:linear-gradient(135deg,#154360 0%,#1a5276 50%,#1e618f 100%);color:white;border-bottom:4px solid #f39c12}
    .gov-top-bar{background:#0d2d47;display:flex;align-items:center;justify-content:space-between;padding:6px 20px;font-size:10px;color:#b8cdd9}
    .gov-logo-row{display:flex;align-items:center;gap:16px;padding:12px 20px 10px}
    .emblem{width:56px;height:56px;background:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:28px;border:2px solid #f39c12;flex-shrink:0}
    .gov-title-block{flex:1}
    .gov-title-block .dept-name{font-size:18px;font-weight:bold;color:white;line-height:1.2;text-transform:uppercase;letter-spacing:1px}
    .gov-title-block .dept-sub{font-size:11px;color:#aed6f1;margin-top:2px}
    .gov-subtitle-bar{background:#1a6fa8;padding:7px 20px;font-size:11px;color:#d6eaf8;border-top:1px solid rgba(255,255,255,0.15);text-align:center;letter-spacing:0.3px}

    /* ── AUTH LAYOUT ── */
    .auth-body{flex:1;display:flex;align-items:center;justify-content:center;padding:32px 16px}
    .auth-card{background:white;border:1px solid #d0dde8;border-radius:2px;overflow:hidden;width:100%;max-width:420px;box-shadow:0 2px 10px rgba(21,67,96,0.08)}
    .section-header{background:#154360;color:white;padding:10px 16px;font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;display:flex;align-items:center;gap:8px}
    .section-header .sec-num{background:#f39c12;color:#1a1a2e;width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:bold;flex-shrink:0}
    .section-body{padding:20px}

    /* ── ALERTS ── */
    .alert{padding:9px 12px;border-radius:2px;font-size:11px;margin-bottom:12px;border-left:4px solid}
    .alert-success{background:#e8f5e9;border-color:#27ae60;color:#1b5e20}
    .alert-error{background:#fdecea;border-color:#c0392b;color:#7f0000}
    .alert-info{background:#e3f2fd;border-color:#2196f3;color:#1565c0}
    .notice-bar{background:#fff3cd;border:1px solid #ffc107;border-left:4px solid #e67e22;padding:8px 12px;font-size:10px;color:#7d6608;margin-bottom:14px}

    /* ── FORM FIELDS ── */
    .field-group{display:flex;flex-direction:column;gap:4px;margin-bottom:14px}
    .field-label{font-size:11px;font-weight:bold;color:#1a3a5c;text-transform:uppercase;letter-spacing:0.3px}
    .field-label .req{color:#c0392b;margin-left:2px}
    .field-hint{font-size:10px;color:#888;margin-top:2px}
    .field-error{font-size:10px;color:#c0392b;margin-top:2px}
    input[type=text],input[type=email],input[type=password]{width:100%;padding:8px 10px;border:1px solid #b0c4d8;border-radius:2px;font-size:12px;color:#1a1a2e;background:#f8fbfd;outline:none;transition:border-color 0.15s}
    input[type=text]:focus,input[type=email]:focus,input[type=password]:focus{border-color:#154360;background:white;box-shadow:0 0 0 2px rgba(21,67,96,0.1)}

    .row-between{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;font-size:11px}
    .check-label{display:flex;align-items:center;gap:6px;color:#333;cursor:pointer}
    .check-label input[type=checkbox]{cursor:pointer;accent-color:#154360}
    .link-muted{color:#154360;text-decoration:none;font-weight:bold}
    .link-muted:hover{text-decoration:underline}

    /* ── BUTTONS ── */
    .btn-primary{width:100%;background:#154360;color:white;border:none;padding:10px 24px;font-size:12px;font-weight:bold;cursor:pointer;text-transform:uppercase;letter-spacing:0.5px;border-radius:2px;transition:background 0.15s;margin-bottom:8px}
    .btn-primary:hover{background:#1a6fa8}
    .btn-primary:disabled{background:#aaa;cursor:not-allowed}

    .btn-secondary{width:100%;background:#eaf2f8;color:#154360;border:1px solid #c8dce9;padding:10px 24px;font-size:12px;font-weight:bold;cursor:pointer;text-transform:uppercase;letter-spacing:0.5px;border-radius:2px;transition:all 0.15s}
    .btn-secondary:hover{background:#d6eaf8;border-color:#154360}

    .portal-footer{text-align:center;font-size:10px;color:#888;padding:14px}

    .otp-info{background:#e8f4f8;border:1px solid #b3d9e8;border-radius:2px;padding:10px;margin-bottom:14px;font-size:10px;color:#0d5c7a}
    .otp-timer{color:#c0392b;font-weight:bold}

    @media(max-width:600px){.gov-title-block .dept-name{font-size:14px}}
  </style>
</head>
<body>
<div class="portal-wrap">

  {{-- ── HEADER ── --}}
  <div class="gov-header">
    <div class="gov-top-bar">
      <span>🔒 Secure Login &nbsp;|&nbsp; {{ date('d-M-Y') }}</span>
    </div>
    <div class="gov-logo-row">
      <div class="emblem">🏠</div>
      <div class="gov-title-block">
        <div class="dept-name">Land Record Digitalization</div>
        <div class="dept-sub">Revenue Department Sangareddy District</div>
      </div>
    </div>
    <div class="gov-subtitle-bar">
      Verify your identity with the OTP sent to your email
    </div>
  </div>

  <div class="auth-body">
    <div class="auth-card">
      <div class="section-header">
        <span class="sec-num">2</span>
        Verify OTP
      </div>
      <div class="section-body">

        {{-- Success Message --}}
        @if(session('success'))
          <div class="alert alert-success">
            ✔ {{ session('success') }}
          </div>
        @endif

        {{-- Error Messages --}}
        @if($errors->any())
          <div class="alert alert-error">
            <strong>Verification failed:</strong>
            <ul style="margin-top:5px;padding-left:16px">
              @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        {{-- Info Message --}}
        <div class="otp-info">
          📧 We've sent a 6-digit OTP to your email. Please enter it below.
        </div>

        {{-- OTP Verification Form --}}
        <form method="POST" action="{{ route('otp.verify') }}">
          @csrf

          {{-- Email (hidden) --}}
          <input type="hidden" name="email" value="{{ session('email') ?? old('email') }}">

          {{-- OTP Input --}}
          <div class="field-group">
            <label class="field-label" for="otp">Enter OTP <span class="req">*</span></label>
            <input 
              type="text" 
              id="otp" 
              name="otp" 
              placeholder="000000" 
              maxlength="6"
              inputmode="numeric"
              pattern="[0-9]{6}"
              required 
              autofocus
            >
            <div class="field-hint">6-digit code from your email</div>
          </div>

          {{-- Remember Me --}}
          <div class="row-between" style="margin-bottom:16px">
            <label class="check-label">
              <input type="checkbox" name="remember"> Remember me
            </label>
          </div>

          {{-- Verify Button --}}
          <button type="submit" class="btn-primary">Verify OTP</button>
        </form>

        {{-- Resend OTP --}}
        <div style="text-align:center;margin-top:14px">
          <p style="font-size:10px;color:#666;margin-bottom:8px">
            Didn't receive the OTP?
          </p>
          <form method="POST" action="{{ route('otp.resend') }}" style="display:inline">
            @csrf
            <input type="hidden" name="email" value="{{ session('email') ?? old('email') }}">
            <button type="submit" class="btn-secondary" style="width:auto;padding:8px 20px">
              Resend OTP
            </button>
          </form>
        </div>

      </div>
    </div>

    {{-- Back to Login --}}
    <div style="text-align:center;margin-top:16px;font-size:11px">
      <a href="{{ route('login') }}" style="color:#154360;text-decoration:none;font-weight:bold">
        ← Back to Login
      </a>
    </div>

  </div>

  <div class="portal-footer">
    &copy; {{ date('Y') }} Pahani Management System. All rights reserved.
  </div>

</div>

<script>
// Only allow numbers in OTP field
document.getElementById('otp')?.addEventListener('input', function(e) {
  this.value = this.value.replace(/[^0-9]/g, '');
});

// Auto-submit when 6 digits entered
document.getElementById('otp')?.addEventListener('input', function(e) {
  if (this.value.length === 6) {
    // Optional: auto-submit after small delay
    // this.form.submit();
  }
});
</script>

</body>
</html>
