<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Edit User — {{ $user->name }} — Admin Dashboard</title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:Arial,sans-serif;font-size:12px;background:#f0f4f8;color:#1a1a2e}
    a{color:inherit}

    /* ══ APP SHELL ══ */
    .app-shell{display:flex;min-height:100vh}

    /* ══ SIDEBAR ══ */
    .sidebar{width:230px;background:#154360;color:#d6eaf8;flex-shrink:0;display:flex;flex-direction:column;transition:margin-left 0.25s ease;position:relative;z-index:200}
    .sidebar-brand{display:flex;align-items:center;gap:10px;padding:16px 18px;border-bottom:1px solid rgba(255,255,255,0.12)}
    .sidebar-brand .emblem{width:34px;height:34px;background:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;border:2px solid #f39c12;flex-shrink:0}
    .sidebar-brand .brand-text{line-height:1.2}
    .sidebar-brand .brand-title{font-size:12px;font-weight:bold;color:white;text-transform:uppercase;letter-spacing:0.4px}
    .sidebar-brand .brand-sub{font-size:9px;color:#8fb6d3}

    .sidebar-nav{flex:1;padding:12px 0;overflow-y:auto}
    .nav-section-label{font-size:9px;text-transform:uppercase;letter-spacing:0.6px;color:#6f97b8;padding:10px 18px 6px}
    .sb-item{display:flex;align-items:center;gap:10px;padding:10px 18px;font-size:11px;color:#c7dcec;cursor:pointer;text-decoration:none;border-left:3px solid transparent;transition:background 0.15s,border-color 0.15s}
    .sb-item .sb-icon{font-size:14px;width:18px;text-align:center;flex-shrink:0}
    .sb-item:hover{background:rgba(255,255,255,0.06);color:white}
    .sb-item.active{background:rgba(243,156,18,0.14);border-left-color:#f39c12;color:white;font-weight:bold}

    .sidebar-footer{padding:12px 18px;border-top:1px solid rgba(255,255,255,0.12);font-size:9px;color:#6f97b8}

    /* ══ MAIN COLUMN ══ */
    .main-col{flex:1;display:flex;flex-direction:column;min-width:0}

    /* ══ TOPBAR ══ */
    .topbar{background:white;border-bottom:1px solid #d5e8f5;padding:0 18px;height:56px;display:flex;align-items:center;justify-content:space-between;position:sticky;top:0;z-index:150}
    .topbar-left{display:flex;align-items:center;gap:14px}
    .hamburger{display:none;background:none;border:none;cursor:pointer;font-size:18px;color:#154360;padding:4px}
    .topbar-title{font-size:13px;font-weight:bold;color:#154360;text-transform:uppercase;letter-spacing:0.4px}
    .topbar-breadcrumb{font-size:10px;color:#888}

    .topbar-right{display:flex;align-items:center;gap:16px}
    .icon-btn{background:none;border:none;cursor:pointer;font-size:15px;color:#607d8b;position:relative;padding:4px}

    /* profile dropdown */
    .profile-wrap{position:relative}
    .profile-trigger{display:flex;align-items:center;gap:8px;cursor:pointer;padding:5px 8px;border-radius:4px;transition:background 0.15s}
    .profile-trigger:hover{background:#f0f4f8}
    .avatar{width:30px;height:30px;border-radius:50%;background:#154360;color:white;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:bold;flex-shrink:0}
    .profile-meta{line-height:1.2;text-align:left}
    .profile-name{font-size:11px;font-weight:bold;color:#1a1a2e}
    .profile-role{font-size:9px;color:#888}
    .caret{font-size:9px;color:#999;transition:transform 0.15s}
    .profile-wrap.open .caret{transform:rotate(180deg)}

    .profile-menu{position:absolute;top:calc(100% + 8px);right:0;background:white;border:1px solid #d5e8f5;border-radius:4px;box-shadow:0 6px 18px rgba(0,0,0,0.12);min-width:180px;display:none;overflow:hidden;z-index:300}
    .profile-wrap.open .profile-menu{display:block}
    .profile-menu-item{display:flex;align-items:center;gap:9px;padding:9px 14px;font-size:11px;color:#333;cursor:pointer;text-decoration:none;transition:background 0.15s}
    .profile-menu-item:hover{background:#eaf2f8}
    .profile-menu-item.danger{color:#c0392b}
    .profile-menu-item.danger:hover{background:#fdecea}

    /* ══ CONTENT ══ */
    .content{flex:1;padding:18px 20px;max-width:1100px;width:100%;margin:0 auto}
    .page-heading{background:white;border:1px solid #d5e8f5;border-left:4px solid #154360;padding:10px 16px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px}
    .page-heading h2{font-size:13px;font-weight:bold;color:#154360;text-transform:uppercase;letter-spacing:0.5px}
    .page-heading .ph-sub{font-size:10px;color:#666}

    .toolbar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:12px}
    .search-box{display:flex;align-items:center;gap:6px;background:white;border:1px solid #b0c4d8;border-radius:2px;padding:6px 10px;min-width:220px}
    .search-box input{border:none;outline:none;font-size:11px;flex:1;background:transparent}

    .section-card{background:white;border:1px solid #d0dde8;border-radius:2px;overflow:hidden}
    .data-table{width:100%;border-collapse:collapse;font-size:11px}
    .data-table th{background:#eaf2f8;border:1px solid #c8dce9;padding:8px 10px;text-align:left;font-size:10px;font-weight:bold;text-transform:uppercase;color:#154360;letter-spacing:0.3px;white-space:nowrap}
    .data-table td{border:1px solid #dce8f0;padding:8px 10px;vertical-align:middle}
    .data-table tr:nth-child(even) td{background:#f7fbfd}
    .data-table tr:hover td{background:#edf6ff}

    .pill{display:inline-flex;align-items:center;gap:5px;font-size:10px;font-weight:bold;padding:3px 9px;border-radius:12px}
    .pill-active{background:#e8f5e9;color:#1b5e20;border:1px solid #a5d6a7}
    .pill-inactive{background:#f3f3f3;color:#777;border:1px solid #ddd}

    .row-actions{display:flex;gap:6px}
    .btn-icon{border:none;background:#eaf2f8;color:#154360;cursor:pointer;width:26px;height:26px;border-radius:3px;font-size:11px;display:flex;align-items:center;justify-content:center;transition:background 0.15s}
    .btn-icon:hover{background:#d6eaf8}

    .empty-state{text-align:center;padding:36px 10px;color:#888;font-size:11px}
    .es-icon{font-size:32px;margin-bottom:8px}

    .loading-bar{display:none;align-items:center;gap:10px;padding:16px;background:#f0f8ff;border:1px solid #b8d4e8;color:#154360;font-size:11px}
    .loading-bar.show{display:flex}
    .spinner{display:inline-block;width:12px;height:12px;border:2px solid #b8d4e8;border-top-color:#154360;border-radius:50%;animation:spin 0.6s linear infinite}
    @keyframes spin{to{transform:rotate(360deg)}}

    .pagination-container{display:none;align-items:center;justify-content:space-between;padding:12px 16px;border-top:1px solid #dce8f0;background:#fafbfc;font-size:10px}
    .pagination-container.show{display:flex}
    .pagination-info{color:#666}
    .pagination-nav{display:flex;gap:4px}
    .pagination-nav button{border:1px solid #b0c4d8;background:white;color:#154360;padding:4px 8px;border-radius:2px;cursor:pointer;font-size:10px;font-weight:bold;transition:background 0.15s}
    .pagination-nav button:hover{background:#eaf2f8}
    .pagination-nav button.active{background:#154360;color:white}
    .pagination-nav button:disabled{opacity:0.5;cursor:not-allowed}

    .toast{position:fixed;bottom:16px;right:16px;background:#154360;color:white;padding:12px 16px;border-radius:3px;font-size:11px;box-shadow:0 4px 12px rgba(0,0,0,0.15);display:none;z-index:9999;border-left:4px solid #f39c12;max-width:300px}
    .toast.show{display:block;animation:slideIn 0.3s ease}
    @keyframes slideIn{from{transform:translateX(120%)}}


    /* ══ EDIT PAGE ══ */
    .content{max-width:1180px}
    .back-link{display:inline-flex;align-items:center;gap:6px;font-size:11px;font-weight:bold;color:#154360;text-decoration:none;margin-bottom:10px}
    .back-link:hover{text-decoration:underline}

    .user-hero{background:white;border:1px solid #d0dde8;border-left:4px solid #154360;padding:14px 16px;margin-bottom:14px;display:flex;align-items:center;gap:14px;flex-wrap:wrap}
    .user-hero .avatar-lg{width:46px;height:46px;border-radius:50%;background:#154360;color:white;display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:bold;border:2px solid #f39c12;flex-shrink:0}
    .user-hero .uh-name{font-size:14px;font-weight:bold;color:#154360}
    .user-hero .uh-meta{font-size:10px;color:#777;margin-top:2px}
    .user-hero .uh-right{margin-left:auto}

    .card{background:white;border:1px solid #d0dde8;border-radius:2px;margin-bottom:14px;overflow:hidden}
    .card-header{background:#154360;color:white;padding:9px 14px;font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;display:flex;align-items:center;gap:8px}
    .card-header .num{background:#f39c12;color:#1a1a2e;width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;flex-shrink:0}
    .card-header .ch-right{margin-left:auto;font-size:10px;font-weight:normal;text-transform:none;color:#aed6f1}
    .card-body{padding:16px}

    .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:4px 18px}
    .form-field{margin-bottom:12px;display:flex;flex-direction:column;gap:4px}
    .form-field label{font-size:10px;font-weight:bold;color:#154360;text-transform:uppercase;letter-spacing:0.3px}
    .form-field label .req{color:#c0392b}
    .form-field input,.form-field select{padding:8px;border:1px solid #b0c4d8;border-radius:2px;font-size:11px;background:#f8fbfd;width:100%}
    .form-field input:focus,.form-field select:focus{outline:none;border-color:#154360;background:#fff;box-shadow:0 0 0 2px rgba(21,67,96,0.1)}
    .form-field.has-error input,.form-field.has-error select{border-color:#c0392b;background:#fef5f5}
    .field-error{color:#c0392b;font-size:10px;display:none}
    .form-field.has-error .field-error{display:block}
    .field-hint{font-size:9px;color:#888}
    .pw-wrap{position:relative}
    .pw-wrap input{padding-right:34px}
    .pw-toggle{position:absolute;right:8px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;font-size:12px}

    .alert{padding:9px 12px;border-radius:2px;font-size:11px;margin-bottom:12px;border-left:4px solid}
    .alert-error{background:#fdecea;border-color:#c0392b;color:#7f0000}

    /* permission matrix */
    .matrix-tools{display:flex;align-items:center;gap:10px;flex-wrap:wrap;margin-bottom:10px}
    .matrix-tools .search-box{min-width:240px}
    .matrix-tools .mt-note{font-size:10px;color:#888}
    .matrix-wrap{overflow:auto;max-height:560px;border:1px solid #d0dde8}
    .matrix{width:100%;border-collapse:separate;border-spacing:0;font-size:11px}
    .matrix th,.matrix td{border-right:1px solid #dce8f0;border-bottom:1px solid #dce8f0;padding:7px 8px;text-align:center;vertical-align:middle;background:white}
    .matrix thead th{position:sticky;z-index:2;font-size:10px;text-transform:uppercase;letter-spacing:0.3px;white-space:nowrap}
    .matrix thead tr.grp th{top:0;color:white;font-weight:bold}
    .matrix thead tr.grp th.pahani{background:#154360}
    .matrix thead tr.grp th.bb{background:#1e6b52}
    .matrix thead tr.grp th.blank{background:#eaf2f8}
    .matrix thead tr.cols th{top:31px;background:#eaf2f8;color:#154360;font-weight:bold}
    .matrix thead tr.cols th.bbcol{background:#e8f5e9;color:#145a45}
    .matrix th.mandal-col,.matrix td.mandal-col{text-align:left;position:sticky;left:0;z-index:1;min-width:170px}
    .matrix thead th.mandal-col{z-index:3}
    .matrix tbody tr:hover td{background:#f2f8fd}
    .matrix td.sep,.matrix th.sep{border-left:2px solid #9fbfd6}
    .matrix input[type=checkbox]{width:16px;height:16px;cursor:pointer;accent-color:#154360}
    .matrix .bbcell input[type=checkbox]{accent-color:#1e6b52}
    .col-count{display:block;font-size:9px;font-weight:normal;color:#777;text-transform:none;margin-top:2px}
    .col-head{display:flex;flex-direction:column;align-items:center;gap:3px}
    .inactive-tag{font-size:9px;color:#999;margin-left:4px}
    .matrix tr.hidden-row{display:none}

    /* module upload permission list */
    .mod-table{width:100%;border-collapse:separate;border-spacing:0;font-size:11px}
    .mod-table th,.mod-table td{border-bottom:1px solid #dce8f0;padding:8px 10px;vertical-align:middle;background:white}
    .mod-table thead th{position:sticky;top:0;z-index:2;background:#e8f5e9;color:#145a45;font-size:10px;text-transform:uppercase;letter-spacing:0.3px;text-align:left;white-space:nowrap}
    .mod-table th.c,.mod-table td.c{text-align:center;width:150px}
    .mod-table tbody tr:hover td{background:#f4faf6}
    .mod-table input[type=checkbox]{width:16px;height:16px;cursor:pointer;accent-color:#1e6b52}
    .mod-table tr.hidden-row{display:none}
    .mod-table tr.is-inactive td{color:#999}
    .perm-note{font-size:10px;color:#555;background:#f4faf6;border:1px solid #cfe8d8;border-left:3px solid #1e6b52;padding:8px 10px;margin-bottom:10px;line-height:1.5}

    /* sticky save bar */
    .save-bar{position:sticky;bottom:0;background:white;border-top:2px solid #f39c12;box-shadow:0 -4px 12px rgba(0,0,0,0.06);padding:10px 16px;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;z-index:100;margin:0 -20px -18px}
    .dirty-note{font-size:10px;color:#b9770e;font-weight:bold;display:none}
    .dirty-note.show{display:inline}
    .btn-secondary-sm{background:#f0f4f8;color:#154360;border:1px solid #b0c4d8;padding:8px 16px;font-size:11px;font-weight:bold;cursor:pointer;border-radius:2px;text-decoration:none;display:inline-block}
    .btn-secondary-sm:hover{background:#eaf2f8}
    .btn-primary-sm{background:#154360;color:white;border:none;padding:8px 20px;font-size:11px;font-weight:bold;cursor:pointer;border-radius:2px}
    .btn-primary-sm:hover{background:#1a6fa8}
    .btn-primary-sm:disabled{background:#aaa;cursor:not-allowed}

    .page-loading{display:flex;align-items:center;gap:10px;padding:16px;background:#f0f8ff;border:1px solid #b8d4e8;color:#154360;font-size:11px;margin-bottom:14px}

    @media(max-width:760px){
      .grid-2{grid-template-columns:1fr}
      .user-hero .uh-right{margin-left:0}
    }
  </style>
</head>
<body>

  <div class="app-shell">

    {{-- ══════════════ SIDEBAR ══════════════ --}}
    <aside class="sidebar" id="sidebar">
      <div class="sidebar-brand">
        <div class="emblem">⚖️</div>
        <div class="brand-text">
          <div class="brand-title">Admin Panel</div>
          <div class="brand-sub">Pahani Digitization System</div>
        </div>
      </div>

      <nav class="sidebar-nav">
        <div class="nav-section-label">Master Data</div>
        <a class="sb-item" href="{{ route('admin.mandals.index') }}">
          <span class="sb-icon">🏛️</span> Mandal
        </a>
        <a class="sb-item" href="{{ route('admin.villages.index') }}">
          <span class="sb-icon">🏘️</span> Village
        </a>
        <a class="sb-item" href="{{ route('admin.working-offices.index') }}">
          <span class="sb-icon">🏢</span> Working Office
        </a>
        <a class="sb-item" href="{{ route('admin.modules.index') }}">
          <span class="sb-icon">🧩</span> Modules
        </a>

        <div class="nav-section-label">Access Control</div>
        <a class="sb-item" href="{{ route('admin.pahani-management.index') }}">
          <span class="sb-icon">👤</span> Pahani Management
        </a>
        <a class="sb-item" href="{{ route('admin.bhu-bharathi-management.index') }}">
          <span class="sb-icon">👤</span> Bhu Bharathi Management
        </a>
        <a class="sb-item" href="{{ route('reports.admin') }}">
          <span class="sb-icon">👤</span> Summary 
        </a>
        <a class="sb-item active" href="{{ route('admin.users.index') }}">
          <span class="sb-icon">👤</span> Users List
        </a>
      </nav>

      <div class="sidebar-footer">
        v1.0 &nbsp;·&nbsp; © {{ date('Y') }} Revenue Dept.
      </div>
    </aside>

    {{-- ══════════════ MAIN COLUMN ══════════════ --}}
    <div class="main-col">

      {{-- ── TOPBAR ── --}}
      <div class="topbar">
        <div class="topbar-left">
          <button class="hamburger" onclick="openSidebar()">☰</button>
          <div>
            <div class="topbar-title">✎ Edit User</div>
            <div class="topbar-breadcrumb">Admin › Access Control › <a href="{{ route('admin.users.index') }}" style="color:#1a6fa8">Users</a> › {{ $user->name }}</div>
          </div>
        </div>

        <div class="topbar-right">
          <div class="profile-wrap" id="profile-wrap">
            <div class="profile-trigger" onclick="toggleProfileMenu(event)">
              <div class="avatar">{{ strtoupper(substr(auth()->user()->name ?? 'A', 0, 1)) }}</div>
              <div class="profile-meta">
                <div class="profile-name">{{ auth()->user()->name ?? 'Admin User' }}</div>
                <div class="profile-role">{{ auth()->user()->role ?? 'Administrator' }}</div>
              </div>
              <span class="caret">▾</span>
            </div>

            <div class="profile-menu">
              <a class="profile-menu-item danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <span>🚪</span> Logout
              </a>
            </div>
          </div>
        </div>
      </div>

      {{-- Hidden logout form --}}
      <form id="logout-form" action="{{ route('logout') ?? '#' }}" method="POST" style="display:none">
        @csrf
      </form>

      {{-- ── CONTENT ── --}}
      <div class="content">

        <a class="back-link" href="{{ route('admin.users.index') }}">← Back to Users List</a>

        <div class="user-hero">
          <div class="avatar-lg">{{ strtoupper(mb_substr($user->name ?? 'U', 0, 1)) }}</div>
          <div>
            <div class="uh-name" id="hero-name">{{ $user->name }}</div>
            <div class="uh-meta">
              {{ $user->email }} &nbsp;·&nbsp; User ID #{{ $user->id }}
              @if($user->created_at) &nbsp;·&nbsp; Joined {{ $user->created_at->format('d-M-Y') }} @endif
            </div>
          </div>
          <div class="uh-right" id="hero-status"></div>
        </div>

        <div class="page-loading" id="page-loading"><div class="spinner"></div> Loading user details…</div>
        <div id="form-errors"></div>

        <form id="user-edit-form" onsubmit="submitEditUserForm(event)" autocomplete="off" style="display:none">
          <input type="hidden" id="user-edit-id" value="{{ $user->id }}">

          {{-- ═══ 1. ACCOUNT DETAILS ═══ --}}
          <div class="card">
            <div class="card-header"><span class="num">1</span> Account Details</div>
            <div class="card-body">
              <div class="grid-2">
                <div class="form-field" id="user-edit-name-field">
                  <label for="user-edit-name-input">Name <span class="req">*</span></label>
                  <input type="text" id="user-edit-name-input" maxlength="255" required>
                  <div class="field-error" id="user-edit-name-error"></div>
                </div>
                <div class="form-field" id="user-edit-email-field">
                  <label for="user-edit-email-input">Email <span class="req">*</span></label>
                  <input type="email" id="user-edit-email-input" maxlength="255" required>
                  <div class="field-error" id="user-edit-email-error"></div>
                </div>
                <div class="form-field" id="user-edit-office-field">
                  <label for="user-edit-office-select">Working Office <span class="req">*</span></label>
                  <select id="user-edit-office-select" required></select>
                  <div class="field-error" id="user-edit-office-error"></div>
                </div>
                <div class="form-field" id="user-edit-status-field">
                  <label for="user-edit-status-select">Status</label>
                  <select id="user-edit-status-select">
                    <option value="1">✔ Active</option>
                    <option value="0">— Inactive</option>
                  </select>
                  <div class="field-hint">Inactive users cannot log in.</div>
                </div>
              </div>
            </div>
          </div>

          {{-- ═══ 2. PASSWORD ═══ --}}
          <div class="card">
            <div class="card-header"><span class="num">2</span> Change Password <span class="ch-right">Optional — leave blank to keep the current password</span></div>
            <div class="card-body">
              <div class="grid-2">
                <div class="form-field" id="user-edit-password-field">
                  <label for="user-edit-password-input">New Password</label>
                  <div class="pw-wrap">
                    <input type="password" id="user-edit-password-input" minlength="8" autocomplete="new-password" placeholder="Minimum 8 characters">
                    <button type="button" class="pw-toggle" onclick="togglePw('user-edit-password-input', this)">👁️</button>
                  </div>
                  <div class="field-error" id="user-edit-password-error"></div>
                </div>
                <div class="form-field" id="user-edit-password2-field">
                  <label for="user-edit-password2-input">Confirm New Password</label>
                  <div class="pw-wrap">
                    <input type="password" id="user-edit-password2-input" minlength="8" autocomplete="new-password" placeholder="Re-enter new password">
                    <button type="button" class="pw-toggle" onclick="togglePw('user-edit-password2-input', this)">👁️</button>
                  </div>
                  <div class="field-error" id="user-edit-password2-error"></div>
                </div>
              </div>
            </div>
          </div>

          {{-- ═══ 3. MANDAL PERMISSIONS MATRIX ═══ --}}
          <div class="card">
            <div class="card-header"><span class="num">3</span> Mandal Permissions <span class="ch-right">Tick a column header to select / clear the whole column</span></div>
            <div class="card-body">
              <div class="matrix-tools">
                <div class="search-box">🔎 <input type="text" id="mandal-search" placeholder="Filter mandals…" oninput="filterMandalRows()"></div>
                <span class="mt-note">Filtering only hides rows — ticks on hidden rows are still saved.</span>
              </div>
              <div class="matrix-wrap">
                <table class="matrix" id="perm-matrix">
                  <thead>
                    <tr class="grp">
                      <th class="mandal-col blank"></th>
                      <th class="pahani sep" colspan="3">📑 Pahani Uploads</th>
                      <th class="bb sep" colspan="3">🗂️ Bhu Bharathi Disposals</th>
                      <th class="blank sep"></th>
                    </tr>
                    <tr class="cols" id="matrix-cols"></tr>
                  </thead>
                  <tbody id="matrix-body">
                    <tr><td colspan="8" style="padding:16px;color:#999">Loading mandals…</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- ═══ 4. BHU BHARATHI MODULE UPLOAD PERMISSIONS ═══ --}}
          <div class="card">
            <div class="card-header" style="background:#1e6b52"><span class="num">4</span> Bhu Bharathi — Module Permissions <span class="ch-right" style="color:#cfe8d8">Modules this user may upload</span></div>
            <div class="card-body">
              <div class="perm-note">
                Only the ticked modules appear in this user's <strong>Module</strong> dropdown on the Bhu Bharathi Disposals page.
                This works together with the Bhu Bharathi <strong>Upload</strong> mandals above — the user needs both.
              </div>
              <div class="matrix-tools">
                <div class="search-box">🔎 <input type="text" id="module-search" placeholder="Filter modules…" oninput="filterModuleRows()"></div>
                <span class="mt-note">Inactive modules are listed so existing permissions are kept, but users never see them in the dropdown.</span>
              </div>
              <div class="matrix-wrap" style="max-height:420px">
                <table class="mod-table" id="module-table">
                  <thead>
                    <tr>
                      <th>Module</th>
                      <th class="c">
                        <label class="col-head" title="Select / clear all (visible) modules">
                          <input type="checkbox" id="module-upload-toggle" onchange="toggleAllModules(this.checked)">
                          📤 Upload
                          <span class="col-count" id="module-upload-count">0 selected</span>
                        </label>
                      </th>
                    </tr>
                  </thead>
                  <tbody id="module-body">
                    <tr><td colspan="2" style="padding:16px;color:#999">Loading modules…</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          {{-- ═══ SAVE BAR ═══ --}}
          <div class="save-bar">
            <span class="dirty-note" id="dirty-note">● You have unsaved changes</span>
            <span></span>
            <div style="display:flex;gap:8px">
              <a href="{{ route('admin.users.index') }}" class="btn-secondary-sm" onclick="return confirmLeave()">Cancel</a>
              <button type="submit" class="btn-primary-sm" id="user-edit-submit-btn">💾 Save Changes</button>
            </div>
          </div>
        </form>

      </div>
    </div>
  </div>

  <div id="toast" class="toast"></div>

<script>
/* ══════════════════════════════════════════════════════════════════
   CONFIG
   Permission keys match AdminController::updateUser():
   Pahani: upload / view / edit   →  upload_mandal_ids ...
   Bhu Bharathi: bb_upload / bb_view / bb_edit → bb_upload_mandal_ids ...
══════════════════════════════════════════════════════════════════ */
const USER_ID = {{ (int) $user->id }};
const LIST_URL = @json(route('admin.users.index'));

const PERM_COLUMNS = [
  { key: 'upload',    label: '📤 Upload', bb: false },
  { key: 'view',      label: '👁 View',   bb: false },
  { key: 'edit',      label: '✎ Edit',   bb: false },
  { key: 'bb_upload', label: '📤 Upload', bb: true  },
  { key: 'bb_view',   label: '👁 View',   bb: true  },
  { key: 'bb_edit',   label: '✎ Edit',   bb: true  },
];

let initialSnapshot = null;
let saving = false;

document.addEventListener('DOMContentLoaded', loadPage);

/* ══════════════════════════════════════════════════════════════════
   LOAD: user + offices + mandals in parallel
══════════════════════════════════════════════════════════════════ */
async function loadPage() {
  const getJson = url => fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status} for ${url}`); return r.json(); });

  try {
    const [userRes, officesRes, mandalsRes, modulesRes] = await Promise.all([
      getJson(`/api/admin/users/${USER_ID}`),
      getJson('/api/admin/working-offices'),
      getJson('/api/admin/mandals'),
      getJson('/api/admin/modules'),
    ]);

    if (!userRes.success || !userRes.user) throw new Error('User not found.');
    const user    = userRes.user;
    const offices = Array.isArray(officesRes) ? officesRes : (officesRes.data || []);
    const mandals = Array.isArray(mandalsRes) ? mandalsRes : (mandalsRes.data || []);
    const modules = Array.isArray(modulesRes) ? modulesRes : (modulesRes.data || []);

    // Account details
    document.getElementById('user-edit-name-input').value  = user.name || '';
    document.getElementById('user-edit-email-input').value = user.email || '';
    document.getElementById('user-edit-status-select').value = Number(user.status) ? '1' : '0';
    fillOffices(offices, user.working_office_id);
    renderHeroStatus(Number(user.status));

    // Permissions
    const p = user.permissions || {};
    const selected = {};
    PERM_COLUMNS.forEach(c => selected[c.key] = normalizeMandalIds(p[`${c.key}_mandal_ids`]));
    renderMatrix(mandals, selected);
    renderModules(modules, normalizeMandalIds(p.bb_upload_module_ids));

    document.getElementById('page-loading').style.display = 'none';
    document.getElementById('user-edit-form').style.display = 'block';

    initialSnapshot = snapshot();
    document.getElementById('user-edit-form').addEventListener('input', updateDirty);
    document.getElementById('user-edit-form').addEventListener('change', updateDirty);
  } catch (err) {
    console.error(err);
    document.getElementById('page-loading').innerHTML =
      `<span style="color:#c0392b">✘ Failed to load user details. <a href="" style="color:#154360;font-weight:bold">Reload</a> or go back to the <a href="${LIST_URL}" style="color:#154360;font-weight:bold">Users List</a>.</span>`;
  }
}

function fillOffices(offices, selectedId) {
  const sel = document.getElementById('user-edit-office-select');
  sel.innerHTML = '<option value="">— Select Office —</option>';
  offices.forEach(o => {
    const opt = document.createElement('option');
    opt.value = o.id;
    opt.textContent = o.name;
    sel.appendChild(opt);
  });
  if (selectedId !== null && selectedId !== undefined) sel.value = String(selectedId);
}

function renderHeroStatus(active) {
  document.getElementById('hero-status').innerHTML = active
    ? '<span class="pill pill-active">✔ Active</span>'
    : '<span class="pill pill-inactive">— Inactive</span>';
}

/* ══════════════════════════════════════════════════════════════════
   PERMISSION MATRIX  (rows = mandals, columns = 6 permission types)
   Checkbox class "mandal-<key>" so getCheckedMandals() works as before.
══════════════════════════════════════════════════════════════════ */
function renderMatrix(mandals, selected) {
  const cols = document.getElementById('matrix-cols');
  cols.innerHTML = '<th class="mandal-col">Mandal</th>' + PERM_COLUMNS.map((c, i) => `
    <th class="${c.bb ? 'bbcol' : ''} ${i === 0 || i === 3 ? 'sep' : ''}">
      <label class="col-head" title="Select / clear whole column">
        <input type="checkbox" class="col-toggle" data-col="${c.key}" onchange="toggleColumn('${c.key}', this.checked)">
        ${c.label}
        <span class="col-count" id="count-${c.key}">0</span>
      </label>
    </th>`).join('') + '<th class="sep">All</th>';

  const body = document.getElementById('matrix-body');
  if (!mandals.length) {
    body.innerHTML = '<tr><td colspan="8" style="padding:16px;color:#999">No mandals available.</td></tr>';
    return;
  }

  const sets = {};
  PERM_COLUMNS.forEach(c => sets[c.key] = new Set(selected[c.key] || []));

  body.innerHTML = mandals.map(m => {
    const id = parseInt(m.id, 10);
    const inactive = (m.is_active === false || m.is_active === 0 || m.is_active === '0');
    return `
      <tr data-name="${escapeHtml(String(m.name).toLowerCase())}">
        <td class="mandal-col">${escapeHtml(m.name)}${inactive ? '<span class="inactive-tag">(inactive)</span>' : ''}</td>
        ${PERM_COLUMNS.map((c, i) => `
          <td class="${c.bb ? 'bbcell' : ''} ${i === 0 || i === 3 ? 'sep' : ''}">
            <input type="checkbox" class="mandal-${c.key}" value="${id}" data-row="${id}"
                   title="${c.bb ? 'Bhu Bharathi' : 'Pahani'} ${c.key.replace('bb_', '')} — ${escapeHtml(m.name)}"
                   ${sets[c.key].has(id) ? 'checked' : ''} onchange="refreshCounts()">
          </td>`).join('')}
        <td class="sep">
          <input type="checkbox" class="row-toggle" data-row="${id}" title="All permissions for ${escapeHtml(m.name)}"
                 onchange="toggleRow(${id}, this.checked)">
        </td>
      </tr>`;
  }).join('');

  refreshCounts();
}

function toggleColumn(key, checked) {
  // Only affects visible rows, so admins can filter → select all → clear filter
  document.querySelectorAll(`#matrix-body tr:not(.hidden-row) input.mandal-${key}`).forEach(cb => cb.checked = checked);
  refreshCounts();
}

function toggleRow(id, checked) {
  document.querySelectorAll(`#matrix-body input[data-row="${id}"]:not(.row-toggle)`).forEach(cb => cb.checked = checked);
  refreshCounts();
}

function refreshCounts() {
  PERM_COLUMNS.forEach(c => {
    const all = document.querySelectorAll(`#matrix-body input.mandal-${c.key}`);
    const visible = document.querySelectorAll(`#matrix-body tr:not(.hidden-row) input.mandal-${c.key}`);
    const checked = Array.from(all).filter(cb => cb.checked).length;
    const visChecked = Array.from(visible).filter(cb => cb.checked).length;
    document.getElementById(`count-${c.key}`).textContent = `${checked} selected`;

    const head = document.querySelector(`.col-toggle[data-col="${c.key}"]`);
    head.checked = visible.length > 0 && visChecked === visible.length;
    head.indeterminate = visChecked > 0 && visChecked < visible.length;
  });

  document.querySelectorAll('#matrix-body .row-toggle').forEach(rt => {
    const boxes = Array.from(document.querySelectorAll(`#matrix-body input[data-row="${rt.dataset.row}"]:not(.row-toggle)`));
    const n = boxes.filter(b => b.checked).length;
    rt.checked = n === boxes.length;
    rt.indeterminate = n > 0 && n < boxes.length;
  });
  updateDirty();
}

function filterMandalRows() {
  const q = document.getElementById('mandal-search').value.trim().toLowerCase();
  document.querySelectorAll('#matrix-body tr[data-name]').forEach(tr => {
    tr.classList.toggle('hidden-row', q !== '' && !tr.dataset.name.includes(q));
  });
  refreshCounts();
}

function getCheckedMandals(permissionType) {
  return Array.from(document.querySelectorAll(`#matrix-body input.mandal-${permissionType}:checked`))
    .map(cb => parseInt(cb.value, 10));
}

/* ══════════════════════════════════════════════════════════════════
   MODULE UPLOAD PERMISSIONS  →  bb_upload_module_ids
══════════════════════════════════════════════════════════════════ */
function renderModules(modules, selectedIds) {
  const body = document.getElementById('module-body');
  if (!modules.length) {
    body.innerHTML = '<tr><td colspan="2" style="padding:16px;color:#999">No modules yet. Add them under Admin → Modules.</td></tr>';
    refreshModuleCount();
    return;
  }

  const selected = new Set(selectedIds);
  body.innerHTML = modules.map(m => {
    const id = parseInt(m.id, 10);
    const inactive = (m.is_active === false || m.is_active === 0 || m.is_active === '0');
    return `
      <tr data-name="${escapeHtml(String(m.name).toLowerCase())}" class="${inactive ? 'is-inactive' : ''}">
        <td>
          <label for="module-upload-${id}" style="cursor:pointer">${escapeHtml(m.name)}</label>
          ${inactive ? '<span class="inactive-tag">(inactive)</span>' : ''}
        </td>
        <td class="c">
          <input type="checkbox" id="module-upload-${id}" class="module-upload" value="${id}"
                 ${selected.has(id) ? 'checked' : ''} onchange="refreshModuleCount()"
                 title="Allow uploading for ${escapeHtml(m.name)}">
        </td>
      </tr>`;
  }).join('');

  refreshModuleCount();
}

function toggleAllModules(checked) {
  document.querySelectorAll('#module-body tr:not(.hidden-row) input.module-upload').forEach(cb => cb.checked = checked);
  refreshModuleCount();
}

function refreshModuleCount() {
  const all     = Array.from(document.querySelectorAll('#module-body input.module-upload'));
  const visible = Array.from(document.querySelectorAll('#module-body tr:not(.hidden-row) input.module-upload'));
  const visChecked = visible.filter(cb => cb.checked).length;

  document.getElementById('module-upload-count').textContent = `${all.filter(cb => cb.checked).length} selected`;
  const head = document.getElementById('module-upload-toggle');
  head.checked = visible.length > 0 && visChecked === visible.length;
  head.indeterminate = visChecked > 0 && visChecked < visible.length;
  updateDirty();
}

function filterModuleRows() {
  const q = document.getElementById('module-search').value.trim().toLowerCase();
  document.querySelectorAll('#module-body tr[data-name]').forEach(tr => {
    tr.classList.toggle('hidden-row', q !== '' && !tr.dataset.name.includes(q));
  });
  refreshModuleCount();
}

function getCheckedModules() {
  return Array.from(document.querySelectorAll('#module-body input.module-upload:checked'))
    .map(cb => parseInt(cb.value, 10));
}

/* ══════════════════════════════════════════════════════════════════
   UNSAVED-CHANGES TRACKING
══════════════════════════════════════════════════════════════════ */
function snapshot() {
  const perms = {};
  PERM_COLUMNS.forEach(c => perms[c.key] = getCheckedMandals(c.key).sort((a, b) => a - b));
  perms.bb_upload_modules = getCheckedModules().sort((a, b) => a - b);
  return JSON.stringify({
    name:   document.getElementById('user-edit-name-input').value.trim(),
    email:  document.getElementById('user-edit-email-input').value.trim(),
    office: document.getElementById('user-edit-office-select').value,
    status: document.getElementById('user-edit-status-select').value,
    pw:     document.getElementById('user-edit-password-input').value !== '',
    perms,
  });
}

function isDirty() { return initialSnapshot !== null && snapshot() !== initialSnapshot; }
function updateDirty() { document.getElementById('dirty-note').classList.toggle('show', isDirty()); }

function confirmLeave() {
  return !isDirty() || confirm('You have unsaved changes. Leave this page without saving?');
}

window.addEventListener('beforeunload', e => {
  if (!saving && isDirty()) { e.preventDefault(); e.returnValue = ''; }
});

/* ══════════════════════════════════════════════════════════════════
   SUBMIT  →  PUT /api/admin/users/{id}   (AdminController::updateUser)
══════════════════════════════════════════════════════════════════ */
function submitEditUserForm(e) {
  e.preventDefault();
  clearAllErrors();

  const name     = document.getElementById('user-edit-name-input').value.trim();
  const email    = document.getElementById('user-edit-email-input').value.trim();
  const officeId = document.getElementById('user-edit-office-select').value;
  const status   = parseInt(document.getElementById('user-edit-status-select').value, 10);
  const pw       = document.getElementById('user-edit-password-input').value;
  const pw2      = document.getElementById('user-edit-password2-input').value;

  let hasError = false;
  if (!name)     { setFieldError('user-edit-name-field', 'user-edit-name-error', 'Name is required.'); hasError = true; }
  if (!email)    { setFieldError('user-edit-email-field', 'user-edit-email-error', 'Email is required.'); hasError = true; }
  if (!officeId) { setFieldError('user-edit-office-field', 'user-edit-office-error', 'Please select an office.'); hasError = true; }
  if (pw || pw2) {
    if (pw.length < 8) { setFieldError('user-edit-password-field', 'user-edit-password-error', 'Password must be at least 8 characters.'); hasError = true; }
    else if (pw !== pw2) { setFieldError('user-edit-password2-field', 'user-edit-password2-error', 'Passwords do not match.'); hasError = true; }
  }
  if (hasError) {
    document.querySelector('.form-field.has-error')?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    return;
  }

  const payload = {
    name,
    email,
    working_office_id: officeId,
    status,
    upload_mandal_ids:    getCheckedMandals('upload'),
    view_mandal_ids:      getCheckedMandals('view'),
    edit_mandal_ids:      getCheckedMandals('edit'),
    bb_upload_mandal_ids: getCheckedMandals('bb_upload'),
    bb_view_mandal_ids:   getCheckedMandals('bb_view'),
    bb_edit_mandal_ids:   getCheckedMandals('bb_edit'),
    bb_upload_module_ids: getCheckedModules(),
  };
  if (pw) {
    payload.password = pw;
    payload.password_confirmation = pw2;
  }

  const btn = document.getElementById('user-edit-submit-btn');
  btn.disabled = true;
  btn.textContent = 'Saving…';

  fetch(`/api/admin/users/${USER_ID}`, {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken(),
      'X-Requested-With': 'XMLHttpRequest',
    },
    body: JSON.stringify(payload),
  })
  .then(async r => {
    const data = await r.json().catch(() => ({}));

    if (r.status === 422) {
      const errs = data.errors || {};
      if (errs.name)              setFieldError('user-edit-name-field', 'user-edit-name-error', errs.name[0]);
      if (errs.email)             setFieldError('user-edit-email-field', 'user-edit-email-error', errs.email[0]);
      if (errs.working_office_id) setFieldError('user-edit-office-field', 'user-edit-office-error', errs.working_office_id[0]);
      if (errs.password)          setFieldError('user-edit-password-field', 'user-edit-password-error', errs.password[0]);

      // Anything else (e.g. mandal permission errors) → list at the top
      const shown = ['name', 'email', 'working_office_id', 'password'];
      const other = Object.entries(errs).filter(([k]) => !shown.includes(k)).flatMap(([, v]) => v);
      if (other.length) showFormErrors(other);
      if (!data.errors) showToast(data.message || 'Validation failed.', true);
      (document.querySelector('.form-field.has-error') || document.getElementById('form-errors'))
        .scrollIntoView({ behavior: 'smooth', block: 'center' });
      return;
    }

    if (!r.ok || !data.success) {
      showToast(data.message || 'Failed to update user.', true);
      return;
    }

    // Success — stay on the page with the saved state
    showToast('✔ User updated successfully.');
    document.getElementById('user-edit-password-input').value = '';
    document.getElementById('user-edit-password2-input').value = '';
    document.getElementById('hero-name').textContent = data.user?.name || name;
    renderHeroStatus(Number(data.user?.status ?? status));
    initialSnapshot = snapshot();
    updateDirty();
  })
  .catch(err => {
    console.error('Update error:', err);
    showToast('Network error. Please try again.', true);
  })
  .finally(() => {
    btn.disabled = false;
    btn.textContent = '💾 Save Changes';
  });
}

/* ══════════════════════════════════════════════════════════════════
   UTILITY FUNCTIONS
══════════════════════════════════════════════════════════════════ */
function normalizeMandalIds(input) {
  if (!Array.isArray(input)) return [];
  return input.map(id => parseInt(id, 10)).filter(id => !isNaN(id));
}

function csrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

function togglePw(id, btn) {
  const input = document.getElementById(id);
  const show = input.type === 'password';
  input.type = show ? 'text' : 'password';
  btn.textContent = show ? '🙈' : '👁️';
}

function clearAllErrors() {
  document.querySelectorAll('.form-field.has-error').forEach(f => f.classList.remove('has-error'));
  document.querySelectorAll('.field-error').forEach(e => e.textContent = '');
  document.getElementById('form-errors').innerHTML = '';
}

function showFormErrors(list) {
  document.getElementById('form-errors').innerHTML =
    `<div class="alert alert-error"><strong>Please fix the following:</strong>
      <ul style="margin-top:5px;padding-left:16px">${list.map(e => `<li>${escapeHtml(e)}</li>`).join('')}</ul></div>`;
}

function setFieldError(fieldId, errorId, message) {
  const field = document.getElementById(fieldId);
  if (field) {
    field.classList.add('has-error');
    const error = document.getElementById(errorId);
    if (error) error.textContent = message;
  }
}

function escapeHtml(str) {
  const d = document.createElement('div');
  d.textContent = str ?? '';
  return d.innerHTML;
}

function showToast(msg, isError = false) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.style.display = 'block';
  t.style.borderLeftColor = isError ? '#c0392b' : '#f39c12';
  t.style.background = isError ? '#7f0000' : '#154360';
  t.classList.add('show');
  clearTimeout(showToast._t);
  showToast._t = setTimeout(() => {
    t.classList.remove('show');
    t.style.display = 'none';
  }, 4500);
}

function toggleProfileMenu(event) {
  event.stopPropagation();
  document.getElementById('profile-wrap').classList.toggle('open');
}

document.addEventListener('click', () => {
  document.getElementById('profile-wrap').classList.remove('open');
});

function openSidebar() {
  document.getElementById('sidebar').style.marginLeft = '0';
}

function closeSidebar() {
  if (window.innerWidth < 768) {
    document.getElementById('sidebar').style.marginLeft = '-230px';
  }
}
</script>

</body>
</html>