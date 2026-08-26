<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Admin Login — Land Record Digitalization</title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:Arial,sans-serif;font-size:12px;background:#f0f4f8;color:#1a1a2e;min-height:100vh;display:flex;flex-direction:column}

    /* ── HEADER STRIP (matches portal theme) ── */
    .gov-header{background:linear-gradient(135deg,#154360 0%,#1a5276 50%,#1e618f 100%);color:white;border-bottom:4px solid #f39c12}
    .gov-top-bar{background:#0d2d47;padding:6px 20px;font-size:10px;color:#b8cdd9;text-align:center}
    .gov-logo-row{display:flex;align-items:center;justify-content:center;gap:14px;padding:14px 20px}
    .emblem{width:46px;height:46px;background:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:22px;border:2px solid #f39c12;flex-shrink:0}
    .gov-title-block{text-align:left}
    .gov-title-block .dept-name{font-size:15px;font-weight:bold;color:white;line-height:1.2;text-transform:uppercase;letter-spacing:1px}
    .gov-title-block .dept-sub{font-size:10px;color:#aed6f1;margin-top:2px}

    /* ── CENTER STAGE ── */
    .center-stage{flex:1;display:flex;align-items:center;justify-content:center;padding:30px 16px}

    .login-card{background:white;border:1px solid #d0dde8;border-top:4px solid #154360;border-radius:3px;width:100%;max-width:380px;box-shadow:0 8px 24px rgba(21,67,96,0.08)}
    .login-card-header{padding:22px 26px 6px;text-align:center}
    .login-card-header .lock-icon{width:44px;height:44px;background:#eaf2f8;border:1px solid #b8d4e8;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:20px;margin:0 auto 10px}
    .login-card-header h1{font-size:14px;font-weight:bold;color:#154360;text-transform:uppercase;letter-spacing:0.5px}
    .login-card-header p{font-size:10px;color:#888;margin-top:4px}

    .login-card-body{padding:20px 26px 26px}

    .alert{padding:9px 12px;border-radius:2px;font-size:11px;margin-bottom:14px;border-left:4px solid;background:#fdecea;border-color:#c0392b;color:#7f0000}
    .alert ul{margin:4px 0 0 16px}

    .form-field{margin-bottom:16px;display:flex;flex-direction:column;gap:5px}
    .form-field label{font-size:11px;font-weight:bold;color:#1a3a5c;text-transform:uppercase;letter-spacing:0.3px}
    .form-field input{padding:9px 11px;border:1px solid #b0c4d8;border-radius:2px;font-size:12px;color:#1a1a2e;background:#f8fbfd;outline:none;transition:border-color 0.15s}
    .form-field input:focus{border-color:#154360;background:white;box-shadow:0 0 0 2px rgba(21,67,96,0.1)}
    .form-field.has-error input{border-color:#c0392b}
    .field-error{font-size:10px;color:#c0392b;margin-top:2px}

    .form-options{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;font-size:11px}
    .remember-check{display:flex;align-items:center;gap:6px;color:#555}
    .remember-check input{accent-color:#154360}

    .btn-login{width:100%;background:#154360;color:white;border:none;padding:11px;font-size:12px;font-weight:bold;cursor:pointer;border-radius:2px;text-transform:uppercase;letter-spacing:0.5px;transition:background 0.15s}
    .btn-login:hover{background:#1a6fa8}
    .btn-login[disabled]{opacity:0.6;cursor:not-allowed}

    .login-footer-note{text-align:center;margin-top:16px;font-size:10px;color:#999}

    .site-footer{text-align:center;padding:14px;font-size:10px;color:#889;background:white;border-top:1px solid #e2eaf1}
  </style>
</head>
<body>

  {{-- ── HEADER ── --}}
  <div class="gov-header">
    <div class="gov-top-bar">Restricted Access &nbsp;·&nbsp; Authorized Personnel Only</div>
    <div class="gov-logo-row">
      <div class="emblem">⚖️</div>
      <div class="gov-title-block">
        <div class="dept-name">Land Record Digitalization</div>
        <div class="dept-sub">Sangareddy &nbsp;|&nbsp; Revenue Department</div>
      </div>
    </div>
  </div>

  {{-- ── LOGIN CARD ── --}}
  <div class="center-stage">
    <div class="login-card">
      <div class="login-card-header">
        <div class="lock-icon">🔐</div>
        <h1>Admin Login</h1>
        <p>Sign in with your administrator credentials</p>
      </div>

      <div class="login-card-body">

        @if ($errors->any())
          <div class="alert">
            <strong>{{ $errors->first() }}</strong>
          </div>
        @endif

        @if (session('status'))
          <div class="alert" style="background:#e8f5e9;border-color:#27ae60;color:#1b5e20">
            {{ session('status') }}
          </div>
        @endif

        <form method="POST" action="{{ route('admin.login.submit') }}" novalidate>
          @csrf

          <div class="form-field {{ $errors->has('email') ? 'has-error' : '' }}">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="admin@example.com" required autofocus>
            @error('email')
              <div class="field-error">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-field {{ $errors->has('password') ? 'has-error' : '' }}">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="••••••••" required>
            @error('password')
              <div class="field-error">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-options">
            <label class="remember-check">
              <input type="checkbox" name="remember" value="1"> Remember me
            </label>
          </div>

          <button type="submit" class="btn-login">Sign In to Admin Panel</button>
        </form>

        <div class="login-footer-note">
          This portal is for authorized administrators only.<br>Unauthorized access attempts are logged.
        </div>
      </div>
    </div>
  </div>

  <div class="site-footer">
    © {{ date('Y') }} Pahani Management System. All rights reserved.
  </div>
<script>
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