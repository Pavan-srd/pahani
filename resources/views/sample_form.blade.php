<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
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
    <!-- <div class="nav-item">📋 View Records</div>
    <div class="nav-item">🔍 Search</div>
    <div class="nav-item">📊 Reports</div>
    <div class="nav-item">⚙️ Settings</div> -->
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
<script>
  const villageData={hayathnagar:['Hayathnagar','Saroornagar','Vanasthalipuram','Bandlaguda Jagir','Pedda Amberpet'],ibrahimpatnam:['Ibrahimpatnam','Manchal','Pedda Gollagudem','Yacharam','Turkapally'],keesara:['Keesara','Cherlapally','Kushaiguda','Ghatkesar','Boduppal'],malkajgiri:['Malkajgiri','Uppal','Kapra','Neredmet','Medipally'],medchal:['Medchal','Kompally','Nagaram','Bollaram','Dundigal'],rajendranagar:['Rajendranagar','Budwel','Jilakarragudem','Gandipet','Narsingi'],shamshabad:['Shamshabad','Kothur','Farooqnagar','Chevella Road','Shadnagar'],shankarpally:['Shankarpally','Mokila','Ameenpur','Sultanpur','Gummadidala'],chevella:['Chevella','Marpally','Pudur','Vikarabad','Donthanpally'],vikarabad:['Vikarabad','Tandur','Kodangal','Marpally','Nawabpet']};

const CORE_OPTIONS=[
  {value:'sethwar',label:'Sethwar Pahani',desc:'Survey Wise Area Statement (పట్టా పహాణి)'},
  {value:'kasra',label:'Kasra Pahani',desc:'Crop Survey Register (కాసరా పహాణి)'},
  {value:'sessala',label:'Sessala Pahani',desc:'Detailed Field Measurement Record (సెస్సాల పహాణి)'}
];
const YEAR_OPTIONS=[];
for(let y=1960;y<=2024;y++) YEAR_OPTIONS.push({value:`yr_${y}`,label:`${y}-${String(y+1).slice(-2)}`});
const ALL_OPTIONS=[...CORE_OPTIONS,...YEAR_OPTIONS];

const state={rows:[],usedValues:new Set(),files:{}};

function getAvailableOptions(excludeValue){
  return ALL_OPTIONS.filter(o=>!state.usedValues.has(o.value)||o.value===excludeValue);
}

function updateVillages(){
  const m=document.getElementById('mandal-select').value;
  const vs=document.getElementById('village-select');
  vs.innerHTML='<option value="">— Select Village —</option>';
  if(m&&villageData[m]){
    villageData[m].forEach(v=>{
      const o=document.createElement('option');
      o.value=v.toLowerCase().replace(/\s/g,'_');
      o.textContent=v;
      vs.appendChild(o);
    });
    vs.disabled=false;
  } else {
    vs.disabled=true;
  }
}

function rebuildAllDropdowns(){
  state.rows.forEach(row=>{
    const sel=document.getElementById('docsel-'+row.id);
    if(!sel) return;
    const cur=row.value;
    sel.innerHTML='<option value="">— Select Document —</option>';
    getAvailableOptions(cur).forEach(o=>{
      const opt=document.createElement('option');
      opt.value=o.value;
      opt.textContent=o.label;
      if(o.value===cur) opt.selected=true;
      sel.appendChild(opt);
    });
  });
}

function addRow(){
  const available=ALL_OPTIONS.filter(o=>!state.usedValues.has(o.value));
  if(available.length===0){
    document.getElementById('add-row-btn').disabled=true;
    return;
  }
  const id='row_'+Date.now();
  state.rows.push({id,value:'',physical:null,fileName:null});
  renderTable();
}

function removeRow(id){
  const row=state.rows.find(r=>r.id===id);
  if(row&&row.value) state.usedValues.delete(row.value);
  state.rows=state.rows.filter(r=>r.id!==id);
  renderTable();
  rebuildAllDropdowns();
}

function onDocChange(id,sel){
  const row=state.rows.find(r=>r.id===id);
  if(!row) return;
  if(row.value) state.usedValues.delete(row.value);
  row.value=sel.value;
  if(sel.value) state.usedValues.add(sel.value);
  row.physical=null;
  row.fileName=null;
  delete state.files[id];
  rebuildAllDropdowns();
  renderTable();
}

