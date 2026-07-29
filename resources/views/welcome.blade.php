<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'Arial', sans-serif; }
  .portal-wrap {
    font-family: Arial, sans-serif;
    font-size: 12px;
    background: #f0f4f8;
    min-height: 100vh;
    color: #1a1a2e;
  }
  .gov-header {
    background: linear-gradient(135deg, #154360 0%, #1a5276 50%, #1e618f 100%);
    color: white;
    padding: 0;
    border-bottom: 4px solid #f39c12;
  }
  .gov-top-bar {
    background: #0d2d47;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 6px 20px;
    font-size: 10px;
    color: #b8cdd9;
  }
  .gov-top-bar span { display: flex; align-items: center; gap: 6px; }
  .gov-logo-row {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 12px 20px 10px;
  }
  .emblem {
    width: 56px;
    height: 56px;
    background: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    border: 2px solid #f39c12;
  }
  .gov-title-block { flex: 1; }
  .gov-title-block .dept-hi {
    font-size: 13px;
    color: #aed6f1;
    font-weight: normal;
    letter-spacing: 0.5px;
  }
  .gov-title-block .dept-name {
    font-size: 18px;
    font-weight: bold;
    color: white;
    line-height: 1.2;
    text-transform: uppercase;
    letter-spacing: 1px;
  }
  .gov-title-block .dept-sub {
    font-size: 11px;
    color: #aed6f1;
    margin-top: 2px;
  }
  .gov-subtitle-bar {
    background: #1a6fa8;
    padding: 7px 20px;
    font-size: 11px;
    color: #d6eaf8;
    border-top: 1px solid rgba(255,255,255,0.15);
    text-align: center;
    letter-spacing: 0.3px;
  }
  .page-nav {
    background: #154360;
    border-bottom: 2px solid #f39c12;
    display: flex;
    align-items: center;
    gap: 0;
    padding: 0 20px;
    font-size: 11px;
  }
  .nav-item {
    color: #aed6f1;
    padding: 8px 14px;
    cursor: pointer;
    border-right: 1px solid rgba(255,255,255,0.1);
    transition: background 0.15s;
  }
  .nav-item:hover { background: rgba(255,255,255,0.1); color: white; }
  .nav-item.active { background: #f39c12; color: #1a1a2e; font-weight: bold; }
  .main-body { padding: 16px 20px; max-width: 900px; margin: 0 auto; }
  .page-heading {
    background: white;
    border: 1px solid #d5e8f5;
    border-left: 4px solid #154360;
    padding: 10px 16px;
    margin-bottom: 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .page-heading h2 {
    font-size: 13px;
    font-weight: bold;
    color: #154360;
    text-transform: uppercase;
    letter-spacing: 0.5px;
  }
  .page-heading .ref {
    font-size: 10px;
    color: #666;
  }
  .breadcrumb {
    font-size: 10px;
    color: #666;
    margin-bottom: 10px;
    display: flex;
    align-items: center;
    gap: 4px;
  }
  .breadcrumb span { color: #154360; cursor: pointer; }
  .breadcrumb span:hover { text-decoration: underline; }
  .section-card {
    background: white;
    border: 1px solid #d0dde8;
    margin-bottom: 14px;
    border-radius: 2px;
    overflow: hidden;
  }
  .section-header {
    background: #154360;
    color: white;
    padding: 8px 14px;
    font-size: 11px;
    font-weight: bold;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    display: flex;
    align-items: center;
    gap: 8px;
  }
  .section-header .sec-num {
    background: #f39c12;
    color: #1a1a2e;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 10px;
    font-weight: bold;
    flex-shrink: 0;
  }
  .section-body { padding: 14px; }
  .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 14px;
    margin-bottom: 14px;
  }
  .field-group { display: flex; flex-direction: column; gap: 4px; }
  .field-label {
    font-size: 11px;
    font-weight: bold;
    color: #1a3a5c;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }
  .field-label .req { color: #c0392b; margin-left: 2px; }
  .field-hint { font-size: 10px; color: #888; }
  select, input[type="text"] {
    width: 100%;
    padding: 6px 8px;
    border: 1px solid #b0c4d8;
    border-radius: 2px;
    font-size: 11px;
    color: #1a1a2e;
    background: #f8fbfd;
    outline: none;
    transition: border-color 0.15s;
  }
  select:focus, input[type="text"]:focus {
    border-color: #154360;
    background: white;
    box-shadow: 0 0 0 2px rgba(21,67,96,0.1);
  }
  .doc-table { width: 100%; border-collapse: collapse; font-size: 11px; }
  .doc-table th {
    background: #eaf2f8;
    border: 1px solid #c8dce9;
    padding: 6px 10px;
    text-align: left;
    font-size: 10px;
    font-weight: bold;
    text-transform: uppercase;
    color: #154360;
    letter-spacing: 0.3px;
  }
  .doc-table td {
    border: 1px solid #dce8f0;
    padding: 7px 10px;
    vertical-align: middle;
  }
  .doc-table tr:nth-child(even) td { background: #f7fbfd; }
  .doc-name {
    font-weight: bold;
    color: #154360;
    font-size: 11px;
    line-height: 1.3;
  }
  .doc-desc { font-size: 10px; color: #777; margin-top: 1px; }
  .radio-group {
    display: flex;
    gap: 12px;
    align-items: center;
  }
  .radio-label {
    display: flex;
    align-items: center;
    gap: 4px;
    font-size: 11px;
    color: #333;
    cursor: pointer;
  }
  .radio-label input[type="radio"] { cursor: pointer; accent-color: #154360; }
  .upload-zone {
    border: 1.5px dashed #154360;
    background: #eaf2f8;
    border-radius: 2px;
    padding: 8px 12px;
    text-align: center;
    cursor: pointer;
    transition: background 0.15s;
    position: relative;
    min-width: 160px;
  }
  .upload-zone:hover { background: #d6eaf8; }
  .upload-zone input[type="file"] {
    position: absolute;
    inset: 0;
    opacity: 0;
    cursor: pointer;
    width: 100%;
    height: 100%;
  }
  .upload-zone .uz-icon { font-size: 16px; color: #154360; }
  .upload-zone .uz-text { font-size: 10px; color: #154360; font-weight: bold; }
  .upload-zone .uz-hint { font-size: 9px; color: #888; }
  .uploaded-file {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #e8f5e9;
    border: 1px solid #a5d6a7;
    border-radius: 2px;
    padding: 4px 8px;
    font-size: 10px;
    color: #2e7d32;
    margin-top: 4px;
  }
  .uploaded-file .remove-btn {
    margin-left: auto;
    cursor: pointer;
    color: #c62828;
    font-weight: bold;
    font-size: 11px;
    border: none;
    background: none;
    padding: 0 2px;
  }
  .year-table { width: 100%; border-collapse: collapse; font-size: 11px; }
  .year-table th {
    background: #1a3a5c;
    color: white;
    border: 1px solid #0d2d47;
    padding: 6px 10px;
    text-align: left;
    font-size: 10px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
  }
  .year-table td {
    border: 1px solid #d0dde8;
    padding: 6px 10px;
    vertical-align: middle;
    background: white;
  }
  .year-table tr:nth-child(even) td { background: #f7fbfd; }
  .year-badge {
    background: #154360;
    color: white;
    font-size: 10px;
    font-weight: bold;
    padding: 2px 7px;
    border-radius: 2px;
    white-space: nowrap;
  }
  .add-year-btn {
    background: #154360;
    color: white;
    border: none;
    padding: 7px 16px;
    font-size: 11px;
    font-weight: bold;
    cursor: pointer;
    border-radius: 2px;
    display: flex;
    align-items: center;
    gap: 6px;
    margin-top: 10px;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    transition: background 0.15s;
  }
  .add-year-btn:hover { background: #1a6fa8; }
  .add-year-btn:disabled { background: #aaa; cursor: not-allowed; }
  .year-count-info {
    font-size: 10px;
    color: #666;
    margin-top: 6px;
  }
  .form-footer {
    background: #eaf2f8;
    border-top: 1px solid #c8dce9;
    padding: 12px 14px;
    display: flex;
    align-items: center;
    justify-content: space-between;
  }
  .btn-primary {
    background: #154360;
    color: white;
    border: none;
    padding: 9px 24px;
    font-size: 12px;
    font-weight: bold;
    cursor: pointer;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-radius: 2px;
    transition: background 0.15s;
  }
  .btn-primary:hover { background: #1a6fa8; }
  .btn-secondary {
    background: white;
    color: #154360;
    border: 1.5px solid #154360;
    padding: 8px 18px;
    font-size: 11px;
    font-weight: bold;
    cursor: pointer;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    border-radius: 2px;
    transition: all 0.15s;
  }
  .btn-secondary:hover { background: #eaf2f8; }
  .btn-danger {
    background: #c0392b;
    color: white;
    border: none;
    padding: 3px 8px;
    font-size: 10px;
    cursor: pointer;
    border-radius: 2px;
    font-weight: bold;
  }
  .notice-bar {
    background: #fff3cd;
    border: 1px solid #ffc107;
    border-left: 4px solid #e67e22;
    padding: 8px 12px;
    font-size: 10px;
    color: #7d6608;
    margin-bottom: 14px;
    display: flex;
    align-items: flex-start;
    gap: 8px;
  }
  .status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #27ae60;
    display: inline-block;
    margin-right: 4px;
  }
  .hidden { display: none; }
  .cell-inline { display: flex; flex-direction: column; gap: 5px; }
  .toast {
    position: fixed;
    top: 20px;
    right: 20px;
    background: #154360;
    color: white;
    padding: 10px 18px;
    border-left: 4px solid #f39c12;
    font-size: 12px;
    border-radius: 2px;
    z-index: 9999;
    display: none;
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  }
  .toast.show { display: block; }
  @media (max-width: 600px) {
    .form-row { grid-template-columns: 1fr; }
    .gov-logo-row { padding: 10px; }
    .gov-title-block .dept-name { font-size: 14px; }
  }
</style>

<div class="portal-wrap">
  <div class="toast" id="toast">✓ Form submitted successfully! Reference No: TSRLA-2025-00412</div>

  <div class="gov-header">
    <div class="gov-top-bar">
      <span>🇮🇳 Government of Telangana</span>
      <span><span class="status-dot"></span>Portal Status: Active &nbsp;|&nbsp; Last Updated: 17-Jun-2025</span>
    </div>
    <div class="gov-logo-row">
      <div class="emblem" aria-hidden="true">⚖️</div>
      <div class="gov-title-block">
        <div class="dept-hi">तेलंगाना सरकार — రాజస్వ విభాగం</div>
        <div class="dept-name">Department of Revenue & Land Records</div>
        <div class="dept-sub">Telangana State &nbsp;|&nbsp; Land Administration Division</div>
      </div>
    </div>
    <div class="gov-subtitle-bar">
      PAHANI DIGITIZATION MANAGEMENT SYSTEM — Sangareddy District Portal (Version 3.1.2)
    </div>
  </div>

  <div class="page-nav">
    <div class="nav-item active">📂 Upload Documents</div>
    <div class="nav-item">📋 View Records</div>
    <div class="nav-item">🔍 Search</div>
    <div class="nav-item">📊 Reports</div>
    <div class="nav-item">⚙️ Settings</div>
  </div>

  <div class="main-body">
    <div class="notice-bar">
      <span>⚠️</span>
      <span><strong>Important Notice:</strong> All uploaded documents must be in PDF format only (Max. 5 MB per file). Ensure scanned documents are legible with minimum 300 DPI resolution. For queries contact: helpdesk.rla@telangana.gov.in | Toll Free: 1800-599-4788</span>
    </div>

    <div class="page-heading">
      <h2>📑 Pahani Document Upload &amp; Registration Form</h2>
      <div class="ref">Form No.: TSRLA/PDU/2025 &nbsp;|&nbsp; Ref: G.O. Ms. No. 92 Rev. Dept.</div>
    </div>

    <div class="breadcrumb">
      <span>Home</span> › <span>Revenue Records</span> › <span>Pahani Digitization</span> › Upload Form
    </div>

    <!-- SECTION 1: AREA -->
    <div class="section-card">
      <div class="section-header">
        <span class="sec-num">1</span>
        Area / Location Details
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
        Core Documents — Pahani Records
      </div>
      <div class="section-body">
        <table class="doc-table" id="core-docs-table">
          <thead>
            <tr>
              <th style="width:28%">Document Name</th>
              <th style="width:18%">Physical Document</th>
              <th style="width:18%">Scanned Copy</th>
              <th>Upload PDF</th>
            </tr>
          </thead>
          <tbody id="core-docs-tbody"></tbody>
        </table>
      </div>
    </div>

    <!-- SECTION 3: YEARWISE -->
    <div class="section-card">
      <div class="section-header">
        <span class="sec-num">3</span>
        Year-wise Pahani Records (Fasali / Agricultural Year)
      </div>
      <div class="section-body">
        <table class="year-table">
          <thead>
            <tr>
              <th style="width:14%">Fasali Year</th>
              <th style="width:20%">Physical Document</th>
              <th style="width:20%">Scanned Copy</th>
              <th>Upload PDF</th>
              <th style="width:8%; text-align:center;">Action</th>
            </tr>
          </thead>
          <tbody id="year-table-body"></tbody>
        </table>
        <button class="add-year-btn" id="add-year-btn" onclick="addYear()">
          ➕ Add Next Year Record
        </button>
        <div class="year-count-info" id="year-count-info"></div>
      </div>
    </div>

    <div class="form-footer">
      <div style="display:flex;gap:10px;">
        <button class="btn-secondary" onclick="resetForm()">🔄 Reset Form</button>
        <button class="btn-secondary">💾 Save as Draft</button>
      </div>
      <div style="display:flex;align-items:center;gap:12px;">
        <span style="font-size:10px;color:#666;">Fields marked <span style="color:#c0392b;font-weight:bold;">*</span> are mandatory</span>
        <button class="btn-primary" onclick="submitForm()">✔ Submit &amp; Register</button>
      </div>
    </div>
  </div>
</div>

<script>
const villageData = {
  hayathnagar: ['Hayathnagar', 'Saroornagar', 'Vanasthalipuram', 'Bandlaguda Jagir', 'Pedda Amberpet'],
  ibrahimpatnam: ['Ibrahimpatnam', 'Manchal', 'Pedda Gollagudem', 'Yacharam', 'Turkapally'],
  keesara: ['Keesara', 'Cherlapally', 'Kushaiguda', 'Ghatkesar', 'Boduppal'],
  malkajgiri: ['Malkajgiri', 'Uppal', 'Kapra', 'Neredmet', 'Medipally'],
  medchal: ['Medchal', 'Kompally', 'Nagaram', 'Bollaram', 'Dundigal'],
  rajendranagar: ['Rajendranagar', 'Budwel', 'Jilakarragudem', 'Gandipet', 'Narsingi'],
  shamshabad: ['Shamshabad', 'Kothur', 'Farooqnagar', 'Chevella Road', 'Shadnagar'],
  shankarpally: ['Shankarpally', 'Mokila', 'Ameenpur', 'Sultanpur', 'Gummadidala'],
  chevella: ['Chevella', 'Marpally', 'Pudur', 'Vikarabad', 'Donthanpally'],
  vikarabad: ['Vikarabad', 'Tandur', 'Kodangal', 'Marpally', 'Nawabpet']
};

const coreDocuments = [
  { id: 'sethwar', name: 'Sethwar Pahani', desc: 'Survey Wise Area Statement (పట్టా పహాణి)' },
  { id: 'kasra', name: 'Kasra Pahani', desc: 'Crop Survey Register (కాసరా పహాణి)' },
  { id: 'sessala', name: 'Sessala Pahani', desc: 'Detailed Field Measurement Record (సెస్సాల పహాణి)' }
];

const state = {
  coreFiles: {},
  yearFiles: {},
  yearRows: [],
  currentYear: 1960
};

const ALL_YEARS = [];
for (let y = 1960; y <= 2024; y++) {
  ALL_YEARS.push(`${y}-${String(y+1).slice(-2)}`);
}

function updateVillages() {
  const m = document.getElementById('mandal-select').value;
  const vs = document.getElementById('village-select');
  vs.innerHTML = '<option value="">— Select Village —</option>';
  if (m && villageData[m]) {
    villageData[m].forEach(v => {
      const o = document.createElement('option');
      o.value = v.toLowerCase().replace(/\s/g, '_');
      o.textContent = v;
      vs.appendChild(o);
    });
    vs.disabled = false;
  } else {
    vs.disabled = true;
  }
}

function makeRadioGroup(name, onChange) {
  const d = document.createElement('div');
  d.className = 'radio-group';
  ['Yes', 'No'].forEach(val => {
    const lbl = document.createElement('label');
    lbl.className = 'radio-label';
    const inp = document.createElement('input');
    inp.type = 'radio';
    inp.name = name;
    inp.value = val;
    inp.addEventListener('change', onChange);
    lbl.appendChild(inp);
    lbl.appendChild(document.createTextNode(' ' + val));
    d.appendChild(lbl);
  });
  return d;
}

function makeUploadCell(key, store) {
  const wrap = document.createElement('div');
  wrap.className = 'cell-inline';
  const zone = document.createElement('div');
  zone.className = 'upload-zone';
  zone.innerHTML = `<input type="file" accept=".pdf" /><div class="uz-icon">📄</div><div class="uz-text">Click to Upload PDF</div><div class="uz-hint">Max 5 MB | PDF only</div>`;
  const fileInput = zone.querySelector('input');
  fileInput.addEventListener('change', () => {
    const f = fileInput.files[0];
    if (f) {
      store[key] = f.name;
      showFileTag(wrap, zone, f.name, key, store);
    }
  });
  wrap.appendChild(zone);
  return wrap;
}

function showFileTag(wrap, zone, name, key, store) {
  const old = wrap.querySelector('.uploaded-file');
  if (old) old.remove();
  const tag = document.createElement('div');
  tag.className = 'uploaded-file';
  tag.innerHTML = `📄 ${name.length > 28 ? name.slice(0,25)+'...' : name} <button class="remove-btn" title="Remove">✕</button>`;
  tag.querySelector('.remove-btn').onclick = () => {
    delete store[key];
    tag.remove();
    zone.querySelector('input').value = '';
  };
  wrap.appendChild(tag);
}

function buildCoreTable() {
  const tbody = document.getElementById('core-docs-tbody');
  tbody.innerHTML = '';
  coreDocuments.forEach(doc => {
    const tr = document.createElement('tr');
    const tdName = document.createElement('td');
    tdName.innerHTML = `<div class="doc-name">${doc.name}</div><div class="doc-desc">${doc.desc}</div>`;

    const tdPhys = document.createElement('td');
    const physRadio = makeRadioGroup(`phys_${doc.id}`, function() {
      const val = this.value;
      scanCell.innerHTML = '';
      uploadCell.innerHTML = '';
      if (val === 'Yes') {
        const scanRadio = makeRadioGroup(`scan_${doc.id}`, function() {
          uploadCell.innerHTML = '';
          if (this.value === 'Yes') {
            uploadCell.appendChild(makeUploadCell(doc.id, state.coreFiles));
          }
        });
        scanCell.appendChild(scanRadio);
      }
    });
    tdPhys.appendChild(physRadio);

    const tdScan = document.createElement('td');
    tdScan.innerHTML = '<span style="color:#bbb;font-size:10px;">— Select Physical first —</span>';
    const scanCell = tdScan;

    const tdUpload = document.createElement('td');
    tdUpload.innerHTML = '<span style="color:#bbb;font-size:10px;">—</span>';
    const uploadCell = tdUpload;

    tr.appendChild(tdName);
    tr.appendChild(tdPhys);
    tr.appendChild(tdScan);
    tr.appendChild(tdUpload);
    tbody.appendChild(tr);
  });
}

function addYear() {
  if (state.currentYear > 2024) {
    document.getElementById('add-year-btn').disabled = true;
    return;
  }
  const yr = `${state.currentYear}-${String(state.currentYear+1).slice(-2)}`;
  state.yearRows.push(yr);
  state.currentYear++;
  renderYearTable();
  updateAddBtn();
}

function renderYearTable() {
  const tbody = document.getElementById('year-table-body');
  tbody.innerHTML = '';
  state.yearRows.forEach((yr, idx) => {
    const tr = document.createElement('tr');
    tr.id = `yr-row-${idx}`;

    const tdYr = document.createElement('td');
    tdYr.innerHTML = `<span class="year-badge">${yr}</span>`;

    const physKey = `yr_phys_${idx}`;
    const scanKey = `yr_scan_${idx}`;
    const fileKey = `yr_file_${yr}`;

    const tdPhys = document.createElement('td');
    const physRadio = makeRadioGroup(physKey, function() {
      const scanCell = document.getElementById(`scan-cell-${idx}`);
      const uploadCell = document.getElementById(`upload-cell-${idx}`);
      scanCell.innerHTML = '';
      uploadCell.innerHTML = '<span style="color:#bbb;font-size:10px;">—</span>';
      if (this.value === 'Yes') {
        const scanRadio = makeRadioGroup(scanKey, function() {
          const uc = document.getElementById(`upload-cell-${idx}`);
          uc.innerHTML = '';
          if (this.value === 'Yes') {
            uc.appendChild(makeUploadCell(fileKey, state.yearFiles));
          } else {
            uc.innerHTML = '<span style="color:#aaa;font-size:10px;">Not scanned</span>';
          }
        });
        scanCell.appendChild(scanRadio);
      } else {
        scanCell.innerHTML = '<span style="color:#aaa;font-size:10px;">N/A</span>';
        uploadCell.innerHTML = '<span style="color:#aaa;font-size:10px;">—</span>';
        delete state.yearFiles[fileKey];
      }
    });
    tdPhys.appendChild(physRadio);

    const tdScan = document.createElement('td');
    tdScan.id = `scan-cell-${idx}`;
    tdScan.innerHTML = '<span style="color:#bbb;font-size:10px;">— Select first —</span>';

    const tdUpload = document.createElement('td');
    tdUpload.id = `upload-cell-${idx}`;
    tdUpload.innerHTML = '<span style="color:#bbb;font-size:10px;">—</span>';

    const tdAction = document.createElement('td');
    tdAction.style.textAlign = 'center';
    if (idx === state.yearRows.length - 1) {
      const delBtn = document.createElement('button');
      delBtn.className = 'btn-danger';
      delBtn.textContent = '✕';
      delBtn.title = 'Remove this year';
      delBtn.onclick = () => {
        state.yearRows.pop();
        state.currentYear--;
        delete state.yearFiles[fileKey];
        renderYearTable();
        updateAddBtn();
      };
      tdAction.appendChild(delBtn);
    } else {
      tdAction.innerHTML = '<span style="color:#ccc;font-size:10px;">—</span>';
    }

    tr.appendChild(tdYr);
    tr.appendChild(tdPhys);
    tr.appendChild(tdScan);
    tr.appendChild(tdUpload);
    tr.appendChild(tdAction);
    tbody.appendChild(tr);
  });

  const info = document.getElementById('year-count-info');
  if (state.yearRows.length > 0) {
    const remaining = 2024 - state.currentYear + 1;
    info.textContent = `${state.yearRows.length} year(s) added | ${remaining > 0 ? remaining + ' more year(s) available (up to 2024-25)' : 'All years up to 2024-25 have been added'}`;
  } else {
    info.textContent = 'No year records added yet. Click "Add Next Year Record" to begin from 1960-61.';
  }
}

function updateAddBtn() {
  const btn = document.getElementById('add-year-btn');
  if (state.currentYear > 2024) {
    btn.disabled = true;
    btn.textContent = '✓ All Years Added (1960-61 to 2024-25)';
  } else {
    btn.disabled = false;
    btn.textContent = `➕ Add Next Year Record (${state.currentYear}-${String(state.currentYear+1).slice(-2)})`;
  }
}

function resetForm() {
  if (!confirm('Are you sure you want to reset the form? All entered data will be lost.')) return;
  document.getElementById('mandal-select').value = '';
  document.getElementById('village-select').value = '';
  document.getElementById('village-select').disabled = true;
  state.coreFiles = {};
  state.yearFiles = {};
  state.yearRows = [];
  state.currentYear = 1960;
  buildCoreTable();
  renderYearTable();
  updateAddBtn();
}

function submitForm() {
  const mandal = document.getElementById('mandal-select').value;
  const village = document.getElementById('village-select').value;
  if (!mandal || !village) {
    alert('Please select both Mandal and Village before submitting.');
    return;
  }
  const toast = document.getElementById('toast');
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 4000);
}

buildCoreTable();
renderYearTable();
updateAddBtn();
document.getElementById('year-count-info').textContent = 'No year records added yet. Click "Add Next Year Record" to begin from 1960-61.';
</script>
