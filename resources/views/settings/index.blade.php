<x-layouts.app title="Settings">

@push('styles')
<style>
/* ── Settings Page ── */
.st-page { padding:24px; animation:st-fadein .3s ease; }
@keyframes st-fadein { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:none} }

.st-max { max-width:800px; /*margin:0 auto;*/ }

.st-header { margin-bottom:28px; padding-bottom:16px; border-bottom:1px solid #e2e8f0; }
.st-title   { font-size:1.5rem; font-weight:700; color:#0f172a; letter-spacing:-.02em; }
.st-sub     { font-size:.8rem; color:#64748b; margin-top:3px; }

/* Section card */
.st-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:24px; margin-bottom:20px; box-shadow:0 1px 3px rgba(0,0,0,.04); transition:box-shadow .2s; }
.st-card:hover { box-shadow:0 4px 12px rgba(0,0,0,.07); }

/* Section title */
.st-section-title { font-size:1rem; font-weight:600; color:#0f172a; margin-bottom:20px; padding-bottom:12px; border-bottom:2px solid rgba(20,184,166,.2); display:flex; align-items:center; gap:8px; }
.st-section-title::before { content:''; width:4px; height:20px; background:#14b8a6; border-radius:2px; flex-shrink:0; }

/* Setting row */
.st-row { display:flex; justify-content:space-between; align-items:center; padding:16px 0; border-bottom:1px solid #e2e8f0; gap:20px; }
.st-row:last-child  { border-bottom:none; padding-bottom:0; }
.st-row:first-child { padding-top:0; }
.st-row-info { flex:1; min-width:0; }
.st-label { font-size:.9rem; font-weight:600; color:#0f172a; margin-bottom:3px; }
.st-desc  { font-size:.78rem; color:#64748b; line-height:1.5; }
.st-control { flex-shrink:0; }

/* Inputs */
.st-input { min-width:220px; padding:9px 13px; border:1px solid #e2e8f0; border-radius:8px; font-family:inherit; font-size:.875rem; background:#fff; transition:all .2s; }
.st-input:focus { outline:none; border-color:#14b8a6; box-shadow:0 0 0 3px rgba(20,184,166,.12); }
.st-input:hover { border-color:rgba(20,184,166,.5); }

/* Toggle switch */
.st-toggle { position:relative; display:inline-block; width:48px; height:26px; cursor:pointer; flex-shrink:0; }
.st-toggle input { opacity:0; width:0; height:0; }
.st-toggle-slider { position:absolute; inset:0; background:#cbd5e1; border-radius:26px; transition:all .3s; }
.st-toggle-slider::before { content:''; position:absolute; height:20px; width:20px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:all .3s; box-shadow:0 1px 4px rgba(0,0,0,.2); }
.st-toggle input:checked + .st-toggle-slider { background:#14b8a6; }
.st-toggle input:checked + .st-toggle-slider::before { transform:translateX(22px); }
.st-toggle:hover .st-toggle-slider { filter:brightness(.95); }

/* Buttons */
.st-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:8px; font-family:inherit; font-size:.82rem; font-weight:500; cursor:pointer; transition:all .15s; text-decoration:none; border:1px solid #e2e8f0; background:#f8fafc; color:#374151; }
.st-btn:hover { background:#fff; border-color:#14b8a6; color:#0f172a; }
.st-btn-primary { background:#14b8a6; color:#fff; border-color:#14b8a6; }
.st-btn-primary:hover { background:#0d9488; }

/* Badge pills */
.st-badge { display:inline-flex; align-items:center; padding:4px 12px; border-radius:9999px; font-size:.75rem; font-weight:600; background:#f1f5f9; border:1px solid #e2e8f0; color:#475569; transition:all .15s; cursor:default; }
.st-badge:hover { background:rgba(20,184,166,.08); border-color:#14b8a6; color:#0f766e; }
.st-badges { display:flex; gap:10px; flex-wrap:wrap; align-items:center; }

/* ── Login History Modal ── */
.st-overlay { position:fixed; inset:0; background:rgba(15,23,42,.4); backdrop-filter:blur(4px); z-index:2000; display:none; align-items:center; justify-content:center; padding:16px; opacity:0; transition:opacity .2s; }
.st-overlay.active { display:flex; opacity:1; }
.st-modal { background:#fff; border-radius:14px; width:100%; max-width:640px; max-height:82vh; display:flex; flex-direction:column; box-shadow:0 20px 60px rgba(0,0,0,.2); transform:scale(.97) translateY(-6px); transition:transform .2s; overflow:hidden; }
.st-overlay.active .st-modal { transform:none; }
.st-modal-head { display:flex; justify-content:space-between; align-items:center; padding:18px 22px; border-bottom:1px solid #e2e8f0; flex-shrink:0; }
.st-modal-title { font-size:1rem; font-weight:700; color:#0f172a; }
.st-modal-close { width:28px; height:28px; border:none; background:#f1f5f9; border-radius:6px; color:#64748b; font-size:1.1rem; cursor:pointer; display:flex; align-items:center; justify-content:center; transition:background .15s; }
.st-modal-close:hover { background:#e2e8f0; }
.st-modal-body { overflow-y:auto; flex:1; }

/* History table */
.st-table { width:100%; border-collapse:collapse; font-size:.84rem; }
.st-table thead th { padding:11px 16px; text-align:left; font-size:.72rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#64748b; background:#f8fafc; position:sticky; top:0; border-bottom:1px solid #e2e8f0; }
.st-table tbody td { padding:11px 16px; border-bottom:1px solid #f1f5f9; color:#374151; }
.st-table tbody tr:last-child td { border-bottom:none; }
.st-table tbody tr:hover td { background:rgba(20,184,166,.03); }

/* Skeleton */
.st-skel { background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%); background-size:200% 100%; animation:st-shimmer 1.4s infinite; border-radius:12px; }
@keyframes st-shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* Toast */
.st-toasts { position:fixed; top:18px; right:18px; z-index:4000; display:flex; flex-direction:column; gap:8px; pointer-events:none; }
.st-toast { padding:11px 18px; border-radius:8px; font-size:.82rem; font-weight:500; color:#fff; box-shadow:0 4px 14px rgba(0,0,0,.18); animation:st-tin .25s ease; pointer-events:auto; }
@keyframes st-tin { from{opacity:0;transform:translateX(20px)} to{opacity:1;transform:none} }
.st-toast-success { background:#14b8a6; }
.st-toast-error   { background:#ef4444; }
</style>
@endpush

<div class="st-page">
<div class="st-max">

    {{-- Header --}}
    <div class="st-header">
        <div class="st-title">Settings</div>
        <div class="st-sub">Configure application preferences and integrations</div>
    </div>

    {{-- Settings Container --}}
    <div id="stContainer">
        <div class="st-card st-skel" style="height:200px"></div>
        <div class="st-card st-skel" style="height:160px;margin-bottom:20px"></div>
    </div>

</div>
</div>

{{-- Login History Modal --}}
<div class="st-overlay" id="stHistoryModal">
    <div class="st-modal">
        <div class="st-modal-head">
            <div class="st-modal-title">Login History</div>
            <button class="st-modal-close" onclick="stCloseModal()">×</button>
        </div>
        <div class="st-modal-body">
            <div id="stHistoryBody" style="padding:16px 0">
                <div style="padding:0 16px"><div class="st-skel" style="height:14px;margin-bottom:8px"></div><div class="st-skel" style="height:14px;width:80%;margin-bottom:8px"></div></div>
            </div>
        </div>
    </div>
</div>

<div class="st-toasts" id="stToasts"></div>

<script>
const ST_CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

// ── Utilities ──────────────────────────────────────────────────────
function stEsc(s){ const d=document.createElement('div'); d.textContent=s||''; return d.innerHTML; }
function stToast(msg, type='success'){
    const t=document.createElement('div'); t.className=`st-toast st-toast-${type}`; t.textContent=msg;
    document.getElementById('stToasts').appendChild(t); setTimeout(()=>t.remove(),3000);
}
function stOpenModal()  { document.getElementById('stHistoryModal').classList.add('active'); }
function stCloseModal() { document.getElementById('stHistoryModal').classList.remove('active'); }

async function stGet(url){ const r=await fetch(url,{headers:{Accept:'application/json','X-CSRF-TOKEN':ST_CSRF}}); return r.json(); }
async function stPost(url,data){ const r=await fetch(url,{method:'POST',headers:{Accept:'application/json','Content-Type':'application/json','X-CSRF-TOKEN':ST_CSRF},body:JSON.stringify(data)}); return r.json(); }

// ── Save a setting ────────────────────────────────────────────────
async function stSave(key, value){
    const r = await stPost('/settings/api/update', {key, value}).catch(()=>({ok:false}));
    if(r.ok) stToast('Saved');
    else     stToast(r.error||'Failed to save','error');
}

// ── Builder helpers ───────────────────────────────────────────────
function stText(key, label, desc, value, type='text'){
    return `<div class="st-row">
        <div class="st-row-info">
            <div class="st-label">${stEsc(label)}</div>
            ${desc ? `<div class="st-desc">${stEsc(desc)}</div>` : ''}
        </div>
        <div class="st-control">
            <input type="${type}" class="st-input" value="${stEsc(value||'')}"
                onchange="stSave('${key}',this.value)"
                style="min-width:${type==='time'?'130px':'220px'}">
        </div>
    </div>`;
}

function stSelect(key, label, desc, value, options){
    const opts = options.map(o => `<option value="${o.v}" ${value===o.v?'selected':''}>${stEsc(o.l)}</option>`).join('');
    return `<div class="st-row">
        <div class="st-row-info">
            <div class="st-label">${stEsc(label)}</div>
            ${desc ? `<div class="st-desc">${stEsc(desc)}</div>` : ''}
        </div>
        <div class="st-control">
            <select class="st-input" style="min-width:160px" onchange="stSave('${key}',this.value)">${opts}</select>
        </div>
    </div>`;
}

function stToggle(key, label, desc, value){
    const checked = value === '1' || value === true || value === 1;
    return `<div class="st-row">
        <div class="st-row-info">
            <div class="st-label">${stEsc(label)}</div>
            ${desc ? `<div class="st-desc">${stEsc(desc)}</div>` : ''}
        </div>
        <div class="st-control">
            <label class="st-toggle">
                <input type="checkbox" ${checked?'checked':''} onchange="stSave('${key}',this.checked?'1':'0')">
                <span class="st-toggle-slider"></span>
            </label>
        </div>
    </div>`;
}

// ── Load settings ─────────────────────────────────────────────────
async function stLoad(){
    const data = await stGet('/settings/api/get').catch(()=>({ok:false}));
    if(!data.ok){ document.getElementById('stContainer').innerHTML='<div style="padding:40px;text-align:center;color:#ef4444">Failed to load settings</div>'; return; }

    const s     = data.settings || {};
    const stats = s._stats      || {};

    document.getElementById('stContainer').innerHTML = `

        {{-- General --}}
        <div class="st-card">
            <div class="st-section-title">General</div>
            ${stText('company_name', 'Company Name', '', s.company_name)}
            ${stText('app_name',     'App Name',     '', s.app_name)}
            ${stText('timezone',     'Timezone',     '', s.timezone)}
        </div>

        {{-- Tasks --}}
        <div class="st-card">
            <div class="st-section-title">Tasks</div>
            ${stSelect('default_priority', 'Default Priority', '', s.default_priority, [
                {v:'low',l:'Low'},{v:'normal',l:'Normal'},{v:'high',l:'High'},{v:'urgent',l:'Urgent'}
            ])}
            ${stText('auto_archive_days', 'Auto-archive After (days)', 'Completed tasks archived after this many days', s.auto_archive_days, 'number')}
        </div>

        {{-- Reports --}}
        <div class="st-card">
            <div class="st-section-title">Reports</div>
            ${stText('report_deadline', 'Submission Deadline', '24h format (e.g. 19:00)', s.report_deadline, 'time')}
            ${stToggle('weekend_reports', 'Weekend Reports', 'Require reports on weekends', s.weekend_reports)}
        </div>

        {{-- Notifications --}}
        <div class="st-card">
            <div class="st-section-title">Notifications</div>
            ${stText('webhook_url', 'Webhook URL', 'For Ops bot integration', s.webhook_url, 'url')}
            ${stToggle('notify_task_created',     'Task Created',      '', s.notify_task_created)}
            ${stToggle('notify_task_completed',   'Task Completed',    '', s.notify_task_completed)}
            ${stToggle('notify_task_overdue',     'Task Overdue',      '', s.notify_task_overdue)}
            ${stToggle('notify_report_submitted', 'Report Submitted',  '', s.notify_report_submitted)}
            ${stToggle('notify_report_missing',   'Report Missing',    '', s.notify_report_missing)}
        </div>

        {{-- Security --}}
        <div class="st-card">
            <div class="st-section-title">Security</div>
            ${stText('session_timeout', 'Session Timeout (seconds)', 'Default: 86400 (24 hours)', s.session_timeout, 'number')}
            <div class="st-row">
                <div class="st-row-info">
                    <div class="st-label">Login History</div>
                    <div class="st-desc">Recent login attempts across all staff</div>
                </div>
                <div class="st-control">
                    <button class="st-btn" onclick="stShowHistory()">View</button>
                </div>
            </div>
        </div>

        {{-- Data --}}
        <div class="st-card">
            <div class="st-section-title">Data</div>
            <div class="st-row">
                <div class="st-row-info"><div class="st-label">Database Stats</div></div>
                <div class="st-badges">
                    <span class="st-badge">${stats.total_tasks||0} tasks</span>
                    <span class="st-badge">${stats.total_reports||0} reports</span>
                    <span class="st-badge">${stats.total_staff||0} staff</span>
                    <span class="st-badge">${stats.total_comments||0} comments</span>
                    <span class="st-badge">${stats.db_size||'—'}</span>
                </div>
            </div>
            <div class="st-row">
                <div class="st-row-info">
                    <div class="st-label">Export All Data</div>
                    <div class="st-desc">Full JSON export of all tasks, reports, staff and settings</div>
                </div>
                <div class="st-control">
                    <a href="/settings/export/data" class="st-btn" download>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download JSON
                    </a>
                </div>
            </div>
            <div class="st-row">
                <div class="st-row-info">
                    <div class="st-label">Export Reports</div>
                    <div class="st-desc">CSV export of all submitted daily reports</div>
                </div>
                <div class="st-control">
                    <a href="/settings/export/reports" class="st-btn" download>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download CSV
                    </a>
                </div>
            </div>
        </div>
    `;
}

// ── Login history ─────────────────────────────────────────────────
async function stShowHistory(){
    stOpenModal();
    document.getElementById('stHistoryBody').innerHTML = `
        <div style="padding:0 16px 16px">
            <div class="st-skel" style="height:14px;margin-bottom:8px"></div>
            <div class="st-skel" style="height:14px;width:80%;margin-bottom:8px"></div>
            <div class="st-skel" style="height:14px;width:60%"></div>
        </div>`;

    const data = await stGet('/settings/api/login-history').catch(()=>({ok:false}));
    if(!data.ok){ document.getElementById('stHistoryBody').innerHTML='<div style="padding:16px;color:#ef4444">Failed to load</div>'; return; }

    const h = data.history || [];
    if(!h.length){
        document.getElementById('stHistoryBody').innerHTML='<div style="padding:24px;text-align:center;color:#94a3b8">No login history found</div>';
        return;
    }

    document.getElementById('stHistoryBody').innerHTML = `
        <table class="st-table">
            <thead><tr><th>Staff</th><th>IP Address</th><th>Date &amp; Time</th></tr></thead>
            <tbody>${h.map(r => `<tr>
                <td style="font-weight:500">${stEsc(r.staff_name)}</td>
                <td style="font-size:.78rem;color:#64748b;font-family:monospace">${stEsc(r.ip_address||'—')}</td>
                <td style="font-size:.78rem;color:#64748b">${new Date(r.created_at).toLocaleString('en-IN',{day:'numeric',month:'short',year:'numeric',hour:'2-digit',minute:'2-digit'})}</td>
            </tr>`).join('')}</tbody>
        </table>`;
}

// Close modal on overlay click
document.getElementById('stHistoryModal').addEventListener('click', e => {
    if(e.target === document.getElementById('stHistoryModal')) stCloseModal();
});

stLoad();
</script>

</x-layouts.app>
