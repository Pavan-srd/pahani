<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
  <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <div class="portal-wrap">
  <div class="toast" id="toast">✓ Form submitted successfully! Reference No: TSRLA-2025-00412</div>

  <div class="gov-header">
    <div class="gov-top-bar">
      <span><span class="status-dot"></span>Portal Status: Active &nbsp;|&nbsp; Last Updated: 17-Jun-2025</span>
    </div>
    <div class="gov-logo-row">
      <div class="emblem" aria-hidden="true">⚖️</div>
      <div class="gov-title-block">
        <div class="dept-name">Land Record Digitalization</div>
        <div class="dept-sub">Sangareddy &nbsp;|&nbsp; Revenue Department</div>
      </div>
    </div>
    <div class="gov-subtitle-bar">
      PAHANI DIGITIZATION MANAGEMENT SYSTEM — Revenue Divisional Officer (Name, Designation)
    </div>
  </div>

  <div class="page-nav">
    <div class="nav-item active">📂 Upload Documents</div>
  </div>

  <div class="main-body">

    <div class="page-heading">
      <h2>📑 Pahani - Upload Form</h2>
      <!-- <div class="ref">Form No.: TSRLA/PDU/2025 &nbsp;|&nbsp; Ref: G.O. Ms. No. 92 Rev. Dept.</div> -->
    </div>

    <div class="breadcrumb">
      <span>Home</span> › <span>Revenue Records</span> › <span>Pahani Digitization</span> › Upload Form
    </div>

    <!-- SECTION 1: AREA -->
    <div class="section-card">
      <div class="section-header">
        <span class="sec-num">1</span>
        Revenue Mandal & Village Details
      </div>
      <div class="section-body">
        <div class="form-row">
          <div class="field-group">
            <label class="field-label">Mandal <span class="req">*</span></label>
            <div class="field-hint">Select the Mandal jurisdiction</div>
            <select id="mandal-select" onchange="updateVillages()">
              <option value="">— Select Mandal —</option>
              <option value="hayathnagar">Hayathnagar</option>
              <option value="ibrahimpatnam">Ibrahimpatnam</option>
              <option value="keesara">Keesara</option>
              <option value="malkajgiri">Malkajgiri</option>
              <option value="medchal">Medchal</option>
              <option value="rajendranagar">Rajendranagar</option>
              <option value="shamshabad">Shamshabad</option>
              <option value="shankarpally">Shankarpally</option>
              <option value="chevella">Chevella</option>
              <option value="vikarabad">Vikarabad</option>
              <option value="sangareddy">Sangareddy</option>
            </select>
          </div>
          <div class="field-group">
            <label class="field-label">Village / Revenue Village <span class="req">*</span></label>
            <div class="field-hint">Select village within the Mandal</div>
            <select id="village-select" disabled>
              <option value="">— First Select Mandal —</option>
            </select>
          </div>
        </div>
      </div>
    </div>

    <!-- SECTION 2: CORE DOCUMENTS -->
    <div class="section-card">
      <div class="section-header">
        <span class="sec-num">2</span>
        Pahani Records — Core Documents &amp; Year-wise Records
      </div>
      <div class="section-body">
        <table class="doc-table" id="doc-table">
          <thead>
            <tr>
              <th style="width:32%">Document Name</th>
              <th style="width:22%">Physical Document</th>
              <th>Upload PDF</th>
              <th style="width:8%;text-align:center">Action</th>
            </tr>
          </thead>
          <tbody id="doc-tbody"></tbody>
        </table>
        <button class="add-row-btn btn-primary mt-3" id="add-row-btn" onclick="addRow()">
          <i class="ti ti-plus" aria-hidden="true"></i> Add Document Record
        </button>
        <div class="year-count-info" id="row-info">No records added yet. Click "Add Document Record" to begin.</div>
      </div>
    </div>

    <div class="form-footer">
      <div style="display:flex;gap:10px;">
      </div>
      <div style="display:flex;align-items:center;gap:12px;">
        <span style="font-size:10px;color:#666;">Fields marked <span style="color:#c0392b;font-weight:bold;">*</span> are mandatory</span>
        <button class="btn-primary" onclick="submitForm()">✔ Submit &amp; Register</button>
      </div>
    </div>
  </div>
</div>
<script src="{{ asset('js/script.js') }}"></script>

</body>
</html>