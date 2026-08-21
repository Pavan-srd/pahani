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
    .btn-view-pdf:disabled{background:#ccc;color:#666;cursor:not-allowed;opacity:0.6}
    .btn-view-pdf-disabled{display:inline-flex;align-items:center;gap:6px;background:#ccc;color:#666;border:none;padding:6px 14px;font-size:10px;font-weight:bold;cursor:not-allowed;border-radius:2px;text-transform:uppercase;letter-spacing:0.3px;opacity:0.5;title:'No view permission'}
    
    .btn-edit-doc{display:inline-flex;align-items:center;gap:6px;background:#f39c12;color:white;border:none;padding:6px 14px;font-size:10px;font-weight:bold;cursor:pointer;border-radius:2px;text-transform:uppercase;letter-spacing:0.3px;transition:background 0.15s}
    .btn-edit-doc:hover{background:#e67e22}
    .btn-edit-doc:disabled{background:#ccc;color:#666;cursor:not-allowed;opacity:0.6}
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

    /* ── PERMISSION ALERT ── */
    .permission-alert{background:#fdecea;border:1px solid #c0392b;border-left:4px solid #c0392b;padding:12px 14px;margin-bottom:14px;border-radius:2px;font-size:11px;color:#7f0000;display:flex;align-items:flex-start;gap:8px}
    .permission-alert .icon{font-size:14px}

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
            <label class="field-label">Select Mandal <span class="req">*</span></label>
            <select id="mandal-select" onchange="onMandalChange()">
              <option value="">— Select a Mandal —</option>
              @forelse($mandals as $m)
                <option value="{{ $m->slug }}" data-id="{{ $m->id }}">{{ $m->name }}</option>
              @empty
                <option value="" disabled>No mandals available</option>
              @endforelse
            </select>
            @if($mandals->isEmpty())
              <div class="field-hint" style="color: #c0392b; margin-top: 4px;">
                ⚠️ You don't have view permission for any mandals yet. Please contact your administrator.
              </div>
            @endif
          </div>

          <div class="field-group">
            <label class="field-label">Select Village <span class="req">*</span></label>
            <select id="village-select" onchange="onVillageChange()">
              <option value="">— Select a Village —</option>
            </select>
          </div>
        </div>

        <div id="records-loading" class="loading-bar">
          <div class="spinner"></div>
          <span>Loading records…</span>
        </div>
      </div>
    </div>

    {{-- ── SECTION 2: EXISTING RECORDS ── --}}
    <div class="section-card">
      <div class="section-header">
        <span class="sec-num">2</span>
        Pahani Records
      </div>
      <div class="section-body">
        
        {{-- PERMISSION ALERTS --}}
        @if(!$canView && $mandal)
          <div class="permission-alert">
            <span class="icon">🔒</span>
            <span><strong>No View Permission:</strong> You don't have permission to view records for <strong>{{ $mandal->name }}</strong> mandal. Please contact your administrator to request access.</span>
          </div>
        @endif

        @if($canView && !$canEdit && $mandal)
          <div style="background:#e8f5e9;border:1px solid #a5d6a7;border-left:4px solid #27ae60;padding:12px 14px;margin-bottom:14px;border-radius:2px;font-size:11px;color:#1b5e20;display:flex;align-items:flex-start;gap:8px">
            <span style="font-size:14px">ℹ️</span>
            <span><strong>View Only:</strong> You can view records but don't have edit permission for this mandal.</span>
          </div>
        @endif

        {{-- EXISTING BADGE --}}
        @if($records->count() > 0)
          <div class="existing-badge">
            <span class="dot"></span>
            <span>{{ $records->count() }} Records Found</span>
          </div>
        @endif

        {{-- RECORDS TABLE --}}
        @if($canView && $records->count() > 0)
          <div style="overflow-x:auto">
            <table class="doc-table">
              <thead>
                <tr>
                  <th>Document Type</th>
                  <th>File Name</th>
                  <th>Uploaded Date</th>
                  <th>File Size</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody id="records-tbody">
                @foreach($records as $record)
                  <tr>
                    <td>
                      <strong>{{ $record->pahaniDocument?->label ?? 'N/A' }}</strong>
                      <div class="doc-desc">{{ $record->pahaniDocument?->description }}</div>
                    </td>
                    <td>
                      @if($record->file_name)
                        @php
                          $ext = strtolower(pathinfo($record->file_name, PATHINFO_EXTENSION));
                          $isTiffFile = in_array($ext, ['tif', 'tiff']);
                        @endphp
                        {{ $record->file_name }}
                        <span style="font-size:9px;font-weight:bold;padding:1px 5px;border-radius:2px;margin-left:6px;{{ $isTiffFile ? 'background:#eaf2f8;color:#154360' : 'background:#fdecea;color:#7f0000' }}">
                          {{ $isTiffFile ? 'TIFF' : 'PDF' }}
                        </span>
                      @else
                        <span class="no-file-tag">No file uploaded</span>
                      @endif
                    </td>
                    <td>{{ $record->created_at ? date('d-M-Y', strtotime($record->created_at)) : 'N/A' }}</td>
                    <td>
                      @if($record->file_size)
                        {{ round($record->file_size / 1024, 2) }} KB
                      @else
                        —
                      @endif
                    </td>
                    <td style="display: flex; gap: 6px; align-items: center;">
                      {{-- VIEW BUTTON --}}
                      @if($record->file_name)
                        <a href="{{ route('pahani.view-pdf', $record->id) }}" 
                           target="_blank" 
                           class="btn-view-pdf"
                           title="View PDF document">
                          👁 View
                        </a>
                      @else
                        <button class="btn-view-pdf-disabled" 
                                disabled 
                                title="No file uploaded">
                          👁 View
                        </button>
                      @endif

                      {{-- EDIT BUTTON --}}
                      @if($canEdit)
                        <button class="btn-edit-doc"
                                onclick="openEditPdfModal({{ $record->id }}, '{{ $record->file_name }}', '{{ $record->pahaniDocument?->label }}')"
                                title="Edit this record">
                          ✏️ Edit
                        </button>
                      @else
                        <button class="btn-edit-doc-disabled"
                                disabled
                                title="No edit permission for this mandal">
                          ✏️ Edit
                        </button>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @elseif(!$canView && $mandal)
          <div class="empty-state">
            <div class="es-icon">🔒</div>
            <p><strong>Access Denied</strong></p>
            <p>You don't have permission to view records for this mandal.</p>
          </div>
        @elseif($canView && $records->count() === 0 && $mandal)
          <div class="empty-state">
            <div class="es-icon">📭</div>
            <p><strong>No Records</strong></p>
            <p>No Pahani records found for <strong>{{ $mandal->name }} - {{ $village->name }}</strong></p>
          </div>
        @else
          <div class="empty-state">
            <div class="es-icon">📋</div>
            <p><strong>Select a Mandal &amp; Village</strong></p>
            <p>Please select a mandal and village from the sections above to view records.</p>
          </div>
        @endif

      </div>
    </div>

  </div>
</div>

{{-- ── EDIT PDF MODAL ── --}}
<div id="edit-pdf-modal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6); z-index: 10000; align-items: center; justify-content: center;">
  <div style="background: white; border-radius: 4px; max-width: 500px; width: 90%; padding: 0; box-shadow: 0 8px 24px rgba(0,0,0,0.2);">
    
    {{-- Modal Header --}}
    <div style="background: #154360; color: white; padding: 16px; display: flex; justify-content: space-between; align-items: center; border-radius: 4px 4px 0 0;">
      <h3 style="margin: 0; font-size: 14px;">✏️ Edit Pahani Record</h3>
      <button onclick="closeEditPdfModal()" style="background: none; border: none; color: white; font-size: 20px; cursor: pointer; padding: 0;">✕</button>
    </div>

    {{-- Modal Body --}}
    <div style="padding: 20px;">
      
      {{-- Alert Messages --}}
      <div id="edit-error-message" style="background: #fdecea; border: 1px solid #c0392b; border-left: 4px solid #c0392b; color: #7f0000; padding: 12px; border-radius: 2px; margin-bottom: 14px; display: none; font-size: 11px;"></div>
      <div id="edit-success-message" style="background: #e8f5e9; border: 1px solid #a5d6a7; border-left: 4px solid #27ae60; color: #1b5e20; padding: 12px; border-radius: 2px; margin-bottom: 14px; display: none; font-size: 11px;"></div>

      {{-- Document Info --}}
      <div style="background: #f8fbfd; border: 1px solid #d0dde8; border-radius: 2px; padding: 10px 12px; margin-bottom: 14px; font-size: 11px;">
        <div style="color: #666; margin-bottom: 4px;">📄 Document Type:</div>
        <div style="font-weight: bold; color: #154360;" id="edit-doc-label"></div>
      </div>

      {{-- File Upload --}}
      <div style="margin-bottom: 14px;">
        <label style="display: block; font-size: 11px; font-weight: bold; color: #1a3a5c; margin-bottom: 6px; text-transform: uppercase; letter-spacing: 0.3px;">Upload New PDF <span style="color: #c0392b;">*</span></label>
        <input type="file" 
               id="edit-file-input" 
               accept=".pdf,.tif,.tiff" 
               style="display: block; width: 100%; padding: 8px; border: 1px solid #b0c4d8; border-radius: 2px; font-size: 11px;"
               onchange="onEditFileSelected()">
        <div style="font-size: 10px; color: #666; margin-top: 6px;">PDF or TIFF files allowed (max 50 MB)</div>
      </div>

      {{-- Progress Bar --}}
      <div id="edit-upload-progress" style="display: none; margin-bottom: 14px;">
        <div style="font-size: 10px; font-weight: bold; color: #154360; margin-bottom: 4px;">Uploading...</div>
        <div style="width: 100%; height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden;">
          <div id="edit-progress-bar" style="height: 100%; background: #154360; width: 0%; transition: width 0.3s;"></div>
        </div>
        <div style="font-size: 10px; color: #666; margin-top: 4px;"><span id="edit-progress-text">0</span>%</div>
      </div>

    </div>

    {{-- Modal Footer --}}
    <div style="background: #f8fbfd; padding: 12px 20px; display: flex; gap: 8px; justify-content: flex-end; border-top: 1px solid #d0dde8; border-radius: 0 0 4px 4px;">
      <button onclick="closeEditPdfModal()" style="background: #e8e8e8; color: #333; border: none; padding: 8px 16px; border-radius: 2px; cursor: pointer; font-size: 11px; font-weight: bold;">Cancel</button>
      <button id="edit-submit-btn" onclick="submitEditPdf()" style="background: #f39c12; color: white; border: none; padding: 8px 16px; border-radius: 2px; cursor: pointer; font-size: 11px; font-weight: bold;">🔄 Update PDF</button>
    </div>
  </div>
</div>

<script>
// ============================================================
// GLOBAL STATE
// ============================================================
let currentEditRecord = null;

// Permission flags from backend
const USER_CAN_VIEW = {{ $canView ? 'true' : 'false' }};
const USER_CAN_EDIT = {{ $canEdit ? 'true' : 'false' }};
const VIEW_MANDAL_IDS = {!! json_encode($viewMandalIds) !!};
const EDIT_MANDAL_IDS = {!! json_encode($editMandalIds) !!};

console.log('Permissions loaded:', {
  canView: USER_CAN_VIEW,
  canEdit: USER_CAN_EDIT,
  viewMandalIds: VIEW_MANDAL_IDS,
  editMandalIds: EDIT_MANDAL_IDS
});

// ============================================================
// MANDAL DROPDOWN CHANGE
// ============================================================
async function onMandalChange() {
  const mandalSelect = document.getElementById('mandal-select');
  const mandalSlug = mandalSelect.value;
  const mandalId = mandalSelect.options[mandalSelect.selectedIndex]?.dataset?.id;

  if (!mandalSlug || !mandalId) {
    document.getElementById('village-select').innerHTML = '<option value="">— Select a Village —</option>';
    return;
  }

  // Check if user has view permission for this mandal
  const hasViewPerm = VIEW_MANDAL_IDS && VIEW_MANDAL_IDS.includes(parseInt(mandalId));
  
  if (!hasViewPerm) {
    showToast('❌ You don\'t have permission to view this mandal', true);
    mandalSelect.value = '';
    return;
  }

  // Show loading state
  const villageSelect = document.getElementById('village-select');
  villageSelect.innerHTML = '<option value="">Loading villages…</option>';
  villageSelect.disabled = true;

  // Fetch villages for this mandal
  try {
    const response = await fetch(`/api/mandals/${mandalId}/villages`, {
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    });
    
    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }
    
    const data = await response.json();
    
    // Handle both response formats:
    // Format 1: { data: [...] }
    // Format 2: [...] (direct array)
    let villages = Array.isArray(data) ? data : (data.data || []);
    
    console.log('Villages loaded:', villages);
    
    villageSelect.innerHTML = '<option value="">— Select a Village —</option>';
    villageSelect.disabled = false;
    
    if (villages.length === 0) {
      showToast('⚠️ No villages found for this mandal', true);
      return;
    }
    
    villages.forEach(v => {
      const opt = document.createElement('option');
      opt.value = v.slug;
      opt.dataset.id = v.id;
      opt.textContent = v.name;
      villageSelect.appendChild(opt);
    });
    
    showToast(`✔ ${villages.length} villages loaded`);
    
  } catch (err) {
    console.error('Failed to load villages:', err);
    showToast('❌ Failed to load villages. Please try again.', true);
    villageSelect.innerHTML = '<option value="">— Select a Village —</option>';
    villageSelect.disabled = false;
  }
}

// ============================================================
// VILLAGE DROPDOWN CHANGE
// ============================================================
function onVillageChange() {
  const mandalSel = document.getElementById('mandal-select');
  const villageSel = document.getElementById('village-select');

  if (!mandalSel.value || !villageSel.value) return;

  // Redirect to view with selected mandal and village
  const url = `/pahani/view?mandal=${mandalSel.value}&village=${villageSel.value}`;
  window.location.href = url;
}

// ============================================================
// OPEN EDIT PDF MODAL
// ============================================================
function openEditPdfModal(recordId, fileName, docLabel) {
  if (!USER_CAN_EDIT) {
    showToast('❌ You don\'t have permission to edit this record', true);
    return;
  }

  currentEditRecord = { id: recordId, file_name: fileName, doc_label: docLabel };
  
  document.getElementById('edit-doc-label').textContent = docLabel;
  document.getElementById('edit-file-input').value = '';
  document.getElementById('edit-error-message').style.display = 'none';
  document.getElementById('edit-success-message').style.display = 'none';
  
  const modal = document.getElementById('edit-pdf-modal');
  modal.style.display = 'flex';
}

// ============================================================
// CLOSE EDIT PDF MODAL
// ============================================================
function closeEditPdfModal() {
  document.getElementById('edit-pdf-modal').style.display = 'none';
  currentEditRecord = null;
}

// ============================================================
// FILE SELECTED HANDLER
// ============================================================
function onEditFileSelected() {
  const input = document.getElementById('edit-file-input');
  const file = input.files[0];

  if (!file) return;

  // Validate file type by extension — browsers report TIFF MIME types
  // inconsistently (image/tiff, image/x-tiff, or blank), so file.type alone
  // isn't reliable.
  const ext = (file.name.split('.').pop() || '').toLowerCase();
  if (!['pdf', 'tif', 'tiff'].includes(ext)) {
    showToast('❌ Only PDF or TIFF files are allowed', true);
    input.value = '';
    return;
  }

  // Validate file size (250 MB — matches the large scanned PDFs/TIFFs this system stores)
  const maxSize = 250 * 1024 * 1024;
  if (file.size > maxSize) {
    showToast('❌ File size exceeds 250 MB limit', true);
    input.value = '';
    return;
  }

  showToast('✔ File selected: ' + file.name);
}

// ============================================================
// SUBMIT EDIT PDF
// ============================================================
async function submitEditPdf() {
  const fileInput = document.getElementById('edit-file-input');
  const file = fileInput.files[0];

  if (!file) {
    showToast('❌ Please select a file to upload', true);
    return;
  }

  if (!currentEditRecord) {
    showToast('❌ Record not found', true);
    return;
  }

  const submitBtn = document.getElementById('edit-submit-btn');
  submitBtn.disabled = true;
  submitBtn.textContent = '⏳ Updating…';

  const ext = (file.name.split('.').pop() || '').toLowerCase();
  const fileMime = file.type || (ext === 'pdf' ? 'application/pdf' : 'image/tiff');

  try {
    // Step 1: Get presigned URL from backend
    const presignResponse = await fetch(`/pahani/${currentEditRecord.id}/get-presign-url`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
        'X-Requested-With': 'XMLHttpRequest',
      },
      body: JSON.stringify({
        file_name: file.name,
        file_mime: fileMime,
        file_ext: ext,
        file_size: file.size,
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
        file_mime: fileMime,
        old_file_path: currentEditRecord.file_path,
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

// ============================================================
// UPLOAD TO R2
// ============================================================
async function uploadToR2(presignedUrl, file, headers) {
  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();

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

    if (headers) {
      Object.entries(headers).forEach(([key, value]) => {
        xhr.setRequestHeader(key, value);
      });
    }

    xhr.send(file);
  });
}

// ============================================================
// PROGRESS BAR
// ============================================================
function showProgressBar(show) {
  document.getElementById('edit-upload-progress').style.display = show ? 'block' : 'none';
  if (show) {
    updateProgressBar(0);
  }
}

function updateProgressBar(percent) {
  document.getElementById('edit-progress-bar').style.width = percent + '%';
  document.getElementById('edit-progress-text').textContent = percent;
}

// ============================================================
// MESSAGES
// ============================================================
function showErrorMessage(message) {
  const errorDiv = document.getElementById('edit-error-message');
  errorDiv.textContent = message;
  errorDiv.style.display = 'block';
  document.getElementById('edit-success-message').style.display = 'none';
}

function showSuccessMessage(message) {
  const successDiv = document.getElementById('edit-success-message');
  successDiv.textContent = message;
  successDiv.style.display = 'block';
  document.getElementById('edit-error-message').style.display = 'none';
}

// ============================================================
// TOAST NOTIFICATION
// ============================================================
function showToast(message, isError = false) {
  const toast = document.getElementById('toast');
  toast.textContent = message;
  toast.style.background = isError ? '#c0392b' : '#154360';
  toast.style.display = 'block';
  
  setTimeout(() => {
    toast.style.display = 'none';
  }, 3000);
}

// ============================================================
// LOGOUT
// ============================================================
function confirmLogout() {
  if (confirm('Are you sure you want to logout?')) {
    document.getElementById('logoutForm').submit();
  }
}

// ============================================================
// INITIALIZE
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
  console.log('Page initialized with permissions:', {
    canView: USER_CAN_VIEW,
    canEdit: USER_CAN_EDIT,
  });
});
</script>

</body>
</html>