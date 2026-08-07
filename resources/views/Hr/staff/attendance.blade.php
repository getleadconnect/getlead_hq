{{-- HR › Staff Section › Attendance --}}
<style>
    .att-card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:20px; margin-bottom:18px; }
    .att-card h2 { font-size:15px; font-weight:600; color:var(--text-1); }

    /* Today's attendance */
    .att-today { display:flex; align-items:center; justify-content:space-between; gap:16px; flex-wrap:wrap; }
    .att-today .dt { display:flex; align-items:center; gap:8px; font-size:13px; color:var(--text-2); margin-top:10px; }
    .att-today .dt svg { width:15px; height:15px; stroke:var(--text-3); fill:none; stroke-width:2; }
    .att-today .acts { display:flex; gap:10px; }
    .att-btn { display:inline-flex; align-items:center; gap:7px; padding:9px 15px; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:600; cursor:pointer; border:1px solid var(--border); background:var(--bg-card); color:var(--text-1); }
    .att-btn svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2; }
    .att-btn.in { background:#16A34A; color:#fff; border-color:#16A34A; } .att-btn.in:hover { background:#15803D; }
    .att-btn:hover { border-color:var(--text-3); }
    .att-btn:disabled { opacity:.5; cursor:not-allowed; }

    /* Stat cards */
    .att-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:18px; }
    @media(max-width:900px){ .att-stats{ grid-template-columns:repeat(2,1fr);} }
    .att-stat { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:18px; text-align:center; }
    .att-stat .n { font-size:24px; font-weight:700; letter-spacing:-.02em; }
    .att-stat .l { font-size:12px; color:var(--text-2); margin-top:4px; }
    .att-stat.wd .n { color:var(--text-1); } .att-stat.pr .n { color:#16A34A; } .att-stat.ab .n { color:var(--brand-red); } .att-stat.hr .n { color:#2563EB; }

    /* Calendar */
    .cal-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:14px; }
    .cal-nav { display:flex; align-items:center; gap:10px; }
    .cal-nav button { width:30px; height:30px; display:flex; align-items:center; justify-content:center; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--bg-card); cursor:pointer; color:var(--text-2); }
    .cal-nav button:hover { border-color:var(--text-3); color:var(--text-1); }
    .cal-nav button svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2; }
    .cal-nav .lbl { font-size:13.5px; font-weight:600; color:var(--text-1); min-width:110px; text-align:center; }
    .cal-grid { display:grid; grid-template-columns:repeat(7,1fr); gap:4px; margin-left: 50px; margin-right: 25px; }
    .cal-dow { text-align:left; font-size:13px; font-weight:500; color:var(--text-2); padding:6px 10px; }
    .cal-cell { aspect-ratio:1; display:flex; align-items:center; justify-content:center; height:45px;}
    .cal-day { width:34px; height:34px; display:flex; align-items:center; justify-content:center; border-radius:50%; font-size:13px; color:var(--text-2); }
    .cal-day.muted { color:var(--text-2); }
    .cal-day.present { background:#22C55E; color:#fff; font-weight:600; }
    .cal-day.absent { background:#EF4444; color:#fff; font-weight:600; }
    .cal-day.on_leave { background:#EAB308; color:#fff; font-weight:600; }
    .cal-day.half_day { background:#F97316; color:#fff; font-weight:600; }
    .cal-day.today { border:2px solid #2563EB; color:#2563EB; font-weight:600; }
    .cal-legend { display:flex; gap:18px; flex-wrap:wrap; margin-top:16px; padding-top:14px; border-top:1px solid var(--border-soft); }
    .cal-legend span { display:inline-flex; align-items:center; gap:6px; font-size:12px; color:var(--text-2); }
    .cal-legend i { width:11px; height:11px; border-radius:50%; display:inline-block; }

    /* History */
    .att-hist-head { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:14px; }
    .att-hist-head .sel { display:flex; gap:8px; }
    .att-hist-head select { height:34px; border:1px solid var(--border); border-radius:var(--radius-sm); padding:0 10px; font-family:inherit; font-size:13px; color:var(--text-1); background:var(--bg-card); outline:none; }
    .att-hist-head select:focus { border-color:var(--brand-red); box-shadow:0 0 0 3px var(--brand-red-soft); }
    table.att-tbl { width:100%; border-collapse:collapse; }
    table.att-tbl thead th { text-align:left; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--text-3); padding:11px 14px; background:var(--bg-page); border-bottom:1px solid var(--border); }
    table.att-tbl tbody td { font-size:13px; color:var(--text-2); padding:11px 14px; border-bottom:1px solid var(--border-soft); }
    table.att-tbl tbody tr:last-child td { border-bottom:none; }
    .att-empty { text-align:center; color:var(--text-3); padding:26px; font-size:13.5px; }
    .st-badge { display:inline-block; padding:2px 10px; border-radius:var(--radius-pill); font-size:11.5px; font-weight:600; }
    .st-badge.present { background:#DCFCE7; color:#15803D; } .st-badge.absent { background:var(--danger-soft); color:var(--danger-text); }
    .st-badge.on_leave { background:#FEF9C3; color:#854D0E; } .st-badge.half_day { background:#FFEDD5; color:#9A3412; }

    /* Confirm modal */
    .att-modal { position:fixed; inset:0; background:rgba(15,23,42,.5); z-index:1200; display:none; align-items:center; justify-content:center; padding:20px; }
    .att-modal.open { display:flex; }
    .att-box { background:var(--bg-card); border-radius:var(--radius-lg); width:100%; max-width:400px; box-shadow:0 24px 60px rgba(0,0,0,.25); overflow:hidden; }
    .am-head { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--border-soft); }
    .am-head h3 { font-size:15px; font-weight:600; color:var(--text-1); }
    .am-close { border:none; background:none; font-size:20px; color:var(--text-3); cursor:pointer; }
    .am-body { padding:18px 20px; }
    .am-foot { display:flex; justify-content:flex-end; gap:8px; padding:14px 20px; border-top:1px solid var(--border-soft); background:var(--bg-page); }
</style>

<div id="attRoot" data-loaded="0">
    {{-- Today's Attendance --}}
    <div class="att-card">
        <h2>Today's Attendance</h2>
        <div class="att-today">
            <div class="dt">
                <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                <span id="attTodayDate">—</span>
            </div>
            <div class="acts">
                <button type="button" class="att-btn in" id="attCheckIn">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Check In
                </button>
                <button type="button" class="att-btn" id="attCheckOut">
                    <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    Check Out
                </button>
            </div>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="att-stats">
        <div class="att-stat wd"><div class="n" id="attWorking">0</div><div class="l">Working Days</div></div>
        <div class="att-stat pr"><div class="n" id="attPresent">0</div><div class="l">Present</div></div>
        <div class="att-stat ab"><div class="n" id="attAbsent">0</div><div class="l">Absent</div></div>
        <div class="att-stat hr"><div class="n" id="attHours">0h</div><div class="l">Total Hours</div></div>
    </div>

    {{-- Calendar --}}
    <div class="att-card">
        <div class="cal-head">
            <h2>Attendance Calendar</h2>
            <div class="cal-nav">
                <button type="button" id="calPrev"><svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></button>
                <span class="lbl" id="calLabel">—</span>
                <button type="button" id="calNext"><svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></button>
            </div>
        </div>
        <div class="cal-grid" id="calGrid"></div>
        <div class="cal-legend">
            <span><i style="background:#22C55E"></i>Present</span>
            <span><i style="background:#EF4444"></i>Absent</span>
            <span><i style="background:#EAB308"></i>Leave</span>
            <span><i style="background:#F97316"></i>Half Day</span>
        </div>
    </div>

    {{-- History --}}
    <div class="att-card">
        <div class="att-hist-head">
            <h2>Attendance History</h2>
            <div class="sel">
                <select id="histMonth"></select>
                <select id="histYear"></select>
            </div>
        </div>
        <div style="overflow-x:auto;">
            <table class="att-tbl">
                <thead><tr><th>Date</th><th>Check In</th><th>Check Out</th><th>Hours</th><th>Status</th></tr></thead>
                <tbody id="histBody"><tr><td colspan="5" class="att-empty">Loading…</td></tr></tbody>
            </table>
        </div>
    </div>
</div>

{{-- Check in / out confirmation modal --}}
<div class="att-modal" id="attConfirm">
    <div class="att-box">
        <div class="am-head"><h3 id="acTitle">Confirm</h3><button type="button" class="am-close" data-close>&times;</button></div>
        <div class="am-body">
            <p id="acMsg" style="font-size:13.5px;color:var(--text-2);line-height:1.6;"></p>
            <p style="font-size:12px;color:var(--text-3);margin-top:8px;">Time will be recorded as <b id="acTime" style="color:var(--text-2);"></b>.</p>
        </div>
        <div class="am-foot">
            <button type="button" class="att-btn" data-close>Cancel</button>
            <button type="button" class="att-btn in" id="acConfirm">Confirm</button>
        </div>
    </div>
</div>

<script>
(function () {
    const root = document.getElementById('attRoot');
    if (!root) return;
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const URLS = {
        overview: "{{ route('hr.staff.attendance.overview') }}",
        history:  "{{ route('hr.staff.attendance.history') }}",
        checkIn:  "{{ route('hr.staff.attendance.check-in') }}",
        checkOut: "{{ route('hr.staff.attendance.check-out') }}",
    };
    const MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
    const now = new Date();
    let calMonth = now.getMonth() + 1, calYear = now.getFullYear();

    const $ = id => document.getElementById(id);
    function toast(msg, ok) {
        let t = document.getElementById('attToast');
        if (!t) { t = document.createElement('div'); t.id = 'attToast';
            t.style.cssText = 'position:fixed;right:20px;bottom:20px;z-index:1300;padding:12px 16px;border-radius:10px;font-size:13px;color:#fff;box-shadow:0 12px 32px rgba(0,0,0,.2)'; document.body.appendChild(t); }
        t.textContent = msg; t.style.background = ok ? '#16A34A' : '#DC2626'; t.style.display = 'block';
        clearTimeout(t._h); t._h = setTimeout(() => t.style.display = 'none', 2600);
    }

    async function getJSON(url) { const r = await fetch(url, { headers: { 'Accept': 'application/json' } }); return r.json().then(j => ({ ok: r.ok, j })); }
    async function postJSON(url) { const r = await fetch(url, { method: 'POST', headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } }); return r.json().then(j => ({ ok: r.ok, j })); }

    // ── Overview (today card + stats + calendar) ───────────────────
    async function loadOverview() {
        const { ok, j } = await getJSON(URLS.overview + '?month=' + calMonth + '&year=' + calYear);
        if (!ok || !j.success) { renderEmptyCalendar(j.message); return; }
        const d = j.data;
        $('attTodayDate').textContent = d.today_date;
        $('calLabel').textContent = d.label;

        // Today's check-in/out button state.
        const t = d.today;
        $('attCheckIn').disabled  = !!(t && t.check_in);
        $('attCheckOut').disabled = !(t && t.check_in) || !!(t && t.check_out);

        // Stats.
        $('attWorking').textContent = d.summary.working_days;
        $('attPresent').textContent = d.summary.present;
        $('attAbsent').textContent  = d.summary.absent;
        $('attHours').textContent   = d.summary.total_hours + 'h';

        renderCalendar(d.days);
    }

    function renderEmptyCalendar(msg) {
        $('calGrid').innerHTML = '<div style="grid-column:1/-1;text-align:center;color:var(--text-3);padding:24px;font-size:13px">' + (msg || 'No data') + '</div>';
    }

    function renderCalendar(days) {
        const dow = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
        let html = dow.map(d => '<div class="cal-dow">' + d + '</div>').join('');
        // leading blanks based on first day's weekday
        const lead = days.length ? days[0].weekday : 0;
        for (let i = 0; i < lead; i++) html += '<div class="cal-cell"></div>';
        days.forEach(day => {
            let cls = 'cal-day';
            if (day.status) cls += ' ' + day.status;
            else if (day.is_today) cls += ' today';
            else if (day.is_weekend || day.is_future) cls += ' muted';
            html += '<div class="cal-cell"><div class="' + cls + '">' + day.day + '</div></div>';
        });
        $('calGrid').innerHTML = html;
    }

    // ── History ────────────────────────────────────────────────────
    async function loadHistory() {
        const m = $('histMonth').value, y = $('histYear').value;
        $('histBody').innerHTML = '<tr><td colspan="5" class="att-empty">Loading…</td></tr>';
        const { ok, j } = await getJSON(URLS.history + '?month=' + m + '&year=' + y);
        if (!ok || !j.success) { $('histBody').innerHTML = '<tr><td colspan="5" class="att-empty">' + (j.message || 'Could not load') + '</td></tr>'; return; }
        const rows = j.data.records;
        if (!rows.length) { $('histBody').innerHTML = '<tr><td colspan="5" class="att-empty">No attendance records found</td></tr>'; return; }
        $('histBody').innerHTML = rows.map(r =>
            '<tr><td>' + r.date + '</td><td>' + (r.check_in || '--:--') + '</td><td>' + (r.check_out || '--:--') + '</td><td>'
            + (r.hours || '--') + '</td><td><span class="st-badge ' + r.status + '">' + labelFor(r.status) + '</span></td></tr>'
        ).join('');
    }
    function labelFor(s) { return ({ present:'Present', absent:'Absent', on_leave:'On Leave', half_day:'Half Day' })[s] || s; }

    // ── Wire up ────────────────────────────────────────────────────
    $('calPrev').addEventListener('click', () => { calMonth--; if (calMonth < 1) { calMonth = 12; calYear--; } loadOverview(); });
    $('calNext').addEventListener('click', () => { calMonth++; if (calMonth > 12) { calMonth = 1; calYear++; } loadOverview(); });

    // Check in / out → confirm in a modal, then submit.
    const confirmModal = $('attConfirm');
    let pendingAction = null; // 'in' | 'out'

    function nowTime() {
        return new Date().toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true });
    }
    function openConfirm(action) {
        pendingAction = action;
        $('acTitle').textContent = action === 'in' ? 'Check In' : 'Check Out';
        $('acMsg').textContent = action === 'in'
            ? 'Are you sure you want to check in for today?'
            : 'Are you sure you want to check out for today?';
        $('acTime').textContent = nowTime();
        confirmModal.classList.add('open');
    }
    confirmModal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => confirmModal.classList.remove('open')));
    confirmModal.addEventListener('click', e => { if (e.target === confirmModal) confirmModal.classList.remove('open'); });

    $('attCheckIn').addEventListener('click', () => openConfirm('in'));
    $('attCheckOut').addEventListener('click', () => openConfirm('out'));

    $('acConfirm').addEventListener('click', async function () {
        if (!pendingAction) return;
        this.disabled = true;
        const { j } = await postJSON(pendingAction === 'in' ? URLS.checkIn : URLS.checkOut);
        this.disabled = false;
        confirmModal.classList.remove('open');
        toast(j.message, j.success);
        if (j.success) { loadOverview(); loadHistory(); }
    });

    // History selectors.
    $('histMonth').innerHTML = MONTHS.map((m, i) => '<option value="' + (i + 1) + '"' + (i + 1 === calMonth ? ' selected' : '') + '>' + m + '</option>').join('');
    let yrs = ''; for (let y = now.getFullYear() + 1; y >= now.getFullYear() - 3; y--) yrs += '<option value="' + y + '"' + (y === calYear ? ' selected' : '') + '>' + y + '</option>';
    $('histYear').innerHTML = yrs;
    $('histMonth').addEventListener('change', loadHistory);
    $('histYear').addEventListener('change', loadHistory);

    // Lazy init when the Attendance tab is first opened (or immediately if already active).
    window.initStaffAttendance = function () {
        if (root.dataset.loaded === '1') return;
        root.dataset.loaded = '1';
        loadOverview(); loadHistory();
    };
    if (root.closest('.set-pane')?.classList.contains('active')) window.initStaffAttendance();
})();
</script>
