<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Dashboard — Land Record Digitalization</title>
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
    .status-dot{width:8px;height:8px;border-radius:50%;background:#27ae60;display:inline-block;margin-right:4px}

    /* ── NAV ── */
    .page-nav{display:flex;justify-content:space-between;align-items:center;background:#154360;border-bottom:2px solid #f39c12;padding:0 20px;font-size:11px}
    .nav-left{display:flex;align-items:center;flex-wrap:wrap}
    .nav-item{color:#aed6f1;padding:8px 14px;border-right:1px solid rgba(255,255,255,0.1);transition:background 0.15s;text-decoration:none;display:inline-block}
    .nav-item:hover{background:rgba(255,255,255,0.1);color:white}
    .nav-item.active{background:#f39c12;color:#1a1a2e;font-weight:bold}
    .nav-right{margin-left:auto}
    .logout-btn{background:#dc3545;color:#fff;border:none;padding:6px 14px;border-radius:4px;cursor:pointer;font-weight:600;transition:.2s}
    .logout-btn:hover{background:#bb2d3b}

    /* ── LAYOUT ── */
    .main-body{flex:1;padding:16px 20px 28px;max-width:960px;width:100%;margin:0 auto}
    .page-heading{background:white;border:1px solid #d5e8f5;border-left:4px solid #154360;padding:10px 16px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap}
    .page-heading h2{font-size:13px;font-weight:bold;color:#154360;text-transform:uppercase;letter-spacing:0.5px}
    .page-heading .ph-sub{font-size:10px;color:#666}

    .welcome{background:white;border:1px solid #d0dde8;border-radius:2px;padding:14px 16px;margin-bottom:16px;display:flex;align-items:center;gap:14px}
    .welcome .avatar{width:42px;height:42px;border-radius:50%;background:#154360;color:white;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:bold;flex-shrink:0;border:2px solid #f39c12}
    .welcome .w-name{font-size:14px;font-weight:bold;color:#154360}
    .welcome .w-meta{font-size:10px;color:#777;margin-top:2px}

    .alert{padding:9px 12px;border-radius:2px;font-size:11px;margin-bottom:12px;border-left:4px solid}
    .alert-success{background:#e8f5e9;border-color:#27ae60;color:#1b5e20}
    .alert-error{background:#fdecea;border-color:#c0392b;color:#7f0000}

    /* ── CARDS ── */
    .card-grid{display:grid;grid-template-columns:1fr 1fr;gap:18px}
    .dash-card{background:white;border:1px solid #d0dde8;border-radius:3px;overflow:hidden;display:flex;flex-direction:column;transition:box-shadow 0.15s,transform 0.15s}
    .dash-card:hover{box-shadow:0 6px 18px rgba(21,67,96,0.15);transform:translateY(-2px)}
    .dash-card .card-top{background:linear-gradient(135deg,#154360 0%,#1a6fa8 100%);color:white;padding:18px 18px 16px;display:flex;align-items:center;gap:14px;border-bottom:3px solid #f39c12;text-decoration:none}
    .dash-card.bb .card-top{background:linear-gradient(135deg,#145a45 0%,#1e8a66 100%)}
    .card-icon{width:52px;height:52px;border-radius:50%;background:white;display:flex;align-items:center;justify-content:center;font-size:26px;flex-shrink:0;border:2px solid #f39c12}
    .card-step{font-size:9px;text-transform:uppercase;letter-spacing:0.6px;color:#d6eaf8}
    .card-title{font-size:15px;font-weight:bold;line-height:1.3;margin-top:2px}

    .card-body{padding:16px 18px;flex:1;display:flex;flex-direction:column;gap:12px}
    .card-desc{font-size:11px;color:#555;line-height:1.55}

    .perm-row{display:flex;gap:8px;flex-wrap:wrap}
    .perm{font-size:10px;font-weight:bold;padding:4px 9px;border-radius:12px;border:1px solid}
    .perm.on{background:#e8f5e9;color:#1b5e20;border-color:#a5d6a7}
    .perm.off{background:#f3f3f3;color:#999;border-color:#ddd}
    .card-warn{font-size:10px;color:#856404;background:#fff3cd;border:1px solid #ffc107;border-left:3px solid #e67e22;padding:6px 9px;border-radius:2px}

    .card-actions{padding:12px 18px;background:#eaf2f8;border-top:1px solid #c8dce9;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap}
    .btn-go{background:#154360;color:white;text-decoration:none;padding:9px 18px;font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:0.4px;border-radius:2px;transition:background 0.15s;display:inline-flex;align-items:center;gap:6px}
    .btn-go:hover{background:#1a6fa8}
    .dash-card.bb .btn-go{background:#145a45}
    .dash-card.bb .btn-go:hover{background:#1e8a66}
    .link-sec{font-size:11px;color:#154360;font-weight:bold;text-decoration:none}
    .link-sec:hover{text-decoration:underline}

    .portal-footer{text-align:center;font-size:10px;color:#888;padding:14px}

    @media(max-width:700px){
      .card-grid{grid-template-columns:1fr}
      .gov-title-block .dept-name{font-size:14px}
    }
  </style>
</head>
<body>
<div class="portal-wrap">

  {{-- ── HEADER ── --}}
  <div class="gov-header">
    <div class="gov-top-bar">
      <span><span class="status-dot"></span>Portal Status: Active &nbsp;|&nbsp; {{ date('d-M-Y') }}</span>
    </div>
    <div class="gov-logo-row">
      <div class="emblem">⚖️</div>
      <div class="gov-title-block">
        <div class="dept-name">Land Record Digitalization</div>
        <div class="dept-sub">Sangareddy &nbsp;|&nbsp; Revenue Department</div>
      </div>
    </div>
    <div class="gov-subtitle-bar">
      USER DASHBOARD — Sangareddy District ({{ $user?->name }}{{ $user?->getMandal?->name ? ', ' . $user->getMandal->name : '' }})
    </div>
  </div>

  {{-- ── NAV ── --}}
  <div class="page-nav">
    <div class="nav-left">
      <a class="nav-item active" href="{{ route('home') }}">🏠 Dashboard</a>
      <a class="nav-item" href="{{ route('pahani.index') }}">📂 Pahani Upload</a>
      <a class="nav-item" href="{{ route('pahani.view') }}">📋 View Records</a>
      <a class="nav-item" href="{{ route('bhu-bharathi.index') }}">🗂️ Bhu Bharathi Disposals</a>
      <a class="nav-item" href="{{ route('reports.user') }}">📊 Reports</a>
    </div>
    <div class="nav-right">
      <form id="logoutForm" method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="button" class="logout-btn" onclick="confirmLogout()">🚪 Logout</button>
      </form>
    </div>
  </div>

  <div class="main-body">

    @if(session('success'))
      <div class="alert alert-success">✔ {{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="welcome">
      <div class="avatar">{{ strtoupper(mb_substr($user?->name ?? 'U', 0, 1)) }}</div>
      <div>
        <div class="w-name">Welcome, {{ $user?->name }}</div>
        <div class="w-meta">{{ $user?->email }} &nbsp;·&nbsp; Choose a module below to start uploading documents</div>
      </div>
    </div>

    <div class="page-heading">
      <h2>🏠 Document Upload Modules</h2>
      <div class="ph-sub">Select where you want to work</div>
    </div>

    <div class="card-grid">

      {{-- ═══ CARD 1: PAHANI ═══ --}}
      <div class="dash-card">
        <a class="card-top" href="{{ route('pahani.index') }}">
          <div class="card-icon">📑</div>
          <div>
            <div class="card-step">Module 1</div>
            <div class="card-title">Pahani Document Upload</div>
          </div>
        </a>
        <div class="card-body">
          <div class="card-desc">
            Upload core documents (like Sethwar) and year-wise Pahani records village by village, with parallel PDF / TIFF upload.
          </div>
          <div class="perm-row">
            <span class="perm {{ $pahani['upload'] ? 'on' : 'off' }}">📤 Upload: {{ $pahani['upload'] }} mandal(s)</span>
            <span class="perm {{ $pahani['view'] ? 'on' : 'off' }}">👁 View: {{ $pahani['view'] }}</span>
            <span class="perm {{ $pahani['edit'] ? 'on' : 'off' }}">✎ Edit: {{ $pahani['edit'] }}</span>
          </div>
          @if(!$pahani['upload'])
            <div class="card-warn">⚠ No mandals assigned for Pahani upload. Contact the administrator.</div>
          @endif
        </div>
        <div class="card-actions">
          <a class="btn-go" href="{{ route('pahani.index') }}">Go to Pahani Upload →</a>
          <a class="link-sec" href="{{ route('pahani.view') }}">📋 View Records</a>
        </div>
      </div>

      {{-- ═══ CARD 2: BHU BHARATHI ═══ --}}
      <div class="dash-card bb">
        <a class="card-top" href="{{ route('bhu-bharathi.index') }}">
          <div class="card-icon">🗂️</div>
          <div>
            <div class="card-step">Module 2</div>
            <div class="card-title">Bhu Bharathi Document Upload</div>
          </div>
        </a>
        <div class="card-body">
          <div class="card-desc">
            Upload Bhu Bharathi disposals — select Module, enter Application Number and attach the PDF. Multiple disposals upload in parallel.
          </div>
          <div class="perm-row">
            <span class="perm {{ $bhuBharathi['upload'] ? 'on' : 'off' }}">📤 Upload: {{ $bhuBharathi['upload'] }} mandal(s)</span>
            <span class="perm {{ $bhuBharathi['view'] ? 'on' : 'off' }}">👁 View: {{ $bhuBharathi['view'] }}</span>
            <span class="perm {{ $bhuBharathi['edit'] ? 'on' : 'off' }}">✎ Edit: {{ $bhuBharathi['edit'] }}</span>
          </div>
          @if(!$bhuBharathi['upload'])
            <div class="card-warn">⚠ No mandals assigned for Bhu Bharathi upload. Contact the administrator.</div>
          @endif
        </div>
        <div class="card-actions">
          <a class="btn-go" href="{{ route('bhu-bharathi.index') }}">Go to Bhu Bharathi Upload →</a>
        </div>
      </div>

    </div>
  </div>

  <div class="portal-footer">
    &copy; {{ date('Y') }} Pahani Management System. All rights reserved.
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmLogout() {
  Swal.fire({
    title: 'Logout?', text: 'Are you sure you want to logout?', icon: 'warning',
    showCancelButton: true, confirmButtonColor: '#d33', cancelButtonColor: '#6c757d',
    confirmButtonText: 'Yes, Logout', cancelButtonText: 'Cancel'
  }).then(r => { if (r.isConfirmed) document.getElementById('logoutForm').submit(); });
}
</script>
</body>
</html>