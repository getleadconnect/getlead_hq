<x-layouts.app title="Payroll Management">
@php $money = fn($v) => '$'.number_format((float)$v, 2); @endphp
@push('styles')
<style>
    .hr-wrap { padding: 24px 28px 48px; width: 100%; }
    .hr-head { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:18px; }
    .hr-eyebrow { font-size:11px; font-weight:600; letter-spacing:.09em; text-transform:uppercase; color:var(--brand-red); }
    .hr-head h1 { font-size:24px; font-weight:600; letter-spacing:-.5px; color:var(--text-1); margin-top:4px; }
    .head-ctrls { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }

    .btn { display:inline-flex; align-items:center; gap:7px; padding:9px 15px; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:500; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
    .btn-primary { background:var(--brand-red); color:#fff; } .btn-primary:hover { background:var(--brand-red-dark); }
    .btn-dark { background:var(--text-1); color:#fff; border:1px solid var(--text-1); } .btn-dark:hover { background:#000; }
    .btn-secondary { background:var(--bg-card); color:var(--text-1); border-color:var(--border); } .btn-secondary:hover { border-color:var(--text-3); }
    .btn-sm { padding:5px 12px; font-size:12px; }
    .msel { height:38px; border:1px solid var(--border); border-radius:var(--radius-sm); padding:0 11px; font-family:inherit; font-size:13px; color:var(--text-1); background:var(--bg-card); outline:none; }

    .flash { padding:11px 15px; border-radius:var(--radius-sm); font-size:13px; margin-bottom:16px; background:var(--success-soft); color:var(--success-text); border:1px solid var(--success-border); }

    .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:22px; }
    @media(max-width:980px){ .stat-grid{ grid-template-columns:repeat(2,1fr);} }
    @media(max-width:520px){ .stat-grid{ grid-template-columns:1fr;} }
    .stat-card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:18px; display:flex; align-items:center; gap:14px;}
    
    .stat-ico { width:44px; height:44px; border-radius:11px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .stat-ico svg { width:22px; height:22px; stroke:currentColor; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
    .ico-b { background:#EFF6FF; color:#1D4ED8; } .ico-g { background:var(--success-soft); color:var(--success); } .ico-o { background:#FFFBEB; color:#B45309; } .ico-r { background:var(--brand-red-soft); color:var(--brand-red); }
    .stat-card .v { font-size:22px; font-weight:700; color:var(--text-1); letter-spacing:-.02em; }
    .stat-card .l { font-size:12px; color:var(--text-2); margin-top:1px; }

    .card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); overflow:hidden; }
    .card-head { display:flex; align-items:center; justify-content:space-between; padding:16px 18px; border-bottom:1px solid var(--border-soft); }
    .card-head h2 { font-size:16px; font-weight:600; color:var(--text-1); }
    .tbl-wrap { overflow-x:auto; }
    table.pr { width:100%; border-collapse:collapse; }
    table.pr thead th { text-align:left; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--text-3); padding:12px 14px; background:var(--bg-page); border-bottom:1px solid var(--border); white-space:nowrap; }
    table.pr tbody td { font-size:13px; color:var(--text-2); padding:12px 14px; border-bottom:1px solid var(--border-soft); white-space:nowrap; }
    table.pr tbody tr:hover { background:var(--bg-page); }
    .emp-cell { display:flex; align-items:center; gap:10px; }
    .emp-av { width:32px; height:32px; border-radius:50%; background:var(--brand-red-soft); color:var(--brand-red-dark); border:1px solid var(--brand-red-border); font-size:12px; font-weight:600; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .emp-cell .nm { color:var(--text-1); font-weight:500; }
    .num-strong { font-weight:600; color:var(--text-1); }
    .ded { color:var(--danger); font-weight:500; }
    .badge { display:inline-block; padding:3px 10px; border-radius:var(--radius-pill); font-size:11px; font-weight:600; }
    .b-approved, .b-paid { background:var(--success-soft); color:var(--success-text); border:1px solid var(--success-border); }
    .b-pending { background:var(--warning-soft); color:var(--warning-text); border:1px solid var(--warning-border); }
    .row-acts { display:flex; gap:6px; }

    /* Modal */
    .modal-ov { position:fixed; inset:0; background:rgba(15,23,42,.5); z-index:1200; display:none; align-items:flex-start; justify-content:center; padding:32px 20px; overflow-y:auto; }
    .modal-ov.open { display:flex; }
    .modal-bx { background:var(--bg-card); border-radius:var(--radius-lg); width:100%; max-width:540px; box-shadow:0 24px 60px rgba(0,0,0,.25); overflow:hidden; }
    .mhead { padding:20px 24px 4px; position:relative; }
    .mhead h3 { font-size:17px; font-weight:700; color:var(--text-1); }
    .mclose { position:absolute; top:18px; right:20px; border:none; background:none; font-size:20px; color:var(--text-3); cursor:pointer; }
    .mbody { padding:16px 24px; }
    .mrow { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    @media(max-width:520px){ .mrow{ grid-template-columns:1fr; } }
    .mfield { margin-bottom:16px; }
    .mfield > label { display:block; font-size:13px; font-weight:500; color:var(--text-1); margin-bottom:6px; }
    .mfield .req { color:var(--brand-red); }
    .mfield input, .mfield select, .mfield textarea { width:100%; border:1px solid var(--border); border-radius:var(--radius-sm); padding:9px 11px; font-family:inherit; font-size:13.5px; color:var(--text-1); background:var(--bg-card); outline:none; }
    .mfield input:focus, .mfield select:focus, .mfield textarea:focus { border-color:var(--brand-red); box-shadow:0 0 0 3px var(--brand-red-soft); }
    .mfield textarea { min-height:72px; resize:vertical; }
    .mfoot { display:flex; justify-content:flex-end; gap:8px; padding:8px 24px 22px; }
    .m-err { font-size:12.5px; color:var(--danger); margin-bottom:10px; display:none; }
    /* Payslip */
    .ps-row { display:flex; justify-content:space-between; padding:9px 0; border-bottom:1px solid var(--border-soft); font-size:13.5px; color:var(--text-2); }
    .ps-row b { color:var(--text-1); }
    .ps-row.net { border-bottom:none; margin-top:6px; padding-top:12px; border-top:2px solid var(--border); font-size:15px; font-weight:600; color:var(--text-1); }
    #toast { position:fixed; right:20px; bottom:20px; z-index:1300; display:none; padding:12px 16px; border-radius:var(--radius-md); font-size:13px; color:#fff; box-shadow:0 12px 32px rgba(0,0,0,.2); background:var(--success); }
</style>
@endpush

<div class="hr-wrap">
    <div class="hr-head">
        <div>
            <div class="hr-eyebrow">HR Management</div>
            <h1>Payroll Management</h1>
        </div>
        <div class="head-ctrls">
            <select class="msel" id="monthSel">
                @foreach($months as $m)<option value="{{ $m['value'] }}" {{ $m['value'] === $month ? 'selected' : '' }}>{{ $m['label'] }}</option>@endforeach
            </select>
            <form method="POST" action="{{ route('hr.payroll.process') }}" style="display:inline;">
                @csrf
                <input type="hidden" name="month" value="{{ $month }}">
                <button type="submit" class="btn btn-dark" onclick="return confirm('Process payroll for {{ $label }}?');">
                    <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Process Payroll
                </button>
            </form>
            <a href="{{ route('hr.payroll.export', ['month' => $month]) }}" class="btn btn-secondary">
                <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg> Export Payslips
            </a>
        </div>
    </div>

    @if(session('success'))<div class="flash">{{ session('success') }}</div>@endif

    <div class="stat-grid">
        <div class="stat-card"><div class="stat-ico ico-b"><svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div><div><div class="v num">{{ $money($stats['total_payroll']) }}</div><div class="l">Total Payroll</div></div></div>
        <div class="stat-card"><div class="stat-ico ico-g"><svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div><div><div class="v num">{{ $stats['processed'] }}</div><div class="l">Employees Processed</div></div></div>
        <div class="stat-card"><div class="stat-ico ico-o"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div><div><div class="v num">{{ $stats['pending'] }}</div><div class="l">Pending Approval</div></div></div>
        <div class="stat-card"><div class="stat-ico ico-r"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div><div><div class="v num">{{ $stats['avg_attendance'] }}%</div><div class="l">Avg Attendance</div></div></div>
    </div>

    <div class="card">
        <div class="card-head">
            <h2>Employee Payroll — {{ $label }}</h2>
            <button type="button" class="btn btn-secondary btn-sm" id="setSalaryBtn">Set Base Salary</button>
        </div>
        <div class="tbl-wrap">
            <table class="pr">
                <thead>
                    <tr><th>Employee</th><th>Base Salary</th><th>Working Days</th><th>Present</th><th>Absent</th><th>Leave</th><th>Per Day Salary</th><th>Deduction</th><th>Net Salary</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @forelse($rows as $r)
                        <tr>
                            <td><div class="emp-cell"><div class="emp-av">{{ strtoupper(mb_substr($r['name'] ?? 'E',0,1)) }}</div><span class="nm">{{ $r['name'] }}</span></div></td>
                            <td>{{ $money($r['base_salary']) }}</td>
                            <td>{{ $r['working_days'] }}</td>
                            <td>{{ rtrim(rtrim(number_format($r['present'],1),'0'),'.') }}</td>
                            <td>{{ rtrim(rtrim(number_format($r['absent'],1),'0'),'.') }}</td>
                            <td>{{ rtrim(rtrim(number_format($r['leave'],1),'0'),'.') }}</td>
                            <td>{{ $money($r['per_day']) }}</td>
                            <td>{!! $r['deduction'] > 0 ? '<span class="ded">-'.$money($r['deduction']).'</span>' : $money(0) !!}</td>
                            <td class="num-strong">{{ $money($r['net_salary']) }}</td>
                            <td><span class="badge b-{{ $r['status'] }}">{{ strtoupper($r['status']) }}</span></td>
                            <td>
                                <div class="row-acts">
                                    <button type="button" class="btn btn-secondary btn-sm view-pr"
                                        data-name="{{ $r['name'] }}" data-base="{{ $r['base_salary'] }}" data-wd="{{ $r['working_days'] }}"
                                        data-present="{{ $r['present'] }}" data-absent="{{ $r['absent'] }}" data-leave="{{ $r['leave'] }}"
                                        data-perday="{{ $r['per_day'] }}" data-ded="{{ $r['deduction'] }}" data-net="{{ $r['net_salary'] }}" data-status="{{ $r['status'] }}">View</button>
                                    <button type="button" class="btn btn-secondary btn-sm edit-pr" data-emp="{{ $r['employee_id'] }}" data-base="{{ $r['base_salary'] }}" data-hra="{{ $r['hra'] }}" data-ta="{{ $r['ta'] }}">Edit</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="11" style="text-align:center;color:var(--text-3);padding:32px;">No employees found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Set Base Salary modal --}}
<div class="modal-ov" id="salModal">
    <div class="modal-bx">
        <div class="mhead"><h3 id="salTitle">Set Base Salary</h3><button type="button" class="mclose" data-close>&times;</button></div>
        <div class="mbody">
            <div class="m-err" id="salErr"></div>
            <div class="mfield">
                <label>Employee <span class="req">*</span></label>
                <select id="salEmp">
                    <option value="">Choose employee…</option>
                    @foreach($employees as $e)<option value="{{ $e->id }}">{{ $e->full_name }}</option>@endforeach
                </select>
            </div>
            <div class="mrow">
                <div class="mfield"><label>Base Salary (Monthly) <span class="req">*</span></label><input type="number" id="salBase" min="0" step="0.01" placeholder="6000"></div>
                <div class="mfield"><label>Effective From <span class="req">*</span></label><input type="date" id="salEff" value="{{ now()->toDateString() }}"></div>
            </div>
            <div class="mrow">
                <div class="mfield"><label>HRA (%)</label><input type="number" id="salHra" min="0" max="100" step="0.01" placeholder="0"></div>
                <div class="mfield"><label>TA (%)</label><input type="number" id="salTa" min="0" max="100" step="0.01" placeholder="0"></div>
            </div>
            <div class="mfield"><label>Notes</label><textarea id="salNotes" placeholder="Add notes (optional)"></textarea></div>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-secondary" data-close>Cancel</button>
            <button type="button" class="btn btn-dark" id="salSave">Save Salary</button>
        </div>
    </div>
</div>

{{-- Payslip modal --}}
<div class="modal-ov" id="psModal">
    <div class="modal-bx">
        <div class="mhead"><h3 id="psName">Payslip</h3><button type="button" class="mclose" data-close>&times;</button></div>
        <div class="mbody" id="psBody"></div>
        <div class="mfoot"><button type="button" class="btn btn-secondary" data-close>Close</button></div>
    </div>
</div>
<div id="toast"></div>

<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const gid = id => document.getElementById(id);
    const money = v => '$' + (parseFloat(v) || 0).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    function toast(m) { const t = gid('toast'); t.textContent = m; t.style.display = 'block'; setTimeout(() => t.style.display = 'none', 2200); }

    // Month change → reload the page for that month.
    gid('monthSel').addEventListener('change', function () { window.location = "{{ route('hr.payroll') }}?month=" + this.value; });

    // Modals close.
    document.querySelectorAll('.modal-ov').forEach(ov => {
        ov.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => ov.classList.remove('open')));
        ov.addEventListener('click', e => { if (e.target === ov) ov.classList.remove('open'); });
    });

    // Set Base Salary.
    const salModal = gid('salModal');
    const SAL_URLS = { create: "{{ route('hr.payroll.set-salary') }}", edit: "{{ route('hr.payroll.update-salary') }}" };
    let salMode = 'create';
    function openSal(mode, empId, base, hra, ta) {
        salMode = mode;
        gid('salErr').style.display = 'none';
        gid('salTitle').textContent = mode === 'edit' ? 'Edit Base Salary' : 'Set Base Salary';
        gid('salEmp').value = empId || ''; gid('salBase').value = base || '';
        gid('salHra').value = (hra && parseFloat(hra) > 0) ? hra : ''; gid('salTa').value = (ta && parseFloat(ta) > 0) ? ta : ''; gid('salNotes').value = '';
        gid('salEff').value = "{{ now()->toDateString() }}";
        salModal.classList.add('open');
    }
    gid('setSalaryBtn').addEventListener('click', () => openSal('create', '', '', '', ''));
    document.querySelectorAll('.edit-pr').forEach(b => b.addEventListener('click', function () { openSal('edit', this.dataset.emp, this.dataset.base > 0 ? this.dataset.base : '', this.dataset.hra, this.dataset.ta); }));

    // Changing the employee clears all other fields (start fresh for the picked employee).
    gid('salEmp').addEventListener('change', function () {
        gid('salErr').style.display = 'none';
        gid('salBase').value = ''; gid('salHra').value = ''; gid('salTa').value = ''; gid('salNotes').value = '';
        gid('salEff').value = "{{ now()->toDateString() }}";
    });

    gid('salSave').addEventListener('click', function () {
        const err = gid('salErr'); err.style.display = 'none';
        if (!gid('salEmp').value) { err.textContent = 'Please choose an employee.'; err.style.display = 'block'; return; }
        if (!gid('salBase').value) { err.textContent = 'Please enter the base salary.'; err.style.display = 'block'; return; }
        const btn = this; btn.disabled = true;
        const body = new URLSearchParams({ _token: CSRF, employee_id: gid('salEmp').value, base_salary: gid('salBase').value, effective_from: gid('salEff').value, hra: gid('salHra').value, ta: gid('salTa').value, notes: gid('salNotes').value });
        fetch(SAL_URLS[salMode], { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body })
            .then(r => r.json().then(j => ({ s: r.status, j })))
            .then(({ s, j }) => {
                btn.disabled = false;
                if (!j.ok) { err.textContent = j.message || (j.errors ? Object.values(j.errors)[0][0] : 'Save failed.'); err.style.display = 'block'; return; }
                toast(j.message); salModal.classList.remove('open');
                setTimeout(() => window.location.reload(), 700);
            }).catch(() => { btn.disabled = false; err.textContent = 'Save failed.'; err.style.display = 'block'; });
    });

    // View payslip.
    const psModal = gid('psModal');
    document.querySelectorAll('.view-pr').forEach(b => b.addEventListener('click', function () {
        const d = this.dataset;
        gid('psName').textContent = 'Payslip — ' + d.name;
        gid('psBody').innerHTML =
            '<div class="ps-row"><span>Base Salary</span><b>' + money(d.base) + '</b></div>' +
            '<div class="ps-row"><span>Working Days</span><b>' + d.wd + '</b></div>' +
            '<div class="ps-row"><span>Present</span><b>' + d.present + '</b></div>' +
            '<div class="ps-row"><span>Absent</span><b>' + d.absent + '</b></div>' +
            '<div class="ps-row"><span>Leave</span><b>' + d.leave + '</b></div>' +
            '<div class="ps-row"><span>Per Day Salary</span><b>' + money(d.perday) + '</b></div>' +
            '<div class="ps-row"><span>Deduction</span><b style="color:var(--danger)">-' + money(d.ded) + '</b></div>' +
            '<div class="ps-row"><span>Status</span><b>' + (d.status || '').toUpperCase() + '</b></div>' +
            '<div class="ps-row net"><span>Net Salary</span><span>' + money(d.net) + '</span></div>';
        psModal.classList.add('open');
    }));
})();
</script>
</x-layouts.app>
