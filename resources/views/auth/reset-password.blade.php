<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Reset Password — Land Record Digitalization</title>
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
    .alert-warning{background:#fff3cd;border-color:#ffc107;color:#7d6608}
    .notice-bar{background:#fff3cd;border:1px solid #ffc107;border-left:4px solid #e67e22;padding:8px 12px;font-size:10px;color:#7d6608;margin-bottom:14px}

    /* ── FORM FIELDS ── */
    .field-group{display:flex;flex-direction:column;gap:4px;margin-bottom:14px}
    .field-label{font-size:11px;font-weight:bold;color:#1a3a5c;text-transform:uppercase;letter-spacing:0.3px}
    .field-label .req{color:#c0392b;margin-left:2px}
    .field-hint{font-size:10px;color:#888;margin-top:2px}
    .field-error{font-size:10px;color:#c0392b;margin-top:2px}
    .password-strength{height:4px;background:#e0e0e0;border-radius:2px;margin-top:4px;overflow:hidden}
    .password-strength-bar{height:100%;width:0%;background:#c0392b;transition:width 0.3s,background-color 0.3s}
    .password-strength-bar.weak{background:#e67e22;width:33%}
    .password-strength-bar.medium{background:#f39c12;width:66%}
    .password-strength-bar.strong{background:#27ae60;width:100%}
    .password-strength-text{font-size:9px;color:#888;margin-top:2px}

    input[type=text],input[type=email],input[type=password]{width:100%;padding:8px 10px;border:1px solid #b0c4d8;border-radius:2px;font-size:12px;color:#1a1a2e;background:#f8fbfd;outline:none;transition:border-color 0.15s}
    input[type=text]:focus,input[type=email]:focus,input[type=password]:focus{border-color:#154360;background:white;box-shadow:0 0 0 2px rgba(21,67,96,0.1)}
    .password-wrap{position:relative}
    .password-wrap .toggle-pw{position:absolute;right:10px;top:50%;transform:translateY(-50%);cursor:pointer;font-size:12px;color:#154360;background:none;border:none;padding:0}

    /* ── PASSWORD REQUIREMENTS ── */
    .pw-requirements{background:#f8fbfd;border:1px solid #d0dde8;border-radius:2px;padding:10px;font-size:10px;color:#666;margin-top:10px}
    .pw-requirement{display:flex;align-items:center;gap:6px;margin:4px 0}
    .pw-requirement.met{color:#27ae60}
    .pw-requirement.unmet{color:#c0392b}
    .requirement-icon{width:12px;height:12px;display:flex;align-items:center;justify-content:center;font-size:9px}

    /* ── BUTTONS ── */
    .btn-primary{width:100%;background:#154360;color:white;border:none;padding:10px 24px;font-size:12px;font-weight:bold;cursor:pointer;text-transform:uppercase;letter-spacing:0.5px;border-radius:2px;transition:background 0.15s;margin-top:10px}
    .btn-primary:hover{background:#1a6fa8}
    .btn-primary:disabled{background:#aaa;cursor:not-allowed}

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
        <div class="dept-sub">Housing Scheme Administration Portal</div>
      </div>
    </div>
    <div class="gov-subtitle-bar">
      Create a new password for your account
    </div>
  </div>

  <div class="auth-body">
    <div class="auth-card">
      <div class="section-header">
        <span class="sec-num">1</span>
        Reset Password
      </div>
      <div class="section-body">

        {{-- Invalid Token Error --}}
        @if($errors->has('token'))
          <div class="alert alert-error">
            ✘ This password reset link is invalid or has expired. Please request a new one.
          </div>
          <div style="text-align:center;margin-top:16px">
            <a href="{{ route('password.request') }}" style="color:#154360;text-decoration:none;font-weight:bold">
              ← Request New Reset Link
            </a>
          </div>
        @else

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
        <div class="notice-bar">
          ℹ️&nbsp;<span>Create a strong password to secure your account. Use uppercase, lowercase, numbers, and symbols.</span>
        </div>

        {{-- Reset Password Form --}}
        <form method="POST" action="{{ route('password.update') }}">
          @csrf

          <input type="hidden" name="token" value="{{ $token }}">

          {{-- Email Field (readonly) --}}
          <div class="field-group">
            <label class="field-label" for="email">Email Address</label>
            <input 
              type="email" 
              id="email" 
              name="email" 
              value="{{ $email ?? old('email') }}" 
              required 
              readonly
              style="opacity:0.7;cursor:not-allowed"
            >
          </div>

          {{-- New Password Field --}}
          <div class="field-group">
            <label class="field-label" for="password">New Password <span class="req">*</span></label>
            <div class="password-wrap">
              <input 
                type="password" 
                id="password" 
                name="password" 
                placeholder="Enter new password" 
                required
                oninput="checkPasswordStrength(this.value)"
              >
              <button type="button" class="toggle-pw" onclick="togglePassword('password', this)">👁️</button>
            </div>
            <div class="password-strength">
              <div class="password-strength-bar" id="strength-bar"></div>
            </div>
            <div class="password-strength-text" id="strength-text"></div>

            {{-- Password Requirements --}}
            <div class="pw-requirements">
              <div class="pw-requirement" id="req-length">
                <span class="requirement-icon">✗</span>
                <span>At least 8 characters</span>
              </div>
              <div class="pw-requirement" id="req-upper">
                <span class="requirement-icon">✗</span>
                <span>One uppercase letter (A-Z)</span>
              </div>
              <div class="pw-requirement" id="req-lower">
                <span class="requirement-icon">✗</span>
                <span>One lowercase letter (a-z)</span>
              </div>
              <div class="pw-requirement" id="req-number">
                <span class="requirement-icon">✗</span>
                <span>One number (0-9)</span>
              </div>
              <div class="pw-requirement" id="req-symbol">
                <span class="requirement-icon">✗</span>
                <span>One special character (!@#$%^&*)</span>
              </div>
            </div>
          </div>

          {{-- Confirm Password Field --}}
          <div class="field-group">
            <label class="field-label" for="password_confirmation">Confirm Password <span class="req">*</span></label>
            <div class="password-wrap">
              <input 
                type="password" 
                id="password_confirmation" 
                name="password_confirmation" 
                placeholder="Confirm new password" 
                required
              >
              <button type="button" class="toggle-pw" onclick="togglePassword('password_confirmation', this)">👁️</button>
            </div>
          </div>

          <button type="submit" class="btn-primary" id="submit-btn">
            <span>Reset Password</span>
            <div class="loading-spinner"></div>
          </button>
        </form>

        @endif

      </div>
    </div>

    {{-- Back to Login --}}
    @if(!$errors->has('token'))
      <div style="text-align:center;margin-top:16px;font-size:11px">
        <a href="{{ route('login') }}" style="color:#154360;text-decoration:none;font-weight:bold">
          ← Back to Login
        </a>
      </div>
    @endif

  </div>

  <div class="portal-footer">
    &copy; {{ date('Y') }} Pahani Management System. All rights reserved.
  </div>

</div>

<script>
function togglePassword(id, btn) {
  const input = document.getElementById(id);
  const isPw = input.type === 'password';
  input.type = isPw ? 'text' : 'password';
  btn.textContent = isPw ? '🙈' : '👁️';
}

function checkPasswordStrength(password) {
  const requirements = {
    length: password.length >= 8,
    upper: /[A-Z]/.test(password),
    lower: /[a-z]/.test(password),
    number: /[0-9]/.test(password),
    symbol: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)
  };

  // Update requirement indicators
  updateRequirement('req-length', requirements.length);
  updateRequirement('req-upper', requirements.upper);
  updateRequirement('req-lower', requirements.lower);
  updateRequirement('req-number', requirements.number);
  updateRequirement('req-symbol', requirements.symbol);

  // Calculate strength
  const metCount = Object.values(requirements).filter(v => v).length;
  const bar = document.getElementById('strength-bar');
  const text = document.getElementById('strength-text');

  bar.className = 'password-strength-bar';
  
  if (metCount === 0) {
    bar.style.width = '0%';
    text.textContent = '';
  } else if (metCount <= 2) {
    bar.classList.add('weak');
    text.textContent = 'Weak password';
  } else if (metCount <= 3) {
    bar.classList.add('medium');
    text.textContent = 'Medium password';
  } else if (metCount >= 4) {
    bar.classList.add('strong');
    text.textContent = 'Strong password';
  }
}

function updateRequirement(id, isMet) {
  const elem = document.getElementById(id);
  const icon = elem.querySelector('.requirement-icon');
  
  if (isMet) {
    elem.classList.add('met');
    elem.classList.remove('unmet');
    icon.textContent = '✓';
  } else {
    elem.classList.add('unmet');
    elem.classList.remove('met');
    icon.textContent = '✗';
  }
}

// Form submission
document.querySelector('form')?.addEventListener('submit', function() {
  const btn = document.getElementById('submit-btn');
  if (btn) {
    btn.classList.add('loading');
    btn.disabled = true;
  }
});
</script>

</body>
</html>