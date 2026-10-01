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

    /* ── LOCKED/DISABLED ROW ── */
    .doc-table tr.row-locked{background:#f0f0f0 !important;opacity:0.8}
    .doc-table tr.row-locked td{color:#888}
    .locked-badge{display:inline-flex;align-items:center;gap:4px;background:#fff3cd;border:1px solid #ffc107;color:#856404;padding:4px 8px;border-radius:2px;font-size:9px;margin-top:4px}
    .btn-disabled{opacity:0.4;cursor:not-allowed !important;pointer-events:none}

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

    /* ── SUBMIT OVERLAY ── */
    #submit-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:8000;align-items:center;justify-content:center}
    #submit-overlay.show{display:flex}
    .submit-box{background:white;border-left:4px solid #f39c12;padding:24px 32px;border-radius:2px;text-align:center;font-size:12px;color:#154360}
    .submit-box .big-spinner{width:32px;height:32px;border:3px solid #d0dde8;border-top-color:#154360;border-radius:50%;animation:spin 0.8s linear infinite;margin:0 auto 12px}

    /* ── UPLOAD PROGRESS BAR (TOP OF PAGE) ── */
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
    @media (max-width:600px){
      .upload-progress-rows{
        grid-template-columns:1fr;
      }
    }
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

    .upload-progress-wrap {
      position: relative;
      height: 16px;
      background: #eee;
      border-radius: 4px;
      overflow: hidden;
      margin-top: 6px;
    }
    .upload-progress-bar {
      height: 100%;
      background: #2ecc71;
      width: 0%;
      transition: width .15s ease;
    }
    .upload-progress-text {
      position: absolute;
      top: 0; left: 6px;
      font-size: 9px;
      line-height: 16px;
      color: #333;
    }

    @media(max-width:600px){.form-row{grid-template-columns:1fr}.gov-title-block .dept-name{font-size:14px}}
  </style>
</head>
<body>
<div class="portal-wrap">

  <div class="toast" id="toast"></div>

  <!-- UPLOAD PROGRESS BAR (TOP OF PAGE) -->
  <div id="upload-progress-container">
    <div class="upload-progress-content">
      <div class="upload-progress-header">
        <div class="upload-progress-spinner"></div>
        <div class="upload-progress-text">
          Uploading Files... <span class="percent"><span id="overall-progress-pct">0</span>%</span>
        </div>
      </div>
      <div class="upload-progress-main-bar">
        <div class="upload-progress-main-fill" id="overall-progress-bar" style="width:0%"></div>
      </div>
      <div class="upload-progress-rows" id="upload-progress-rows-container"></div>
    </div>
  </div>

  <div id="submit-overlay">
    <div class="submit-box">
      <div class="big-spinner"></div>
      Saving records to database & uploading files…<br>
      <span style="font-size:10px;color:#888;margin-top:4px;display:block">Please do not close this tab</span>
    </div>
  </div>
  @php
    $currentUserId = auth()->id();
    $userMandalIds = auth()->user()?->documentPermission()?->pluck('upload_mandal_ids')
      ->flatMap(function ($ids) {
          return is_array($ids) ? $ids : [];
      })
      ->unique()
      ->toArray() ?? [];
  @endphp

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
          <a class="nav-item active" href="{{ route('pahani.index') }}">📂 Upload Documents</a>
          <a class="nav-item" href="{{ route('pahani.view') }}">📋 View Records</a>
          <a class="nav-item" href="#">🔍 Search</a>
          <a class="nav-item" href="{{ route('reports.user') }}">📊 Reports</a>
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
      <h2>📑 Pahani — Upload Form</h2>
      <div style="font-size:10px;color:#666">Select Mandal &amp; Village, then add document records below</div>
    </div>

    <div class="breadcrumb">
      <a href="#">Home</a> › <a href="#">Revenue Records</a> › <a href="#">Pahani Digitization</a> › Upload Form
    </div>

    {{-- ── SUCCESS / ERROR MESSAGES ── --}}
    @if(session('success'))
      <div class="alert alert-success">✔ {{ session('success') }}</div>
    @endif
    @if(session('submit'))
      <div class="alert alert-error"> {{ session('submit') }}</div>
    @endif
    @if($errors->any())
      <div class="alert alert-error">
        <strong>Please fix the following errors:</strong>
        <ul style="margin-top:5px;padding-left:16px">
          @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="notice-bar">
      ℹ️&nbsp;<span>Select Mandal &amp; Village to automatically load any existing records. You can then add new documents or re-upload files for existing ones. Submitting will <strong>update</strong> existing records and <strong>add</strong> new ones.</span>
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
                {{-- NEW: Check if user is assigned to this mandal --}}
                @php
                  $isAssigned = in_array($mandal->id, $userMandalIds);
                @endphp
                <option value="{{ $mandal->slug }}"
                  data-id="{{ $mandal->id }}"
                  {{ old('mandal') == $mandal->slug ? 'selected' : '' }}
                  {{ !$isAssigned ? 'disabled' : '' }}>
                  {{ $mandal->name }}
                  @if(!$isAssigned)
                    (Not assigned)
                  @endif
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

        {{-- Loading indicator for existing records --}}
        <div class="loading-bar" id="records-loading">
          <div class="spinner"></div> Loading existing records for this village…
        </div>
      </div>
    </div>

    {{-- ── SECTION 2: DOCUMENTS ── --}}
    <div class="section-card">
      <div class="section-header">
        <span class="sec-num">2</span>
        Pahani Records — Core Documents &amp; Year-wise Records
      </div>
      <div class="section-body">

        <div id="existing-info" style="display:none"></div>

        <table class="doc-table" id="doc-table">
          <thead>
            <tr>
              <th style="width:30%">Document Name</th>
              <th style="width:20%">Physical Document</th>
              <th>Upload PDF</th>
              <th style="width:8%;text-align:center">Action</th>
            </tr>
          </thead>
          <tbody id="doc-tbody"></tbody>
        </table>

        <button class="add-row-btn" id="add-row-btn" onclick="addRow()">
          + Add Document Record
        </button>
        <div class="row-info" id="row-info">No records added yet. Click "Add Document Record" to begin.</div>
      </div>
    </div>

    <div class="form-footer">
      <div style="display:flex;gap:10px">
        <button class="btn-secondary" onclick="resetForm()">🔄 Reset Form</button>
      </div>
      <div style="display:flex;align-items:center;gap:12px">
        <span style="font-size:10px;color:#666">Fields marked <span style="color:#c0392b;font-weight:bold">*</span> are mandatory</span>
        <button class="btn-primary" id="submit-btn" onclick="submitForm()">✔ Submit &amp; Register</button>
      </div>
    </div>

  </div>{{-- /main-body --}}

</div>{{-- /portal-wrap --}}
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  const CURRENT_USER_ID = {{ $currentUserId }};
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

  const PART_SIZE = 10 * 1024 * 1024;       // 10MB per part
  const MULTIPART_THRESHOLD = 20 * 1024 * 1024; // below this, keep your existing single-PUT flow
  const MAX_CONCURRENT_PARTS = 4;

  function csrfToken() {
    return document.querySelector('meta[name=csrf-token]').content;
  }
  
  async function uploadFileMultipart(mandal, village, docValue, file, onProgress) {
    const initRes = await fetch('{{ route("pahani.multipart.init") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
      body: JSON.stringify({ mandal, village, docValue, fileExt: getFileExt(file) }),
    });
    const { key, uploadId } = await initRes.json();

    const totalParts = Math.ceil(file.size / PART_SIZE);
    const progressByPart = new Array(totalParts).fill(0);
    const completedParts = [];

    const uploadPart = async (partNumber) => {
      const start = (partNumber - 1) * PART_SIZE;
      const blob = file.slice(start, Math.min(start + PART_SIZE, file.size));

      const signRes = await fetch('{{ route("pahani.multipart.sign-part") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
        body: JSON.stringify({ key, uploadId, partNumber }),
      });
      const { url } = await signRes.json();

      const etag = await new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest();
        xhr.open('PUT', url);
        xhr.upload.onprogress = (e) => {
          if (!e.lengthComputable) return;
          progressByPart[partNumber - 1] = e.loaded / e.total;
          const overall = progressByPart.reduce((a, b) => a + b, 0) / totalParts * 100;
          onProgress(Math.round(overall));
        };
        xhr.onload = () => xhr.status >= 200 && xhr.status < 300
          ? resolve(xhr.getResponseHeader('ETag'))
          : reject(new Error(`Part ${partNumber} failed (${xhr.status})`));
        xhr.onerror = () => reject(new Error(`Network error on part ${partNumber}`));
        xhr.send(blob);
      });

      completedParts.push({ PartNumber: partNumber, ETag: etag });
    };

    let cursor = 1;
    const worker = async () => { while (cursor <= totalParts) await uploadPart(cursor++); };
    await Promise.all(Array.from({ length: Math.min(MAX_CONCURRENT_PARTS, totalParts) }, worker));

    completedParts.sort((a, b) => a.PartNumber - b.PartNumber);

    await fetch('{{ route("pahani.multipart.complete") }}', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
      body: JSON.stringify({ key, uploadId, parts: completedParts }),
    });

    return key;
  }
