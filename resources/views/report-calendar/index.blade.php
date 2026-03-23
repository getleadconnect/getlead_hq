<x-layouts.app title="Report Calendar">

@push('styles')
<style>
:root {
    --rc-teal:           #0D9B8C;
    --rc-teal-light:     #E8F7F5;
    --rc-teal-deep:      #0A7E72;
    --rc-red:            #E5484D;
    --rc-amber:          #F0A30A;
    --rc-green:          #30A46C;
    --rc-border:         #E5E7EB;
    --rc-border-light:   #F0F1F3;
    --rc-text:           #1A1D21;
    --rc-muted:          #6B7280;
    --rc-faint:          #9CA3AF;
    --rc-card:           #FFFFFF;
    --rc-bg:             #F4F5F7;
    --cell-submitted:    #34D399;
    --cell-missing:      #F87171;
    --cell-late:         #FBBF24;
    --cell-weekend:      #F0F1F3;
    --cell-future:       #F9FAFB;
}

.rc-wrap { padding:24px; animation:rc-fadein .3s ease; }
@keyframes rc-fadein { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:none} }

/* Page header */
.rc-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:14px; }
.rc-title   { font-size:1.4rem; font-weight:700; color:#0f172a; letter-spacing:-.02em; }
.rc-sub     { font-size:.8rem; color:var(--rc-muted); margin-top:3px; }
.rc-actions { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }

/* Month nav */
.rc-month-nav { display:flex; align-items:center; gap:6px; }
.rc-mnav-btn  { width:34px; height:34px; border-radius:8px; border:1px solid var(--rc-border); background:var(--rc-card); cursor:pointer; display:flex; align-items:center; justify-content:center; transition:all .15s; color:var(--rc-muted); }
.rc-mnav-btn:hover { background:var(--rc-bg); color:var(--rc-text); }
.rc-mnav-btn svg { width:16px; height:16px; }
.rc-month-label { font-size:.9rem; font-weight:700; min-width:150px; text-align:center; color:#0f172a; }

/* Buttons */
.rc-btn { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:8px; font-family:inherit; font-size:.8rem; font-weight:500; cursor:pointer; transition:all .15s; border:1px solid var(--rc-border); background:var(--rc-card); color:var(--rc-text); white-space:nowrap; }
.rc-btn:hover { background:var(--rc-bg); }
.rc-btn svg  { width:14px; height:14px; flex-shrink:0; }
.rc-btn-primary { background:var(--rc-teal); color:#fff; border-color:var(--rc-teal); }
.rc-btn-primary:hover { background:var(--rc-teal-deep); }

/* Summary chips */
.rc-summary { display:flex; gap:10px; margin-bottom:20px; flex-wrap:wrap; }
.rc-chip { background:var(--rc-card); border:1px solid var(--rc-border); border-radius:10px; padding:14px 18px; display:flex; align-items:center; gap:12px; flex:1; min-width:120px; box-shadow:0 1px 2px rgba(0,0,0,.04); }
.rc-chip-dot { width:10px; height:10px; border-radius:50%; flex-shrink:0; }
.rc-chip-val { font-size:1.4rem; font-weight:700; line-height:1; }
.rc-chip-lbl { font-size:.65rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em; color:var(--rc-faint); margin-top:2px; }

/* Filter + legend bar */
.rc-bar { display:flex; align-items:center; gap:14px; margin-bottom:16px; flex-wrap:wrap; }
.rc-search { position:relative; }
.rc-search input { padding:8px 12px 8px 32px; border:1px solid var(--rc-border); border-radius:8px; font-size:.82rem; font-family:inherit; background:var(--rc-card); outline:none; width:220px; transition:border .15s; }
.rc-search input:focus { border-color:var(--rc-teal); }
.rc-search svg { position:absolute; left:9px; top:50%; transform:translateY(-50%); width:14px; height:14px; color:var(--rc-faint); }
.rc-legend { display:flex; align-items:center; gap:14px; font-size:.74rem; color:var(--rc-muted); flex-wrap:wrap; }
.rc-legend-item { display:flex; align-items:center; gap:5px; }
.rc-legend-box  { width:13px; height:13px; border-radius:3px; }

/* Heatmap card */
.rc-card { background:var(--rc-card); border:1px solid var(--rc-border); border-radius:12px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.04); }
.rc-table-wrap { overflow-x:auto; }
.rc-table { width:100%; border-collapse:collapse; min-width:900px; }
.rc-table thead th { font-size:.7rem; font-weight:600; color:var(--rc-faint); text-align:center; padding:10px 0; border-bottom:1px solid var(--rc-border); background:#FAFBFC; position:sticky; top:0; z-index:2; }
.rc-table thead th:first-child { text-align:left; padding-left:16px; min-width:190px; width:190px; }
.rc-table thead th:last-child  { min-width:78px; width:78px; }
.rc-table thead th.rc-today-hd { color:var(--rc-teal); font-weight:700; }
.rc-table thead th.rc-wknd-hd  { opacity:.5; }
.rc-table tbody tr { border-bottom:1px solid var(--rc-border-light); transition:background .1s; }
.rc-table tbody tr:last-child { border-bottom:none; }
.rc-table tbody tr:hover { background:#FAFBFC; }
.rc-table td { padding:6px 2px; text-align:center; vertical-align:middle; }
.rc-table td:first-child { text-align:left; padding:8px 16px; }
.rc-table td:last-child  { padding-right:16px; }

/* Staff cell */
.rc-staff-cell { display:flex; align-items:center; gap:10px; }
.rc-avatar { width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; color:#fff; flex-shrink:0; }
.rc-staff-name { font-size:.82rem; font-weight:600; line-height:1.2; color:#0f172a; }
.rc-staff-role { font-size:.7rem; color:var(--rc-faint); }

/* Day cells */
.rc-cell { width:26px; height:26px; border-radius:5px; display:inline-flex; align-items:center; justify-content:center; margin:0 auto; cursor:default; position:relative; transition:transform .12s, box-shadow .12s; }
.rc-cell:hover:not(.future):not(.weekend) { transform:scale(1.25); box-shadow:0 2px 8px rgba(0,0,0,.12); z-index:5; cursor:pointer; }
.rc-cell.submitted { background:var(--cell-submitted); }
.rc-cell.missing   { background:var(--cell-missing); }
.rc-cell.late      { background:var(--cell-late); }
.rc-cell.weekend   { background:var(--cell-weekend); }
.rc-cell.future    { background:var(--cell-future); border:1px dashed var(--rc-border); }
.rc-cell.today-ring { box-shadow:0 0 0 2.5px var(--rc-teal), 0 0 0 4px rgba(13,155,140,.2); }
.rc-cell.missing.today-ring { box-shadow:0 0 0 2.5px var(--rc-red), 0 0 0 4px rgba(229,72,77,.2); animation:rc-pulse 2s ease-in-out infinite; }
@keyframes rc-pulse { 0%,100%{box-shadow:0 0 0 2.5px var(--rc-red),0 0 0 4px rgba(229,72,77,.15)} 50%{box-shadow:0 0 0 2.5px var(--rc-red),0 0 0 8px rgba(229,72,77,.04)} }

/* Completion bar */
.rc-rate-cell { display:flex; align-items:center; gap:6px; }
.rc-bar-track { width:34px; height:5px; background:var(--rc-border-light); border-radius:99px; overflow:hidden; }
.rc-bar-fill  { height:100%; border-radius:99px; transition:width .5s ease; }
.rc-rate-pct  { font-size:.75rem; font-weight:600; min-width:30px; text-align:right; }

/* Daily % summary row */
.rc-daily-row td { background:#FAFBFC !important; padding:10px 2px !important; border-top:2px solid var(--rc-border) !important; }
.rc-daily-row td:first-child { padding-left:16px !important; font-size:.72rem; font-weight:700; color:var(--rc-muted); text-transform:uppercase; letter-spacing:.04em; }
.rc-dpct { font-size:.68rem; font-weight:700; color:var(--rc-faint); line-height:26px; }
.rc-dpct.good { color:var(--rc-green); }
.rc-dpct.warn { color:var(--rc-amber); }
.rc-dpct.bad  { color:var(--rc-red); }

/* Tooltip */
.rc-tooltip { display:none; position:fixed; background:#1A1D21; color:#fff; font-size:.75rem; font-weight:500; padding:7px 12px; border-radius:8px; pointer-events:none; z-index:9000; white-space:nowrap; box-shadow:0 4px 12px rgba(0,0,0,.2); }
.rc-tooltip.visible { display:block; }

/* Modal */
.rc-modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.35); backdrop-filter:blur(3px); z-index:3000; align-items:center; justify-content:center; }
.rc-modal-overlay.open { display:flex; }
.rc-modal { background:#fff; border-radius:16px; padding:26px; max-width:360px; width:92%; box-shadow:0 20px 40px rgba(0,0,0,.14); position:relative; animation:rc-popup .2s ease; }
@keyframes rc-popup { from{opacity:0;transform:scale(.96) translateY(8px)} to{opacity:1;transform:none} }
.rc-modal-close { position:absolute; top:14px; right:14px; width:28px; height:28px; border:none; background:#f1f5f9; border-radius:50%; cursor:pointer; display:flex; align-items:center; justify-content:center; color:#64748b; transition:background .15s; }
.rc-modal-close:hover { background:#e2e8f0; }
.rc-modal-close svg { width:14px; height:14px; }
.rc-modal-stat { display:flex; align-items:center; gap:10px; padding:11px 0; border-bottom:1px solid #f1f5f9; }
.rc-modal-stat:last-child { border-bottom:none; }
.rc-modal-dot  { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.rc-modal-lbl  { font-size:.82rem; color:#64748b; flex:1; }
.rc-modal-val  { font-size:.82rem; font-weight:600; }

/* Loading skeleton */
.rc-skel { background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%); background-size:200% 100%; animation:rc-shimmer 1.4s infinite; border-radius:8px; }
@keyframes rc-shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }

/* Responsive */
@media(max-width:768px) {
    .rc-wrap { padding:14px 12px; }
    .rc-header { flex-direction:column; gap:12px; }
    .rc-actions { width:100%; justify-content:space-between; }
    .rc-summary { gap:8px; }
    .rc-chip { min-width:calc(50% - 4px); flex:unset; padding:12px 14px; }
    .rc-chip-val { font-size:1.2rem; }
    .rc-bar { flex-direction:column; align-items:flex-start; gap:10px; }
    .rc-search input { width:100%; }
    .rc-modal-overlay { padding:0; align-items:flex-end; }
    .rc-modal { border-radius:14px 14px 0 0; max-width:100%; }
}
@media(max-width:480px) {
    .rc-chip { min-width:calc(50% - 4px); }
    .rc-actions { flex-wrap:wrap; gap:6px; }
    .rc-month-label { min-width:120px; font-size:.82rem; }
}
</style>
@endpush

<div class="rc-wrap">

    {{-- Header --}}
    <div class="rc-header">
        <div>
            <div class="rc-title">Report Calendar</div>
            <div class="rc-sub">Visual overview of daily report submissions across your team</div>
        </div>
        <div class="rc-actions">
            <div class="rc-month-nav">
                <button class="rc-mnav-btn" onclick="rcChangeMonth(-1)" title="Previous month">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M15 19l-7-7 7-7"/></svg>
                </button>
                <div class="rc-month-label" id="rcMonthLabel">Loading…</div>
                <button class="rc-mnav-btn" onclick="rcChangeMonth(1)" title="Next month">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M9 5l7 7-7 7"/></svg>
                </button>
            </div>
            <button class="rc-btn" onclick="rcExportCSV()">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                Export
            </button>
        </div>
    </div>

    {{-- Summary chips --}}
    <div class="rc-summary" id="rcSummary">
        <div class="rc-chip rc-skel" style="height:60px"></div>
        <div class="rc-chip rc-skel" style="height:60px"></div>
        <div class="rc-chip rc-skel" style="height:60px"></div>
        <div class="rc-chip rc-skel" style="height:60px"></div>
        <div class="rc-chip rc-skel" style="height:60px"></div>
    </div>

    {{-- Filter + legend --}}
    <div class="rc-bar">
        <div class="rc-search">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" id="rcSearch" placeholder="Search staff…" oninput="rcRender()">
        </div>
        <div class="rc-legend">
            <div class="rc-legend-item"><div class="rc-legend-box" style="background:var(--cell-submitted)"></div> Submitted</div>
            <div class="rc-legend-item"><div class="rc-legend-box" style="background:var(--cell-late)"></div> Late</div>
            <div class="rc-legend-item"><div class="rc-legend-box" style="background:var(--cell-missing)"></div> Missing</div>
            <div class="rc-legend-item"><div class="rc-legend-box" style="background:var(--cell-weekend)"></div> Weekend</div>
            <div class="rc-legend-item"><div class="rc-legend-box" style="background:var(--cell-future);border:1px dashed #e2e8f0"></div> Future</div>
        </div>
    </div>

    {{-- Heatmap --}}
    <div class="rc-card">
        <div class="rc-table-wrap">
            <table class="rc-table" id="rcTable">
                <tbody><tr><td colspan="33" style="padding:48px;text-align:center;color:#94a3b8">Loading calendar…</td></tr></tbody>
            </table>
        </div>
    </div>

</div>

{{-- Tooltip --}}
<div class="rc-tooltip" id="rcTooltip"></div>

{{-- Detail modal --}}
<div class="rc-modal-overlay" id="rcModalOverlay" onclick="if(event.target===this)rcCloseModal()">
    <div class="rc-modal" id="rcModal"></div>
</div>

<script>
const RC_CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';
const RC_MONTHS = ["January","February","March","April","May","June","July","August","September","October","November","December"];
const RC_DAYS   = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];

// State
let rcYear, rcMonth; // JS 0-indexed month
let rcData = null;   // last API response

// Init: current month
(function(){
    const now = new Date();
    rcYear  = now.getFullYear();
    rcMonth = now.getMonth();
    rcLoad();
})();

function rcChangeMonth(delta) {
    rcMonth += delta;
    if (rcMonth > 11) { rcMonth = 0; rcYear++; }
    if (rcMonth < 0)  { rcMonth = 11; rcYear--; }
    rcLoad();
}

async function rcLoad() {
    document.getElementById('rcMonthLabel').textContent = `${RC_MONTHS[rcMonth]} ${rcYear}`;
    document.getElementById('rcTable').innerHTML =
        '<tbody><tr><td colspan="33" style="padding:48px;text-align:center;color:#94a3b8">Loading…</td></tr></tbody>';

    const url = `/report-calendar/api/data?year=${rcYear}&month=${rcMonth + 1}`;
    const res = await fetch(url, { headers: { Accept: 'application/json', 'X-CSRF-TOKEN': RC_CSRF } });
    rcData = await res.json();

    if (!rcData.ok) {
        document.getElementById('rcTable').innerHTML =
            '<tbody><tr><td colspan="33" style="padding:48px;text-align:center;color:#ef4444">Failed to load data</td></tr></tbody>';
        return;
    }

    rcRender();
}

function rcRender() {
    if (!rcData) return;
    const { staff, reports, daysInMonth, year, month, todayDay, todayMonth, todayYear } = rcData;
    const query   = (document.getElementById('rcSearch').value || '').toLowerCase();
    const isCurrentMonth = (year === todayYear && month === todayMonth);

    // Summary stats
    let totSubmitted = 0, totMissing = 0, totLate = 0, totWorking = 0;
    staff.forEach(s => {
        for (let d = 1; d <= daysInMonth; d++) {
            const st = reports[s.id]?.[d]?.status;
            if (st === 'submitted') totSubmitted++;
            else if (st === 'missing') totMissing++;
            else if (st === 'late') totLate++;
            if (st !== 'weekend' && st !== 'future') totWorking++;
        }
    });
    const compliance = totWorking > 0 ? Math.round((totSubmitted + totLate) / totWorking * 100) : 0;

    document.getElementById('rcSummary').innerHTML = `
        <div class="rc-chip">
            <div class="rc-chip-dot" style="background:var(--rc-teal)"></div>
            <div><div class="rc-chip-val">${staff.length}</div><div class="rc-chip-lbl">Staff</div></div>
        </div>
        <div class="rc-chip">
            <div class="rc-chip-dot" style="background:var(--cell-submitted)"></div>
            <div><div class="rc-chip-val" style="color:var(--rc-green)">${totSubmitted}</div><div class="rc-chip-lbl">Submitted</div></div>
        </div>
        <div class="rc-chip">
            <div class="rc-chip-dot" style="background:var(--cell-missing)"></div>
            <div><div class="rc-chip-val" style="color:var(--rc-red)">${totMissing}</div><div class="rc-chip-lbl">Missing</div></div>
        </div>
        <div class="rc-chip">
            <div class="rc-chip-dot" style="background:var(--cell-late)"></div>
            <div><div class="rc-chip-val" style="color:var(--rc-amber)">${totLate}</div><div class="rc-chip-lbl">Late</div></div>
        </div>
        <div class="rc-chip">
            <div class="rc-chip-dot" style="background:var(--rc-teal)"></div>
            <div><div class="rc-chip-val">${compliance}%</div><div class="rc-chip-lbl">Compliance</div></div>
        </div>`;

    // Table header
    let html = '<thead><tr><th>Staff</th>';
    for (let d = 1; d <= daysInMonth; d++) {
        const date = new Date(year, month, d);
        const dow  = date.getDay();
        const isWknd  = dow === 0 || dow === 6;
        const isToday = isCurrentMonth && d === todayDay;
        html += `<th class="${isToday ? 'rc-today-hd' : isWknd ? 'rc-wknd-hd' : ''}">${d}</th>`;
    }
    html += '<th>Rate</th></tr></thead>';

    // Body
    const filtered = staff.filter(s =>
        s.name.toLowerCase().includes(query) || s.role.toLowerCase().includes(query)
    );

    html += '<tbody>';
    filtered.forEach(s => {
        let submitted = 0, workDays = 0;
        for (let d = 1; d <= daysInMonth; d++) {
            const st = reports[s.id]?.[d]?.status;
            if (st !== 'weekend' && st !== 'future') workDays++;
            if (st === 'submitted' || st === 'late') submitted++;
        }
        const pct      = workDays > 0 ? Math.round(submitted / workDays * 100) : 0;
        const pctColor = pct >= 90 ? 'var(--rc-green)' : pct >= 70 ? 'var(--rc-amber)' : 'var(--rc-red)';
        const barColor = pct >= 90 ? 'var(--cell-submitted)' : pct >= 70 ? 'var(--cell-late)' : 'var(--cell-missing)';

        html += `<tr><td>
            <div class="rc-staff-cell">
                <div class="rc-avatar" style="background:${s.color}">${s.initials}</div>
                <div><div class="rc-staff-name">${rcEsc(s.name)}</div><div class="rc-staff-role">${rcEsc(s.role)}</div></div>
            </div></td>`;

        for (let d = 1; d <= daysInMonth; d++) {
            const entry   = reports[s.id]?.[d] || { status: 'future' };
            const isToday = isCurrentMonth && d === todayDay;
            const cls     = entry.status + (isToday ? ' today-ring' : '');
            html += `<td><div class="rc-cell ${cls}"
                data-sid="${s.id}" data-name="${rcEsc(s.name)}" data-role="${rcEsc(s.role)}" data-color="${s.color}" data-initials="${s.initials}"
                data-day="${d}" data-status="${entry.status}" data-time="${entry.time || ''}"
                onmouseenter="rcShowTooltip(event,${d})" onmouseleave="rcHideTooltip()"
                onclick="rcOpenModal(${s.id},${d})"></div></td>`;
        }

        html += `<td><div class="rc-rate-cell">
            <div class="rc-bar-track"><div class="rc-bar-fill" style="width:${pct}%;background:${barColor}"></div></div>
            <div class="rc-rate-pct" style="color:${pctColor}">${pct}%</div>
        </div></td></tr>`;
    });

    // Daily % summary row
    html += '<tr class="rc-daily-row"><td>Daily %</td>';
    for (let d = 1; d <= daysInMonth; d++) {
        const date     = new Date(year, month, d);
        const isWknd   = date.getDay() === 0 || date.getDay() === 6;
        const isFuture = isCurrentMonth ? d > todayDay : (year > todayYear || (year === todayYear && month > todayMonth));

        if (isWknd || isFuture) {
            html += '<td><div class="rc-dpct">—</div></td>';
        } else {
            let dayOk = 0;
            staff.forEach(s => {
                const st = reports[s.id]?.[d]?.status;
                if (st === 'submitted' || st === 'late') dayOk++;
            });
            const dp  = staff.length > 0 ? Math.round(dayOk / staff.length * 100) : 0;
            const cls = dp >= 90 ? 'good' : dp >= 70 ? 'warn' : 'bad';
            html += `<td><div class="rc-dpct ${cls}">${dp}%</div></td>`;
        }
    }
    html += '<td></td></tr></tbody>';

    document.getElementById('rcTable').innerHTML = html;
}

// Tooltip
const rcTip = document.getElementById('rcTooltip');
function rcShowTooltip(e, day) {
    const el     = e.currentTarget;
    const status = el.dataset.status;
    if (status === 'weekend' || status === 'future') return;
    const name = el.dataset.name;
    const time = el.dataset.time;
    const label = status === 'submitted' ? '✓ Submitted' : status === 'late' ? '⏰ Late' : '✗ Missing';
    rcTip.innerHTML = `<div style="font-weight:700">${name} · ${day} ${RC_MONTHS[rcMonth].slice(0,3)}</div>
        <div style="opacity:.85;margin-top:2px">${label}${time ? ' · ' + time : ''}</div>`;
    rcTip.classList.add('visible');
    const r = el.getBoundingClientRect();
    rcTip.style.left = (r.left + r.width / 2 - rcTip.offsetWidth / 2) + 'px';
    rcTip.style.top  = (r.top - rcTip.offsetHeight - 8) + 'px';
}
function rcHideTooltip() { rcTip.classList.remove('visible'); }

// Modal
function rcOpenModal(staffId, day) {
    if (!rcData) return;
    const { reports, year, month } = rcData;
    const entry = reports[staffId]?.[day];
    if (!entry || entry.status === 'weekend' || entry.status === 'future') return;

    const s = rcData.staff.find(x => x.id === staffId);
    const date = new Date(year, month, day);

    // Calc consecutive streak ending at this day
    let streak = 0;
    for (let d = day; d >= 1; d--) {
        const st = reports[staffId]?.[d]?.status;
        if (st === 'submitted' || st === 'late') streak++;
        else if (st === 'missing') break;
    }

    const sColors = { submitted: 'var(--rc-green)', late: 'var(--rc-amber)', missing: 'var(--rc-red)' };
    const sLabels = { submitted: 'Submitted', late: 'Late Submission', missing: 'Not Submitted' };

    document.getElementById('rcModal').innerHTML = `
        <button class="rc-modal-close" onclick="rcCloseModal()">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px">
            <div class="rc-avatar" style="background:${s.color};width:38px;height:38px;font-size:13px;border-radius:50%">${s.initials}</div>
            <div>
                <div style="font-size:1rem;font-weight:700;color:#0f172a">${rcEsc(s.name)}</div>
                <div style="font-size:.75rem;color:#94a3b8">${rcEsc(s.role)}</div>
            </div>
        </div>
        <div style="font-size:.78rem;color:#64748b;margin-bottom:14px">${RC_DAYS[date.getDay()]}, ${day} ${RC_MONTHS[month]} ${year}</div>
        <div class="rc-modal-stat">
            <div class="rc-modal-dot" style="background:${sColors[entry.status]}"></div>
            <div class="rc-modal-lbl">Status</div>
            <div class="rc-modal-val" style="color:${sColors[entry.status]}">${sLabels[entry.status]}</div>
        </div>
        ${entry.time ? `<div class="rc-modal-stat">
            <div class="rc-modal-dot" style="background:#94a3b8"></div>
            <div class="rc-modal-lbl">Submitted at</div>
            <div class="rc-modal-val">${entry.time}</div>
        </div>` : ''}
        <div class="rc-modal-stat">
            <div class="rc-modal-dot" style="background:var(--rc-teal)"></div>
            <div class="rc-modal-lbl">Streak (this month)</div>
            <div class="rc-modal-val">${streak} day${streak !== 1 ? 's' : ''}</div>
        </div>`;

    document.getElementById('rcModalOverlay').classList.add('open');
}
function rcCloseModal() { document.getElementById('rcModalOverlay').classList.remove('open'); }

// CSV export
function rcExportCSV() {
    if (!rcData) return;
    const { staff, reports, daysInMonth, year, month } = rcData;
    const days = Array.from({ length: daysInMonth }, (_, i) => i + 1);
    let csv = `Staff,Role,${days.join(',')},Rate\n`;
    staff.forEach(s => {
        let ok = 0, work = 0;
        let row = `"${s.name}","${s.role}"`;
        days.forEach(d => {
            const st = reports[s.id]?.[d]?.status || 'future';
            row += ',' + st;
            if (st !== 'weekend' && st !== 'future') work++;
            if (st === 'submitted' || st === 'late') ok++;
        });
        const pct = work > 0 ? Math.round(ok / work * 100) : 0;
        csv += row + `,${pct}%\n`;
    });
    const blob = new Blob([csv], { type: 'text/csv' });
    const a    = document.createElement('a');
    a.href     = URL.createObjectURL(blob);
    a.download = `report-calendar-${RC_MONTHS[month]}-${year}.csv`;
    a.click();
}

function rcEsc(s) {
    const d = document.createElement('div'); d.textContent = s || ''; return d.innerHTML;
}
</script>

</x-layouts.app>
