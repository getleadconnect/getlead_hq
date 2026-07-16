<x-layouts.app title="Leave Requests">
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/datatables.net/css/jquery.dataTables.css') }}">
<style>
    .hr-wrap { padding: 24px 28px 48px; width: 100%; }
    .hr-head { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:16px; }
    .hr-eyebrow { font-size:11px; font-weight:600; letter-spacing:.09em; text-transform:uppercase; color:var(--brand-red); }
    .hr-head h1 { font-size:24px; font-weight:600; letter-spacing:-.5px; color:var(--text-1); margin-top:4px; }

    .btn { display:inline-flex; align-items:center; gap:7px; padding:9px 15px; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:500; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
    .btn-primary { background:var(--brand-red); color:#fff; } .btn-primary:hover { background:var(--brand-red-dark); }
    .btn-secondary { background:var(--bg-card); color:var(--text-1); border-color:var(--border); } .btn-secondary:hover { border-color:var(--text-3); }

    /* Status tabs */
    .tabs { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:16px; }
    .tab-pill { padding:7px 15px; border:1px solid var(--border); background:var(--bg-card); border-radius:var(--radius-sm); font-size:13px; font-weight:500; color:var(--text-2); cursor:pointer; font-family:inherit; }
    .tab-pill:hover { border-color:var(--text-3); color:var(--text-1); }
    .tab-pill.active { background:var(--brand-red); border-color:var(--brand-red); color:#fff; }

    .toolbar { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg) var(--radius-lg) 0 0; padding:12px 14px; display:flex; align-items:center; gap:10px; flex-wrap:wrap; border-bottom:none; }
    .toolbar select { height:36px; min-width:160px; border:1px solid var(--border); border-radius:var(--radius-sm); padding:0 11px; font-family:inherit; font-size:13px; color:var(--text-1); background:var(--bg-card); outline:none; }
    .toolbar select:focus { border-color:var(--brand-red); box-shadow:0 0 0 3px var(--brand-red-soft); }
    .card { background:var(--bg-card); border:1px solid var(--border); border-radius:0 0 var(--radius-lg) var(--radius-lg); padding:8px 16px 16px; }

    .emp-cell { display:flex; align-items:center; gap:10px; }
    .emp-avatar-fb { width:34px; height:34px; border-radius:50%; background:var(--brand-red-soft); color:var(--brand-red-dark); border:1px solid var(--brand-red-border); font-size:12px; font-weight:600; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .emp-cell .nm { color:var(--text-1); font-weight:500; }
    .emp-cell .em { font-size:11px; color:var(--text-3); }
    .text-muted { color:var(--text-3); }

    .att-badge { display:inline-block; padding:3px 10px; border-radius:var(--radius-pill); font-size:11px; font-weight:600; }
    .st-pending { background:var(--warning-soft); color:var(--warning-text); border:1px solid var(--warning-border); }
    .st-approved { background:var(--success-soft); color:var(--success-text); border:1px solid var(--success-border); }
    .st-rejected { background:var(--danger-soft); color:var(--danger-text); border:1px solid var(--danger-border); }
    .row-acts { display:flex; gap:6px; }
    .ico-btn { border:1px solid var(--border); background:var(--bg-card); border-radius:var(--radius-sm); padding:4px 9px; cursor:pointer; font-size:13px; }
    .ico-btn:hover { background:var(--bg-neutral); }
    .del-lv:hover { background:var(--brand-red-soft); border-color:var(--brand-red-border); }

    /* DataTables theming */
    table.dataTable thead th { text-align:left; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--text-3); border-bottom:1px solid var(--border) !important; padding:12px; }
    table.dataTable tbody td { font-size:13px; color:var(--text-2); border-bottom:1px solid var(--border-soft) !important; padding:10px 12px; vertical-align:middle; }
    table.dataTable tbody tr:hover { background:var(--bg-page); }
    .dataTables_wrapper .dataTables_filter input, .dataTables_wrapper .dataTables_length select { border:1px solid var(--border); border-radius:var(--radius-sm); padding:6px 10px; font-family:inherit; font-size:13px; outline:none; }
    .dataTables_wrapper .dataTables_filter input:focus { border-color:var(--brand-red); box-shadow:0 0 0 3px var(--brand-red-soft); }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current { background:var(--brand-red) !important; color:#fff !important; border:1px solid var(--brand-red) !important; border-radius:var(--radius-sm); }
    .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter { font-size:12.5px; color:var(--text-2); }

    /* Modal */
    .modal-ov { position:fixed; inset:0; background:rgba(15,23,42,.5); z-index:1200; display:none; align-items:flex-start; justify-content:center; padding:32px 20px; overflow-y:auto; }
    .modal-ov.open { display:flex; }
    .modal-bx { background:var(--bg-card); border-radius:var(--radius-lg); width:100%; max-width:560px; box-shadow:0 24px 60px rgba(0,0,0,.25); overflow:hidden; }
    .mhead { padding:20px 24px 4px; position:relative; }
    .mhead h3 { font-size:17px; font-weight:700; color:var(--text-1); }
    .mclose { position:absolute; top:18px; right:20px; border:none; background:none; font-size:20px; color:var(--text-3); cursor:pointer; }
    .mclose:hover { color:var(--text-1); }
    .mbody { padding:16px 24px; }
    .mrow3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:14px; }
    @media(max-width:540px){ .mrow3{ grid-template-columns:1fr; } }
    .mfield { margin-bottom:16px; }
    .mfield > label { display:block; font-size:13px; font-weight:500; color:var(--text-1); margin-bottom:6px; }
    .mfield .req { color:var(--brand-red); }
    .mfield input, .mfield select, .mfield textarea { width:100%; border:1px solid var(--border); border-radius:var(--radius-sm); padding:9px 11px; font-family:inherit; font-size:13.5px; color:var(--text-1); background:var(--bg-card); outline:none; }
    .mfield input:focus, .mfield select:focus, .mfield textarea:focus { border-color:var(--brand-red); box-shadow:0 0 0 3px var(--brand-red-soft); }
    .mfield textarea { min-height:80px; resize:vertical; }
    .mfoot { display:flex; justify-content:flex-end; gap:8px; padding:8px 24px 22px; }
    .m-err { font-size:12.5px; color:var(--danger); margin-bottom:10px; display:none; }
    #toast { position:fixed; right:20px; bottom:20px; z-index:1300; display:none; padding:12px 16px; border-radius:var(--radius-md); font-size:13px; color:#fff; box-shadow:0 12px 32px rgba(0,0,0,.2); }
    #toast.ok { background:var(--success); } #toast.err { background:var(--danger); }
</style>
@endpush

<div class="hr-wrap">
    <div class="hr-head">
        <div>
            <div class="hr-eyebrow">HR Management</div>
            <h1>Leave Requests</h1>
        </div>
        <button type="button" class="btn btn-primary" id="addBtn">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Leave Request
        </button>
    </div>

    <div class="tabs" id="statusTabs">
        <button type="button" class="tab-pill active" data-status="">All (<span data-c="all">{{ $counts['all'] }}</span>)</button>
        <button type="button" class="tab-pill" data-status="pending">Pending (<span data-c="pending">{{ $counts['pending'] }}</span>)</button>
        <button type="button" class="tab-pill" data-status="approved">Approved (<span data-c="approved">{{ $counts['approved'] }}</span>)</button>
        <button type="button" class="tab-pill" data-status="rejected">Rejected (<span data-c="rejected">{{ $counts['rejected'] }}</span>)</button>
    </div>

    <div class="toolbar">
        <select id="fltDept">
            <option value="">All Departments</option>
            @foreach($departments as $d)<option value="{{ $d->id }}">{{ $d->department_name }}</option>@endforeach
        </select>
        <select id="fltType">
            <option value="">All Types</option>
            @foreach($leaveTypes as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach
        </select>
    </div>

    <div class="card">
        <div style="overflow-x:auto;">
            <table id="lvTable" class="table dataTable" style="width:100%">
                <thead>
                    <tr><th>Employee</th><th>Leave Type</th><th>From</th><th>To</th><th>Days</th><th>Reason</th><th>Status</th><th>Action</th></tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- Add Leave Request modal --}}
<div class="modal-ov" id="lvModal">
    <div class="modal-bx">
        <div class="mhead">
            <h3>Add Leave Request</h3>
            <button type="button" class="mclose" data-close>&times;</button>
        </div>
        <div class="mbody">
            <div class="m-err" id="lvErr"></div>
            <div class="mfield">
                <label>Employee <span class="req">*</span></label>
                <select id="lvEmp">
                    <option value="">Select employee…</option>
                    @foreach($employees as $e)<option value="{{ $e->id }}">{{ $e->full_name }}</option>@endforeach
                </select>
            </div>
            <div class="mfield">
                <label>Leave Type <span class="req">*</span></label>
                <select id="lvType">
                    <option value="">Select leave type…</option>
                    @foreach($leaveTypes as $t)<option value="{{ $t }}">{{ $t }}</option>@endforeach
                </select>
            </div>
            <div class="mrow3">
                <div class="mfield"><label>From Date <span class="req">*</span></label><input type="date" id="lvFrom"></div>
                <div class="mfield"><label>To Date <span class="req">*</span></label><input type="date" id="lvTo"></div>
                <div class="mfield"><label>Days <span class="req">*</span></label><input type="number" id="lvDays" min="0.5" step="0.5"></div>
            </div>
            <div class="mfield"><label>Reason</label><textarea id="lvReason" placeholder="Enter reason for leave…"></textarea></div>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-secondary" data-close>Cancel</button>
            <button type="button" class="btn btn-primary" id="lvSave">Save</button>
        </div>
    </div>
</div>
<div id="toast"></div>

<script src="{{ asset('assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('assets/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const gid = id => document.getElementById(id);
    const base = "{{ url('hr/leave-requests') }}";
    @php $initStatus = in_array(request('status'), ['pending', 'approved', 'rejected'], true) ? request('status') : ''; @endphp
    let curStatus = @json($initStatus);
    function toast(m, ok) { const t = gid('toast'); t.textContent = m; t.className = ok ? 'ok' : 'err'; t.style.display = 'block'; setTimeout(() => t.style.display = 'none', 2600); }

    const table = $('#lvTable').DataTable({
        processing: true, serverSide: true, order: [], pageLength: 10, lengthMenu: [10, 25, 50, 100],
        language: { search: '', searchPlaceholder: 'Search employees…', emptyTable: 'No leave requests found', zeroRecords: 'No leave requests found' },
        ajax: {
            url: "{{ route('hr.leave-requests.data') }}",
            data: d => { d.status = curStatus; d.department_id = $('#fltDept').val(); d.leave_type = $('#fltType').val(); }
        },
        columns: [
            { data: 'employee', name: 'hr_employees.full_name' },
            { data: 'leave_type_txt', name: 'leave_type' },
            { data: 'from_fmt', name: 'from_date' },
            { data: 'to_fmt', name: 'to_date' },
            { data: 'days_txt', name: 'days' },
            { data: 'reason_txt', name: 'reason', orderable: false },
            { data: 'status_badge', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
    });

    // Status tabs.
    $('#statusTabs .tab-pill').on('click', function () {
        $('#statusTabs .tab-pill').removeClass('active'); $(this).addClass('active');
        curStatus = $(this).data('status'); table.ajax.reload();
    });
    // Honor an initial ?status= (e.g. from the dashboard "View" link).
    if (curStatus) {
        $('#statusTabs .tab-pill').removeClass('active');
        $('#statusTabs .tab-pill[data-status="' + curStatus + '"]').addClass('active');
    }
    $('#fltDept, #fltType').on('change', () => table.ajax.reload());

    function refreshCounts() {
        fetch("{{ route('hr.leave-requests.counts') }}", { headers: { 'Accept': 'application/json' } })
            .then(r => r.json()).then(c => { ['all', 'pending', 'approved', 'rejected'].forEach(k => { const el = document.querySelector('[data-c="' + k + '"]'); if (el) el.textContent = c[k]; }); });
    }

    // ── Modal ──
    const modal = gid('lvModal');
    document.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => modal.classList.remove('open')));
    modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('open'); });

    function calcDays() {
        const f = gid('lvFrom').value, t = gid('lvTo').value;
        if (!f || !t) { return; }
        const d = Math.round((new Date(t) - new Date(f)) / 86400000) + 1;
        if (d >= 1) gid('lvDays').value = d;
    }
    gid('lvFrom').addEventListener('change', calcDays);
    gid('lvTo').addEventListener('change', calcDays);

    gid('addBtn').addEventListener('click', function () {
        gid('lvErr').style.display = 'none';
        gid('lvEmp').value = ''; gid('lvType').value = ''; gid('lvFrom').value = ''; gid('lvTo').value = ''; gid('lvDays').value = ''; gid('lvReason').value = '';
        modal.classList.add('open');
    });

    gid('lvSave').addEventListener('click', function () {
        const err = gid('lvErr'); err.style.display = 'none';
        if (!gid('lvEmp').value) { err.textContent = 'Please select an employee.'; err.style.display = 'block'; return; }
        if (!gid('lvType').value) { err.textContent = 'Please select a leave type.'; err.style.display = 'block'; return; }
        if (!gid('lvFrom').value || !gid('lvTo').value) { err.textContent = 'Please select the from/to dates.'; err.style.display = 'block'; return; }
        const btn = this; btn.disabled = true;
        const body = new URLSearchParams({ _token: CSRF, employee_id: gid('lvEmp').value, leave_type: gid('lvType').value, from_date: gid('lvFrom').value, to_date: gid('lvTo').value, days: gid('lvDays').value, reason: gid('lvReason').value });
        fetch(base, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body })
            .then(r => r.json().then(j => ({ s: r.status, j })))
            .then(({ s, j }) => {
                btn.disabled = false;
                if (!j.ok) { err.textContent = j.message || (j.errors ? Object.values(j.errors)[0][0] : 'Save failed.'); err.style.display = 'block'; return; }
                modal.classList.remove('open'); toast(j.message, true); table.ajax.reload(null, false); refreshCounts();
            }).catch(() => { btn.disabled = false; err.textContent = 'Save failed.'; err.style.display = 'block'; });
    });

    // Row actions: approve / reject / delete.
    function act(id, url, payload, confirmMsg) {
        if (confirmMsg && !confirm(confirmMsg)) return;
        fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: new URLSearchParams(payload) })
            .then(r => r.json()).then(d => { toast(d.message, d.ok); if (d.ok) { table.ajax.reload(null, false); refreshCounts(); } });
    }
    $('#lvTable tbody').on('click', '.app-lv', function () { act($(this).data('id'), base + '/' + $(this).data('id') + '/status', { _token: CSRF, status: 'approved' }); });
    $('#lvTable tbody').on('click', '.rej-lv', function () { act($(this).data('id'), base + '/' + $(this).data('id') + '/status', { _token: CSRF, status: 'rejected' }); });
    $('#lvTable tbody').on('click', '.del-lv', function () { act($(this).data('id'), base + '/' + $(this).data('id'), { _token: CSRF, _method: 'DELETE' }, 'Delete this leave request?'); });
})();
</script>
</x-layouts.app>
