<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Pahani Records — Land Record Digitalization</title>
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }
    body { font-family: Arial, sans-serif; font-size: 12px; background: #f0f4f8; color: #1a1a2e; min-height: 100vh; }

    /* ── HEADER ── */
    .gov-header {
      background: linear-gradient(135deg, #154360 0%, #1a5276 50%, #1e618f 100%);
      color: white;
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
      width: 56px; height: 56px;
      background: white; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 28px; border: 2px solid #f39c12;
    }
    .gov-title-block { flex: 1; }
    .gov-title-block .dept-name {
      font-size: 18px; font-weight: bold; color: white;
      line-height: 1.2; text-transform: uppercase; letter-spacing: 1px;
    }
    .gov-title-block .dept-sub { font-size: 11px; color: #aed6f1; margin-top: 2px; }
    .gov-subtitle-bar {
      background: #1a6fa8;
      padding: 7px 20px;
      font-size: 11px; color: #d6eaf8;
      border-top: 1px solid rgba(255,255,255,0.15);
      text-align: center; letter-spacing: 0.3px;
    }

    /* ── NAV ── */
    .page-nav {
      background: #154360;
      border-bottom: 2px solid #f39c12;
      display: flex; align-items: center;
      padding: 0 20px; font-size: 11px;
    }
    .nav-item {
      color: #aed6f1; padding: 8px 14px; cursor: pointer;
      border-right: 1px solid rgba(255,255,255,0.1);
      text-decoration: none; display: inline-block;
      transition: background 0.15s;
    }
    .nav-item:hover { background: rgba(255,255,255,0.1); color: white; }
    .nav-item.active { background: #f39c12; color: #1a1a2e; font-weight: bold; }

    /* ── MAIN ── */
    .main-body { padding: 16px 20px; max-width: 1000px; margin: 0 auto; }
    .page-heading {
      background: white; border: 1px solid #d5e8f5;
      border-left: 4px solid #154360;
      padding: 10px 16px; margin-bottom: 14px;
      display: flex; align-items: center; justify-content: space-between;
    }
    .page-heading h2 { font-size: 13px; font-weight: bold; color: #154360; text-transform: uppercase; letter-spacing: 0.5px; }
    .breadcrumb {
      font-size: 10px; color: #666; margin-bottom: 10px;
      display: flex; align-items: center; gap: 4px;
    }
    .breadcrumb a { color: #154360; cursor: pointer; text-decoration: none; }
    .breadcrumb a:hover { text-decoration: underline; }
    .status-dot { width: 8px; height: 8px; border-radius: 50%; background: #27ae60; display: inline-block; margin-right: 4px; }

    /* ── SECTION CARD ── */
    .section-card {
      background: white; border: 1px solid #d0dde8;
      margin-bottom: 14px; border-radius: 2px; overflow: hidden;
    }
    .section-header {
      background: #154360; color: white;
      padding: 8px 14px; font-size: 11px; font-weight: bold;
      text-transform: uppercase; letter-spacing: 0.5px;
      display: flex; align-items: center; gap: 8px;
    }
    .section-header .sec-num {
      background: #f39c12; color: #1a1a2e;
      width: 20px; height: 20px; border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-size: 10px; font-weight: bold; flex-shrink: 0;
    }
    .section-body { padding: 14px; }

    /* ── FORM FIELDS ── */
    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .field-group { display: flex; flex-direction: column; gap: 4px; }
    .field-label { font-size: 11px; font-weight: bold; color: #1a3a5c; text-transform: uppercase; letter-spacing: 0.3px; }
    .field-hint { font-size: 10px; color: #888; }
    select {
      width: 100%; padding: 6px 8px;
      border: 1px solid #b0c4d8; border-radius: 2px;
      font-size: 11px; color: #1a1a2e; background: #f8fbfd;
      outline: none; transition: border-color 0.15s;
    }
    select:focus { border-color: #154360; background: white; box-shadow: 0 0 0 2px rgba(21,67,96,0.1); }

    /* ── SEARCH BTN ── */
    .btn-search {
      background: #154360; color: white;
      border: none; padding: 7px 20px;
      font-size: 11px; font-weight: bold;
      cursor: pointer; border-radius: 2px;
      text-transform: uppercase; letter-spacing: 0.3px;
      transition: background 0.15s; margin-top: 4px;
    }
    .btn-search:hover { background: #1a6fa8; }

    /* ── RESULTS PANEL ── */
    #results-panel { display: none; }
    .results-meta {
      background: #eaf2f8; border: 1px solid #b8d4e8;
      border-left: 4px solid #154360;
      padding: 8px 14px; margin-bottom: 10px;
      font-size: 11px; color: #1a3a5c;
      display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px;
    }
    .results-meta strong { color: #154360; }
    .badge-count {
      background: #154360; color: white;
      font-size: 10px; font-weight: bold;
      padding: 2px 8px; border-radius: 10px;
    }

    /* ── DOCS TABLE ── */
    .view-table { width: 100%; border-collapse: collapse; font-size: 11px; }
    .view-table th {
      background: #1a3a5c; color: white;
      border: 1px solid #0d2d47;
      padding: 7px 10px; text-align: left;
      font-size: 10px; text-transform: uppercase; letter-spacing: 0.3px;
    }
    .view-table td {
      border: 1px solid #d0dde8; padding: 8px 10px;
      vertical-align: middle; background: white;
    }
    .view-table tr:nth-child(even) td { background: #f7fbfd; }
    .view-table tr:hover td { background: #eaf2f8; }

    .doc-name { font-weight: bold; color: #154360; font-size: 11px; }
    .doc-sub  { font-size: 10px; color: #777; margin-top: 1px; }

    .badge-type {
      display: inline-block; font-size: 9px; font-weight: bold;
      padding: 2px 6px; border-radius: 2px; text-transform: uppercase; letter-spacing: 0.3px;
    }
    .badge-core { background: #d6eaf8; color: #154360; border: 1px solid #aed6f1; }
    .badge-year { background: #e8f5e9; color: #1e8449; border: 1px solid #a9dfb0; }

    .status-avail { color: #1e8449; font-weight: bold; font-size: 10px; }
    .status-none  { color: #999; font-size: 10px; font-style: italic; }

    .btn-show-pdf {
      background: #154360; color: white;
      border: none; padding: 5px 12px;
      font-size: 10px; font-weight: bold;
      cursor: pointer; border-radius: 2px;
      text-transform: uppercase; letter-spacing: 0.3px;
      transition: background 0.15s;
      display: inline-flex; align-items: center; gap: 4px;
    }
    .btn-show-pdf:hover { background: #f39c12; color: #1a1a2e; }

    /* ── EMPTY STATE ── */
    .empty-state {
      text-align: center; padding: 32px 20px;
      color: #999; font-size: 11px;
    }
    .empty-state .es-icon { font-size: 32px; margin-bottom: 8px; }
    .empty-state strong { display: block; font-size: 12px; color: #555; margin-bottom: 4px; }

    /* ── NOTICE ── */
    .notice-bar {
      background: #fff3cd; border: 1px solid #ffc107;
      border-left: 4px solid #e67e22;
      padding: 8px 12px; font-size: 10px; color: #7d6608;
      margin-bottom: 14px; display: flex; align-items: flex-start; gap: 8px;
    }

    /* ── PDF VIEWER TAB ── */
    #pdf-overlay {
      display: none;
      position: fixed; inset: 0; z-index: 9000;
      flex-direction: column; background: #f0f4f8;
    }
    #pdf-overlay.open { display: flex; }

    .pdf-tab-header {
      background: linear-gradient(135deg, #154360 0%, #1a5276 50%, #1e618f 100%);
      border-bottom: 4px solid #f39c12;
      flex-shrink: 0;
    }
    .pdf-tab-topbar {
      background: #0d2d47;
      display: flex; align-items: center;
      justify-content: space-between;
      padding: 5px 16px; font-size: 10px; color: #b8cdd9;
    }
    .pdf-tab-logo-row {
      display: flex; align-items: center; gap: 12px; padding: 8px 16px 7px;
    }
    .pdf-tab-logo-row .emblem { width: 40px; height: 40px; font-size: 20px; }
    .pdf-tab-logo-row .dept-name { font-size: 14px; font-weight: bold; color: white; text-transform: uppercase; letter-spacing: 1px; }
    .pdf-tab-logo-row .dept-sub { font-size: 10px; color: #aed6f1; }

    .pdf-tab-nav {
      background: #154360; border-bottom: 2px solid #f39c12;
      display: flex; align-items: center;
      justify-content: space-between;
      padding: 0 16px; font-size: 11px;
    }
    .pdf-tab-nav .nav-left { display: flex; align-items: center; gap: 0; }
    .pdf-breadcrumb {
      font-size: 10px; color: #aed6f1; padding: 8px 0; display: flex; align-items: center; gap: 4px;
    }
    .pdf-breadcrumb span { color: #f39c12; }
    .btn-back-nav {
      background: rgba(255,255,255,0.12); color: white;
      border: 1px solid rgba(255,255,255,0.2);
      padding: 5px 14px; font-size: 10px; font-weight: bold;
      cursor: pointer; border-radius: 2px;
      text-transform: uppercase; letter-spacing: 0.3px;
      transition: background 0.15s; display: flex; align-items: center; gap: 5px;
    }
    .btn-back-nav:hover { background: rgba(255,255,255,0.22); }

    .pdf-tab-body { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

    .pdf-info-bar {
      background: white; border-bottom: 1px solid #d0dde8;
      padding: 7px 16px;
      display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 6px;
      font-size: 10px; color: #333; flex-shrink: 0;
    }
    .pdf-info-bar strong { color: #154360; font-size: 11px; }
    .pdf-info-pill {
      display: inline-flex; align-items: center; gap: 4px;
      background: #eaf2f8; border: 1px solid #b8d4e8;
      padding: 2px 8px; border-radius: 10px;
      font-size: 10px; color: #154360; font-weight: bold;
    }
    .btn-dl {
      background: #154360; color: white; border: none;
      padding: 4px 12px; font-size: 10px; font-weight: bold;
      cursor: pointer; border-radius: 2px;
      text-transform: uppercase; letter-spacing: 0.3px;
      transition: background 0.15s;
    }
    .btn-dl:hover { background: #f39c12; color: #1a1a2e; }

    #pdf-frame { flex: 1; border: none; background: #555; }

    /* ── FOOTER ── */
    .gov-footer {
      background: #0d2d47; color: #b8cdd9;
      font-size: 10px; text-align: center;
      padding: 10px 20px; margin-top: 20px;
      border-top: 3px solid #f39c12;
    }

    @media (max-width: 600px) {
      .form-row { grid-template-columns: 1fr; }
      .gov-title-block .dept-name { font-size: 14px; }
    }
  </style>
</head>
<body>

<!-- ════════════════════════════════════════
     PDF VIEWER OVERLAY (full page, same chrome)
════════════════════════════════════════ -->
<div id="pdf-overlay" role="dialog" aria-modal="true" aria-label="PDF Viewer">

  <div class="pdf-tab-header">
    <div class="pdf-tab-topbar">
      <span><span class="status-dot"></span>Portal Status: Active &nbsp;|&nbsp; Last Updated: 17-Jun-2025</span>
      <span id="pdf-tab-ref"></span>
    </div>
    <div class="pdf-tab-logo-row">
      <div class="emblem" aria-hidden="true">⚖️</div>
      <div>
        <div class="dept-name">Land Record Digitalization</div>
        <div class="dept-sub">Sangareddy &nbsp;|&nbsp; Revenue Department</div>
      </div>
    </div>
  </div>

  <div class="pdf-tab-nav">
    <div class="nav-left">
      <div class="pdf-breadcrumb">
        <a href="#" onclick="closePdfViewer();return false;" style="color:#aed6f1;text-decoration:none">Home</a>
        &rsaquo; <a href="#" onclick="closePdfViewer();return false;" style="color:#aed6f1;text-decoration:none">View Records</a>
        &rsaquo; <span id="pdf-breadcrumb-doc">Document</span>
      </div>
    </div>
    <button class="btn-back-nav" onclick="closePdfViewer()">
      &#8592; Back to Records
    </button>
  </div>

  <div class="pdf-tab-body">
    <div class="pdf-info-bar">
      <div>
        <strong id="pdf-viewer-title">—</strong>
        <span style="margin-left:10px;color:#777" id="pdf-viewer-sub"></span>
      </div>
      <div style="display:flex;align-items:center;gap:8px">
        <span class="pdf-info-pill" id="pdf-viewer-village">—</span>
        <span class="pdf-info-pill" id="pdf-viewer-mandal">—</span>
        <button class="btn-dl" id="pdf-dl-btn">&#8595; Download</button>
      </div>
    </div>
    <iframe id="pdf-frame" title="PDF Document Viewer"></iframe>
  </div>

</div>
<!-- /PDF OVERLAY -->


<!-- ════════════════════════════════════════
     MAIN PAGE
════════════════════════════════════════ -->
<div id="main-page">

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
    <a class="nav-item" href="index.php">&#128194; Upload Documents</a>
    <a class="nav-item active" href="view.php">&#128203; View Records</a>
    <a class="nav-item" href="#">&#128269; Search</a>
    <a class="nav-item" href="#">&#128202; Reports</a>
    <a class="nav-item" href="#">&#9881;&#65039; Settings</a>
  </div>

  <div class="main-body">

    <div class="page-heading">
      <h2>&#128196; Pahani — View Records</h2>
      <div style="font-size:10px;color:#666">Select Mandal &amp; Village to view all uploaded documents</div>
    </div>

    <div class="breadcrumb">
      <a href="#">Home</a> &rsaquo; <a href="#">Revenue Records</a> &rsaquo; <a href="#">Pahani Digitization</a> &rsaquo; View Records
    </div>

    <div class="notice-bar">
      &#9432;&nbsp; <span>This page displays all Pahani records uploaded for the selected village. Click <strong>Show PDF</strong> to view the document within the portal. Records marked "Not Available" have not yet been digitised.</span>
    </div>

    <!-- SECTION 1: SELECT AREA -->
    <div class="section-card">
      <div class="section-header">
        <span class="sec-num">1</span>
        Select Revenue Mandal &amp; Village
      </div>
      <div class="section-body">
        <div class="form-row">
          <div class="field-group">
            <label class="field-label" for="mandal-select">Mandal</label>
            <div class="field-hint">Select the Mandal jurisdiction</div>
            <select id="mandal-select" onchange="onMandalChange()">
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
            <label class="field-label" for="village-select">Village / Revenue Village</label>
            <div class="field-hint">Select village within the Mandal</div>
            <select id="village-select" disabled onchange="onVillageChange()">
              <option value="">— First Select Mandal —</option>
            </select>
          </div>
        </div>
        <div style="margin-top:10px">
          <button class="btn-search" onclick="loadRecords()">&#128269; View Records</button>
        </div>
      </div>
    </div>

    <!-- SECTION 2: RESULTS -->
    <div id="results-panel">
      <div class="section-card">
        <div class="section-header">
          <span class="sec-num">2</span>
          <span id="results-heading">Uploaded Pahani Records</span>
        </div>
        <div class="section-body" id="results-body">
          <!-- dynamically populated -->
        </div>
      </div>
    </div>

  </div><!-- /main-body -->

  <div class="gov-footer">
    &copy; 2025 Revenue Department, Government of Telangana &nbsp;|&nbsp; Pahani Digitization Management System &nbsp;|&nbsp; All rights reserved
  </div>

</div><!-- /main-page -->


<script>
/* ──────────────────────────────────────────────
   DATA
────────────────────────────────────────────── */
const villageData = {
  hayathnagar:    ['Hayathnagar','Saroornagar','Vanasthalipuram','Bandlaguda Jagir','Pedda Amberpet'],
  ibrahimpatnam:  ['Ibrahimpatnam','Manchal','Pedda Gollagudem','Yacharam','Turkapally'],
  keesara:        ['Keesara','Cherlapally','Kushaiguda','Ghatkesar','Boduppal'],
  malkajgiri:     ['Malkajgiri','Uppal','Kapra','Neredmet','Medipally'],
  medchal:        ['Medchal','Kompally','Nagaram','Bollaram','Dundigal'],
  rajendranagar:  ['Rajendranagar','Budwel','Jilakarragudem','Gandipet','Narsingi'],
  shamshabad:     ['Shamshabad','Kothur','Farooqnagar','Chevella Road','Shadnagar'],
  shankarpally:   ['Shankarpally','Mokila','Ameenpur','Sultanpur','Gummadidala'],
  chevella:       ['Chevella','Marpally','Pudur','Vikarabad','Donthanpally'],
  vikarabad:      ['Vikarabad','Tandur','Kodangal','Marpally','Nawabpet'],
  sangareddy:     ['Arutla','Byathole','Cheriyal','Chidruppa','Chintalpalle','Edthanur','Fasalwadi','Indrakaran','Irigipalle','Ismailkhanpet','Julkal','Kalabgoor','Kalvemula','Kandi','Kasipur','Kothlapur','Koulampet','Makthaalloor','Mamidipalle','Mohd.Shapur','Nagapur','Tadlapalle','Topgonda','Utharpalle']
};

const CORE_DOCS = [
  { value:'sethwar', label:'Sethwar Pahani',  type:'core', desc:'Survey Wise Area Statement (పట్టా పహాణి)' },
  { value:'kasra',   label:'Kasra Pahani',    type:'core', desc:'Crop Survey Register (కాసరా పహాణి)' },
  { value:'sessala', label:'Sessala Pahani',  type:'core', desc:'Detailed Field Measurement Record (సెస్సాల పహాణి)' }
];
const YEAR_DOCS = [];
for (let y = 1960; y <= 2024; y++) {
  YEAR_DOCS.push({ value:'yr_'+y, label:y+'-'+String(y+1).slice(-2), type:'year', desc:'Fasali Year Record' });
}
const ALL_DOCS = [...CORE_DOCS, ...YEAR_DOCS];


const UPLOADED_RECORDS = {
  'sangareddy__kandi': {
    'sethwar': {
      fileName: 'Sethwar_Kandi_Sangareddy.pdf',
      fileUrl:  '/pahani/Sethwar_Kandi_Sangareddy.pdf'
    },
    'kasra': {
      fileName: 'Kasra_Kandi_Sangareddy.pdf',
      fileUrl:  '/pahani/Kasra_Kandi_Sangareddy.pdf'
    },
    'yr_1985': {
      fileName: 'Pahani_1985-86_Kandi_Sangareddy.pdf',
      fileUrl:  '/pahani/Pahani_1985-86_Kandi_Sangareddy.pdf'
    },
    'yr_2010': {
      fileName: 'Pahani_2010-11_Kandi_Sangareddy.pdf',
      fileUrl:  '/pahani/Pahani_2010-11_Kandi_Sangareddy.pdf'
    }
  }
  /* Add more village records here */
};

/* ──────────────────────────────────────────────
   HELPERS
────────────────────────────────────────────── */
function villageKey(mandal, village) {
  return mandal.toLowerCase() + '__' + village.toLowerCase().replace(/\s/g,'_');
}

function onMandalChange() {
  const m  = document.getElementById('mandal-select').value;
  const vs = document.getElementById('village-select');
  vs.innerHTML = '<option value="">— Select Village —</option>';
  if (m && villageData[m]) {
    villageData[m].forEach(v => {
      const o = document.createElement('option');
      o.value = v.toLowerCase().replace(/\s/g,'_');
      o.textContent = v;
      vs.appendChild(o);
    });
    vs.disabled = false;
  } else {
    vs.disabled = true;
  }
  document.getElementById('results-panel').style.display = 'none';
}

function onVillageChange() {
  document.getElementById('results-panel').style.display = 'none';
}

/* ──────────────────────────────────────────────
   LOAD & RENDER RECORDS
────────────────────────────────────────────── */
function loadRecords() {
  const mandalSel  = document.getElementById('mandal-select');
  const villageSel = document.getElementById('village-select');
  const mandal     = mandalSel.value;
  const village    = villageSel.value;

  if (!mandal || !village) {
    alert('Please select both Mandal and Village first.');
    return;
  }

  const mandalLabel  = mandalSel.options[mandalSel.selectedIndex].text;
  const villageLabel = villageSel.options[villageSel.selectedIndex].text;
  const key          = villageKey(mandal, village);
  const records      = UPLOADED_RECORDS[key] || {};
  const uploadedKeys = Object.keys(records);

  document.getElementById('results-heading').textContent =
    'Uploaded Pahani Records — ' + villageLabel + ', ' + mandalLabel;

  const panel = document.getElementById('results-panel');
  const body  = document.getElementById('results-body');
  panel.style.display = 'block';

  if (uploadedKeys.length === 0) {
    body.innerHTML =
      '<div class="empty-state">' +
      '<div class="es-icon">&#128196;</div>' +
      '<strong>No Records Found</strong>' +
      'No Pahani documents have been uploaded for ' + villageLabel + ', ' + mandalLabel + ' yet.' +
      '</div>';
    return;
  }

  /* meta bar */
  const coreCount = uploadedKeys.filter(k => ['sethwar','kasra','sessala'].includes(k)).length;
  const yearCount = uploadedKeys.filter(k => k.startsWith('yr_')).length;

  body.innerHTML = '';
  const meta = document.createElement('div');
  meta.className = 'results-meta';
  meta.innerHTML =
    '<span>Showing records for <strong>' + villageLabel + '</strong>, ' + mandalLabel + '</span>' +
    '<span style="display:flex;gap:6px;flex-wrap:wrap">' +
    '<span class="badge-count">' + uploadedKeys.length + ' Total</span>' +
    '<span class="badge-count" style="background:#1e8449">' + coreCount + ' Core</span>' +
    '<span class="badge-count" style="background:#1a6fa8">' + yearCount + ' Year-wise</span>' +
    '</span>';
  body.appendChild(meta);

  /* table */
  const table = document.createElement('table');
  table.className = 'view-table';
  table.innerHTML =
    '<thead><tr>' +
    '<th style="width:5%">#</th>' +
    '<th style="width:22%">Document Name</th>' +
    '<th style="width:10%">Type</th>' +
    '<th>File Name</th>' +
    '<th style="width:14%;text-align:center">Action</th>' +
    '</tr></thead>';

  const tbody = document.createElement('tbody');

  ALL_DOCS.forEach((doc, idx) => {
    const rec = records[doc.value];
    if (!rec) return;                     /* only show uploaded docs */

    const tr = document.createElement('tr');

    /* # */
    const tdN = document.createElement('td');
    tdN.style.color = '#999';
    tdN.style.textAlign = 'center';
    tdN.textContent = String(idx + 1).padStart(2,'0');

    /* name */
    const tdName = document.createElement('td');
    tdName.innerHTML =
      '<div class="doc-name">' + doc.label + '</div>' +
      '<div class="doc-sub">' + doc.desc  + '</div>';

    /* type badge */
    const tdType = document.createElement('td');
    if (doc.type === 'core') {
      tdType.innerHTML = '<span class="badge-type badge-core">Core</span>';
    } else {
      tdType.innerHTML = '<span class="badge-type badge-year">Year</span>';
    }

    /* file */
    const tdFile = document.createElement('td');
    tdFile.innerHTML =
      '<span class="status-avail">&#10003; ' + rec.fileName + '</span>';

    /* action */
    const tdAction = document.createElement('td');
    tdAction.style.textAlign = 'center';
    const btn = document.createElement('button');
    btn.className = 'btn-show-pdf';
    btn.innerHTML = '&#128196; Show PDF';
    btn.onclick = function() {
      openPdfViewer({
        title:        doc.label,
        desc:         doc.desc,
        fileName:     rec.fileName,
        fileUrl:      rec.fileUrl,
        mandalLabel:  mandalLabel,
        villageLabel: villageLabel,
        type:         doc.type
      });
    };
    tdAction.appendChild(btn);

    tr.appendChild(tdN);
    tr.appendChild(tdName);
    tr.appendChild(tdType);
    tr.appendChild(tdFile);
    tr.appendChild(tdAction);
    tbody.appendChild(tr);
  });

  table.appendChild(tbody);
  body.appendChild(table);

  panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
}

/* ──────────────────────────────────────────────
   PDF VIEWER
────────────────────────────────────────────── */
function openPdfViewer(opt) {
  const overlay = document.getElementById('pdf-overlay');
  const frame   = document.getElementById('pdf-frame');

  document.getElementById('pdf-viewer-title').textContent   = opt.title;
  document.getElementById('pdf-viewer-sub').textContent     = opt.desc;
  document.getElementById('pdf-viewer-village').textContent = '&#127968; ' + opt.villageLabel;
  document.getElementById('pdf-viewer-mandal').textContent  = '&#128204; ' + opt.mandalLabel;
  document.getElementById('pdf-breadcrumb-doc').textContent = opt.title;
  document.getElementById('pdf-tab-ref').textContent        = opt.fileName;

  /* Download button */
  const dlBtn = document.getElementById('pdf-dl-btn');
  if (opt.fileUrl) {
    dlBtn.onclick = function() {
      const a = document.createElement('a');
      a.href = opt.fileUrl;
      a.download = opt.fileName;
      a.click();
    };
  } else {
    dlBtn.onclick = function() { alert('File URL not configured. Update UPLOADED_RECORDS with real fileUrl paths.'); };
  }

  /*
    fileUrl should be the real Laravel storage URL, e.g.:
    /storage/pahani/hayathnagar/hayathnagar/sethwar.pdf

    For demo, we show a placeholder message inside the iframe.
  */
  if (opt.fileUrl) {
    frame.src = opt.fileUrl;
  } else {
    frame.srcdoc =
      '<!DOCTYPE html><html><head><style>' +
      'body{margin:0;font-family:Arial,sans-serif;background:#3a3a3a;display:flex;align-items:center;justify-content:center;min-height:100vh;color:white;text-align:center}' +
      '.box{background:#4a4a4a;border:2px dashed #888;border-radius:4px;padding:40px 60px}' +
      '.icon{font-size:48px;margin-bottom:16px}h2{font-size:16px;color:#f39c12;margin-bottom:8px}p{font-size:12px;color:#ccc;max-width:360px;line-height:1.7}code{background:#333;padding:2px 6px;border-radius:2px;color:#f39c12}' +
      '</style></head><body>' +
      '<div class="box"><div class="icon">&#128196;</div>' +
      '<h2>' + opt.title + '</h2>' +
      '<p style="color:#aaa;font-size:11px;margin-bottom:12px">' + opt.fileName + '</p>' +
      '<p>Place this file in your Laravel <code>public/pahani/</code> folder and it will load here automatically.</p>' +
      '</div></body></html>';
  }

  document.getElementById('main-page').style.display = 'none';
  overlay.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closePdfViewer() {
  document.getElementById('pdf-overlay').classList.remove('open');
  document.getElementById('main-page').style.display = 'block';
  document.body.style.overflow = '';
  document.getElementById('pdf-frame').src = '';
  document.getElementById('pdf-frame').srcdoc = '';
}

/* Close viewer on Escape */
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape' && document.getElementById('pdf-overlay').classList.contains('open')) {
    closePdfViewer();
  }
});
</script>

</body>
</html>