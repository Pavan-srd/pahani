<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Admin Reports — Pahani System Summary</title>
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

    /* ── NAV ── */
    .admin-nav { background: #154360; border-bottom: 2px solid #f39c12; display: flex; align-items: center; gap: 0; padding: 0 20px; font-size: 11px; justify-content: space-between; }
    .nav-left { display: flex; align-items: center; gap: 0; }
    .nav-item { color: #aed6f1; padding: 8px 14px; cursor: pointer; border-right: 1px solid rgba(255,255,255,0.1); transition: background 0.15s; text-decoration: none; display: inline-block; }
    .nav-item:hover { background: rgba(255,255,255,0.1); color: white; }
    .nav-item.active { background: #f39c12; color: #1a1a2e; font-weight: bold; }
    .nav-right { margin-left: auto; }

    /* ── LAYOUT ── */
    .main-body { padding: 16px 20px; max-width: 1400px; margin: 0 auto; }
    .page-heading { background: white; border: 1px solid #d5e8f5; border-left: 4px solid #154360; padding: 10px 16px; margin-bottom: 14px; display: flex; align-items: center; justify-content: space-between; }
    .page-heading h2 { font-size: 13px; font-weight: bold; color: #154360; text-transform: uppercase; letter-spacing: 0.5px; }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; background: #27ae60; display: inline-block; margin-right: 4px; }

    /* ── STATS CARDS ── */
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 12px; margin-bottom: 16px; }
    .stat-card { background: white; border: 1px solid #d0dde8; border-radius: 2px; padding: 14px; }
    .stat-card.primary { border-left: 4px solid #154360; }
    .stat-card.success { border-left: 4px solid #27ae60; }
    .stat-card.warning { border-left: 4px solid #f39c12; }
    .stat-label { font-size: 10px; font-weight: bold; color: #666; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 4px; }
    .stat-value { font-size: 28px; font-weight: bold; color: #154360; }
    .stat-subtitle { font-size: 9px; color: #888; margin-top: 4px; }

    /* ── CHARTS CONTAINER ── */
    .charts-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 14px; margin-bottom: 16px; }
    .chart-container { background: white; border: 1px solid #d0dde8; border-radius: 2px; padding: 14px; }
    .chart-title { font-size: 11px; font-weight: bold; color: #154360; text-transform: uppercase; letter-spacing: 0.3px; margin-bottom: 12px; }
    .chart-wrapper { position: relative; height: 350px; }

    /* ── TABLES ── */
    .section-card { background: white; border: 1px solid #d0dde8; margin-bottom: 14px; border-radius: 2px; overflow: hidden; }
    .section-header { background: #154360; color: white; padding: 8px 14px; font-size: 11px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; }
    .section-body { padding: 14px; overflow-x: auto; }
    .table { width: 100%; border-collapse: collapse; font-size: 11px; }
    .table th { background: #eaf2f8; border: 1px solid #c8dce9; padding: 6px 10px; text-align: left; font-size: 10px; font-weight: bold; text-transform: uppercase; color: #154360; letter-spacing: 0.3px; }
    .table td { border: 1px solid #dce8f0; padding: 7px 10px; vertical-align: middle; }
    .table tbody tr:nth-child(even) td { background: #f7fbfd; }
    .table tbody tr:hover td { background: #edf6ff; }

    /* ── BADGES ── */
    .badge { display: inline-block; padding: 2px 8px; border-radius: 12px; font-size: 9px; font-weight: bold; }
    .badge-success { background: #e8f5e9; color: #1b5e20; border: 1px solid #a5d6a7; }
    .badge-warning { background: #fff3cd; color: #856404; border: 1px solid #ffc107; }
    .badge-danger { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    .badge-info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }

    /* ── PROGRESS ── */
    .progress-bar { height: 6px; background: #e0e0e0; border-radius: 3px; overflow: hidden; }
    .progress-fill { height: 100%; background: #154360; }

    /* ── EMPTY STATE ── */
    .empty-state { text-align: center; padding: 30px 10px; color: #888; font-size: 11px; }
    .empty-state .es-icon { font-size: 30px; margin-bottom: 8px; opacity: 0.5; }

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
      <span><span class="status-dot"></span>Admin Dashboard — Pahani System</span>
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
    <div class="nav-left">
      <a class="nav-item" href="{{ route('admin.dashboard') }}">← Back to Dashboard</a>
      <a class="nav-item active" href="{{ route('reports.admin') }}">📊 System Reports</a>
    </div>
  </div>

  <div class="main-body">

    <div class="page-heading">
      <h2>📊 System Upload Summary Report</h2>
      <div style="font-size:10px;color:#666">Generated: {{ date('d-M-Y H:i') }}</div>
    </div>

    {{-- ── SYSTEM STATISTICS ── --}}
    <div class="stats-grid">
      <div class="stat-card success">
        <div class="stat-label">📤 Total System Uploads</div>
        <div class="stat-value">{{ $totalSystemUploads }}</div>
        <div class="stat-subtitle">Documents uploaded</div>
      </div>

      <div class="stat-card primary">
        <div class="stat-label">📁 Total Mandals</div>
        <div class="stat-value">{{ $totalActiveMandals }}</div>
        <div class="stat-subtitle">In system</div>
      </div>

      <div class="stat-card warning">
        <div class="stat-label">🏘️ Total Villages</div>
        <div class="stat-value">{{ $totalActiveVillages }}</div>
        <div class="stat-subtitle">Across mandals</div>
      </div>

      <div class="stat-card primary">
        <div class="stat-label">👥 Active Users</div>
        <div class="stat-value">{{ $totalActiveUsers }}</div>
        <div class="stat-subtitle">With permissions</div>
      </div>

      <div class="stat-card success">
        <div class="stat-label">📄 Document Types</div>
        <div class="stat-value">{{ $totalDocumentTypes }}</div>
        <div class="stat-subtitle">System documents</div>
      </div>

      <div class="stat-card info" style="border-left-color: #3498db;">
        <div class="stat-label">📊 Avg Per User</div>
        <div class="stat-value">{{ $totalActiveUsers > 0 ? round($totalSystemUploads / $totalActiveUsers, 1) : 0 }}</div>
        <div class="stat-subtitle">Uploads per user</div>
      </div>
    </div>

    {{-- ── CHARTS ── --}}
    <div class="charts-grid">
      
    
    </div>

    {{-- ── MANDAL WISE SUMMARY TABLE ── --}}
    <div class="section-card">
      <div class="section-header">
        📁 Mandal-wise Upload Summary (Detailed)
      </div>
      <div class="section-body">
        @if($mandalUploads->count() > 0)
          <div style="overflow-x:auto">
            <table class="table">
              <thead>
                <tr>
                  <th>Mandal</th>
                  <th>Total Villages</th>
                  <th>Villages Uploaded</th>
                  <th>Villages Pending</th>
                  <th>Total Documents</th>
                  <th>Documents Uploaded</th>
                  <th>Documents Pending</th>
                  <th>Completion %</th>
                  <th>% of System</th>
                </tr>
              </thead>
              <tbody>
                @foreach($mandalUploads as $mandal)
                  <tr>
                    <td><strong>{{ $mandal['mandal'] }}</strong></td>
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
                        <strong>{{ $mandal['total_villages'] - $mandal['uploaded_villages'] }}</strong>
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
                        <strong>{{ $mandal['total_documents'] - $mandal['uploaded_documents'] }}</strong> 
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
                      <div style="display:flex;align-items:center;gap:6px;min-width:80px">
                        <div class="progress-bar" style="flex:1">
                          <div class="progress-fill" style="width:{{ $mandal['percentage_of_system'] }}%"></div>
                        </div>
                        <strong style="min-width:50px;font-size:10px">{{ $mandal['percentage_of_system'] }}%</strong>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        @else
          <div class="empty-state">
            <div class="es-icon">📭</div>
            <p>No uploads found</p>
          </div>
        @endif
      </div>
    </div>

        {{-- ── USER WISE SUMMARY TABLE ── --}}
    <div class="section-card">
      <div class="section-header">
        👥 User-wise Upload Summary
      </div>
      <div class="section-body">
        @if(count($userSummaries) > 0)
          <table class="table">
            <thead>
              <tr>
                <th>User Name</th>
                <th>Email</th>
                <th>Assigned Mandals</th>
                <th>Uploaded Mandals</th>
                <th>Assigned Villages</th>
                <th>Uploaded Villages</th>
                <th>Total Uploads</th>
                <th>Completion %</th>
              </tr>
            </thead>
            <tbody>
              @foreach($userSummaries as $user)
                <tr>
                  <td><strong>{{ $user['name'] }}</strong></td>
                  <td><small>{{ $user['email'] }}</small></td>
                  <td>{{ $user['assigned_mandals'] }}</td>
                  <td>
                    <span class="badge badge-info">
                      {{ $user['uploaded_mandals'] }} / {{ $user['assigned_mandals'] }}
                    </span>
                  </td>
                  <td>{{ $user['assigned_villages'] }}</td>
                  <td>
                    <span class="badge badge-success">
                      {{ $user['uploaded_villages'] }} / {{ $user['assigned_villages'] }}
                    </span>
                  </td>
                  <td><strong>{{ $user['total_uploads'] }}</strong></td>
                  <td>
                    <div style="display:flex;align-items:center;gap:6px">
                      <div class="progress-bar" style="flex:1">
                        <div class="progress-fill" style="width:{{ $user['completion_percentage'] }}%"></div>
                      </div>
                      <strong style="min-width:40px;text-align:right">{{ $user['completion_percentage'] }}%</strong>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        @else
          <div class="empty-state">
            <div class="es-icon">👥</div>
            <p>No users with permissions found</p>
          </div>
        @endif
      </div>
    </div>

    <div style="text-align:center;padding:20px;color:#666;font-size:10px;border-top:1px solid #e0e0e0;margin-top:20px">
      <p>📋 This report was generated on {{ date('d-M-Y H:i A') }}</p>
      <p><button onclick="window.print()" style="background:#154360;color:white;border:none;padding:6px 12px;border-radius:2px;cursor:pointer;font-weight:bold">🖨️ Print Report</button></p>
    </div>

  </div>

</body>
</html>