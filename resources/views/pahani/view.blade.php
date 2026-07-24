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
      PAHANI DIGITIZATION MANAGEMENT SYSTEM — Revenue Divisional Officer ({{ auth()->user()?->name }}, Designation)
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

</div>{{-- /portal-wrap --}}

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
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

  records.forEach(rec => {
    const tr = document.createElement('tr');

    const isYes = rec.physical_document === 'yes';
    const hasFile = !!rec.file_path;

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
      <td style="text-align:center">
        ${hasFile
          ? `<a class="btn-view-pdf" href="/pahani/view-pdf/${rec.id}" target="_blank" rel="noopener">👁 View PDF</a>`
          : `<span class="no-file-tag">—</span>`}
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
</script>

</body>
</html>