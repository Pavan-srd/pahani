<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Users Management — Admin Dashboard</title>
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

    /* ══ MODAL ══ */
    .modal-overlay{display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:500;align-items:center;justify-content:center;padding:16px;overflow-y:auto}
    .modal-overlay.show{display:flex}
    .modal-box{background:white;border-radius:4px;box-shadow:0 6px 24px rgba(0,0,0,0.18);width:100%;max-width:520px;overflow:hidden;animation:modalSlide 0.25s ease;margin:auto}
    @keyframes modalSlide{from{transform:translateY(-30px);opacity:0}}

    .modal-header{background:#eaf2f8;border-bottom:1px solid #b8d4e8;padding:12px 16px;display:flex;align-items:center;justify-content:space-between}
    .modal-header h3{font-size:12px;font-weight:bold;color:#154360;text-transform:uppercase;letter-spacing:0.4px;margin:0}
    .modal-close{background:none;border:none;font-size:16px;color:#999;cursor:pointer;padding:0;width:24px;height:24px;display:flex;align-items:center;justify-content:center}
    .modal-close:hover{color:#154360}

    .modal-body{padding:16px;max-height:600px;overflow-y:auto}
    .modal-footer{padding:12px 16px;border-top:1px solid #dce8f0;background:#fafbfc;display:flex;gap:8px;justify-content:flex-end}

    .form-field{margin-bottom:14px;display:flex;flex-direction:column;gap:4px}
    .form-field label{font-size:10px;font-weight:bold;color:#154360;text-transform:uppercase;letter-spacing:0.3px}
    .form-field input,.form-field select{padding:8px;border:1px solid #b0c4d8;border-radius:2px;font-size:11px}
    .form-field input:focus,.form-field select:focus{outline:none;border-color:#154360;background:#f0f8ff}
    .form-field.has-error input,.form-field.has-error select{border-color:#c0392b;background:#fef5f5}
    .field-error{color:#c0392b;font-size:10px;display:none}
    .form-field.has-error .field-error{display:block}

    .form-field-sub{font-size:9px;color:#888;margin-top:-2px;font-weight:normal}

    .checkbox-group{display:flex;flex-direction:column;gap:6px;max-height:200px;overflow-y:auto;padding:8px;background:#f9fbfc;border:1px solid #d5e8f5;border-radius:3px}
    .checkbox-item{display:flex;align-items:center;gap:8px;padding:6px;background:white;border:1px solid #e0e8f0;border-radius:2px}
    .checkbox-item input{width:16px;height:16px;cursor:pointer;flex-shrink:0}
    .checkbox-item label{margin:0;font-weight:normal;font-size:11px;cursor:pointer}

    .section-divider{border-top:1px solid #d5e8f5;margin:12px 0;padding-top:12px}

    .btn-secondary-sm{background:#f0f4f8;color:#154360;border:1px solid #b0c4d8;padding:8px 16px;font-size:11px;font-weight:bold;cursor:pointer;border-radius:2px;transition:background 0.15s}
    .btn-secondary-sm:hover{background:#eaf2f8}

    .btn-primary-sm{background:#154360;color:white;border:none;padding:8px 16px;font-size:11px;font-weight:bold;cursor:pointer;border-radius:2px;transition:background 0.15s}
    .btn-primary-sm:hover{background:#1a6fa8}
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

        <div class="nav-section-label">Access Control</div>
        <a class="sb-item" href="{{ route('admin.pahani-management.index') }}">
          <span class="sb-icon">👤</span> Pahani Management
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
            <div class="topbar-title">👤 Users Management</div>
            <div class="topbar-breadcrumb">Admin › Access Control › Users</div>
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

        {{-- ═══ USERS MANAGEMENT ═══ --}}
        <div class="page-heading">
          <h2>👤 Users Management</h2>
          <div class="ph-sub">Manage user accounts and permissions</div>
        </div>

        <div class="toolbar">
          <div class="search-box">
            🔎 <input type="text" id="users-search" placeholder="Search User by name or email…" oninput="filterAndPaginate()">
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
          <div class="pagination-container" id="users-pagination">
            <div class="pagination-info">
              Showing <strong id="users-info-from">1</strong>–<strong id="users-info-to">10</strong> of <strong id="users-info-total">0</strong>
            </div>
            <div class="pagination-nav" id="users-nav"></div>
          </div>
        </div>

      </div>
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
        <input type="hidden" id="user-edit-id">
        <div class="modal-body">
          
          {{-- ═══ BASIC INFO ═══ --}}
          <div class="form-field" id="user-edit-name-field">
            <label>Name *</label>
            <input type="text" id="user-edit-name-input" required>
            <div class="field-error" id="user-edit-name-error"></div>
          </div>
  
          <div class="form-field" id="user-edit-email-field">
            <label>Email *</label>
            <input type="email" id="user-edit-email-input" required>
            <div class="field-error" id="user-edit-email-error"></div>
          </div>
  
          <div class="form-field" id="user-edit-office-field">
            <label>Working Office *</label>
            <select id="user-edit-office-select" required></select>
            <div class="field-error" id="user-edit-office-error"></div>
          </div>
  
          <div class="form-field" id="user-edit-status-field">
            <label>Status</label>
            <select id="user-edit-status-select">
              <option value="1">✔ Active</option>
              <option value="0">— Inactive</option>
            </select>
          </div>
  
          {{-- ═══ UPLOAD MANDALS PERMISSION ═══ --}}
          <div class="section-divider"></div>
          <div class="form-field" id="user-edit-upload-mandals-field">
            <label>📤 Upload Mandals</label>
            <div class="form-field-sub">Select mandals where user can upload documents</div>
            <div class="checkbox-group" id="user-edit-upload-mandals-list">
              <div style="text-align:center; padding: 10px; color: #999;">Loading mandals…</div>
            </div>
            <div class="field-error" id="user-edit-upload-mandals-error"></div>
          </div>
  
          {{-- ═══ VIEW MANDALS PERMISSION ═══ --}}
          <div class="section-divider"></div>
          <div class="form-field" id="user-edit-view-mandals-field">
            <label>👁 View Mandals</label>
            <div class="form-field-sub">Select mandals where user can view documents</div>
            <div class="checkbox-group" id="user-edit-view-mandals-list">
              <div style="text-align:center; padding: 10px; color: #999;">Loading mandals…</div>
            </div>
            <div class="field-error" id="user-edit-view-mandals-error"></div>
          </div>
  
          {{-- ═══ EDIT MANDALS PERMISSION ═══ --}}
          <div class="section-divider"></div>
          <div class="form-field" id="user-edit-edit-mandals-field">
            <label>✎ Edit Mandals</label>
            <div class="form-field-sub">Select mandals where user can edit documents</div>
            <div class="checkbox-group" id="user-edit-edit-mandals-list">
              <div style="text-align:center; padding: 10px; color: #999;">Loading mandals…</div>
            </div>
            <div class="field-error" id="user-edit-edit-mandals-error"></div>
          </div>
  
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary-sm" onclick="closeModal('user-edit-modal-overlay')">Cancel</button>
          <button type="submit" class="btn-primary-sm" id="user-edit-submit-btn">Update User</button>
        </div>
      </form>
    </div>
  </div>

  <div id="toast" class="toast"></div>

<script>
/* ══════════════════════════════════════════════════════════════════
   PAGINATION & SEARCH
══════════════════════════════════════════════════════════════════ */
const PAGINATION_SIZE = 10;
const paginationState = {
  currentPage: 1,
  totalPages: 1,
  allData: [],
  filteredData: [],
};

let dataLoaded = false;

/* ══════════════════════════════════════════════════════════════════
   PAGE LOAD
══════════════════════════════════════════════════════════════════ */
document.addEventListener('DOMContentLoaded', () => {
  loadUsersData();
});

/* ══════════════════════════════════════════════════════════════════
   LOAD USERS DATA
══════════════════════════════════════════════════════════════════ */
function loadUsersData() {
  const loadingEl = document.getElementById('users-loading');
  const tableEl   = document.getElementById('users-table');
  const emptyEl   = document.getElementById('users-empty');

  loadingEl.classList.add('show');
  tableEl.style.display = 'none';
  emptyEl.style.display = 'none';

  fetch('/api/admin/users', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(r => r.json())
    .then(data => {
      dataLoaded = true;
      paginationState.allData = Array.isArray(data) ? data : (data.data || []);
      paginationState.filteredData = [...paginationState.allData];
      paginationState.currentPage = 1;
      paginationState.totalPages = Math.ceil(paginationState.filteredData.length / PAGINATION_SIZE) || 1;
      renderTable();
      updatePagination();
    })
    .catch(() => showToast('Failed to load users.', true))
    .finally(() => loadingEl.classList.remove('show'));
}

/* ══════════════════════════════════════════════════════════════════
   RENDER TABLE
══════════════════════════════════════════════════════════════════ */
function renderTable() {
  const tbody   = document.getElementById('users-tbody');
  const tableEl = document.getElementById('users-table');
  const emptyEl = document.getElementById('users-empty');
  const state   = paginationState;

  tbody.innerHTML = '';

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

    tr.innerHTML = `
      <td>${globalIndex}</td>
      <td>${escapeHtml(row.name)}</td>
      <td>${escapeHtml(row.email)}</td>
      <td>${escapeHtml(row.working_office_name ?? '—')}</td>
      <td>${row.status ? '<span class="pill pill-active">✔ Active</span>' : '<span class="pill pill-inactive">— Inactive</span>'}</td>
      <td style="text-align:center">
        <div class="row-actions" style="justify-content:center">
          <button class="btn-icon" title="Edit" onclick="openEditUserModal(${row.id})">✎</button>
        </div>
      </td>`;
    tbody.appendChild(tr);
  });
}

/* ══════════════════════════════════════════════════════════════════
   PAGINATION
══════════════════════════════════════════════════════════════════ */
function updatePagination() {
  const state = paginationState;
  const paginationEl = document.getElementById('users-pagination');
  const navEl = document.getElementById('users-nav');

  if (state.filteredData.length === 0) {
    paginationEl.style.display = 'none';
    return;
  }

  paginationEl.style.display = 'flex';

  const start = (state.currentPage - 1) * PAGINATION_SIZE + 1;
  const end = Math.min(state.currentPage * PAGINATION_SIZE, state.filteredData.length);
  document.getElementById('users-info-from').textContent = start;
  document.getElementById('users-info-to').textContent = end;
  document.getElementById('users-info-total').textContent = state.filteredData.length;

  navEl.innerHTML = '';
  for (let p = 1; p <= state.totalPages; p++) {
    const btn = document.createElement('button');
    btn.type = 'button';
    btn.textContent = p;
    btn.disabled = state.currentPage === p;
    btn.classList.toggle('active', state.currentPage === p);
    btn.onclick = () => goToPage(p);
    navEl.appendChild(btn);
  }
}

function goToPage(page) {
  paginationState.currentPage = page;
  renderTable();
  updatePagination();
}

/* ══════════════════════════════════════════════════════════════════
   SEARCH & FILTER
══════════════════════════════════════════════════════════════════ */
function filterAndPaginate() {
  if (!dataLoaded) return;
  const query = document.getElementById('users-search').value.toLowerCase();
  paginationState.filteredData = paginationState.allData.filter(u =>
    u.name.toLowerCase().includes(query) || u.email.toLowerCase().includes(query)
  );
  paginationState.currentPage = 1;
  paginationState.totalPages = Math.ceil(paginationState.filteredData.length / PAGINATION_SIZE) || 1;
  renderTable();
  updatePagination();
}

/* ══════════════════════════════════════════════════════════════════
   POPULATE WORKING OFFICES DROPDOWN
══════════════════════════════════════════════════════════════════ */
function populateOfficesDropdown(selectId, selectedId = null) {
  const sel = document.getElementById(selectId);
  if (!sel) return;
  
  sel.innerHTML = '<option value="">Loading offices…</option>';
 
  fetch('/api/admin/working-offices', { 
    headers: { 'X-Requested-With': 'XMLHttpRequest' } 
  })
  .then(r => r.json())
  .then(offices => {
    sel.innerHTML = '<option value="">— Select Office —</option>';
    const data = Array.isArray(offices) ? offices : (offices.data || []);
    data.forEach(o => {
      const opt = document.createElement('option');
      opt.value = o.id;
      opt.textContent = o.name;
      sel.appendChild(opt);
    });
    if (selectedId !== null) {
      sel.value = selectedId;
    }
  })
  .catch(err => {
    console.error('Failed to load offices:', err);
    sel.innerHTML = '<option value="">Failed to load offices</option>';
    showToast('Failed to load offices for dropdown.', true);
  });
}

/* ══════════════════════════════════════════════════════════════════
   POPULATE MANDALS CHECKBOXES FOR 3 PERMISSION TYPES
══════════════════════════════════════════════════════════════════ */
function populateMandalsCheckboxes(permissionType, selectedMandals = []) {
  let containerId;
  
  switch(permissionType) {
    case 'upload':
      containerId = 'user-edit-upload-mandals-list';
      break;
    case 'view':
      containerId = 'user-edit-view-mandals-list';
      break;
    case 'edit':
      containerId = 'user-edit-edit-mandals-list';
      break;
    default:
      console.error(`Unknown permission type: ${permissionType}`);
      return;
  }
 
  const container = document.getElementById(containerId);
  if (!container) {
    console.error(`Container not found: ${containerId}`);
    return;
  }
  
  container.innerHTML = '<div style="text-align:center; padding: 10px; color: #999;">Loading mandals…</div>';
 
  // Normalize selected mandals to ensure they're all integers
  const normalizedSelected = normalizeMandalIds(selectedMandals);
  
  console.log(`[${permissionType}] Loading mandals with pre-selected IDs:`, normalizedSelected);
 
  fetch('/api/admin/mandals', { 
    headers: { 'X-Requested-With': 'XMLHttpRequest' } 
  })
  .then(r => {
    if (!r.ok) throw new Error(`HTTP ${r.status}`);
    return r.json();
  })
  .then(mandals => {
    const data = Array.isArray(mandals) ? mandals : (mandals.data || []);
    
    if (data.length === 0) {
      container.innerHTML = '<div style="text-align:center; padding: 10px; color: #999;">No mandals available</div>';
      return;
    }
 
    container.innerHTML = '';
    let checkedCount = 0;
    
    data.forEach(m => {
      const mandalId = parseInt(m.id, 10);
      const isChecked = normalizedSelected.includes(mandalId);
      if (isChecked) checkedCount++;
      
      const item = document.createElement('div');
      item.className = 'checkbox-item';
      
      const fieldId = `mandal-${permissionType}-${mandalId}`;
      const fieldClass = `mandal-${permissionType}`;
      
      item.innerHTML = `
        <input type="checkbox" 
               id="${fieldId}" 
               class="${fieldClass}" 
               value="${mandalId}" 
               ${isChecked ? 'checked' : ''}>
        <label for="${fieldId}">${escapeHtml(m.name)}</label>
      `;
      container.appendChild(item);
    });
 
    console.log(`[${permissionType}] Pre-selected ${checkedCount} out of ${data.length} mandals`);
  })
  .catch(err => {
    console.error(`Failed to load ${permissionType} mandals:`, err);
    container.innerHTML = '<div style="text-align:center; padding: 10px; color: #c0392b;">Failed to load mandals</div>';
    showToast('Failed to load mandals for ' + permissionType + ' permission.', true);
  });
}
 
/* ══════════════════════════════════════════════════════════════════
   OPEN EDIT USER MODAL
══════════════════════════════════════════════════════════════════ */
function openEditUserModal(id) {
  // Show loading state by setting the ID first
  document.getElementById('user-edit-id').value = id;
  openModal('user-edit-modal-overlay');
  
  // Fetch the specific user with permissions from the API
  fetch(`/api/admin/users/${id}`, {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => {
    if (!r.ok) throw new Error(`HTTP ${r.status}`);
    return r.json();
  })
  .then(data => {
    if (!data.success || !data.user) {
      showToast('Failed to load user data.', true);
      closeModal('user-edit-modal-overlay');
      return;
    }
    
    const user = data.user;
    console.log('User data loaded:', user);
    
    // Populate basic form fields
    document.getElementById('user-edit-name-input').value = user.name || '';
    document.getElementById('user-edit-email-input').value = user.email || '';
    document.getElementById('user-edit-status-select').value = user.status ? '1' : '0';
    
    // Populate working office dropdown
    populateOfficesDropdown('user-edit-office-select', user.working_office_id);
    
    // Extract and normalize permission IDs
    let uploadMandalIds = [];
    let viewMandalIds = [];
    let editMandalIds = [];
    
    if (user.permissions) {
      uploadMandalIds = normalizeMandalIds(user.permissions.upload_mandal_ids);
      viewMandalIds = normalizeMandalIds(user.permissions.view_mandal_ids);
      editMandalIds = normalizeMandalIds(user.permissions.edit_mandal_ids);
    }
    
    console.log('Normalized permissions:', {
      uploadMandalIds,
      viewMandalIds,
      editMandalIds
    });
    
    // Populate mandal checkboxes with pre-selected values
    populateMandalsCheckboxes('upload', uploadMandalIds);
    populateMandalsCheckboxes('view', viewMandalIds);
    populateMandalsCheckboxes('edit', editMandalIds);
    
    // Clear any previous error messages
    clearFieldError('user-edit-name-field', 'user-edit-name-error');
    clearFieldError('user-edit-email-field', 'user-edit-email-error');
    clearFieldError('user-edit-office-field', 'user-edit-office-error');
  })
  .catch(err => {
    console.error('Failed to load user:', err);
    showToast('Failed to load user data. Please try again.', true);
    closeModal('user-edit-modal-overlay');
  });
}

function normalizeMandalIds(input) {
  if (!input) return [];
  
  if (!Array.isArray(input)) {
    return [];
  }
  
  return input
    .map(id => {
      const parsed = parseInt(id, 10);
      return isNaN(parsed) ? null : parsed;
    })
    .filter(id => id !== null);
}
/* ══════════════════════════════════════════════════════════════════
   SUBMIT EDIT USER FORM
══════════════════════════════════════════════════════════════════ */
function submitEditUserForm(e) {
  e.preventDefault();
 
  // Clear previous errors
  clearFieldError('user-edit-name-field', 'user-edit-name-error');
  clearFieldError('user-edit-email-field', 'user-edit-email-error');
  clearFieldError('user-edit-office-field', 'user-edit-office-error');
 
  // Get form values
  const id = document.getElementById('user-edit-id').value;
  const name = document.getElementById('user-edit-name-input').value.trim();
  const email = document.getElementById('user-edit-email-input').value.trim();
  const officeId = document.getElementById('user-edit-office-select').value;
  const status = parseInt(document.getElementById('user-edit-status-select').value, 10);
  
  // Get selected mandals for each permission type
  const uploadMandalIds = Array.from(document.querySelectorAll('input.mandal-upload:checked'))
    .map(checkbox => parseInt(checkbox.value, 10));
  
  const viewMandalIds = Array.from(document.querySelectorAll('input.mandal-view:checked'))
    .map(checkbox => parseInt(checkbox.value, 10));
  
  const editMandalIds = Array.from(document.querySelectorAll('input.mandal-edit:checked'))
    .map(checkbox => parseInt(checkbox.value, 10));
 
  console.log('Form submission data:', {
    id,
    name,
    email,
    officeId,
    status,
    uploadMandalIds,
    viewMandalIds,
    editMandalIds
  });
 
  // Validate required fields
  let hasError = false;
  if (!name) {
    setFieldError('user-edit-name-field', 'user-edit-name-error', 'Name is required.');
    hasError = true;
  }
  if (!email) {
    setFieldError('user-edit-email-field', 'user-edit-email-error', 'Email is required.');
    hasError = true;
  }
  if (!officeId) {
    setFieldError('user-edit-office-field', 'user-edit-office-error', 'Please select an office.');
    hasError = true;
  }
  if (hasError) return;
 
  // Disable submit button and show loading state
  const btn = document.getElementById('user-edit-submit-btn');
  btn.disabled = true;
  btn.textContent = 'Updating…';
 
  // Send update request
  fetch(`/api/admin/users/${id}`, {
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
      working_office_id: officeId,
      status,
      upload_mandal_ids: uploadMandalIds,
      view_mandal_ids: viewMandalIds,
      edit_mandal_ids: editMandalIds,
    }),
  })
  .then(async r => {
    const data = await r.json().catch(() => ({}));
 
    // Handle validation errors (422)
    if (r.status === 422) {
      if (data.errors?.name) setFieldError('user-edit-name-field', 'user-edit-name-error', data.errors.name[0]);
      if (data.errors?.email) setFieldError('user-edit-email-field', 'user-edit-email-error', data.errors.email[0]);
      if (data.errors?.working_office_id) setFieldError('user-edit-office-field', 'user-edit-office-error', data.errors.working_office_id[0]);
      if (!data.errors) showToast(data.message || 'Validation failed.', true);
      return;
    }
    
    // Handle general errors
    if (!r.ok || !data.success) {
      showToast(data.message || 'Failed to update user.', true);
      return;
    }
 
    // Success
    showToast('✔ User updated successfully.');
    closeModal('user-edit-modal-overlay');
    loadUsersData(); // Reload the users list
  })
  .catch(err => {
    console.error('Update error:', err);
    showToast('Network error. Please try again.', true);
  })
  .finally(() => {
    // Re-enable submit button
    btn.disabled = false;
    btn.textContent = 'Update User';
  });
}
/* ══════════════════════════════════════════════════════════════════
   UTILITY FUNCTIONS
══════════════════════════════════════════════════════════════════ */
function csrfToken() {
  return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

function openModal(id) {
  document.getElementById(id).classList.add('show');
}

function closeModal(id) {
  document.getElementById(id).classList.remove('show');
}

function clearFieldError(fieldId, errorId) {
  const field = document.getElementById(fieldId);
  if (field) {
    field.classList.remove('has-error');
    const error = document.getElementById(errorId);
    if (error) error.textContent = '';
  }
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
  setTimeout(() => {
    t.classList.remove('show');
    t.style.display = 'none';
  }, 4500);
}

function toggleProfileMenu(event) {
  event.stopPropagation();
  const wrap = document.getElementById('profile-wrap');
  wrap.classList.toggle('open');
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