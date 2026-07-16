<x-layouts.app title="HR Employees">
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/datatables.net/css/jquery.dataTables.css') }}">
<style>
    .hr-wrap { padding: 24px 28px 48px; width: 100%; }
    .hr-head { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom: 18px; }
    .hr-eyebrow { font-size: 11px; font-weight: 600; letter-spacing: .09em; text-transform: uppercase; color: var(--brand-red); }
    .hr-head h1 { font-size: 24px; font-weight: 600; letter-spacing: -.5px; color: var(--text-1); margin-top: 4px; }
    .hr-head p { font-size: 13px; color: var(--text-2); margin-top: 4px; }

    .btn { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:500; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn-primary { background: var(--brand-red); color:#fff; } .btn-primary:hover { background: var(--brand-red-dark); }
    .btn-secondary { background: var(--bg-card); color: var(--text-1); border-color: var(--border); } .btn-secondary:hover { border-color: var(--text-3); }

    .emp-stats { display:flex; gap:10px; flex-wrap:wrap; margin-left:auto; }
    .stat { background:var(--bg-page); border:1px solid var(--border); border-radius:var(--radius-md); padding:6px 16px; text-align:center; }
    .stat .n { font-size:18px; font-weight:600; color:var(--text-1); line-height:1.2; }
    .stat .l { font-size:11px; color:var(--text-2); }

    .toolbar { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:12px 14px; display:flex; align-items:flex-end; gap:10px; flex-wrap:wrap; margin-bottom:18px; }
    .toolbar .fld { display:flex; flex-direction:column; gap:5px; }
    .toolbar label { font-size:11.5px; font-weight:500; color:var(--text-2); }
    .toolbar select, .toolbar input[type=date] { height:36px; min-width:150px; border:1px solid var(--border); border-radius:var(--radius-sm); padding:0 11px; font-family:inherit; font-size:13px; color:var(--text-1); background:var(--bg-card); outline:none; }
    .toolbar select { min-width:170px; }
    .toolbar select:focus, .toolbar input[type=date]:focus { border-color:var(--brand-red); box-shadow:0 0 0 3px var(--brand-red-soft); }
    .toolbar .spacer { flex:1; }

    .view-toggle { display:inline-flex; border:1px solid var(--border); border-radius:var(--radius-sm); overflow:hidden; }
    .view-toggle button { border:none; background:var(--bg-card); padding:8px 12px; cursor:pointer; color:var(--text-2); display:flex; align-items:center; gap:6px; font-family:inherit; font-size:13px; }
    .view-toggle button svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2; }
    .view-toggle button.active { background:var(--brand-red); color:#fff; }
    .view-toggle button + button { border-left:1px solid var(--border); }

    /* Avatars + badges (shared) */
    .emp-avatar { width:40px; height:40px; border-radius:50%; object-fit:cover; border:1px solid var(--border); background:var(--bg-neutral); flex-shrink:0; }
    .emp-avatar-fb { width:40px; height:40px; border-radius:50%; background:var(--brand-red-soft); color:var(--brand-red-dark); border:1px solid var(--brand-red-border); font-size:14px; font-weight:600; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .badge { display:inline-block; padding:3px 10px; border-radius:var(--radius-pill); font-size:11px; font-weight:600; }
    .b-active { background:var(--success-soft); color:var(--success-text); border:1px solid var(--success-border); }
    .b-inactive { background:var(--bg-neutral); color:var(--text-2); border:1px solid var(--border); }
    .emp-cell { display:flex; align-items:center; gap:11px; }
    .emp-cell .nm { color:var(--text-1); font-weight:500; }
    .emp-cell .em { font-size:11.5px; color:var(--text-3); }

    .card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:8px 16px 16px; }

    /* DataTables base theming (matches Applications page) */
    table.dataTable thead th { text-align:left; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--text-3); border-bottom:1px solid var(--border) !important; padding:12px; }
    table.dataTable tbody td { font-size:13px; color:var(--text-2); border-bottom:1px solid var(--border-soft) !important; padding:10px 12px; vertical-align:middle; }
    table.dataTable tbody tr:hover { background:var(--bg-page); }

    /* Card view */
    .card-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(228px,1fr)); gap:18px; }
    .emp-card { position:relative; background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:24px 18px 18px; text-align:center; transition:box-shadow .15s, border-color .15s; }
    .emp-card:hover { border-color:var(--text-3); box-shadow:0 8px 24px rgba(0,0,0,.06); }
    .emp-card .emp-status { position:absolute; top:12px; right:12px; }
    .emp-card .emp-avatar, .emp-card .emp-avatar-fb { width:70px; height:70px; margin:4px auto 12px; }
    .emp-card .emp-avatar-fb { background:var(--brand-red-soft); color:var(--brand-red-dark); border:1px solid var(--brand-red-border); font-size:22px; font-weight:600; }
    .emp-card .nm { font-size:15px; font-weight:600; color:var(--text-1); }
    .emp-card .role { font-size:12.5px; color:var(--text-2); margin-top:3px; }
    .emp-card .dept-pill { display:inline-block; margin-top:10px; font-size:11.5px; color:var(--text-2); background:var(--bg-neutral); border:1px solid var(--border); padding:3px 12px; border-radius:var(--radius-pill); }
    .emp-card .emp-stat-row { display:flex; justify-content:space-between; align-items:center; margin-top:16px; padding-top:14px; border-top:1px solid var(--border-soft); font-size:11.5px; color:var(--text-3); }
    .emp-card .emp-stat-row b { color:var(--text-1); font-size:13px; font-weight:600; margin-right:3px; }
    .emp-card .emp-view-btn { display:block; margin-top:14px; padding:9px; border-radius:var(--radius-sm); background:var(--brand-red-soft); color:var(--brand-red-dark); font-size:13px; font-weight:500; text-decoration:none; border:1px solid var(--brand-red-border); }
    .emp-card .emp-view-btn:hover { background:var(--brand-red); color:#fff; border-color:var(--brand-red); }
    .card-empty { text-align:center; color:var(--text-3); padding:44px 16px; grid-column:1/-1; }
    .hidden { display:none !important; }

    .alink{text-decoration:none;color:#4b4bdc;}

</style>
@endpush

<div class="hr-wrap">
    <div class="hr-head">
        <div>
            <div class="hr-eyebrow">HR Management</div>
            <h1>Employees</h1>
            <p>Company employee directory.</p>
        </div>
        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;">
            <div class="view-toggle" id="viewToggle">
                <button type="button" data-view="list" class="active">
                    <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    List
                </button>
                <button type="button" data-view="card">
                    <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    Card
                </button>
            </div>
            <a href="{{ route('hr.employees.create') }}" class="btn btn-primary">
                <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                New Employee
            </a>
        </div>
    </div>

    <div class="toolbar">
        <div class="fld">
            <label>From Date</label>
            <input type="date" id="fltStart">
        </div>
        <div class="fld">
            <label>To Date</label>
            <input type="date" id="fltEnd">
        </div>
        <div class="fld">
            <label>Department</label>
            <select id="fltDept">
                <option value="">All departments</option>
                @foreach($departments as $d)
                    <option value="{{ $d->id }}">{{ $d->department_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="fld">
            <label>Designation</label>
            <select id="fltDesig">
                <option value="">All designations</option>
                @foreach($designations as $ds)
                    <option value="{{ $ds->id }}">{{ $ds->designation_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="fld">
            <label>Status</label>
            <select id="fltStatus">
                <option value="">All</option>
                <option value="1">Active</option>
                <option value="0">Inactive</option>
            </select>
        </div>
        <button type="button" class="btn btn-primary" id="applyFilter">Filter</button>
        <button type="button" class="btn btn-secondary" id="clearFilter">Clear</button>

        <div class="emp-stats">
            <div class="stat"><div class="n num">{{ $counts['total'] }}</div><div class="l">Total</div></div>
            <div class="stat"><div class="n num">{{ $counts['active'] }}</div><div class="l">Active</div></div>
            <div class="stat"><div class="n num">{{ $counts['inactive'] }}</div><div class="l">Inactive</div></div>
        </div>
    </div>

    {{-- LIST VIEW (DataTable) --}}
    <div class="card view-pane" id="listView">
        <div style="overflow-x:auto;">
            <table id="empTable" class="table dataTable" style="width:100%">
                <thead>
                    <tr>
                        <th>SlNo</th><th>Employee</th><th>Emp ID</th><th>Mobile</th><th>Job Title</th>
                        <th>Department</th><th>Designation</th><th>Join Date</th><th>Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    {{-- CARD VIEW (rendered from the current DataTable page) --}}
    <div class="card-grid view-pane hidden" id="cardView"></div>
</div>

<script src="{{ asset('assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('assets/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script>
(function () {
    function esc(s) { return $('<div>').text(s == null ? '' : s).html(); }
    function initials2(name) {
        const p = (name || 'E').trim().split(/\s+/);
        let s = (p[0] || '').charAt(0);
        s += p.length > 1 ? (p[1] || '').charAt(0) : ((p[0] || '').charAt(1) || '');
        return s.toUpperCase();
    }
    const monthDays = new Date(new Date().getFullYear(), new Date().getMonth() + 1, 0).getDate();

    const table = $('#empTable').DataTable({
        processing: true,
        serverSide: true,
        order: [],
        pageLength: 10,
        lengthMenu: [10, 25, 50, 100],
        language: { search: '', searchPlaceholder: 'Search employees…' },
        ajax: {
            url: "{{ route('hr.employees.data') }}",
            data: function (d) {
                d.start_date = $('#fltStart').val();
                d.end_date = $('#fltEnd').val();
                d.department_id = $('#fltDept').val();
                d.designation_id = $('#fltDesig').val();
                d.status = $('#fltStatus').val();
            }
        },

        columnDefs:[
            {width:"100px",targets:7},
        ],

        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'employee', name: 'full_name' },
            { data: 'employee_id', name: 'employee_id' },
            { data: 'mobile_number', name: 'mobile_number' },
            { data: 'job_title', name: 'job_title' },
            { data: 'department_name', name: 'hr_departments.department_name' },
            { data: 'designation_name', name: 'hr_designations.designation_name' },
            { data: 'join_date_fmt', name: 'join_date' },
            { data: 'status_badge', name: 'status', orderable: false },
        ],
    });

    // Rebuild the card grid from the current page's rows on every draw.
    table.on('draw', function () {
        const rows = table.rows({ page: 'current' }).data().toArray();
        const $grid = $('#cardView').empty();
        if (!rows.length) { $grid.html('<div class="card-empty">No employees found.</div>'); return; }
        rows.forEach(function (r) {
            const ini = initials2(r.full_name);
            const avatar = r.photo_url
                ? '<img src="' + esc(r.photo_url) + '" class="emp-avatar" onerror="this.outerHTML=\'<div class=&quot;emp-avatar-fb&quot;>' + ini + '</div>\'">'
                : '<div class="emp-avatar-fb">' + ini + '</div>';
            const desig = (r.designation_name && r.designation_name !== '—') ? r.designation_name : 'No Designation';
            const dept  = (r.department_name && r.department_name !== '—') ? r.department_name : 'No Department';
            const badge = r.status == 1
                ? '<span class="badge b-active">Active</span>' : '<span class="badge b-inactive">Inactive</span>';
            $grid.append(
                '<div class="emp-card">' +
                    '<span class="emp-status">' + badge + '</span>' +
                    avatar +
                    '<div class="nm">' + esc(r.full_name) + '</div>' +
                    '<div class="role">' + esc(desig) + '</div>' +
                    '<div><span class="dept-pill">' + esc(dept) + '</span></div>' +
                    '<div class="emp-stat-row">' +
                        '<div><b>0%</b>Attendance</div>' +
                        '<div><b>0/' + monthDays + '</b>Leave</div>' +
                    '</div>' +
                    '<a href="{{ url('hr/employees') }}/' + r.id + '" class="emp-view-btn">View Details</a>' +
                '</div>'
            );
        });
    });

    $('#applyFilter').on('click', () => table.ajax.reload());
    $('#clearFilter').on('click', () => { $('#fltStart').val(''); $('#fltEnd').val(''); $('#fltDept').val(''); $('#fltDesig').val(''); $('#fltStatus').val(''); table.ajax.reload(); });

    // View toggle (both views stay in sync with the DataTable's search/pagination).
    const toggle = document.getElementById('viewToggle');
    const listView = document.getElementById('listView');
    const cardView = document.getElementById('cardView');
    function setView(v) {
        listView.classList.toggle('hidden', v !== 'list');
        cardView.classList.toggle('hidden', v !== 'card');
        toggle.querySelectorAll('button').forEach(b => b.classList.toggle('active', b.dataset.view === v));
        try { localStorage.setItem('hrEmpView', v); } catch (e) {}
    }
    toggle.querySelectorAll('button').forEach(b => b.addEventListener('click', () => setView(b.dataset.view)));
    let saved = 'list';
    try { saved = localStorage.getItem('hrEmpView') || 'list'; } catch (e) {}
    setView(saved);
})();
</script>
</x-layouts.app>
