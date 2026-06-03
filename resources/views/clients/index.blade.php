<x-layouts.app title="Clients">
@push('styles')
<link rel="stylesheet" href="{{asset('intl_tel_input/intlTelInput.min.css')}}">
<style>
  /* intl-tel-input fit into our form field */
  .iti{width:100%;}
  .iti__country-list, .iti__country, .iti__dial-code, .iti__country-name, .iti__selected-dial-code{font-size:13px;}
  .iti__flag{background-image:url("{{asset('intl_tel_input/img/flags.png')}}");}

  .iti--container
  {
    width:410px !important;
  }
  .iti__country-list{
    width:400px !important;
  }
  
  @media (-webkit-min-device-pixel-ratio:2),(min-resolution:192dpi){.iti__flag{background-image:url("{{asset('intl_tel_input/img/flags@2x.png')}}");}}
  .cl-content{padding:24px 32px 48px;min-width:0;}
  .page-head{display:flex;align-items:center;gap:14px;margin-bottom:20px;flex-wrap:wrap;}
  .page-title{font-size:26px;font-weight:600;letter-spacing:-.6px;color:var(--text-1);}
  .page-count{font-size:13px;color:var(--text-3);font-weight:500;}
  .head-spacer{flex:1;}
  .btn{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:var(--radius-sm);font-family:inherit;font-size:13px;font-weight:500;cursor:pointer;border:1px solid transparent;text-decoration:none;}
  .btn svg{width:15px;height:15px;stroke:currentColor;stroke-width:2;fill:none;}
  .btn-primary{background:var(--brand-red);color:#fff;}
  .btn-primary:hover{background:var(--brand-red-dark);}
  .btn-secondary{background:var(--bg-card);color:var(--text-1);border-color:var(--border);}
  .btn-secondary:hover{border-color:var(--text-3);}

  /* Toolbar */
  .toolbar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:14px;}
  .search{position:relative;}
  .search>svg{position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;stroke:var(--text-3);stroke-width:1.8;fill:none;}
  .search input{height:36px;border:1px solid var(--border);border-radius:var(--radius-sm);padding:0 12px 0 32px;font-family:inherit;font-size:13px;color:var(--text-1);background:var(--bg-card);outline:none;width:280px;max-width:100%;}
  .search input:focus{border-color:var(--brand-red);box-shadow:0 0 0 3px var(--brand-red-soft);}
  .search input::placeholder{color:var(--text-3);}

  /* Table */
  .table-wrap{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;}
  .cl-table{width:100%;border-collapse:collapse;font-size:13px;}
  .cl-table thead th{text-align:left;font-size:10px;font-weight:600;color:var(--text-3);letter-spacing:.6px;text-transform:uppercase;padding:12px 16px;border-bottom:1px solid var(--border);white-space:nowrap;background:var(--bg-neutral);}
  .cl-table tbody tr{border-bottom:1px solid var(--border-soft);}
  .cl-table tbody tr:last-child{border-bottom:none;}
  .cl-table tbody tr:hover{background:var(--bg-neutral);}
  .cl-table td{padding:13px 16px;color:var(--text-2);vertical-align:middle;}
  .cl-table td.primary{color:var(--text-1);font-weight:500;}
  .cl-name{display:flex;align-items:center;gap:9px;}
  .cl-av{width:28px;height:28px;border-radius:50%;background:var(--brand-red-soft);color:var(--brand-red-dark);display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:600;flex-shrink:0;}
  .cl-mono{font-family:var(--font-num);font-variant-numeric:tabular-nums;letter-spacing:-.01em;}
  .cl-actions{display:flex;gap:6px;justify-content:flex-end;}
  .icon-btn{width:30px;height:30px;display:inline-flex;align-items:center;justify-content:center;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-card);color:var(--text-2);cursor:pointer;}
  .icon-btn:hover{background:var(--bg-neutral);color:var(--text-1);}
  .icon-btn.danger:hover{color:var(--brand-red);border-color:var(--brand-red-border);background:var(--brand-red-soft);}
  .icon-btn svg{width:15px;height:15px;stroke:currentColor;stroke-width:1.9;fill:none;stroke-linecap:round;stroke-linejoin:round;}
  .table-foot{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-top:1px solid var(--border);font-size:12.5px;color:var(--text-3);}

  /* Empty */
  .empty{display:none;flex-direction:column;align-items:center;justify-content:center;padding:64px 20px;text-align:center;}
  .empty.show{display:flex;}
  .empty .ring{width:64px;height:64px;border-radius:50%;border:1.5px solid var(--border);display:flex;align-items:center;justify-content:center;margin-bottom:16px;color:var(--text-3);}
  .empty .ring svg{width:26px;height:26px;stroke:currentColor;stroke-width:1.6;fill:none;}
  .empty h3{font-size:16px;font-weight:600;color:var(--text-1);margin-bottom:4px;}
  .empty p{font-size:13px;color:var(--text-3);margin-bottom:18px;}

  /* Modal */
  .modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);z-index:500;display:none;align-items:center;justify-content:center;padding:16px;}
  .modal-backdrop.open{display:flex;}
  .modal-box{background:var(--bg-card);border-radius:var(--radius-lg);border:1px solid var(--border);box-shadow:0 25px 50px -12px rgba(0,0,0,.25);width:100%;max-width:460px;max-height:calc(100vh - 32px);display:flex;flex-direction:column;}
  .modal-head{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;}
  .modal-head h3{font-size:16px;font-weight:600;color:var(--text-1);margin:0;}
  .modal-close{background:none;border:none;cursor:pointer;color:var(--text-2);width:28px;height:28px;display:flex;align-items:center;justify-content:center;border-radius:4px;font-size:20px;line-height:1;}
  .modal-close:hover{background:var(--bg-neutral);color:var(--text-1);}
  .modal-body{padding:20px;overflow-y:auto;}
  .modal-foot{padding:12px 20px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px;}
  .f-group{margin-bottom:14px;}
  .f-label{display:block;font-size:12px;font-weight:600;color:var(--text-1);margin-bottom:5px;}
  .f-input{width:100%;padding:8px 10px;font-size:13px;font-family:inherit;border:1px solid var(--border);border-radius:6px;background:var(--bg-card);color:var(--text-1);}
  .f-input:focus{outline:none;border-color:var(--brand-red);box-shadow:0 0 0 3px var(--brand-red-soft);}
  .f-err{font-size:11px;color:var(--danger);margin-top:3px;display:none;}
  .phone-row{display:flex;gap:8px;}
  .phone-row .cc{width:84px;flex-shrink:0;}

  /* Toast */
  .toast-stack{position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px;}
  .toast{padding:10px 16px;border-radius:6px;font-size:13px;font-weight:500;box-shadow:0 4px 12px rgba(0,0,0,.12);}
  .toast-ok{background:var(--success-soft);color:var(--success);border:1px solid var(--success-border);}
  .toast-err{background:var(--brand-red-soft);color:var(--brand-red-dark);border:1px solid var(--brand-red-border);}

  @media(max-width:768px){.cl-content{padding:16px;}.search input{width:100%;}.search{flex:1;}}
