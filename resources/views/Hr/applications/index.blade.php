<x-layouts.app title="HR Applications">
@push('styles')

<link rel="stylesheet" href="{{asset('assets/datatables.net/css/jquery.dataTables.css')}}">

<style>
    .hr-wrap { padding: 24px 28px 48px; width: 100%; }
    .hr-head { margin-bottom: 20px; }
    .hr-eyebrow { font-size: 11px; font-weight: 600; letter-spacing: .09em; text-transform: uppercase; color: var(--brand-red); }
    .hr-head h1 { font-size: 24px; font-weight: 600; letter-spacing: -.5px; color: var(--text-1); margin-top: 4px; }
    .hr-head p { font-size: 13px; color: var(--text-2); margin-top: 4px; }

    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 8px 14px; border-radius: var(--radius-sm); font-family: inherit; font-size: 13px; font-weight: 500; cursor: pointer; border: 1px solid transparent; text-decoration: none; }
    .btn-primary { background: var(--brand-red); color: #fff; }
    .btn-primary:hover { background: var(--brand-red-dark); }
    .btn-secondary { background: var(--bg-card); color: var(--text-1); border-color: var(--border); }
    .btn-secondary:hover { border-color: var(--text-3); }

    .toolbar { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 14px 16px; display: flex; align-items: flex-end; gap: 12px; flex-wrap: wrap; margin-bottom: 18px; }
    .toolbar .fld { display: flex; flex-direction: column; gap: 5px; }
    .toolbar label { font-size: 11.5px; font-weight: 500; color: var(--text-2); }
    .toolbar select { height: 36px; min-width: 190px; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 0 10px; font-family: inherit; font-size: 13px; color: var(--text-1); background: var(--bg-card); outline: none; }
    .toolbar select:focus { border-color: var(--brand-red); box-shadow: 0 0 0 3px var(--brand-red-soft); }

    .card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 8px 16px 16px; }

    .link-name { color: var(--brand-red-dark); font-weight: 500; text-decoration: none; }
    .link-name:hover { text-decoration: underline; }
    .app-photo { width: 44px; height: 44px; border-radius: 8px; object-fit: cover; border: 1px solid var(--border); background: var(--bg-neutral); }
    .text-muted { color: var(--text-3); }

    /* Status select colours */
    .status-select { border: 1px solid var(--border); border-radius: var(--radius-pill); padding: 4px 26px 4px 10px; font-size: 12px; font-weight: 600; font-family: inherit; cursor: pointer; outline: none; -webkit-appearance: none; appearance: none;
        background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='10' height='10' viewBox='0 0 24 24' fill='none' stroke='%23475569' stroke-width='3'><polyline points='6 9 12 15 18 9'/></svg>"); background-repeat: no-repeat; background-position: right 8px center; }
    .status-select.st-new { background-color:#EFF6FF; color:#1D4ED8; border-color:#BFDBFE; }
    .status-select.st-contacted { background-color:#F0FDFA; color:#0F766E; border-color:#99F6E4; }
    .status-select.st-appointed, .status-select.st-short-listed { background-color: var(--success-soft); color: var(--success-text); border-color: var(--success-border); }
    .status-select.st-rejected, .status-select.st-not-fit-for-this-job, .status-select.st-not-interested, .status-select.st-not-joined { background-color: var(--danger-soft); color: var(--danger-text); border-color: var(--danger-border); }
    .status-select.st-no-vacancies-now { background-color: var(--warning-soft); color: var(--warning-text); border-color: var(--warning-border); }

    /* Row action menu (shared fixed popup) */
    .menu-btn { border: none; background: transparent; cursor: pointer; font-size: 18px; line-height: 1; color: var(--text-2); padding: 4px 8px; border-radius: var(--radius-sm); }
    .menu-btn:hover { background: var(--bg-neutral); color: var(--text-1); }
    #rowMenu { position: fixed; z-index: 1000; display: none; min-width: 168px; background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-md); box-shadow: 0 12px 32px rgba(0,0,0,.14); padding: 5px; }
    #rowMenu a { display: flex; align-items: center; gap: 9px; padding: 9px 11px; font-size: 13px; color: var(--text-2); text-decoration: none; border-radius: var(--radius-sm); cursor: pointer; }
    #rowMenu a:hover { background: var(--bg-neutral); color: var(--text-1); }
    #rowMenu a.danger { color: var(--brand-red); }
    #rowMenu a.danger:hover { background: var(--brand-red-soft); }
  
    /* Toast */
    #toast { position: fixed; right: 20px; bottom: 20px; z-index: 1100; display: none; padding: 12px 16px; border-radius: var(--radius-md); font-size: 13px; color: #fff; box-shadow: 0 12px 32px rgba(0,0,0,.2); }
    #toast.ok { background: var(--success); } #toast.err { background: var(--danger); }

    /* Reason modal */
    .btn-danger { background: var(--danger); color: #fff; }
    .btn-danger:hover { background: #7f1d1d; }
    .modal-overlay { position: fixed; inset: 0; background: rgba(15,23,42,.5); z-index: 1200; display: none; align-items: center; justify-content: center; padding: 20px; }
    .modal-overlay.open { display: flex; }
    .modal-box { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); width: 100%; max-width: 460px; box-shadow: 0 24px 60px rgba(0,0,0,.25); overflow: hidden; animation: mpop .2s ease; }
    @keyframes mpop { from { transform: translateY(8px); opacity: 0; } to { transform: none; opacity: 1; } }
    .modal-head { display: flex; align-items: center; justify-content: space-between; padding: 16px 18px; border-bottom: 1px solid var(--border-soft); }
    .modal-head h3 { font-size: 15px; font-weight: 600; color: var(--text-1); }
    .modal-close { border: none; background: transparent; font-size: 22px; line-height: 1; color: var(--text-3); cursor: pointer; }
    .modal-close:hover { color: var(--text-1); }
    .modal-body { padding: 18px; }
    .modal-status-line { font-size: 12.5px; color: var(--text-2); margin-bottom: 14px; }
    .modal-status-line b { color: var(--brand-red-dark); font-weight: 600; }
    .modal-label { display: block; font-size: 12.5px; font-weight: 500; color: var(--text-2); margin-bottom: 6px; }
    .modal-textarea { width: 100%; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 10px 12px; font-family: inherit; font-size: 13.5px; color: var(--text-1); background: var(--bg-card); outline: none; resize: vertical; min-height: 92px; }
    .modal-textarea:focus { border-color: var(--brand-red); box-shadow: 0 0 0 3px var(--brand-red-soft); }
    .modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 14px 18px; border-top: 1px solid var(--border-soft); background: var(--bg-page); }
</style>
@endpush

<div class="hr-wrap">
    <div class="hr-head">
        <div class="hr-eyebrow">HR Management</div>
        <h1>Applications</h1>
        <p>View and manage submitted job applications.</p>
    </div>

    <div class="toolbar">
        <div class="fld">
            <label>Filter by Category</label>
            <select id="fltCategory">
                <option value="">All categories</option>
                @foreach($categories as $c)
                    <option value="{{ $c->id }}">{{ $c->category_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="fld">
            <label>Filter by Status</label>
            <select id="fltStatus">
                <option value="">All statuses</option>
                @foreach($statuses as $s)
                    <option value="{{ $s }}">{{ $s }}</option>
                @endforeach
            </select>
        </div>
        <button type="button" class="btn btn-primary" id="applyFilter">Filter</button>
        <button type="button" class="btn btn-secondary" id="clearFilter">Clear</button>
    </div>

    <div class="card">
        <div style="overflow-x:auto;">
            <table id="appTable" class="table dataTable" style="width:100%">
                <thead>
                    <tr>
                        <th>SlNo</th><th>Dated</th><th>Name</th><th>Photo</th><th>Mobile</th>
                        <th>Email</th><th>Job Category</th><th>Salary</th><th>CV_File</th>
                        <th>Status</th><th>Reason</th><th>Action</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- Shared row-action popup + toast --}}
<div id="rowMenu">
    <a id="rmView">👁 View Details</a>
    <a id="rmDelete" class="danger">🗑 Delete</a>
</div>
<div id="toast"></div>

{{-- Rejection reason modal --}}
<div id="reasonModal" class="modal-overlay">
    <div class="modal-box">
        <div class="modal-head">
            <h3>Update Status</h3>
            <button type="button" class="modal-close" id="reasonCancelX" aria-label="Close">&times;</button>
        </div>
        <div class="modal-body">
            <p class="modal-status-line">New status: <b id="reasonStatusName"></b></p>
            <label class="modal-label">Reason <span style="color:var(--text-3);font-weight:400;">(optional)</span></label>
            <textarea id="reasonText" class="modal-textarea" rows="4" placeholder="Enter a reason for this status change…"></textarea>
        </div>
        <div class="modal-foot">
            <button type="button" class="btn btn-secondary" id="reasonCancel">Cancel</button>
            <button type="button" class="btn btn-primary" id="reasonConfirm">Update Status</button>
        </div>
    </div>
</div>

<script src="{{asset('assets/js/jquery-3.7.1.js')}}"></script>
<script src="{{asset('assets/datatables.net/js/jquery.dataTables.min.js')}}"></script>

<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const showUrl = "{{ url('hr/applications') }}";

    function toast(msg, ok) {
        const t = document.getElementById('toast');
        t.textContent = msg; t.className = ok ? 'ok' : 'err'; t.style.display = 'block';
        setTimeout(() => { t.style.display = 'none'; }, 2600);
    }

    const table = $('#appTable').DataTable({

        processing: true,
        serverSide: true,
		stateSave:true,
		paging     : true,
        pageLength :50,
		scrollX: true,
		
		'pagingType':"simple_numbers",
        'lengthChange': true,

        language: {
            search: '',
            searchPlaceholder: 'Search ...',
        },

        ajax: {
            url: "{{ route('hr.applications.data') }}",
            data: function (d) {
                d.job_category_id = $('#fltCategory').val();
                d.status = $('#fltStatus').val();
            }
        },

        columnDefs:[
            {width:"100px",targets:1},
        ],
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'dated', name: 'created_at',orderable: true, searchable: true },
            { data: 'name', name: 'name',orderable: true, searchable: true },
            { data: 'photo', name: 'photo', orderable: false, searchable: false },
            { data: 'mobile', name: 'mobile', orderable: false, searchable: true},
            { data: 'email', name: 'email', orderable: false, searchable: true},
            { data: 'job_cat', name: 'hr_job_category.category_name' },
            { data: 'salary', name: 'expected_salary', searchable: false },
            { data: 'cv_file', name: 'cv_file', orderable: false, searchable: false },
            { data: 'status', name: 'status', orderable: false },
            { data: 'reason', name: 'rejection_reason' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
    });

    $('#applyFilter').on('click', () => table.ajax.reload());
    $('#clearFilter').on('click', () => { $('#fltCategory').val(''); $('#fltStatus').val(''); table.ajax.reload(); });
    
    
    // Shared status updater.
    function sendStatus(id, status, reason) {
        $.ajax({
            url: showUrl + '/' + id + '/status',
            type: 'POST',
            dataType: 'json',
            data: { _token: CSRF, status: status, rejection_reason: reason },
            success: (res) => { toast(res.msg, true); table.ajax.reload(null, false); },
            error: () => toast('Could not update status.', false),
        });
    }

    // Remember each select's value before it changes (so we can revert on cancel).
    $('#appTable tbody').on('focus', '.status-select', function () { this.dataset.prev = this.value; });

    // Reason modal.
    const reasonModal = document.getElementById('reasonModal');
    const reasonText  = document.getElementById('reasonText');
    const reasonStatusName = document.getElementById('reasonStatusName');
    let pendingUpdate = null; // { id, select, prev, status }

    function closeReasonModal(revert) {
        if (revert && pendingUpdate) pendingUpdate.select.value = pendingUpdate.prev;
        reasonModal.classList.remove('open');
        pendingUpdate = null;
    }

    // Inline status change → "New" applies immediately; every other status asks for a reason.
    $('#appTable tbody').on('change', '.status-select', function () {
        const id = $(this).data('id');
        const status = $(this).val();
        if (status === 'New') {
            sendStatus(id, status, null);
        } else {
            pendingUpdate = { id: id, select: this, prev: this.dataset.prev || '', status: status };
            reasonText.value = '';
            reasonStatusName.textContent = status;
            reasonModal.classList.add('open');
            setTimeout(() => reasonText.focus(), 60);
        }
    });

    document.getElementById('reasonConfirm').addEventListener('click', function () {
        if (!pendingUpdate) return;
        sendStatus(pendingUpdate.id, pendingUpdate.status, reasonText.value.trim());
        reasonModal.classList.remove('open');
        pendingUpdate = null;
    });
    document.getElementById('reasonCancel').addEventListener('click', () => closeReasonModal(true));
    document.getElementById('reasonCancelX').addEventListener('click', () => closeReasonModal(true));
    reasonModal.addEventListener('click', (e) => { if (e.target === reasonModal) closeReasonModal(true); });
    document.addEventListener('keydown', (e) => { if (e.key === 'Escape' && reasonModal.classList.contains('open')) closeReasonModal(true); });

    // Row action popup (fixed position → never clipped).
    const menu = document.getElementById('rowMenu');
    let menuId = null;
    $('#appTable tbody').on('click', '.menu-btn', function (e) {
        e.stopPropagation();
        menuId = $(this).data('id');
        const r = this.getBoundingClientRect();
        menu.style.display = 'block';
        let left = r.right - menu.offsetWidth;
        if (left < 8) left = 8;
        menu.style.left = left + 'px';
        menu.style.top = (r.bottom + 4) + 'px';
    });
    document.addEventListener('click', () => { menu.style.display = 'none'; });
    document.getElementById('rmView').addEventListener('click', () => { if (menuId) window.location.href = showUrl + '/' + menuId; });
    document.getElementById('rmDelete').addEventListener('click', function () {
        if (!menuId) return;
        menu.style.display = 'none';
        if (!confirm('Are you sure you want to delete this application and all its data?')) return;
        $.ajax({
            url: showUrl + '/' + menuId,
            type: 'POST',
            dataType: 'json',
            data: { _token: CSRF, _method: 'DELETE' },
            success: (res) => { toast(res.msg, res.status); table.ajax.reload(null, false); },
            error: () => toast('Could not delete.', false),
        });
    });
})();
</script>
</x-layouts.app>
