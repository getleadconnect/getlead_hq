<x-layouts.app title="HR Job Openings">
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/datatables.net/css/jquery.dataTables.css') }}">
<style>
    .hr-wrap { padding: 24px 28px 48px; width: 100%; }
    .hr-head { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:20px; }
    .hr-eyebrow { font-size:11px; font-weight:600; letter-spacing:.09em; text-transform:uppercase; color:var(--brand-red); }
    .hr-head h1 { font-size:24px; font-weight:600; letter-spacing:-.5px; color:var(--text-1); margin-top:4px; }
    .hr-head p { font-size:13px; color:var(--text-2); margin-top:4px; }

    .btn { display:inline-flex; align-items:center; gap:6px; padding:9px 15px; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:500; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2; }
    .btn-primary { background:var(--brand-red); color:#fff; } .btn-primary:hover { background:var(--brand-red-dark); }
    .btn-secondary { background:var(--bg-card); color:var(--text-1); border-color:var(--border); } .btn-secondary:hover { border-color:var(--text-3); }

    .flash { padding:10px 14px; border-radius:var(--radius-sm); font-size:13px; margin-bottom:16px; }
    .flash-success { background:var(--success-soft); color:var(--success-text); border:1px solid var(--success-border); }

    /* Filter bar */
    .toolbar { background:var(--bg-page); border:1px solid var(--border); border-radius:var(--radius-md); padding:12px 14px; display:flex; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:16px; }
    .toolbar .lbl { font-size:12.5px; font-weight:600; color:var(--text-2); }
    .toolbar input[type=date], .toolbar select { height:38px; border:1px solid var(--border); border-radius:var(--radius-sm); padding:0 10px; font-family:inherit; font-size:13px; color:var(--text-1); background:var(--bg-card); outline:none; }
    .toolbar select { min-width:190px; }
    .toolbar input[type=date]:focus, .toolbar select:focus { border-color:var(--brand-red); box-shadow:0 0 0 3px var(--brand-red-soft); }

    .card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:8px 16px 16px; }
    .link-name { color:var(--brand-red-dark); font-weight:500; text-decoration:none; }
    .link-name:hover { text-decoration:underline; }

    .badge { display:inline-block; padding:3px 11px; border-radius:var(--radius-pill); font-size:11.5px; font-weight:600; }
    .b-open { background:var(--success-soft); color:var(--success-text); border:1px solid var(--success-border); }
    .b-closed { background:var(--bg-neutral); color:var(--text-2); border:1px solid var(--border); }

    /* Row action menu (fixed popup → never clipped) */
    .menu-btn { border:none; background:transparent; cursor:pointer; font-size:18px; line-height:1; color:var(--text-2); padding:4px 8px; border-radius:var(--radius-sm); }
    .menu-btn:hover { background:var(--bg-neutral); color:var(--text-1); }
    #rowMenu { position:fixed; z-index:1000; display:none; min-width:170px; background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-md); box-shadow:0 12px 32px rgba(0,0,0,.14); padding:5px; }
    #rowMenu a { display:flex; align-items:center; gap:9px; padding:9px 11px; font-size:13px; color:var(--text-2); text-decoration:none; border-radius:var(--radius-sm); cursor:pointer; }
    #rowMenu a:hover { background:var(--bg-neutral); color:var(--text-1); }
    #rowMenu a.danger { color:var(--brand-red); }
    #rowMenu a.danger:hover { background:var(--brand-red-soft); }

    #toast { position:fixed; right:20px; bottom:20px; z-index:1100; display:none; padding:12px 16px; border-radius:var(--radius-md); font-size:13px; color:#fff; box-shadow:0 12px 32px rgba(0,0,0,.2); }
    #toast.ok { background:var(--success); } #toast.err { background:var(--danger); }
</style>
@endpush