</style>
@endpush

<main class="cl-content">
  <div class="page-head">
    <span class="page-title">Clients</span>
    <span class="page-count num" id="count">0 clients</span>
    <div class="head-spacer"></div>
    <div class="search">
      <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input id="search" type="text" placeholder="Search clients…">
    </div>
    <button class="btn btn-primary" onclick="openCreate()"><svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>Add Client</button>
  </div>

  <div class="table-wrap" id="tableWrap">
    <table class="cl-table">
      <thead>
        <tr>
          <th>Business ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Mobile</th>
          <th style="text-align:right;width:90px;">Actions</th>
        </tr>
      </thead>
      <tbody id="tbody"></tbody>
    </table>
    <div class="empty" id="empty">
      <div class="ring"><svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/></svg></div>
      <h3>No clients found</h3>
      <p>Add your first client to get started.</p>
      <button class="btn btn-primary" onclick="openCreate()"><svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>Add Client</button>
    </div>
    <div class="table-foot" id="foot"></div>
  </div>
</main>

{{-- Add / Edit modal --}}
<div class="modal-backdrop" id="clientModal">
  <div class="modal-box">
    <div class="modal-head">
      <h3 id="cm_heading">Add Client</h3>
      <button class="modal-close" onclick="closeModal()">×</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="cm_id">
      <div class="f-group">
        <label class="f-label">Client Business ID</label>
        <input type="text" id="cm_business_id" class="f-input" placeholder="e.g. BIZ-1001">
      </div>
      <div class="f-group">
        <label class="f-label">Name *</label>
        <input type="text" id="cm_name" class="f-input" placeholder="Client name">
        <div class="f-err" id="cm_name_err">Name is required</div>
      </div>
      <div class="f-group">
        <label class="f-label">Email</label>
        <input type="email" id="cm_email" class="f-input" placeholder="name@example.com">
      </div>
      <div class="f-group">
        <label class="f-label">Mobile</label>
        <input type="tel" id="cm_mobile" class="f-input" placeholder="Mobile number">
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
      <button class="btn btn-primary" id="cm_submit" onclick="submitClient()">Save</button>
    </div>
  </div>
