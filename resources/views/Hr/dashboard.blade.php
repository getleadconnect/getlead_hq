<x-layouts.app title="HR Dashboard">
@push('styles')
<style>
    .hr-wrap { padding: 24px 28px 48px; width: 100%; }
    .hr-head { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom: 22px; }
    .hr-eyebrow { font-size: 11px; font-weight: 600; letter-spacing: .09em; text-transform: uppercase; color: var(--brand-red); }
    .hr-head h1 { font-size: 24px; font-weight: 600; letter-spacing: -.5px; color: var(--text-1); margin-top: 4px; }
    .hr-head p { font-size: 13px; color: var(--text-2); margin-top: 4px; }
    .btn { display:inline-flex; align-items:center; gap:6px; padding:9px 15px; border-radius:var(--radius-sm); font-size:13px; font-weight:500; text-decoration:none; border:1px solid transparent; }
    .btn-primary { background: var(--brand-red); color:#fff; } .btn-primary:hover { background: var(--brand-red-dark); }

    .kpi-grid { display:grid; grid-template-columns:repeat(5,1fr); gap:16px; margin-bottom:26px; }
    @media(max-width:1100px){ .kpi-grid{ grid-template-columns:repeat(3,1fr);} }
    @media(max-width:640px){ .kpi-grid{ grid-template-columns:repeat(2,1fr);} }
    @media(max-width:420px){ .kpi-grid{ grid-template-columns:1fr;} }
    .kpi-card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:18px; }
    .kpi-top-row { display:flex; align-items:center; justify-content:space-between; gap:8px; }
    .kpi-view { font-size:11.5px; font-weight:600; color:var(--brand-red-dark); background:var(--brand-red-soft); border:1px solid var(--brand-red-border); border-radius:var(--radius-pill); padding:2px 10px; cursor:pointer; text-decoration:none; font-family:inherit; }
    .kpi-view:hover { background:var(--brand-red); color:#fff; border-color:var(--brand-red); }

    /* On-leave modal */
    .modal-ov { position:fixed; inset:0; background:rgba(15,23,42,.5); z-index:1200; display:none; align-items:flex-start; justify-content:center; padding:40px 20px; overflow-y:auto; }
    .modal-ov.open { display:flex; }
    .modal-bx { background:var(--bg-card); border-radius:var(--radius-lg); width:100%; max-width:520px; box-shadow:0 24px 60px rgba(0,0,0,.25); overflow:hidden; }
    .mhead { display:flex; align-items:center; justify-content:space-between; padding:18px 22px; border-bottom:1px solid var(--border-soft); }
    .mhead h3 { font-size:16px; font-weight:600; color:var(--text-1); }
    .mclose { border:none; background:none; font-size:20px; color:var(--text-3); cursor:pointer; }
    .mclose:hover { color:var(--text-1); }
    .mbody { padding:8px 22px 20px; max-height:60vh; overflow-y:auto; }
    .lv-item { display:flex; align-items:center; gap:12px; padding:12px 0; border-bottom:1px solid var(--border-soft); }
    .lv-item:last-child { border-bottom:none; }
    .lv-av { width:36px; height:36px; border-radius:50%; background:var(--brand-red-soft); color:var(--brand-red-dark); border:1px solid var(--brand-red-border); display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:600; flex-shrink:0; }
    .lv-item .nm { font-size:13.5px; font-weight:500; color:var(--text-1); }
    .lv-item .meta { font-size:11.5px; color:var(--text-3); margin-top:1px; }
    .lv-item .type { margin-left:auto; font-size:11px; font-weight:600; padding:2px 10px; border-radius:var(--radius-pill); background:#EFF6FF; color:#1D4ED8; border:1px solid #BFDBFE; white-space:nowrap; }
    .lv-empty { text-align:center; color:var(--text-3); font-size:13.5px; padding:32px 12px; }
    .kpi-label { font-size:12.5px; color:var(--text-2); font-weight:500; margin-bottom:10px; }
    .kpi-figure { font-size:30px; font-weight:600; color:var(--text-1); letter-spacing:-.02em; }

    /* Application link bar */
    .app-link { display:flex; align-items:center; gap:10px; flex-wrap:wrap;     background: #f9d7d74d; border:1px solid var(--border); border-radius:var(--radius-lg); padding:13px 16px; margin-bottom:20px; }
    .al-label { font-size:14px; font-weight:600; color:var(--text-2); flex-shrink:0; }
    .al-url { font-size:14px; color:var(--brand-red-dark); text-decoration:none; word-break:break-all; }
    .al-url:hover { text-decoration:underline; }
    .al-copy { display:inline-flex; align-items:center; gap:6px; flex-shrink:0; font-family:inherit; font-size:11.5px; font-weight:600; cursor:pointer;
               color:var(--brand-red-dark); background:var(--brand-red-soft); border:1px solid var(--brand-red-border); border-radius:var(--radius-pill); padding:5px 12px; }
    .al-copy:hover { background:var(--brand-red); color:#fff; border-color:var(--brand-red); }
    .al-copy.copied { background:var(--success); color:#fff; border-color:var(--success); }

    .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start; }
    @media(max-width:900px){ .grid-2{ grid-template-columns:1fr;} }
    .card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:20px; }
    .card h2 { font-size:15px; font-weight:600; color:var(--text-1); margin-bottom:14px; }

    .st-row { display:flex; align-items:center; gap:12px; margin-bottom:12px; }
    .st-row:last-child { margin-bottom:0; }
    .st-name { font-size:13px; color:var(--text-2); width:150px; flex-shrink:0; }
    .st-bar { flex:1; height:8px; background:var(--bg-neutral); border-radius:var(--radius-pill); overflow:hidden; }
    .st-fill { height:100%; border-radius:var(--radius-pill); background:var(--brand-red); }
    .st-count { font-size:13px; font-weight:600; color:var(--text-1); width:36px; text-align:right; font-variant-numeric:tabular-nums; }

    .role-row { display:flex; align-items:center; justify-content:space-between; padding:10px 0; border-bottom:1px solid var(--border-soft); font-size:13px; }
    .role-row:last-child { border-bottom:none; }
    .role-row .rc { font-weight:600; color:var(--brand-red-dark); }

    .table-card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); overflow:hidden; margin-top:20px; }
    .t-head { display:flex; align-items:center; justify-content:space-between; padding:16px 18px; border-bottom:1px solid var(--border-soft); }
    .t-head h2 { font-size:15px; font-weight:600; color:var(--text-1); }
    table.mini { width:100%; border-collapse:collapse; }
    table.mini thead th { text-align:left; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--text-3); padding:11px 18px; background:var(--bg-page); border-bottom:1px solid var(--border); }
    table.mini tbody td { font-size:13px; color:var(--text-2); padding:11px 18px; border-bottom:1px solid var(--border-soft); }
    table.mini tbody tr:last-child td { border-bottom:none; }
    table.mini tbody tr:hover { background:var(--bg-page); }
    .link-name { color:var(--brand-red-dark); font-weight:500; text-decoration:none; } .link-name:hover { text-decoration:underline; }
    .pill { display:inline-block; padding:2px 9px; border-radius:var(--radius-pill); font-size:11px; font-weight:600; background:var(--bg-neutral); color:var(--text-2); }
