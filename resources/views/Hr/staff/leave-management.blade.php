{{-- HR › Staff Section › Leave Management (server-rendered + DataTable list) --}}
@php
    $openApply = $errors->any() || session('leave_error');
@endphp
<link rel="stylesheet" href="{{ asset('assets/datatables.net/css/jquery.dataTables.css') }}">
<style>
    .lv-head { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:18px; }
    .lv-head h2 { font-size:18px; font-weight:700; color:var(--text-1); }
    .lv-btn { display:inline-flex; align-items:center; gap:7px; padding:9px 15px; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:600; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .lv-btn svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2; }
    .lv-btn.primary { background:var(--brand-red); color:#fff; } .lv-btn.primary:hover { background:var(--brand-red-dark); }
    .lv-btn.secondary { background:var(--bg-card); color:var(--text-1); border-color:var(--border); } .lv-btn.secondary:hover { border-color:var(--text-3); }
    .lv-btn.danger { background:var(--danger); color:#fff; } .lv-btn.danger:hover { background:#7f1d1d; }
    .lv-btn.link-danger { background:transparent; color:var(--brand-red); border:none; padding:4px 8px; font-weight:500; cursor:pointer; font-family:inherit; }
    .lv-btn.link-danger:hover { text-decoration:underline; }
    .lv-btn:disabled { opacity:.55; cursor:not-allowed; }

    .lv-flash { padding:10px 14px; border-radius:var(--radius-sm); font-size:13px; margin-bottom:16px; }
    .lv-flash.ok { background:var(--success-soft); color:var(--success-text); border:1px solid var(--success-border); }
    .lv-flash.err { background:var(--danger-soft); color:var(--danger-text); border:1px solid var(--danger-border); }

    .lv-bal { display:grid; grid-template-columns:repeat(auto-fit,minmax(200px,1fr)); gap:16px; margin-bottom:20px; }
    .lv-bcard { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:18px; }
    .lv-bcard .t { font-size:13px; font-weight:500; color:var(--text-2); margin-bottom:10px; }
    .lv-bcard .v { font-size:24px; font-weight:700; color:var(--text-1); letter-spacing:-.02em; }
    .lv-bcard .v small { font-size:14px; font-weight:600; color:var(--text-3); }
    .lv-bar { height:7px; background:var(--bg-neutral); border-radius:var(--radius-pill); overflow:hidden; margin-top:12px; }
    .lv-bar > span { display:block; height:100%; border-radius:var(--radius-pill); background:var(--brand-red); }
    .lv-bcard .rem { font-size:11.5px; color:var(--text-3); margin-top:8px; }
    .lv-bcard.total { background:#0f172a; }
    .lv-bcard.total .t, .lv-bcard.total .rem { color:rgba(255,255,255,.65); }
    .lv-bcard.total .v { color:#fff; } .lv-bcard.total .v small { color:rgba(255,255,255,.6); }
    .lv-bcard.total .lv-bar { background:rgba(255,255,255,.15); } .lv-bcard.total .lv-bar > span { background:#fff; }

    .lv-card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:16px 18px 18px; }
    .lv-card h3 { font-size:15px; font-weight:600; color:var(--text-1); margin-bottom:12px; }

    /* Sub-tabs (All / Pending / Approved / Rejected) */
    .lv-subtabs { display:flex; gap:6px; border-bottom:1px solid var(--border-soft); margin-bottom:2px; }
    .lv-subtab { background:none; border:none; font-family:inherit; font-size:13px; font-weight:500; color:var(--text-3); padding:8px 10px; cursor:pointer; border-bottom:2px solid transparent; margin-bottom:-1px; }
    .lv-subtab:hover { color:var(--text-1); }
    .lv-subtab.active { color:var(--text-1); font-weight:600; border-bottom-color:var(--text-1); }
    .lv-subpane { display:none; }
    .lv-subpane.active { display:block; }

    /* Normal tables (Pending / Approved / Rejected) */
    table.lv-ntable { width:100%; border-collapse:collapse; }
    table.lv-ntable thead th { text-align:left; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--text-3); padding:11px 14px; border-bottom:1px solid var(--border); }
    table.lv-ntable tbody td { font-size:13px; color:var(--text-2); padding:11px 14px; border-bottom:1px solid var(--border-soft); vertical-align:middle; }
    table.lv-ntable tbody tr:last-child td { border-bottom:none; }
    .lv-nempty { text-align:center; color:var(--text-3); padding:32px; font-size:13.5px; }

    .lv-badge { display:inline-block; padding:2px 10px; border-radius:var(--radius-pill); font-size:11.5px; font-weight:600; }
    .lv-badge.pending { background:var(--warning-soft); color:var(--warning-text); }
    .lv-badge.approved { background:#DCFCE7; color:#15803D; }
    .lv-badge.rejected { background:var(--danger-soft); color:var(--danger-text); }

    .lv-modal { position:fixed; inset:0; background:rgba(15,23,42,.5); z-index:1200; display:none; align-items:center; justify-content:center; padding:20px; }
    .lv-modal.open { display:flex; }
    .lv-box { background:var(--bg-card); border-radius:var(--radius-lg); width:100%; max-width:460px; box-shadow:0 24px 60px rgba(0,0,0,.25); overflow:hidden; }
    .lv-mhead { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--border-soft); }
    .lv-mhead h3 { font-size:15px; font-weight:600; color:var(--text-1); }
    .lv-mhead p { font-size:12px; color:var(--text-3); margin-top:2px; }
    .lv-mclose { border:none; background:none; font-size:20px; color:var(--text-3); cursor:pointer; }
    .lv-mbody { padding:18px 20px; }
    .lv-fld { margin-bottom:14px; }
    .lv-fld label { display:block; font-size:12.5px; font-weight:500; color:var(--text-2); margin-bottom:6px; }
    .lv-fld input, .lv-fld select, .lv-fld textarea { width:100%; border:1px solid var(--border); border-radius:var(--radius-sm); padding:9px 11px; font-family:inherit; font-size:13px; color:var(--text-1); background:var(--bg-card); outline:none; }
    .lv-fld textarea { min-height:72px; resize:vertical; }
    .lv-fld input:focus, .lv-fld select:focus, .lv-fld textarea:focus { border-color:var(--brand-red); box-shadow:0 0 0 3px var(--brand-red-soft); }
    .lv-row2 { display:grid; grid-template-columns:1fr 1fr; gap:12px; }
    .lv-days { font-size:12px; color:var(--text-2); background:var(--bg-page); border:1px solid var(--border-soft); border-radius:var(--radius-sm); padding:8px 11px; margin-bottom:14px; }
    .lv-days b { color:var(--brand-red-dark); }
    .lv-ferr { font-size:11.5px; color:var(--danger); margin-top:5px; }
    .lv-mfoot { display:flex; justify-content:flex-end; gap:8px; padding:14px 20px; border-top:1px solid var(--border-soft); background:var(--bg-page); }
</style>

<div id="lvRoot">
    <div class="lv-head">
        <h2>Leave Management</h2>
        <button type="button" class="lv-btn primary" id="lvApplyBtn" @unless($employeeLinked) disabled @endunless>
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Apply for Leave
        </button>
    </div>

    @if(session('leave_success'))
        <div class="lv-flash ok">{{ session('leave_success') }}</div>
    @endif
    @if(session('leave_error'))
        <div class="lv-flash err">{{ session('leave_error') }}</div>
    @endif
    @unless($employeeLinked)
        <div class="lv-flash err">Your account is not linked to an employee profile, so leave requests are unavailable.</div>
    @endunless

    {{-- Balance cards (server-rendered) --}}
    <div class="lv-bal">
        @foreach($leave['balance'] as $b)
            <div class="lv-bcard">
                <div class="t">{{ $b['leave_type'] }}</div>
                <div class="v">{{ $b['taken'] }} <small>/ {{ $b['allowed'] }}</small></div>
                <div class="lv-bar"><span style="width: {{ $b['allowed'] > 0 ? min(100, $b['taken'] / $b['allowed'] * 100) : 0 }}%"></span></div>
                <div class="rem">{{ $b['remaining'] }} days remaining</div>
            </div>
        @endforeach
        <div class="lv-bcard ">
            <div class="t">Total</div>
            <div class="v">{{ $leave['total']['taken'] }} <small>/ {{ $leave['total']['allowed'] }}</small></div>
            <div class="lv-bar"><span style="width: {{ $leave['total']['allowed'] > 0 ? min(100, $leave['total']['taken'] / $leave['total']['allowed'] * 100) : 0 }}%"></span></div>
            <div class="rem">{{ $leave['total']['remaining'] }} days remaining</div>
        </div>
    </div>

    {{-- Requests list — All (DataTable) + Pending/Approved/Rejected (normal tables) --}}
    <div class="lv-card">
        <h3>Leave Requests</h3>

        <div class="lv-subtabs" id="lvSubtabs">
            <button type="button" class="lv-subtab active" data-sub="all">All</button>
            <button type="button" class="lv-subtab" data-sub="pending">Pending</button>
            <button type="button" class="lv-subtab" data-sub="approved">Approved</button>
            <button type="button" class="lv-subtab" data-sub="rejected">Rejected</button>
        </div>

        {{-- All → DataTable --}}
        <div class="lv-subpane active" data-subpane="all">
            <div style="overflow-x:auto;">
                <table id="lvTable" class="table dataTable" style="width:100%">
                    <thead>
                        <tr><th>S.No</th><th>Leave Type</th><th>From Date</th><th>To Date</th><th>Days</th><th>Reason</th><th>Status</th><th>Action</th></tr>
                    </thead>
                </table>
            </div>
        </div>

        {{-- Pending / Approved / Rejected → rows fetched via AJAX on tab click --}}
        @foreach(['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected'] as $st => $stLabel)
            <div class="lv-subpane" data-subpane="{{ $st }}">
                <div style="overflow-x:auto;">
                    <table class="lv-ntable">
                        <thead>
                            <tr><th>Leave Type</th><th>From Date</th><th>To Date</th><th>Days</th><th>Reason</th><th>Status</th><th>Action</th></tr>
                        </thead>
                        <tbody data-rows="{{ $st }}"><tr><td colspan="7" class="lv-nempty">Loading…</td></tr></tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- Apply modal (real form POST) --}}
<div class="lv-modal {{ $openApply ? 'open' : '' }}" id="lvApplyModal">
    <div class="lv-box">
        <form method="POST" action="{{ route('hr.staff.leave.store') }}">
            @csrf
            <div class="lv-mhead"><div><h3>Apply for Leave</h3><p>Submit a new leave request</p></div><button type="button" class="lv-mclose" data-close>&times;</button></div>
            <div class="lv-mbody">
                <div class="lv-fld">
                    <label>Leave Type <span style="color:var(--brand-red)">*</span></label>
                    <select name="leave_type" required>
                        <option value="">Select leave type</option>
                        @foreach($leave['types'] as $type)
                            <option value="{{ $type }}" @selected(old('leave_type') === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                    @error('leave_type')<div class="lv-ferr">{{ $message }}</div>@enderror
                </div>
                <div class="lv-row2">
                    <div class="lv-fld"><label>From Date <span style="color:var(--brand-red)">*</span></label><input type="date" name="from_date" id="lvFrom" value="{{ old('from_date') }}" required>@error('from_date')<div class="lv-ferr">{{ $message }}</div>@enderror</div>
                    <div class="lv-fld"><label>To Date <span style="color:var(--brand-red)">*</span></label><input type="date" name="to_date" id="lvTo" value="{{ old('to_date') }}" required>@error('to_date')<div class="lv-ferr">{{ $message }}</div>@enderror</div>
                </div>
                <div class="lv-days" id="lvDays" style="display:none;">Duration: <b id="lvDaysN">0</b> day(s)</div>
                <div class="lv-fld"><label>Reason</label><textarea name="reason" placeholder="Reason for leave (optional)">{{ old('reason') }}</textarea>@error('reason')<div class="lv-ferr">{{ $message }}</div>@enderror</div>
            </div>
            <div class="lv-mfoot">
                <button type="button" class="lv-btn secondary" data-close>Cancel</button>
                <button type="submit" class="lv-btn primary">Submit Request</button>
            </div>
        </form>
    </div>
</div>

{{-- Cancel confirmation modal --}}
<div class="lv-modal" id="lvCancelModal">
    <div class="lv-box">
        <div class="lv-mhead"><h3>Cancel Leave Request</h3><button type="button" class="lv-mclose" data-close>&times;</button></div>
        <div class="lv-mbody"><p style="font-size:13.5px;color:var(--text-2);line-height:1.5;">Are you sure you want to cancel this <b id="lvCancelType" style="color:var(--text-1);"></b> request? This cannot be undone.</p></div>
        <div class="lv-mfoot"><button type="button" class="lv-btn secondary" data-close>Keep</button><button type="button" class="lv-btn danger" id="lvCancelConfirm">Cancel Request</button></div>
    </div>
</div>

<script src="{{ asset('assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('assets/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const destroyBase = "{{ url('hr/staff-section/leave') }}";
    let lvTable = null;

    function toast(msg, ok) {
        let t = document.getElementById('lvToast');
        if (!t) { t = document.createElement('div'); t.id = 'lvToast';
            t.style.cssText = 'position:fixed;right:20px;bottom:20px;z-index:1300;padding:12px 16px;border-radius:10px;font-size:13px;color:#fff;box-shadow:0 12px 32px rgba(0,0,0,.2)'; document.body.appendChild(t); }
        t.textContent = msg; t.style.background = ok ? '#16A34A' : '#DC2626'; t.style.display = 'block';
        clearTimeout(t._h); t._h = setTimeout(() => t.style.display = 'none', 2600);
    }

    // Lazy DataTable init (measures wrong while the pane is hidden).
    window.initStaffLeave = function () {
        if (lvTable) { lvTable.columns.adjust(); return; }
        lvTable = $('#lvTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            lengthChange: true,
            pagingType: 'simple_numbers',
            order: [[2, 'desc']],
            language: { search: '', searchPlaceholder: 'Search requests...' },
            ajax: { url: "{{ route('hr.staff.leave.data') }}" },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '60px' },
                { data: 'leave_type', name: 'leave_type' },
                { data: 'from_date', name: 'from_date' },
                { data: 'to_date', name: 'to_date' },
                { data: 'days', name: 'days', width: '70px' },
                { data: 'reason', name: 'reason' },
                { data: 'status_badge', name: 'status', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, width: '90px' },
            ],
        });
    };

    // Apply modal open/close.
    const applyModal = document.getElementById('lvApplyModal');
    document.getElementById('lvApplyBtn').addEventListener('click', () => applyModal.classList.add('open'));
    applyModal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => applyModal.classList.remove('open')));
    applyModal.addEventListener('click', e => { if (e.target === applyModal) applyModal.classList.remove('open'); });

    // Live duration calc.
    const lvFrom = document.getElementById('lvFrom'), lvTo = document.getElementById('lvTo');
    function updateDays() {
        const f = lvFrom.value, t = lvTo.value;
        if (f && t && t >= f) {
            document.getElementById('lvDaysN').textContent = Math.round((new Date(t) - new Date(f)) / 86400000) + 1;
            document.getElementById('lvDays').style.display = 'block';
        } else { document.getElementById('lvDays').style.display = 'none'; }
    }
    lvFrom.addEventListener('change', updateDays);
    lvTo.addEventListener('change', updateDays);
    updateDays();

    // Fetch the row HTML for a status and inject it into that pane's tbody.
    const rowsUrl = "{{ route('hr.staff.leave.rows') }}";
    function loadRows(status) {
        const tbody = document.querySelector('tbody[data-rows="' + status + '"]');
        if (!tbody) return;
        tbody.innerHTML = '<tr><td colspan="7" class="lv-nempty">Loading…</td></tr>';
        $.getJSON(rowsUrl, { status: status })
            .done(res => { tbody.innerHTML = res.html; })
            .fail(() => { tbody.innerHTML = '<tr><td colspan="7" class="lv-nempty">Could not load.</td></tr>'; });
    }

    // Sub-tab switching (All / Pending / Approved / Rejected).
    document.querySelectorAll('.lv-subtab').forEach(btn => btn.addEventListener('click', function () {
        const key = this.dataset.sub;
        document.querySelectorAll('.lv-subtab').forEach(b => b.classList.toggle('active', b === this));
        document.querySelectorAll('.lv-subpane').forEach(p => p.classList.toggle('active', p.dataset.subpane === key));
        if (key === 'all') { if (lvTable) lvTable.columns.adjust(); }
        else { loadRows(key); }   // click each tab → AJAX get + inject rows
    }));

    // Cancel (AJAX) with confirmation modal — works for both the DataTable and the normal tables.
    const cancelModal = document.getElementById('lvCancelModal');
    let cancelId = null;
    cancelModal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => cancelModal.classList.remove('open')));
    cancelModal.addEventListener('click', e => { if (e.target === cancelModal) cancelModal.classList.remove('open'); });

    document.getElementById('lvRoot').addEventListener('click', function (e) {
        const btn = e.target.closest('.lv-cancel');
        if (!btn) return;
        cancelId = btn.dataset.id;
        document.getElementById('lvCancelType').textContent = btn.dataset.type;
        cancelModal.classList.add('open');
    });

    document.getElementById('lvCancelConfirm').addEventListener('click', function () {
        if (!cancelId) return;
        this.disabled = true;
        $.ajax({
            url: destroyBase + '/' + cancelId, type: 'POST', dataType: 'json',
            data: { _token: CSRF, _method: 'DELETE' },
            success: (res) => {
                this.disabled = false; cancelModal.classList.remove('open'); toast(res.msg, res.status);
                if (!res.status) return;
                if (lvTable) lvTable.ajax.reload(null, false);   // refresh the All DataTable
                loadRows('pending');                             // refresh the Pending tab (only pending is cancellable)
            },
            error: (xhr) => { this.disabled = false; cancelModal.classList.remove('open'); toast(xhr.responseJSON?.msg || 'Could not cancel.', false); },
        });
    });

    // Init now if the pane is already active (deep-link / redirect back to leave tab).
    if (document.getElementById('lvRoot').closest('.set-pane')?.classList.contains('active')) window.initStaffLeave();
})();
</script>
