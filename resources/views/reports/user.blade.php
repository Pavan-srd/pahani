<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>My Reports — Pahani Upload Summary</title>
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

    /* ── PRINT STYLES ── */
    @media print {
      .page-nav, .logout-btn { display: none; }
      body { background: white; }
      .main-body { max-width: 100%; }
    }

    @media (max-width: 768px) {
      .charts-grid { grid-template-columns: 1fr; }
      .stats-grid { grid-template-columns: repeat(2, 1fr); }
      .chart-wrapper { height: 250px; }
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
      PAHANI DIGITIZATION MANAGEMENT SYSTEM — My Upload Reports ({{ auth()->user()?->name }})
    </div>
  </div>

  {{-- ── NAV ── --}}
  <div class="page-nav">
    <div class="nav-left">
      <a class="nav-item" href="{{ route('pahani.index') }}">📂 Upload Documents</a>
      <a class="nav-item" href="{{ route('pahani.view') }}">📋 View Records</a>
      <a class="nav-item active" href="{{ route('reports.user') }}">📊 My Reports</a>
    </div>
    <div class="nav-right">
      <form id="logoutForm" method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="button" class="logout-btn" onclick="confirmLogout()">🚪 Logout</button>
      </form>
    </div>
  </div>

  <div class="main-body">

    <div class="page-heading">
      <h2>📊 My Upload Summary Report</h2>
      <div style="font-size:10px;color:#666">Generated: {{ date('d-M-Y H:i') }}</div>
    </div>

    <div class="breadcrumb">
      <a href="#">Home</a> › <a href="#">Reports</a> › My Upload Summary
    </div>

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

    <div style="text-align:center;padding:20px;color:#666;font-size:10px;border-top:1px solid #e0e0e0;margin-top:20px">
      <p>📋 This report was generated on {{ date('d-M-Y H:i A') }}</p>
      <p><button onclick="window.print()" style="background:#154360;color:white;border:none;padding:6px 12px;border-radius:2px;cursor:pointer;font-weight:bold">🖨️ Print Report</button></p>
    </div>

  </div>

  <script>
    

    // ══════════════════════════════════════════════════════════════════
    // UTILITY FUNCTIONS
    // ══════════════════════════════════════════════════════════════════
    
    function confirmLogout() {
      if (confirm('Are you sure you want to logout?')) {
        document.getElementById('logoutForm').submit();
      }
    }
  </script>

</body>
</html>