/* ══════════════════════════════════════════════════════════════════
   MASTER DATA from DB (passed by controller)
══════════════════════════════════════════════════════════════════ */
const DB_MANDALS   = @json($mandals->map(fn($m) => ['id'=>$m->id,'name'=>$m->name,'slug'=>$m->slug]));
const DB_DOCUMENTS = @json($documents);  // [{id, value, label, type, description}, …]

// Build the ALL_OPTIONS array JS side from DB data
const ALL_OPTIONS = DB_DOCUMENTS.map(d => ({
  id:    d.id,
  value: d.value,
  label: d.label,
  type:  d.type,
  desc:  d.description ?? ''
}));

/* ══════════════════════════════════════════════════════════════════
   STATE
══════════════════════════════════════════════════════════════════ */
const state = {
  rows:       [],     // { id, value, physical, fileName, existingFile, existingFileName, pahaniId }
  usedValues: new Set(),
  files:      {},     // rowId → File object
};

/* ══════════════════════════════════════════════════════════════════
   MANDAL CHANGE — fetch villages via AJAX
══════════════════════════════════════════════════════════════════ */
function onMandalChange() {
  const mandalSel = document.getElementById('mandal-select');
  const villageSel = document.getElementById('village-select');
  const mandalId  = mandalSel.options[mandalSel.selectedIndex]?.dataset?.id;

  villageSel.innerHTML = '<option value="">— Select Village —</option>';
  villageSel.disabled  = true;
  clearRows();
  document.getElementById('existing-info').style.display = 'none';

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
   VILLAGE CHANGE — load existing records for village
══════════════════════════════════════════════════════════════════ */
function onVillageChange() {
  const villageSel = document.getElementById('village-select');
  const villageOpt = villageSel.options[villageSel.selectedIndex];
  const villageId  = villageOpt?.dataset?.id;

  clearRows();
  document.getElementById('existing-info').style.display = 'none';

  if (!villageId) return;

  document.getElementById('records-loading').classList.add('show');

  fetch(`/api/villages/${villageId}/pahanis`, {
    headers: { 'X-Requested-With': 'XMLHttpRequest' }
  })
  .then(r => r.json())
  .then(records => {
    if (records.length > 0) {
      // Pre-populate rows from DB
      records.forEach(rec => {
        const id = 'row_' + Date.now() + '_' + Math.random().toString(36).slice(2,6);
        state.usedValues.add(rec.document_value);
        state.rows.push({
          id,
          value:            rec.document_value,
          physical:         rec.physical_document === 'yes' ? 'Yes' : 'No',
          fileName:         null,          // new upload (empty)
          existingFile:     rec.file_path  ?? null,
          existingFileName: rec.file_name  ?? null,
          pahaniId:         rec.id,
          uploaded_by:      rec.uploaded_by,
        });
      });

      // Show badge
      const infoEl = document.getElementById('existing-info');
      infoEl.innerHTML =
        `<div class="existing-badge"><span class="dot"></span>${records.length} existing record(s) loaded for this village. You can add new documents or replace files below.</div>`;
      infoEl.style.display = 'block';
    }
    renderTable();
  })
  .catch(() => showToast('Failed to load existing records.', true))
  .finally(() => document.getElementById('records-loading').classList.remove('show'));
}

/* ══════════════════════════════════════════════════════════════════
   HELPERS
══════════════════════════════════════════════════════════════════ */
function clearRows() {
  state.rows       = [];
  state.usedValues = new Set();
  state.files      = {};
  renderTable();
}

function getAvailableOptions(excludeValue) {
  return ALL_OPTIONS.filter(o => !state.usedValues.has(o.value) || o.value === excludeValue);
}

function showToast(msg, isError = false) {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = 'toast show' + (isError ? ' error' : '');
  setTimeout(() => t.className = 'toast', 4500);
}

/* ══════════════════════════════════════════════════════════════════
   ADD / REMOVE ROWS
══════════════════════════════════════════════════════════════════ */
function addRow() {
  const available = ALL_OPTIONS.filter(o => !state.usedValues.has(o.value));
  if (available.length === 0) { document.getElementById('add-row-btn').disabled = true; return; }
  const id = 'row_' + Date.now();
  state.rows.push({ id, value:'', physical:null, fileName:null, existingFile:null, existingFileName:null, pahaniId:null });
  renderTable();
}

function removeRow(id) {
  const row = state.rows.find(r => r.id === id);
  if (row?.value) state.usedValues.delete(row.value);
  delete state.files[id];
  state.rows = state.rows.filter(r => r.id !== id);
  renderTable();
  rebuildAllDropdowns();
}

/* ══════════════════════════════════════════════════════════════════
   EVENTS
══════════════════════════════════════════════════════════════════ */
function onDocChange(id, sel) {
  const row = state.rows.find(r => r.id === id);
  if (!row) return;
  if (row.value) state.usedValues.delete(row.value);
  row.value    = sel.value;
  row.physical = null;
  row.fileName = null;
  row.existingFile     = null;
  row.existingFileName = null;
  row.pahaniId = null;
  delete state.files[id];
  if (sel.value) state.usedValues.add(sel.value);
  rebuildAllDropdowns();
  renderTable();
}

function onPhysicalChange(id, val) {
  const row = state.rows.find(r => r.id === id);
  if (!row) return;
  row.physical = val;
  row.fileName = null;
  delete state.files[id];
  renderUploadCell(id);
}

function rebuildAllDropdowns() {
  state.rows.forEach(row => {
    const sel = document.getElementById('docsel-' + row.id);
    if (!sel) return;
    const cur = row.value;
    sel.innerHTML = '<option value="">— Select Document —</option>';
    getAvailableOptions(cur).forEach(o => {
      const op = document.createElement('option');
      op.value = o.value;
      op.textContent = o.label;
      if (o.value === cur) op.selected = true;
      sel.appendChild(op);
    });
  });
}

/* ══════════════════════════════════════════════════════════════════
   FILE TYPE HELPERS
   Browsers report TIFF MIME types inconsistently (image/tiff,
   image/x-tiff, or blank on some Windows setups), so validate by
   filename extension instead of relying on file.type.
══════════════════════════════════════════════════════════════════ */
function getFileExt(file) {
  return (file.name.split('.').pop() || '').toLowerCase();
}
function isAllowedDocFile(file) {
  return ['pdf', 'tif', 'tiff'].includes(getFileExt(file));
}

/* ══════════════════════════════════════════════════════════════════
   RENDER UPLOAD CELL
══════════════════════════════════════════════════════════════════ */
function renderUploadCell(id) {
  const row  = state.rows.find(r => r.id === id);
  const cell = document.getElementById('upload-' + id);
  if (!cell || !row) return;
  cell.innerHTML = '';

  if (row.physical === 'Yes') {
    const wrap = document.createElement('div');

    // Show existing saved file if any
    if (row.existingFileName) {
      const saved = document.createElement('div');
      saved.className = 'saved-file';
      saved.innerHTML = `<span class="saved-file-label">Saved</span> 📄 ${row.existingFileName} <span style="margin-left:auto;font-size:9px;color:#888">Replace below ↓</span>`;
      wrap.appendChild(saved);
    }

    // New upload zone
    const zone = document.createElement('div');
    zone.className = 'upload-zone';
    zone.innerHTML = `<input type="file" accept=".pdf,.tif,.tiff">
      <div class="uz-icon">📄</div>
      <div class="uz-text">${row.existingFileName ? 'Click to replace PDF/TIFF' : 'Click to upload PDF or TIFF'}</div>`;
    const fi = zone.querySelector('input');
    fi.addEventListener('change', () => {
      const f = fi.files[0];
      if (!f) return;
      if (!isAllowedDocFile(f)) { showToast('Only PDF or TIFF files are allowed.', true); fi.value=''; return; }
      row.fileName = f.name;
      state.files[id] = f;
      const old = wrap.querySelector('.uploaded-file');
      if (old) old.remove();
      const tag = document.createElement('div');
      tag.className = 'uploaded-file';
      tag.innerHTML = `📄 ${f.name.length>30?f.name.slice(0,27)+'...':f.name}
        <button class="remove-btn" title="Remove">✕</button>`;
      tag.querySelector('.remove-btn').onclick = () => {
        row.fileName = null;
        delete state.files[id];
        tag.remove();
        fi.value = '';
      };
      wrap.appendChild(tag);
    });
    wrap.appendChild(zone);
    cell.appendChild(wrap);

  } else if (row.physical === 'No') {
    cell.innerHTML = '<div class="no-doc-msg">ℹ️ No physical document available</div>';
  }
}

/* ══════════════════════════════════════════════════════════════════
   RENDER TABLE
══════════════════════════════════════════════════════════════════ */
function renderTable() {
  const tbody = document.getElementById('doc-tbody');
  tbody.innerHTML = '';

  state.rows.forEach(row => {
    const opt = ALL_OPTIONS.find(o => o.value === row.value);
    const tr  = document.createElement('tr');
    
    // Check if this row is LOCKED (has pahaniId and not uploaded by current user)
    const isLocked = row.pahaniId && row.uploaded_by !== CURRENT_USER_ID;
    if (isLocked) {
      tr.className = 'row-locked';
      tr.id = 'row-' + row.id;
    }

    // ── Col 1: Document Name ──────────────────────────────
    const tdName = document.createElement('td');
    
    if (isLocked) {
      // LOCKED: Show as plain text (not editable)
      const nameDiv = document.createElement('div');
      nameDiv.style.cssText = 'font-weight:500;color:#333';
      nameDiv.textContent = opt?.label || row.value;
      tdName.appendChild(nameDiv);
      
      if (opt?.desc) {
        const d = document.createElement('div');
        d.className = 'doc-desc';
        d.textContent = opt.desc;
        tdName.appendChild(d);
      }
      
      const lockBadge = document.createElement('div');
      lockBadge.className = 'locked-badge';
      lockBadge.innerHTML = '🔒 Locked - Document Locked';
      tdName.appendChild(lockBadge);
    } else {
      // EDITABLE: Show as dropdown
      const sel = document.createElement('select');
      sel.id = 'docsel-' + row.id;
      sel.style.cssText = 'font-size:11px;padding:5px 7px;width:100%';
      const ph = document.createElement('option');
      ph.value = ''; ph.textContent = '— Select Document —';
      sel.appendChild(ph);
      getAvailableOptions(row.value).forEach(o => {
        const op = document.createElement('option');
        op.value = o.value; op.textContent = o.label;
        if (o.value === row.value) op.selected = true;
        sel.appendChild(op);
      });
      sel.addEventListener('change', () => onDocChange(row.id, sel));
      tdName.appendChild(sel);
      
      if (opt?.desc) {
        const d = document.createElement('div');
        d.className = 'doc-desc'; d.style.marginTop = '3px';
        d.textContent = opt.desc;
        tdName.appendChild(d);
      }
      
      // Show badge if editing existing (but not locked)
      if (row.pahaniId) {
        const badge = document.createElement('div');
        badge.style.cssText = 'font-size:9px;color:#27ae60;margin-top:3px';
        badge.textContent = '✎ Editing existing record #' + row.pahaniId;
        tdName.appendChild(badge);
      }
    }

    // ── Col 2: Physical Document radio ────────────────────────────
    const tdPhys = document.createElement('td');
    if (row.value) {
      if (isLocked) {
        // LOCKED: Show as plain text (not editable)
        const physDiv = document.createElement('div');
        physDiv.style.cssText = 'font-weight:500;color:#333';
        physDiv.textContent = row.physical;
        tdPhys.appendChild(physDiv);
      } else {
        // EDITABLE: Show as radio buttons
        const rg = document.createElement('div');
        rg.className = 'radio-group';
        ['Yes','No'].forEach(v => {
          const lbl = document.createElement('label');
          lbl.className = 'radio-label';
          const inp = document.createElement('input');
          inp.type = 'radio'; inp.name = 'phys_'+row.id; inp.value = v;
          if (row.physical === v) inp.checked = true;
          inp.addEventListener('change', () => onPhysicalChange(row.id, v));
          lbl.appendChild(inp);
          lbl.appendChild(document.createTextNode(' '+v));
          rg.appendChild(lbl);
        });
        tdPhys.appendChild(rg);
      }
    } else {
      tdPhys.innerHTML = '<span style="color:#aaa;font-size:10px">— Select doc first —</span>';
    }

    // ── Col 3: Upload PDF ─────────────────────────────────────────
    const tdUpload = document.createElement('td');
    tdUpload.id = 'upload-' + row.id;
    if (row.physical === 'Yes') {
      const wrap = document.createElement('div');
      
      // NEW: Check if user can edit this document
      // Allow if: (1) no pahaniId (new row) OR (2) user is the one who uploaded it
      const canEdit = !row.pahaniId || row.uploaded_by === CURRENT_USER_ID;
      
      if (row.existingFileName) {
        const saved = document.createElement('div');
        saved.className = 'saved-file';
        
        // NEW: Show ownership info and conditional replace message
        let replaceText = '';
        if (canEdit) {
          replaceText = ' <span style="margin-left:auto;font-size:9px;color:#888">Replace below ↓</span>';
        } else {
          replaceText = ' <span style="margin-left:auto;font-size:9px;color:#c0392b">⛔ (Document Locked)</span>';
        }
        
        saved.innerHTML = `<span class="saved-file-label">Saved</span> 📄 ${row.existingFileName}${replaceText}`;
        wrap.appendChild(saved);
      }
      
      // NEW: Only show upload zone if user can edit this document
      if (canEdit) {
        const zone = document.createElement('div');
        zone.className = 'upload-zone';
        zone.innerHTML = `<input type="file" accept=".pdf,.tif,.tiff">
          <div class="uz-icon">📄</div>
          <div class="uz-text">${row.existingFileName ? 'Replace PDF/TIFF' : 'Click to upload PDF or TIFF'}</div>`;
        const fi = zone.querySelector('input');
        fi.addEventListener('change', () => {
          const f = fi.files[0];
          if (!f) return;
          if (!isAllowedDocFile(f)) { showToast('Only PDF or TIFF files are allowed.', true); fi.value=''; return; }
          row.fileName = f.name;
          state.files[row.id] = f;
          const old = wrap.querySelector('.uploaded-file');
          if (old) old.remove();
          const tag = document.createElement('div');
          tag.className = 'uploaded-file';
          tag.innerHTML = `📄 ${f.name.length>30?f.name.slice(0,27)+'...':f.name}
            <button class="remove-btn" title="Remove">✕</button>`;
          tag.querySelector('.remove-btn').onclick = () => {
            row.fileName = null;
            delete state.files[row.id];
            tag.remove();
            fi.value = '';
          };
          wrap.appendChild(tag);
        });
        wrap.appendChild(zone);

        // ── Upload progress bar (hidden until upload starts) ──
        // ── NEW: progress bar for this row ──
        const progWrap = document.createElement('div');
        progWrap.className = 'upload-progress-wrap';
        progWrap.id = 'progress-wrap-' + row.id;
        progWrap.style.display = 'none';
        progWrap.innerHTML = `
          <div class="upload-progress-bar" id="progress-bar-${row.id}" style="width:0%"></div>
          <span class="upload-progress-text" id="progress-text-${row.id}">0%</span>
        `;
        wrap.appendChild(progWrap);
        // ── end new ──

        if (row.fileName) {
          const tag = document.createElement('div');
          tag.className = 'uploaded-file';
          tag.innerHTML = `📄 ${row.fileName}`;
          wrap.appendChild(tag);
        }
      } else {
        // NEW: Show message if user cannot edit
        const msg = document.createElement('div');
        msg.className = 'no-doc-msg';
        msg.innerHTML = '🔒 You cannot modify documents here.';
        wrap.appendChild(msg);
      }
      
      tdUpload.appendChild(wrap);
    } else if (row.physical === 'No') {
      tdUpload.innerHTML = '<div class="no-doc-msg">ℹ️ No physical document available</div>';
    } else {
      tdUpload.innerHTML = '<span style="color:#aaa;font-size:10px">—</span>';
    }

    // ── Col 4: Remove button ──────────────────────────────────────
    const tdAction = document.createElement('td');
    tdAction.style.textAlign = 'center';
    const del = document.createElement('button');
    del.className = 'btn-danger-sm';
    del.title = isLocked ? 'Cannot delete locked documents' : 'Remove row';
    del.innerHTML = '✕';
    if (isLocked) {
      del.classList.add('btn-disabled');
      del.disabled = true;
    } else {
      del.onclick = () => removeRow(row.id);
    }
    tdAction.appendChild(del);

    tr.appendChild(tdName);
    tr.appendChild(tdPhys);
    tr.appendChild(tdUpload);
    tr.appendChild(tdAction);
    tbody.appendChild(tr);
  });

  // Update add-button & info text
  const available = ALL_OPTIONS.filter(o => !state.usedValues.has(o.value));
  const btn  = document.getElementById('add-row-btn');
  const info = document.getElementById('row-info');
  btn.disabled = available.length === 0;
  btn.textContent = available.length === 0 ? '✓ All documents added' : '+ Add Document Record';
  info.textContent = state.rows.length === 0
    ? 'No records added yet. Click "Add Document Record" to begin.'
    : `${state.rows.length} record(s) | ${available.length} option(s) remaining`;
}

/* ══════════════════════════════════════════════════════════════════
   RESET
══════════════════════════════════════════════════════════════════ */
function resetForm() {
  if (!confirm('Reset the form? All unsaved data will be lost.')) return;
  document.getElementById('mandal-select').value = '';
  const vs = document.getElementById('village-select');
  vs.innerHTML = '<option value="">— First Select Mandal —</option>';
  vs.disabled  = true;
  clearRows();
  document.getElementById('existing-info').style.display = 'none';
}

/* ══════════════════════════════════════════════════════════════════
   SUBMIT — FormData to controller
══════════════════════════════════════════════════════════════════ */
async function submitForm() {
  const mandalSel  = document.getElementById('mandal-select');
  const villageSel = document.getElementById('village-select');
  const mandal     = mandalSel.value;
  const village    = villageSel.value;

  if (!mandal || !village) { showToast('Please select both Mandal and Village.', true); return; }
  if (state.rows.length === 0) { showToast('Add at least one document record.', true); return; }

  for (const row of state.rows) {
    if (!row.value) { showToast('Please select a document name for all rows.', true); return; }
    if (!row.physical) { showToast(`Please select Physical Document (Yes/No) for "${row.value}".`, true); return; }
    if (row.physical === 'Yes' && !row.fileName && !row.existingFileName) {
      showToast(`Please upload a PDF for "${row.value}".`, true); return;
    }
  }

  // Show upload progress bar (instead of submit-overlay)
  showUploadProgress();
  document.getElementById('submit-btn').disabled = true;

  function updateRowProgress(rowId, pct) {
    const wrap = document.getElementById(`progress-wrap-${rowId}`);
    const bar  = document.getElementById(`progress-bar-${rowId}`);
    const text = document.getElementById(`progress-text-${rowId}`);
    if (!wrap) return;
    wrap.style.display = 'block';
    bar.style.width = pct + '%';
    text.textContent = pct + '%';
    
    // Also update top progress bar
    const fill = document.getElementById(`upload-row-progress-${rowId}`);
    const pct_el = document.getElementById(`upload-row-pct-${rowId}`);
    if (fill) fill.style.width = pct + '%';
    if (pct_el) pct_el.textContent = Math.round(pct);
  }

  function uploadFileWithProgress(url, file, headers, onProgress) {
    return new Promise((resolve, reject) => {
      const xhr = new XMLHttpRequest();
      xhr.open('PUT', url, true);
      Object.entries(headers).forEach(([k, v]) => xhr.setRequestHeader(k, v));

      xhr.upload.onprogress = (e) => {
        if (e.lengthComputable) onProgress(Math.round((e.loaded / e.total) * 100));
      };
      xhr.onload = () => (xhr.status >= 200 && xhr.status < 300)
        ? resolve()
        : reject(new Error('Upload to storage failed (' + xhr.status + ')'));
      xhr.onerror = () => reject(new Error('Network error while uploading file'));
      xhr.send(file);
    });
  }

  async function getPresignedUrl(mandal, village, docValue, file) {
    const ext = getFileExt(file);
    const fallbackMime = ext === 'pdf' ? 'application/pdf' : 'image/tiff';
    const res = await fetch('{{ route("pahani.presign") }}', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
      },
      body: JSON.stringify({ mandal, village, docValue, fileMime: file.type || fallbackMime, fileExt: ext }),
    });
    const data = await res.json();
    if (!res.ok || !data.success) throw new Error(data.message || 'Could not prepare upload.');
    return data; // { key, url, headers }
  }

  try {
    // Step 1 — upload every file straight to R2, in parallel, with per-row progress
    const uploadTasks = state.rows
      .filter(row => state.files[row.id])
      .map(async row => {
        const file = state.files[row.id];

        let key;
        if (file.size > MULTIPART_THRESHOLD) {
          key = await uploadFileMultipart(mandal, village, row.value, file, pct => updateRowProgress(row.id, pct));
        } else {
          const presigned = await getPresignedUrl(mandal, village, row.value, file);
          await uploadFileWithProgress(presigned.url, file, presigned.headers, pct => updateRowProgress(row.id, pct));
          key = presigned.key;
        }

        row.r2Key    = key;
        row.fileSize = file.size;
        row.fileMime = file.type || (getFileExt(file) === 'pdf' ? 'application/pdf' : 'image/tiff');
        updateRowProgress(row.id, 100);
      });

    await Promise.all(uploadTasks);
    updateOverallProgress(100);

    // Step 2 — tiny JSON request, no files attached
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
   UPLOAD PROGRESS BAR FUNCTIONS
══════════════════════════════════════════════════════════════════ */
function showUploadProgress() {
  const container = document.getElementById('upload-progress-container');
  const rowsContainer = document.getElementById('upload-progress-rows-container');
  
  // Create progress row elements for each file being uploaded
  rowsContainer.innerHTML = state.rows
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

/* ══════════════════════════════════════════════════════════════════
   INIT
══════════════════════════════════════════════════════════════════ */
renderTable();
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
</script>

</body>
</html>