<div class="hr-wrap">
    <div class="hr-head">
        <div>
            <div class="hr-eyebrow">HR Management</div>
            <h1>All Job Openings</h1>
            <p>Manage job openings and positions</p>
        </div>
        <a href="{{ route('hr.job-openings.create') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add New Job Opening
        </a>
    </div>

    @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif

    <div class="card">
        <div class="toolbar">
            <span class="lbl">Filter By:</span>
            <input type="date" id="fltFrom" aria-label="From date">
            <input type="date" id="fltTo" aria-label="To date">
            <select id="fltCategory">
                <option value="">Select category</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}">{{ $c->category_name }}</option>
                @endforeach
            </select>
            <select id="fltPosition">
                <option value="">Select Position</option>
                @foreach($designations as $d)
                    <option value="{{ $d->id }}">{{ $d->designation_name }}</option>
                @endforeach
            </select>
            <button type="button" class="btn btn-primary" id="applyFilter">Filter</button>
            <button type="button" class="btn btn-secondary" id="clearFilter">Clear</button>
        </div>

        <div style="overflow-x:auto;">
            <table id="joTable" class="table dataTable" style="width:100%">
                <thead>
                    <tr>
                        <th>SlNo</th><th>Posted Date</th><th>Job Title</th><th>Job Category</th>
                        <th>Position</th><th>Job Location</th><th>Closing Date</th><th>Status</th><th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- Shared row-action popup + toast --}}
<div id="rowMenu">
    <a id="rmEdit">✏️ Edit</a>
    <a id="rmToggle">🔄 Toggle Status</a>
    <a id="rmDelete" class="danger">🗑 Delete</a>
</div>
<div id="toast"></div>

<script src="{{ asset('assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('assets/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const base = "{{ url('hr/job-openings') }}";

    function toast(msg, ok) {
        const t = document.getElementById('toast');
        t.textContent = msg; t.className = ok ? 'ok' : 'err'; t.style.display = 'block';
        setTimeout(() => { t.style.display = 'none'; }, 2600);
    }

    const table = $('#joTable').DataTable({
        processing: true,
        serverSide: true,
        paging: true,
        pageLength: 10,
        lengthChange: true,
        pagingType: 'simple_numbers',
        order: [[1, 'desc']],
        language: { search: '', searchPlaceholder: 'Search job openings...' },
        ajax: {
            url: "{{ route('hr.job-openings.data') }}",
            data: function (d) {
                d.from_date = $('#fltFrom').val();
                d.to_date = $('#fltTo').val();
                d.job_category_id = $('#fltCategory').val();
                d.job_designation_id = $('#fltPosition').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'created_at', name: 'hr_job_openings.created_at' },
            { data: 'title', name: 'hr_job_openings.job_title' },
            { data: 'category', name: 'hr_job_category.category_name' },
            { data: 'position', name: 'hr_designations.designation_name' },
            { data: 'job_location', name: 'hr_job_openings.job_location' },
            { data: 'closing', name: 'hr_job_openings.job_closing_date' },
            { data: 'status_badge', name: 'hr_job_openings.status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
    });

    $('#applyFilter').on('click', () => table.ajax.reload());
    $('#clearFilter').on('click', function () {
        $('#fltFrom, #fltTo, #fltCategory, #fltPosition').val('');
        table.ajax.reload();
    });

    // Row action popup.
    const menu = document.getElementById('rowMenu');
    let menuId = null, menuActive = 0;
    $('#joTable tbody').on('click', '.menu-btn', function (e) {
        e.stopPropagation();
        menuId = $(this).data('id');
        menuActive = $(this).data('active');
        document.getElementById('rmToggle').textContent = menuActive ? '🔄 Mark as Closed' : '🔄 Mark as Active';
        const r = this.getBoundingClientRect();
        menu.style.display = 'block';
        let left = r.right - menu.offsetWidth;
        if (left < 8) left = 8;
        menu.style.left = left + 'px';
        menu.style.top = (r.bottom + 4) + 'px';
    });
    document.addEventListener('click', () => { menu.style.display = 'none'; });

    document.getElementById('rmEdit').addEventListener('click', () => {
        if (menuId) window.location.href = base + '/' + menuId + '/edit';
    });

    document.getElementById('rmToggle').addEventListener('click', function () {
        if (!menuId) return;
        menu.style.display = 'none';
        $.ajax({
            url: base + '/' + menuId + '/toggle',
            type: 'POST', dataType: 'json', data: { _token: CSRF },
            success: (res) => { toast(res.msg, res.status); table.ajax.reload(null, false); },
            error: () => toast('Could not update status.', false),
        });
    });

    document.getElementById('rmDelete').addEventListener('click', function () {
        if (!menuId) return;
        menu.style.display = 'none';
        if (!confirm('Are you sure you want to delete this job opening?')) return;
        $.ajax({
            url: base + '/' + menuId,
            type: 'POST', dataType: 'json', data: { _token: CSRF, _method: 'DELETE' },
            success: (res) => { toast(res.msg, res.status); table.ajax.reload(null, false); },
            error: () => toast('Could not delete.', false),
        });
    });
})();
</script>
</x-layouts.app>
