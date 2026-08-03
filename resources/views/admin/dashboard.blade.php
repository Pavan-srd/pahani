<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Admin Dashboard — Land Record Digitalization</title>
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
    .sb-item .sb-count{margin-left:auto;background:rgba(255,255,255,0.12);color:#d6eaf8;font-size:9px;padding:1px 6px;border-radius:10px}

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
    .icon-btn .badge-dot{position:absolute;top:2px;right:2px;width:7px;height:7px;border-radius:50%;background:#c0392b;border:1.5px solid white}

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
    .profile-menu-header{padding:10px 14px;border-bottom:1px solid #eef3f7}
    .profile-menu-header .pm-name{font-size:11px;font-weight:bold;color:#1a1a2e}
    .profile-menu-header .pm-email{font-size:9px;color:#888;margin-top:1px}
    .profile-menu-item{display:flex;align-items:center;gap:9px;padding:9px 14px;font-size:11px;color:#333;cursor:pointer;text-decoration:none;transition:background 0.15s}
    .profile-menu-item:hover{background:#eaf2f8}
    .profile-menu-item.danger{color:#c0392b}
    .profile-menu-item.danger:hover{background:#fdecea}
    .profile-menu-divider{height:1px;background:#eef3f7;margin:4px 0}

    /* ══ CONTENT ══ */
    .content{flex:1;padding:18px 20px;max-width:1100px;width:100%;margin:0 auto}
    .page-heading{background:white;border:1px solid #d5e8f5;border-left:4px solid #154360;padding:10px 16px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px}
    .page-heading h2{font-size:13px;font-weight:bold;color:#154360;text-transform:uppercase;letter-spacing:0.5px}
    .page-heading .ph-sub{font-size:10px;color:#666}

    .toolbar{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px;margin-bottom:12px}
    .search-box{display:flex;align-items:center;gap:6px;background:white;border:1px solid #b0c4d8;border-radius:2px;padding:6px 10px;min-width:220px}
    .search-box input{border:none;outline:none;font-size:11px;flex:1;background:transparent}
    .btn-primary-sm{background:#154360;color:white;border:none;padding:8px 16px;font-size:11px;font-weight:bold;cursor:pointer;border-radius:2px;display:inline-flex;align-items:center;gap:6px;text-transform:uppercase;letter-spacing:0.3px;transition:background 0.15s}
    .btn-primary-sm:hover{background:#1a6fa8}

    .section-card{background:white;border:1px solid #d0dde8;border-radius:2px;overflow:hidden}
    .data-table{width:100%;border-collapse:collapse;font-size:11px}
    .data-table th{background:#eaf2f8;border:1px solid #c8dce9;padding:8px 10px;text-align:left;font-size:10px;font-weight:bold;text-transform:uppercase;color:#154360;letter-spacing:0.3px;white-space:nowrap}
    .data-table td{border:1px solid #dce8f0;padding:8px 10px;vertical-align:middle}
    .data-table tr:nth-child(even) td{background:#f7fbfd}
    .data-table tr:hover td{background:#edf6ff}

    .pill{display:inline-flex;align-items:center;gap:5px;font-size:10px;font-weight:bold;padding:3px 9px;border-radius:12px}
    .pill-active{background:#e8f5e9;color:#1b5e20;border:1px solid #a5d6a7}
    .pill-inactive{background:#f3f3f3;color:#777;border:1px solid #ddd}
    .role-pill{background:#eaf2f8;color:#154360;border:1px solid #b8d4e8;font-size:9px;font-weight:bold;padding:2px 8px;border-radius:10px;text-transform:uppercase}

    .row-actions{display:flex;gap:6px}
    .btn-icon{border:none;background:#eaf2f8;color:#154360;cursor:pointer;width:26px;height:26px;border-radius:3px;font-size:11px;display:flex;align-items:center;justify-content:center;transition:background 0.15s}
    .btn-icon:hover{background:#d6eaf8}
    .btn-icon.danger{background:#fdecea;color:#c0392b}
    .btn-icon.danger:hover{background:#f8d0cb}

    .empty-state{text-align:center;padding:36px 10px;color:#888;font-size:11px}
    .empty-state .es-icon{font-size:30px;margin-bottom:8px;opacity:0.5}
    .loading-bar{display:none;align-items:center;gap:8px;padding:18px 0;font-size:11px;color:#154360;justify-content:center}
    .loading-bar.show{display:flex}
    .spinner{width:14px;height:14px;border:2px solid #d0dde8;border-top-color:#154360;border-radius:50%;animation:spin 0.7s linear infinite}
    @keyframes spin{to{transform:rotate(360deg)}}

    .tab-panel{display:none}
    .tab-panel.active{display:block}

    /* ══ PAGINATION ══ */
    .pagination-container{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-top:1px solid #d0dde8;background:#f7fbfd;flex-wrap:wrap;gap:10px}
    .pagination-info{font-size:11px;color:#666}
    .pagination-nav{display:flex;align-items:center;gap:4px}
    .page-btn{background:#eaf2f8;color:#154360;border:1px solid #c8dce9;padding:5px 10px;font-size:10px;font-weight:bold;cursor:pointer;border-radius:2px;text-transform:uppercase;letter-spacing:0.3px;transition:background 0.15s}
    .page-btn:hover:not(:disabled){background:#d6eaf8}
    .page-btn:disabled{opacity:0.5;cursor:not-allowed}
    .page-btn.active{background:#154360;color:white;border-color:#154360}

    /* ══ OVERLAY (mobile sidebar) ══ */
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:190}
    .sidebar-overlay.show{display:block}

    /* ══ TOAST ══ */
    .toast{position:fixed;top:20px;right:20px;background:#154360;color:white;padding:10px 18px;border-left:4px solid #f39c12;font-size:12px;border-radius:2px;z-index:9999;display:none;box-shadow:0 4px 12px rgba(0,0,0,0.3);max-width:320px}

    /* ══ MODAL ══ */
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(21,67,96,0.45);z-index:400;align-items:center;justify-content:center;padding:16px}
    .modal-overlay.show{display:flex}
    .modal-box{background:white;border-radius:3px;width:100%;max-width:420px;box-shadow:0 12px 32px rgba(0,0,0,0.25);overflow:hidden}
    .modal-header{background:#154360;color:white;padding:12px 18px;display:flex;align-items:center;justify-content:space-between}
    .modal-header h3{font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:0.4px}
    .modal-close{background:none;border:none;color:#d6eaf8;cursor:pointer;font-size:16px;line-height:1;padding:2px 4px}
    .modal-close:hover{color:white}
    .modal-body{padding:18px}
    .modal-footer{padding:12px 18px;border-top:1px solid #eef3f7;display:flex;justify-content:flex-end;gap:8px}

    .form-field{margin-bottom:14px;display:flex;flex-direction:column;gap:5px}
    .form-field label{font-size:11px;font-weight:bold;color:#1a3a5c;text-transform:uppercase;letter-spacing:0.3px}
    .form-field label .req{color:#c0392b;margin-left:2px}
    .form-field input[type="text"],
    .form-field select{padding:8px 10px;border:1px solid #b0c4d8;border-radius:2px;font-size:12px;color:#1a1a2e;background:#f8fbfd;outline:none;transition:border-color 0.15s}
    .form-field input[type="text"]:focus,
    .form-field select:focus{border-color:#154360;background:white;box-shadow:0 0 0 2px rgba(21,67,96,0.1)}
    .form-field .field-error{font-size:10px;color:#c0392b;display:none}
    .form-field.has-error input,
    .form-field.has-error select{border-color:#c0392b}
    .form-field.has-error .field-error{display:block}

    .default-status-note{display:flex;align-items:center;gap:6px;background:#eaf2f8;border:1px solid #b8d4e8;border-radius:2px;padding:7px 10px;font-size:10px;color:#154360;margin-bottom:4px}

    .btn-secondary-sm{background:#eaf2f8;color:#154360;border:1px solid #c8dce9;padding:8px 16px;font-size:11px;font-weight:bold;cursor:pointer;border-radius:2px;text-transform:uppercase;letter-spacing:0.3px;transition:background 0.15s}
    .btn-secondary-sm:hover{background:#d6eaf8}
    .btn-primary-sm[disabled],.btn-secondary-sm[disabled]{opacity:0.6;cursor:not-allowed}

    .mandal-checkbox-item {
      display: flex;
      align-items: center;
      gap: 8px;
    }
  
    .mandal-checkbox-item input[type="checkbox"] {
      width: 16px;
      height: 16px;
      cursor: pointer;
      accent-color: #154360;
    }
  
    .mandal-checkbox-item label {
      flex: 1;
      margin: 0;
      font-size: 11px;
      color: #1a1a2e;
      cursor: pointer;
      font-weight: normal;
      text-transform: none;
      letter-spacing: normal;
    }

    /* ══ RESPONSIVE ══ */
    @media(max-width:860px){
      .sidebar{position:fixed;top:0;bottom:0;left:0;margin-left:-230px}
      .sidebar.open{margin-left:0}
      .hamburger{display:inline-flex;align-items:center}
      .profile-meta{display:none}
    }
    @media(max-width:560px){
      .content{padding:14px 12px}
      .search-box{min-width:0;flex:1}
      .toolbar{flex-direction:column;align-items:stretch}
      .data-table{font-size:10px}
      .data-table th,.data-table td{padding:6px}
      .pagination-container{flex-direction:column;align-items:stretch;gap:8px}
    }
  </style>
</head>
<body>

  <div class="toast" id="toast"></div>
  <div class="sidebar-overlay" id="sidebar-overlay" onclick="closeSidebar()"></div>

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
        <a class="sb-item active" data-tab="mandal" onclick="switchTab('mandal', this); return false;" href="#">
          <span class="sb-icon">🏛️</span> Mandal
        </a>
        <a class="sb-item" data-tab="village" onclick="switchTab('village', this); return false;" href="#">
          <span class="sb-icon">🏘️</span> Village
        </a>
        <a class="sb-item" data-tab="working_office" onclick="switchTab('working_office', this); return false;" href="#">
          <span class="sb-icon">🏢</span> Working Office
        </a>

        <div class="nav-section-label">Access Control</div>
        <a class="sb-item" href="{{ route('admin.pahani-management.index') }}">
          <span class="sb-icon">👤</span> Pahani Management 
        </a>
        <a class="sb-item" data-tab="users" onclick="switchTab('users', this); return false;" href="#">
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
            <div class="topbar-title" id="topbar-title">Mandal Management</div>
            <div class="topbar-breadcrumb" id="topbar-breadcrumb">Admin › Master Data › Mandal</div>
          </div>
        </div>

        <div class="topbar-right">
          <button class="icon-btn" title="Notifications">🔔<span class="badge-dot"></span></button>

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
              <div class="profile-menu-header">
                <div class="pm-name">{{ auth()->user()->name ?? 'Admin User' }}</div>
                <div class="pm-email">{{ auth()->user()->email ?? 'admin@example.com' }}</div>
              </div>
              <a class="profile-menu-item" href="#">
                <span>👤</span> Profile
              </a>
              <div class="profile-menu-divider"></div>
              <a class="profile-menu-item danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <span>🚪</span> Logout
              </a>
            </div>
          </div>
        </div>
      </div>

      {{-- Hidden logout form (POST for CSRF safety) --}}
      <form id="logout-form" action="{{ route('logout') ?? '#' }}" method="POST" style="display:none">
        @csrf
      </form>

      {{-- ── CONTENT ── --}}
      <div class="content">

        {{-- ═══ TAB: MANDAL ═══ --}}
        <div class="tab-panel active" id="tab-mandal">
          <div class="page-heading">
            <h2>🏛️ Mandal Management</h2>
            <div class="ph-sub">Add, edit, and manage revenue Mandals</div>
          </div>

          <div class="toolbar">
            <div class="search-box">
              🔎 <input type="text" id="mandal-search" placeholder="Search Mandal by name…" oninput="filterAndPaginate('mandal')">
            </div>
            <button class="btn-primary-sm" onclick="openAddMandalModal()">
              + Add Mandal
            </button>
          </div>

          <div class="section-card">
            <div class="loading-bar" id="mandal-loading"><div class="spinner"></div> Loading mandals…</div>
            <table class="data-table" id="mandal-table" style="display:none">
              <thead>
                <tr>
                  <th style="width:6%">#</th>
                  <th>Mandal Name</th>
                  <th>Slug</th>
                  <th style="width:14%">Villages</th>
                  <th style="width:12%">Status</th>
                  <th style="width:12%;text-align:center">Actions</th>
                </tr>
              </thead>
              <tbody id="mandal-tbody"></tbody>
            </table>
            <div class="empty-state" id="mandal-empty" style="display:none">
              <div class="es-icon">🏛️</div> No mandals found.
            </div>
            <div class="pagination-container" id="mandal-pagination" style="display:none">
              <div class="pagination-info">
                Showing <strong id="mandal-info-from">1</strong>–<strong id="mandal-info-to">10</strong> of <strong id="mandal-info-total">0</strong>
              </div>
              <div class="pagination-nav" id="mandal-nav"></div>
            </div>
          </div>
        </div>

        {{-- ═══ TAB: VILLAGE ═══ --}}
        <div class="tab-panel" id="tab-village">
          <div class="page-heading">
            <h2>🏘️ Village Management</h2>
            <div class="ph-sub">Add, edit, and manage villages under each Mandal</div>
          </div>

          <div class="toolbar">
            <div class="search-box">
              🔎 <input type="text" id="village-search" placeholder="Search Village by name…" oninput="filterAndPaginate('village')">
            </div>
            <button class="btn-primary-sm" onclick="openAddVillageModal()">
              + Add Village
            </button>
          </div>

          <div class="section-card">
            <div class="loading-bar" id="village-loading"><div class="spinner"></div> Loading villages…</div>
            <table class="data-table" id="village-table" style="display:none">
              <thead>
                <tr>
                  <th style="width:6%">#</th>
                  <th>Village Name</th>
                  <th>Slug</th>
                  <th>Mandal</th>
                  <th style="width:12%">Status</th>
                  <th style="width:12%;text-align:center">Actions</th>
                </tr>
              </thead>
              <tbody id="village-tbody"></tbody>
            </table>
            <div class="empty-state" id="village-empty" style="display:none">
              <div class="es-icon">🏘️</div> No villages found.
            </div>
            <div class="pagination-container" id="village-pagination" style="display:none">
              <div class="pagination-info">
                Showing <strong id="village-info-from">1</strong>–<strong id="village-info-to">10</strong> of <strong id="village-info-total">0</strong>
              </div>
              <div class="pagination-nav" id="village-nav"></div>
            </div>
          </div>
        </div>

        {{-- ═══ TAB: WORKING OFFICE ═══ --}}
        <div class="tab-panel" id="tab-working_office">
          <div class="page-heading">
            <h2>🏢 Working Office Management</h2>
            <div class="ph-sub">Add, edit, and manage working offices</div>
          </div>

          <div class="toolbar">
            <div class="search-box">
              🔎 <input type="text" id="working_office-search" placeholder="Search Working Office by name…" oninput="filterAndPaginate('working_office')">
            </div>
            <button class="btn-primary-sm" onclick="openAddWorkingOfficeModal()">
              + Add Working Office
            </button>
          </div>

          <div class="section-card">
            <div class="loading-bar" id="working_office-loading"><div class="spinner"></div> Loading working offices…</div>
            <table class="data-table" id="working_office-table" style="display:none">
              <thead>
                <tr>
                  <th style="width:6%">#</th>
                  <th>Office Name</th>
                  <th>Slug</th>
                  <th style="width:20%">Created Date</th>
                  <th style="width:12%">Status</th>
                  <th style="width:12%;text-align:center">Actions</th>
                </tr>
              </thead>
              <tbody id="working_office-tbody"></tbody>
            </table>
            <div class="empty-state" id="working_office-empty" style="display:none">
              <div class="es-icon">🏢</div> No working offices found.
            </div>
            <div class="pagination-container" id="working_office-pagination" style="display:none">
              <div class="pagination-info">
                Showing <strong id="working_office-info-from">1</strong>–<strong id="working_office-info-to">10</strong> of <strong id="working_office-info-total">0</strong>
              </div>
              <div class="pagination-nav" id="working_office-nav"></div>
            </div>
          </div>
        </div>

        {{-- ═══ TAB: USERS ═══ --}}
        <div class="tab-panel" id="tab-users">
          <div class="page-heading">
            <h2>👤 Users Management</h2>
            <div class="ph-sub">Manage user accounts and permissions</div>
          </div>

          <div class="toolbar">
            <div class="search-box">
              🔎 <input type="text" id="users-search" placeholder="Search User by name or email…" oninput="filterAndPaginate('users')">
            </div>
          </div>

          <div class="section-card">
            <div class="loading-bar" id="users-loading"><div class="spinner"></div> Loading users…</div>
            <table class="data-table" id="users-table" style="display:none">
              <thead>
                <tr>
                  <th style="width:6%">#</th>
                  <th>Name</th>
                  <th>Email</th>
                  <th>Working Office</th>
                  <th style="width:12%">Status</th>
                  <th style="width:12%;text-align:center">Actions</th>
                </tr>
              </thead>
              <tbody id="users-tbody"></tbody>
            </table>
            <div class="empty-state" id="users-empty" style="display:none">
              <div class="es-icon">👤</div> No users found.
            </div>
            <div class="pagination-container" id="users-pagination" style="display:none">
              <div class="pagination-info">
                Showing <strong id="users-info-from">1</strong>–<strong id="users-info-to">10</strong> of <strong id="users-info-total">0</strong>
              </div>
              <div class="pagination-nav" id="users-nav"></div>
            </div>
          </div>
        </div>

      </div>

    </div>

  </div>

  {{-- ══════════════ ADD MANDAL MODAL ══════════════ --}}
  <div class="modal-overlay" id="mandal-modal-overlay" onclick="if(event.target===this) closeModal('mandal-modal-overlay')">
    <div class="modal-box">
      <div class="modal-header">
        <h3>+ Add Mandal</h3>
        <button class="modal-close" onclick="closeModal('mandal-modal-overlay')">✕</button>
      </div>
      <form id="mandal-add-form" onsubmit="submitMandalForm(event)">
        <div class="modal-body">
          <div class="default-status-note">✔ New mandals are added as <strong>Active</strong> by default.</div>
          <div class="form-field" id="mandal-name-field">
            <label for="mandal-name-input">Mandal Name <span class="req">*</span></label>
            <input type="text" id="mandal-name-input" name="name" placeholder="e.g. Rangareddy" autocomplete="off">
            <div class="field-error" id="mandal-name-error"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary-sm" onclick="closeModal('mandal-modal-overlay')">Cancel</button>
          <button type="submit" class="btn-primary-sm" id="mandal-submit-btn">Save Mandal</button>
        </div>
      </form>
    </div>
  </div>

  {{-- ══════════════ ADD VILLAGE MODAL ══════════════ --}}
  <div class="modal-overlay" id="village-modal-overlay" onclick="if(event.target===this) closeModal('village-modal-overlay')">
    <div class="modal-box">
      <div class="modal-header">
        <h3>+ Add Village</h3>
        <button class="modal-close" onclick="closeModal('village-modal-overlay')">✕</button>
      </div>
      <form id="village-add-form" onsubmit="submitVillageForm(event)">
        <div class="modal-body">
          <div class="default-status-note">✔ New villages are added as <strong>Active</strong> by default.</div>

          <div class="form-field" id="village-mandal-field">
            <label for="village-mandal-select">Mandal <span class="req">*</span></label>
            <select id="village-mandal-select" name="mandal_id">
              <option value="">— Select Mandal —</option>
            </select>
            <div class="field-error" id="village-mandal-error"></div>
          </div>

          <div class="form-field" id="village-name-field">
            <label for="village-name-input">Village Name <span class="req">*</span></label>
            <input type="text" id="village-name-input" name="name" placeholder="e.g. Kandi" autocomplete="off">
            <div class="field-error" id="village-name-error"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary-sm" onclick="closeModal('village-modal-overlay')">Cancel</button>
          <button type="submit" class="btn-primary-sm" id="village-submit-btn">Save Village</button>
        </div>
      </form>
    </div>
  </div>

  {{-- ══════════════ ADD WORKING OFFICE MODAL ══════════════ --}}
  <div class="modal-overlay" id="working_office-modal-overlay" onclick="if(event.target===this) closeModal('working_office-modal-overlay')">
    <div class="modal-box">
      <div class="modal-header">
        <h3>+ Add Working Office</h3>
        <button class="modal-close" onclick="closeModal('working_office-modal-overlay')">✕</button>
      </div>
      <form id="working_office-add-form" onsubmit="submitWorkingOfficeForm(event)">
        <div class="modal-body">
          <div class="default-status-note">✔ New offices are added as <strong>Active</strong> by default.</div>
          <div class="form-field" id="working_office-name-field">
            <label for="working_office-name-input">Office Name <span class="req">*</span></label>
            <input type="text" id="working_office-name-input" name="name" placeholder="e.g. District Revenue Office" autocomplete="off">
            <div class="field-error" id="working_office-name-error"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary-sm" onclick="closeModal('working_office-modal-overlay')">Cancel</button>
          <button type="submit" class="btn-primary-sm" id="working_office-submit-btn">Save Office</button>
        </div>
      </form>
    </div>
  </div>

  {{-- ══════════════ EDIT MANDAL MODAL ══════════════ --}}
  <div class="modal-overlay" id="mandal-edit-modal-overlay" onclick="if(event.target===this) closeModal('mandal-edit-modal-overlay')">
    <div class="modal-box">
      <div class="modal-header">
        <h3>✎ Edit Mandal</h3>
        <button class="modal-close" onclick="closeModal('mandal-edit-modal-overlay')">✕</button>
      </div>
      <form id="mandal-edit-form" onsubmit="submitEditMandalForm(event)">
        <div class="modal-body">
          <input type="hidden" id="mandal-edit-id">
          <div class="form-field" id="mandal-edit-name-field">
            <label for="mandal-edit-name-input">Mandal Name <span class="req">*</span></label>
            <input type="text" id="mandal-edit-name-input" name="name" autocomplete="off">
            <div class="field-error" id="mandal-edit-name-error"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary-sm" onclick="closeModal('mandal-edit-modal-overlay')">Cancel</button>
          <button type="submit" class="btn-primary-sm" id="mandal-edit-submit-btn">Update Mandal</button>
        </div>
      </form>
    </div>
  </div>

  {{-- ══════════════ EDIT VILLAGE MODAL ══════════════ --}}
  <div class="modal-overlay" id="village-edit-modal-overlay" onclick="if(event.target===this) closeModal('village-edit-modal-overlay')">
    <div class="modal-box">
      <div class="modal-header">
        <h3>✎ Edit Village</h3>
        <button class="modal-close" onclick="closeModal('village-edit-modal-overlay')">✕</button>
      </div>
      <form id="village-edit-form" onsubmit="submitEditVillageForm(event)">
        <div class="modal-body">
          <input type="hidden" id="village-edit-id">
          <div class="form-field" id="village-edit-mandal-field">
            <label for="village-edit-mandal-select">Mandal <span class="req">*</span></label>
            <select id="village-edit-mandal-select" name="mandal_id">
              <option value="">— Select Mandal —</option>
            </select>
            <div class="field-error" id="village-edit-mandal-error"></div>
          </div>

          <div class="form-field" id="village-edit-name-field">
            <label for="village-edit-name-input">Village Name <span class="req">*</span></label>
            <input type="text" id="village-edit-name-input" name="name" autocomplete="off">
            <div class="field-error" id="village-edit-name-error"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary-sm" onclick="closeModal('village-edit-modal-overlay')">Cancel</button>
          <button type="submit" class="btn-primary-sm" id="village-edit-submit-btn">Update Village</button>
        </div>
      </form>
    </div>
  </div>

  {{-- ══════════════ EDIT WORKING OFFICE MODAL ══════════════ --}}
  <div class="modal-overlay" id="working_office-edit-modal-overlay" onclick="if(event.target===this) closeModal('working_office-edit-modal-overlay')">
    <div class="modal-box">
      <div class="modal-header">
        <h3>✎ Edit Working Office</h3>
        <button class="modal-close" onclick="closeModal('working_office-edit-modal-overlay')">✕</button>
      </div>
      <form id="working_office-edit-form" onsubmit="submitEditWorkingOfficeForm(event)">
        <div class="modal-body">
          <input type="hidden" id="working_office-edit-id">
          <div class="form-field" id="working_office-edit-name-field">
            <label for="working_office-edit-name-input">Office Name <span class="req">*</span></label>
            <input type="text" id="working_office-edit-name-input" name="name" autocomplete="off">
            <div class="field-error" id="working_office-edit-name-error"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary-sm" onclick="closeModal('working_office-edit-modal-overlay')">Cancel</button>
          <button type="submit" class="btn-primary-sm" id="working_office-edit-submit-btn">Update Office</button>
        </div>
      </form>
    </div>
  </div>

  {{-- ══════════════ EDIT USER MODAL ══════════════ --}}
  <div class="modal-overlay" id="user-edit-modal-overlay" onclick="if(event.target===this) closeModal('user-edit-modal-overlay')">
    <div class="modal-box">
      <div class="modal-header">
        <h3>✎ Edit User</h3>
        <button class="modal-close" onclick="closeModal('user-edit-modal-overlay')">✕</button>
      </div>
      <form id="user-edit-form" onsubmit="submitEditUserForm(event)">
        <div class="modal-body">
          <input type="hidden" id="user-edit-id">
          
          <div class="form-field" id="user-edit-name-field">
            <label for="user-edit-name-input">Name <span class="req">*</span></label>
            <input type="text" id="user-edit-name-input" name="name" autocomplete="off">
            <div class="field-error" id="user-edit-name-error"></div>
          </div>

          <div class="form-field" id="user-edit-email-field">
            <label for="user-edit-email-input">Email <span class="req">*</span></label>
            <input type="text" id="user-edit-email-input" name="email" autocomplete="off">
            <div class="field-error" id="user-edit-email-error"></div>
          </div>

          <!-- NEW: Working Office Dropdown -->
          <div class="form-field" id="user-edit-working-office-field">
            <label for="user-edit-working-office-select">Working Office <span class="req">*</span></label>
            <select id="user-edit-working-office-select" name="working_office_id">
              <option value="">— Select Working Office —</option>
            </select>
            <div class="field-error" id="user-edit-working-office-error"></div>
          </div>

          <!-- NEW: Status Toggle -->
          <div class="form-field" id="user-edit-status-field">
            <label for="user-edit-status-checkbox">
              <input type="checkbox" id="user-edit-status-checkbox" name="status" style="width:16px;height:16px;cursor:pointer;accent-color:#154360;">
              <span style="margin-left:8px;">Active</span>
            </label>
            <div class="field-error" id="user-edit-status-error"></div>
          </div>

          <!-- Mandals Assignment -->
          <div class="form-field" id="user-edit-mandals-field">
            <label>Assign Mandals</label>
            <div id="user-edit-mandals-container" style="display: flex; flex-direction: column; gap: 8px; max-height: 200px; overflow-y: auto; border: 1px solid #b0c4d8; border-radius: 2px; padding: 10px; background: #f8fbfd;">
              <!-- Mandal checkboxes will be populated here -->
            </div>
            <div class="field-error" id="user-edit-mandals-error"></div>
          </div>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary-sm" onclick="closeModal('user-edit-modal-overlay')">Cancel</button>
          <button type="submit" class="btn-primary-sm" id="user-edit-submit-btn">Update User</button>
        </div>
      </form>
    </div>
  </div>

  {{-- ══════════════ GENERIC DELETE CONFIRM MODAL ══════════════ --}}
  <div class="modal-overlay" id="delete-modal-overlay" onclick="if(event.target===this) closeModal('delete-modal-overlay')">
    <div class="modal-box">
      <div class="modal-header">
        <h3>🗑️ Confirm Delete</h3>
        <button class="modal-close" onclick="closeModal('delete-modal-overlay')">✕</button>
      </div>
      <div class="modal-body">
        <p id="delete-modal-message" style="font-size:12px;color:#333;line-height:1.5"></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn-secondary-sm" onclick="closeModal('delete-modal-overlay')">Cancel</button>
        <button type="button" class="btn-icon danger" style="width:auto;padding:8px 16px;font-size:11px;font-weight:bold;text-transform:uppercase" id="delete-confirm-btn" onclick="confirmDelete()">Delete</button>
      </div>
    </div>
  </div>

<script>
/* ══════════════════════════════════════════════════════════════════
   PAGINATION STATE
══════════════════════════════════════════════════════════════════ */
const PAGINATION_SIZE = 10;
const paginationState = {
  mandal: { currentPage: 1, totalPages: 1, allData: [], filteredData: [] },
  village: { currentPage: 1, totalPages: 1, allData: [], filteredData: [] },
  working_office: { currentPage: 1, totalPages: 1, allData: [], filteredData: [] },
  users: { currentPage: 1, totalPages: 1, allData: [], filteredData: [] },
};

/* ══════════════════════════════════════════════════════════════════
   TAB SWITCHING
══════════════════════════════════════════════════════════════════ */
const TAB_META = {
  mandal:  { title: 'Mandal Management',         crumb: 'Admin › Master Data › Mandal',         endpoint: '/api/admin/mandals' },
  village: { title: 'Village Management',        crumb: 'Admin › Master Data › Village',        endpoint: '/api/admin/villages' },
  working_office: { title: 'Working Office Management', crumb: 'Admin › Master Data › Working Office', endpoint: '/api/admin/working-offices' },
  users:   { title: 'Users List',                crumb: 'Admin › Access Control › Users',       endpoint: '/api/admin/users' },
};
const loadedTabs = new Set();

function switchTab(tab, el) {
  document.querySelectorAll('.sb-item').forEach(i => i.classList.remove('active'));
  el.classList.add('active');

  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  document.getElementById('tab-' + tab).classList.add('active');

  document.getElementById('topbar-title').textContent = TAB_META[tab].title;
  document.getElementById('topbar-breadcrumb').textContent = TAB_META[tab].crumb;

  if (!loadedTabs.has(tab)) {
    loadTabData(tab);
  }

  closeSidebar();
}

/* ══════════════════════════════════════════════════════════════════
   DATA LOADING WITH PAGINATION
══════════════════════════════════════════════════════════════════ */
function loadTabData(tab) {
  const meta = TAB_META[tab];
  const loadingEl = document.getElementById(tab + '-loading');
  const tableEl   = document.getElementById(tab + '-table');
  const emptyEl   = document.getElementById(tab + '-empty');

  loadingEl.classList.add('show');
  tableEl.style.display = 'none';
  emptyEl.style.display = 'none';

  fetch(meta.endpoint, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(data => {
      loadedTabs.add(tab);
      paginationState[tab].allData = Array.isArray(data) ? data : (data.data || []);
      paginationState[tab].filteredData = [...paginationState[tab].allData];
      paginationState[tab].currentPage = 1;
      paginationState[tab].totalPages = Math.ceil(paginationState[tab].filteredData.length / PAGINATION_SIZE) || 1;
      renderTab(tab);
      updatePagination(tab);
    })
    .catch(() => showToast(`Failed to load ${tab} data.`, true))
    .finally(() => loadingEl.classList.remove('show'));
}

function renderTab(tab) {
  const tbody   = document.getElementById(tab + '-tbody');
  const tableEl = document.getElementById(tab + '-table');
  const emptyEl = document.getElementById(tab + '-empty');
  const state   = paginationState[tab];

  tbody.innerHTML = '';

  // Get page data
  const start = (state.currentPage - 1) * PAGINATION_SIZE;
  const end = start + PAGINATION_SIZE;
  const pageData = state.filteredData.slice(start, end);

  if (state.filteredData.length === 0) {
    emptyEl.style.display = 'block';
    tableEl.style.display = 'none';
    return;
  }

  tableEl.style.display = 'table';

  pageData.forEach((row, i) => {
    const tr = document.createElement('tr');
    const globalIndex = start + i + 1;

    if (tab === 'mandal') {
      tr.innerHTML = `
        <td>${globalIndex}</td>
        <td>${escapeHtml(row.name)}</td>
        <td>${escapeHtml(row.slug)}</td>
        <td>${row.villages_count ?? 0}</td>
        <td>${statusPill(row.is_active)}</td>
        <td style="text-align:center">${rowActions('mandal', row.id)}</td>`;
    } else if (tab === 'village') {
      tr.innerHTML = `
        <td>${globalIndex}</td>
        <td>${escapeHtml(row.name)}</td>
        <td>${escapeHtml(row.slug)}</td>
        <td>${escapeHtml(row.mandal_name ?? '—')}</td>
        <td>${statusPill(row.is_active)}</td>
        <td style="text-align:center">${rowActions('village', row.id)}</td>`;
    } else if (tab === 'working_office') {
      tr.innerHTML = `
        <td>${globalIndex}</td>
        <td>${escapeHtml(row.name)}</td>
        <td>${escapeHtml(row.slug)}</td>
        <td>${formatDate(row.created_at)}</td>
        <td>${statusPill(row.is_active)}</td>
        <td style="text-align:center">${rowActions('working_office', row.id)}</td>`;
    } else if (tab === 'users') {
      tr.innerHTML = `
        <td>${globalIndex}</td>
        <td>${escapeHtml(row.name)}</td>
        <td>${escapeHtml(row.email)}</td>
        <td>${escapeHtml(row.working_office_name ?? '—')}</td>
        <td>${row.status ? '<span class="pill pill-active">✔ Active</span>' : '<span class="pill pill-inactive">— Inactive</span>'}</td>
        <td style="text-align:center">${rowActions('users', row.id)}</td>`;
    }
    tbody.appendChild(tr);
  });
}

function statusPill(isActive) {
  return isActive
    ? '<span class="pill pill-active">✔ Active</span>'
    : '<span class="pill pill-inactive">— Inactive</span>';
}

function rowActions(tab, id) {
  return `
    <div class="row-actions" style="justify-content:center">
      <button class="btn-icon" title="Edit" onclick="openEditModal('${tab}', ${id})">✎</button>
      <button class="btn-icon danger" title="Delete" onclick="openDeleteModal('${tab}', ${id})">✕</button>
    </div>`;
}

function formatDate(dateString) {
  if (!dateString) return '—';
  const date = new Date(dateString);
  return date.toLocaleDateString('en-IN');
}

function escapeHtml(str) {
  const d = document.createElement('div');
  d.textContent = str ?? '';
  return d.innerHTML;
}

/* ══════════════════════════════════════════════════════════════════
   PAGINATION CONTROLS
══════════════════════════════════════════════════════════════════ */
function updatePagination(tab) {
  const state = paginationState[tab];
  const paginationEl = document.getElementById(tab + '-pagination');
  const navEl = document.getElementById(tab + '-nav');

  if (state.filteredData.length === 0) {
    paginationEl.style.display = 'none';
    return;
  }

  paginationEl.style.display = 'flex';

  const start = (state.currentPage - 1) * PAGINATION_SIZE + 1;
  const end = Math.min(start + PAGINATION_SIZE - 1, state.filteredData.length);

  document.getElementById(tab + '-info-from').textContent = start;
  document.getElementById(tab + '-info-to').textContent = end;
  document.getElementById(tab + '-info-total').textContent = state.filteredData.length;

  navEl.innerHTML = '';
  
  // Previous button
  const prevBtn = document.createElement('button');
  prevBtn.className = 'page-btn';
  prevBtn.textContent = '← Prev';
  prevBtn.disabled = state.currentPage === 1;
  prevBtn.onclick = () => goToPage(tab, state.currentPage - 1);
  navEl.appendChild(prevBtn);

  // Page numbers
  for (let i = 1; i <= state.totalPages; i++) {
    const pageBtn = document.createElement('button');
    pageBtn.className = 'page-btn' + (i === state.currentPage ? ' active' : '');
    pageBtn.textContent = i;
    pageBtn.onclick = () => goToPage(tab, i);
    navEl.appendChild(pageBtn);
  }

  // Next button
  const nextBtn = document.createElement('button');
  nextBtn.className = 'page-btn';
  nextBtn.textContent = 'Next →';
  nextBtn.disabled = state.currentPage === state.totalPages;
  nextBtn.onclick = () => goToPage(tab, state.currentPage + 1);
  navEl.appendChild(nextBtn);
}

function goToPage(tab, page) {
  const state = paginationState[tab];
  if (page < 1 || page > state.totalPages) return;
  state.currentPage = page;
  renderTab(tab);
  updatePagination(tab);
}

function filterAndPaginate(tab) {
  const searchInput = document.getElementById(tab + '-search');
  const query = searchInput.value.trim().toLowerCase();
  const state = paginationState[tab];

  // Filter data
  if (query === '') {
    state.filteredData = [...state.allData];
  } else {
    state.filteredData = state.allData.filter(row => {
      const searchableText = JSON.stringify(row).toLowerCase();
      return searchableText.includes(query);
    });
  }

  // Reset to page 1 and recalculate total pages
  state.currentPage = 1;
  state.totalPages = Math.ceil(state.filteredData.length / PAGINATION_SIZE) || 1;

  renderTab(tab);
  updatePagination(tab);
}

/* ══════════════════════════════════════════════════════════════════
   PROFILE DROPDOWN
══════════════════════════════════════════════════════════════════ */
function toggleProfileMenu(e) {
  e.stopPropagation();
  document.getElementById('profile-wrap').classList.toggle('open');
}
document.addEventListener('click', () => {
  document.getElementById('profile-wrap').classList.remove('open');
});

/* ══════════════════════════════════════════════════════════════════
   MOBILE SIDEBAR
══════════════════════════════════════════════════════════════════ */
function openSidebar() {
  document.getElementById('sidebar').classList.add('open');
  document.getElementById('sidebar-overlay').classList.add('show');
}
function closeSidebar() {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('sidebar-overlay').classList.remove('show');
}

/* ══════════════════════════════════════════════════════════════════
   CSRF HELPER
══════════════════════════════════════════════════════════════════ */
function csrfToken() {
  return document.querySelector('meta[name="csrf-token"]').content;
}

/* ══════════════════════════════════════════════════════════════════
   MODAL OPEN / CLOSE
══════════════════════════════════════════════════════════════════ */
function openModal(id) {
  document.getElementById(id).classList.add('show');
}
function closeModal(id) {
  document.getElementById(id).classList.remove('show');
}

function clearFieldError(fieldId, errorId) {
  document.getElementById(fieldId).classList.remove('has-error');
  document.getElementById(errorId).textContent = '';
}
function setFieldError(fieldId, errorId, message) {
  document.getElementById(fieldId).classList.add('has-error');
  document.getElementById(errorId).textContent = message;
}

/* ══════════════════════════════════════════════════════════════════
   ADD MANDAL
══════════════════════════════════════════════════════════════════ */
function openAddMandalModal() {
  document.getElementById('mandal-add-form').reset();
  clearFieldError('mandal-name-field', 'mandal-name-error');
  openModal('mandal-modal-overlay');
  document.getElementById('mandal-name-input').focus();
}

function submitMandalForm(e) {
  e.preventDefault();
  clearFieldError('mandal-name-field', 'mandal-name-error');

  const name = document.getElementById('mandal-name-input').value.trim();
  if (!name) {
    setFieldError('mandal-name-field', 'mandal-name-error', 'Mandal name is required.');
    return;
  }

  const btn = document.getElementById('mandal-submit-btn');
  btn.disabled = true;
  btn.textContent = 'Saving…';

  fetch('/api/admin/mandals', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken(),
      'X-Requested-With': 'XMLHttpRequest',
    },
    body: JSON.stringify({ name }),
  })
  .then(async r => {
    const data = await r.json().catch(() => ({}));

    if (r.status === 422) {
      const msg = data.errors?.name?.[0] || data.message || 'Validation failed.';
      setFieldError('mandal-name-field', 'mandal-name-error', msg);
      return;
    }
    if (!r.ok || !data.success) {
      showToast(data.message || 'Failed to add mandal.', true);
      return;
    }

    showToast('✔ Mandal added successfully.');
    closeModal('mandal-modal-overlay');
    loadedTabs.delete('mandal');
    loadTabData('mandal');
  })
  .catch(() => showToast('Network error. Please try again.', true))
  .finally(() => {
    btn.disabled = false;
    btn.textContent = 'Save Mandal';
  });
}

/* ══════════════════════════════════════════════════════════════════
   ADD VILLAGE
══════════════════════════════════════════════════════════════════ */
function openAddVillageModal() {
  document.getElementById('village-add-form').reset();
  clearFieldError('village-mandal-field', 'village-mandal-error');
  clearFieldError('village-name-field', 'village-name-error');
  populateMandalDropdown('village-mandal-select');
  openModal('village-modal-overlay');
}

function populateMandalDropdown(selectId, selectedId = null) {
  const sel = document.getElementById(selectId);
  sel.innerHTML = '<option value="">Loading mandals…</option>';

  fetch('/api/admin/mandals', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(mandals => {
      sel.innerHTML = '<option value="">— Select Mandal —</option>';
      const data = Array.isArray(mandals) ? mandals : (mandals.data || []);
      data.forEach(m => {
        const opt = document.createElement('option');
        opt.value = m.id;
        opt.textContent = m.name;
        sel.appendChild(opt);
      });
      if (selectedId !== null) {
        sel.value = selectedId;
      }
    })
    .catch(() => {
      sel.innerHTML = '<option value="">Failed to load mandals</option>';
      showToast('Failed to load mandals for dropdown.', true);
    });
}

function submitVillageForm(e) {
  e.preventDefault();
  clearFieldError('village-mandal-field', 'village-mandal-error');
  clearFieldError('village-name-field', 'village-name-error');

  const mandalId = document.getElementById('village-mandal-select').value;
  const name     = document.getElementById('village-name-input').value.trim();

  let hasError = false;
  if (!mandalId) {
    setFieldError('village-mandal-field', 'village-mandal-error', 'Please select a Mandal.');
    hasError = true;
  }
  if (!name) {
    setFieldError('village-name-field', 'village-name-error', 'Village name is required.');
    hasError = true;
  }
  if (hasError) return;

  const btn = document.getElementById('village-submit-btn');
  btn.disabled = true;
  btn.textContent = 'Saving…';

  fetch('/api/admin/villages', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken(),
      'X-Requested-With': 'XMLHttpRequest',
    },
    body: JSON.stringify({ mandal_id: mandalId, name }),
  })
  .then(async r => {
    const data = await r.json().catch(() => ({}));

    if (r.status === 422) {
      if (data.errors?.mandal_id) setFieldError('village-mandal-field', 'village-mandal-error', data.errors.mandal_id[0]);
      if (data.errors?.name)      setFieldError('village-name-field', 'village-name-error', data.errors.name[0]);
      if (!data.errors) showToast(data.message || 'Validation failed.', true);
      return;
    }
    if (!r.ok || !data.success) {
      showToast(data.message || 'Failed to add village.', true);
      return;
    }

    showToast('✔ Village added successfully.');
    closeModal('village-modal-overlay');
    loadedTabs.delete('village');
    loadTabData('village');
  })
  .catch(() => showToast('Network error. Please try again.', true))
  .finally(() => {
    btn.disabled = false;
    btn.textContent = 'Save Village';
  });
}

/* ══════════════════════════════════════════════════════════════════
   ADD WORKING OFFICE
══════════════════════════════════════════════════════════════════ */
function openAddWorkingOfficeModal() {
  document.getElementById('working_office-add-form').reset();
  clearFieldError('working_office-name-field', 'working_office-name-error');
  openModal('working_office-modal-overlay');
  document.getElementById('working_office-name-input').focus();
}

function submitWorkingOfficeForm(e) {
  e.preventDefault();
  clearFieldError('working_office-name-field', 'working_office-name-error');

  const name = document.getElementById('working_office-name-input').value.trim();
  if (!name) {
    setFieldError('working_office-name-field', 'working_office-name-error', 'Office name is required.');
    return;
  }

  const btn = document.getElementById('working_office-submit-btn');
  btn.disabled = true;
  btn.textContent = 'Saving…';

  fetch('/api/admin/working-offices', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken(),
      'X-Requested-With': 'XMLHttpRequest',
    },
    body: JSON.stringify({ name }),
  })
  .then(async r => {
    const data = await r.json().catch(() => ({}));

    if (r.status === 422) {
      const msg = data.errors?.name?.[0] || data.message || 'Validation failed.';
      setFieldError('working_office-name-field', 'working_office-name-error', msg);
      return;
    }
    if (!r.ok || !data.success) {
      showToast(data.message || 'Failed to add working office.', true);
      return;
    }

    showToast('✔ Working office added successfully.');
    closeModal('working_office-modal-overlay');
    loadedTabs.delete('working_office');
    loadTabData('working_office');
  })
  .catch(() => showToast('Network error. Please try again.', true))
  .finally(() => {
    btn.disabled = false;
    btn.textContent = 'Save Office';
  });
}

/* ══════════════════════════════════════════════════════════════════
   EDIT — shared config
══════════════════════════════════════════════════════════════════ */
const EDIT_META = {
  mandal:  { editUrl: id => `/api/admin/mandals/${id}/edit`,              updateUrl: id => `/api/admin/mandals/${id}`,              reloadTab: 'mandal'  },
  village: { editUrl: id => `/api/admin/villages/${id}/edit`,             updateUrl: id => `/api/admin/villages/${id}`,             reloadTab: 'village' },
  working_office: { editUrl: id => `/api/admin/working-offices/${id}/edit`, updateUrl: id => `/api/admin/working-offices/${id}`, reloadTab: 'working_office' },
  users:   { editUrl: id => `/api/admin/users/${id}/edit`,                updateUrl: id => `/api/admin/users/${id}`,                reloadTab: 'users'   },
};
 
function openEditModal(tab, id) {
  fetch(EDIT_META[tab].editUrl(id), { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => {
      if (!r.ok) throw new Error('not found');
      return r.json();
    })
    .then(record => {
      if (tab === 'mandal') {
        clearFieldError('mandal-edit-name-field', 'mandal-edit-name-error');
        document.getElementById('mandal-edit-id').value = record.id;
        document.getElementById('mandal-edit-name-input').value = record.name;
        openModal('mandal-edit-modal-overlay');
      } else if (tab === 'village') {
        clearFieldError('village-edit-mandal-field', 'village-edit-mandal-error');
        clearFieldError('village-edit-name-field', 'village-edit-name-error');
        document.getElementById('village-edit-id').value = record.id;
        populateMandalDropdown('village-edit-mandal-select', record.mandal_id);
        document.getElementById('village-edit-name-input').value = record.name;
        openModal('village-edit-modal-overlay');
      } else if (tab === 'working_office') {
        clearFieldError('working_office-edit-name-field', 'working_office-edit-name-error');
        document.getElementById('working_office-edit-id').value = record.id;
        document.getElementById('working_office-edit-name-input').value = record.name;
        openModal('working_office-edit-modal-overlay');
      } else if (tab === 'users') {
        clearFieldError('user-edit-name-field', 'user-edit-name-error');
        clearFieldError('user-edit-email-field', 'user-edit-email-error');
        clearFieldError('user-edit-working-office-field', 'user-edit-working-office-error');
        
        document.getElementById('user-edit-id').value = record.id;
        document.getElementById('user-edit-name-input').value = record.name;
        document.getElementById('user-edit-email-input').value = record.email;
        document.getElementById('user-edit-status-checkbox').checked = record.status === 1 || record.status === true;
        
        populateWorkingOfficeDropdown('user-edit-working-office-select', record.working_office_id);
        populateMandalCheckboxes(record.mandal_ids || []);
        openModal('user-edit-modal-overlay');
      }
    })
    .catch(() => showToast('Failed to load record details.', true));
}

function populateMandalCheckboxes(selectedIds) {
  const container = document.getElementById('user-edit-mandals-container');
  container.innerHTML = '';

  // Convert selectedIds to array of integers for proper comparison
  const selected = Array.isArray(selectedIds) 
    ? selectedIds.map(id => parseInt(id))
    : [];

  console.log('Selected mandal IDs:', selected);

  fetch('/api/admin/mandals', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(mandals => {
      const data = Array.isArray(mandals) ? mandals : (mandals.data || []);
      
      if (data.length === 0) {
        container.innerHTML = '<p style="color: #999; font-size: 11px;">No mandals available</p>';
        return;
      }

      data.forEach(m => {
        const mandalId = parseInt(m.id);
        const isChecked = selected.includes(mandalId);
        

        const label = document.createElement('label');
        label.className = 'mandal-checkbox-item';
        label.innerHTML = `
          <input type="checkbox" name="mandals" value="${mandalId}" ${isChecked ? 'checked' : ''}>
          <span>${escapeHtml(m.name)}</span>
        `;
        container.appendChild(label);
      });

      console.log('Mandals populated successfully');
    })
    .catch((err) => {
      console.error('Failed to load mandals:', err);
      showToast('Failed to load mandals for assignment.', true);
    });
}

// NEW: Function to populate working offices dropdown
function populateWorkingOfficeDropdown(selectId, selectedId = null) {
  const sel = document.getElementById(selectId);
  sel.innerHTML = '<option value="">Loading offices…</option>';

  fetch('/api/admin/working-offices', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(offices => {
      sel.innerHTML = '<option value="">— Select Working Office —</option>';
      const data = Array.isArray(offices) ? offices : (offices.data || []);
      data.forEach(office => {
        const opt = document.createElement('option');
        opt.value = office.id;
        opt.textContent = office.name;
        sel.appendChild(opt);
      });
      if (selectedId !== null && selectedId !== '') {
        sel.value = selectedId;
      }
    })
    .catch(() => {
      sel.innerHTML = '<option value="">Failed to load offices</option>';
      showToast('Failed to load working offices.', true);
    });
}

/* ══════════════════════════════════════════════════════════════════
   UPDATE MANDAL
══════════════════════════════════════════════════════════════════ */
function submitEditUserForm(e) {
  e.preventDefault();
  clearFieldError('user-edit-name-field', 'user-edit-name-error');
  clearFieldError('user-edit-email-field', 'user-edit-email-error');
  clearFieldError('user-edit-working-office-field', 'user-edit-working-office-error');
 
  const id = document.getElementById('user-edit-id').value;
  const name = document.getElementById('user-edit-name-input').value.trim();
  const email = document.getElementById('user-edit-email-input').value.trim();
  const working_office_id = document.getElementById('user-edit-working-office-select').value;
  const status = document.getElementById('user-edit-status-checkbox').checked ? 1 : 0;
 
  // Get selected mandals
  const mandalCheckboxes = document.querySelectorAll('#user-edit-mandals-container input[type="checkbox"]:checked');
  const mandalIds = Array.from(mandalCheckboxes).map(cb => parseInt(cb.value));
 
  let hasError = false;
  if (!name) {
    setFieldError('user-edit-name-field', 'user-edit-name-error', 'Name is required.');
    hasError = true;
  }
  if (!email) {
    setFieldError('user-edit-email-field', 'user-edit-email-error', 'Email is required.');
    hasError = true;
  }
  if (!working_office_id) {
    setFieldError('user-edit-working-office-field', 'user-edit-working-office-error', 'Please select a working office.');
    hasError = true;
  }
  if (hasError) return;
 
  const btn = document.getElementById('user-edit-submit-btn');
  btn.disabled = true;
  btn.textContent = 'Updating…';
 
  fetch(EDIT_META.users.updateUrl(id), {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken(),
      'X-Requested-With': 'XMLHttpRequest',
    },
    body: JSON.stringify({ 
      name, 
      email,
      working_office_id: parseInt(working_office_id),
      status: status,
      mandal_ids: mandalIds,
    }),
  })
  .then(async r => {
    const data = await r.json().catch(() => ({}));
    if (r.status === 422) {
      if (data.errors?.name) setFieldError('user-edit-name-field', 'user-edit-name-error', data.errors.name[0]);
      if (data.errors?.email) setFieldError('user-edit-email-field', 'user-edit-email-error', data.errors.email[0]);
      if (data.errors?.working_office_id) setFieldError('user-edit-working-office-field', 'user-edit-working-office-error', data.errors.working_office_id[0]);
      if (!data.errors) showToast(data.message || 'Validation failed.', true);
      return;
    }
    if (!r.ok || !data.success) {
      showToast(data.message || 'Failed to update user.', true);
      return;
    }
    showToast('✔ User updated successfully.');
    closeModal('user-edit-modal-overlay');
    loadedTabs.delete('users');
    loadTabData('users');
  })
  .catch(() => showToast('Network error. Please try again.', true))
  .finally(() => {
    btn.disabled = false;
    btn.textContent = 'Update User';
  });
}

/* ══════════════════════════════════════════════════════════════════
   UPDATE VILLAGE
══════════════════════════════════════════════════════════════════ */
function submitEditVillageForm(e) {
  e.preventDefault();
  clearFieldError('village-edit-mandal-field', 'village-edit-mandal-error');
  clearFieldError('village-edit-name-field', 'village-edit-name-error');

  const id       = document.getElementById('village-edit-id').value;
  const mandalId = document.getElementById('village-edit-mandal-select').value;
  const name     = document.getElementById('village-edit-name-input').value.trim();

  let hasError = false;
  if (!mandalId) {
    setFieldError('village-edit-mandal-field', 'village-edit-mandal-error', 'Please select a Mandal.');
    hasError = true;
  }
  if (!name) {
    setFieldError('village-edit-name-field', 'village-edit-name-error', 'Village name is required.');
    hasError = true;
  }
  if (hasError) return;

  const btn = document.getElementById('village-edit-submit-btn');
  btn.disabled = true;
  btn.textContent = 'Updating…';

  fetch(EDIT_META.village.updateUrl(id), {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken(),
      'X-Requested-With': 'XMLHttpRequest',
    },
    body: JSON.stringify({ mandal_id: mandalId, name }),
  })
  .then(async r => {
    const data = await r.json().catch(() => ({}));
    if (r.status === 422) {
      if (data.errors?.mandal_id) setFieldError('village-edit-mandal-field', 'village-edit-mandal-error', data.errors.mandal_id[0]);
      if (data.errors?.name)      setFieldError('village-edit-name-field', 'village-edit-name-error', data.errors.name[0]);
      if (!data.errors) showToast(data.message || 'Validation failed.', true);
      return;
    }
    if (!r.ok || !data.success) {
      showToast(data.message || 'Failed to update village.', true);
      return;
    }
    showToast('✔ Village updated successfully.');
    closeModal('village-edit-modal-overlay');
    loadedTabs.delete('village');
    loadTabData('village');
  })
  .catch(() => showToast('Network error. Please try again.', true))
  .finally(() => {
    btn.disabled = false;
    btn.textContent = 'Update Village';
  });
}

/* ══════════════════════════════════════════════════════════════════
   UPDATE WORKING OFFICE
══════════════════════════════════════════════════════════════════ */
function submitEditWorkingOfficeForm(e) {
  e.preventDefault();
  clearFieldError('working_office-edit-name-field', 'working_office-edit-name-error');

  const id   = document.getElementById('working_office-edit-id').value;
  const name = document.getElementById('working_office-edit-name-input').value.trim();

  if (!name) {
    setFieldError('working_office-edit-name-field', 'working_office-edit-name-error', 'Office name is required.');
    return;
  }

  const btn = document.getElementById('working_office-edit-submit-btn');
  btn.disabled = true;
  btn.textContent = 'Updating…';

  fetch(EDIT_META.working_office.updateUrl(id), {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken(),
      'X-Requested-With': 'XMLHttpRequest',
    },
    body: JSON.stringify({ name }),
  })
  .then(async r => {
    const data = await r.json().catch(() => ({}));
    if (r.status === 422) {
      if (data.errors?.name) setFieldError('working_office-edit-name-field', 'working_office-edit-name-error', data.errors.name[0]);
      if (!data.errors) showToast(data.message || 'Validation failed.', true);
      return;
    }
    if (!r.ok || !data.success) {
      showToast(data.message || 'Failed to update working office.', true);
      return;
    }
    showToast('✔ Working office updated successfully.');
    closeModal('working_office-edit-modal-overlay');
    loadedTabs.delete('working_office');
    loadTabData('working_office');
  })
  .catch(() => showToast('Network error. Please try again.', true))
  .finally(() => {
    btn.disabled = false;
    btn.textContent = 'Update Office';
  });
}

/* ══════════════════════════════════════════════════════════════════
   UPDATE USER
══════════════════════════════════════════════════════════════════ */
function submitEditUserForm(e) {
  e.preventDefault();
  clearFieldError('user-edit-name-field', 'user-edit-name-error');
  clearFieldError('user-edit-email-field', 'user-edit-email-error');
  clearFieldError('user-edit-working-office-field', 'user-edit-working-office-error');
 
  const id = document.getElementById('user-edit-id').value;
  const name = document.getElementById('user-edit-name-input').value.trim();
  const email = document.getElementById('user-edit-email-input').value.trim();
  const working_office_id = document.getElementById('user-edit-working-office-select').value;
  const status = document.getElementById('user-edit-status-checkbox').checked ? 1 : 0;
 
  const mandalCheckboxes = document.querySelectorAll('#user-edit-mandals-container input[type="checkbox"]:checked');
  const mandalIds = Array.from(mandalCheckboxes).map(cb => parseInt(cb.value));
 
  let hasError = false;
  if (!name) {
    setFieldError('user-edit-name-field', 'user-edit-name-error', 'Name is required.');
    hasError = true;
  }
  if (!email) {
    setFieldError('user-edit-email-field', 'user-edit-email-error', 'Email is required.');
    hasError = true;
  }
  if (!working_office_id) {
    setFieldError('user-edit-working-office-field', 'user-edit-working-office-error', 'Please select a working office.');
    hasError = true;
  }
  if (hasError) return;
 
  const btn = document.getElementById('user-edit-submit-btn');
  btn.disabled = true;
  btn.textContent = 'Updating…';
 
  fetch(EDIT_META.users.updateUrl(id), {
    method: 'PUT',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken(),
      'X-Requested-With': 'XMLHttpRequest',
    },
    body: JSON.stringify({ 
      name, 
      email,
      working_office_id: parseInt(working_office_id),
      status: status,
      mandal_ids: mandalIds,
    }),
  })
  .then(async r => {
    const data = await r.json().catch(() => ({}));
    if (r.status === 422) {
      if (data.errors?.name) setFieldError('user-edit-name-field', 'user-edit-name-error', data.errors.name[0]);
      if (data.errors?.email) setFieldError('user-edit-email-field', 'user-edit-email-error', data.errors.email[0]);
      if (data.errors?.working_office_id) setFieldError('user-edit-working-office-field', 'user-edit-working-office-error', data.errors.working_office_id[0]);
      if (!data.errors) showToast(data.message || 'Validation failed.', true);
      return;
    }
    if (!r.ok || !data.success) {
      showToast(data.message || 'Failed to update user.', true);
      return;
    }
    showToast('✔ User updated successfully.');
    closeModal('user-edit-modal-overlay');
    loadedTabs.delete('users');
    loadTabData('users');
  })
  .catch(() => showToast('Network error. Please try again.', true))
  .finally(() => {
    btn.disabled = false;
    btn.textContent = 'Update User';
  });
}

/* ══════════════════════════════════════════════════════════════════
   DELETE — shared confirm modal for all tabs
══════════════════════════════════════════════════════════════════ */
const DELETE_META = {
  mandal:  { url: id => `/api/admin/mandals/${id}`,           label: 'this Mandal',         reloadTab: 'mandal'  },
  village: { url: id => `/api/admin/villages/${id}`,          label: 'this Village',        reloadTab: 'village' },
  working_office: { url: id => `/api/admin/working-offices/${id}`, label: 'this Working Office', reloadTab: 'working_office' },
  users:   { url: id => `/api/admin/users/${id}`,             label: 'this User',           reloadTab: 'users'   },
};
let pendingDelete = null;

function openDeleteModal(tab, id) {
  pendingDelete = { tab, id };
  document.getElementById('delete-modal-message').textContent =
    `Are you sure you want to delete ${DELETE_META[tab].label}? This action cannot be undone.`;
  openModal('delete-modal-overlay');
}

function confirmDelete() {
  if (!pendingDelete) return;
  const { tab, id } = pendingDelete;
  const meta = DELETE_META[tab];

  const btn = document.getElementById('delete-confirm-btn');
  btn.disabled = true;
  btn.textContent = 'Deleting…';

  fetch(meta.url(id), {
    method: 'DELETE',
    headers: {
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken(),
      'X-Requested-With': 'XMLHttpRequest',
    },
  })
  .then(async r => {
    const data = await r.json().catch(() => ({}));
    if (!r.ok || !data.success) {
      showToast(data.message || 'Failed to delete.', true);
      return;
    }
    showToast(data.message || '✔ Deleted successfully.');
    closeModal('delete-modal-overlay');
    loadedTabs.delete(meta.reloadTab);
    loadTabData(meta.reloadTab);
  })
  .catch(() => showToast('Network error. Please try again.', true))
  .finally(() => {
    btn.disabled = false;
    btn.textContent = 'Delete';
    pendingDelete = null;
  });
}

/* ══════════════════════════════════════════════════════════════════
   TOAST
══════════════════════════════════════════════════════════════════ */
function showToast(msg, isError = false) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.style.display = 'block';
  t.style.borderLeftColor = isError ? '#c0392b' : '#f39c12';
  t.style.background = isError ? '#7f0000' : '#154360';
  setTimeout(() => t.style.display = 'none', 3500);
}

/* ══════════════════════════════════════════════════════════════════
   INIT — load first tab
══════════════════════════════════════════════════════════════════ */
loadTabData('mandal');
</script>

</body>
</html>