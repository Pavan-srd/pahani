<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>My Bhu Bharathi Disposals — Land Record Digitalization</title>
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
    .main-body{padding:16px 20px;max-width:1040px;margin:0 auto}
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
  
    /* ── MY FILES PAGE ── */
    .search-bar{display:flex;gap:8px;align-items:flex-end;flex-wrap:wrap}
    .search-bar .field-group{flex:1;min-width:220px}
    .summary-line{font-size:10px;color:#666;margin-top:8px}
    .app-no{font-family:Consolas,monospace;font-weight:bold;color:#1a1a2e}
    .mod-tag{display:inline-block;background:#e8f5e9;color:#1b5e20;border:1px solid #a5d6a7;border-radius:10px;padding:2px 8px;font-size:10px;font-weight:bold}
    mark{background:#fff3a8;padding:0 1px}
    .pager{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap;margin-top:12px;font-size:11px;color:#555}
    .pager-links{display:flex;gap:4px;flex-wrap:wrap}
    .pager-links a,.pager-links span{min-width:30px;text-align:center;padding:5px 9px;border:1px solid #c8dce9;border-radius:2px;background:white;color:#154360;text-decoration:none;font-weight:bold}
    .pager-links a:hover{background:#eaf2f8}
    .pager-links .current{background:#154360;color:white;border-color:#154360}
    .pager-links .disabled{color:#bbb;background:#f6f6f6}
  </style>
</head>
<body>
<div class="portal-wrap">

  <div class="toast" id="toast"></div>

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
      MY BHU BHARATHI DISPOSALS — Sangareddy District ({{ auth()->user()?->name }}, {{ auth()->user()?->getMandal?->name }})
    </div>
  </div>

  {{-- ── NAV ── --}}
  @include('partials.user-nav', ['active' => 'bb-view'])

  <div class="main-body">

    <div class="page-heading">
      <h2>📋 My Bhu Bharathi Disposals</h2>
      <a href="{{ route('bhu-bharathi.index') }}" style="font-size:10px;color:#154360;font-weight:bold;text-decoration:none">📤 Upload new disposals →</a>
    </div>

    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a> › <a href="#">Bhu Bharathi</a> › View Disposals
    </div>

    {{-- ── SEARCH ── --}}
    <div class="section-card">
      <div class="section-header">
        <span class="sec-num">1</span> Search
      </div>
      <div class="section-body">
        <form method="GET" action="{{ route('bhu-bharathi.my-files') }}" class="search-bar">
          <div class="field-group">
            <label class="field-label" for="q">File / Application Number</label>
            <input type="text" class="txt" id="q" name="q" value="{{ $q }}" maxlength="100" placeholder="Type full or part of the application number…" autofocus>
          </div>
          <button type="submit" class="btn-primary" style="padding:7px 20px">🔎 Search</button>
          @if($q !== '')
            <a href="{{ route('bhu-bharathi.my-files') }}" class="btn-secondary" style="text-decoration:none;padding:6px 16px">✕ Clear</a>
          @endif
        </form>
        <div class="summary-line">
          @if($q !== '')
            {{ $records->total() }} result(s) for “<strong>{{ $q }}</strong>”
          @else
            You have uploaded {{ $records->total() }} disposal(s) in total.
          @endif
        </div>
      </div>
    </div>

    {{-- ── LIST ── --}}
    <div class="section-card">
      <div class="section-header">
        <span class="sec-num">2</span> Uploaded Disposals
        <span class="sec-right">
          @if($records->total())
            Showing {{ $records->firstItem() }}–{{ $records->lastItem() }} of {{ $records->total() }}
          @endif
        </span>
      </div>
      <div class="section-body">
        <div class="table-wrap">
          <table class="doc-table">
            <thead>
              <tr>
                <th style="width:5%">#</th>
                <th>Mandal</th>
                <th>Village</th>
                <th>Module</th>
                <th>File / Application No.</th>
                <th>PDF</th>
                <th>Uploaded</th>
                <th style="width:14%;text-align:center">Actions</th>
              </tr>
            </thead>
            <tbody id="files-tbody"></tbody>
          </table>
        </div>

        {{-- ── PAGINATION ── --}}
        @if($records->hasPages())
          @php
            $cur = $records->currentPage();
            $last = $records->lastPage();
            $from = max(1, $cur - 2);
            $to = min($last, $cur + 2);
          @endphp
          <div class="pager">
            <div>Page {{ $cur }} of {{ $last }}</div>
            <div class="pager-links">
              @if($records->onFirstPage())
                <span class="disabled">‹ Prev</span>
              @else
                <a href="{{ $records->previousPageUrl() }}">‹ Prev</a>
              @endif

              @if($from > 1)
                <a href="{{ $records->url(1) }}">1</a>
                @if($from > 2)<span class="disabled">…</span>@endif
              @endif
              @for($i = $from; $i <= $to; $i++)
                @if($i === $cur)
                  <span class="current">{{ $i }}</span>
                @else
                  <a href="{{ $records->url($i) }}">{{ $i }}</a>
                @endif
              @endfor
              @if($to < $last)
                @if($to < $last - 1)<span class="disabled">…</span>@endif
                <a href="{{ $records->url($last) }}">{{ $last }}</a>
              @endif

              @if($records->hasMorePages())
                <a href="{{ $records->nextPageUrl() }}">Next ›</a>
              @else
                <span class="disabled">Next ›</span>
              @endif
            </div>
          </div>
        @endif
      </div>
    </div>

  </div>{{-- /main-body --}}
</div>{{-- /portal-wrap --}}

{{-- ── EDIT MODAL ── --}}
<div class="modal-overlay" id="edit-modal" onclick="if(event.target===this && !editState.busy) closeEditModal()">
  <div class="modal-box">
    <div class="modal-header">
      <h3>✎ Edit Disposal <span id="edit-title-id"></span></h3>
      <button class="modal-close" type="button" onclick="closeEditModal()">✕</button>
    </div>
    <div class="modal-body">
      <div class="field-group">
        <label class="field-label" for="edit-module">Module <span class="req">*</span></label>
        <select id="edit-module"></select>
      </div>
      <div class="field-group">
        <label class="field-label" for="edit-appno">Application Number <span class="req">*</span></label>
        <input type="text" class="txt" id="edit-appno" maxlength="100">
      </div>
      <div class="field-group">
        <label class="field-label">PDF</label>
        <div id="edit-upload-cell"></div>
        <div class="field-hint">Leave empty to keep the saved PDF.</div>
      </div>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn-secondary" onclick="closeEditModal()">Cancel</button>
      <button type="button" class="btn-primary" id="edit-save-btn" onclick="saveEdit()">Save Changes</button>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
/* ══════════════════════════════════════════════════════════════════
   CONFIG
══════════════════════════════════════════════════════════════════ */
const MODULES = @json($modules);   // modules this user may upload (edit-popup dropdown)
const SEARCH  = @json($q);
const FIRST_NO = {{ (int) ($records->firstItem() ?? 1) }};

const ROUTES = {
  update:    @json(url('/bhu-bharathi')),              // + '/{id}'
  presign:   @json(route('bhu-bharathi.presign')),
  mpInit:    @json(route('bhu-bharathi.multipart.init')),
  mpSign:    @json(route('bhu-bharathi.multipart.sign-part')),
  mpDone:    @json(route('bhu-bharathi.multipart.complete')),
  mpAbort:   @json(route('bhu-bharathi.multipart.abort')),
};

const PART_SIZE            = 10 * 1024 * 1024;
const MULTIPART_THRESHOLD  = 20 * 1024 * 1024;
const MAX_CONCURRENT_PARTS = 4;

// state.saved keeps the name used by the edit-popup functions
const state = { saved: @json($rows) };

/* ══════════════════════════════════════════════════════════════════
   HELPERS + DIRECT-TO-R2 UPLOAD  (same code as the upload page)
══════════════════════════════════════════════════════════════════ */
function refreshInfo() {}   // upload-page counter; not used here

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

/**
 * Fill a <select> with the modules this user may upload.
 * `extra` lets the edit modal keep the record's current module even when it
 * is not in the user's list (not permitted, or deactivated).
 */
function fillModuleSelect(sel, selectedId, extra = null) {
  sel.innerHTML = '<option value="">— Select Module —</option>';
  const list = [...MODULES];
  if (extra && extra.id && !list.some(m => m.id === extra.id)) {
    list.push({ id: extra.id, name: extra.name + ' (current)' });
  }
  list.forEach(m => {
    const o = document.createElement('option');
    o.value = m.id;
    o.textContent = m.name;
    if (Number(selectedId) === Number(m.id)) o.selected = true;
    sel.appendChild(o);
  });
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

/**
 * Upload zone + selected-file tag + inline progress bar.
 * Used by the new-rows table and the edit modal.
 */
function buildUploadWidget({ progressId, currentFile, onPick, onClear, replaceLabel }) {
  const wrap = document.createElement('div');

  const zone = document.createElement('div');
  zone.className = 'upload-zone';
  zone.innerHTML = `<input type="file" accept=".pdf,application/pdf">
    <div class="uz-icon">📄</div>
    <div class="uz-text">${replaceLabel || 'Click to upload PDF'}</div>`;
  const fi = zone.querySelector('input');
  wrap.appendChild(zone);

  const showTag = (file) => {
    wrap.querySelector('.uploaded-file')?.remove();
    if (!file) return;
    const tag = document.createElement('div');
    tag.className = 'uploaded-file';
    tag.innerHTML = `📄 ${escapeHtml(shortName(file.name))} <span class="muted">(${humanSize(file.size)})</span>
      <button type="button" class="remove-btn" title="Remove">✕</button>`;
    tag.querySelector('.remove-btn').onclick = () => { onClear(); fi.value = ''; showTag(null); refreshInfo(); };
    zone.after(tag);
  };

  fi.addEventListener('change', () => {
    const f = fi.files[0];
    if (!f) return;
    if (!isPdf(f)) { showToast('Only PDF files are allowed.', true); fi.value = ''; return; }
    onPick(f);
    showTag(f);
    refreshInfo();
  });

  const prog = document.createElement('div');
  prog.className = 'row-progress'; prog.id = 'progress-wrap-' + progressId;
  prog.innerHTML = `<div class="bar" id="progress-bar-${progressId}"></div><span class="txt" id="progress-text-${progressId}">0%</span>`;
  wrap.appendChild(prog);

  if (currentFile()) showTag(currentFile());
  return wrap;
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

/* ══════════════════════════════════════════════════════════════════
   TABLE
══════════════════════════════════════════════════════════════════ */
function highlight(text) {
  const safe = escapeHtml(text);
  if (!SEARCH) return safe;
  const q = escapeHtml(SEARCH).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
  return safe.replace(new RegExp(q, 'gi'), m => `<mark>${m}</mark>`);
}

const editState = { record: null, file: null, busy: false };

/** View button: enabled only with Bhu Bharathi VIEW permission (and a saved PDF). */
function viewButton(r) {
  if (!r.has_file) {
    return '<button type="button" class="btn-link" disabled title="No PDF uploaded">👁 View</button>';
  }
  if (!r.can_view || !r.file_url) {
    return '<button type="button" class="btn-link" disabled title="No Bhu Bharathi view permission for this mandal">👁 View</button>';
  }
  // Opens the secure in-app viewer (same tab; its Back button returns here)
  return `<a class="btn-link" target="_blank" href="${r.file_url}">👁 View</a>`;
}

function openEditModal(id) {
  const rec = state.saved.find(r => r.id === id);
  if (!rec || !rec.can_edit) return;
  editState.record = rec; editState.file = null; editState.busy = false;

  document.getElementById('edit-title-id').textContent = '#' + rec.id;
  fillModuleSelect(document.getElementById('edit-module'), rec.module_id,
                   rec.module_id ? { id: rec.module_id, name: rec.module } : null);
  document.getElementById('edit-appno').value  = rec.application_number;
  ['edit-module', 'edit-appno'].forEach(i => document.getElementById(i).classList.remove('invalid'));

  const cell = document.getElementById('edit-upload-cell');
  cell.innerHTML = '';
  if (rec.has_file) {
    const saved = document.createElement('div');
    saved.className = 'saved-file';
    saved.innerHTML = `<span class="saved-file-label">Saved</span> 📄 ${escapeHtml(rec.file_name || 'document.pdf')}
      <span style="margin-left:auto;font-size:9px;color:#888">Replace below ↓</span>`;
    cell.appendChild(saved);
  }
  cell.appendChild(buildUploadWidget({
    progressId: 'edit',
    currentFile: () => editState.file,
    onPick: f => editState.file = f,
    onClear: () => editState.file = null,
    replaceLabel: rec.has_file ? 'Click to replace PDF' : 'Click to upload PDF',
  }));

  document.getElementById('edit-modal').classList.add('show');
  document.getElementById('edit-module').focus();
}

function closeEditModal() {
  if (editState.busy) return;
  document.getElementById('edit-modal').classList.remove('show');
  editState.record = null; editState.file = null;
}

async function saveEdit() {
  const rec = editState.record;
  if (!rec || editState.busy) return;

  const moduleId = document.getElementById('edit-module').value;
  const appNo    = document.getElementById('edit-appno').value.trim();
  if (!moduleId) { markInvalid('edit-module'); showToast('Please select a module.', true); return; }
  if (!appNo)  { markInvalid('edit-appno');  showToast('Application number is required.', true); return; }

  const dup = state.saved.some(s => s.id !== rec.id && String(s.application_number).trim().toLowerCase() === appNo.toLowerCase());
  if (dup) { markInvalid('edit-appno'); showToast(`Application number "${appNo}" already exists.`, true); return; }

  const btn = document.getElementById('edit-save-btn');
  editState.busy = true; btn.disabled = true; btn.textContent = 'Saving…';

  try {
    const payload = { module_id: Number(moduleId), application_number: appNo };

    if (editState.file) {
      const f = editState.file;
      const key = await uploadPdf(rec.mandal_slug, rec.village_slug, f, pct => updateRowProgress('edit', pct));
      updateRowProgress('edit', 100);
      Object.assign(payload, { r2Key: key, fileName: f.name, fileSize: f.size });
    }

    const { res, data } = await postJson(`${ROUTES.update}/${rec.id}`, payload, 'PUT');

    if (res.ok && data.success) {
      const idx = state.saved.findIndex(s => s.id === rec.id);
      if (idx > -1) state.saved[idx] = data.record;
      renderSaved();
      editState.busy = false;
      closeEditModal();
      showToast('✔ ' + (data.message || 'Disposal updated.'));
      return;
    }

    if (data.errors) {
      const list = Array.isArray(data.errors) ? data.errors : Object.values(data.errors).flat();
      if (data.errors.application_number) markInvalid('edit-appno');
      if (data.errors.module_id) markInvalid('edit-module');
      showToast(list.join(' '), true);
    } else {
      showToast(data.message || 'Update failed.', true);
    }
  } catch (err) {
    updateRowProgress('edit', 0, true);
    showToast(err.message || 'Upload failed. Please try again.', true);
  } finally {
    editState.busy = false; btn.disabled = false; btn.textContent = 'Save Changes';
  }
}

function renderSaved() {
  const tbody = document.getElementById('files-tbody');
  if (!state.saved.length) {
    tbody.innerHTML = `<tr class="empty-row"><td colspan="8">${
      SEARCH ? 'No disposals match this file number.' : 'You have not uploaded any Bhu Bharathi disposals yet.'
    }</td></tr>`;
    return;
  }
  tbody.innerHTML = state.saved.map((r, i) => `
    <tr>
      <td>${FIRST_NO + i}</td>
      <td>${escapeHtml(r.mandal_name || '—')}</td>
      <td>${escapeHtml(r.village_name || '—')}</td>
      <td><span class="mod-tag">${escapeHtml(r.module_name || r.module || '—')}</span></td>
      <td><span class="app-no">${highlight(r.application_number)}</span></td>
      <td>${r.has_file
            ? `📄 ${escapeHtml(shortName(r.file_name || 'document.pdf'))}<div class="muted">${escapeHtml(r.file_size_human || '')}</div>`
            : '<span class="muted">—</span>'}</td>
      <td><span class="muted" style="font-size:11px;color:#333">${escapeHtml(r.created_at || '')}</span>
          ${r.updated_at && r.updated_at !== r.created_at ? `<div class="muted">Updated ${escapeHtml(r.updated_at)}</div>` : ''}</td>
      <td>
        <div class="actions">
          ${viewButton(r)}
          ${r.can_edit
            ? `<button type="button" class="btn-link" onclick="openEditModal(${r.id})">✎ Edit</button>`
            : '<button type="button" class="btn-link" disabled title="No Bhu Bharathi edit permission for this mandal">✎ Edit</button>'}
        </div>
      </td>
    </tr>`).join('');
}

renderSaved();
document.getElementById('edit-module').addEventListener('change', e => e.target.classList.remove('invalid'));
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeEditModal(); });
</script>
</body>
</html>