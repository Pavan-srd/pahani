<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Forgot Password — Land Record Digitalization</title>
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
    input[type=text],input[type=email]{width:100%;padding:8px 10px;border:1px solid #b0c4d8;border-radius:2px;font-size:12px;color:#1a1a2e;background:#f8fbfd;outline:none;transition:border-color 0.15s}
    input[type=text]:focus,input[type=email]:focus{border-color:#154360;background:white;box-shadow:0 0 0 2px rgba(21,67,96,0.1)}

    /* ── BUTTONS ── */
    .btn-primary{width:100%;background:#154360;color:white;border:none;padding:10px 24px;font-size:12px;font-weight:bold;cursor:pointer;text-transform:uppercase;letter-spacing:0.5px;border-radius:2px;transition:background 0.15s;margin-bottom:10px}
    .btn-primary:hover{background:#1a6fa8}
    .btn-primary:disabled{background:#aaa;cursor:not-allowed}

    .btn-secondary{width:100%;background:#eaf2f8;color:#154360;border:1px solid #c8dce9;padding:10px 24px;font-size:12px;font-weight:bold;cursor:pointer;text-transform:uppercase;letter-spacing:0.5px;border-radius:2px;transition:all 0.15s;text-decoration:none;display:flex;align-items:center;justify-content:center;gap:6px}
    .btn-secondary:hover{background:#d6eaf8;border-color:#154360}

    /* ── FOOTER OF CARD ── */
    .auth-footer{background:#eaf2f8;border-top:1px solid #c8dce9;padding:12px 16px;text-align:center;font-size:11px;color:#444}

    .portal-footer{text-align:center;font-size:10px;color:#888;padding:14px}

    .loading-spinner{display:none;width:14px;height:14px;border:2px solid #e0e0e0;border-top-color:#154360;border-radius:50%;animation:spin 0.6s linear infinite}
    @keyframes spin{to{transform:rotate(360deg)}}
    .btn-primary.loading .loading-spinner{display:inline-block}
    .btn-primary.loading span{display:none}

    @media(max-width:600px){.gov-title-block .dept-name{font-size:14px}}
  </style>
</head>
<body>
<div class="portal-wrap">

  {{-- ── HEADER ── --}}
  <div class="gov-header">
    <div class="gov-top-bar">
      <span>🔒 Secure Portal &nbsp;|&nbsp; {{ date('d-M-Y') }}</span>
    </div>
    <div class="gov-logo-row">
      <div class="emblem">🏠</div>
      <div class="gov-title-block">
        <div class="dept-name">Land Record Digitalization</div>
        <div class="dept-sub">Revenue Department Sangareddy District</div>
      </div>
    </div>
    <div class="gov-subtitle-bar">
      Reset your password to regain access to your account
    </div>
  </div>

  <div class="auth-body">
    <div class="auth-card">
      <div class="section-header">
        <span class="sec-num">1</span>
        Forgot Password
      </div>
      <div class="section-body">

        {{-- Success Message --}}
        @if(session('status'))
          <div class="alert alert-success">
            ✔ {{ session('status') }}
          </div>
          <div class="alert alert-info">
            💡 Check your email for a password reset link. The link will expire in 60 minutes.
          </div>
        @endif

        {{-- Validation Errors --}}
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

        {{-- Info Message --}}
        @if(!session('status'))
          <div class="notice-bar">
            ℹ️&nbsp;<span>Enter the email address associated with your account and we'll send you a link to reset your password.</span>
          </div>
        @endif

        {{-- Forgot Password Form --}}
        @if(!session('status'))
          <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <div class="field-group">
              <label class="field-label" for="email">Email Address <span class="req">*</span></label>
              <input 
                type="email" 
                id="email" 
                name="email" 
                value="{{ old('email') }}" 
                placeholder="you@example.com" 
                required 
                autofocus
              >
              <div class="field-hint">Your registered email address</div>
            </div>

            <button type="submit" class="btn-primary" id="submit-btn">
              <span>Send Reset Link</span>
              <div class="loading-spinner"></div>
            </button>
          </form>
        @endif

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
document.querySelector('form')?.addEventListener('submit', function() {
  const btn = document.getElementById('submit-btn');
  btn.classList.add('loading');
  btn.disabled = true;
});
document.querySelectorAll('form').forEach(function (form) {
  form.addEventListener('submit', function () {
    const btn = form.querySelector('button[type="submit"]');
    if (!btn) return;
    if (btn.dataset.submitted === '1') {
      // Guard in case submit fires again somehow
      return;
    }
    btn.dataset.submitted = '1';
    btn.dataset.originalText = btn.textContent;
    btn.disabled = true;
    btn.textContent = 'Please wait...';
  });
});
</script>

</body>
</html>