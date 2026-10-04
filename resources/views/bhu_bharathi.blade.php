<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Bhu Bharathi Disposals — Land Record Digitalization</title>
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
    .page-nav{display:flex;justify-content:space-between;align-items:center;background:#154360;border-bottom:2px solid #f39c12;padding:0 20px;font-size:11px}
    .nav-left{display:flex;align-items:center;gap:0;flex-wrap:wrap}
    .nav-item{color:#aed6f1;padding:8px 14px;cursor:pointer;border-right:1px solid rgba(255,255,255,0.1);transition:background 0.15s;text-decoration:none;display:inline-block}
    .nav-item:hover{background:rgba(255,255,255,0.1);color:white}
    .nav-item.active{background:#f39c12;color:#1a1a2e;font-weight:bold}
    .nav-right{margin-left:auto}
    .logout-btn{background:#dc3545;color:#fff;border:none;padding:6px 14px;border-radius:4px;cursor:pointer;font-weight:600;transition:.2s}
    .logout-btn:hover{background:#bb2d3b}

    /* ── LAYOUT ── */
    .main-body{padding:16px 20px;max-width:1280px;margin:0 auto}
    .page-heading{background:white;border:1px solid #d5e8f5;border-left:4px solid #154360;padding:10px 16px;margin-bottom:14px;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap}
    .page-heading h2{font-size:13px;font-weight:bold;color:#154360;text-transform:uppercase;letter-spacing:0.5px}
    .breadcrumb{font-size:10px;color:#666;margin-bottom:10px;display:flex;align-items:center;gap:4px}
    .breadcrumb a{color:#154360;text-decoration:none}
    .status-dot{width:8px;height:8px;border-radius:50%;background:#27ae60;display:inline-block;margin-right:4px}

    /* ── ALERTS ── */
    .alert{padding:9px 12px;border-radius:2px;font-size:11px;margin-bottom:12px;border-left:4px solid}
    .alert-error{background:#fdecea;border-color:#c0392b;color:#7f0000}
    .notice-bar{background:#fff3cd;border:1px solid #ffc107;border-left:4px solid #e67e22;padding:8px 12px;font-size:10px;color:#7d6608;margin-bottom:14px;display:flex;align-items:flex-start;gap:8px}
    .info-box{background:#eaf2f8;border:1px solid #b8d4e8;border-left:4px solid #154360;padding:10px 12px;font-size:11px;color:#154360}

    /* ── SECTION CARDS ── */
    .section-card{background:white;border:1px solid #d0dde8;margin-bottom:14px;border-radius:2px;overflow:hidden}
    .section-header{background:#154360;color:white;padding:8px 14px;font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;display:flex;align-items:center;gap:8px}
    .section-header .sec-num{background:#f39c12;color:#1a1a2e;width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:bold;flex-shrink:0}
    .section-header .sec-right{margin-left:auto;font-size:10px;font-weight:normal;text-transform:none;color:#aed6f1}
    .section-body{padding:14px}

    /* ── FORM FIELDS ── */
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:6px}
    .field-group{display:flex;flex-direction:column;gap:4px}
    .field-label{font-size:11px;font-weight:bold;color:#1a3a5c;text-transform:uppercase;letter-spacing:0.3px}
    .field-label .req{color:#c0392b;margin-left:2px}
    .field-hint{font-size:10px;color:#888}
    select,input.txt{width:100%;padding:6px 8px;border:1px solid #b0c4d8;border-radius:2px;font-size:11px;color:#1a1a2e;background:#f8fbfd;outline:none;transition:border-color 0.15s}
    select:focus,input.txt:focus{border-color:#154360;background:white;box-shadow:0 0 0 2px rgba(21,67,96,0.1)}
    select:disabled{opacity:0.55;cursor:not-allowed}
    input.txt.invalid,select.invalid{border-color:#c0392b;background:#fef5f5}

    /* ── PERMISSION CHIPS ── */
    .perm-chips{display:flex;gap:6px;flex-wrap:wrap;margin-top:8px}
    .chip{font-size:9px;font-weight:bold;padding:3px 8px;border-radius:10px;border:1px solid}
    .chip.on{background:#e8f5e9;color:#1b5e20;border-color:#a5d6a7}
    .chip.off{background:#f3f3f3;color:#999;border-color:#ddd;text-decoration:line-through}

    /* ── LOADING ── */
    .loading-bar{display:none;align-items:center;gap:8px;padding:8px 0;font-size:11px;color:#154360}
    .loading-bar.show{display:flex}
    .spinner{width:14px;height:14px;border:2px solid #d0dde8;border-top-color:#154360;border-radius:50%;animation:spin 0.7s linear infinite}
    @keyframes spin{to{transform:rotate(360deg)}}

    /* ── TABLES ── */
    .table-wrap{overflow-x:auto}
    .doc-table{width:100%;border-collapse:collapse;font-size:11px}
    .doc-table th{background:#eaf2f8;border:1px solid #c8dce9;padding:6px 10px;text-align:left;font-size:10px;font-weight:bold;text-transform:uppercase;color:#154360;letter-spacing:0.3px;white-space:nowrap}
    .doc-table td{border:1px solid #dce8f0;padding:7px 10px;vertical-align:middle}
    .doc-table tr:nth-child(even) td{background:#f7fbfd}
    .doc-table tr:hover td{background:#edf6ff}
    .muted{color:#888;font-size:10px}
    .empty-row td{text-align:center;color:#888;font-style:italic;padding:16px}

    /* ── UPLOAD ── */
    .upload-zone{border:1.5px dashed #154360;background:#eaf2f8;border-radius:2px;padding:8px 12px;text-align:center;cursor:pointer;position:relative;min-width:160px;transition:background 0.15s}
    .upload-zone:hover{background:#d6eaf8}
    .upload-zone input[type=file]{position:absolute;inset:0;opacity:0;cursor:pointer;width:100%;height:100%}
    .upload-zone .uz-icon{font-size:14px;color:#154360}
    .upload-zone .uz-text{font-size:10px;color:#154360;font-weight:bold}
    .uploaded-file{display:flex;align-items:center;gap:6px;background:#e8f5e9;border:1px solid #a5d6a7;border-radius:2px;padding:4px 8px;font-size:10px;color:#2e7d32;margin-top:4px;word-break:break-all}
    .uploaded-file .remove-btn{margin-left:auto;cursor:pointer;color:#c62828;font-weight:bold;font-size:11px;border:none;background:none;padding:0 2px}
    .saved-file{display:flex;align-items:center;gap:6px;background:#e3f2fd;border:1px solid #90caf9;border-radius:2px;padding:4px 8px;font-size:10px;color:#1565c0;margin-bottom:4px;word-break:break-all}
    .saved-file-label{font-size:9px;background:#154360;color:white;padding:1px 5px;border-radius:2px;flex-shrink:0}

    .row-progress{position:relative;height:16px;background:#eee;border-radius:4px;overflow:hidden;margin-top:6px;display:none}
    .row-progress .bar{height:100%;background:#2ecc71;width:0%;transition:width .15s ease}
    .row-progress .txt{position:absolute;top:0;left:6px;font-size:9px;line-height:16px;color:#333}

    /* ── BUTTONS ── */
    .btn-danger-sm{border:none;background:none;cursor:pointer;color:#c0392b;font-size:16px;padding:0 4px;line-height:1}
    .btn-danger-sm:hover{color:#7f0000}
    .add-row-btn{background:#154360;color:white;border:none;padding:7px 16px;font-size:11px;font-weight:bold;cursor:pointer;border-radius:2px;display:inline-flex;align-items:center;gap:6px;margin-top:10px;text-transform:uppercase;letter-spacing:0.3px;transition:background 0.15s}
    .add-row-btn:hover{background:#1a6fa8}
    .add-row-btn:disabled{background:#aaa;cursor:not-allowed}
    .row-info{font-size:10px;color:#666;margin-top:6px}
    .btn-primary{background:#154360;color:white;border:none;padding:9px 24px;font-size:12px;font-weight:bold;cursor:pointer;text-transform:uppercase;letter-spacing:0.5px;border-radius:2px;transition:background 0.15s}
    .btn-primary:hover{background:#1a6fa8}
    .btn-primary:disabled{background:#aaa;cursor:not-allowed}
    .btn-secondary{background:white;color:#154360;border:1.5px solid #154360;padding:8px 18px;font-size:11px;font-weight:bold;cursor:pointer;text-transform:uppercase;letter-spacing:0.3px;border-radius:2px;transition:all 0.15s}
    .btn-secondary:hover{background:#eaf2f8}
    .btn-link{display:inline-flex;align-items:center;gap:4px;border:1px solid #b8d4e8;background:#eaf2f8;color:#154360;padding:4px 9px;font-size:10px;font-weight:bold;border-radius:2px;cursor:pointer;text-decoration:none;white-space:nowrap}
    .btn-link:hover{background:#d6eaf8}
    .btn-link:disabled,.btn-link.disabled{background:#f3f3f3;color:#aaa;border-color:#ddd;cursor:not-allowed}
    .lock-tag{font-size:10px;color:#856404;background:#fff3cd;border:1px solid #ffc107;padding:3px 7px;border-radius:2px;white-space:nowrap}
    .actions{display:flex;gap:6px;justify-content:center;flex-wrap:wrap}

    /* ── FOOTER BAR ── */
    .form-footer{background:#eaf2f8;border-top:1px solid #c8dce9;padding:12px 14px;display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap}

    /* ── TOAST ── */
    .toast{position:fixed;top:20px;right:20px;background:#154360;color:white;padding:10px 18px;border-left:4px solid #f39c12;font-size:12px;border-radius:2px;z-index:9999;display:none;box-shadow:0 4px 12px rgba(0,0,0,0.3);max-width:340px}
    .toast.show{display:block}
    .toast.error{border-left-color:#c0392b;background:#7f0000}

    /* ── TOP UPLOAD PROGRESS PANEL ── */
    #upload-progress-container{display:none;position:fixed;top:0;left:0;right:0;background:white;border-bottom:3px solid #154360;padding:20px;z-index:8000;box-shadow:0 2px 8px rgba(0,0,0,0.15)}
    #upload-progress-container.show{display:block}
    .upload-progress-content{max-width:1040px;margin:0 auto}
    .upload-progress-header{display:flex;align-items:center;gap:12px;margin-bottom:16px}
    .upload-progress-spinner{width:20px;height:20px;border:2px solid #d0dde8;border-top-color:#154360;border-radius:50%;animation:spin 0.8s linear infinite;flex-shrink:0}
    .upload-progress-title{font-size:13px;font-weight:bold;color:#154360}
    .upload-progress-title .percent{color:#f39c12}
    .upload-progress-main-bar{width:100%;height:24px;background:#e8f0f7;border:1px solid #c0d5e3;border-radius:4px;overflow:hidden;margin-bottom:12px}
    .upload-progress-main-fill{height:100%;background:linear-gradient(90deg,#154360 0%,#1a6fa8 100%);width:0%;transition:width 0.3s ease}
    .upload-progress-rows{display:grid;grid-template-columns:1fr 1fr;gap:12px;max-height:240px;overflow-y:auto}
    .upload-progress-row{background:#f8fbfd;border:1px solid #d0dde8;border-radius:3px;padding:8px;font-size:10px}
    .upload-progress-row-name{font-weight:bold;color:#154360;margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
    .upload-progress-row-bar{width:100%;height:12px;background:#d0dde8;border-radius:2px;overflow:hidden;margin-bottom:3px}
    .upload-progress-row-fill{height:100%;background:#27ae60;width:0%;transition:width 0.2s ease}
    .upload-progress-row-fill.failed{background:#c0392b}
    .upload-progress-row-percent{text-align:right;color:#666;font-size:9px}

    /* ── EDIT MODAL ── */
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.5);z-index:7000;align-items:center;justify-content:center;padding:16px;overflow-y:auto}
    .modal-overlay.show{display:flex}
    .modal-box{background:white;border-radius:4px;box-shadow:0 6px 24px rgba(0,0,0,0.18);width:100%;max-width:520px;overflow:hidden;margin:auto}
    .modal-header{background:#154360;color:white;padding:10px 16px;display:flex;align-items:center;justify-content:space-between}
    .modal-header h3{font-size:12px;text-transform:uppercase;letter-spacing:0.4px}
    .modal-close{background:none;border:none;font-size:16px;color:#aed6f1;cursor:pointer}
    .modal-body{padding:16px;display:flex;flex-direction:column;gap:12px}
    .modal-footer{padding:12px 16px;border-top:1px solid #dce8f0;background:#fafbfc;display:flex;gap:8px;justify-content:flex-end}

    @media(max-width:700px){
      .form-row{grid-template-columns:1fr}
      .upload-progress-rows{grid-template-columns:1fr}
      .gov-title-block .dept-name{font-size:14px}
    }
  
    /* ── BULK ROWS ── */
    .bulk-table td{vertical-align:top}
    .bulk-table select,.bulk-table input.txt{min-width:130px}
    .bulk-table td.num{font-weight:bold;color:#154360;padding-top:12px}
    .bulk-table td.upcell{min-width:230px}
    .row-actions{display:flex;flex-direction:column;align-items:center;gap:6px}
    .btn-copy{border:1px solid #b8d4e8;background:#eaf2f8;color:#154360;cursor:pointer;font-size:10px;font-weight:bold;padding:2px 6px;border-radius:2px;white-space:nowrap}
    .btn-copy:hover{background:#d6eaf8}
    .upload-zone.disabled{background:#f3f3f3;border-color:#c5c5c5;cursor:not-allowed;opacity:0.65}
    .upload-zone.disabled input[type=file]{cursor:not-allowed}
    .upload-zone.disabled .uz-icon,.upload-zone.disabled .uz-text{color:#888}
    .row-status{margin-top:5px;font-size:10px;line-height:1.4;padding:4px 7px;border-radius:2px;display:none}
    .row-status.show{display:block}
    .row-status.st-checking{background:#f0f8ff;color:#154360;border:1px solid #b8d4e8}
    .row-status.st-exists{background:#e3f2fd;color:#0d47a1;border:1px solid #90caf9}
    .row-status.st-dup{background:#fff3cd;color:#7d6608;border:1px solid #ffc107}
    .row-status.st-warn{background:#fdecea;color:#7f0000;border:1px solid #f5c6cb}
    .row-status a{color:#0d47a1;font-weight:bold}
    tr.row-exists td{background:#f5f9ff !important}
    .mini-spin{display:inline-block;width:9px;height:9px;border:2px solid #b8d4e8;border-top-color:#154360;border-radius:50%;animation:spin 0.7s linear infinite;vertical-align:-1px;margin-right:4px}
  </style>
</head>
<body>
<div class="portal-wrap">

  <div class="toast" id="toast"></div>

  {{-- UPLOAD PROGRESS PANEL (TOP OF PAGE) --}}
  <div id="upload-progress-container">
    <div class="upload-progress-content">
      <div class="upload-progress-header">
        <div class="upload-progress-spinner"></div>
        <div class="upload-progress-title">
          <span id="upload-progress-label">Uploading Files…</span>
          <span class="percent"><span id="overall-progress-pct">0</span>%</span>
        </div>
      </div>
      <div class="upload-progress-main-bar">
        <div class="upload-progress-main-fill" id="overall-progress-bar"></div>
      </div>
      <div class="upload-progress-rows" id="upload-progress-rows-container"></div>
    </div>
  </div>

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
      BHU BHARATHI DISPOSALS — BULK UPLOAD — Sangareddy District ({{ auth()->user()?->name }}, {{ auth()->user()?->getMandal?->name }})
    </div>
  </div>

  {{-- ── NAV ── --}}
  @include('partials.user-nav', ['active' => 'bb-upload'])

  <div class="main-body">

    <div class="page-heading">
      <h2>🗂️ Bhu Bharathi Disposals</h2>
      <a href="{{ route('bhu-bharathi.my-files') }}" style="font-size:10px;color:#154360;font-weight:bold;text-decoration:none">📋 View my uploaded disposals →</a>
    </div>

    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a> › <a href="#">Bhu Bharathi</a> › Upload Disposals
    </div>

    @if($modules->isEmpty())
      <div class="alert alert-error">
        You have not been given upload permission for any Bhu Bharathi module. Please contact the administrator.
      </div>
    @endif

    @if($mandals->isEmpty())
      <div class="alert alert-error">
        You have not been given Bhu Bharathi upload permission for any mandal. Please contact the administrator.
      </div>
    @endif

    <div class="notice-bar">
      ℹ️&nbsp;<span>Add one row per PDF. Each row has its own <strong>Mandal</strong>, <strong>Village</strong>, <strong>Module</strong> and <strong>Application Number</strong>, so one submission can cover many mandals and villages.
      If that combination is already uploaded, the PDF upload for the row is disabled. All PDFs upload in parallel when you submit.</span>
    </div>

    {{-- ── UPLOAD BHU BHARATHI DISPOSALS ── --}}
    <div class="section-card">
      <div class="section-header">
        <span class="sec-num">1</span> Upload Bhu Bharathi Disposals
        <span class="sec-right">PDF only · max <span id="max-rows"></span> rows per submission</span>
      </div>
      <div class="section-body">
        <div id="js-error-anchor"></div>
        <div class="table-wrap">
          <table class="doc-table bulk-table">
            <thead>
              <tr>
                <th style="width:3%">#</th>
                <th>Mandal <span style="color:#c0392b">*</span></th>
                <th>Village <span style="color:#c0392b">*</span></th>
                <th>Module <span style="color:#c0392b">*</span></th>
                <th>Application No. <span style="color:#c0392b">*</span></th>
                <th>Upload PDF <span style="color:#c0392b">*</span></th>
                <th style="width:6%;text-align:center">Action</th>
              </tr>
            </thead>
            <tbody id="new-tbody"></tbody>
          </table>
        </div>
        <button type="button" class="add-row-btn" id="add-row-btn" onclick="addRow()">+ New Disposal Record</button>
        <div class="row-info" id="row-info"></div>
      </div>

      <div class="form-footer">
        <button type="button" class="btn-secondary" onclick="resetForm()">🔄 Reset Form</button>
        <div style="display:flex;align-items:center;gap:12px">
          <span style="font-size:10px;color:#666">Fields marked <span style="color:#c0392b;font-weight:bold">*</span> are mandatory</span>
          <button type="button" class="btn-primary" id="submit-btn" onclick="submitForm()">✔ Submit &amp; Register</button>
        </div>
      </div>
    </div>

  </div>{{-- /main-body --}}
</div>{{-- /portal-wrap --}}

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
/* ══════════════════════════════════════════════════════════════════
   CONFIG
══════════════════════════════════════════════════════════════════ */
const MANDALS = @json($mandals->map(fn ($m) => ['id' => $m->id, 'name' => $m->name, 'slug' => $m->slug])->values());  // upload-permitted
const MODULES = @json($modules);     // [{ id, name }] — active modules the admin allowed this user to upload

const ROUTES = {
  myFiles:   @json(route('bhu-bharathi.my-files')),
  store:     @json(route('bhu-bharathi.store')),
  check:     @json(route('bhu-bharathi.check')),
  presign:   @json(route('bhu-bharathi.presign')),
  mpInit:    @json(route('bhu-bharathi.multipart.init')),
  mpSign:    @json(route('bhu-bharathi.multipart.sign-part')),
  mpDone:    @json(route('bhu-bharathi.multipart.complete')),
  mpAbort:   @json(route('bhu-bharathi.multipart.abort')),
};

const MAX_ROWS             = 100;               // same limit as BhuBharathiController::MAX_ROWS
const CHECK_DELAY_MS       = 450;               // wait after typing before checking the server
const PART_SIZE            = 10 * 1024 * 1024;  // 10MB per part
const MULTIPART_THRESHOLD  = 20 * 1024 * 1024;  // above this, use multipart
const MAX_CONCURRENT_PARTS = 4;

/* ══════════════════════════════════════════════════════════════════
   STATE
   row = { id, mandalId, mandalSlug, villageId, villageSlug, moduleId, appNo,
           file, r2Key, exists, checking, checkFailed, checkSeq, checkTimer, el }
══════════════════════════════════════════════════════════════════ */
const state = { rows: [] };
let rowSeq = 0;
const villageCache = new Map();   // mandalId -> Promise<[{id, name, slug}]>

/* ══════════════════════════════════════════════════════════════════
   HELPERS + DIRECT-TO-R2 UPLOAD (unchanged)
══════════════════════════════════════════════════════════════════ */
function csrfToken() { return document.querySelector('meta[name=csrf-token]').content; }

function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s ?? ''; return d.innerHTML; }

function getFileExt(file) { return (file.name.split('.').pop() || '').toLowerCase(); }

function isPdf(file) { return getFileExt(file) === 'pdf'; }

function shortName(n) { return n.length > 34 ? n.slice(0, 31) + '…' : n; }

function humanSize(b) {
  if (!b) return '';
  const u = ['B','KB','MB','GB']; let i = 0;
  while (b >= 1024 && i < u.length - 1) { b /= 1024; i++; }
  return b.toFixed(i ? 2 : 0) + ' ' + u[i];
}

function showToast(msg, isError = false) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'toast show' + (isError ? ' error' : '');
  clearTimeout(showToast._t);
  showToast._t = setTimeout(() => t.className = 'toast', 4500);
}

async function postJson(url, body, method = 'POST') {
  const res = await fetch(url, {
    method,
    headers: {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-CSRF-TOKEN': csrfToken(),
      'X-Requested-With': 'XMLHttpRequest',
    },
    body: JSON.stringify(body),
  });
  const data = await res.json().catch(() => ({}));
  return { res, data };
}

function putWithProgress(url, blob, headers, onProgress) {
  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.open('PUT', url, true);
    Object.entries(headers || {}).forEach(([k, v]) => xhr.setRequestHeader(k, v));
    xhr.upload.onprogress = e => { if (e.lengthComputable) onProgress(e.loaded / e.total); };
    xhr.onload  = () => (xhr.status >= 200 && xhr.status < 300) ? resolve(xhr) : reject(new Error('Upload to storage failed (' + xhr.status + ')'));
    xhr.onerror = () => reject(new Error('Network error while uploading file'));
    xhr.send(blob);
  });
}

async function uploadSingle(mandal, village, file, onProgress) {
  const { res, data } = await postJson(ROUTES.presign, { mandal, village, fileExt: 'pdf' });
  if (!res.ok || !data.success) throw new Error(data.message || 'Could not prepare upload.');
  await putWithProgress(data.url, file, data.headers, f => onProgress(Math.round(f * 100)));
  return data.key;
}

async function uploadMultipart(mandal, village, file, onProgress) {
  const init = await postJson(ROUTES.mpInit, { mandal, village, fileExt: 'pdf' });
  if (!init.res.ok || !init.data.success) throw new Error(init.data.message || 'Could not start upload.');
  const { key, uploadId } = init.data;

  const totalParts = Math.ceil(file.size / PART_SIZE);
  const partProgress = new Array(totalParts).fill(0);
  const done = [];

  const uploadPart = async (partNumber) => {
    const start = (partNumber - 1) * PART_SIZE;
    const blob  = file.slice(start, Math.min(start + PART_SIZE, file.size));
    const sign  = await postJson(ROUTES.mpSign, { key, uploadId, partNumber });
    if (!sign.res.ok || !sign.data.success) throw new Error(`Could not sign part ${partNumber}.`);

    const xhr = await putWithProgress(sign.data.url, blob, {}, f => {
      partProgress[partNumber - 1] = f;
      onProgress(Math.round(partProgress.reduce((a, b) => a + b, 0) / totalParts * 100));
    });
    done.push({ PartNumber: partNumber, ETag: xhr.getResponseHeader('ETag') });
  };

  try {
    let cursor = 1;
    const worker = async () => { while (cursor <= totalParts) await uploadPart(cursor++); };
    await Promise.all(Array.from({ length: Math.min(MAX_CONCURRENT_PARTS, totalParts) }, worker));

    done.sort((a, b) => a.PartNumber - b.PartNumber);
    const fin = await postJson(ROUTES.mpDone, { key, uploadId, parts: done });
    if (!fin.res.ok || !fin.data.success) throw new Error('Could not finalise upload.');
    return key;
  } catch (err) {
    postJson(ROUTES.mpAbort, { key, uploadId }).catch(() => {}); // don't leave billed parts in R2
    throw err;
  }
}

function uploadPdf(mandal, village, file, onProgress) {
  return file.size > MULTIPART_THRESHOLD
    ? uploadMultipart(mandal, village, file, onProgress)
    : uploadSingle(mandal, village, file, onProgress);
}

function showUploadProgress(items, label = 'Uploading Files…') {
  document.getElementById('upload-progress-label').textContent = label;
  document.getElementById('upload-progress-rows-container').innerHTML = items.map(it => `
    <div class="upload-progress-row">
      <div class="upload-progress-row-name">${escapeHtml(it.label)}</div>
      <div class="upload-progress-row-bar"><div class="upload-progress-row-fill" id="upload-row-progress-${it.id}"></div></div>
      <div class="upload-progress-row-percent"><span id="upload-row-pct-${it.id}">0</span>%</div>
    </div>`).join('');
  updateOverallProgress(0);
  document.getElementById('upload-progress-container').classList.add('show');
}

function hideUploadProgress() {
  document.getElementById('upload-progress-container').classList.remove('show');
}

function updateOverallProgress(pct) {
  document.getElementById('overall-progress-bar').style.width = pct + '%';
  document.getElementById('overall-progress-pct').textContent = Math.round(pct);
}

function updateRowProgress(id, pct, failed = false) {
  const wrap = document.getElementById('progress-wrap-' + id);
  if (wrap) {
    wrap.style.display = 'block';
    document.getElementById('progress-bar-' + id).style.width = pct + '%';
    document.getElementById('progress-text-' + id).textContent = failed ? 'Failed' : pct + '%';
  }
  const fill = document.getElementById('upload-row-progress-' + id);
  if (fill) { fill.style.width = (failed ? 100 : pct) + '%'; fill.classList.toggle('failed', failed); }
  const p = document.getElementById('upload-row-pct-' + id);
  if (p) p.textContent = failed ? '✕' : Math.round(pct);
}

function markInvalid(id) { document.getElementById(id)?.classList.add('invalid'); }

function renderFormErrors(errors) {
  document.getElementById('js-success-box')?.remove();
  let box = document.getElementById('js-error-box');
  if (!box) {
    box = document.createElement('div');
    box.id = 'js-error-box';
    box.className = 'alert alert-error';
    document.getElementById('js-error-anchor').appendChild(box);
  }
  box.innerHTML = `<strong>Please fix the following errors:</strong>
    <ul style="margin-top:5px;padding-left:16px">${errors.map(e => `<li>${escapeHtml(e)}</li>`).join('')}</ul>`;
  box.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

function showSuccessBox(msg) {
  document.getElementById('js-error-box')?.remove();
  let box = document.getElementById('js-success-box');
  if (!box) {
    box = document.createElement('div');
    box.id = 'js-success-box';
    box.className = 'alert';
    box.style.cssText = 'background:#e8f5e9;border-color:#27ae60;color:#1b5e20';
    document.getElementById('js-error-anchor').appendChild(box);
  }
  box.innerHTML = `✔ ${escapeHtml(msg)} &nbsp;<a href="${ROUTES.myFiles}" style="color:#154360;font-weight:bold">View my uploaded disposals →</a>`;
  box.scrollIntoView({ behavior: 'smooth', block: 'start' });
}


function optionList(sel, items, placeholder, selectedId) {
  sel.innerHTML = `<option value="">${placeholder}</option>`;
  items.forEach(it => {
    const o = document.createElement('option');
    o.value = it.id;
    o.textContent = it.name;
    if (it.slug) o.dataset.slug = it.slug;
    if (String(selectedId) === String(it.id)) o.selected = true;
    sel.appendChild(o);
  });
}

function loadVillages(mandalId) {
  const key = String(mandalId);
  if (!villageCache.has(key)) {
    villageCache.set(key,
      fetch(`/api/mandals/${mandalId}/villages`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => { if (!r.ok) throw new Error('HTTP ' + r.status); return r.json(); })
        .then(v => Array.isArray(v) ? v : (v.data || []))
        .catch(err => { villageCache.delete(key); throw err; })
    );
  }
  return villageCache.get(key);
}

/* ══════════════════════════════════════════════════════════════════
   ROWS
══════════════════════════════════════════════════════════════════ */
function newRowState(copyFrom = null) {
  return {
    id: 'r' + (++rowSeq),
    mandalId:   copyFrom?.mandalId   || '',
    mandalSlug: copyFrom?.mandalSlug || '',
    villageId:  copyFrom?.villageId  || '',
    villageSlug:copyFrom?.villageSlug|| '',
    moduleId:   copyFrom?.moduleId   || '',
    appNo: '', file: null, r2Key: null,
    exists: null, checking: false, checkFailed: false, checkSeq: 0, checkTimer: null,
    el: {},
  };
}

function addRow(copyFrom = null) {
  if (state.rows.length >= MAX_ROWS) {
    showToast(`You can add at most ${MAX_ROWS} rows per submission.`, true);
    return;
  }
  const row = newRowState(copyFrom);
  state.rows.push(row);
  document.getElementById('new-tbody').appendChild(buildRow(row));
  if (row.mandalId) fillVillages(row, row.villageId);
  afterRowsChanged();
  (copyFrom ? row.el.app : row.el.mandal).focus();
}

function removeRow(id) {
  const row = state.rows.find(r => r.id === id);
  if (!row) return;
  clearTimeout(row.checkTimer);
  row.el.tr.remove();
  state.rows = state.rows.filter(r => r.id !== id);
  if (state.rows.length === 0) addRow();
  afterRowsChanged();
}

function buildRow(row) {
  const tr = document.createElement('tr');
  tr.innerHTML = `
    <td class="num"></td>
    <td><select class="f-mandal"></select></td>
    <td><select class="f-village" disabled><option value="">— Select Mandal first —</option></select></td>
    <td><select class="f-module"></select></td>
    <td><input type="text" class="txt f-app" maxlength="100" placeholder="Application no."></td>
    <td class="upcell">
      <div class="upload-zone disabled" title="Select mandal, village & module first">
        <input type="file" accept=".pdf,application/pdf" disabled>
        <div class="uz-icon">📄</div>
        <div class="uz-text">Select mandal, village &amp; module first</div>
      </div>
      <div class="file-tag-wrap"></div>
      <div class="row-progress" id="progress-wrap-${row.id}">
        <div class="bar" id="progress-bar-${row.id}"></div><span class="txt" id="progress-text-${row.id}">0%</span>
      </div>
      <div class="row-status"></div>
    </td>
    <td style="text-align:center">
      <div class="row-actions">
        <button type="button" class="btn-danger-sm" title="Remove row">✕</button>
        <button type="button" class="btn-copy" title="New row with the same mandal, village and module">⧉ Copy</button>
      </div>
    </td>`;

  row.el = {
    tr,
    num:     tr.querySelector('.num'),
    mandal:  tr.querySelector('.f-mandal'),
    village: tr.querySelector('.f-village'),
    module:  tr.querySelector('.f-module'),
    app:     tr.querySelector('.f-app'),
    zone:    tr.querySelector('.upload-zone'),
    zoneTxt: tr.querySelector('.uz-text'),
    file:    tr.querySelector('input[type=file]'),
    tagWrap: tr.querySelector('.file-tag-wrap'),
    status:  tr.querySelector('.row-status'),
  };

  optionList(row.el.mandal, MANDALS, '— Select Mandal —', row.mandalId);
  optionList(row.el.module, MODULES, '— Select Module —', row.moduleId);

  row.el.mandal.addEventListener('change', () => onMandalChange(row));
  row.el.village.addEventListener('change', () => onVillageChange(row));
  row.el.module.addEventListener('change', () => {
    row.moduleId = row.el.module.value;
    row.el.module.classList.remove('invalid');
    scheduleCheck(row);
  });
  row.el.app.addEventListener('input', () => {
    row.appNo = row.el.app.value;
    row.el.app.classList.remove('invalid');
    scheduleCheck(row);
  });
  row.el.file.addEventListener('change', () => onFilePicked(row));
  tr.querySelector('.btn-danger-sm').onclick = () => removeRow(row.id);
  tr.querySelector('.btn-copy').onclick = () => addRow(row);

  return tr;
}

function onMandalChange(row) {
  const opt = row.el.mandal.selectedOptions[0];
  row.mandalId = row.el.mandal.value;
  row.mandalSlug = opt?.dataset.slug || '';
  row.villageId = ''; row.villageSlug = '';
  row.r2Key = null;                       // R2 key is tied to mandal/village folder
  row.el.mandal.classList.remove('invalid');
  fillVillages(row, null);
  scheduleCheck(row);
}

function fillVillages(row, selectedId) {
  const sel = row.el.village;
  if (!row.mandalId) {
    sel.innerHTML = '<option value="">— Select Mandal first —</option>';
    sel.disabled = true;
    return;
  }
  sel.innerHTML = '<option value="">Loading villages…</option>';
  sel.disabled = true;
  const forMandal = row.mandalId;
  loadVillages(forMandal)
    .then(villages => {
      if (row.mandalId !== forMandal) return;   // mandal changed while loading
      optionList(sel, villages, '— Select Village —', selectedId);
      sel.disabled = false;
      if (selectedId && sel.value !== String(selectedId)) { row.villageId = ''; row.villageSlug = ''; }
      afterRowsChanged();
    })
    .catch(() => {
      sel.innerHTML = '<option value="">Failed to load — reselect mandal</option>';
      showToast('Failed to load villages. Please try again.', true);
    });
}

function onVillageChange(row) {
  const opt = row.el.village.selectedOptions[0];
  row.villageId = row.el.village.value;
  row.villageSlug = opt?.dataset.slug || '';
  row.r2Key = null;
  row.el.village.classList.remove('invalid');
  scheduleCheck(row);
}

function onFilePicked(row) {
  const f = row.el.file.files[0];
  if (!f) return;
  if (row.exists || !dropdownsReady(row)) { row.el.file.value = ''; return; }
  if (!isPdf(f)) { showToast('Only PDF files are allowed.', true); row.el.file.value = ''; return; }
  setRowFile(row, f);
}

function setRowFile(row, file) {
  row.file = file;
  row.r2Key = null;
  row.el.tagWrap.innerHTML = '';
  if (file) {
    const tag = document.createElement('div');
    tag.className = 'uploaded-file';
    tag.innerHTML = `📄 ${escapeHtml(shortName(file.name))} <span class="muted">(${humanSize(file.size)})</span>
      <button type="button" class="remove-btn" title="Remove">✕</button>`;
    tag.querySelector('.remove-btn').onclick = () => { row.el.file.value = ''; setRowFile(row, null); };
    row.el.tagWrap.appendChild(tag);
  } else {
    row.el.file.value = '';
  }
  refreshInfo();
}

/** Upload is allowed once mandal, village and module are selected (application no. not needed). */
function dropdownsReady(r) {
  return !!(r.mandalId && r.villageId && r.moduleId);
}

function comboKey(r) {
  return [r.mandalId, r.villageId, r.moduleId, r.appNo.trim().toLowerCase()].join('|');
}
function isComplete(r) {
  return r.mandalId && r.villageId && r.moduleId && r.appNo.trim() !== '';
}

/* ══════════════════════════════════════════════════════════════════
   "ALREADY UPLOADED?" CHECK  →  GET bhu-bharathi/check
══════════════════════════════════════════════════════════════════ */
function scheduleCheck(row) {
  clearTimeout(row.checkTimer);
  row.checkSeq++;                 // invalidates any request still in flight
  row.exists = null;
  row.checkFailed = false;
  row.checking = isComplete(row);
  if (row.checking) row.checkTimer = setTimeout(() => runCheck(row), CHECK_DELAY_MS);
  afterRowsChanged();
}

async function runCheck(row) {
  const seq = row.checkSeq;
  const qs = new URLSearchParams({
    mandal_id: row.mandalId, village_id: row.villageId,
    module_id: row.moduleId, application_number: row.appNo.trim(),
  });
  try {
    const res  = await fetch(`${ROUTES.check}?${qs}`, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' } });
    const data = await res.json().catch(() => ({}));
    if (seq !== row.checkSeq) return;            // inputs changed meanwhile
    if (!res.ok || !data.success) throw new Error(data.message || 'check failed');
    row.exists = data.exists ? (data.record || {}) : null;
    if (row.exists && row.file) setRowFile(row, null);   // can't upload over an existing record
  } catch (e) {
    if (seq !== row.checkSeq) return;
    row.checkFailed = true;
  }
  row.checking = false;
  afterRowsChanged();
}

/* ══════════════════════════════════════════════════════════════════
   ROW STATUS (disabled upload, duplicates, checking…)
══════════════════════════════════════════════════════════════════ */
function afterRowsChanged() {
  // duplicates inside this form (same 4 values as an earlier row)
  const firstSeen = new Map();
  state.rows.forEach((r, i) => {
    r.dupOf = null;
    if (!isComplete(r)) return;
    const k = comboKey(r);
    if (firstSeen.has(k)) r.dupOf = firstSeen.get(k);
    else firstSeen.set(k, i + 1);
  });

  state.rows.forEach((r, i) => {
    r.el.num.textContent = i + 1;
    const blocked  = !!r.exists;
    const ready    = dropdownsReady(r);
    const disabled = blocked || !ready;
    const hint     = blocked ? 'Already uploaded'
                   : ready   ? 'Click to upload PDF'
                   :           'Select mandal, village & module first';
    r.el.zone.classList.toggle('disabled', disabled);
    r.el.file.disabled = disabled;
    r.el.zoneTxt.textContent = hint;
    r.el.zone.title = hint;
    r.el.tr.classList.toggle('row-exists', blocked);

    const st = r.el.status;
    st.className = 'row-status';
    st.innerHTML = '';
    if (r.checking) {
      st.className += ' show st-checking';
      st.innerHTML = '<span class="mini-spin"></span>Checking if already uploaded…';
    } else if (r.exists) {
      const rec = r.exists;
      st.className += ' show st-exists';
      st.innerHTML = `✔ Already uploaded${rec.uploader_name ? ' by ' + escapeHtml(rec.uploader_name) : ''}${rec.created_at ? ' on ' + escapeHtml(rec.created_at) : ''}.
        ${rec.file_url ? `<a href="${rec.file_url}" target="_blank" rel="noopener">👁 View</a> ·` : ''}
        Change a field or remove this row.`;
    } else if (r.dupOf) {
      st.className += ' show st-dup';
      st.textContent = `⚠ Same mandal, village, module and application no. as row ${r.dupOf}.`;
    } else if (r.checkFailed) {
      st.className += ' show st-warn';
      st.textContent = 'Could not check for an existing upload — it will be checked again on submit.';
    }
  });

  document.getElementById('add-row-btn').disabled = state.rows.length >= MAX_ROWS || !MANDALS.length || !MODULES.length;
  refreshInfo();
}

function refreshInfo() {
  const files  = state.rows.filter(r => r.file).length;
  const exists = state.rows.filter(r => r.exists).length;
  document.getElementById('row-info').textContent =
    `${state.rows.length} record(s) | ${files} PDF(s) attached` + (exists ? ` | ${exists} already uploaded` : '');
}

/* ══════════════════════════════════════════════════════════════════
   VALIDATION
══════════════════════════════════════════════════════════════════ */
function validateRows() {
  const errors = [];
  state.rows.forEach((r, i) => {
    const n = i + 1;
    if (!r.mandalId)       { errors.push(`Row ${n}: Please select a mandal.`);  markEl(r.el.mandal); }
    if (!r.villageId)      { errors.push(`Row ${n}: Please select a village.`); markEl(r.el.village); }
    if (!r.moduleId)       { errors.push(`Row ${n}: Please select a module.`);  markEl(r.el.module); }
    if (!r.appNo.trim())   { errors.push(`Row ${n}: Application number is required.`); markEl(r.el.app); }
    if (r.exists)          { errors.push(`Row ${n}: Already uploaded for this mandal, village and module — change it or remove the row.`); }
    else if (r.dupOf)      { errors.push(`Row ${n}: Same mandal, village, module and application no. as row ${r.dupOf}.`); markEl(r.el.app); }
    else if (!r.file && !r.r2Key) { errors.push(`Row ${n}: Please upload the PDF.`); }
  });
  return errors;
}
function markEl(el) { el.classList.add('invalid'); }

/* ══════════════════════════════════════════════════════════════════
   SUBMIT — parallel PDF uploads to R2, then one small JSON request
══════════════════════════════════════════════════════════════════ */
async function submitForm() {
  if (state.rows.some(r => r.checking)) {
    showToast('Please wait — still checking for existing uploads.', true);
    return;
  }
  document.getElementById('js-error-box')?.remove();
  document.getElementById('js-success-box')?.remove();

  const errors = validateRows();
  if (errors.length) { renderFormErrors(errors); return; }

  const btn = document.getElementById('submit-btn');
  btn.disabled = true;

  const pending    = state.rows.filter(r => r.file && !r.r2Key);
  const totalBytes = pending.reduce((s, r) => s + r.file.size, 0) || 1;
  const loaded = {};
  const recompute = () => updateOverallProgress(
    pending.reduce((s, r) => s + r.file.size * (loaded[r.id] || 0) / 100, 0) / totalBytes * 100
  );
  const rowNo = r => state.rows.indexOf(r) + 1;

  try {
    if (pending.length) {
      showUploadProgress(pending.map(r => ({ id: r.id, label: `Row ${rowNo(r)} · ${r.appNo.trim()} — ${r.file.name}` })));

      // Step 1 — each PDF straight to R2 (into its own mandal/village folder), in parallel
      const results = await Promise.allSettled(pending.map(async row => {
        try {
          const key = await uploadPdf(row.mandalSlug, row.villageSlug, row.file, pct => {
            loaded[row.id] = pct; updateRowProgress(row.id, pct); recompute();
          });
          row.r2Key = key;
          loaded[row.id] = 100; updateRowProgress(row.id, 100); recompute();
        } catch (e) {
          updateRowProgress(row.id, 0, true);
          throw new Error(`Row ${rowNo(row)} (${row.appNo.trim()}): ${e.message}`);
        }
      }));

      const failed = results.filter(r => r.status === 'rejected').map(r => r.reason.message);
      if (failed.length) {
        hideUploadProgress();
        renderFormErrors(['Some PDFs failed to upload. Submit again to retry only the failed ones.', ...failed]);
        return;
      }
      updateOverallProgress(100);
    }

    // Step 2 — one JSON request with every row's mandal / village / module
    document.getElementById('upload-progress-label').textContent = 'Saving records…';
    const { res, data } = await postJson(ROUTES.store, {
      records: state.rows.map(r => ({
        mandal_id:          Number(r.mandalId),
        village_id:         Number(r.villageId),
        module_id:          Number(r.moduleId),
        application_number: r.appNo.trim(),
        r2Key:              r.r2Key,
        fileName:           r.file?.name || null,
        fileSize:           r.file?.size || null,
      })),
    });
    hideUploadProgress();

    if (res.ok && data.success) {
      showToast('✔ ' + (data.message || 'Disposals saved successfully.'));
      clearAllRows();
      addRow();
      showSuccessBox(data.message || 'Disposals saved successfully.');
      return;
    }

    if (data.errors) {
      renderFormErrors(Array.isArray(data.errors) ? data.errors : Object.values(data.errors).flat());
      // someone may have uploaded the same combination meanwhile — re-check all rows
      state.rows.forEach(r => { if (isComplete(r)) scheduleCheck(r); });
    } else {
      showToast(data.message || 'Submission failed.', true);
    }
  } catch (err) {
    hideUploadProgress();
    showToast(err.message || 'Upload failed. Please try again.', true);
  } finally {
    btn.disabled = !MANDALS.length || !MODULES.length;
  }
}

/* ══════════════════════════════════════════════════════════════════
   RESET / INIT
══════════════════════════════════════════════════════════════════ */
function clearAllRows() {
  state.rows.forEach(r => clearTimeout(r.checkTimer));
  state.rows = [];
  document.getElementById('new-tbody').innerHTML = '';
}

function resetForm() {
  if (!confirm('Reset the form? All unsaved rows will be lost.')) return;
  clearAllRows();
  addRow();
  document.getElementById('js-error-box')?.remove();
  document.getElementById('js-success-box')?.remove();
}

document.getElementById('max-rows').textContent = MAX_ROWS;
if (!MANDALS.length || !MODULES.length) {
  document.getElementById('submit-btn').disabled = true;
}
addRow();
</script>
</body>
</html>