</div>

{{-- Delete confirmation modal --}}
<div class="modal-backdrop" id="confirmModal">
  <div class="modal-box" style="max-width:400px;">
    <div class="modal-head">
      <h3>Delete Client</h3>
      <button class="modal-close" onclick="closeConfirm()">×</button>
    </div>
    <div class="modal-body">
      <p style="font-size:13.5px;color:var(--text-2);line-height:1.55;margin:0;">Are you sure you want to delete <strong id="dc_name" style="color:var(--text-1);"></strong>? This action cannot be undone.</p>
    </div>
    <div class="modal-foot">
      <button class="btn btn-secondary" onclick="closeConfirm()">Cancel</button>
      <button class="btn btn-primary" id="dc_confirm" onclick="confirmDelete()">Delete</button>
    </div>
  </div>
</div>

<div class="toast-stack" id="toasts"></div>

<script src="{{asset('intl_tel_input/intlTelInput.min.js')}}"></script>
<script>
const CSRF       = '{{ csrf_token() }}';
const STORE_URL  = '{{ route('clients.store') }}';
let clients      = @json($clients);
let state        = { search:'' };

/* ── intl-tel-input on the mobile field ── */
const mobileInput = document.getElementById('cm_mobile');
const iti = window.intlTelInput(mobileInput, {
  initialCountry: 'in',
  separateDialCode: true,
  preferredCountries: ['in', 'us', 'gb', 'ae'],
  dropdownContainer: document.body,
  utilsScript: "{{asset('intl_tel_input/utils.js')}}",
});
function getDialCode(){ return '+' + (iti.getSelectedCountryData().dialCode || '91'); }
function setPhone(countryCode, mobile){
  const dial = (countryCode || '+91').replace('+','');
  const list = window.intlTelInputGlobals.getCountryData();
  const match = list.find(c => c.dialCode === dial);
  iti.setCountry(match ? match.iso2 : 'in');
  mobileInput.value = mobile || '';
}

function esc(s){const d=document.createElement('div');d.textContent=s==null?'':s;return d.innerHTML;}
function initials(n){return (n||'?').split(' ').map(w=>w[0]).slice(0,2).join('').toUpperCase();}
function toast(msg,type='ok'){const el=document.createElement('div');el.className='toast toast-'+type;el.textContent=msg;document.getElementById('toasts').appendChild(el);setTimeout(()=>el.remove(),3000);}
function openModal(){document.getElementById('clientModal').classList.add('open');}
function closeModal(){document.getElementById('clientModal').classList.remove('open');}
async function req(url,method='GET',body=null){const opt={method,headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}};if(body){opt.headers['Content-Type']='application/json';opt.body=JSON.stringify(body);}const r=await fetch(url,opt);return r.json();}

function filtered(){
  const s=state.search;
  if(!s) return clients;
  return clients.filter(c=>
    (c.business_id||'').toLowerCase().includes(s) ||
    (c.name||'').toLowerCase().includes(s) ||
    (c.email||'').toLowerCase().includes(s) ||
    ((c.country_code||'')+(c.mobile||'')).toLowerCase().includes(s)
  );
}