function onPhysicalChange(id,val){
  const row=state.rows.find(r=>r.id===id);
  if(!row) return;
  row.physical=val;
  row.fileName=null;
  delete state.files[id];
  renderUploadCell(id);
}

function renderUploadCell(id){
  const row=state.rows.find(r=>r.id===id);
  const cell=document.getElementById('upload-'+id);
  if(!cell||!row) return;
  cell.innerHTML='';
  if(row.physical==='Yes'){
    const wrap=document.createElement('div');
    const zone=document.createElement('div');
    zone.className='upload-zone';
    zone.innerHTML='<input type="file" accept=".pdf"><div class="uz-text"><i class="ti ti-upload" aria-hidden="true"></i> Click to upload PDF</div><div class="uz-hint">Max 5 MB | PDF only</div>';
    const fi=zone.querySelector('input');
    fi.addEventListener('change',()=>{
      const f=fi.files[0];
      if(f){
        row.fileName=f.name;
        state.files[id]=f.name;
        const old=wrap.querySelector('.uploaded-file');
        if(old) old.remove();
        const tag=document.createElement('div');
        tag.className='uploaded-file';
        tag.innerHTML=`<i class="ti ti-file" aria-hidden="true"></i> ${f.name.length>26?f.name.slice(0,23)+'...':f.name} <button class="remove-btn" title="Remove file" aria-label="Remove file">✕</button>`;
        tag.querySelector('.remove-btn').onclick=()=>{
          row.fileName=null;
          delete state.files[id];
          tag.remove();
          fi.value='';
        };
        wrap.appendChild(tag);
      }
    });
    wrap.appendChild(zone);
    if(row.fileName){
      const tag=document.createElement('div');
      tag.className='uploaded-file';
      tag.innerHTML=`<i class="ti ti-file" aria-hidden="true"></i> ${row.fileName.length>26?row.fileName.slice(0,23)+'...':row.fileName}`;
      wrap.appendChild(tag);
    }
    cell.appendChild(wrap);
  } else if(row.physical==='No'){
    const p=document.createElement('div');
    p.className='no-doc';
    p.innerHTML='<i class="ti ti-info-circle" aria-hidden="true"></i> No physical document available';
    cell.appendChild(p);
  }
}

