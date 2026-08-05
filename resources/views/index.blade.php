<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Pahani Upload Form — Land Record Digitalization</title>
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

    /* ── RADIO ── */
    .radio-group{display:flex;gap:12px;align-items:center}
    .radio-label{display:flex;align-items:center;gap:4px;font-size:11px;color:#333;cursor:pointer}
    .radio-label input[type=radio]{cursor:pointer;accent-color:#154360}

    /* ── UPLOAD ── */
    .upload-zone{border:1.5px dashed #154360;background:#eaf2f8;border-radius:2px;padding:8px 12px;text-align:center;cursor:pointer;position:relative;min-width:160px;transition:background 0.15s}
    .upload-zone:hover{background:#d6eaf8}
    .upload-zone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
    .upload-zone .uz-icon{font-size:14px;color:#154360}
    .upload-zone .uz-text{font-size:10px;color:#154360;font-weight:bold}
    .upload-zone .uz-hint{font-size:9px;color:#888}
    .uploaded-file{display:flex;align-items:center;gap:6px;background:#e8f5e9;border:1px solid #a5d6a7;border-radius:2px;padding:4px 8px;font-size:10px;color:#2e7d32;margin-top:4px}
    .uploaded-file .remove-btn{margin-left:auto;cursor:pointer;color:#c62828;font-weight:bold;font-size:11px;border:none;background:none;padding:0 2px}

    /* existing file tag (already saved in DB) */
    .saved-file{display:flex;align-items:center;gap:6px;background:#e3f2fd;border:1px solid #90caf9;border-radius:2px;padding:4px 8px;font-size:10px;color:#1565c0;margin-top:4px}
    .saved-file-label{font-size:9px;background:#154360;color:white;padding:1px 5px;border-radius:2px}
    .no-doc-msg{font-size:11px;color:#888;font-style:italic}

    /* action / delete */
    .btn-danger-sm{border:none;background:none;cursor:pointer;color:#c0392b;font-size:16px;padding:0 4px;line-height:1}
    .btn-danger-sm:hover{color:#7f0000}

    /* ── BUTTONS ── */
    .add-row-btn{background:#154360;color:white;border:none;padding:7px 16px;font-size:11px;font-weight:bold;cursor:pointer;border-radius:2px;display:flex;align-items:center;gap:6px;margin-top:10px;text-transform:uppercase;letter-spacing:0.3px;transition:background 0.15s}
    .add-row-btn:hover{background:#1a6fa8}
    .add-row-btn:disabled{background:#aaa;cursor:not-allowed}
    .row-info{font-size:10px;color:#666;margin-top:6px}
    .btn-primary{background:#154360;color:white;border:none;padding:9px 24px;font-size:12px;font-weight:bold;cursor:pointer;text-transform:uppercase;letter-spacing:0.5px;border-radius:2px;transition:background 0.15s}
    .btn-primary:hover{background:#1a6fa8}
    .btn-primary:disabled{background:#aaa;cursor:not-allowed}
    .btn-secondary{background:white;color:#154360;border:1.5px solid #154360;padding:8px 18px;font-size:11px;font-weight:bold;cursor:pointer;text-transform:uppercase;letter-spacing:0.3px;border-radius:2px;transition:all 0.15s}
    .btn-secondary:hover{background:#eaf2f8}

    /* ── FOOTER BAR ── */
    .form-footer{background:#eaf2f8;border-top:1px solid #c8dce9;padding:12px 14px;display:flex;align-items:center;justify-content:space-between}

    /* ── TOAST ── */
    .toast{position:fixed;top:20px;right:20px;background:#154360;color:white;padding:10px 18px;border-left:4px solid #f39c12;font-size:12px;border-radius:2px;z-index:9999;display:none;box-shadow:0 4px 12px rgba(0,0,0,0.3);max-width:320px}
    .toast.show{display:block}
    .toast.error{border-left-color:#c0392b;background:#7f0000}

    /* ── UPLOAD PROGRESS BAR (replaces submit-overlay) ── */
    #upload-progress-container{
      display:none;
      position:fixed;
      top:0;
      left:0;
      right:0;
      background:white;
      border-bottom:3px solid #154360;
      padding:20px;
      z-index:8000;
      box-shadow:0 2px 8px rgba(0,0,0,0.15);
    }
    #upload-progress-container.show{
      display:block;
    }
    .upload-progress-content{
      max-width:960px;
      margin:0 auto;
    }
    .upload-progress-header{
      display:flex;
      align-items:center;
      gap:12px;
      margin-bottom:16px;
    }
    .upload-progress-spinner{
      width:20px;
      height:20px;
      border:2px solid #d0dde8;
      border-top-color:#154360;
      border-radius:50%;
      animation:spin 0.8s linear infinite;
      flex-shrink:0;
    }
    .upload-progress-text{
      font-size:13px;
      font-weight:bold;
      color:#154360;
    }
    .upload-progress-text .percent{
      color:#f39c12;
      font-weight:bold;
    }
    .upload-progress-main-bar{
      width:100%;
      height:24px;
      background:#e8f0f7;
      border:1px solid #c0d5e3;
      border-radius:4px;
      overflow:hidden;
      margin-bottom:12px;
    }
    .upload-progress-main-fill{
      height:100%;
      background:linear-gradient(90deg, #154360 0%, #1a6fa8 100%);
      width:0%;
      transition:width 0.3s ease;
      display:flex;
      align-items:center;
      justify-content:center;
      color:white;
      font-size:11px;
      font-weight:bold;
    }
    .upload-progress-rows{
      display:grid;
      grid-template-columns:1fr 1fr;
      gap:12px;
      max-height:240px;
      overflow-y:auto;
    }
    .upload-progress-row{
      background:#f8fbfd;
      border:1px solid #d0dde8;
      border-radius:3px;
      padding:8px;
      font-size:10px;
    }
    .upload-progress-row-name{
      font-weight:bold;
      color:#154360;
      margin-bottom:4px;
      white-space:nowrap;
      overflow:hidden;
      text-overflow:ellipsis;
    }
    .upload-progress-row-bar{
      width:100%;
      height:12px;
      background:#d0dde8;
      border-radius:2px;
      overflow:hidden;
      margin-bottom:3px;
    }
    .upload-progress-row-fill{
      height:100%;
      background:#27ae60;
      width:0%;
      transition:width 0.2s ease;
    }
    .upload-progress-row-percent{
      text-align:right;
      color:#666;
      font-size:9px;
    }

    /* ── HIDDEN SUBMIT OVERLAY ── */
    #submit-overlay{display:none}
    
    /* scroll-friendly progress container on mobile */
    @media (max-width:600px){
      .upload-progress-rows{
        grid-template-columns:1fr;
      }
    }
  </style>
</head>
<body>
<div class="portal-wrap">

  <!-- ════════════════════════════════════════════════════════════
       GOVERNMENT HEADER
  ════════════════════════════════════════════════════════════ -->
  <div class="gov-header">
    <div class="gov-top-bar">
      <span>🇮🇳 India | Ministry of Land Resources</span>
      <span>📞 Help: 1-800-PAHANI-1</span>
    </div>
    <div class="gov-logo-row">
      <div class="emblem">🏛️</div>
      <div class="gov-title-block">
        <div class="dept-name">Pahani — Digital Land Records</div>
        <div class="dept-sub">Telangana State Land Records Management System</div>
      </div>
    </div>
    <div class="gov-subtitle-bar">
      🔒 Secure Document Upload Portal | Version 2.0
    </div>
  </div>

  <!-- NAVIGATION -->
  <div class="page-nav">
    <div class="nav-left">
      <a href="#" class="nav-item active">📋 Upload Documents</a>
      <a href="#" class="nav-item">📊 View Records</a>
      <a href="#" class="nav-item">⚙️ Settings</a>
    </div>
    <div class="nav-right">
      <form action="{{ route('logout') }}" method="POST" style="display:inline">
        @csrf
        <button type="submit" class="logout-btn">🚪 Logout</button>
      </form>
    </div>
  </div>

  <!-- MAIN BODY -->
  <div class="main-body">
    <div class="breadcrumb">
      <a href="#">Home</a> / <a href="#">Documents</a> / <span style="color:#999">Upload</span>
      <span style="margin-left:auto;display:flex;align-items:center;gap:4px">
        <span class="status-dot"></span> System Online
      </span>
    </div>

    <!-- ════════════════════════════════════════════════════════════
         UPLOAD PROGRESS BAR (NEW — replaces submit-overlay)
    ════════════════════════════════════════════════════════════ -->
    <div id="upload-progress-container">
      <div class="upload-progress-content">
        <div class="upload-progress-header">
          <div class="upload-progress-spinner"></div>
          <div class="upload-progress-text">
            Uploading Files... <span class="percent"><span id="overall-progress-pct">0</span>%</span>
          </div>
        </div>
        <div class="upload-progress-main-bar">
          <div class="upload-progress-main-fill" id="overall-progress-bar" style="width:0%">
            <span id="overall-progress-label"></span>
          </div>
        </div>
        <div class="upload-progress-rows" id="upload-progress-rows-container"></div>
      </div>
    </div>

    <div class="page-heading">
      <h2>📄 Upload Land Records</h2>
    </div>

    <div class="notice-bar">
      ℹ️ <strong>Note:</strong> Only PDF files (.pdf) up to 50 MB are accepted. All uploads are secure and encrypted.
    </div>

    <!-- ════════════════════════════════════════════════════════════
         FORM SECTION
    ════════════════════════════════════════════════════════════ -->
    <form id="pahani-form">
      @csrf

      <!-- Section 1: Location Selection -->
      <div class="section-card">
        <div class="section-header">
          <span class="sec-num">1</span>
          <span>📍 Select Location</span>
        </div>
        <div class="section-body">
          <div class="form-row">
            <div class="field-group">
              <label class="field-label">District <span class="req">*</span></label>
              <select id="district-select">
                <option value="">— Select District —</option>
                <option value="rangareddy">Rangareddy</option>
                <option value="medchal">Medchal-Malkajgiri</option>
                <option value="telangana">Telangana</option>
              </select>
            </div>
            <div class="field-group">
              <label class="field-label">Mandal <span class="req">*</span></label>
              <select id="mandal-select" disabled>
                <option value="">— First Select District —</option>
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="field-group">
              <label class="field-label">Village <span class="req">*</span></label>
              <select id="village-select" disabled>
                <option value="">— First Select Mandal —</option>
              </select>
            </div>
            <div class="field-group">
              <label class="field-label">Survey Number</label>
              <select id="survey-select">
                <option value="">— Optional —</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <!-- Section 2: Document Upload -->
      <div class="section-card">
        <div class="section-header">
          <span class="sec-num">2</span>
          <span>📑 Add & Upload Documents</span>
        </div>
        <div class="section-body">
          <p style="font-size:10px;color:#666;margin-bottom:10px">Add each document row-by-row below, upload a PDF for any "Physical Document = Yes", then submit.</p>

          <!-- Document Table -->
          <div style="overflow-x:auto">
            <table class="doc-table">
              <thead>
                <tr>
                  <th style="width:20%">Document Type</th>
                  <th style="width:12%">Physical Doc?</th>
                  <th style="width:30%">Upload PDF</th>
                  <th style="width:20%">Status</th>
                  <th style="width:8%">Del</th>
                </tr>
              </thead>
              <tbody id="doc-table-body">
              </tbody>
            </table>
          </div>

          <button type="button" class="add-row-btn" id="add-row-btn" onclick="addRow()">
            ➕ Add Document Row
          </button>
          <div class="row-info">Max 10 documents per submission. Each file: max 50 MB (PDF only).</div>

          <!-- Existing Records Info -->
          <div id="existing-info" style="display:none;margin-top:12px">
            <div class="existing-badge">
              <span class="dot"></span>
              Previously uploaded documents found
            </div>
            <p style="font-size:10px;color:#666;margin-top:6px">
              You can re-upload to replace or keep existing files. The system will auto-detect and skip duplicates.
            </p>
          </div>
        </div>
      </div>

      <!-- Section 3: Review & Submit -->
      <div class="section-card">
        <div class="section-header">
          <span class="sec-num">3</span>
          <span>✔️ Review & Submit</span>
        </div>
        <div class="section-body">
          <p style="font-size:11px;color:#666;margin-bottom:12px">
            Click "Submit" to start uploading files in parallel. A progress bar will show real-time upload status.
          </p>
          <div class="form-footer">
            <div style="font-size:10px;color:#666">
              Submission will upload all files to secure cloud storage, then save metadata to the database.
            </div>
            <div style="display:flex;gap:8px">
              <button type="button" class="btn-secondary" onclick="clearRows()">↻ Clear All</button>
              <button type="button" class="btn-primary" id="submit-btn" onclick="submitForm()">✓ Submit & Upload</button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- ════════════════════════════════════════════════════════════
     TOAST NOTIFICATION
════════════════════════════════════════════════════════════ -->
<div class="toast" id="toast"></div>

<!-- ════════════════════════════════════════════════════════════
     HIDDEN SUBMIT OVERLAY (kept for backwards compatibility, but unused)
════════════════════════════════════════════════════════════ -->
<div id="submit-overlay">
  <div class="submit-box">
    <div class="big-spinner"></div>
    Saving records to database...
  </div>
</div>

<script>
/* ══════════════════════════════════════════════════════════════════
   STATE MANAGEMENT
══════════════════════════════════════════════════════════════════ */
const state = {
  rows: [],
  files: {}
};

/* ══════════════════════════════════════════════════════════════════
   DISTRICT → MANDAL → VILLAGE CASCADE
══════════════════════════════════════════════════════════════════ */
const DATA = {
  rangareddy: {
    mandals: ['Tandur', 'Vikarabad', 'Kanakal'],
    villages: {
      'Tandur': ['Tandur Village', 'Shadnagar', 'Moinabad'],
      'Vikarabad': ['Vikarabad Town', 'Tandur', 'Tandur'],
      'Kanakal': ['Kanakal', 'Tandur', 'Tandur']
    }
  },
  medchal: {
    mandals: ['Medchal', 'Malkajgiri', 'Tandur'],
    villages: {
      'Medchal': ['Medchal', 'Mominpet', 'Tandur'],
      'Malkajgiri': ['Malkajgiri', 'Tandur', 'Tandur'],
      'Tandur': ['Tandur', 'Tandur', 'Tandur']
    }
  },
  telangana: {
    mandals: ['Tandur', 'Tandur', 'Tandur'],
    villages: {
      'Tandur': ['Tandur', 'Tandur', 'Tandur']
    }
  }
};

document.getElementById('district-select').addEventListener('change', function() {
  const ds = document.getElementById('district-select');
  const ms = document.getElementById('mandal-select');
  const vs = document.getElementById('village-select');

  const district = ds.value;
  if (!district || !DATA[district]) {
    ms.innerHTML = '<option value="">— First Select District —</option>';
    ms.disabled = true;
    vs.innerHTML = '<option value="">— First Select Mandal —</option>';
    vs.disabled = true;
    clearRows();
    document.getElementById('existing-info').style.display = 'none';
    return;
  }

  const mandals = DATA[district].mandals;
  ms.innerHTML = '<option value="">— Select Mandal —</option>' + mandals.map(m => `<option value="${m}">${m}</option>`).join('');
  ms.disabled = false;
  vs.innerHTML = '<option value="">— First Select Mandal —</option>';
  vs.disabled = true;
  clearRows();
  document.getElementById('existing-info').style.display = 'none';
});

document.getElementById('mandal-select').addEventListener('change', function() {
  const ds = document.getElementById('district-select');
  const ms = document.getElementById('mandal-select');
  const vs = document.getElementById('village-select');

  const district = ds.value;
  const mandal = ms.value;

  if (!mandal || !DATA[district] || !DATA[district].villages[mandal]) {
    vs.innerHTML = '<option value="">— First Select Mandal —</option>';
    vs.disabled = true;
    clearRows();
    document.getElementById('existing-info').style.display = 'none';
    return;
  }

  const villages = DATA[district].villages[mandal];
  vs.innerHTML = '<option value="">— Select Village —</option>' + villages.map(v => `<option value="${v}">${v}</option>`).join('');
  vs.disabled = false;
  clearRows();
  document.getElementById('existing-info').style.display = 'none';
});

document.getElementById('village-select').addEventListener('change', function() {
  clearRows();
  document.getElementById('existing-info').style.display = 'block';
});

/* ══════════════════════════════════════════════════════════════════
   ROW MANAGEMENT
══════════════════════════════════════════════════════════════════ */
const DOC_TYPES = [
  'Property Deed',
  'Mutation Record',
  'Tax Payment Receipt',
  'Boundary Map',
  'Survey Certificate',
  'Encumbrance Certificate'
];

let rowIdCounter = 0;

function addRow() {
  if (state.rows.length >= 10) { showToast('Maximum 10 documents per submission.', true); return; }
  const rowId = 'row_' + (++rowIdCounter);
  state.rows.push({ id: rowId, value: '', physical: '', fileName: '', existingFileName: '', pahaniId: null });
  renderTable();
}

function removeRow(rowId) {
  state.rows = state.rows.filter(r => r.id !== rowId);
  delete state.files[rowId];
  renderTable();
}

function clearRows() {
  state.rows = [];
  state.files = {};
  renderTable();
}

function renderTable() {
  const body = document.getElementById('doc-table-body');
  if (state.rows.length === 0) {
    body.innerHTML = '<tr><td colspan="5" style="text-align:center;color:#999;padding:20px;font-size:11px">No documents added yet. Click "Add Document Row" to begin.</td></tr>';
    return;
  }

  body.innerHTML = state.rows.map(row => `
    <tr>
      <td>
        <select onchange="setRowValue('${row.id}', this.value)" style="width:100%;font-size:11px">
          <option value="">— Select Type —</option>
          ${DOC_TYPES.map(dt => `<option value="${dt}" ${row.value === dt ? 'selected' : ''}>${dt}</option>`).join('')}
        </select>
      </td>
      <td>
        <div class="radio-group" style="gap:6px">
          <label class="radio-label">
            <input type="radio" name="physical_${row.id}" value="Yes" ${row.physical === 'Yes' ? 'checked' : ''} onchange="setRowPhysical('${row.id}', 'Yes')">
            Yes
          </label>
          <label class="radio-label">
            <input type="radio" name="physical_${row.id}" value="No" ${row.physical === 'No' ? 'checked' : ''} onchange="setRowPhysical('${row.id}', 'No')">
            No
          </label>
        </div>
      </td>
      <td>
        <div class="field-group">
          ${state.files[row.id] ? `
            <div class="uploaded-file">
              📄 ${state.files[row.id].name.substring(0, 20)}...
              <button class="remove-btn" onclick="removeFile('${row.id}')">✕</button>
            </div>
          ` : row.existingFileName ? `
            <div class="saved-file">
              ✓ ${row.existingFileName}
              <span class="saved-file-label">Saved</span>
            </div>
          ` : `
            <div class="upload-zone" onclick="document.getElementById('file_${row.id}').click()">
              <div class="uz-icon">📁</div>
              <div class="uz-text">Click to Upload</div>
              <div class="uz-hint">PDF, max 50 MB</div>
              <input type="file" id="file_${row.id}" accept=".pdf" onchange="handleFileSelect('${row.id}', this)">
            </div>
          `}
          ${state.files[row.id] && row.existingFileName ? `<div class="no-doc-msg" style="margin-top:4px">Will replace existing file</div>` : ''}
        </div>
      </td>
      <td style="font-size:10px;color:#666">
        <div id="progress-wrap-${row.id}" style="display:none">
          <div style="width:80px;height:8px;background:#e8f0f7;border-radius:2px;overflow:hidden;margin-bottom:3px">
            <div id="progress-bar-${row.id}" style="height:100%;background:#27ae60;width:0%;transition:width 0.2s"></div>
          </div>
          <div id="progress-text-${row.id}" style="font-size:9px">0%</div>
        </div>
      </td>
      <td style="text-align:center">
        <button type="button" class="btn-danger-sm" onclick="removeRow('${row.id}')">🗑️</button>
      </td>
    </tr>
  `).join('');
}

function setRowValue(rowId, value) {
  const row = state.rows.find(r => r.id === rowId);
  if (row) row.value = value;
}

function setRowPhysical(rowId, value) {
  const row = state.rows.find(r => r.id === rowId);
  if (row) row.physical = value;
}

function handleFileSelect(rowId, input) {
  const file = input.files[0];
  if (!file) return;
  if (file.type !== 'application/pdf') {
    showToast('Only PDF files are allowed.', true);
    input.value = '';
    return;
  }
  if (file.size > 50 * 1024 * 1024) {
    showToast('File too large (max 50 MB).', true);
    input.value = '';
    return;
  }
  state.files[rowId] = file;
  renderTable();
}

function removeFile(rowId) {
  delete state.files[rowId];
  const input = document.getElementById('file_' + rowId);
  if (input) input.value = '';
  renderTable();
}

/* ══════════════════════════════════════════════════════════════════
   TOAST NOTIFICATION
══════════════════════════════════════════════════════════════════ */
function showToast(msg, isError = false) {
  const toast = document.getElementById('toast');
  toast.textContent = msg;
  toast.className = 'toast show' + (isError ? ' error' : '');
  setTimeout(() => toast.classList.remove('show'), 4000);
}

/* ══════════════════════════════════════════════════════════════════
   PROGRESS BAR UPDATES (MAIN FIX)
══════════════════════════════════════════════════════════════════ */
function showUploadProgress(rows) {
  const container = document.getElementById('upload-progress-container');
  const rowsContainer = document.getElementById('upload-progress-rows-container');
  
  // Create progress row elements for each file being uploaded
  rowsContainer.innerHTML = rows
    .filter(r => state.files[r.id])
    .map(r => `
      <div class="upload-progress-row">
        <div class="upload-progress-row-name">${r.value || 'Document'}</div>
        <div class="upload-progress-row-bar">
          <div class="upload-progress-row-fill" id="upload-row-progress-${r.id}" style="width:0%"></div>
        </div>
        <div class="upload-progress-row-percent"><span id="upload-row-pct-${r.id}">0</span>%</div>
      </div>
    `)
    .join('');
  
  container.classList.add('show');
}

function hideUploadProgress() {
  const container = document.getElementById('upload-progress-container');
  container.classList.remove('show');
}

function updateOverallProgress(percent) {
  const bar = document.getElementById('overall-progress-bar');
  const pct = document.getElementById('overall-progress-pct');
  bar.style.width = percent + '%';
  pct.textContent = Math.round(percent);
}

function updateRowProgress(rowId, percent) {
  const fill = document.getElementById(`upload-row-progress-${rowId}`);
  const pct = document.getElementById(`upload-row-pct-${rowId}`);
  if (fill) fill.style.width = percent + '%';
  if (pct) pct.textContent = Math.round(percent);
  
  // Also update the table progress indicator
  const tableProgressBar = document.getElementById(`progress-bar-${rowId}`);
  const tableProgressText = document.getElementById(`progress-text-${rowId}`);
  if (tableProgressBar) tableProgressBar.style.width = percent + '%';
  if (tableProgressText) tableProgressText.textContent = Math.round(percent) + '%';
}

/* ══════════════════════════════════════════════════════════════════
   FILE UPLOAD WITH PROGRESS
══════════════════════════════════════════════════════════════════ */
function uploadFileWithProgress(url, file, headers, onProgress) {
  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.open('PUT', url, true);
    Object.entries(headers).forEach(([k, v]) => xhr.setRequestHeader(k, v));

    xhr.upload.onprogress = (e) => {
      if (e.lengthComputable) {
        const percent = (e.loaded / e.total) * 100;
        onProgress(percent);
      }
    };
    xhr.onload = () => {
      if (xhr.status >= 200 && xhr.status < 300) {
        resolve();
      } else {
        reject(new Error('Upload to storage failed (' + xhr.status + ')'));
      }
    };
    xhr.onerror = () => reject(new Error('Network error while uploading file'));
    xhr.send(file);
  });
}

/* ══════════════════════════════════════════════════════════════════
   GET PRESIGNED URL
══════════════════════════════════════════════════════════════════ */
async function getPresignedUrl(mandal, village, docValue, file) {
  const res = await fetch('{{ route("pahani.presign") }}', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
    },
    body: JSON.stringify({ 
      mandal, 
      village, 
      docValue, 
      fileMime: file.type || 'application/pdf' 
    }),
  });
  const data = await res.json();
  if (!res.ok || !data.success) {
    throw new Error(data.message || 'Could not prepare upload.');
  }
  return data; // { key, url, headers }
}

/* ══════════════════════════════════════════════════════════════════
   MAIN SUBMIT FUNCTION (IMPROVED)
══════════════════════════════════════════════════════════════════ */
async function submitForm() {
  const mandalSel  = document.getElementById('mandal-select');
  const villageSel = document.getElementById('village-select');
  const mandal     = mandalSel.value;
  const village    = villageSel.value;

  // Validation
  if (!mandal || !village) { 
    showToast('Please select both Mandal and Village.', true); 
    return; 
  }
  if (state.rows.length === 0) { 
    showToast('Add at least one document record.', true); 
    return; 
  }

  for (const row of state.rows) {
    if (!row.value) { 
      showToast('Please select a document name for all rows.', true); 
      return; 
    }
    if (!row.physical) { 
      showToast(`Please select Physical Document (Yes/No) for "${row.value}".`, true); 
      return; 
    }
    if (row.physical === 'Yes' && !state.files[row.id] && !row.existingFileName) {
      showToast(`Please upload a PDF for "${row.value}".`, true); 
      return;
    }
  }

  // Show upload progress bar (instead of submit-overlay)
  showUploadProgress(state.rows);
  document.getElementById('submit-btn').disabled = true;

  try {
    // Step 1: Upload every file straight to R2, in parallel, with per-row progress
    const uploadTasks = state.rows
      .filter(row => state.files[row.id])
      .map(async row => {
        const file = state.files[row.id];
        const { key, url, headers } = await getPresignedUrl(mandal, village, row.value, file);
        await uploadFileWithProgress(url, file, headers, pct => updateRowProgress(row.id, pct));
        row.r2Key   = key;
        row.fileSize = file.size;
        row.fileMime = file.type || 'application/pdf';
        updateRowProgress(row.id, 100); // Mark as complete
      });

    // Wait for all uploads to complete
    await Promise.all(uploadTasks);
    
    // Update overall progress
    updateOverallProgress(100);

    // Step 2: Tiny JSON request, no files attached
    const recordsMeta = state.rows.map(row => ({
      docValue: row.value,
      physical: row.physical.toLowerCase(),
      r2Key:    row.r2Key || null,
      fileName: state.files[row.id]?.name || null,
      fileSize: row.fileSize || null,
      fileMime: row.fileMime || null,
      pahaniId: row.pahaniId || null,
      keepFile: !row.r2Key && !!row.existingFileName,
    }));

    const res = await fetch('{{ route("pahani.store") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
      },
      body: JSON.stringify({ mandal, village, records: recordsMeta }),
    });

    const data = await res.json();
    hideUploadProgress();
    document.getElementById('submit-btn').disabled = false;

    if (res.ok && data.success) {
      showToast('✔ ' + (data.message || 'Records saved successfully.'));
      clearRows();
      setTimeout(() => window.location.href = '{{ route("pahani.index") }}', 1200);
      return;
    }

    if (data.errors) {
      const list = Array.isArray(data.errors) ? data.errors : Object.values(data.errors).flat();
      renderFormErrors(list);
    } else {
      showToast(data.message || 'Submission failed.', true);
    }
  } catch (err) {
    hideUploadProgress();
    document.getElementById('submit-btn').disabled = false;
    showToast(err.message || 'Upload failed. Please try again.', true);
  }
}

/* ══════════════════════════════════════════════════════════════════
   FORM ERROR DISPLAY
══════════════════════════════════════════════════════════════════ */
function renderFormErrors(errors) {
  let box = document.getElementById('js-error-box');
  if (!box) {
    box = document.createElement('div');
    box.id = 'js-error-box';
    box.className = 'alert alert-error';
    document.querySelector('.main-body').insertBefore(box, document.querySelector('.notice-bar'));
  }
  box.innerHTML = `<strong>Please fix the following errors:</strong>
    <ul style="margin-top:5px;padding-left:16px">
      ${errors.map(e => `<li>${e}</li>`).join('')}
    </ul>`;
  box.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/* ══════════════════════════════════════════════════════════════════
   INIT
══════════════════════════════════════════════════════════════════ */
renderTable();
</script>

</body>
</html>