<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Bhu Bharathi Disposals — Admin Dashboard — Land Record Digitalization</title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:Arial,sans-serif;font-size:12px;background:#f0f4f8;color:#1a1a2e}
    a{color:inherit;text-decoration:none}

    /* ── HEADER ── */
    .gov-header{background:linear-gradient(135deg,#154360 0%,#1a5276 50%,#1e618f 100%);color:white;border-bottom:4px solid #f39c12}
    .gov-top-bar{background:#0d2d47;display:flex;align-items:center;justify-content:space-between;padding:6px 20px;font-size:10px;color:#b8cdd9}
    .gov-logo-row{display:flex;align-items:center;gap:16px;padding:12px 20px 10px}
    .emblem{width:48px;height:48px;background:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:24px;border:2px solid #f39c12;flex-shrink:0}
    .gov-title-block{flex:1}
    .gov-title-block .dept-name{font-size:16px;font-weight:bold;color:white;line-height:1.2;text-transform:uppercase;letter-spacing:1px}
    .gov-title-block .dept-sub{font-size:10px;color:#aed6f1;margin-top:2px}
    .status-dot{width:8px;height:8px;border-radius:50%;background:#27ae60;display:inline-block;margin-right:4px}

    /* ── ADMIN NAV ── */
    .admin-nav{background:#154360;border-bottom:2px solid #f39c12;display:flex;align-items:center;gap:0;padding:0 20px;font-size:11px}
    .nav-item{color:#aed6f1;padding:8px 14px;cursor:pointer;border-right:1px solid rgba(255,255,255,0.1);transition:background 0.15s;text-decoration:none;display:inline-block}
    .nav-item:hover{background:rgba(255,255,255,0.1);color:white}
    .nav-item.active{background:#f39c12;color:#1a1a2e;font-weight:bold}

    /* ── LAYOUT ── */
    .main-body{padding:20px;max-width:1200px;margin:0 auto}
    .page-heading{background:white;border:1px solid #d5e8f5;border-left:4px solid #154360;padding:12px 16px;margin-bottom:16px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px}
    .page-heading h2{font-size:13px;font-weight:bold;color:#154360;text-transform:uppercase;letter-spacing:0.5px}

    /* ── ALERTS ── */
    .alert{padding:10px 14px;border-radius:2px;font-size:11px;margin-bottom:14px;border-left:4px solid}
    .alert-success{background:#e8f5e9;border-color:#27ae60;color:#1b5e20}
    .alert-error{background:#fdecea;border-color:#c0392b;color:#7f0000}

    /* ── FILTER SECTION ── */
    .filter-section{background:white;border:1px solid #d0dde8;border-radius:2px;padding:14px;margin-bottom:16px;display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end}
    .filter-group{display:flex;flex-direction:column;gap:4px}
    .filter-group label{font-size:10px;font-weight:bold;color:#1a3a5c;text-transform:uppercase;letter-spacing:0.3px}
    .filter-group input[type="text"],
    .filter-group select{padding:6px 8px;border:1px solid #b0c4d8;border-radius:2px;font-size:11px;color:#1a1a2e;background:#f8fbfd;outline:none}
    .filter-group input[type="text"]:focus,
    .filter-group select:focus{border-color:#154360;background:white;box-shadow:0 0 0 2px rgba(21,67,96,0.1)}

    /* ── BUTTONS ── */
    .btn{border:none;padding:7px 16px;font-size:11px;font-weight:bold;cursor:pointer;border-radius:2px;text-transform:uppercase;letter-spacing:0.3px;transition:all 0.15s}
    .btn-primary{background:#154360;color:white}
    .btn-primary:hover{background:#1a6fa8}
    .btn-secondary{background:#eaf2f8;color:#154360;border:1px solid #c8dce9}
    .btn-secondary:hover{background:#d6eaf8}
    .btn-danger{background:#fdecea;color:#c0392b;border:1px solid #e8a9a0}
    .btn-danger:hover{background:#f8d0cb}
    .btn-icon-small{background:#eaf2f8;color:#154360;border:1px solid #c8dce9;width:26px;height:26px;padding:0;display:inline-flex;align-items:center;justify-content:center;font-size:12px;border-radius:3px}
    .btn-icon-small:hover{background:#d6eaf8}
    .btn-icon-small.danger{background:#fdecea;color:#c0392b;border-color:#e8a9a0}
    .btn-icon-small.danger:hover{background:#f8d0cb}

    /* ── TABLE ── */
    .table-wrap{background:white;border:1px solid #d0dde8;border-radius:2px;overflow:hidden}
    .pdf-table{width:100%;border-collapse:collapse;font-size:11px}
    .pdf-table th{background:#eaf2f8;border:1px solid #c8dce9;padding:8px 10px;text-align:left;font-size:10px;font-weight:bold;text-transform:uppercase;color:#154360;letter-spacing:0.3px;white-space:nowrap}
    .pdf-table td{border:1px solid #dce8f0;padding:8px 10px;vertical-align:middle}
    .pdf-table tbody tr:nth-child(even) td{background:#f7fbfd}
    .pdf-table tbody tr:hover td{background:#edf6ff}
    .pdf-table a{color:#154360;text-decoration:underline;cursor:pointer}
    .pdf-table a:hover{color:#1a6fa8}

    /* ── EMPTY STATE ── */
    .empty-state{text-align:center;padding:40px 20px;color:#888;font-size:11px}
    .empty-state .es-icon{font-size:40px;margin-bottom:10px;opacity:0.5}

    /* ── PAGINATION ── */
    .pagination{display:flex;gap:6px;justify-content:center;padding:20px;font-size:11px;flex-wrap:wrap;align-items:center}
    .pagination > *{display:inline-flex;align-items:center;justify-content:center}
    .pagination a,.pagination span{min-width:36px;height:36px;padding:6px 10px;border:1px solid #b0c4d8;border-radius:3px;background:white;color:#154360;text-decoration:none;font-weight:500;transition:all 0.15s}
    .pagination a:hover:not(.disabled){background:#eaf2f8;border-color:#154360;color:#1a6fa8;box-shadow:0 2px 4px rgba(21,67,96,0.1)}
    .pagination .active{background:#154360;color:white;border-color:#154360;font-weight:bold;box-shadow:0 2px 4px rgba(21,67,96,0.15)}
    .pagination .disabled{opacity:0.5;cursor:not-allowed;background:#f5f5f5;color:#999;border-color:#ddd}
    /* Custom arrow styling */
    .pagination [rel="prev"],.pagination [rel="next"]{min-width:38px;height:38px;font-size:16px;font-weight:bold;padding:8px}
    .pagination [rel="prev"]::before{content:'←';margin-right:4px}
    .pagination [rel="next"]::after{content:'→';margin-left:4px}
    .pagination [rel="prev"]:hover,.pagination [rel="next"]:hover{background:#154360;color:white;border-color:#154360}
    .pagination .disabled[rel="prev"],.pagination .disabled[rel="next"]{background:#f5f5f5;color:#999}

    /* ── TOAST ── */
    .toast{position:fixed;top:20px;right:20px;background:#154360;color:white;padding:10px 18px;border-left:4px solid #f39c12;font-size:12px;border-radius:2px;z-index:9999;display:none;box-shadow:0 4px 12px rgba(0,0,0,0.3)}
    .toast.show{display:block}
    .toast.error{border-left-color:#c0392b;background:#7f0000}

    /* ── MODAL ── */
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(21,67,96,0.45);z-index:400;align-items:center;justify-content:center}
    .modal-overlay.show{display:flex}
    .modal-box{background:white;border-radius:3px;width:100%;max-width:400px;box-shadow:0 12px 32px rgba(0,0,0,0.25);overflow:hidden}
    .modal-header{background:#154360;color:white;padding:12px 18px}
    .modal-header h3{font-size:12px;font-weight:bold;text-transform:uppercase}
    .modal-body{padding:18px;font-size:11px}
    .modal-footer{padding:12px 18px;border-top:1px solid #eef3f7;display:flex;justify-content:flex-end;gap:8px}
    .modal-close{background:none;border:none;color:#d6eaf8;cursor:pointer;font-size:16px;padding:2px 4px}

    /* ── RESPONSIVE ── */
    @media(max-width:768px){
      .filter-section{flex-direction:column}
      .filter-group{min-width:100%}
      .pdf-table{font-size:10px}
      .pdf-table th,.pdf-table td{padding:6px 8px}
    }
  
    /* ── BHU BHARATHI EXTRAS ── */
    .app-no{font-family:Consolas,monospace;font-weight:bold;color:#1a1a2e}
    .mod-tag{display:inline-block;background:#e8f5e9;color:#1b5e20;border:1px solid #a5d6a7;border-radius:10px;padding:2px 8px;font-size:10px;font-weight:bold}
    .btn-icon-small.disabled{opacity:0.45;cursor:not-allowed;pointer-events:none}
    .muted{font-size:10px;color:#888}
  </style>
</head>
<body>

  {{-- ── HEADER ── --}}
  <div class="gov-header">
    <div class="gov-top-bar">
      <span><span class="status-dot"></span>Admin Panel</span>
      <span>Pahani Management System</span>
    </div>
    <div class="gov-logo-row">
      <div class="emblem">⚖️</div>
      <div class="gov-title-block">
        <div class="dept-name">Land Record Digitalization</div>
        <div class="dept-sub">Sangareddy &nbsp;|&nbsp; Revenue Department</div>
      </div>
    </div>
  </div>

  {{-- ── NAV ── --}}
  <div class="admin-nav">
    <a class="nav-item" href="{{ route('admin.dashboard') }}">← Back to Dashboard</a>
    <a class="nav-item" href="{{ route('admin.pahani-management.index') }}">Pahani Files</a>
    <a class="nav-item active" href="{{ route('admin.bhu-bharathi-management.index') }}">Bhu Bharathi Files</a>
  </div>

  {{-- ── MAIN CONTENT ── --}}
  <div class="main-body">

    <div class="page-heading">
      <h2>🗂️ Bhu Bharathi Disposals — Uploaded Files</h2>
      <div style="font-size:10px;color:#666">Total: {{ $records->total() }} PDF(s)</div>
    </div>

    {{-- ── ALERTS ── --}}
    @if(session('success'))
      <div class="alert alert-success">✔ {{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-error">✘ {{ session('error') }}</div>
    @endif

    {{-- ── FILTER SECTION ── --}}
    <div class="filter-section">
      <form method="GET" style="display:flex;flex-wrap:wrap;gap:12px;align-items:flex-end;flex:1">
        <div class="filter-group">
          <label>Search User</label>
          <input type="text" name="search_user" placeholder="User name..." value="{{ request('search_user') }}" style="min-width:140px">
        </div>
        <div class="filter-group">
          <label>Mandal</label>
          <select name="mandal_id" id="mandal_id" style="min-width:140px">
            <option value="">All Mandals</option>
            @foreach($mandals as $mandal)
              <option value="{{ $mandal->id }}" {{ request('mandal_id') == $mandal->id ? 'selected' : '' }}>
                {{ $mandal->name }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="filter-group">
          <label>Village</label>
          <select name="village_id" id="village_id" style="min-width:150px">
            <option value="">All Villages</option>
          </select>
        </div>
        <div class="filter-group">
          <label>Module</label>
          <select name="module_id" id="module_id" style="min-width:150px">
            <option value="">All Modules</option>
            @foreach($modules as $module)
              <option value="{{ $module->id }}" {{ request('module_id') == $module->id ? 'selected' : '' }}>
                {{ $module->name }}{{ $module->is_active ? '' : ' (inactive)' }}
              </option>
            @endforeach
          </select>
        </div>
        <div class="filter-group">
          <label>File / Application No.</label>
          <input type="text" name="application_number" placeholder="Application number..." value="{{ request('application_number') }}" style="min-width:150px">
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
        <a href="{{ route('admin.bhu-bharathi-management.index') }}" class="btn btn-secondary">Reset</a>
      </form>
    </div>

    {{-- ── TABLE ── --}}
    @if($records->count() > 0)
      <div class="table-wrap" style="overflow-x:auto">
        <table class="pdf-table">
          <thead>
            <tr>
              <th style="width:40px">#</th>
              <th style="width:150px">User</th>
              <th style="width:110px">Mandal</th>
              <th style="width:110px">Village</th>
              <th style="width:140px">Module</th>
              <th style="width:130px">File / Application No.</th>
              <th style="width:150px">File Name</th>
              <th style="width:70px">Size</th>
              <th style="width:100px">Uploaded</th>
              <th style="width:60px;text-align:center">View</th>
            </tr>
          </thead>
          <tbody>
            @foreach($records as $record)
              <tr>
                <td>{{ $records->firstItem() + $loop->index }}</td>
                <td>
                  <strong>{{ $record->uploader?->name ?? '—' }}</strong><br>
                  <span class="muted">{{ $record->uploader?->email ?? '—' }}</span>
                </td>
                <td>{{ $record->mandal?->name ?? '—' }}</td>
                <td>{{ $record->village?->name ?? '—' }}</td>
                <td><span class="mod-tag">{{ $record->moduleMaster?->name ?? $record->module ?? '—' }}</span></td>
                <td><span class="app-no">{{ $record->application_number }}</span></td>
                <td>
                  @if($record->file_path)
                    <a href="{{ route('admin.bhu-bharathi-management.file', $record->id) }}" target="_blank" rel="noopener" title="{{ $record->file_name }}">
                      📄 {{ Str::limit($record->file_name ?? 'document.pdf', 20, '...') }}
                    </a>
                  @else
                    <span class="muted">No file</span>
                  @endif
                </td>
                <td>{{ $record->file_size_human ?? '—' }}</td>
                <td style="font-size:10px">
                  {{ $record->created_at?->format('M d, Y') ?? '—' }}<br>
                  <span class="muted">{{ $record->created_at?->format('h:i A') }}</span>
                </td>
                <td style="text-align:center">
                  @if($record->file_path)
                    <a href="{{ route('admin.bhu-bharathi-management.file', $record->id) }}" target="_blank" rel="noopener" class="btn-icon-small" title="View PDF" style="text-decoration:none">👁</a>
                  @else
                    <span class="btn-icon-small disabled" title="No PDF uploaded">👁</span>
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      {{-- ── PAGINATION ── --}}
      @if($records->hasPages())
      <div style="margin-top:16px">
        <div class="pagination">
          {{-- Previous Page Link --}}
          @if($records->onFirstPage())
            <span class="disabled" aria-disabled="true" rel="prev">← Previous</span>
          @else
            <a href="{{ $records->previousPageUrl() }}" rel="prev">← Previous</a>
          @endif

          {{-- Pagination Elements (windowed: 1 ... current-2..current+2 ... last) --}}
          @php
            $current = $records->currentPage();
            $last = $records->lastPage();
            $onEachSide = 2;

            $pages = [1];
            $rangeStart = max(2, $current - $onEachSide);
            $rangeEnd = min($last - 1, $current + $onEachSide);

            if ($rangeStart > 2) {
                $pages[] = '...';
            }
            for ($p = $rangeStart; $p <= $rangeEnd; $p++) {
                $pages[] = $p;
            }
            if ($rangeEnd < $last - 1) {
                $pages[] = '...';
            }
            if ($last > 1) {
                $pages[] = $last;
            }
          @endphp

          @foreach($pages as $index => $page)
            @if($page === '...')
              <span class="disabled" aria-hidden="true">…</span>
            @elseif($page == $current)
              <span class="active" aria-current="page">{{ $page }}</span>
            @else
              <a href="{{ $records->url($page) }}">{{ $page }}</a>
            @endif
          @endforeach

          {{-- Next Page Link --}}
          @if($records->hasMorePages())
            <a href="{{ $records->nextPageUrl() }}" rel="next">Next →</a>
          @else
            <span class="disabled" aria-disabled="true" rel="next">Next →</span>
          @endif
        </div>
      </div>
      @endif

    @else
      <div class="empty-state">
        <div class="es-icon">🗂️</div>
        <div>No Bhu Bharathi disposals found.</div>
      </div>
    @endif

  </div>

  <script>
    // ── CASCADING FILTER: Mandal -> Village ──
    const villagesData = @json($villages->map(fn($v) => ['id' => $v->id, 'name' => $v->name, 'mandal_id' => $v->mandal_id]));

    const selectedMandalId  = @json((string) request('mandal_id', ''));
    const selectedVillageId = @json((string) request('village_id', ''));

    const mandalSelect  = document.getElementById('mandal_id');
    const villageSelect = document.getElementById('village_id');

    function populateVillages(mandalId, keepSelectedId) {
      villageSelect.innerHTML = '<option value="">All Villages</option>';
      const filtered = mandalId
        ? villagesData.filter(v => String(v.mandal_id) === String(mandalId))
        : villagesData;
      filtered.forEach(v => {
        const opt = document.createElement('option');
        opt.value = v.id;
        opt.textContent = v.name;
        if (keepSelectedId && String(v.id) === String(keepSelectedId)) opt.selected = true;
        villageSelect.appendChild(opt);
      });
    }

    // Initial population, keeping selection from the query string
    populateVillages(selectedMandalId, selectedVillageId);

    // Mandal changed -> show only that mandal's villages
    mandalSelect.addEventListener('change', function () {
      populateVillages(this.value, null);
    });
  </script>

</body>
</html>