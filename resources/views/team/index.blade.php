<x-layouts.app title="Team">

@push('styles')
<style>
/* ── Team Page ── */
.tm-page { padding:20px; }

.tm-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; }
.tm-title  { font-size:1.25rem; font-weight:700; color:#0f172a; }
.tm-sub    { font-size:.78rem; color:#64748b; margin-top:2px; }

/* Add button */
.tm-btn-add { display:inline-flex; align-items:center; gap:6px; padding:8px 16px; border-radius:8px; font-size:.82rem; font-weight:600; cursor:pointer; border:none; background:#14b8a6; color:#fff; font-family:inherit; transition:background .15s; }
.tm-btn-add:hover { background:#0d9488; }

/* Filter bar */
.tm-filter { display:flex; align-items:center; gap:10px; margin-bottom:18px; flex-wrap:wrap; }
.tm-filter input { padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:.82rem; font-family:inherit; background:#fff; min-height:36px; min-width:220px; }
.tm-filter input:focus { outline:none; border-color:#14b8a6; }
.tm-filter select { padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:.82rem; font-family:inherit; background:#fff; min-height:36px; cursor:pointer; }

/* Grid */
.tm-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(280px,1fr)); gap:20px; }

/* Card */
.tm-card { background:#fff; border-radius:16px; padding:24px; border:1px solid #e2e8f0; transition:all .3s cubic-bezier(.4,0,.2,1); position:relative; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,.04); }
.tm-card::before { content:''; position:absolute; top:0; left:0; right:0; height:4px; background:linear-gradient(90deg,#14b8a6,#2dd4bf); background-size:200% 100%; animation:tm-gradient 3s ease infinite; opacity:0; transition:opacity .3s; }
@keyframes tm-gradient { 0%,100%{background-position:0 50%} 50%{background-position:100% 50%} }
.tm-card:hover { transform:translateY(-4px); box-shadow:0 12px 40px -12px rgba(20,184,166,.2),0 4px 16px rgba(0,0,0,.08); border-color:rgba(20,184,166,.2); }
.tm-card:hover::before { opacity:1; }
.tm-card.tm-inactive { opacity:.85; background:linear-gradient(135deg,#f8fafc,#f1f5f9); }
.tm-card.tm-inactive::before { background:linear-gradient(90deg,#94a3b8,#64748b); }

/* Status dot */
.tm-status-dot { position:absolute; top:14px; right:14px; width:10px; height:10px; border-radius:50%; }
.tm-status-dot.active   { background:#10b981; box-shadow:0 0 0 3px rgba(16,185,129,.25); animation:tm-pulse 2s infinite; }
.tm-status-dot.inactive { background:#ef4444; }
@keyframes tm-pulse { 0%,100%{box-shadow:0 0 0 3px rgba(16,185,129,.25)} 50%{box-shadow:0 0 0 5px rgba(16,185,129,.15)} }

/* Avatar */
.tm-avatar { width:72px; height:72px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:1.1rem; font-weight:700; background:linear-gradient(135deg,#f0fdfa,rgba(20,184,166,.1)); color:#14b8a6; margin:0 auto 16px; box-shadow:0 0 0 2px #14b8a6,0 4px 12px rgba(20,184,166,.2); transition:all .3s; }
.tm-avatar.inactive { background:linear-gradient(135deg,#f1f5f9,#e2e8f0); color:#94a3b8; box-shadow:0 0 0 2px #94a3b8,0 4px 12px rgba(100,116,139,.15); }
.tm-card:hover .tm-avatar { transform:scale(1.05); box-shadow:0 0 0 3px #14b8a6,0 6px 20px rgba(20,184,166,.3); }

.tm-name { text-align:center; font-size:1.05rem; font-weight:700; color:#0f172a; margin-bottom:8px; }
.tm-card:hover .tm-name { color:#14b8a6; }

/* Role badge */
.tm-role { display:flex; justify-content:center; margin-bottom:16px; }
.tm-badge { display:inline-flex; align-items:center; padding:4px 14px; border-radius:9999px; font-size:.74rem; font-weight:600; text-transform:capitalize; letter-spacing:.02em; }
.tm-badge.role-admin      { background:rgba(139,92,246,.1); color:#6d28d9; border:1px solid rgba(139,92,246,.2); }
.tm-badge.role-secretary  { background:rgba(20,184,166,.1); color:#0f766e; border:1px solid rgba(20,184,166,.2); }
.tm-badge.role-sales_rep  { background:rgba(245,158,11,.1); color:#b45309; border:1px solid rgba(245,158,11,.2); }
.tm-badge.role-support    { background:rgba(14,165,233,.1); color:#0369a1; border:1px solid rgba(14,165,233,.2); }
.tm-badge.role-hr         { background:rgba(236,72,153,.1); color:#be185d; border:1px solid rgba(236,72,153,.2); }
.tm-badge.role-finance    { background:rgba(34,197,94,.1); color:#15803d; border:1px solid rgba(34,197,94,.2); }
.tm-badge.role-developer  { background:rgba(59,130,246,.1); color:#1d4ed8; border:1px solid rgba(59,130,246,.2); }
.tm-badge.role-tester     { background:rgba(168,85,247,.1); color:#7e22ce; border:1px solid rgba(168,85,247,.2); }

/* Stats */
.tm-stats { display:grid; grid-template-columns:repeat(2,1fr); gap:10px; margin-bottom:16px; padding:12px; background:#f8fafc; border-radius:12px; }
.tm-stat { text-align:center; padding:10px 8px; background:#fff; border-radius:10px; transition:all .2s; }
.tm-stat:hover { transform:translateY(-2px); box-shadow:0 4px 12px rgba(0,0,0,.06); }
.tm-stat-value { font-size:1.55rem; font-weight:800; background:linear-gradient(135deg,#14b8a6,#0d9488); -webkit-background-clip:text; -webkit-text-fill-color:transparent; line-height:1.2; }
.tm-inactive .tm-stat-value { background:linear-gradient(135deg,#94a3b8,#64748b); -webkit-background-clip:text; }
.tm-stat-label { font-size:.67rem; color:#64748b; text-transform:uppercase; letter-spacing:.06em; font-weight:600; margin-top:3px; }

/* Contact */
.tm-contact { display:flex; flex-direction:column; gap:8px; font-size:.8rem; color:#64748b; }
.tm-contact-item { display:flex; align-items:center; gap:10px; padding:8px 10px; background:#f8fafc; border-radius:8px; transition:background .2s; }
.tm-contact-item:hover { background:rgba(20,184,166,.05); }
.tm-contact-icon { width:28px; height:28px; display:flex; align-items:center; justify-content:center; background:#f0fdfa; border-radius:6px; color:#14b8a6; font-size:14px; flex-shrink:0; }
.tm-contact-icon.inactive { background:#f1f5f9; color:#94a3b8; }
.tm-contact-text { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }

/* Card footer */
.tm-card-footer { display:flex; gap:8px; margin-top:16px; padding-top:16px; border-top:1px solid #e2e8f0; }
.tm-foot-btn { flex:1; padding:8px 12px; border-radius:8px; font-size:.78rem; font-weight:500; cursor:pointer; font-family:inherit; transition:all .2s; border:1px solid #e2e8f0; background:#f8fafc; color:#374151; }
.tm-foot-btn:hover { background:#fff; border-color:#14b8a6; color:#0f172a; transform:translateY(-1px); box-shadow:0 4px 12px rgba(0,0,0,.08); }
.tm-foot-btn.tm-btn-danger { background:rgba(239,68,68,.06); border-color:rgba(239,68,68,.2); color:#dc2626; }
.tm-foot-btn.tm-btn-danger:hover { background:rgba(239,68,68,.1); }
.tm-foot-btn.tm-btn-enable { background:rgba(20,184,166,.06); border-color:rgba(20,184,166,.2); color:#0f766e; }
.tm-foot-btn.tm-btn-enable:hover { background:rgba(20,184,166,.12); }

/* Empty state */
.tm-empty { text-align:center; padding:60px 20px; color:#64748b; }
.tm-empty-icon { font-size:3rem; margin-bottom:16px; opacity:.5; }
.tm-empty h3 { font-size:1rem; font-weight:600; margin-bottom:6px; }

/* ── Modal ── */
.tm-overlay { position:fixed; inset:0; background:rgba(15,23,42,.35); backdrop-filter:blur(4px); z-index:2000; display:none; align-items:center; justify-content:center; padding:16px; }
.tm-overlay.active { display:flex; }
.tm-modal { background:#fff; border-radius:16px; width:100%; max-width:460px; box-shadow:0 20px 60px rgba(0,0,0,.2); animation:tm-modal-in .2s ease; }
@keyframes tm-modal-in { from{opacity:0;transform:scale(.95) translateY(-10px)} to{opacity:1;transform:none} }
.tm-modal-head { display:flex; justify-content:space-between; align-items:center; padding:18px 22px 14px; border-bottom:1px solid #e2e8f0; }
.tm-modal-title { font-size:1rem; font-weight:700; color:#0f172a; }
.tm-modal-close { width:28px; height:28px; border-radius:6px; border:none; background:#f1f5f9; color:#64748b; cursor:pointer; font-size:1.1rem; display:flex; align-items:center; justify-content:center; transition:background .15s; }
.tm-modal-close:hover { background:#e2e8f0; }
.tm-modal-body { padding:18px 22px; }
.tm-modal-foot { display:flex; justify-content:flex-end; gap:8px; padding:14px 22px 18px; border-top:1px solid #e2e8f0; }

/* Form fields */
.tm-form-row  { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
.tm-form-group { margin-bottom:14px; }
.tm-form-label { display:block; font-size:.78rem; font-weight:600; color:#374151; margin-bottom:6px; }
.tm-form-hint  { font-size:.72rem; font-weight:400; color:#94a3b8; margin-left:4px; }
.tm-form-input { width:100%; padding:9px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:.84rem; font-family:inherit; background:#fff; transition:border-color .2s; box-sizing:border-box; }
.tm-form-input:focus { outline:none; border-color:#14b8a6; box-shadow:0 0 0 3px rgba(20,184,166,.1); }
.tm-btn-cancel { padding:8px 18px; border-radius:8px; border:1px solid #e2e8f0; background:#f8fafc; color:#374151; font-size:.82rem; font-weight:500; cursor:pointer; font-family:inherit; transition:all .15s; }
.tm-btn-cancel:hover { background:#fff; border-color:#64748b; }
.tm-btn-save { padding:8px 22px; border-radius:8px; border:none; background:#14b8a6; color:#fff; font-size:.82rem; font-weight:600; cursor:pointer; font-family:inherit; transition:background .15s; }
.tm-btn-save:hover { background:#0d9488; }
.tm-btn-save:disabled { opacity:.5; cursor:not-allowed; }

/* Confirm modal */
.tm-confirm-modal { max-width:380px; }
.tm-confirm-icon { width:52px; height:52px; border-radius:50%; background:rgba(245,158,11,.1); display:flex; align-items:center; justify-content:center; font-size:1.4rem; margin:4px auto 14px; }
.tm-confirm-msg  { font-size:.9rem; font-weight:600; color:#0f172a; margin-bottom:6px; text-align:center; }
.tm-confirm-sub  { font-size:.8rem; color:#64748b; text-align:center; }
.tm-btn-confirm-ok { padding:8px 22px; border-radius:8px; border:none; font-size:.82rem; font-weight:600; cursor:pointer; font-family:inherit; transition:background .15s; }
.tm-btn-confirm-ok.danger  { background:#ef4444; color:#fff; }
.tm-btn-confirm-ok.success { background:#14b8a6; color:#fff; }
.tm-btn-confirm-ok:hover.danger  { background:#dc2626; }
.tm-btn-confirm-ok:hover.success { background:#0d9488; }

/* Skeleton */
.tm-skel { background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%); background-size:200% 100%; animation:tm-shimmer 1.4s infinite; border-radius:12px; }
@keyframes tm-shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* Toast */
.tm-toasts { position:fixed; top:18px; right:18px; z-index:4000; display:flex; flex-direction:column; gap:8px; pointer-events:none; }
.tm-toast { padding:11px 18px; border-radius:8px; font-size:.82rem; font-weight:500; color:#fff; box-shadow:0 4px 14px rgba(0,0,0,.18); animation:tm-toast-in .25s ease; pointer-events:auto; }
@keyframes tm-toast-in { from{opacity:0;transform:translateX(20px)} to{opacity:1;transform:none} }
.tm-toast-success { background:#14b8a6; }
.tm-toast-error   { background:#ef4444; }
</style>
@endpush

<div class="tm-page">

    {{-- Header --}}
    <div class="tm-header">
        <div>
            <div class="tm-title">Team</div>
            <div class="tm-sub">Manage your team members and their access</div>
        </div>
        <button class="tm-btn-add" onclick="tmOpenAdd()">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Member
        </button>
    </div>

    {{-- Filter bar --}}
    <div class="tm-filter">
        <input type="text" id="tmSearch" placeholder="Search by name…" oninput="tmFilter()" />
        <select id="tmRoleFilter" onchange="tmFilter()">
            <option value="">All Roles</option>
            @foreach($roles as $r)
                <option value="{{ $r }}">{{ \App\Http\Controllers\TeamController::ROLE_LABELS[$r] ?? ucfirst($r) }}</option>
            @endforeach
        </select>
        <select id="tmStatusFilter" onchange="tmFilter()">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
        </select>
    </div>

    {{-- Team Grid --}}
    <div id="tmGrid">
        <div class="tm-grid">
            <div class="tm-card"><div class="tm-skel" style="height:300px"></div></div>
            <div class="tm-card"><div class="tm-skel" style="height:300px"></div></div>
            <div class="tm-card"><div class="tm-skel" style="height:300px"></div></div>
        </div>
    </div>

</div>

{{-- Add / Edit Modal --}}
<div class="tm-overlay" id="tmModal">
    <div class="tm-modal">
        <div class="tm-modal-head">
            <div class="tm-modal-title" id="tmModalTitle">Add Member</div>
            <button class="tm-modal-close" onclick="tmCloseModal('tmModal')">×</button>
        </div>
        <div class="tm-modal-body">
            <input type="hidden" id="sf_id">
            <div class="tm-form-row">
                <div class="tm-form-group">
                    <label class="tm-form-label">Name *</label>
                    <input type="text" id="sf_name" class="tm-form-input" placeholder="Full name">
                </div>
                <div class="tm-form-group">
                    <label class="tm-form-label">Role *</label>
                    <select id="sf_role" class="tm-form-input">
                        @foreach($roles as $r)
                            <option value="{{ $r }}">{{ \App\Http\Controllers\TeamController::ROLE_LABELS[$r] ?? ucfirst($r) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="tm-form-row">
                <div class="tm-form-group">
                    <label class="tm-form-label">Mobile *</label>
                    <input type="tel" id="sf_mobile" class="tm-form-input" placeholder="10-digit number" inputmode="tel">
                </div>
                <div class="tm-form-group">
                    <label class="tm-form-label">
                        PIN (4 digits)
                        <span class="tm-form-hint" id="tmPinHint">leave blank to keep</span>
                    </label>
                    <input type="text" id="sf_pin" class="tm-form-input" maxlength="4" placeholder="1234" inputmode="numeric">
                </div>
            </div>
            <div class="tm-form-group">
                <label class="tm-form-label">Telegram ID <span class="tm-form-hint">(optional)</span></label>
                <input type="text" id="sf_telegram" class="tm-form-input" placeholder="@username">
            </div>
        </div>
        <div class="tm-modal-foot">
            <button class="tm-btn-cancel" onclick="tmCloseModal('tmModal')">Cancel</button>
            <button class="tm-btn-save" id="tmSaveBtn" onclick="tmSave()">Save</button>
        </div>
    </div>
</div>

{{-- Confirm Modal --}}
<div class="tm-overlay" id="tmConfirmModal">
    <div class="tm-modal tm-confirm-modal">
        <div class="tm-modal-head" style="border-bottom:none;padding-bottom:8px">
            <div></div>
            <button class="tm-modal-close" onclick="tmCloseModal('tmConfirmModal')">×</button>
        </div>
        <div class="tm-modal-body" style="padding-top:0">
            <div class="tm-confirm-icon">⚠️</div>
            <div class="tm-confirm-msg" id="tmConfirmMsg"></div>
            <div class="tm-confirm-sub" id="tmConfirmSub"></div>
        </div>
        <div class="tm-modal-foot">
            <button class="tm-btn-cancel" onclick="tmCloseModal('tmConfirmModal')">Cancel</button>
            <button class="tm-btn-confirm-ok" id="tmConfirmOk">Confirm</button>
        </div>
    </div>
</div>

<div class="tm-toasts" id="tmToasts"></div>

<script>
const TM_CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
let tmAllStaff = [];

// ── Utilities ──────────────────────────────────────────────────────
function tmEsc(s){ const d=document.createElement('div'); d.textContent=s||''; return d.innerHTML; }

function tmToast(msg, type='success'){
    const t = document.createElement('div');
    t.className = `tm-toast tm-toast-${type}`;
    t.textContent = msg;
    document.getElementById('tmToasts').appendChild(t);
    setTimeout(() => t.remove(), 3000);
}

function tmOpenModal(id)  { document.getElementById(id).classList.add('active'); }
function tmCloseModal(id) { document.getElementById(id).classList.remove('active'); }

function tmConfirm(msg, sub, onOk, okClass='danger', okLabel='Confirm'){
    document.getElementById('tmConfirmMsg').textContent = msg;
    document.getElementById('tmConfirmSub').textContent = sub || '';
    const btn = document.getElementById('tmConfirmOk');
    btn.textContent = okLabel;
    btn.className   = `tm-btn-confirm-ok ${okClass}`;
    btn.onclick     = () => { tmCloseModal('tmConfirmModal'); onOk(); };
    tmOpenModal('tmConfirmModal');
}

function tmInitials(name){
    const p = (name||'').split(' ');
    return p.length >= 2 ? (p[0][0]+p[1][0]).toUpperCase() : (name||'').substring(0,2).toUpperCase();
}

function tmFmtDate(d){
    if(!d) return 'Never';
    return new Date(d).toLocaleDateString('en-IN',{day:'numeric',month:'short',year:'numeric'});
}

async function tmPost(url, data){
    const r = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': TM_CSRF, Accept:'application/json' },
        body: JSON.stringify(data),
    });
    return r.json();
}

async function tmGet(url){
    const r = await fetch(url, { headers: { 'X-CSRF-TOKEN': TM_CSRF, Accept:'application/json' } });
    return r.json();
}

// ── Load & Render ──────────────────────────────────────────────────
async function tmLoad(){
    const data = await tmGet('/team/api/list').catch(()=>({ok:false}));
    if(!data.ok){ document.getElementById('tmGrid').innerHTML='<div class="tm-empty"><div class="tm-empty-icon">⚠️</div><h3>Failed to load team</h3></div>'; return; }
    tmAllStaff = data.staff || [];
    tmRender(tmAllStaff);
}

function tmRender(staff){
    const grid = document.getElementById('tmGrid');
    if(!staff.length){
        grid.innerHTML = '<div class="tm-empty"><div class="tm-empty-icon">👥</div><h3>No team members found</h3><p>Add your first team member to get started</p></div>';
        return;
    }
    grid.innerHTML = '<div class="tm-grid">' + staff.map(s => {
        const active    = s.active;
        const inactCls  = active ? '' : 'tm-inactive';
        const avatarCls = active ? '' : 'inactive';
        const iconCls   = active ? '' : 'inactive';
        const dotCls    = active ? 'active' : 'inactive';

        return `<div class="tm-card ${inactCls}">
            <span class="tm-status-dot ${dotCls}"></span>
            <div class="tm-avatar ${avatarCls}">${tmEsc(tmInitials(s.name))}</div>
            <div class="tm-name">${tmEsc(s.name)}</div>
            <div class="tm-role">
                <span class="tm-badge role-${s.role.replace(/_/g,'-')}">${tmEsc(s.role_label)}</span>
            </div>
            <div class="tm-stats">
                <div class="tm-stat">
                    <div class="tm-stat-value">${s.active_tasks}</div>
                    <div class="tm-stat-label">Active Tasks</div>
                </div>
                <div class="tm-stat">
                    <div class="tm-stat-value">${s.total_tasks}</div>
                    <div class="tm-stat-label">Total Tasks</div>
                </div>
            </div>
            <div class="tm-contact">
                <div class="tm-contact-item">
                    <div class="tm-contact-icon ${iconCls}">📱</div>
                    <span class="tm-contact-text">${tmEsc(s.mobile || '—')}</span>
                </div>
                ${s.telegram_id ? `<div class="tm-contact-item">
                    <div class="tm-contact-icon ${iconCls}">✈️</div>
                    <span class="tm-contact-text">@${tmEsc(s.telegram_id.replace(/^@/,''))}</span>
                </div>` : ''}
                <div class="tm-contact-item">
                    <div class="tm-contact-icon ${iconCls}">🕐</div>
                    <span class="tm-contact-text">Last login: ${tmEsc(tmFmtDate(s.last_login))}</span>
                </div>
            </div>
            <div class="tm-card-footer">
                <button class="tm-foot-btn" onclick='tmOpenEdit(${JSON.stringify(s).replace(/'/g,"&#39;")})'>Edit</button>
                <button class="tm-foot-btn ${active ? 'tm-btn-danger' : 'tm-btn-enable'}"
                    onclick="tmToggle(${s.id}, '${tmEsc(s.name)}', ${active})">
                    ${active ? 'Disable' : 'Enable'}
                </button>
            </div>
        </div>`;
    }).join('') + '</div>';
}

// ── Filter ────────────────────────────────────────────────────────
function tmFilter(){
    const search = document.getElementById('tmSearch').value.toLowerCase();
    const role   = document.getElementById('tmRoleFilter').value;
    const status = document.getElementById('tmStatusFilter').value;

    const filtered = tmAllStaff.filter(s => {
        const matchName   = !search || s.name.toLowerCase().includes(search);
        const matchRole   = !role   || s.role === role;
        const matchStatus = !status || (status === 'active' ? s.active : !s.active);
        return matchName && matchRole && matchStatus;
    });
    tmRender(filtered);
}

// ── Add / Edit ────────────────────────────────────────────────────
function tmOpenAdd(){
    document.getElementById('sf_id').value        = '';
    document.getElementById('sf_name').value      = '';
    document.getElementById('sf_role').value      = 'sales_rep';
    document.getElementById('sf_mobile').value    = '';
    document.getElementById('sf_pin').value       = '';
    document.getElementById('sf_telegram').value  = '';
    document.getElementById('tmModalTitle').textContent = 'Add Member';
    document.getElementById('tmPinHint').style.display  = 'none';
    tmOpenModal('tmModal');
}

function tmOpenEdit(s){
    document.getElementById('sf_id').value        = s.id;
    document.getElementById('sf_name').value      = s.name;
    document.getElementById('sf_role').value      = s.role;
    document.getElementById('sf_mobile').value    = s.mobile || '';
    document.getElementById('sf_pin').value       = '';
    document.getElementById('sf_telegram').value  = s.telegram_id || '';
    document.getElementById('tmModalTitle').textContent = 'Edit Member';
    document.getElementById('tmPinHint').style.display  = 'inline';
    tmOpenModal('tmModal');
}

async function tmSave(){
    const id   = document.getElementById('sf_id').value;
    const name = document.getElementById('sf_name').value.trim();
    const role = document.getElementById('sf_role').value;
    const mob  = document.getElementById('sf_mobile').value.trim();
    const pin  = document.getElementById('sf_pin').value;
    const tg   = document.getElementById('sf_telegram').value.trim();

    if(!name)  { tmToast('Name is required','error'); return; }
    if(!mob)   { tmToast('Mobile is required','error'); return; }
    if(!id && !pin) { tmToast('PIN required for new member','error'); return; }
    if(pin && !/^\d{4}$/.test(pin)) { tmToast('PIN must be exactly 4 digits','error'); return; }

    const btn = document.getElementById('tmSaveBtn');
    btn.disabled = true; btn.textContent = 'Saving…';

    const payload = { name, role, mobile: mob, telegram_id: tg || null };
    if(id)  payload.id = parseInt(id);
    if(pin) payload.pin = pin;

    const r = await tmPost('/team/api/save', payload).catch(()=>({ok:false}));
    btn.disabled = false; btn.textContent = 'Save';

    if(r.ok){ tmToast(id ? 'Member updated' : 'Member added'); tmCloseModal('tmModal'); tmLoad(); }
    else    { tmToast(r.error || 'Failed to save','error'); }
}

// ── Toggle ────────────────────────────────────────────────────────
function tmToggle(id, name, isActive){
    const action = isActive ? 'disable' : 'enable';
    const msg    = isActive ? `Disable ${name}?` : `Enable ${name}?`;
    const sub    = isActive ? 'This will prevent them from logging in.' : 'This will restore their access.';
    const cls    = isActive ? 'danger' : 'success';
    const label  = isActive ? 'Disable' : 'Enable';

    tmConfirm(msg, sub, async () => {
        const r = await tmPost('/team/api/toggle', { id }).catch(()=>({ok:false}));
        if(r.ok){ tmToast(isActive ? 'Member disabled' : 'Member enabled'); tmLoad(); }
        else    { tmToast(r.error || 'Failed to update','error'); }
    }, cls, label);
}

// Close modals on overlay click
document.querySelectorAll('.tm-overlay').forEach(el =>
    el.addEventListener('click', e => { if(e.target === el) tmCloseModal(el.id); })
);

tmLoad();
</script>

</x-layouts.app>
