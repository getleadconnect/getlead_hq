<x-layouts.app title="Analytics">

@push('styles')
<style>
/* ── Analytics Page ── */
.an-page { padding:20px; }

.an-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
.an-title   { font-size:1.25rem; font-weight:700; color:#0f172a; }
.an-sub     { font-size:.78rem; color:#64748b; margin-top:2px; }

/* Date Range Picker */
.an-range { display:flex; align-items:center; gap:8px; flex-wrap:wrap; }
.an-range-select { padding:7px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:.8rem; font-family:inherit; background:#fff; height:36px; cursor:pointer; }
.an-range-select:focus { outline:none; border-color:#14b8a6; }
.an-range-dates { display:flex; align-items:center; gap:6px; }
.an-range-dates input[type="date"] { padding:7px 10px; border:1px solid #e2e8f0; border-radius:8px; font-size:.8rem; font-family:inherit; background:#fff; height:36px; }
.an-btn { display:inline-flex; align-items:center; gap:6px; padding:7px 14px; border-radius:8px; font-size:.8rem; font-weight:500; cursor:pointer; border:1px solid #14b8a6; background:#14b8a6; color:#fff; font-family:inherit; transition:background .15s; height:36px; }
.an-btn:hover { background:#0d9488; }

/* Section */
.an-section { margin-bottom:28px; }
.an-section-title { font-size:.88rem; font-weight:600; color:#0f172a; margin-bottom:14px; padding-bottom:8px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; gap:7px; }

/* KPI Grid */
.an-kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(170px,1fr)); gap:12px; margin-bottom:16px; }
.an-kpi { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:18px 16px; position:relative; overflow:hidden; transition:all .2s; }
.an-kpi:hover { transform:translateY(-2px); box-shadow:0 6px 24px rgba(0,0,0,.06); }
.an-kpi::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,#14b8a6,#2dd4bf); opacity:0; transition:opacity .2s; }
.an-kpi:hover::before { opacity:1; }
.an-kpi-label { font-size:.7rem; font-weight:600; text-transform:uppercase; letter-spacing:.04em; color:#64748b; margin-bottom:8px; }
.an-kpi-value { font-size:1.8rem; font-weight:700; color:#0f172a; letter-spacing:-.02em; line-height:1.1; }
.an-kpi-sub { font-size:.73rem; color:#64748b; margin-top:5px; }
.an-kpi-value.c-green  { color:#10b981; }
.an-kpi-value.c-teal   { color:#14b8a6; }
.an-kpi-value.c-blue   { color:#0ea5e9; }
.an-kpi-value.c-red    { color:#ef4444; }
.an-kpi-value.c-amber  { color:#f59e0b; }

/* Grid 2 */
.an-grid2 { display:grid; grid-template-columns:repeat(auto-fit,minmax(300px,1fr)); gap:14px; margin-bottom:14px; }

/* Cards */
.an-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; }
.an-card-head { padding:14px 18px; border-bottom:1px solid #e2e8f0; font-size:.82rem; font-weight:600; color:#0f172a; display:flex; align-items:center; gap:7px; }
.an-card-body { padding:16px 18px; }

/* Bar chart */
.an-bar-chart {}
.an-bar-row { display:flex; align-items:center; gap:10px; margin-bottom:10px; font-size:.8rem; }
.an-bar-label { width:120px; color:#64748b; font-weight:500; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; flex-shrink:0; }
.an-bar-track { flex:1; height:9px; background:#f1f5f9; border-radius:5px; overflow:hidden; }
.an-bar-fill  { height:100%; border-radius:5px; transition:width .6s cubic-bezier(.4,0,.2,1); }
.an-bar-value { width:70px; text-align:right; font-weight:600; color:#0f172a; font-size:.78rem; flex-shrink:0; }

/* Trend chart */
.an-trend { }
.an-trend-legend { display:flex; gap:16px; margin-bottom:12px; font-size:.75rem; }
.an-trend-item { display:flex; align-items:center; gap:5px; color:#64748b; }
.an-trend-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.an-trend-bars { display:flex; align-items:flex-end; gap:6px; height:130px; padding-bottom:22px; border-bottom:1px solid #e2e8f0; position:relative; overflow:hidden; }
.an-trend-col  { flex:1; display:flex; flex-direction:column; align-items:center; min-width:0; }
.an-trend-group { display:flex; gap:2px; align-items:flex-end; height:108px; width:100%; justify-content:center; }
.an-trend-bar   { width:10px; border-radius:3px 3px 0 0; transition:height .5s ease; min-height:3px; }
.an-trend-lbl   { font-size:.6rem; color:#94a3b8; margin-top:4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:100%; text-align:center; }

/* Funnel */
.an-funnel { }
.an-funnel-step { display:flex; align-items:center; gap:10px; margin-bottom:10px; font-size:.82rem; }
.an-funnel-bar  { height:30px; border-radius:6px; background:linear-gradient(90deg,#14b8a6,#2dd4bf); transition:width .6s ease; min-width:4px; }
.an-funnel-info { display:flex; justify-content:space-between; flex:1; }
.an-funnel-label { color:#64748b; font-weight:500; }
.an-funnel-count { font-weight:700; color:#0f172a; }

/* Progress Ring */
.an-ring-wrap  { display:flex; flex-direction:column; align-items:center; gap:5px; }
.an-ring-label { font-size:.68rem; color:#64748b; text-transform:uppercase; letter-spacing:.03em; }

/* Team cards */
.an-team-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(270px,1fr)); gap:14px; }
.an-team-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:18px; transition:all .2s; }
.an-team-card:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,.06); border-color:rgba(20,184,166,.2); }
.an-team-head { display:flex; align-items:center; gap:12px; margin-bottom:14px; }
.an-team-avatar { width:42px; height:42px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.88rem; font-weight:600; flex-shrink:0; }
.an-team-name { font-weight:600; font-size:.87rem; color:#0f172a; }
.an-team-role { font-size:.73rem; color:#64748b; margin-top:1px; }
.an-team-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:8px; }
.an-stat { text-align:center; }
.an-stat-val { font-size:1.05rem; font-weight:700; color:#0f172a; display:block; }
.an-stat-lbl  { font-size:.65rem; color:#64748b; text-transform:uppercase; letter-spacing:.02em; }

/* HR stats */
.an-hr-stats { display:grid; grid-template-columns:repeat(2,1fr); gap:12px; margin-bottom:16px; }
.an-hr-stat  { text-align:center; padding:14px; background:#f8fafc; border-radius:10px; border:1px solid #e2e8f0; }
.an-hr-val   { font-size:1.55rem; font-weight:700; margin-bottom:3px; }
.an-hr-lbl   { font-size:.7rem; color:#64748b; text-transform:uppercase; letter-spacing:.03em; }

/* Streak */
.an-streak-list {}
.an-streak-row { display:flex; align-items:center; gap:10px; padding:9px 0; border-bottom:1px solid #f1f5f9; }
.an-streak-row:last-child { border-bottom:none; }
.an-streak-rank { width:28px; text-align:center; font-weight:600; font-size:.88rem; }
.an-streak-name { flex:1; font-size:.84rem; font-weight:500; color:#0f172a; }
.an-streak-badge { display:inline-flex; align-items:center; padding:3px 9px; border-radius:20px; font-size:.68rem; font-weight:600; background:rgba(20,184,166,.1); color:#0f766e; }

/* Skeleton */
.an-skel { background:linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%); background-size:200% 100%; animation:an-shimmer 1.4s infinite; border-radius:8px; }
@keyframes an-shimmer { 0%{background-position:200% 0} 100%{background-position:-200% 0} }
.an-skel-line { height:14px; margin-bottom:8px; }
.an-skel-kpi  { height:80px; border-radius:12px; }

/* Empty */
.an-empty { text-align:center; padding:36px 20px; color:#94a3b8; font-size:.84rem; }
</style>
@endpush

<div class="an-page">

    {{-- Header --}}
    <div class="an-header">
        <div>
            <div class="an-title">Analytics</div>
            <div class="an-sub">Performance metrics across tasks, reports, and team</div>
        </div>
        <div class="an-range">
            <select class="an-range-select" id="anRange" onchange="anRangeChanged()">
                <option value="this_week">This Week</option>
                <option value="this_month" selected>This Month</option>
                <option value="last_month">Last Month</option>
                <option value="custom">Custom Range</option>
            </select>
            <span id="anCustomDates" style="display:none" class="an-range-dates">
                <input type="date" id="anFrom">
                <span style="color:#64748b;font-size:.8rem">to</span>
                <input type="date" id="anTo">
                <button class="an-btn" onclick="anLoadAll()">Go</button>
            </span>
        </div>
    </div>

    {{-- Task Analytics --}}
    <div class="an-section">
        <div class="an-section-title">📋 Task Analytics</div>
        <div class="an-kpi-grid" id="anTaskKpis">
            <div class="an-kpi"><div class="an-skel an-skel-kpi"></div></div>
            <div class="an-kpi"><div class="an-skel an-skel-kpi"></div></div>
            <div class="an-kpi"><div class="an-skel an-skel-kpi"></div></div>
            <div class="an-kpi"><div class="an-skel an-skel-kpi"></div></div>
        </div>
        <div class="an-grid2">
            <div class="an-card">
                <div class="an-card-head">Completion Rate by Person</div>
                <div class="an-card-body" id="anTasksByPerson">
                    <div class="an-skel an-skel-line"></div>
                    <div class="an-skel an-skel-line" style="width:80%"></div>
                    <div class="an-skel an-skel-line" style="width:60%"></div>
                </div>
            </div>
            <div class="an-card">
                <div class="an-card-head">Tasks by Category</div>
                <div class="an-card-body" id="anTasksByCategory">
                    <div class="an-skel an-skel-line"></div>
                    <div class="an-skel an-skel-line" style="width:70%"></div>
                </div>
            </div>
        </div>
        <div class="an-card">
            <div class="an-card-head">Task Creation vs Completion Trend</div>
            <div class="an-card-body" id="anTaskTrend">
                <div class="an-skel an-skel-line"></div>
            </div>
        </div>
    </div>

    {{-- Report Analytics --}}
    <div class="an-section" style="margin-top:14px">
        <div class="an-section-title">📝 Report Analytics</div>
        <div class="an-grid2">
            <div class="an-card">
                <div class="an-card-head">Submission Rate by Person</div>
                <div class="an-card-body" id="anReportsByPerson">
                    <div class="an-skel an-skel-line"></div>
                    <div class="an-skel an-skel-line" style="width:80%"></div>
                </div>
            </div>
            <div class="an-card">
                <div class="an-card-head">🔥 Streak Leaderboard</div>
                <div class="an-card-body" id="anStreakBoard">
                    <div class="an-skel an-skel-line"></div>
                    <div class="an-skel an-skel-line" style="width:70%"></div>
                </div>
            </div>
        </div>
        <div class="an-card" style="margin-top:14px">
            <div class="an-card-head">Daily Submission Trend</div>
            <div class="an-card-body" id="anReportTrend">
                <div class="an-skel an-skel-line"></div>
            </div>
        </div>
    </div>

    {{-- Team Performance --}}
    <div class="an-section" style="margin-top:14px">
        <div class="an-section-title">👥 Team Performance</div>
        <div id="anTeam">
            <div class="an-team-grid">
                <div class="an-team-card"><div class="an-skel" style="height:100px;border-radius:10px"></div></div>
                <div class="an-team-card"><div class="an-skel" style="height:100px;border-radius:10px"></div></div>
                <div class="an-team-card"><div class="an-skel" style="height:100px;border-radius:10px"></div></div>
            </div>
        </div>
    </div>

    {{-- HR Analytics --}}
    <div class="an-section" style="margin-top:14px">
        <div class="an-section-title">🏢 HR Analytics <span style="font-size:.7rem;font-weight:400;color:#94a3b8">(Based on daily report submissions)</span></div>
        <div class="an-grid2">
            <div class="an-card">
                <div class="an-card-head">Attendance Overview</div>
                <div class="an-card-body" id="anHrAttendance">
                    <div class="an-skel an-skel-line"></div>
                    <div class="an-skel an-skel-line" style="width:70%"></div>
                </div>
            </div>
            <div class="an-card">
                <div class="an-card-head">Absent / Low Submission</div>
                <div class="an-card-body" id="anHrLeave">
                    <div class="an-skel an-skel-line"></div>
                    <div class="an-skel an-skel-line" style="width:60%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Marketing --}}
    <div class="an-section" style="margin-top:14px">
        <div class="an-section-title">📈 Customer & Marketing</div>
        <div class="an-kpi-grid" id="anMktKpis">
            <div class="an-kpi"><div class="an-skel an-skel-kpi"></div></div>
            <div class="an-kpi"><div class="an-skel an-skel-kpi"></div></div>
            <div class="an-kpi"><div class="an-skel an-skel-kpi"></div></div>
            <div class="an-kpi"><div class="an-skel an-skel-kpi"></div></div>
        </div>
        <div class="an-grid2">
            <div class="an-card">
                <div class="an-card-head">Conversion Funnel</div>
                <div class="an-card-body" id="anMktFunnel">
                    <div class="an-skel an-skel-line"></div>
                    <div class="an-skel an-skel-line" style="width:80%"></div>
                </div>
            </div>
            <div class="an-card">
                <div class="an-card-head">Subscription Breakdown</div>
                <div class="an-card-body" id="anMktCampaigns">
                    <div class="an-skel an-skel-line"></div>
                    <div class="an-skel an-skel-line" style="width:70%"></div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
const AN_CSRF = document.querySelector('meta[name="csrf-token"]')?.content || '';

// ── Helpers ────────────────────────────────────────────────────────
function anEsc(s){ const d=document.createElement('div'); d.textContent=s||''; return d.innerHTML; }

function fmtNum(n){
    n = Number(n) || 0;
    if(n >= 10000000) return (n/10000000).toFixed(1) + 'Cr';
    if(n >= 100000)   return (n/100000).toFixed(1)   + 'L';
    if(n >= 1000)     return (n/1000).toFixed(1)     + 'K';
    return n;
}

function fmtCurr(n){
    n = Number(n) || 0;
    if(n >= 10000000) return '₹' + (n/10000000).toFixed(2) + 'Cr';
    if(n >= 100000)   return '₹' + (n/100000).toFixed(1)   + 'L';
    if(n >= 1000)     return '₹' + (n/1000).toFixed(1)     + 'K';
    return '₹' + n;
}

// ── Date range helpers ─────────────────────────────────────────────
function getDateRange(){
    const v   = document.getElementById('anRange').value;
    const now = new Date();
    let from, to;
    if(v === 'this_week'){
        const d = now.getDay(), diff = now.getDate() - d + (d === 0 ? -6 : 1);
        from = new Date(now.getFullYear(), now.getMonth(), diff);
        to   = now;
    } else if(v === 'this_month'){
        from = new Date(now.getFullYear(), now.getMonth(), 1);
        to   = now;
    } else if(v === 'last_month'){
        from = new Date(now.getFullYear(), now.getMonth() - 1, 1);
        to   = new Date(now.getFullYear(), now.getMonth(), 0);
    } else {
        from = new Date(document.getElementById('anFrom').value || now);
        to   = new Date(document.getElementById('anTo').value   || now);
    }
    const fmt = d => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
    return { from: fmt(from), to: fmt(to) };
}

function anRangeChanged(){
    const custom = document.getElementById('anRange').value === 'custom';
    document.getElementById('anCustomDates').style.display = custom ? 'flex' : 'none';
    if(!custom) anLoadAll();
}

async function anGet(url, params = {}){
    const u = url + '?' + new URLSearchParams(params);
    const r = await fetch(u, { headers:{ 'X-CSRF-TOKEN': AN_CSRF, Accept:'application/json' } });
    return r.json();
}

// ── Chart builders ─────────────────────────────────────────────────
function barChart(items){
    if(!items.length) return '<div class="an-empty">No data</div>';
    const mx = Math.max(...items.map(i => i.value)) || 1;
    return '<div class="an-bar-chart">' + items.map(i => {
        const pct   = Math.round((i.value / mx) * 100);
        const color = i.color || '#14b8a6';
        return `<div class="an-bar-row">
            <div class="an-bar-label" title="${anEsc(i.label)}">${anEsc(i.label)}</div>
            <div class="an-bar-track"><div class="an-bar-fill" style="width:${pct}%;background:${color}"></div></div>
            <div class="an-bar-value">${i.display !== undefined ? i.display : i.value}</div>
        </div>`;
    }).join('') + '</div>';
}

function trendChart(data, label1, label2){
    if(!data.length) return '<div class="an-empty">No data</div>';
    const mx = Math.max(...data.map(d => Math.max(d.v1||0, d.v2||0))) || 1;
    const legend = `<div class="an-trend-legend">
        <span class="an-trend-item"><span class="an-trend-dot" style="background:#14b8a6"></span>${anEsc(label1)}</span>
        ${label2 ? `<span class="an-trend-item"><span class="an-trend-dot" style="background:#0ea5e9"></span>${anEsc(label2)}</span>` : ''}
    </div>`;
    const bars = data.map(d => {
        const h1 = Math.round((d.v1 / mx) * 100);
        const h2 = d.v2 !== undefined ? Math.round((d.v2 / mx) * 100) : 0;
        const shortLbl = (d.label||'').substring(5); // strip year
        return `<div class="an-trend-col">
            <div class="an-trend-group">
                <div class="an-trend-bar" style="height:${h1}%;background:#14b8a6" title="${label1}: ${d.v1}"></div>
                ${label2 ? `<div class="an-trend-bar" style="height:${h2}%;background:#0ea5e9" title="${label2}: ${d.v2}"></div>` : ''}
            </div>
            <div class="an-trend-lbl">${anEsc(shortLbl)}</div>
        </div>`;
    }).join('');
    return `${legend}<div class="an-trend-bars">${bars}</div>`;
}

function progressRing(pct, color){
    color = color || '#14b8a6';
    const size=72, stroke=6, r=(size-stroke)/2, c=2*Math.PI*r;
    const offset = c - (pct/100)*c;
    return `<svg width="${size}" height="${size}" style="position:relative">
        <circle cx="${size/2}" cy="${size/2}" r="${r}" fill="none" stroke="#f1f5f9" stroke-width="${stroke}"/>
        <circle cx="${size/2}" cy="${size/2}" r="${r}" fill="none" stroke="${color}" stroke-width="${stroke}"
            stroke-dasharray="${c}" stroke-dashoffset="${offset}" stroke-linecap="round"
            transform="rotate(-90 ${size/2} ${size/2})" style="transition:stroke-dashoffset .5s"/>
        <text x="${size/2}" y="${size/2+4}" text-anchor="middle" font-size="13" font-weight="700" fill="${color}">${Math.round(pct)}%</text>
    </svg>`;
}

function funnelChart(data){
    if(!data.length) return '<div class="an-empty">No data</div>';
    const mx = Math.max(...data.map(d=>d.count)) || 1;
    return '<div class="an-funnel">' + data.map(d => {
        const pct = Math.max(6, Math.round((d.count/mx)*100));
        return `<div class="an-funnel-step">
            <div class="an-funnel-bar" style="width:${pct}%;flex-shrink:0"></div>
            <div class="an-funnel-info">
                <span class="an-funnel-label">${anEsc(d.stage)}</span>
                <span class="an-funnel-count">${d.count}</span>
            </div>
        </div>`;
    }).join('') + '</div>';
}

// ── Loaders ────────────────────────────────────────────────────────
async function loadTasks(){
    const r    = getDateRange();
    const data = await anGet('/analytics/api/tasks', r).catch(()=>({ok:false}));
    if(!data.ok){ document.getElementById('anTaskKpis').innerHTML='<div class="an-empty" style="grid-column:1/-1">Failed to load</div>'; return; }

    document.getElementById('anTaskKpis').innerHTML = `
        <div class="an-kpi"><div class="an-kpi-label">Total Tasks</div><div class="an-kpi-value">${data.total_created}</div></div>
        <div class="an-kpi"><div class="an-kpi-label">Completed</div><div class="an-kpi-value c-green">${data.total_completed}</div><div class="an-kpi-sub">${data.completion_rate}% rate</div></div>
        <div class="an-kpi"><div class="an-kpi-label">Avg Completion</div><div class="an-kpi-value c-teal">${data.avg_completion_days}d</div></div>
        <div class="an-kpi"><div class="an-kpi-label">Overdue Now</div><div class="an-kpi-value c-red">${data.overdue_count}</div></div>
    `;

    const byPerson = (data.by_person||[]).map(p => ({
        label:   p.name,
        value:   p.completed,
        display: `${p.completed}/${p.total} (${p.rate}%)`,
        color:   p.rate >= 70 ? '#10b981' : p.rate >= 40 ? '#f59e0b' : '#ef4444',
    }));
    document.getElementById('anTasksByPerson').innerHTML = barChart(byPerson);

    const byCat = (data.by_category||[]).map(c => ({ label: c.category || '—', value: c.count, color: '#14b8a6' }));
    document.getElementById('anTasksByCategory').innerHTML = barChart(byCat);

    document.getElementById('anTaskTrend').innerHTML = trendChart(
        (data.trend||[]).map(t => ({ label: t.date, v1: t.created, v2: t.completed })),
        'Created', 'Completed'
    );
}

async function loadReports(){
    const r    = getDateRange();
    const data = await anGet('/analytics/api/reports', r).catch(()=>({ok:false}));
    if(!data.ok) return;

    const byPerson = (data.by_person||[]).map(p => ({
        label:   p.name,
        value:   p.submitted,
        display: `${p.submitted}/${p.total_days} (${p.rate}%)`,
        color:   p.rate >= 80 ? '#10b981' : p.rate >= 50 ? '#f59e0b' : '#ef4444',
    }));
    document.getElementById('anReportsByPerson').innerHTML = barChart(byPerson);

    const streaks = data.streaks || [];
    document.getElementById('anStreakBoard').innerHTML = streaks.length
        ? '<div class="an-streak-list">' + streaks.map((s,i) =>
            `<div class="an-streak-row">
                <span class="an-streak-rank">${i===0?'🥇':i===1?'🥈':i===2?'🥉':'#'+(i+1)}</span>
                <span class="an-streak-name">${anEsc(s.name)}</span>
                <span class="an-streak-badge">${s.streak} day${s.streak!==1?'s':''}</span>
            </div>`).join('') + '</div>'
        : '<div class="an-empty">No active streaks</div>';

    document.getElementById('anReportTrend').innerHTML = trendChart(
        (data.trend||[]).map(t => ({ label: t.date, v1: t.submitted })),
        'Submitted'
    );
}

async function loadTeam(){
    const r      = getDateRange();
    const data   = await anGet('/analytics/api/team', r).catch(()=>({ok:false}));
    if(!data.ok) return;
    const members = data.members || [];
    if(!members.length){ document.getElementById('anTeam').innerHTML='<div class="an-empty">No data</div>'; return; }

    let html = '<div class="an-team-grid">';
    members.forEach(m => {
        const score = m.productivity_score || 0;
        const color = score >= 70 ? '#10b981' : score >= 40 ? '#f59e0b' : '#ef4444';
        html += `<div class="an-team-card">
            <div class="an-team-head">
                <div class="an-team-avatar" style="background:${color}20;color:${color}">${anEsc(m.initials)}</div>
                <div style="flex:1">
                    <div class="an-team-name">${anEsc(m.name)}</div>
                    <div class="an-team-role">${anEsc(m.role_label)}</div>
                </div>
                ${progressRing(score, color)}
            </div>
            <div class="an-team-stats">
                <div class="an-stat"><span class="an-stat-val">${m.tasks_completed}</span><span class="an-stat-lbl">Done</span></div>
                <div class="an-stat"><span class="an-stat-val">${m.tasks_pending}</span><span class="an-stat-lbl">Pending</span></div>
                <div class="an-stat"><span class="an-stat-val">${m.reports_submitted}</span><span class="an-stat-lbl">Reports</span></div>
                <div class="an-stat"><span class="an-stat-val" style="color:${m.overdue>0?'#ef4444':'#0f172a'}">${m.overdue}</span><span class="an-stat-lbl">Overdue</span></div>
            </div>
        </div>`;
    });
    html += '</div>';
    document.getElementById('anTeam').innerHTML = html;
}

async function loadHr(){
    const r    = getDateRange();
    const data = await anGet('/analytics/api/hr', r).catch(()=>({ok:false}));
    if(!data.ok) return;
    const att = data.attendance || {};

    document.getElementById('anHrAttendance').innerHTML = `
        <div class="an-hr-stats">
            <div class="an-hr-stat"><div class="an-hr-val" style="color:#10b981">${att.present_days||0}</div><div class="an-hr-lbl">Active Days</div></div>
            <div class="an-hr-stat"><div class="an-hr-val" style="color:#ef4444">${att.leave_days||0}</div><div class="an-hr-lbl">Absent Days</div></div>
            <div class="an-hr-stat"><div class="an-hr-val" style="color:#f59e0b">${att.half_days||0}</div><div class="an-hr-lbl">Half Days</div></div>
            <div class="an-hr-stat"><div class="an-hr-val" style="color:#14b8a6">${att.attendance_rate||0}%</div><div class="an-hr-lbl">Rate</div></div>
        </div>
        ${att.trend && att.trend.length ? trendChart((att.trend||[]).map(t=>({label:t.date,v1:t.present})),'Present') : ''}
    `;

    const leaves = data.leave_by_person || [];
    document.getElementById('anHrLeave').innerHTML = leaves.length
        ? barChart(leaves.map(l => ({
            label:   l.name,
            value:   l.total_leaves,
            display: `${l.total_leaves}d`,
            color:   l.total_leaves >= 10 ? '#ef4444' : l.total_leaves >= 5 ? '#f59e0b' : '#14b8a6',
          })))
        : '<div class="an-empty">All staff active 👍</div>';
}

async function loadMarketing(){
    const r    = getDateRange();
    const data = await anGet('/analytics/api/marketing', r).catch(()=>({ok:false}));
    if(!data.ok){ document.getElementById('anMktKpis').innerHTML='<div class="an-empty" style="grid-column:1/-1">Failed to load</div>'; return; }
    const s = data.summary || {};

    document.getElementById('anMktKpis').innerHTML = `
        <div class="an-kpi"><div class="an-kpi-label">Revenue (Active)</div><div class="an-kpi-value c-green">${fmtCurr(s.total_revenue)}</div></div>
        <div class="an-kpi"><div class="an-kpi-label">Registrations</div><div class="an-kpi-value c-teal">${s.total_registrations}</div><div class="an-kpi-sub">this period</div></div>
        <div class="an-kpi"><div class="an-kpi-label">Conversion Rate</div><div class="an-kpi-value c-blue">${s.conversion_rate}%</div></div>
        <div class="an-kpi"><div class="an-kpi-label">Active Trials</div><div class="an-kpi-value">${s.active_leads}</div></div>
    `;

    document.getElementById('anMktFunnel').innerHTML = funnelChart(data.funnel || []);

    const camps = data.campaigns || [];
    document.getElementById('anMktCampaigns').innerHTML = camps.length
        ? barChart(camps.map(c => ({ label: c.name, value: c.leads, color: '#0ea5e9' })))
        : '<div class="an-empty">No subscription data</div>';
}

// ── Load all sections ──────────────────────────────────────────────
function anLoadAll(){
    loadTasks();
    loadReports();
    loadTeam();
    loadHr();
    loadMarketing();
}

anLoadAll();
</script>

</x-layouts.app>
