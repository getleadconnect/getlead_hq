{{-- HR › Staff Section › Dashboard --}}
<div class="sd-grid">
    <div class="sd-card">
        <div class="sd-head">
            <div class="sd-ico blue"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
            <div class="sd-label">Days Present</div>
        </div>
        <div class="sd-figure">{{ $stats['days_present'] }}<span class="sd-sub">/{{ $stats['working_days'] }}</span></div>
    </div>

    <div class="sd-card">
        <div class="sd-head">
            <div class="sd-ico green"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
            <div class="sd-label">Leave Balance</div>
        </div>
        <div class="sd-figure">{{ $stats['leave_taken'] }}<span class="sd-sub">/{{ $stats['total_leave'] }}</span></div>
    </div>

    <div class="sd-card">
        <div class="sd-head">
            <div class="sd-ico amber"><svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
            <div class="sd-label link">Hours This Month</div>
        </div>
        <div class="sd-figure">{{ $stats['hours_this_month'] }}<span class="sd-sub">h</span></div>
    </div>

    <div class="sd-card">
        <div class="sd-head">
            <div class="sd-ico red"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg></div>
            <div class="sd-label">Pending Requests</div>
        </div>
        <div class="sd-figure">{{ $stats['pending_requests'] }}</div>
    </div>
</div>

<div class="sd-row2">
    <div class="sd-actions">
        <h2>Quick Actions</h2>
        <div class="sd-btns">
            <button type="button" class="qa-btn primary" data-goto-tab="attendance">
                <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                Mark Attendance
            </button>
            <button type="button" class="qa-btn" data-goto-tab="leave-management">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="12" y1="18" x2="12" y2="12"/><line x1="9" y1="15" x2="15" y2="15"/></svg>
                Apply for Leave
            </button>
            <button type="button" class="qa-btn" data-goto-tab="attendance">
                <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                View Reports
            </button>
        </div>
    </div>

    <div class="sd-chart">
        <div class="ch-head">
            <h2>Working Hours</h2>
            <div class="ch-nav">
                <button type="button" id="hrsPrev" aria-label="Previous month"><svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></button>
                <span class="ch-lbl" id="hrsLabel">{{ $charts['month_label'] }}</span>
                <button type="button" id="hrsNext" aria-label="Next month"><svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></button>
            </div>
        </div>
        <div class="cv"><canvas id="chartHours"></canvas></div>
    </div>
</div>

{{-- Analytics charts (current year, 12 months) --}}
<style>
    .sd-row2 { display:grid; grid-template-columns:1fr 1fr; gap:16px; align-items:stretch; }
    @media(max-width:900px){ .sd-row2{ grid-template-columns:1fr; } }
    .sd-charts { display:grid; grid-template-columns:1fr 1fr; gap:16px; margin-top:16px; }
    @media(max-width:900px){ .sd-charts{ grid-template-columns:1fr; } }
    .sd-chart { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:18px 20px; }
    .sd-chart .ch-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
    .sd-chart h2 { font-size:15px; font-weight:600; color:var(--text-1); }
    .sd-chart .yr { font-size:11.5px; font-weight:600; color:var(--text-3); background:var(--bg-neutral); border:1px solid var(--border); padding:2px 9px; border-radius:var(--radius-pill); }
    .sd-chart .cv { position:relative; height:240px; }
    /* Chart controls */
    .ch-nav { display:flex; align-items:center; gap:8px; }
    .ch-nav button { width:26px; height:26px; display:flex; align-items:center; justify-content:center; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--bg-card); cursor:pointer; color:var(--text-2); }
    .ch-nav button:hover { border-color:var(--text-3); color:var(--text-1); }
    .ch-nav button svg { width:14px; height:14px; stroke:currentColor; fill:none; stroke-width:2; }
    .ch-nav .ch-lbl { font-size:12.5px; font-weight:600; color:var(--text-1); min-width:96px; text-align:center; }
    .ch-year { height:28px; border:1px solid var(--border); border-radius:var(--radius-sm); padding:0 8px; font-family:inherit; font-size:12.5px; font-weight:600; color:var(--text-1); background:var(--bg-card); outline:none; cursor:pointer; }
    .ch-year:focus { border-color:var(--brand-red); box-shadow:0 0 0 3px var(--brand-red-soft); }
</style>