function renderTable(){
  const tbody=document.getElementById('doc-tbody');
  tbody.innerHTML='';
  state.rows.forEach(row=>{
    const opt=ALL_OPTIONS.find(o=>o.value===row.value);
    const tr=document.createElement('tr');

    const tdName=document.createElement('td');
    const sel=document.createElement('select');
    sel.id='docsel-'+row.id;
    sel.style.fontSize='12px';
    sel.style.padding='5px 7px';
    const placeholder=document.createElement('option');
    placeholder.value='';
    placeholder.textContent='— Select Document —';
    sel.appendChild(placeholder);
    getAvailableOptions(row.value).forEach(o=>{
      const op=document.createElement('option');
      op.value=o.value;
      op.textContent=o.label;
      if(o.value===row.value) op.selected=true;
      sel.appendChild(op);
    });
    sel.addEventListener('change',()=>onDocChange(row.id,sel));
    tdName.appendChild(sel);
    if(opt&&opt.desc){
      const d=document.createElement('div');
      d.className='doc-desc';
      d.style.marginTop='3px';
      d.textContent=opt.desc;
      tdName.appendChild(d);
    }

    const tdPhys=document.createElement('td');
    if(row.value){
      const rg=document.createElement('div');
      rg.className='radio-group';
      ['Yes','No'].forEach(v=>{
        const lbl=document.createElement('label');
        lbl.className='radio-label';
        const inp=document.createElement('input');
        inp.type='radio';
        inp.name='phys_'+row.id;
        inp.value=v;
        if(row.physical===v) inp.checked=true;
        inp.addEventListener('change',()=>onPhysicalChange(row.id,v));
        lbl.appendChild(inp);
        lbl.appendChild(document.createTextNode(' '+v));
        rg.appendChild(lbl);
      });
      tdPhys.appendChild(rg);
    } else {
      tdPhys.innerHTML='<span style="color:var(--color-text-tertiary);font-size:11px">— Select doc first —</span>';
    }

    const tdUpload=document.createElement('td');
    tdUpload.id='upload-'+row.id;
    if(row.physical==='Yes'){
      const wrap=document.createElement('div');
      const zone=document.createElement('div');
      zone.className='upload-zone';
      zone.innerHTML='<input type="file" accept=".pdf"><div class="uz-text"><i class="ti ti-upload" aria-hidden="true"></i> Click to upload PDF</div><div class="uz-hint">Max 5 MB | PDF only</div>';
      const fi=zone.querySelector('input');
      fi.addEventListener('change',()=>{
        const f=fi.files[0];
        if(f){
          row.fileName=f.name;
          state.files[row.id]=f.name;
          const old=wrap.querySelector('.uploaded-file');
          if(old) old.remove();
          const tag=document.createElement('div');
          tag.className='uploaded-file';
          tag.innerHTML=`<i class="ti ti-file" aria-hidden="true"></i> ${f.name.length>26?f.name.slice(0,23)+'...':f.name} <button class="remove-btn" title="Remove file" aria-label="Remove file">✕</button>`;
          tag.querySelector('.remove-btn').onclick=()=>{
            row.fileName=null;
            delete state.files[row.id];
            tag.remove();
            fi.value='';
          };
          wrap.appendChild(tag);
        }
      });
      wrap.appendChild(zone);
      if(row.fileName){
        const tag=document.createElement('div');
        tag.className='uploaded-file';
        tag.innerHTML=`<i class="ti ti-file" aria-hidden="true"></i> ${row.fileName.length>26?row.fileName.slice(0,23)+'...':row.fileName}`;
        wrap.appendChild(tag);
      }
      tdUpload.appendChild(wrap);
    } else if(row.physical==='No'){
      tdUpload.innerHTML='<div class="no-doc"><i class="ti ti-info-circle" aria-hidden="true"></i> No physical document available</div>';
    } else {
      tdUpload.innerHTML='<span style="color:var(--color-text-tertiary);font-size:11px">—</span>';
    }

    const tdAction=document.createElement('td');
    tdAction.style.textAlign='center';
    const del=document.createElement('button');
    del.className='btn-danger-sm';
    del.title='Remove row';
    del.setAttribute('aria-label','Remove this row');
    del.innerHTML='<i class="ti ti-x" aria-hidden="true"></i>';
    del.onclick=()=>removeRow(row.id);
    tdAction.appendChild(del);

    tr.appendChild(tdName);
    tr.appendChild(tdPhys);
    tr.appendChild(tdUpload);
    tr.appendChild(tdAction);
    tbody.appendChild(tr);
  });

  const available=ALL_OPTIONS.filter(o=>!state.usedValues.has(o.value));
  const btn=document.getElementById('add-row-btn');
  const info=document.getElementById('row-info');
  if(available.length===0){
    btn.disabled=true;
    btn.innerHTML='<i class="ti ti-check" aria-hidden="true"></i> All documents added';
  } else {
    btn.disabled=false;
    btn.innerHTML='<i class="ti ti-plus" aria-hidden="true"></i> Add Document Record';
  }
  if(state.rows.length===0){
    info.textContent='No records added yet. Click "Add Document Record" to begin.';
  } else {
    info.textContent=`${state.rows.length} record(s) added | ${available.length} option(s) remaining`;
  }
}

function resetForm(){
  if(!confirm('Reset all data? This cannot be undone.')) return;
  document.getElementById('mandal-select').value='';
  document.getElementById('village-select').value='';
  document.getElementById('village-select').disabled=true;
  state.rows=[];
  state.usedValues=new Set();
  state.files={};
  renderTable();
}

function submitForm(){
  const mandal=document.getElementById('mandal-select').value;
  const village=document.getElementById('village-select').value;
  if(!mandal||!village){
    alert('Please select both Mandal and Village before submitting.');
    return;
  }
  const toast=document.getElementById('toast');
  toast.classList.add('show');
  setTimeout(()=>toast.classList.remove('show'),4000);
}

renderTable();
</script>

</body>
</html>