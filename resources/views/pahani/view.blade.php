<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Pahani — View Records — Land Record Digitalization</title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:Arial,sans-serif;font-size:12px;background:#f0f4f8;color:#1a1a2e}
    .portal-wrap{min-height:100vh}

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

    /* ── NAV ── */
    .page-nav{background:#154360;border-bottom:2px solid #f39c12;display:flex;align-items:center;gap:0;padding:0 20px;font-size:11px}
    .nav-item{color:#aed6f1;padding:8px 14px;cursor:pointer;border-right:1px solid rgba(255,255,255,0.1);transition:background 0.15s;text-decoration:none;display:inline-block}
    .nav-item:hover{background:rgba(255,255,255,0.1);color:white}
    .nav-item.active{background:#f39c12;color:#1a1a2e;font-weight:bold}

    /* ── LAYOUT ── */
    .main-body{padding:16px 20px;max-width:960px;margin:0 auto}
    .page-heading{background:white;border:1px solid #d5e8f5;border-left:4px solid #154360;padding:10px 16px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between}
    .page-heading h2{font-size:13px;font-weight:bold;color:#154360;text-transform:uppercase;letter-spacing:0.5px}
    .breadcrumb{font-size:10px;color:#666;margin-bottom:10px;display:flex;align-items:center;gap:4px}
    .breadcrumb a{color:#154360;text-decoration:none}
    .breadcrumb a:hover{text-decoration:underline}
    .status-dot{width:8px;height:8px;border-radius:50%;background:#27ae60;display:inline-block;margin-right:4px}

    /* ── ALERTS ── */
    .alert{padding:9px 12px;border-radius:2px;font-size:11px;margin-bottom:12px;border-left:4px solid}
    .alert-success{background:#e8f5e9;border-color:#27ae60;color:#1b5e20}
    .alert-error{background:#fdecea;border-color:#c0392b;color:#7f0000}
    .notice-bar{background:#fff3cd;border:1px solid #ffc107;border-left:4px solid #e67e22;padding:8px 12px;font-size:10px;color:#7d6608;margin-bottom:14px;display:flex;align-items:flex-start;gap:8px}

    /* ── SECTION CARDS ── */
    .section-card{background:white;border:1px solid #d0dde8;margin-bottom:14px;border-radius:2px;overflow:hidden}
    .section-header{background:#154360;color:white;padding:8px 14px;font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;display:flex;align-items:center;gap:8px}
    .section-header .sec-num{background:#f39c12;color:#1a1a2e;width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:bold;flex-shrink:0}
    .section-body{padding:14px}

    /* ── FORM FIELDS ── */
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px}
    .field-group{display:flex;flex-direction:column;gap:4px}
    .field-label{font-size:11px;font-weight:bold;color:#1a3a5c;text-transform:uppercase;letter-spacing:0.3px}
    .field-label .req{color:#c0392b;margin-left:2px}
    .field-hint{font-size:10px;color:#888}
    select{width:100%;padding:6px 8px;border:1px solid #b0c4d8;border-radius:2px;font-size:11px;color:#1a1a2e;background:#f8fbfd;outline:none;transition:border-color 0.15s}
    select:focus{border-color:#154360;background:white;box-shadow:0 0 0 2px rgba(21,67,96,0.1)}
    select:disabled{opacity:0.55;cursor:not-allowed}

    /* ── LOADING SPINNER ── */
    .loading-bar{display:none;align-items:center;gap:8px;padding:8px 0;font-size:11px;color:#154360}
    .loading-bar.show{display:flex}
    .spinner{width:14px;height:14px;border:2px solid #d0dde8;border-top-color:#154360;border-radius:50%;animation:spin 0.7s linear infinite}
    @keyframes spin{to{transform:rotate(360deg)}}

    /* ── EXISTING RECORDS BADGE ── */
    .existing-badge{display:inline-flex;align-items:center;gap:5px;background:#eaf2f8;border:1px solid #b8d4e8;border-radius:2px;padding:3px 8px;font-size:10px;color:#154360;font-weight:bold;margin-bottom:8px}
    .existing-badge .dot{width:6px;height:6px;border-radius:50%;background:#27ae60}

    /* ── DOC TABLE ── */
    .doc-table{width:100%;border-collapse:collapse;font-size:11px}
    .doc-table th{background:#eaf2f8;border:1px solid #c8dce9;padding:6px 10px;text-align:left;font-size:10px;font-weight:bold;text-transform:uppercase;color:#154360;letter-spacing:0.3px}
    .doc-table td{border:1px solid #dce8f0;padding:7px 10px;vertical-align:middle}
    .doc-table tr:nth-child(even) td{background:#f7fbfd}
    .doc-table tr:hover td{background:#edf6ff}
    .doc-desc{font-size:10px;color:#777;margin-top:2px}

    /* ── STATUS PILLS ── */
    .pill{display:inline-flex;align-items:center;gap:5px;font-size:10px;font-weight:bold;padding:3px 9px;border-radius:12px}
    .pill-yes{background:#e8f5e9;color:#1b5e20;border:1px solid #a5d6a7}
    .pill-no{background:#f3f3f3;color:#666;border:1px solid #ddd}

    /* ── VIEW PDF BUTTON ── */
    .btn-view-pdf{display:inline-flex;align-items:center;gap:6px;background:#154360;color:white;border:none;padding:6px 14px;font-size:10px;font-weight:bold;cursor:pointer;border-radius:2px;text-transform:uppercase;letter-spacing:0.3px;text-decoration:none;transition:background 0.15s}
    .btn-view-pdf:hover{background:#1a6fa8}
    .btn-view-pdf-disabled{display:inline-flex;align-items:center;gap:6px;background:#ccc;color:#666;border:none;padding:6px 14px;font-size:10px;font-weight:bold;cursor:not-allowed;border-radius:2px;text-transform:uppercase;letter-spacing:0.3px;opacity:0.5}
    .btn-edit-doc{display:inline-flex;align-items:center;gap:6px;background:#f39c12;color:white;border:none;padding:6px 14px;font-size:10px;font-weight:bold;cursor:pointer;border-radius:2px;text-transform:uppercase;letter-spacing:0.3px;transition:background 0.15s}
    .btn-edit-doc:hover{background:#e67e22}
    .btn-edit-doc-disabled{display:inline-flex;align-items:center;gap:6px;background:#ccc;color:#666;border:none;padding:6px 14px;font-size:10px;font-weight:bold;cursor:not-allowed;border-radius:2px;text-transform:uppercase;letter-spacing:0.3px;opacity:0.5}
    .no-file-tag{font-size:10px;color:#aaa;font-style:italic}

    .empty-state{text-align:center;padding:30px 10px;color:#888;font-size:11px}
    .empty-state .es-icon{font-size:30px;margin-bottom:8px;opacity:0.5}
    .page-nav {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: #0b5ed7;
        padding: 0 15px;
    }

    .nav-left {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .nav-right {
        margin-left: auto;
    }

    .logout-btn {
        background: #dc3545;
        color: #fff;
        border: none;
        padding: 8px 16px;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
        transition: .2s;
    }

    .logout-btn:hover {
        background: #bb2d3b;
    }
    @media(max-width:600px){.form-row{grid-template-columns:1fr}.gov-title-block .dept-name{font-size:14px}}
  </style>
</head>
<body>
<div class="portal-wrap">

  <div class="toast" id="toast" style="position:fixed;top:20px;right:20px;background:#154360;color:white;padding:10px 18px;border-left:4px solid #f39c12;font-size:12px;border-radius:2px;z-index:9999;display:none;box-shadow:0 4px 12px rgba(0,0,0,0.3);max-width:320px"></div>

  {{-- ── HEADER ── --}}
  <div class="gov-header">
    <div class="gov-top-bar">
      <span><span class="status-dot"></span>Portal Status: Active &nbsp;|&nbsp; Last Updated: {{ date('d-M-Y') }}</span>
    </div>
    <div class="gov-logo-row">
      <div class="emblem">⚖️</div>
      <div class="gov-title-block">
        <div class="dept-name">Land Record Digitalization</div>
        <div class="dept-sub">Sangareddy &nbsp;|&nbsp; Revenue Department</div>
      </div>
    </div>
    <div class="gov-subtitle-bar">
      PAHANI DIGITIZATION MANAGEMENT SYSTEM — Sangareddy District ({{ auth()->user()?->name }}, {{auth()->user()->getMandal?->name}})
    </div>
  </div>

  {{-- ── NAV ── --}}
  <div class="page-nav">
      <div class="nav-left">
          <a class="nav-item" href="{{ route('pahani.index') }}">📂 Upload Documents</a>
          <a class="nav-item active" href="{{ route('pahani.view') }}">📋 View Records</a>
          <a class="nav-item" href="#">🔍 Search</a>
          <a class="nav-item" href="#">📊 Reports</a>
          <a class="nav-item" href="#">⚙️ Settings</a>
      </div>

      <div class="nav-right">
          <form id="logoutForm" method="POST" action="{{ route('logout') }}">
              @csrf
              <button type="button" class="logout-btn" onclick="confirmLogout()">
                  🚪 Logout
              </button>
          </form>
      </div>
  </div>

  <div class="main-body">

    <div class="page-heading">
      <h2>📋 Pahani — View Records</h2>
      <div style="font-size:10px;color:#666">Select Mandal &amp; Village to view uploaded documents</div>
    </div>

    <div class="breadcrumb">
      <a href="#">Home</a> › <a href="#">Revenue Records</a> › <a href="#">Pahani Digitization</a> › View Records
    </div>

    <div class="notice-bar">
      ℹ️&nbsp;<span>Select Mandal &amp; Village below to load all Pahani records for that village. Click <strong>View PDF</strong> to open the uploaded document in a secure in-site viewer (opens in a new tab).</span>
    </div>

    {{-- ── SECTION 1: MANDAL & VILLAGE ── --}}
    <div class="section-card">
      <div class="section-header">
        <span class="sec-num">1</span>
        Revenue Mandal &amp; Village Details
      </div>
      <div class="section-body">
        <div class="form-row">
          <div class="field-group">
            <label class="field-label" for="mandal-select">Mandal <span class="req">*</span></label>
            <div class="field-hint">Select the Mandal jurisdiction</div>
            <select id="mandal-select" onchange="onMandalChange()">
              <option value="">— Select Mandal —</option>
              @foreach($mandals as $mandal)
                <option value="{{ $mandal->slug }}" data-id="{{ $mandal->id }}">
                  {{ $mandal->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="field-group">
            <label class="field-label" for="village-select">Village / Revenue Village <span class="req">*</span></label>
            <div class="field-hint">Select village within the Mandal</div>
            <select id="village-select" disabled onchange="onVillageChange()">
              <option value="">— First Select Mandal —</option>
            </select>
            <div class="loading-bar" id="village-loading">
              <div class="spinner"></div> Loading villages…
            </div>
          </div>
        </div>

        <div class="loading-bar" id="records-loading">
          <div class="spinner"></div> Loading records for this village…
        </div>
      </div>
    </div>

    {{-- ── SECTION 2: RECORDS TABLE ── --}}
    <div class="section-card" id="records-card" style="display:none">
      <div class="section-header">
        <span class="sec-num">2</span>
        Pahani Records — Uploaded Documents
      </div>
      <div class="section-body">

        <div id="existing-info" style="display:none"></div>

        <table class="doc-table" id="doc-table">
          <thead>
            <tr>
              <th style="width:32%">Document Name</th>
              <th style="width:16%">Physical Document</th>
              <th style="width:22%">File</th>
              <th style="width:16%;text-align:center">Action</th>
            </tr>
          </thead>
          <tbody id="doc-tbody"></tbody>
        </table>

        <div id="empty-state" class="empty-state" style="display:none">
          <div class="es-icon">🗂️</div>
          No Pahani records found for this village yet.
        </div>
      </div>
    </div>

  </div>{{-- /main-body --}}

  <div class="modal-overlay" id="edit-pdf-modal-overlay" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:9999; display:flex; align-items:center; justify-content:center;" onclick="if(event.target===this) closeEditPdfModal()">
    <div class="modal-container" style="background:white; border-radius:4px; box-shadow:0 4px 12px rgba(0,0,0,0.15); width:90%; max-width:500px; overflow:auto;">
      
      <!-- Modal Header -->
      <div style="padding:16px 20px; border-bottom:1px solid #e0e6ed; display:flex; justify-content:space-between; align-items:center;">
        <h3 style="margin:0; font-size:14px; font-weight:bold; color:#1a1a2e;">📝 Edit PDF Document</h3>
        <button onclick="closeEditPdfModal()" style="background:none; border:none; font-size:20px; cursor:pointer; color:#999;">✕</button>
      </div>

      <!-- Modal Body -->
      <div style="padding:20px;">
        
        <!-- Mandal & Village Info -->
        <div style="margin-bottom:16px; padding:12px; background:#f0f4f8; border-radius:3px;">
          <div style="font-size:11px; color:#666; text-transform:uppercase; font-weight:bold; margin-bottom:4px;">Location</div>
          <div style="font-size:13px; color:#1a1a2e; font-weight:500;">
            <span id="edit-mandal-name"></span> → <span id="edit-village-name"></span>
          </div>
        </div>

        <!-- Document Name -->
        <div style="margin-bottom:16px; padding:12px; background:#f0f4f8; border-radius:3px;">
          <div style="font-size:11px; color:#666; text-transform:uppercase; font-weight:bold; margin-bottom:4px;">Document</div>
          <div style="font-size:13px; color:#1a1a2e; font-weight:500;" id="edit-document-name"></div>
        </div>

        <!-- Current File Info -->
        <div style="margin-bottom:16px; padding:12px; background:#e8f4f8; border-radius:3px; border-left:4px solid #154360;">
          <div style="font-size:11px; color:#666; text-transform:uppercase; font-weight:bold; margin-bottom:4px;">Current File</div>
          <div style="font-size:12px; color:#1a1a2e;">
            📄 <span id="edit-current-filename"></span>
            <br>
            <span style="font-size:10px; color:#888;">Size: <span id="edit-current-filesize"></span></span>
          </div>
        </div>

        <!-- Upload Form -->
        <form id="edit-pdf-form" onsubmit="handleEditPdfUpload(event)" style="margin-bottom:16px;">
          
          <!-- File Input -->
          <div style="margin-bottom:16px;">
            <label style="display:block; font-size:11px; font-weight:bold; color:#1a3a5c; text-transform:uppercase; margin-bottom:8px;">Select New PDF File</label>
            <input 
              type="file" 
              id="edit-pdf-file-input" 
              name="file" 
              accept=".pdf" 
              required 
              style="display:block; width:100%; padding:8px; border:1px solid #b0c4d8; border-radius:3px; font-size:12px;"
            >
            <div style="font-size:10px; color:#888; margin-top:4px;">Maximum file size: 200 MB</div>
          </div>

          <!-- Progress Bar -->
          <div id="edit-upload-progress" style="display:none; margin-bottom:16px;">
            <div style="font-size:11px; font-weight:bold; color:#666; margin-bottom:6px;">Uploading...</div>
            <div style="background:#e8e8e8; border-radius:2px; height:6px; overflow:hidden;">
              <div id="edit-progress-bar" style="background:#154360; height:100%; width:0%; transition:width 0.2s;"></div>
            </div>
            <div style="font-size:10px; color:#888; margin-top:4px;"><span id="edit-progress-text">0</span>%</div>
          </div>

          <!-- Submit Button -->
          <button 
            type="submit" 
            id="edit-submit-btn" 
            style="width:100%; padding:10px; background:#154360; color:white; border:none; border-radius:3px; font-weight:bold; cursor:pointer; transition:background 0.15s;"
          >
            🔄 Update PDF
          </button>
        </form>

        <!-- Error Message -->
        <div id="edit-error-message" style="display:none; padding:12px; background:#fee; border:1px solid #fcc; border-radius:3px; color:#c00; font-size:12px; margin-bottom:16px;"></div>

        <!-- Success Message -->
        <div id="edit-success-message" style="display:none; padding:12px; background:#efe; border:1px solid #cfc; border-radius:3px; color:#060; font-size:12px; margin-bottom:16px;">✔ PDF updated successfully!</div>

      </div>

      <!-- Modal Footer -->
      <div style="padding:16px 20px; border-top:1px solid #e0e6ed; text-align:right;">
        <button onclick="closeEditPdfModal()" style="padding:8px 16px; background:#f0f4f8; color:#1a1a2e; border:1px solid #b0c4d8; border-radius:3px; cursor:pointer; font-weight:bold;">Cancel</button>
      </div>

    </div>
  </div>

</div>{{-- /portal-wrap --}}

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  // ← NEW: Store user permissions
  const USER_PERMISSIONS = {
    can_view: @json($permissions['can_view'] ?? false),
    can_edit: @json($permissions['can_edit'] ?? false),
  };

  function confirmLogout() {
      Swal.fire({
          title: 'Logout?',
          text: 'Are you sure you want to logout?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#6c757d',
          confirmButtonText: 'Yes, Logout',
          cancelButtonText: 'Cancel'
      }).then((result) => {
          if (result.isConfirmed) {
              document.getElementById('logoutForm').submit();
          }
      });
  }
/* ══════════════════════════════════════════════════════════════════
   MANDAL CHANGE — fetch villages via AJAX
══════════════════════════════════════════════════════════════════ */
function onMandalChange() {
  const mandalSel = document.getElementById('mandal-select');
  const villageSel = document.getElementById('village-select');
  const mandalId  = mandalSel.options[mandalSel.selectedIndex]?.dataset?.id;

  villageSel.innerHTML = '<option value="">— Select Village —</option>';
  villageSel.disabled  = true;
  hideRecords();

  if (!mandalId) return;

  document.getElementById('village-loading').classList.add('show');

  fetch(`/api/mandals/${mandalId}/villages`, {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => r.json())
  .then(villages => {
    villageSel.innerHTML = '<option value="">— Select Village —</option>';
    villages.forEach(v => {
      const o = document.createElement('option');
      o.value = v.slug;
      o.textContent = v.name;
      o.dataset.id  = v.id;
      villageSel.appendChild(o);
    });
    villageSel.disabled = false;
  })
  .catch(() => showToast('Failed to load villages. Please refresh.', true))
  .finally(() => document.getElementById('village-loading').classList.remove('show'));
}

/* ══════════════════════════════════════════════════════════════════
   VILLAGE CHANGE — load existing Pahani records for that village
══════════════════════════════════════════════════════════════════ */
function onVillageChange() {
  const mandalSel  = document.getElementById('mandal-select');
  const villageSel = document.getElementById('village-select');
  const villageOpt = villageSel.options[villageSel.selectedIndex];
  const villageId  = villageOpt?.dataset?.id;
  const villageName = villageOpt?.textContent?.trim();
  const mandalName  = mandalSel.options[mandalSel.selectedIndex]?.textContent?.trim();

  hideRecords();
  if (!villageId) return;

  document.getElementById('records-loading').classList.add('show');

  fetch(`/api/villages/${villageId}/pahanis`, {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => r.json())
  .then(records => renderRecords(records, mandalName, villageName))
  .catch(() => showToast('Failed to load records for this village.', true))
  .finally(() => document.getElementById('records-loading').classList.remove('show'));
}

/* ══════════════════════════════════════════════════════════════════
   RENDER RECORDS TABLE
══════════════════════════════════════════════════════════════════ */
function renderRecords(records, mandalName, villageName) {
  const card  = document.getElementById('records-card');
  const tbody = document.getElementById('doc-tbody');
  const empty = document.getElementById('empty-state');
  const info  = document.getElementById('existing-info');

  card.style.display = 'block';
  tbody.innerHTML = '';

  if (!records || records.length === 0) {
    empty.style.display = 'block';
    info.style.display  = 'none';
    return;
  }

  empty.style.display = 'none';
  info.style.display  = 'block';
  info.innerHTML = `<div class="existing-badge"><span class="dot"></span>${records.length} record(s) found for ${villageName}, ${mandalName}</div>`;
  
  // ← CRITICAL: Store records and location globally for modal access
  window.PAHANI_RECORDS = records;
  window.PAHANI_MANDAL_NAME = mandalName;
  window.PAHANI_VILLAGE_NAME = villageName;
  
  // Also need to extract mandal and village slugs - try to get from dropdowns
  const mandalSel = document.getElementById('mandal-select');
  const villageSel = document.getElementById('village-select');
  window.PAHANI_MANDAL_SLUG = mandalSel.value || '';
  window.PAHANI_VILLAGE_SLUG = villageSel.value || '';
  
  records.forEach(rec => {
    const tr = document.createElement('tr');

    const isYes = rec.physical_document === 'yes';
    const hasFile = !!rec.file_path;

    // ← NEW: Build View PDF button with permission checks
    let viewPdfHtml = '';
    if (hasFile) {
      if (USER_PERMISSIONS.can_view) {
        // User has permission to view
        viewPdfHtml = `<a class="btn-view-pdf" href="/pahani/view-pdf/${rec.id}" target="_blank" rel="noopener">👁 View PDF</a>`;
      } else {
        // File exists but no permission
        viewPdfHtml = `<button class="btn-view-pdf-disabled" disabled title="You don't have permission to view PDFs">👁 View PDF</button>`;
      }
    } else {
      // No file uploaded
      viewPdfHtml = `<span class="no-file-tag">—</span>`;
    }

    // ← NEW: Build Edit button with permission checks
    let editHtml = '';
    if (USER_PERMISSIONS.can_edit) {
      editHtml = `<button class="btn-edit-doc" onclick="editRecord(${rec.id})" title="Edit this document">✎ Edit</button>`;
    } else {
      editHtml = `<button class="btn-edit-doc-disabled" disabled title="You don't have permission to edit documents">✎ Edit</button>`;
    }

    tr.innerHTML = `
      <td>${escapeHtml(rec.document_name || rec.document_value)}</td>
      <td>
        <span class="pill ${isYes ? 'pill-yes' : 'pill-no'}">
          ${isYes ? '✔ Yes' : '— No'}
        </span>
      </td>
      <td>
        ${hasFile
          ? `📄 ${escapeHtml(rec.file_name || 'document.pdf')}`
          : `<span class="no-file-tag">No file uploaded</span>`}
      </td>
      <td style="text-align:center; display: flex; gap: 6px; justify-content: center; flex-wrap: wrap;">
        ${viewPdfHtml}
        ${editHtml}
      </td>
    `;
    tbody.appendChild(tr);
  });
}

function hideRecords() {
  document.getElementById('records-card').style.display = 'none';
  document.getElementById('doc-tbody').innerHTML = '';
  document.getElementById('empty-state').style.display = 'none';
  document.getElementById('existing-info').style.display = 'none';
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
  setTimeout(() => t.style.display = 'none', 4500);
}

// Store current record being edited
let currentEditRecord = null;

/**
 * Open edit modal for a record
 * @param {number} pahaniId - ID of the record to edit
 */
function editRecord(pahaniId) {
  // Find the record from the table
  const records = window.PAHANI_RECORDS || [];
  currentEditRecord = records.find(r => r.id === pahaniId);
  
  if (!currentEditRecord) {
    showToast('Record not found.', true);
    return;
  }

  if (!currentEditRecord.file_path) {
    showToast('This record has no file to edit.', true);
    return;
  }

  // Populate modal with record data
  document.getElementById('edit-mandal-name').textContent = window.PAHANI_MANDAL_NAME || 'Mandal';
  document.getElementById('edit-village-name').textContent = window.PAHANI_VILLAGE_NAME || 'Village';
  document.getElementById('edit-document-name').textContent = currentEditRecord.document_name || currentEditRecord.document_value;
  document.getElementById('edit-current-filename').textContent = currentEditRecord.file_name || 'document.pdf';
  document.getElementById('edit-current-filesize').textContent = currentEditRecord.file_size_human || 'Unknown';

  // Clear form
  document.getElementById('edit-pdf-file-input').value = '';
  document.getElementById('edit-error-message').style.display = 'none';
  document.getElementById('edit-success-message').style.display = 'none';
  document.getElementById('edit-upload-progress').style.display = 'none';
  document.getElementById('edit-submit-btn').disabled = false;
  document.getElementById('edit-submit-btn').textContent = '🔄 Update PDF';

  // Show modal
  document.getElementById('edit-pdf-modal-overlay').style.display = 'flex';
}

/**
 * Close edit modal
 */
function closeEditPdfModal() {
  document.getElementById('edit-pdf-modal-overlay').style.display = 'none';
  currentEditRecord = null;
}

/**
 * Handle PDF upload and update
 */
async function handleEditPdfUpload(e) {
  e.preventDefault();

  if (!currentEditRecord) {
    showToast('No record selected.', true);
    return;
  }

  const fileInput = document.getElementById('edit-pdf-file-input');
  const file = fileInput.files[0];

  if (!file) {
    showErrorMessage('Please select a file.');
    return;
  }

  if (file.type !== 'application/pdf') {
    showErrorMessage('Only PDF files are allowed.');
    return;
  }

  if (file.size > 200 * 1024 * 1024) { // 200 MB
    showErrorMessage('File size must be less than 200 MB.');
    return;
  }

  const submitBtn = document.getElementById('edit-submit-btn');
  submitBtn.disabled = true;
  submitBtn.textContent = 'Updating…';

  try {
    // Step 1: Get presigned upload URL
    const presignResponse = await fetch('/pahani/presign', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      },
      body: JSON.stringify({
        mandal: window.PAHANI_MANDAL_SLUG || '',
        village: window.PAHANI_VILLAGE_SLUG || '',
        docValue: currentEditRecord.document_value,
        fileMime: file.type,
      }),
    });

    if (!presignResponse.ok) {
      const err = await presignResponse.json();
      throw new Error(err.message || 'Failed to get upload URL');
    }

    const presignData = await presignResponse.json();

    if (!presignData.success) {
      throw new Error(presignData.message || 'Presign failed');
    }

    // Step 2: Upload to Cloudflare R2
    showProgressBar(true);
    
    const uploadResponse = await uploadToR2(
      presignData.url,
      file,
      presignData.headers
    );

    if (!uploadResponse.ok) {
      throw new Error('Upload to Cloudflare failed: ' + uploadResponse.status);
    }

    showProgressBar(false);

    // Step 3: Update database with new file
    const updateResponse = await fetch(`/pahani/${currentEditRecord.id}/update-file`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
      },
      body: JSON.stringify({
        r2_key: presignData.key,
        file_name: file.name,
        file_size: file.size,
        file_mime: file.type,
        old_file_path: currentEditRecord.file_path, // For deletion
      }),
    });

    if (!updateResponse.ok) {
      const err = await updateResponse.json();
      throw new Error(err.message || 'Failed to update record');
    }

    const updateData = await updateResponse.json();

    if (!updateData.success) {
      throw new Error(updateData.message || 'Update failed');
    }

    // Success!
    showSuccessMessage('PDF updated successfully!');
    setTimeout(() => {
      closeEditPdfModal();
      // Refresh the records
      if (window.loadRecords) {
        window.loadRecords();
      }
    }, 1500);

  } catch (error) {
    console.error('Edit error:', error);
    showErrorMessage(error.message || 'Failed to update PDF. Please try again.');
  } finally {
    submitBtn.disabled = false;
    submitBtn.textContent = '🔄 Update PDF';
  }
}

/**
 * Upload file to Cloudflare R2 with progress tracking
 */
async function uploadToR2(presignedUrl, file, headers) {
  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();

    // Track upload progress
    xhr.upload.addEventListener('progress', (e) => {
      if (e.lengthComputable) {
        const percentComplete = Math.round((e.loaded / e.total) * 100);
        updateProgressBar(percentComplete);
      }
    });

    xhr.addEventListener('load', () => {
      if (xhr.status >= 200 && xhr.status < 300) {
        resolve(new Response('', { status: xhr.status }));
      } else {
        reject(new Error(`Upload failed with status ${xhr.status}`));
      }
    });

    xhr.addEventListener('error', () => {
      reject(new Error('Upload error'));
    });

    xhr.addEventListener('abort', () => {
      reject(new Error('Upload aborted'));
    });

    xhr.open('PUT', presignedUrl);

    // Set headers
    if (headers) {
      Object.entries(headers).forEach(([key, value]) => {
        xhr.setRequestHeader(key, value);
      });
    }

    xhr.send(file);
  });
}

/**
 * Show progress bar
 */
function showProgressBar(show) {
  document.getElementById('edit-upload-progress').style.display = show ? 'block' : 'none';
  if (show) {
    updateProgressBar(0);
  }
}

/**
 * Update progress bar
 */
function updateProgressBar(percent) {
  document.getElementById('edit-progress-bar').style.width = percent + '%';
  document.getElementById('edit-progress-text').textContent = percent;
}

/**
 * Show error message
 */
function showErrorMessage(message) {
  const errorDiv = document.getElementById('edit-error-message');
  errorDiv.textContent = message;
  errorDiv.style.display = 'block';
  document.getElementById('edit-success-message').style.display = 'none';
}

/**
 * Show success message
 */
function showSuccessMessage(message) {
  const successDiv = document.getElementById('edit-success-message');
  successDiv.textContent = message;
  successDiv.style.display = 'block';
  document.getElementById('edit-error-message').style.display = 'none';
}

/**
 * Reload records from server (called after edit)
 */
function loadRecords() {
  const mandalSel = document.getElementById('mandal-select');
  const villageSel = document.getElementById('village-select');
  const villageId = villageSel.options[villageSel.selectedIndex]?.dataset?.id;
  
  if (!villageId) return;
  
  document.getElementById('records-loading').classList.add('show');
  
  fetch(`/api/villages/${villageId}/pahanis`, {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => r.json())
  .then(records => {
    const mandalName = mandalSel.options[mandalSel.selectedIndex]?.textContent?.trim();
    const villageName = villageSel.options[villageSel.selectedIndex]?.textContent?.trim();
    renderRecords(records, mandalName, villageName);
  })
  .catch(err => {
    console.error('Failed to reload records:', err);
    showToast('Failed to reload records', true);
  })
  .finally(() => document.getElementById('records-loading').classList.remove('show'));
}

// Store records and location data globally for modal
document.addEventListener('DOMContentLoaded', () => {
  // This will be set when records are loaded
  window.PAHANI_RECORDS = [];
  window.PAHANI_MANDAL_NAME = '';
  window.PAHANI_VILLAGE_NAME = '';
  window.PAHANI_MANDAL_SLUG = '';
  window.PAHANI_VILLAGE_SLUG = '';
});
</script>

</body>
</html>