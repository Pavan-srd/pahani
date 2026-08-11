<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Working Office Management — Admin Dashboard</title>
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

    .row-actions{display:flex;gap:6px}
    .btn-icon{border:none;background:#eaf2f8;color:#154360;cursor:pointer;width:26px;height:26px;border-radius:3px;font-size:11px;display:flex;align-items:center;justify-content:center;transition:background 0.15s}
    .btn-icon:hover{background:#d6eaf8}
    .btn-icon.danger{background:#fdecea;color:#c0392b}
    .btn-icon.danger:hover{background:#f8d0cb}

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
    .modal-overlay{display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,0.5);z-index:500;align-items:center;justify-content:center;padding:16px}
    .modal-overlay.show{display:flex}
    .modal-box{background:white;border-radius:4px;box-shadow:0 6px 24px rgba(0,0,0,0.18);width:100%;max-width:420px;overflow:hidden;animation:modalSlide 0.25s ease}
    @keyframes modalSlide{from{transform:translateY(-30px);opacity:0}}

    .modal-header{background:#eaf2f8;border-bottom:1px solid #b8d4e8;padding:12px 16px;display:flex;align-items:center;justify-content:space-between}
    .modal-header h3{font-size:12px;font-weight:bold;color:#154360;text-transform:uppercase;letter-spacing:0.4px;margin:0}
    .modal-close{background:none;border:none;font-size:16px;color:#999;cursor:pointer;padding:0;width:24px;height:24px;display:flex;align-items:center;justify-content:center}
    .modal-close:hover{color:#154360}

    .modal-body{padding:16px}
    .modal-footer{padding:12px 16px;border-top:1px solid #dce8f0;background:#fafbfc;display:flex;gap:8px;justify-content:flex-end}

    .form-field{margin-bottom:14px;display:flex;flex-direction:column;gap:4px}
    .form-field label{font-size:10px;font-weight:bold;color:#154360;text-transform:uppercase;letter-spacing:0.3px}
    .form-field input,.form-field select{padding:8px;border:1px solid #b0c4d8;border-radius:2px;font-size:11px}
    .form-field input:focus,.form-field select:focus{outline:none;border-color:#154360;background:#f0f8ff}
    .form-field.has-error input,.form-field.has-error select{border-color:#c0392b;background:#fef5f5}
    .field-error{color:#c0392b;font-size:10px;display:none}
    .form-field.has-error .field-error{display:block}

    .btn-secondary-sm{background:#f0f4f8;color:#154360;border:1px solid #b0c4d8;padding:8px 16px;font-size:11px;font-weight:bold;cursor:pointer;border-radius:2px;transition:background 0.15s}
    .btn-secondary-sm:hover{background:#eaf2f8}
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
        <a class="sb-item active" href="{{ route('admin.working-offices.index') }}">
          <span class="sb-icon">🏢</span> Working Office
        </a>

        <div class="nav-section-label">Access Control</div>
        <a class="sb-item" href="{{ route('admin.pahani-management.index') }}">
          <span class="sb-icon">👤</span> Pahani Management
        </a>
        <a class="sb-item" href="{{ route('admin.users.index') }}">
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
            <div class="topbar-title">🏢 Working Office Management</div>
            <div class="topbar-breadcrumb">Admin › Master Data › Working Office</div>
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
              <a class="profile-menu-item" href="#" onclick="event.preventDefault();">
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

      {{-- Hidden logout form --}}
      <form id="logout-form" action="{{ route('logout') ?? '#' }}" method="POST" style="display:none">
        @csrf
      </form>

      {{-- ── CONTENT ── --}}
      <div class="content">

        {{-- ═══ WORKING OFFICE MANAGEMENT ═══ --}}
        <div class="page-heading">
          <h2>🏢 Working Office Management</h2>
          <div class="ph-sub">Add, edit, and manage working offices</div>
        </div>

        <div class="toolbar">
          <div class="search-box">
            🔎 <input type="text" id="office-search" placeholder="Search Working Office by name…" oninput="filterAndPaginate()">
          </div>
          <button class="btn-primary-sm" onclick="openAddOfficeModal()">
            + Add Working Office
          </button>
        </div>

        <div class="section-card">
          <div class="loading-bar" id="office-loading"><div class="spinner"></div> Loading working offices…</div>
          <table class="data-table" id="office-table" style="display:none">
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
            <tbody id="office-tbody"></tbody>
          </table>
          <div class="empty-state" id="office-empty" style="display:none">
            <div class="es-icon">🏢</div> No working offices found.
          </div>
          <div class="pagination-container" id="office-pagination">
            <div class="pagination-info">
              Showing <strong id="office-info-from">1</strong>–<strong id="office-info-to">10</strong> of <strong id="office-info-total">0</strong>
            </div>
            <div class="pagination-nav" id="office-nav"></div>
          </div>
        </div>

      </div>
    </div>
  </div>

  {{-- ══════════════ ADD OFFICE MODAL ══════════════ --}}
  <div class="modal-overlay" id="office-modal-overlay" onclick="if(event.target===this) closeModal('office-modal-overlay')">
    <div class="modal-box">
      <div class="modal-header">
        <h3>+ Add Working Office</h3>
        <button class="modal-close" onclick="closeModal('office-modal-overlay')">✕</button>
      </div>
      <form id="office-add-form" onsubmit="submitOfficeForm(event)">
        <div class="modal-body">
          <div class="form-field" id="office-name-field">
            <label>Office Name *</label>
            <input type="text" id="office-name-input" placeholder="e.g., Sangareddy Revenue Office" required>
            <div class="field-error" id="office-name-error"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary-sm" onclick="closeModal('office-modal-overlay')">Cancel</button>
          <button type="submit" class="btn-primary-sm" id="office-submit-btn">Save Office</button>
        </div>
      </form>
    </div>
  </div>

  {{-- ══════════════ EDIT OFFICE MODAL ══════════════ --}}
  <div class="modal-overlay" id="office-edit-modal-overlay" onclick="if(event.target===this) closeModal('office-edit-modal-overlay')">
    <div class="modal-box">
      <div class="modal-header">
        <h3>✎ Edit Working Office</h3>
        <button class="modal-close" onclick="closeModal('office-edit-modal-overlay')">✕</button>
      </div>
      <form id="office-edit-form" onsubmit="submitEditOfficeForm(event)">
        <input type="hidden" id="office-edit-id">
        <div class="modal-body">
          <div class="form-field" id="office-edit-name-field">
            <label>Office Name *</label>
            <input type="text" id="office-edit-name-input" placeholder="e.g., Sangareddy Revenue Office" required>
            <div class="field-error" id="office-edit-name-error"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn-secondary-sm" onclick="closeModal('office-edit-modal-overlay')">Cancel</button>
          <button type="submit" class="btn-primary-sm" id="office-edit-submit-btn">Update Office</button>
        </div>
      </form>
    </div>
  </div>

  {{-- ══════════════ DELETE CONFIRM MODAL ══════════════ --}}
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

  <div id="toast" class="toast"></div>

<script>
/* ══════════════════════════════════════════════════════════════════
   PAGINATION STATE
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
  loadOfficeData();
});

/* ══════════════════════════════════════════════════════════════════
   LOAD WORKING OFFICE DATA
══════════════════════════════════════════════════════════════════ */
function loadOfficeData() {
  const loadingEl = document.getElementById('office-loading');
  const tableEl   = document.getElementById('office-table');
  const emptyEl   = document.getElementById('office-empty');

  loadingEl.classList.add('show');
  tableEl.style.display = 'none';
  emptyEl.style.display = 'none';

  fetch('/api/admin/working-offices', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
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
    .catch(() => showToast('Failed to load working offices.', true))
    .finally(() => loadingEl.classList.remove('show'));
}

/* ══════════════════════════════════════════════════════════════════
   RENDER TABLE
══════════════════════════════════════════════════════════════════ */
function renderTable() {
  const tbody   = document.getElementById('office-tbody');
  const tableEl = document.getElementById('office-table');
  const emptyEl = document.getElementById('office-empty');
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
      <td>${escapeHtml(row.slug)}</td>
      <td>${formatDate(row.created_at)}</td>
      <td>${row.is_active ? '<span class="pill pill-active">✔ Active</span>' : '<span class="pill pill-inactive">— Inactive</span>'}</td>
      <td style="text-align:center">
        <div class="row-actions" style="justify-content:center">
          <button class="btn-icon" title="Edit" onclick="openEditOfficeModal(${row.id})">✎</button>
          <button class="btn-icon danger" title="Delete" onclick="openDeleteModal(${row.id})">✕</button>
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
  const paginationEl = document.getElementById('office-pagination');
  const navEl = document.getElementById('office-nav');

  if (state.filteredData.length === 0) {
    paginationEl.style.display = 'none';
    return;
  }

  paginationEl.style.display = 'flex';

  const start = (state.currentPage - 1) * PAGINATION_SIZE + 1;
  const end = Math.min(state.currentPage * PAGINATION_SIZE, state.filteredData.length);
  document.getElementById('office-info-from').textContent = start;
  document.getElementById('office-info-to').textContent = end;
  document.getElementById('office-info-total').textContent = state.filteredData.length;

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
  const query = document.getElementById('office-search').value.toLowerCase();
  paginationState.filteredData = paginationState.allData.filter(o =>
    o.name.toLowerCase().includes(query) || o.slug.toLowerCase().includes(query)
  );
  paginationState.currentPage = 1;
  paginationState.totalPages = Math.ceil(paginationState.filteredData.length / PAGINATION_SIZE) || 1;
  renderTable();
  updatePagination();
}

/* ══════════════════════════════════════════════════════════════════
   ADD OFFICE
══════════════════════════════════════════════════════════════════ */
function openAddOfficeModal() {
  document.getElementById('office-add-form').reset();
  clearFieldError('office-name-field', 'office-name-error');
  openModal('office-modal-overlay');
  document.getElementById('office-name-input').focus();
}

function submitOfficeForm(e) {
  e.preventDefault();
  clearFieldError('office-name-field', 'office-name-error');

  const name = document.getElementById('office-name-input').value.trim();
  if (!name) {
    setFieldError('office-name-field', 'office-name-error', 'Office name is required.');
    return;
  }

  const btn = document.getElementById('office-submit-btn');
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
      setFieldError('office-name-field', 'office-name-error', msg);
      return;
    }
    if (!r.ok || !data.success) {
      showToast(data.message || 'Failed to add working office.', true);
      return;
    }

    showToast('✔ Working office added successfully.');
    closeModal('office-modal-overlay');
    loadOfficeData();
  })
  .catch(() => showToast('Network error. Please try again.', true))
  .finally(() => {
    btn.disabled = false;
    btn.textContent = 'Save Office';
  });
}

/* ══════════════════════════════════════════════════════════════════
   EDIT OFFICE
══════════════════════════════════════════════════════════════════ */
function openEditOfficeModal(id) {
  const office = paginationState.allData.find(o => o.id === id);
  if (!office) return;

  document.getElementById('office-edit-id').value = id;
  document.getElementById('office-edit-name-input').value = office.name;
  clearFieldError('office-edit-name-field', 'office-edit-name-error');
  openModal('office-edit-modal-overlay');
  document.getElementById('office-edit-name-input').focus();
}

function submitEditOfficeForm(e) {
  e.preventDefault();
  clearFieldError('office-edit-name-field', 'office-edit-name-error');

  const id = document.getElementById('office-edit-id').value;
  const name = document.getElementById('office-edit-name-input').value.trim();

  if (!name) {
    setFieldError('office-edit-name-field', 'office-edit-name-error', 'Office name is required.');
    return;
  }

  const btn = document.getElementById('office-edit-submit-btn');
  btn.disabled = true;
  btn.textContent = 'Updating…';

  fetch(`/api/admin/working-offices/${id}`, {
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
      const msg = data.errors?.name?.[0] || data.message || 'Validation failed.';
      setFieldError('office-edit-name-field', 'office-edit-name-error', msg);
      return;
    }
    if (!r.ok || !data.success) {
      showToast(data.message || 'Failed to update working office.', true);
      return;
    }

    showToast('✔ Working office updated successfully.');
    closeModal('office-edit-modal-overlay');
    loadOfficeData();
  })
  .catch(() => showToast('Network error. Please try again.', true))
  .finally(() => {
    btn.disabled = false;
    btn.textContent = 'Update Office';
  });
}

/* ══════════════════════════════════════════════════════════════════
   DELETE OFFICE
══════════════════════════════════════════════════════════════════ */
let pendingDelete = null;

function openDeleteModal(id) {
  const office = paginationState.allData.find(o => o.id === id);
  if (!office) return;
  
  pendingDelete = { type: 'office', id: id, name: office.name };
  document.getElementById('delete-modal-message').textContent = `Are you sure you want to delete the working office "${office.name}"? This action cannot be undone.`;
  openModal('delete-modal-overlay');
}

function confirmDelete() {
  if (!pendingDelete) return;

  const btn = document.getElementById('delete-confirm-btn');
  btn.disabled = true;
  btn.textContent = 'Deleting…';

  fetch(`/api/admin/working-offices/${pendingDelete.id}`, {
    method: 'DELETE',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken(),
      'X-Requested-With': 'XMLHttpRequest',
    },
  })
  .then(async r => {
    const data = await r.json().catch(() => ({}));

    if (!r.ok || !data.success) {
      showToast(data.message || 'Failed to delete working office.', true);
      return;
    }

    showToast('✔ Working office deleted successfully.');
    closeModal('delete-modal-overlay');
    loadOfficeData();
  })
  .catch(() => showToast('Network error. Please try again.', true))
  .finally(() => {
    btn.disabled = false;
    btn.textContent = 'Delete';
    pendingDelete = null;
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
  document.getElementById(fieldId).classList.remove('has-error');
  document.getElementById(errorId).textContent = '';
}

function setFieldError(fieldId, errorId, message) {
  document.getElementById(fieldId).classList.add('has-error');
  document.getElementById(errorId).textContent = message;
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