</style>
@endpush

@php $maxStatus = max(1, $statusCounts->max()); @endphp

<div class="hr-wrap">
    <div class="hr-head">
        <div>
            <div class="hr-eyebrow">HR Management</div>
            <h1>HR Dashboard</h1>
            <p>Recruitment overview — application volume, status breakdown, and recent activity.</p>
        </div>
        <a href="{{ route('hr.applications') }}" class="btn btn-primary">View all applications</a>
    </div>

    <div class="kpi-grid">
        <div class="kpi-card"><div class="kpi-label">Total Applications</div><div class="kpi-figure num">{{ number_format($totals['all']) }}</div></div>
        <div class="kpi-card"><div class="kpi-label">Total Employees</div><div class="kpi-figure num">{{ number_format($totals['employees']) }}</div></div>
        <div class="kpi-card"><div class="kpi-label">Present Today</div><div class="kpi-figure num" style="color:var(--success);">{{ number_format($totals['present_today']) }}</div></div>
        <div class="kpi-card">
            <div class="kpi-top-row"><div class="kpi-label">On Leave Today</div><button type="button" class="kpi-view" id="onLeaveView">View</button></div>
            <div class="kpi-figure num" style="color:var(--warning);">{{ number_format($totals['on_leave_today']) }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-top-row"><div class="kpi-label">Leave Pending Requests</div><a href="{{ route('hr.leave-requests', ['status' => 'pending']) }}" class="kpi-view">View</a></div>
            <div class="kpi-figure num" style="color:var(--brand-red);">{{ number_format($totals['pending_leaves']) }}</div>
        </div>
    </div>

    <div class="app-link">
        <span class="al-label">Application Link:</span>
        <a href="{{ $applicationLink }}" target="_blank" rel="noopener" class="al-url" id="appLinkUrl">{{ $applicationLink }}</a>
        <button type="button" class="al-copy" id="appLinkCopy" data-link="{{ $applicationLink }}">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"></rect><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path></svg>
            <span id="appLinkCopyTxt">Copy</span>
        </button>
    </div>

    <div class="grid-2">
        <div class="card">
            <h2>By Status</h2>
            @foreach($statusCounts as $name => $count)
                <div class="st-row">
                    <span class="st-name">{{ $name }}</span>
                    <span class="st-bar"><span class="st-fill" style="width: {{ $count / $maxStatus * 100 }}%"></span></span>
                    <span class="st-count">{{ $count }}</span>
                </div>
            @endforeach
        </div>
        <div class="card">
            <h2>Top Applied Roles</h2>
            @forelse($topRoles as $role)
                <div class="role-row"><span>{{ $role->category_name ?? '—' }}</span><span class="rc">{{ $role->c }}</span></div>
            @empty
                <div class="role-row"><span class="text-muted">No data yet.</span></div>
            @endforelse
        </div>
    </div>

    <div class="table-card">
        <div class="t-head">
            <h2>Recent Applications</h2>
            <a href="{{ route('hr.applications') }}" class="link-name">View all →</a>
        </div>
        <table class="mini">
            <thead>
                <tr><th>Name</th><th>Applied For</th><th>Mobile</th><th>Dated</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($recent as $r)
                    <tr>
                        <td><a href="{{ route('hr.applications.show', $r->id) }}" class="link-name">{{ ucwords($r->name) }}</a></td>
                        <td>{{ $r->category_name ?? '—' }}</td>
                        <td>{{ ($r->countrycode ?? '').$r->mobile }}</td>
                        <td>{{ $r->created_at ? $r->created_at->format('d-m-Y') : '—' }}</td>
                        <td><span class="pill">{{ $r->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;color:var(--text-3);padding:28px;">No applications yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
{{-- On Leave Today modal --}}
<div class="modal-ov" id="onLeaveModal">
    <div class="modal-bx">
        <div class="mhead">
            <h3>On Leave Today ({{ $onLeaveList->count() }})</h3>
            <button type="button" class="mclose" data-close>&times;</button>
        </div>
        <div class="mbody">
            @forelse($onLeaveList as $l)
                <div class="lv-item">
                    <div class="lv-av">{{ strtoupper(mb_substr($l->full_name ?? 'E', 0, 1)) }}</div>
                    <div>
                        <div class="nm">{{ $l->full_name ?: '—' }}</div>
                        <div class="meta">{{ \Carbon\Carbon::parse($l->from_date)->format('d M Y') }} – {{ \Carbon\Carbon::parse($l->to_date)->format('d M Y') }}</div>
                    </div>
                    <span class="type">{{ $l->leave_type }}</span>
                </div>
            @empty
                <div class="lv-empty">No employees are on leave today.</div>
            @endforelse
        </div>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('onLeaveModal');
    const btn = document.getElementById('onLeaveView');
    if (btn) btn.addEventListener('click', () => modal.classList.add('open'));
    modal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => modal.classList.remove('open')));
    modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('open'); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') modal.classList.remove('open'); });

    // Copy the application link. navigator.clipboard needs a secure context, so fall back to execCommand.
    const copyBtn = document.getElementById('appLinkCopy');
    const copyTxt = document.getElementById('appLinkCopyTxt');
    function flash() {
        copyBtn.classList.add('copied'); copyTxt.textContent = 'Copied!';
        setTimeout(() => { copyBtn.classList.remove('copied'); copyTxt.textContent = 'Copy'; }, 1600);
    }
    function fallbackCopy(text) {
        const ta = document.createElement('textarea');
        ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
        document.body.appendChild(ta); ta.select();
        try { document.execCommand('copy'); flash(); } catch (e) { /* ignore */ }
        document.body.removeChild(ta);
    }
    if (copyBtn) copyBtn.addEventListener('click', function () {
        const link = this.dataset.link;
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(link).then(flash).catch(() => fallbackCopy(link));
        } else {
            fallbackCopy(link);
        }
    });
})();
</script>
</x-layouts.app>
