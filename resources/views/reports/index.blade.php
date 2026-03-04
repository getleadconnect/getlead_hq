<x-layouts.app title="Reports">

@push('styles')
<style>
/* ── Reports Page ── */
.rp-filter { display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-bottom:18px; padding:14px 18px; background:#fff; border:1px solid #e2e8f0; border-radius:12px; }
.rp-filter input[type="date"] { padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:.82rem; font-family:inherit; background:#fff; min-height:36px; cursor:pointer; transition:border-color .2s; }
.rp-filter input[type="date"]:focus { outline:none; border-color:#14b8a6; box-shadow:0 0 0 3px rgba(20,184,166,.1); }
.rp-filter select { padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:.82rem; font-family:inherit; background:#fff; min-height:36px; min-width:160px; cursor:pointer; transition:border-color .2s; }
.rp-filter select:focus { outline:none; border-color:#14b8a6; }

/* Buttons */
.rp-btn { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border-radius:8px; font-size:.8rem; font-weight:500; cursor:pointer; border:1px solid #e2e8f0; background:#f1f5f9; color:#374151; font-family:inherit; transition:all .15s; text-decoration:none; }
.rp-btn:hover { background:#fff; border-color:#14b8a6; color:#0f172a; }

/* Missing Alert */
.rp-missing { margin-bottom:16px; padding:13px 16px; background:rgba(245,158,11,.06); border:1px solid rgba(245,158,11,.2); border-left:3px solid #f59e0b; border-radius:10px; }
.rp-missing-title { font-size:.78rem; font-weight:600; color:#b45309; margin-bottom:7px; display:flex; align-items:center; gap:5px; }
.rp-missing-list { font-size:.8rem; line-height:2; }
.rp-missing-chip { display:inline-flex; align-items:center; gap:4px; margin-right:8px; margin-bottom:2px; }
.rp-missing-chip-name { font-weight:500; color:#0f172a; }
.rp-missing-chip-role { font-size:.65rem; font-weight:600; background:#f1f5f9; border:1px solid #e2e8f0; color:#64748b; padding:1px 6px; border-radius:20px; }

/* Summary bar */
.rp-summary { display:flex; gap:14px; margin-bottom:16px; flex-wrap:wrap; }
.rp-summary-pill { display:flex; align-items:center; gap:6px; padding:6px 14px; border-radius:20px; font-size:.78rem; font-weight:600; }
.rp-pill-green  { background:rgba(16,185,129,.1); color:#15803d; }
.rp-pill-amber  { background:rgba(245,158,11,.1); color:#b45309; }
.rp-pill-slate  { background:#f1f5f9; color:#475569; }

/* Report Card */
.rp-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; margin-bottom:10px; overflow:hidden; transition:all .2s; cursor:pointer; }
.rp-card:hover { border-color:rgba(20,184,166,.3); box-shadow:0 4px 12px rgba(0,0,0,.06); }
.rp-card.rp-open { border-color:#14b8a6; box-shadow:0 4px 20px rgba(20,184,166,.12); }

.rp-card-header { display:flex; justify-content:space-between; align-items:center; padding:16px 20px; transition:background .2s; user-select:none; }
.rp-card.rp-open .rp-card-header { background:rgba(20,184,166,.03); border-bottom:1px solid #e2e8f0; }

.rp-card-left { display:flex; align-items:center; gap:12px; }
.rp-avatar { width:38px; height:38px; border-radius:50%; background:linear-gradient(135deg,#14b8a6,#0d9488); display:flex; align-items:center; justify-content:center; font-size:.8rem; font-weight:700; color:#fff; flex-shrink:0; }
.rp-card-name { font-size:.9rem; font-weight:600; color:#0f172a; }
.rp-card-role { font-size:.72rem; color:#64748b; margin-top:1px; }

.rp-card-right { display:flex; align-items:center; gap:10px; }
.rp-time { font-size:.78rem; font-weight:600; color:#14b8a6; }
.rp-chevron { transition:transform .25s; color:#94a3b8; }
.rp-card.rp-open .rp-chevron { transform:rotate(180deg); }

/* Card body */
.rp-card-body { max-height:0; overflow:hidden; transition:max-height .4s ease; }
.rp-card.rp-open .rp-card-body { max-height:5000px; }

.rp-data-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(300px,1fr)); gap:10px; padding:16px 20px; align-items:stretch; }
.rp-data-item { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px 14px; transition:all .15s; }
.rp-data-item:hover { background:rgba(20,184,166,.04); border-color:rgba(20,184,166,.25); }
.rp-data-label { font-size:.69rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em; color:#64748b; margin-bottom:6px; }
.rp-data-value { font-size:.85rem; font-weight:600; color:#0f172a; line-height:1.6; white-space:pre-wrap; word-break:break-word; }
.rp-data-value.rp-notes { font-weight:400; color:#374151; }

/* Skeleton */
.rp-skeleton { background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%); background-size:200% 100%; animation:rp-shimmer 1.4s infinite; border-radius:12px; }
@keyframes rp-shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
.rp-skel-card { height:68px; margin-bottom:10px; }

/* Empty */
.rp-empty { text-align:center; padding:56px 20px; color:#64748b; }
.rp-empty-icon { font-size:2.4rem; opacity:.45; margin-bottom:12px; }
.rp-empty p { font-size:.9rem; font-weight:500; }

/* Toast */
.rp-toasts { position:fixed; top:18px; right:18px; z-index:4000; display:flex; flex-direction:column; gap:8px; pointer-events:none; }
.rp-toast { padding:11px 18px; border-radius:8px; font-size:.82rem; font-weight:500; color:#fff; box-shadow:0 4px 14px rgba(0,0,0,.18); animation:rp-tin .25s ease; pointer-events:auto; }
@keyframes rp-tin { from{opacity:0;transform:translateX(20px)} to{opacity:1;transform:translateX(0)} }
.rp-toast-success { background:#14b8a6; }
.rp-toast-error   { background:#ef4444; }
</style>
@endpush

<div style="padding:20px">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
        <div>
            <div style="font-size:1.25rem;font-weight:700;color:#0f172a">Reports</div>
            <div style="font-size:.78rem;color:#64748b;margin-top:2px">View submitted daily reports by team members</div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="rp-filter">
        <input type="date" id="rpDate" max="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}" onchange="loadReports()">
        <button class="rp-btn" onclick="setDate('{{ date('Y-m-d') }}')">Today</button>
        <button class="rp-btn" onclick="setDate('{{ date('Y-m-d', strtotime('-1 day')) }}')">Yesterday</button>
        <select id="rpMember" onchange="loadReports()">
            <option value="">All Members</option>
            @foreach($staffList as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
            @endforeach
        </select>
        <span id="rpDateLabel" style="font-size:.78rem;color:#64748b;margin-left:4px"></span>
    </div>

    {{-- Missing Alert --}}
    <div id="rpMissing" class="rp-missing" style="display:none">
        <div class="rp-missing-title">⚠️ Missing Reports</div>
        <div id="rpMissingList" class="rp-missing-list"></div>
    </div>

    {{-- Summary Bar --}}
    <div id="rpSummary" class="rp-summary" style="display:none">
        <div class="rp-summary-pill rp-pill-green">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            <span id="rpSubmittedCount">0</span> Submitted
        </div>
        <div class="rp-summary-pill rp-pill-amber">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span id="rpMissingCount">0</span> Missing
        </div>
        <div class="rp-summary-pill rp-pill-slate" id="rpTotalPill">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            <span id="rpTotalCount">0</span> Total Staff
        </div>
    </div>

    {{-- Report Container --}}
    <div id="rpContainer">
        <div class="rp-skeleton rp-skel-card"></div>
        <div class="rp-skeleton rp-skel-card"></div>
        <div class="rp-skeleton rp-skel-card"></div>
    </div>

</div>

<div class="rp-toasts" id="rpToasts"></div>

<script>
const RP_CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

// Role field definitions
const ROLE_FIELDS = {
    sales_rep: [
        {k:'calls_made',       l:'📞 Calls Made'},
        {k:'calls_connected',  l:'📱 Connected'},
        {k:'demos_scheduled',  l:'🎯 Demos Scheduled'},
        {k:'demos_completed',  l:'🎯 Demos Done'},
        {k:'trials',           l:'🆕 Trials'},
        {k:'payments_closed',  l:'💰 Payments Closed'},
        {k:'payments_amount',  l:'💰 Amount', fmt:'currency'},
        {k:'hot_leads',        l:'🔥 Hot Leads'},
        {k:'notes',            l:'📝 Notes', full:true},
    ],
    secretary: [
        {k:'payments',         l:'💰 Payments', fmt:'table', full:true},
        {k:'tickets_handled',  l:'🎫 Tickets Handled'},
        {k:'license_updates',  l:'📋 License Updates'},
        {k:'followups',        l:'✅ Follow-ups'},
        {k:'notes',            l:'📝 Notes', full:true},
    ],
    support: [
        {k:'tickets_handled',    l:'🎫 Tickets Handled'},
        {k:'tickets_resolved',   l:'✅ Resolved'},
        {k:'avg_response_time',  l:'⏱️ Avg Response (min)'},
        {k:'escalation_count',   l:'⚠️ Escalations'},
        {k:'escalation_details', l:'📋 Escalation Details', full:true},
        {k:'notes',              l:'📝 Notes', full:true},
    ],
    hr: [
        {k:'attendance',      l:'👥 Attendance'},
        {k:'leave_requests',  l:'📋 Leave Requests'},
        {k:'interviews',      l:'🤝 Interviews'},
        {k:'issues',          l:'⚠️ Issues', full:true},
        {k:'notes',           l:'📝 Notes', full:true},
    ],
    finance: [
        {k:'invoices',          l:'📄 Invoices'},
        {k:'collected_count',   l:'💰 Collected'},
        {k:'collected_amount',  l:'💰 Collected Amount', fmt:'currency'},
        {k:'pending_count',     l:'⏳ Pending'},
        {k:'pending_amount',    l:'⏳ Pending Amount',   fmt:'currency'},
        {k:'expenses_count',    l:'💸 Expenses'},
        {k:'expenses_amount',   l:'💸 Expenses Amount',  fmt:'currency'},
        {k:'notes',             l:'📝 Notes', full:true},
    ],
    developer: [
        {k:'tasks',       l:'✅ Tasks Completed'},
        {k:'commits',     l:'🔀 Commits / PRs'},
        {k:'bugs_fixed',  l:'🐛 Bugs Fixed'},
        {k:'blockers',    l:'🚧 Blockers', full:true},
        {k:'notes',       l:'📝 Notes',   full:true},
    ],
    tester: [
        {k:'test_cases',    l:'🧪 Test Cases'},
        {k:'bugs_found',    l:'🐛 Bugs Found'},
        {k:'bugs_verified', l:'✅ Verified'},
        {k:'blockers',      l:'🚧 Blockers', full:true},
        {k:'notes',         l:'📝 Notes',   full:true},
    ],
    admin: [
        {k:'tasks',     l:'✅ Tasks'},
        {k:'decisions', l:'🎯 Decisions', full:true},
        {k:'notes',     l:'📝 Notes',    full:true},
    ],
};

function rpEsc(s){ const d=document.createElement('div'); d.textContent=s||''; return d.innerHTML; }
function rpToast(msg, type='success'){
    const t = document.createElement('div');
    t.className = `rp-toast rp-toast-${type}`;
    t.textContent = msg;
    document.getElementById('rpToasts').appendChild(t);
    setTimeout(() => t.remove(), 3000);
}
function setDate(d){ document.getElementById('rpDate').value = d; loadReports(); }

function fmtVal(v, fmt){
    if(v === undefined || v === null || v === '') return null;
    if(fmt === 'currency') return '₹' + Number(v).toLocaleString('en-IN');
    if(fmt === 'table' && Array.isArray(v))
        return v.map(r => `${rpEsc(r.customer)}: ₹${Number(r.amount||0).toLocaleString('en-IN')} (${rpEsc(r.type)})`).join('\n');
    return String(v);
}

function initials(name){
    return (name||'').split(' ').slice(0,2).map(w=>w[0]||'').join('').toUpperCase();
}

function renderCard(r){
    const fields  = ROLE_FIELDS[r.role] || [];
    const data    = r.report_data || {};
    const cardId  = `rpc-${r.id}`;

    const items = fields.map(f => {
        const raw = data[f.k];
        const val = fmtVal(raw, f.fmt);
        if(val === null || val === '') return '';
        const isNotes = f.k === 'notes';
        return `<div class="rp-data-item">
            <div class="rp-data-label">${f.l}</div>
            <div class="rp-data-value${isNotes?' rp-notes':''}">${rpEsc(val)}</div>
        </div>`;
    }).join('');

    return `<div class="rp-card" id="${cardId}" onclick="toggleCard('${cardId}')">
        <div class="rp-card-header">
            <div class="rp-card-left">
                <div class="rp-avatar">${initials(r.name)}</div>
                <div>
                    <div class="rp-card-name">${r.emoji} ${rpEsc(r.name)}</div>
                    <div class="rp-card-role">${rpEsc(r.role_label)}</div>
                </div>
            </div>
            <div class="rp-card-right">
                <span class="rp-time">${rpEsc(r.time)}</span>
                <svg class="rp-chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </div>
        <div class="rp-card-body">
            ${items ? `<div class="rp-data-grid">${items}</div>` : '<div style="padding:16px 20px;font-size:.82rem;color:#94a3b8">No data submitted</div>'}
        </div>
    </div>`;
}

function toggleCard(id){
    document.getElementById(id)?.classList.toggle('rp-open');
}

async function loadReports(){
    const date     = document.getElementById('rpDate').value;
    const memberId = document.getElementById('rpMember').value;

    // Update date label
    const lbl = document.getElementById('rpDateLabel');
    const d   = new Date(date+'T00:00:00');
    lbl.textContent = d.toLocaleDateString('en-IN', {weekday:'long', day:'numeric', month:'long', year:'numeric'});

    // Skeleton
    document.getElementById('rpContainer').innerHTML =
        '<div class="rp-skeleton rp-skel-card"></div>'.repeat(3);
    document.getElementById('rpMissing').style.display  = 'none';
    document.getElementById('rpSummary').style.display  = 'none';

    const params = new URLSearchParams({date});
    if(memberId) params.append('member_id', memberId);

    let data;
    try {
        const res = await fetch(`/reports/api/summary?${params}`, {
            headers: {'X-CSRF-TOKEN': RP_CSRF, 'Accept': 'application/json'}
        });
        data = await res.json();
    } catch(e){
        document.getElementById('rpContainer').innerHTML =
            '<div class="rp-empty"><div class="rp-empty-icon">⚠️</div><p>Failed to load reports</p></div>';
        return;
    }

    if(!data.ok){ rpToast('Error loading reports','error'); return; }

    const reports = data.reports || [];
    const missing = data.missing || [];

    // Summary bar
    document.getElementById('rpSubmittedCount').textContent = reports.length;
    document.getElementById('rpMissingCount').textContent   = missing.length;
    document.getElementById('rpTotalCount').textContent     = reports.length + missing.length;
    document.getElementById('rpSummary').style.display      = 'flex';

    // Missing alert (only when not filtering by member)
    if(missing.length && !memberId){
        document.getElementById('rpMissingList').innerHTML = missing.map(m =>
            `<span class="rp-missing-chip">
                <span class="rp-missing-chip-name">${rpEsc(m.name)}</span>
                <span class="rp-missing-chip-role">${rpEsc(m.role_label)}</span>
            </span>`
        ).join('');
        document.getElementById('rpMissing').style.display = 'block';
    }

    // Report cards
    if(!reports.length){
        document.getElementById('rpContainer').innerHTML =
            `<div class="rp-empty"><div class="rp-empty-icon">📋</div><p>No reports submitted${memberId?' by this member':''} for this date</p></div>`;
        return;
    }

    document.getElementById('rpContainer').innerHTML = reports.map(renderCard).join('');
    // Auto-open only when a specific member is selected
    if(memberId){
        document.querySelectorAll('#rpContainer .rp-card').forEach(c => c.classList.add('rp-open'));
    }
}

loadReports();
</script>

</x-layouts.app>