function render(){
  const rows=filtered();
  document.getElementById('count').textContent=`${rows.length} client${rows.length!==1?'s':''}`;
  const tbody=document.getElementById('tbody');
  tbody.innerHTML=rows.map(c=>{
    const phone=(c.country_code||'')+(c.mobile?(' '+c.mobile):'');
    return `<tr>
      <td class="cl-mono">${c.business_id?esc(c.business_id):'<span style="color:var(--text-3)">—</span>'}</td>
      <td class="primary"><span class="cl-name"><span class="cl-av">${initials(c.name)}</span>${esc(c.name)}</span></td>
      <td>${c.email?esc(c.email):'<span style="color:var(--text-3)">—</span>'}</td>
      <td class="cl-mono">${c.mobile?esc(phone):'<span style="color:var(--text-3)">—</span>'}</td>
      <td>
        <div class="cl-actions">
          <button class="icon-btn" title="Edit" onclick='editClient(${JSON.stringify(c)})'><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
          <button class="icon-btn danger" title="Delete" onclick="deleteClient(${c.id})"><svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg></button>
        </div>
      </td>
    </tr>`;
  }).join('');
  document.getElementById('empty').classList.toggle('show', rows.length===0);
  document.querySelector('.cl-table').style.display = rows.length===0 ? 'none' : 'table';
  document.getElementById('foot').style.display = rows.length===0 ? 'none' : 'flex';
  document.getElementById('foot').textContent = rows.length ? `Showing ${rows.length} of ${clients.length}` : '';
}

document.getElementById('search').addEventListener('input',e=>{state.search=e.target.value.trim().toLowerCase();render();});

function openCreate(){
  document.getElementById('cm_heading').textContent='Add Client';
  document.getElementById('cm_id').value='';
  document.getElementById('cm_business_id').value='';
  document.getElementById('cm_name').value='';
  document.getElementById('cm_email').value='';
  setPhone('+91','');
  document.getElementById('cm_name_err').style.display='none';
  openModal();
  setTimeout(()=>document.getElementById('cm_name').focus(),100);
}

function editClient(c){
  document.getElementById('cm_heading').textContent='Edit Client';
  document.getElementById('cm_id').value=c.id;
  document.getElementById('cm_business_id').value=c.business_id||'';
  document.getElementById('cm_name').value=c.name||'';
  document.getElementById('cm_email').value=c.email||'';
  setPhone(c.country_code, c.mobile);
  document.getElementById('cm_name_err').style.display='none';
  openModal();
}

async function submitClient(){
  const id=document.getElementById('cm_id').value;
  const name=document.getElementById('cm_name').value.trim();
  if(!name){document.getElementById('cm_name_err').style.display='block';document.getElementById('cm_name').focus();return;}
  const mob = mobileInput.value.trim();
  const payload={
    business_id: document.getElementById('cm_business_id').value.trim()||null,
    name,
    email: document.getElementById('cm_email').value.trim()||null,
    country_code: getDialCode(),
    mobile: mob || null,
  };
  const btn=document.getElementById('cm_submit');btn.disabled=true;btn.textContent='Saving...';
  const r = id ? await req(`/clients/${id}`,'PUT',payload) : await req(STORE_URL,'POST',payload);
  btn.disabled=false;btn.textContent='Save';
  if(!r.ok){toast(r.message||'Failed to save','err');return;}
  if(id){ const i=clients.findIndex(x=>x.id==id); if(i>-1) clients[i]=r.client; toast('Client updated'); }
  else { clients.push(r.client); toast('Client added'); }
  clients.sort((a,b)=>(a.name||'').localeCompare(b.name||''));
  closeModal(); render();
}

let pendingDeleteId = null;
function deleteClient(id){
  const c=clients.find(x=>x.id===id);
  pendingDeleteId = id;
  document.getElementById('dc_name').textContent = c ? c.name : 'this client';
  document.getElementById('confirmModal').classList.add('open');
}
function closeConfirm(){
  document.getElementById('confirmModal').classList.remove('open');
  pendingDeleteId = null;
}
async function confirmDelete(){
  if(!pendingDeleteId) return;
  const id = pendingDeleteId;
  const btn=document.getElementById('dc_confirm'); btn.disabled=true; btn.textContent='Deleting...';
  const r=await req(`/clients/${id}`,'DELETE');
  btn.disabled=false; btn.textContent='Delete';
  if(!r.ok){toast(r.message||'Failed to delete','err'); return;}
  clients=clients.filter(x=>x.id!==id);
  closeConfirm();
  toast('Client deleted'); render();
}

render();
</script>
</x-layouts.app>
