<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>My Reports — Pahani &amp; Bhu Bharathi Upload Summary</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 12px; background: #f0f4f8; color: #1a1a2e; }

    /* ── HEADER ── */
    .gov-header { background: linear-gradient(135deg, #154360 0%, #1a5276 50%, #1e618f 100%); color: white; border-bottom: 4px solid #f39c12; }
    .gov-top-bar { background: #0d2d47; display: flex; align-items: center; justify-content: space-between; padding: 6px 20px; font-size: 10px; color: #b8cdd9; }
    .gov-logo-row { display: flex; align-items: center; gap: 16px; padding: 12px 20px 10px; }
    .emblem { width: 56px; height: 56px; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 28px; border: 2px solid #f39c12; flex-shrink: 0; }
    .gov-title-block { flex: 1; }
    .gov-title-block .dept-name { font-size: 18px; font-weight: bold; color: white; line-height: 1.2; text-transform: uppercase; letter-spacing: 1px; }
    .gov-title-block .dept-sub { font-size: 11px; color: #aed6f1; margin-top: 2px; }
    .gov-subtitle-bar { background: #1a6fa8; padding: 7px 20px; font-size: 11px; color: #d6eaf8; border-top: 1px solid rgba(255,255,255,0.15); text-align: center; letter-spacing: 0.3px; }

    /* ── NAV ── */
    .page-nav { background: #154360; border-bottom: 2px solid #f39c12; display: flex; align-items: center; gap: 0; padding: 0 20px; font-size: 11px; justify-content: space-between; }
    .nav-left { display: flex; align-items: center; gap: 0; }
    .nav-item { color: #aed6f1; padding: 8px 14px; cursor: pointer; border-right: 1px solid rgba(255,255,255,0.1); transition: background 0.15s; text-decoration: none; display: inline-block; }
    .nav-item:hover { background: rgba(255,255,255,0.1); color: white; }
    .nav-item.active { background: #f39c12; color: #1a1a2e; font-weight: bold; }
    .nav-right { margin-left: auto; }
    .logout-btn { background: #dc3545; color: #fff; border: none; padding: 8px 16px; border-radius: 5px; cursor: pointer; font-weight: 600; transition: .2s; }
    .logout-btn:hover { background: #bb2d3b; }

    /* ── LAYOUT ── */
    .main-body { padding: 16px 20px; max-width: 1200px; margin: 0 auto; }
    .page-heading { background: white; border: 1px solid #d5e8f5; border-left: 4px solid #154360; padding: 10px 16px; margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between; }
    .page-heading h2 { font-size: 13px; font-weight: bold; color: #154360; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; background: #27ae60; display: inline-block; margin-right: 4px; }
    .breadcrumb { font-size: 10px; color: #666; margin-bottom: 10px; display: flex; align-items: center; gap: 4px; }
    .breadcrumb a { color: #154360; text-decoration: none; }

    /* ── STATS CARDS ── */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 12px; margin-bottom: 16px; }
    .stat-card { background: white; border: 1px solid #d0dde8; border-radius: 2px; padding: 14px; }
    .stat-card.primary { border-left: 4px solid #154360; }
    .stat-card.success { border-left: 4px solid #27ae60; }
    .stat-card.warning { border-left: 4px solid #f39c12; }
    .stat-card.danger { border-left: 4px solid #c0392b; }
    .stat-label { font-size: 10px; font-weight: bold; color: #666; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 4px; }
    .stat-value { font-size: 24px; font-weight: bold; color: #154360; }
    .stat-subtitle { font-size: 9px; color: #888; margin-top: 4px; }
    .progress-bar { height: 4px; background: #e0e0e0; border-radius: 2px; margin-top: 8px; overflow: hidden; }
    .progress-fill { height: 100%; background: #154360; }

    /* ── CHARTS CONTAINER ── */
    .charts-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 14px; margin-bottom: 16px; }
    .chart-container { background: white; border: 1px solid #d0dde8; border-radius: 2px; padding: 14px; }
    .chart-title { font-size: 11px; font-weight: bold; color: #154360; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 12px; }
    .chart-wrapper { position: relative; height: 300px; }

    /* ── TABLES ── */
    .section-card { background: white; border: 1px solid #d0dde8; margin-bottom: 14px; border-radius: 2px; overflow: hidden; }
    .section-header { background: #154360; color: white; padding: 8px 14px; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
    .section-body { padding: 14px; }
    .table { width: 100%; border-collapse: collapse; font-size: 11px; }
    .table th { background: #eaf2f8; border: 1px solid #c8dce9; padding: 6px 10px; text-align: left; font-size: 10px; font-weight: bold; text-transform: uppercase; color: #154360; letter-spacing: 0.3px; }
    .table td { border: 1px solid #dce8f0; padding: 7px 10px; vertical-align: middle; }
    .table tbody tr:nth-child(even) td { background: #f7fbfd; }
    .table tbody tr:hover td { background: #edf6ff; }

    /* ── BADGES & PILLS ── */
    .badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 9px; font-weight: bold; }
    .badge-success { background: #e8f5e9; color: #1b5e20; border: 1px solid #a5d6a7; }
    .badge-warning { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
    .badge-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }

    /* ── EMPTY STATE ── */
    .empty-state { text-align: center; padding: 30px 10px; color: #888; font-size: 11px; }
    .empty-state .es-icon { font-size: 30px; margin-bottom: 8px; opacity: 0.5; }

    /* ── REPORT TABS ── */
    .report-tabs { display: flex; gap: 0; margin-bottom: 14px; border-bottom: 2px solid #154360; }
    .report-tab { background: #eaf2f8; color: #154360; border: 1px solid #c8dce9; border-bottom: none; padding: 9px 18px; font-size: 11px; font-weight: bold; cursor: pointer; text-transform: uppercase; letter-spacing: 0.4px; margin-right: 4px; border-radius: 3px 3px 0 0; }
    .report-tab:hover { background: #d6eaf8; }
    .report-tab.active { background: #154360; color: white; border-color: #154360; }
    .report-tab.bb.active { background: #1e6b52; border-color: #1e6b52; }
    .report-tab .count { display: inline-block; background: rgba(255,255,255,0.25); border-radius: 10px; padding: 0 7px; margin-left: 6px; font-size: 10px; }
    .report-tab:not(.active) .count { background: #c8dce9; }
    .report-panel { display: none; }
    .report-panel.active { display: block; }
    .print-title { display: none; font-size: 13px; font-weight: bold; color: #154360; margin: 16px 0 10px; text-transform: uppercase; }

    .bb .section-header { background: #1e6b52; }
    .bb .table th { background: #e8f5e9; color: #145a45; border-color: #cfe8d8; }
    .stat-card.bbc { border-left: 4px solid #1e6b52; }
    .stat-card.bbc .stat-value { color: #1e6b52; }
    .muted { color: #888; font-size: 10px; }
    .mod-tag { display: inline-block; background: #e8f5e9; color: #1b5e20; border: 1px solid #a5d6a7; border-radius: 10px; padding: 2px 8px; font-size: 10px; font-weight: bold; }
    .app-no { font-family: Consolas, monospace; font-weight: bold; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .grid-2 .section-card { margin-bottom: 0; }
    .link-more { font-size: 10px; color: #1e6b52; font-weight: bold; text-decoration: none; float: right; text-transform: none; letter-spacing: 0; color: #cfe8d8; }
    .link-more:hover { text-decoration: underline; }

    /* ── PRINT STYLES ── */
    @media print {
      .page-nav, .logout-btn, .report-tabs { display: none; }
      .report-panel { display: block !important; }
      .print-title { display: block; }
      body { background: white; }
      .main-body { max-width: 100%; }
    }

    @media (max-width: 768px) {
      .charts-grid { grid-template-columns: 1fr; }
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
      .chart-wrapper { height: 250px; }
      .grid-2 { grid-template-columns: 1fr; }
    }
  </style>
</head>
<body>

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
      PAHANI &amp; BHU BHARATHI — My Upload Reports ({{ auth()->user()?->name }})
    </div>
  </div>

  {{-- ── NAV ── --}}
  @include('partials.user-nav', ['active' => 'reports'])

  <div class="main-body">

    <div class="page-heading">
      <h2>📊 My Upload Summary Report</h2>
      <div style="font-size:10px;color:#666">Generated: {{ date('d-M-Y H:i') }}</div>
    </div>

    <div class="breadcrumb">
      <a href="{{ route('home') }}">Home</a> › <a href="#">Reports</a> › My Upload Summary
    </div>

    {{-- ── TABS ── --}}
    <div class="report-tabs" role="tablist">
      <button type="button" class="report-tab" data-tab="pahani" role="tab">📑 Pahani <span class="count">{{ $totalDocumentsUploaded }}</span></button>
      <button type="button" class="report-tab bb" data-tab="bhu-bharathi" role="tab">🗂️ Bhu Bharathi <span class="count">{{ $bb['stats']['total_uploads'] }}</span></button>
    </div>

    {{-- ═══════════════════════ PAHANI REPORT ═══════════════════════ --}}
    <div class="report-panel" id="panel-pahani" role="tabpanel">
    <div class="print-title">📑 Pahani Upload Report</div>

    {{-- ── STATISTICS CARDS ── --}}
    <div class="stats-grid">
      <div class="stat-card primary">
        <div class="stat-label">📁 Total Mandals Assigned</div>
        <div class="stat-value">{{ $totalMandalsAssigned }}</div>
        <div class="stat-subtitle">Available for upload</div>
      </div>

      <div class="stat-card success">
        <div class="stat-label">✅ Mandals with Uploads</div>
        <div class="stat-value">{{ $totalMandalsUploaded }}</div>
        <div class="progress-bar"><div class="progress-fill" style="width:{{ $totalMandalsAssigned > 0 ? ($totalMandalsUploaded / $totalMandalsAssigned) * 100 : 0 }}%"></div></div>
      </div>

      <div class="stat-card primary">
        <div class="stat-label">🏘️ Total Villages</div>
        <div class="stat-value">{{ $totalVillagesAssigned }}</div>
        <div class="stat-subtitle">Across {{ $totalMandalsAssigned }} mandals</div>
      </div>

      <div class="stat-card success">
        <div class="stat-label">✓ Villages with Uploads</div>
        <div class="stat-value">{{ $totalVillagesUploaded }}</div>
        <div class="progress-bar"><div class="progress-fill" style="width:{{ $totalVillagesAssigned > 0 ? ($totalVillagesUploaded / $totalVillagesAssigned) * 100 : 0 }}%"></div></div>
      </div>

      <div class="stat-card warning">
        <div class="stat-label">📄 Total Document Types</div>
        <div class="stat-value">{{ $totalDocumentTypes }}</div>
        <div class="stat-subtitle">System documents available</div>
      </div>

      <div class="stat-card success">
        <div class="stat-label">📤 Documents Uploaded</div>
        <div class="stat-value">{{ $totalDocumentsUploaded }}</div>
        <div class="progress-bar"><div class="progress-fill" style="width:{{ $totalDocumentTypes > 0 ? ($totalDocumentsUploaded / ($totalDocumentTypes * $totalVillagesAssigned)) * 100 : 0 }}%"></div></div>
      </div>
    </div>

    {{-- ── MANDAL WISE DETAILS TABLE ── --}}
    <div class="section-card">
      <div class="section-header">
        📁 Mandal-wise Summary (Detailed)
      </div>
      <div class="section-body">
        @if(count($mandalSummary) > 0)
          <div style="overflow-x:auto">
            <table class="table">
              <thead>
                <tr>
                  <th>Mandal</th>
                  <th>Total Villages</th>
                  <th>Villages Uploaded</th>
                  <th>Total Documents</th>
                  <th>Documents Uploaded</th>
                  <th>Pending Documents</th>
                  <th>Completion %</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($mandalSummary as $mandal)
                  <tr>
                    <td><strong>{{ $mandal['name'] }}</strong></td>
                    <td>
                      <div style="font-size:10px">
                        <strong>{{ $mandal['total_villages'] }}</strong>
                      </div>
                    </td>
                    <td>
                      <div style="font-size:10px">
                        <strong>{{ $mandal['uploaded_villages'] }}</strong>
                      </div>
                    </td>
                    <td>
                      <div style="font-size:10px">
                        <strong>{{ $mandal['total_documents'] }}</strong> 
                      </div>
                    </td>
                    <td>
                      <div style="font-size:10px">
                        <strong>{{ $mandal['uploaded_documents'] }}</strong>
                      </div>
                    </td>
                    <td>
                      <div style="font-size:10px">
                        <strong>{{ $mandal['pending_documents'] }}</strong>
                      </div>
                    </td>
                    <td>
                      <div style="display:flex;align-items:center;gap:6px;min-width:100px">
                        <div class="progress-bar" style="flex:1">
                          <div class="progress-fill" style="width:{{ $mandal['completion_percentage'] }}%;background:{{ $mandal['completion_percentage'] >= 75 ? '#27ae60' : ($mandal['completion_percentage'] >= 50 ? '#f39c12' : '#c0392b') }}"></div>
                        </div>
                        <strong style="min-width:40px;font-size:10px">{{ $mandal['completion_percentage'] }}%</strong>
                      </div>
                    </td>
                    <td>
                      @if($mandal['status'] === 'Completed')
                        <span class="badge badge-success">✓ {{ $mandal['status'] }}</span>
                      @elseif($mandal['status'] === 'In Progress')
                        <span class="badge badge-warning">⚠ {{ $mandal['status'] }}</span>
                      @else
                        <span class="badge badge-danger">✘ {{ $mandal['status'] }}</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="empty-state">
            <div class="es-icon">📭</div>
            <p>No mandals assigned yet</p>
          </div>
        @endif
      </div>
    </div>

    {{-- ── VILLAGE WISE DETAILS TABLE ── --}}
    <div class="section-card">
      <div class="section-header">
        🏘️ Village-wise Summary (Top 20)
      </div>
      <div class="section-body">
        @if($villageDetails->count() > 0)
          <table class="table">
            <thead>
              <tr>
                <th>Village Name</th>
                <th>Mandal</th>
                <th>Documents Uploaded</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @foreach($villageDetails->take(20) as $village)
                <tr>
                  <td><strong>{{ $village['name'] }}</strong></td>
                  <td>{{ $village['mandal'] }}</td>
                  <td>{{ $village['upload_count'] }} documents</td>
                  <td>
                    @if($village['upload_count'] > 0)
                      <span class="badge badge-success">✓ Completed</span>
                    @else
                      <span class="badge badge-warning">⏳ Pending</span>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @else
          <div class="empty-state">
            <div class="es-icon">📭</div>
            <p>No villages in assigned mandals</p>
          </div>
        @endif
      </div>
    </div>

    </div>{{-- /panel-pahani --}}

    {{-- ═══════════════════════ BHU BHARATHI REPORT ═══════════════════════ --}}
    <div class="report-panel bb" id="panel-bhu-bharathi" role="tabpanel">
    <div class="print-title">🗂️ Bhu Bharathi Disposals Report</div>

    {{-- ── STATISTICS CARDS ── --}}
    <div class="stats-grid">
      <div class="stat-card bbc">
        <div class="stat-label">📁 Upload Mandals Assigned</div>
        <div class="stat-value">{{ $bb['stats']['mandals_assigned'] }}</div>
        <div class="stat-subtitle">Given by administrator</div>
      </div>
      <div class="stat-card success">
        <div class="stat-label">✅ Mandals with Uploads</div>
        <div class="stat-value">{{ $bb['stats']['mandals_uploaded'] }}</div>
        <div class="stat-subtitle">Mandals where you uploaded disposals</div>
      </div>
      <div class="stat-card bbc">
        <div class="stat-label">🏘️ Villages Covered</div>
        <div class="stat-value">{{ $bb['stats']['villages_uploaded'] }}</div>
        <div class="stat-subtitle">of {{ $bb['stats']['villages_total'] }} villages in your mandals</div>
        <div class="progress-bar"><div class="progress-fill" style="width:{{ $bb['stats']['village_coverage'] }}%;background:#1e6b52"></div></div>
      </div>
      <div class="stat-card bbc">
        <div class="stat-label">🧩 Modules Permitted</div>
        <div class="stat-value">{{ $bb['stats']['modules_permitted'] }}</div>
        <div class="stat-subtitle">{{ $bb['stats']['modules_used'] }} used in your uploads</div>
      </div>
      <div class="stat-card success">
        <div class="stat-label">📤 Disposals Uploaded</div>
        <div class="stat-value">{{ $bb['stats']['total_uploads'] }}</div>
        <div class="stat-subtitle">{{ $bb['stats']['total_size'] }} of PDFs</div>
      </div>
      <div class="stat-card warning">
        <div class="stat-label">🗓️ This Month</div>
        <div class="stat-value">{{ $bb['stats']['this_month'] }}</div>
        <div class="stat-subtitle">Uploaded in {{ date('F Y') }}</div>
      </div>
    </div>

    {{-- ── MANDAL WISE ── --}}
    <div class="section-card">
      <div class="section-header">📁 Mandal-wise Summary</div>
      <div class="section-body">
        @if(count($bb['mandals']) > 0)
          <div style="overflow-x:auto">
            <table class="table">
              <thead>
                <tr>
                  <th>Mandal</th>
                  <th>Total Villages</th>
                  <th>Villages with Disposals</th>
                  <th>Disposals Uploaded</th>
                  <th>Village Coverage</th>
                  <th>Last Upload</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @foreach($bb['mandals'] as $m)
                  <tr>
                    <td>
                      <strong>{{ $m['name'] }}</strong>
                      @unless($m['assigned'])
                        <div class="muted">No longer assigned for upload</div>
                      @endunless
                    </td>
                    <td><strong>{{ $m['total_villages'] }}</strong></td>
                    <td><strong>{{ $m['uploaded_villages'] }}</strong></td>
                    <td><strong>{{ $m['uploads'] }}</strong></td>
                    <td>
                      <div style="display:flex;align-items:center;gap:6px;min-width:100px">
                        <div class="progress-bar" style="flex:1;margin-top:0">
                          <div class="progress-fill" style="width:{{ $m['coverage'] }}%;background:{{ $m['coverage'] >= 75 ? '#27ae60' : ($m['coverage'] >= 50 ? '#f39c12' : '#c0392b') }}"></div>
                        </div>
                        <strong style="min-width:40px;font-size:10px">{{ $m['coverage'] }}%</strong>
                      </div>
                    </td>
                    <td>{{ $m['last_upload'] ?? '—' }}</td>
                    <td>
                      @if($m['status'] === 'All Villages Covered')
                        <span class="badge badge-success">✓ {{ $m['status'] }}</span>
                      @elseif($m['status'] === 'In Progress')
                        <span class="badge badge-warning">⚠ {{ $m['status'] }}</span>
                      @else
                        <span class="badge badge-danger">✘ {{ $m['status'] }}</span>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="empty-state">
            <div class="es-icon">📭</div>
            <p>No Bhu Bharathi mandals assigned yet</p>
          </div>
        @endif
      </div>
    </div>

    <div class="grid-2" style="margin-bottom:14px">
      {{-- ── MODULE WISE ── --}}
      <div class="section-card">
        <div class="section-header">🧩 Module-wise Summary</div>
        <div class="section-body">
          @if(count($bb['modules']) > 0)
            <table class="table">
              <thead>
                <tr>
                  <th>Module</th>
                  <th style="width:90px">Disposals</th>
                  <th style="width:140px">Share</th>
                </tr>
              </thead>
              <tbody>
                @foreach($bb['modules'] as $mod)
                  <tr>
                    <td>
                      <span class="mod-tag">{{ $mod['name'] }}</span>
                      @if(!$mod['permitted'] && $mod['uploads'] > 0)
                        <div class="muted">Not in your current upload list</div>
                      @endif
                    </td>
                    <td><strong>{{ $mod['uploads'] }}</strong></td>
                    <td>
                      <div style="display:flex;align-items:center;gap:6px">
                        <div class="progress-bar" style="flex:1;margin-top:0">
                          <div class="progress-fill" style="width:{{ $mod['share'] }}%;background:#1e6b52"></div>
                        </div>
                        <span style="font-size:10px;min-width:36px">{{ $mod['share'] }}%</span>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @else
            <div class="empty-state">
              <div class="es-icon">🧩</div>
              <p>No modules permitted yet</p>
            </div>
          @endif
        </div>
      </div>

      {{-- ── VILLAGE WISE ── --}}
      <div class="section-card">
        <div class="section-header">🏘️ Village-wise Summary (Top 20)</div>
        <div class="section-body">
          @if(count($bb['villages']) > 0)
            <table class="table">
              <thead>
                <tr>
                  <th>Village</th>
                  <th>Mandal</th>
                  <th style="width:80px">Disposals</th>
                  <th style="width:95px">Last Upload</th>
                </tr>
              </thead>
              <tbody>
                @foreach($bb['villages'] as $v)
                  <tr>
                    <td><strong>{{ $v['name'] }}</strong></td>
                    <td>{{ $v['mandal'] }}</td>
                    <td>{{ $v['uploads'] }}</td>
                    <td>{{ $v['last_upload'] ?? '—' }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          @else
            <div class="empty-state">
              <div class="es-icon">📭</div>
              <p>No disposals uploaded yet</p>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- ── RECENT UPLOADS ── --}}
    <div class="section-card">
      <div class="section-header">
        🕒 Recent Uploads (Last 10)
        <a class="link-more" href="{{ route('bhu-bharathi.my-files') }}">View all disposals →</a>
      </div>
      <div class="section-body">
        @if(count($bb['recent']) > 0)
          <div style="overflow-x:auto">
            <table class="table">
              <thead>
                <tr>
                  <th>File / Application No.</th>
                  <th>Module</th>
                  <th>Mandal</th>
                  <th>Village</th>
                  <th>Uploaded</th>
                </tr>
              </thead>
              <tbody>
                @foreach($bb['recent'] as $r)
                  <tr>
                    <td><span class="app-no">{{ $r['application_number'] }}</span></td>
                    <td><span class="mod-tag">{{ $r['module'] }}</span></td>
                    <td>{{ $r['mandal'] }}</td>
                    <td>{{ $r['village'] }}</td>
                    <td>{{ $r['uploaded_at'] }}</td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="empty-state">
            <div class="es-icon">📭</div>
            <p>No disposals uploaded yet. <a href="{{ route('bhu-bharathi.index') }}" style="color:#1e6b52;font-weight:bold">Upload now →</a></p>
          </div>
        @endif
      </div>
    </div>

    </div>{{-- /panel-bhu-bharathi --}}

    <div style="text-align:center;padding:20px;color:#666;font-size:10px;border-top:1px solid #e0e0e0;margin-top:20px">
      <p>📋 This report was generated on {{ date('d-M-Y H:i A') }}</p>
      <p> <button onclick="window.print()" title="Prints both the Pahani and Bhu Bharathi reports" style="background:#154360;color:white;border:none;padding:6px 12px;border-radius:2px;cursor:pointer;font-weight:bold">🖨️ Print Report</button></p>
    </div>

  </div>

  <script>
    // ══════════════════════════════════════════════════════════════════
    // REPORT TABS  (remembers the tab in the URL: #pahani / #bhu-bharathi)
    // ══════════════════════════════════════════════════════════════════
    (function () {
      const tabs = document.querySelectorAll('.report-tab');

      function show(name) {
        if (!document.getElementById('panel-' + name)) name = 'pahani';
        tabs.forEach(t => {
          const on = t.dataset.tab === name;
          t.classList.toggle('active', on);
          t.setAttribute('aria-selected', on ? 'true' : 'false');
        });
        document.querySelectorAll('.report-panel').forEach(p => p.classList.toggle('active', p.id === 'panel-' + name));
      }

      tabs.forEach(t => t.addEventListener('click', () => {
        show(t.dataset.tab);
        history.replaceState(null, '', '#' + t.dataset.tab);
      }));

      show((location.hash || '#pahani').slice(1));
    })();
  </script>

</body>
</html>