<div class="sd-charts">
    <div class="sd-chart">
        <div class="ch-head">
            <h2>Attendance</h2>
            <select class="ch-year" data-chart="att">
                @foreach($charts['years'] as $y)<option value="{{ $y }}" @selected($y === $charts['year'])>{{ $y }}</option>@endforeach
            </select>
        </div>
        <div class="cv"><canvas id="chartAttendance"></canvas></div>
    </div>
    <div class="sd-chart">
        <div class="ch-head">
            <h2>Leave</h2>
            <select class="ch-year" data-chart="leave">
                @foreach($charts['years'] as $y)<option value="{{ $y }}" @selected($y === $charts['year'])>{{ $y }}</option>@endforeach
            </select>
        </div>
        <div class="cv"><canvas id="chartLeave"></canvas></div>
    </div>
</div>

<script src="{{ asset('assets/chartjs/chart.umd.min.js') }}"></script>
<script>
(function () {
    if (typeof Chart === 'undefined') return;
    const LABELS = @json($charts['labels']);
    const ATT = @json($charts['attendance']);
    const LEAVE = @json($charts['leave']);
    const HRS_LABELS = @json($charts['hours_labels']);
    const HRS = @json($charts['hours']);

    // Theme-aware colours pulled from CSS variables.
    const css = getComputedStyle(document.documentElement);
    const cvar = (n, f) => (css.getPropertyValue(n).trim() || f);
    const red = cvar('--brand-red', '#DC2626');
    const grid = 'rgba(148,163,184,.18)';
    const tick = cvar('--text-2', '#475569');

    const baseOpts = (yTitle) => ({
        responsive: true, maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { display: false }, ticks: { color: tick, font: { size: 11 } } },
            y: { beginAtZero: true, grid: { color: grid }, ticks: { color: tick, precision: 0, font: { size: 11 } }, title: { display: true, text: yTitle, color: tick, font: { size: 11 } } },
        },
    });

    const attChart = new Chart(document.getElementById('chartAttendance'), {
        type: 'bar',
        data: { labels: LABELS, datasets: [{ data: ATT, backgroundColor: red, borderRadius: 5, maxBarThickness: 26 }] },
        options: baseOpts('Days Present'),
    });

    const leaveChart = new Chart(document.getElementById('chartLeave'), {
        type: 'line',
        data: { labels: LABELS, datasets: [{ data: LEAVE, borderColor: red, backgroundColor: 'rgba(220,38,38,.12)', borderWidth: 2, fill: true, tension: .35, pointBackgroundColor: red, pointRadius: 3 }] },
        options: baseOpts('Leave Days'),
    });

    // Working hours — per day, month-navigable.
    const hoursChart = new Chart(document.getElementById('chartHours'), {
        type: 'bar',
        data: { labels: HRS_LABELS, datasets: [{ data: HRS, backgroundColor: red, borderRadius: 4, maxBarThickness: 14 }] },
        options: baseOpts('Hours'),
    });

    // ── Year selector → reload Attendance / Leave for a year ───────
    const YEARLY_URL = "{{ route('hr.staff.dashboard.yearly') }}";
    document.querySelectorAll('.ch-year').forEach(sel => sel.addEventListener('change', function () {
        fetch(YEARLY_URL + '?year=' + this.value, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json()).then(res => {
                if (!res.success) return;
                if (this.dataset.chart === 'att') { attChart.data.datasets[0].data = res.data.attendance; attChart.update(); }
                else { leaveChart.data.datasets[0].data = res.data.leave; leaveChart.update(); }
            });
    }));

    // ── Month navigator → reload Working Hours ─────────────────────
    const HOURS_URL = "{{ route('hr.staff.dashboard.hours') }}";
    let hMonth = {{ $charts['month'] }}, hYear = {{ $charts['year'] }};
    function loadHours() {
        fetch(HOURS_URL + '?month=' + hMonth + '&year=' + hYear, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json()).then(res => {
                if (!res.success) return;
                document.getElementById('hrsLabel').textContent = res.data.label;
                hoursChart.data.labels = res.data.labels;
                hoursChart.data.datasets[0].data = res.data.hours;
                hoursChart.update();
            });
    }
    document.getElementById('hrsPrev').addEventListener('click', () => { hMonth--; if (hMonth < 1) { hMonth = 12; hYear--; } loadHours(); });
    document.getElementById('hrsNext').addEventListener('click', () => { hMonth++; if (hMonth > 12) { hMonth = 1; hYear++; } loadHours(); });
})();
</script>
