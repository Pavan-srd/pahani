const villageData={hayathnagar:['Hayathnagar','Saroornagar','Vanasthalipuram','Bandlaguda Jagir','Pedda Amberpet'],ibrahimpatnam:['Ibrahimpatnam','Manchal','Pedda Gollagudem','Yacharam','Turkapally'],keesara:['Keesara','Cherlapally','Kushaiguda','Ghatkesar','Boduppal'],malkajgiri:['Malkajgiri','Uppal','Kapra','Neredmet','Medipally'],medchal:['Medchal','Kompally','Nagaram','Bollaram','Dundigal'],rajendranagar:['Rajendranagar','Budwel','Jilakarragudem','Gandipet','Narsingi'],shamshabad:['Shamshabad','Kothur','Farooqnagar','Chevella Road','Shadnagar'],shankarpally:['Shankarpally','Mokila','Ameenpur','Sultanpur','Gummadidala'],chevella:['Chevella','Marpally','Pudur','Vikarabad','Donthanpally'],vikarabad:['Vikarabad','Tandur','Kodangal','Marpally','Nawabpet'],sangareddy:['Arutla','Byathole','Cheriyal','Chidruppa','Chintalpalle','Edthanur','Fasalwadi','Indrakaran','Irigipalle','Ismailkhanpet','Julkal','Kalabgoor','Kalvemula','Kandi','Kasipur','Kothlapur','Koulampet','Makthaalloor','Mamidipalle','Mohd.Shapur','Nagapur','Tadlapalle','Topgonda','Utharpalle']};

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