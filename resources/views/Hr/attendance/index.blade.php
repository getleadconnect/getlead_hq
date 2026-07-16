<x-layouts.app title="Attendance">
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/datatables.net/css/jquery.dataTables.css') }}">
<style>
    .hr-wrap { padding: 24px 28px 48px; width: 100%; }
    .hr-head { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:18px; }
    .hr-eyebrow { font-size:11px; font-weight:600; letter-spacing:.09em; text-transform:uppercase; color:var(--brand-red); }
    .hr-head h1 { font-size:24px; font-weight:600; letter-spacing:-.5px; color:var(--text-1); margin-top:4px; }

    .btn { display:inline-flex; align-items:center; gap:7px; padding:9px 15px; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:500; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
    .btn-primary { background:var(--brand-red); color:#fff; } .btn-primary:hover { background:var(--brand-red-dark); }
    .btn-secondary { background:var(--bg-card); color:var(--text-1); border-color:var(--border); } .btn-secondary:hover { border-color:var(--text-3); }

    .toolbar { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:12px 14px; display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:18px; }
    .toolbar select, .toolbar input[type=date] { height:36px; border:1px solid var(--border); border-radius:var(--radius-sm); padding:0 11px; font-family:inherit; font-size:13px; color:var(--text-1); background:var(--bg-card); outline:none; }
    .toolbar select { min-width:160px; }
    .toolbar select:focus, .toolbar input[type=date]:focus { border-color:var(--brand-red); box-shadow:0 0 0 3px var(--brand-red-soft); }
    .toolbar .to { font-size:13px; color:var(--text-3); }

    .card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:8px 16px 16px; }

    .emp-cell { display:flex; align-items:center; gap:10px; }
    .emp-avatar-fb { width:34px; height:34px; border-radius:50%; background:var(--brand-red-soft); color:var(--brand-red-dark); border:1px solid var(--brand-red-border); font-size:12px; font-weight:600; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .emp-cell .nm { color:var(--text-1); font-weight:500; }
    .emp-cell .em { font-size:11px; color:var(--text-3); }
    .text-muted { color:var(--text-3); }

    .att-badge { display:inline-block; padding:3px 10px; border-radius:var(--radius-pill); font-size:11px; font-weight:600; }
    .st-present { background:var(--success-soft); color:var(--success-text); border:1px solid var(--success-border); }
    .st-absent { background:var(--danger-soft); color:var(--danger-text); border:1px solid var(--danger-border); }
    .st-half-day { background:var(--warning-soft); color:var(--warning-text); border:1px solid var(--warning-border); }
    .st-on-leave { background:#EFF6FF; color:#1D4ED8; border:1px solid #BFDBFE; }
    .row-acts { display:flex; gap:6px; }
    .ico-btn { border:1px solid var(--border); background:var(--bg-card); border-radius:var(--radius-sm); padding:4px 8px; cursor:pointer; font-size:13px; }
    .ico-btn:hover { background:var(--bg-neutral); }
    .del-att:hover { background:var(--brand-red-soft); border-color:var(--brand-red-border); }

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
    .modal-bx { background:var(--bg-card); border-radius:var(--radius-lg); width:100%; max-width:600px; box-shadow:0 24px 60px rgba(0,0,0,.25); overflow:hidden; }
    .mhead { padding:20px 24px 4px; position:relative; }
    .mhead h3 { font-size:17px; font-weight:700; color:var(--text-1); }
    .mclose { position:absolute; top:18px; right:20px; border:none; background:none; font-size:20px; color:var(--text-3); cursor:pointer; }
    .mclose:hover { color:var(--text-1); }
    .mbody { padding:16px 24px; }
    .mrow { display:grid; grid-template-columns:1fr 1fr; gap:16px; }
    @media(max-width:540px){ .mrow{ grid-template-columns:1fr; } }
    .mfield { margin-bottom:16px; }
    .mfield > label { display:block; font-size:13px; font-weight:500; color:var(--text-1); margin-bottom:6px; }
    .mfield .req { color:var(--brand-red); }
    .mfield input, .mfield select, .mfield textarea { width:100%; border:1px solid var(--border); border-radius:var(--radius-sm); padding:9px 11px; font-family:inherit; font-size:13.5px; color:var(--text-1); background:var(--bg-card); outline:none; }
    .mfield input:focus, .mfield select:focus, .mfield textarea:focus { border-color:var(--brand-red); box-shadow:0 0 0 3px var(--brand-red-soft); }
    .mfield textarea { min-height:74px; resize:vertical; }
    .radios { display:flex; gap:22px; }
    .radios label { display:flex; align-items:center; gap:7px; font-size:13.5px; color:var(--text-2); cursor:pointer; font-weight:400; }
    .radios input { width:16px; height:16px; accent-color:var(--brand-red); }
    .time-grp { display:flex; align-items:center; gap:6px; }
    .time-grp input { width:65px; text-align:center; } 
    .time-grp select { width:75px; } 
    .time-grp .colon { color:var(--text-3); }
    .total-box { background:var(--bg-page); border:1px solid var(--border); border-radius:var(--radius-sm); padding:11px; text-align:center; font-size:14px; font-weight:600; color:var(--text-1); }
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
            <h1>Attendance Management</h1>
        </div>
        <div style="display:flex; gap:8px; flex-wrap:wrap;">
            <button type="button" class="btn btn-primary" id="markBtn">
                <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                Mark Attendance
            </button>
            <a href="{{ route('hr.attendance.export') }}" id="exportBtn" class="btn btn-secondary">
                <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                Export Report
            </a>
        </div>
    </div>

    <div class="toolbar">
        <select id="fltEmp">
            <option value="">All Employees</option>
            @foreach($employees as $e)
                <option value="{{ $e->id }}">{{ $e->full_name }}</option>
            @endforeach
        </select>
        <input type="date" id="fltStart" value="{{ now()->toDateString() }}">
        <span class="to">to</span>
        <input type="date" id="fltEnd" value="{{ now()->toDateString() }}">
        <select id="fltStatus">
            <option value="">All Status</option>
            @foreach($statuses as $k => $v)
                <option value="{{ $k }}">{{ $v }}</option>
            @endforeach
        </select>
        <button type="button" class="btn btn-primary" id="applyFilter">Filter</button>
        <button type="button" class="btn btn-secondary" id="clearFilter">Clear</button>
    </div>

    <div class="card">
        <div style="overflow-x:auto;">
            <table id="attTable" class="table dataTable" style="width:100%">
                <thead>
                    <tr><th>Employee</th><th>Date</th><th>Check In</th><th>Check Out</th><th>Hours</th><th>Notes</th><th>Status</th><th>Action</th></tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- Mark Attendance modal --}}
<div class="modal-ov" id="attModal">
    <div class="modal-bx">
        <div class="mhead">
            <h3 id="attModalTitle">Mark Employee Attendance</h3>
            <button type="button" class="mclose" data-close>&times;</button>
        </div>
        <div class="mbody">
            <input type="hidden" id="attId">
            <div class="m-err" id="attErr"></div>
            <div class="mrow">
                <div class="mfield"><label>Date <span class="req">*</span></label><input type="date" id="attDate" value="{{ now()->toDateString() }}"></div>
                <div class="mfield">
                    <label>Employee <span class="req">*</span></label>
                    <select id="attEmployee">
                        <option value="">Choose employee…</option>
                    </select>
                </div>
            </div>
            <div class="mfield">
                <label>Attendance Status <span class="req">*</span></label>
                <div class="radios">
                    <label><input type="radio" name="attStatus" value="present" checked> Present</label>
                    <label><input type="radio" name="attStatus" value="absent"> Absent</label>
                    <label><input type="radio" name="attStatus" value="half_day"> Half Day</label>
                </div>
            </div>
            <div class="mrow">
                <div class="mfield">
                    <label>Check In Time</label>
                    <div class="time-grp">
                        <input type="number" id="ciH" min="1" max="12" placeholder="HH"><span class="colon">:</span>
                        <input type="number" id="ciM" min="0" max="59" placeholder="MM">
                        <select id="ciAP"><option>AM</option><option>PM</option></select>
                    </div>
                </div>
                <div class="mfield">
                    <label>Check Out Time</label>
                    <div class="time-grp">
                        <input type="number" id="coH" min="1" max="12" placeholder="HH"><span class="colon">:</span>
                        <input type="number" id="coM" min="0" max="59" placeholder="MM">
                        <select id="coAP"><option>AM</option><option>PM</option></select>
                    </div>
                </div>
            </div>
            <div class="mfield">
                <label>Total Hours</label>
                <div class="total-box" id="attTotal">--</div>
            </div>
            <div class="mfield">
                <label>Notes</label>
                <textarea id="attNotes" placeholder="Add notes (optional)"></textarea>
            </div>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-secondary" data-close>Cancel</button>
            <button type="button" class="btn btn-primary" id="attSave">Save Attendance</button>
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
    const base = "{{ url('hr/attendance') }}";
    function toast(m, ok) { const t = gid('toast'); t.textContent = m; t.className = ok ? 'ok' : 'err'; t.style.display = 'block'; setTimeout(() => t.style.display = 'none', 2600); }

    const table = $('#attTable').DataTable({
        processing: true, serverSide: true, order: [], pageLength: 10, lengthMenu: [10, 25, 50, 100],
        language: { search: '', searchPlaceholder: 'Search employees…', emptyTable: 'No attendance records found for selected filters', zeroRecords: 'No attendance records found for selected filters' },
        ajax: {
            url: "{{ route('hr.attendance.data') }}",
            data: d => { d.employee_id = $('#fltEmp').val(); d.status = $('#fltStatus').val(); d.start_date = $('#fltStart').val(); d.end_date = $('#fltEnd').val(); }
        },
        columns: [
            { data: 'employee', name: 'hr_employees.full_name' },
            { data: 'date_fmt', name: 'attendance_date' },
            { data: 'check_in_fmt', name: 'check_in', orderable: false },
            { data: 'check_out_fmt', name: 'check_out', orderable: false },
            { data: 'hours_fmt', name: 'hours' },
            { data: 'notes', name: 'remarks', orderable: false },
            { data: 'status_badge', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
    });

    $('#applyFilter').on('click', () => table.ajax.reload());
    $('#clearFilter').on('click', () => { $('#fltEmp').val(''); $('#fltStatus').val(''); $('#fltStart').val(''); $('#fltEnd').val(''); table.ajax.reload(); });

    // Export respects the current filters.
    gid('exportBtn').addEventListener('click', function (e) {
        e.preventDefault();
        const p = new URLSearchParams({ employee_id: $('#fltEmp').val(), status: $('#fltStatus').val(), start_date: $('#fltStart').val(), end_date: $('#fltEnd').val() });
        window.location = "{{ route('hr.attendance.export') }}?" + p.toString();
    });

    // ── Modal ──
    const modal = gid('attModal');
    const empUrl = "{{ route('hr.attendance.employees') }}";
    function escHtml(s) { const d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; }

    // Load the employee dropdown grouped into Not Marked / Marked for the given date.
    function loadEmployees(date, selectId) {
        return fetch(empUrl + '?date=' + encodeURIComponent(date), { headers: { 'Accept': 'application/json' } })
            .then(r => r.json()).then(d => {
                if (!d.ok) return;
                const sel = gid('attEmployee');
                let html = '<option value="">Choose employee…</option>';
                const grp = (label, arr) => arr.length
                    ? '<optgroup label="' + label + '">' + arr.map(e => '<option value="' + e.id + '">' + escHtml(e.name) + '</option>').join('') + '</optgroup>' : '';
                html += grp('Not Marked Employees', d.not_marked);
                html += grp('Marked Employees', d.marked);
                sel.innerHTML = html;
                if (selectId) sel.value = selectId;
            });
    }

    // Regroup the employee list whenever the date changes.
    gid('attDate').addEventListener('change', () => loadEmployees(gid('attDate').value));

    document.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => modal.classList.remove('open')));
    modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('open'); });

    function to24(h, m, ap) { h = parseInt(h, 10); m = parseInt(m, 10); if (isNaN(h) || isNaN(m)) return null; if (ap === 'PM' && h < 12) h += 12; if (ap === 'AM' && h === 12) h = 0; return h * 60 + m; }
    function computeTotal() {
        const ci = to24(gid('ciH').value, gid('ciM').value, gid('ciAP').value);
        const co = to24(gid('coH').value, gid('coM').value, gid('coAP').value);
        if (ci === null || co === null) { gid('attTotal').textContent = '--'; return; }
        let diff = Math.abs(co - ci) / 60;
        gid('attTotal').textContent = (Math.round(diff * 100) / 100) + ' hrs';
    }
    ['ciH', 'ciM', 'ciAP', 'coH', 'coM', 'coAP'].forEach(id => gid(id).addEventListener('input', computeTotal));

    function timeStr(h, m, ap) { h = gid(h).value.trim(); m = gid(m).value.trim(); if (h === '' || m === '') return ''; return String(h).padStart(2, '0') + ':' + String(m).padStart(2, '0') + ' ' + ap; }
    function setTime(str, hId, mId, apId) {
        if (!str) { gid(hId).value = ''; gid(mId).value = ''; gid(apId).value = 'AM'; return; }
        const mt = str.match(/(\d+):(\d+)\s*(AM|PM)/i);
        if (mt) { gid(hId).value = parseInt(mt[1], 10); gid(mId).value = mt[2]; gid(apId).value = mt[3].toUpperCase(); }
    }
    function resetModal() {
        gid('attId').value = ''; gid('attErr').style.display = 'none';
        gid('attModalTitle').textContent = 'Mark Employee Attendance';
        gid('attDate').value = "{{ now()->toDateString() }}"; gid('attEmployee').value = '';
        document.querySelector('input[name=attStatus][value=present]').checked = true;
        ['ciH','ciM','coH','coM'].forEach(i => gid(i).value = ''); gid('ciAP').value = 'AM'; gid('coAP').value = 'AM';
        gid('attNotes').value = ''; gid('attTotal').textContent = '--';
    }

    gid('markBtn').addEventListener('click', () => { resetModal(); loadEmployees(gid('attDate').value); modal.classList.add('open'); });

    $('#attTable tbody').on('click', '.edit-att', function () {
        fetch(base + '/' + $(this).data('id') + '/edit', { headers: { 'Accept': 'application/json' } })
            .then(r => r.json()).then(d => {
                if (!d.ok) return;
                resetModal();
                gid('attModalTitle').textContent = 'Edit Employee Attendance';
                gid('attId').value = d.row.id; gid('attDate').value = d.row.attendance_date;
                const st = document.querySelector('input[name=attStatus][value="' + d.row.status + '"]'); if (st) st.checked = true;
                setTime(d.row.check_in, 'ciH', 'ciM', 'ciAP'); setTime(d.row.check_out, 'coH', 'coM', 'coAP');
                gid('attNotes').value = d.row.remarks || ''; computeTotal();
                loadEmployees(d.row.attendance_date, d.row.employee_id);
                modal.classList.add('open');
            });
    });

    gid('attSave').addEventListener('click', function () {
        const err = gid('attErr'); err.style.display = 'none';
        const empId = gid('attEmployee').value;
        if (!gid('attDate').value) { err.textContent = 'Please select a date.'; err.style.display = 'block'; return; }
        if (!empId) { err.textContent = 'Please choose an employee.'; err.style.display = 'block'; return; }
        const btn = this; btn.disabled = true;
        const body = new URLSearchParams({
            _token: CSRF, id: gid('attId').value, employee_id: empId, attendance_date: gid('attDate').value,
            status: document.querySelector('input[name=attStatus]:checked').value,
            check_in: timeStr('ciH', 'ciM', gid('ciAP').value), check_out: timeStr('coH', 'coM', gid('coAP').value),
            remarks: gid('attNotes').value,
        });
        fetch(base, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body })
            .then(r => r.json().then(j => ({ s: r.status, j })))
            .then(({ s, j }) => {
                btn.disabled = false;
                if (!j.ok) { err.textContent = j.message || (j.errors ? Object.values(j.errors)[0][0] : 'Save failed.'); err.style.display = 'block'; return; }
                toast(j.message, true);
                table.ajax.reload(null, false);
                // Keep the modal open so several employees can be marked in a row.
                const editing = gid('attId').value;
                if (editing) {
                    // After an edit, close it — editing is a one-off.
                    modal.classList.remove('open');
                } else {
                    // Reset for the next entry (keep the date), refresh the grouped list.
                    gid('attEmployee').value = '';
                    ['ciH', 'ciM', 'coH', 'coM'].forEach(i => gid(i).value = ''); gid('ciAP').value = 'AM'; gid('coAP').value = 'AM';
                    gid('attNotes').value = ''; gid('attTotal').textContent = '--';
                    document.querySelector('input[name=attStatus][value=present]').checked = true;
                    loadEmployees(gid('attDate').value);
                }
            }).catch(() => { btn.disabled = false; err.textContent = 'Save failed.'; err.style.display = 'block'; });
    });

    $('#attTable tbody').on('click', '.del-att', function () {
        if (!confirm('Delete this attendance record?')) return;
        fetch(base + '/' + $(this).data('id'), { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: new URLSearchParams({ _method: 'DELETE' }) })
            .then(r => r.json()).then(d => { toast(d.message, d.ok); if (d.ok) table.ajax.reload(null, false); });
    });
})();
</script>
</x-layouts.app>
