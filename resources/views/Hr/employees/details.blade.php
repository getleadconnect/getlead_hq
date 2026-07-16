<x-layouts.app title="Employee Details">
@push('styles')
<style>
    .hr-wrap { padding: 24px 28px 48px; width: 100%; }

    .btn { display:inline-flex; align-items:center; gap:7px; padding:9px 15px; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:500; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
    .btn-dark { background:var(--text-1); color:#fff; } .btn-dark:hover { background:#000; }
    .btn-secondary { background:var(--bg-card); color:var(--text-1); border-color:var(--border); } .btn-secondary:hover { border-color:var(--text-3); }

    /* Header card */
    .emp-header { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:22px 24px; display:flex; align-items:center; gap:18px; flex-wrap:wrap; margin-bottom:18px; }
    .emp-header .avatar { width:74px; height:74px; border-radius:50%; object-fit:cover; flex-shrink:0; }
    .emp-header .avatar-fb { width:74px; height:74px; border-radius:50%; background:var(--brand-red-soft); color:var(--brand-red-dark); border:1px solid var(--brand-red-border); display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:600; flex-shrink:0; }
    .emp-header .id-block { flex:1; min-width:220px; }
    .emp-header h1 { font-size:22px; font-weight:700; letter-spacing:-.4px; color:var(--text-1); }
    .emp-header .sub { font-size:13.5px; color:var(--text-2); margin-top:2px; }
    .emp-header .meta { display:flex; gap:18px; flex-wrap:wrap; margin-top:10px; font-size:12.5px; color:var(--text-2); }
    .emp-header .meta .m { display:flex; align-items:center; gap:6px; }
    .emp-header .meta svg { width:14px; height:14px; stroke:var(--text-3); fill:none; stroke-width:2; }
    .emp-header .actions { display:flex; gap:8px; flex-shrink:0; }

    /* Stat cards */
    .stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:22px; }
    @media(max-width:980px){ .stat-grid{ grid-template-columns:repeat(2,1fr);} }
    @media(max-width:520px){ .stat-grid{ grid-template-columns:1fr;} }
    .stat-card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:18px; display:flex; align-items:center; gap:14px; }
    .stat-ico { width:44px; height:44px; border-radius:11px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .stat-ico svg { width:22px; height:22px; stroke:currentColor; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
    .ico-green { background:#F0FDF4; color:#15803D; } .ico-blue { background:#EFF6FF; color:#1D4ED8; }
    .ico-amber { background:#FFFBEB; color:#B45309; } .ico-purple { background:#F5F3FF; color:#6D28D9; }
    .stat-card .v { font-size:22px; font-weight:700; color:var(--text-1); letter-spacing:-.02em; }
    .stat-card .l { font-size:12px; color:var(--text-2); margin-top:1px; }

    /* Tabs */
    .tabs { display:flex; gap:2px; border-bottom:1px solid var(--border); margin-bottom:20px; flex-wrap:wrap; }
    .tab { padding:10px 14px; font-size:13.5px; font-weight:500; color:var(--text-2); cursor:pointer; background:none; border:none; border-bottom:2px solid transparent; font-family:inherit; margin-bottom:-1px; }
    .tab:hover { color:var(--text-1); }
    .tab.active { color:var(--text-1); border-bottom-color:var(--brand-red); }
    .tab-pane { display:none; }
    .tab-pane.active { display:block; }

    /* Info cards */
    .cols { display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start; }
    @media(max-width:820px){ .cols{ grid-template-columns:1fr; } }
    .info-card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:22px; }
    .info-card .head { display:flex; align-items:center; justify-content:space-between; margin-bottom:18px; }
    .info-card .head h2 { font-size:16px; font-weight:600; color:var(--text-1); }
    .info-card .edit-ico { color:var(--text-3); cursor:pointer; background:none; border:none; padding:0; line-height:0; }
    .info-card .edit-ico svg { width:16px; height:16px; stroke:currentColor; fill:none; stroke-width:2; }
    .info-card .edit-ico:hover { color:var(--brand-red); }

    /* Edit modals */
    .modal-ov { position:fixed; inset:0; background:rgba(15,23,42,.5); z-index:1200; display:none; align-items:center; justify-content:center; padding:20px; }
    .modal-ov.open { display:flex; }
    .modal-bx { background:var(--bg-card); border-radius:var(--radius-lg); width:100%; max-width:420px; box-shadow:0 24px 60px rgba(0,0,0,.25); overflow:hidden; animation:mpop .18s ease; }
    @keyframes mpop { from{transform:translateY(8px);opacity:0;} to{transform:none;opacity:1;} }
    .mhead { padding:20px 22px 2px; position:relative; }
    .mhead h3 { font-size:17px; font-weight:700; color:var(--text-1); }
    .mhead p { font-size:12.5px; color:var(--text-2); margin-top:4px; line-height:1.5; max-width:88%; }
    .mclose { position:absolute; top:16px; right:18px; border:none; background:none; font-size:20px; color:var(--text-3); cursor:pointer; line-height:1; }
    .mclose:hover { color:var(--text-1); }
    .mbody { padding:14px 22px; }
    .mfield { margin-bottom:14px; }
    .mfield:last-child { margin-bottom:0; }
    .mfield label { display:block; font-size:13px; font-weight:600; color:var(--text-1); margin-bottom:6px; }
    .mfield input { width:100%; border:1px solid var(--border); border-radius:var(--radius-sm); padding:10px 12px; font-family:inherit; font-size:13.5px; color:var(--text-1); background:var(--bg-card); outline:none; }
    .mfield input:focus { border-color:var(--brand-red); box-shadow:0 0 0 3px var(--brand-red-soft); }
    .mfield input::placeholder { color:var(--text-3); }
    .mfoot { display:flex; justify-content:flex-end; gap:8px; padding:8px 22px 20px; }
    .mfield input[type=file] { padding:8px 10px; font-size:12.5px; }
    .mhint { font-size:11.5px; color:var(--text-3); margin-top:6px; }
    .mhint.err { color:var(--danger); }
    .btn-dark { background:var(--text-1); color:#fff; border:1px solid var(--text-1); } .btn-dark:hover { background:#000; }

    /* Documents tab */
    .doc2-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(220px,1fr)); gap:16px; margin-top:4px; }
    .doc2 { border:1px dashed var(--border); border-radius:var(--radius-md); padding:18px; }
    .doc2-ico { color:var(--brand-red); margin-bottom:10px; }
    .doc2-ico svg { width:34px; height:34px; stroke:currentColor; fill:none; stroke-width:1.6; }
    .doc2-name { font-size:13.5px; font-weight:600; color:var(--text-1); word-break:break-word; margin-bottom:6px; }
    .doc2-meta { font-size:11.5px; color:var(--text-3); }
    .doc2-actions { display:flex; gap:14px; margin-top:12px; }
    .doc2-actions a { font-size:12.5px; text-decoration:none; font-weight:500; color:#4b4bdc; }
    .doc2-actions a:hover { text-decoration:underline; }
    .doc2-actions a.del { color:var(--brand-red); }
    .hidden { display:none !important; }
    .doc2-empty { display:flex; flex-direction:column; align-items:center; justify-content:center; text-align:center; padding:52px 16px; }
    .doc2-empty svg { width:46px; height:46px; stroke:var(--text-3); fill:none; stroke-width:1.4; margin-bottom:16px; }
    .doc2-empty .t { font-size:14px; color:var(--text-2); font-weight:500; }
    .doc2-empty .s { font-size:12.5px; color:var(--text-3); margin-top:5px; }
    .kv-grid { display:grid; grid-template-columns:1fr 1fr; gap:18px 20px; }
    .kv .k { font-size:10.5px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--text-3); margin-bottom:3px; }
    .kv .v { font-size:13.5px; color:var(--text-1); font-weight:500; word-break:break-word; }
    .kv.full { grid-column:1/-1; }
    .badge { display:inline-block; padding:3px 12px; border-radius:var(--radius-pill); font-size:11px; font-weight:700; letter-spacing:.03em; }
    .b-active { background:var(--success-soft); color:var(--success-text); border:1px solid var(--success-border); }
    .b-inactive { background:var(--bg-neutral); color:var(--text-2); border:1px solid var(--border); }

    /* Recent activity */
    .activity-card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:22px; margin-top:20px; }
    .activity-card h2 { font-size:16px; font-weight:600; color:var(--text-1); margin-bottom:16px; }
    .act-row { display:flex; align-items:center; gap:12px; padding:10px 0; }
    .act-ico { width:32px; height:32px; border-radius:50%; background:#F0FDF4; color:#15803D; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .act-ico svg { width:16px; height:16px; stroke:currentColor; fill:none; stroke-width:2.4; }
    .act-row .t { font-size:13.5px; font-weight:500; color:var(--text-1); }
    .act-row .d { font-size:12px; color:var(--text-3); margin-top:1px; }
    .act-row .when { margin-left:auto; font-size:12px; color:var(--text-3); }

    /* Documents */
    .doc-grid { display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:14px; }
    .doc-card { display:flex; align-items:center; gap:12px; background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-md); padding:14px 16px; }
    .doc-card .di { width:38px; height:38px; border-radius:9px; background:var(--brand-red-soft); color:var(--brand-red); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .doc-card .di svg { width:18px; height:18px; stroke:currentColor; fill:none; stroke-width:2; }
    .doc-card .dn { font-size:13px; font-weight:500; color:var(--text-1); }
    .doc-card a { margin-left:auto; font-size:12.5px; color:var(--brand-red-dark); text-decoration:none; font-weight:500; }
    .doc-card a:hover { text-decoration:underline; }
    .empty-pane { background:var(--bg-card); border:1px dashed var(--border); border-radius:var(--radius-lg); padding:44px; text-align:center; color:var(--text-3); font-size:13.5px; }


    .hr-head { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom: 18px; }
    .hr-eyebrow { font-size: 11px; font-weight: 600; letter-spacing: .09em; text-transform: uppercase; color: var(--brand-red); }
    .hr-head h1 { font-size: 24px; font-weight: 600; letter-spacing: -.5px; color: var(--text-1); margin-top: 4px; }
    .hr-head p { font-size: 13px; color: var(--text-2); margin-top: 4px; }

    .btn { display:inline-flex; align-items:center; gap:6px; padding:8px 14px; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:500; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn-primary { background: var(--brand-red); color:#fff; } .btn-primary:hover { background: var(--brand-red-dark); }
    .btn-secondary { background: var(--bg-card); color: var(--text-1); border-color: var(--border); }
    .btn-secondary:hover { border-color: var(--text-3); }

    /* Attendance tab */
    .att-head { display:flex; align-items:center; justify-content:space-between; gap:12px; flex-wrap:wrap; margin-bottom:18px; }
    .att-head h2 { font-size:16px; font-weight:600; color:var(--text-1); }
    .att-controls { display:flex; gap:8px; align-items:center; }
    .att-select { height:36px; border:1px solid var(--border); border-radius:var(--radius-sm); padding:0 11px; font-family:inherit; font-size:13px; color:var(--text-1); background:var(--bg-card); outline:none; }
    .att-select:focus { border-color:var(--brand-red); box-shadow:0 0 0 3px var(--brand-red-soft); }
    .att-summary { display:flex; gap:36px; flex-wrap:wrap; padding-bottom:16px; margin-bottom:6px; border-bottom:1px solid var(--border-soft); }
    .asum .k { font-size:10.5px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--text-3); margin-bottom:3px; }
    .asum .v { font-size:20px; font-weight:700; color:var(--text-1); }
    .att-table { width:100%; border-collapse:collapse; }
    .att-table thead th { text-align:left; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--text-3); padding:12px 10px; border-bottom:1px solid var(--border); }
    .att-table tbody td { font-size:13px; color:var(--text-2); padding:11px 10px; border-bottom:1px solid var(--border-soft); }
    .att-table tbody tr:last-child td { border-bottom:none; }
    .att-pill { display:inline-block; padding:2px 10px; border-radius:var(--radius-pill); font-size:11px; font-weight:600; }
    .st-present { background:var(--success-soft); color:var(--success-text); border:1px solid var(--success-border); }
    .st-absent { background:var(--danger-soft); color:var(--danger-text); border:1px solid var(--danger-border); }
    .st-leave { background:#EFF6FF; color:#1D4ED8; border:1px solid #BFDBFE; }
    .st-half { background:var(--warning-soft); color:var(--warning-text); border:1px solid var(--warning-border); }
    .st-other { background:var(--bg-neutral); color:var(--text-2); border:1px solid var(--border); }
    .att-empty { text-align:center; color:var(--text-3); font-size:13.5px; padding:40px 16px; }

    /* Leave History tab */
    .lv-item { border:1px solid var(--border); border-radius:var(--radius-md); padding:14px 16px; margin-bottom:12px; }
    .lv-item:last-child { margin-bottom:0; }
    .lv-top { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:10px; }
    .lv-name { font-size:14px; font-weight:600; color:var(--text-1); }
    .lv-total { font-size:12px; color:var(--text-3); margin-top:1px; }
    .lv-meta { font-size:12px; color:var(--text-2); white-space:nowrap; }
    .lv-meta .sep { color:var(--text-3); margin:0 4px; }
    .lv-meta .rem { color:var(--success); font-weight:600; }
    .lv-bar { height:7px; background:var(--bg-neutral); border-radius:var(--radius-pill); overflow:hidden; }
    .lv-bar span { display:block; height:100%; background:var(--brand-red); border-radius:var(--radius-pill); }
    .lv-stat { display:flex; align-items:center; justify-content:space-between; padding:14px 0; border-bottom:1px solid var(--border-soft); font-size:13.5px; color:var(--text-2); }
    .lv-stat b { color:var(--text-1); font-weight:600; }
    .lv-stat b.rem { color:var(--success); }

    /* Payroll tab */
    .sal-row { display:flex; align-items:center; justify-content:space-between; padding:13px 2px; border-bottom:1px solid var(--border-soft); font-size:13.5px; color:var(--text-2); }
    .sal-row .amt { font-weight:600; color:var(--text-1); }
    .sal-row .amt.na { color:var(--text-3); font-weight:500; }
    .sal-row.gross { margin:6px -22px -22px; padding:15px 22px; background:var(--bg-page); border-bottom:none; border-radius:0 0 var(--radius-lg) var(--radius-lg); font-weight:600; color:var(--text-1); }
    .sal-row.gross .amt { font-size:15px; }
    .att-table .ded { color:var(--danger); font-weight:500; }
    .att-table .net { color:var(--success); font-weight:600; }
    .att-table .na { color:var(--text-3); }
    .att-table .pay-view { color:#4b4bdc; text-decoration:none; font-weight:500; }
    .att-table .pay-view:hover { text-decoration:underline; }
</style>
@endpush

@php
    $fdate = fn($d) => $d ? \Illuminate\Support\Carbon::parse($d)->format('F j, Y') : 'N/A';
    $val = fn($v) => ($v === null || $v === '') ? 'N/A' : $v;
    $initials = strtoupper(mb_substr($emp->full_name ?? 'E', 0, 1)) . strtoupper(mb_substr(str_contains(trim($emp->full_name ?? ''), ' ') ? explode(' ', trim($emp->full_name))[1] : '', 0, 1));
    $desig = $emp->designation_name ?: 'No Designation';
    $dept  = $emp->department_name ?: 'No Department';
@endphp

<div class="hr-wrap">

<div class="hr-head">
        <div>
            <div class="hr-eyebrow">HR Management</div>
            <h1>Employee Details</h1>
            <!--<p>Company employee directory.</p>-->
        </div>
        <div style="display:flex; align-items:center; gap:10px; flex-wrap:wrap;margin-bottom:20px;">
            <a href="{{ route('hr.employees') }}" class="btn btn-secondary">
                <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Back
            </a>
        </div>
    </div>



    {{-- Header --}}
    <div class="emp-header">
        @if($emp->profile_url)
            <img src="{{ $emp->profile_url }}" class="avatar" onerror="this.outerHTML='<div class=&quot;avatar-fb&quot;>{{ $initials }}</div>'">
        @else
            <div class="avatar-fb">{{ $initials }}</div>
        @endif
        <div class="id-block">
            <h1>{{ $emp->full_name }}</h1>
            <div class="sub">{{ $desig }} · {{ $dept }}</div>
            <div class="meta">
                <span class="m"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>{{ $val($emp->employee_id) }}</span>
                <span class="m"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>Joined {{ $fdate($emp->join_date ?: $emp->date_of_hire) }}</span>
                <span class="m"><svg viewBox="0 0 24 24"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>{{ $emp->email ?: 'No email' }}</span>
            </div>
        </div>
        <div class="actions">
            <a href="{{ route('hr.employees.edit', $emp->id) }}" class="btn btn-secondary"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>Edit Details</a>
            <a href="#" class="btn btn-primary"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>View Payslip</a>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="stat-grid">
        <div class="stat-card">
            <div class="stat-ico ico-green"><svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
            <div><div class="v num">{{ $emp->att_rate }}%</div><div class="l">Attendance Rate</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-ico ico-blue"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
            <div><div class="v num">{{ $leave['stats']['remaining'] }}/{{ $leave['stats']['allocated'] }}</div><div class="l">Leave Balance</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-ico ico-amber"><svg viewBox="0 0 24 24"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></div>
            <div><div class="v num" id="topSalary">{{ $payroll['gross'] > 0 ? '$'.number_format($payroll['gross'], 2) : 'N/A' }}</div><div class="l">Monthly Salary</div></div>
        </div>
        <div class="stat-card">
            <div class="stat-ico ico-purple"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg></div>
            <div><div class="v num">{{ $emp->att_hours_month }}h</div><div class="l">This Month</div></div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="tabs" id="empTabs">
        <button class="tab active" data-tab="overview">Overview</button>
        <button class="tab" data-tab="attendance">Attendance</button>
        <button class="tab" data-tab="leave">Leave History</button>
        <button class="tab" data-tab="payroll">Payroll</button>
        <button class="tab" data-tab="documents">Documents</button>
    </div>

    {{-- Overview --}}
    <div class="tab-pane active" data-pane="overview">
        <div class="cols">
            <div class="info-card">
                <div class="head"><h2>Personal Information</h2>
                    <a href="{{ route('hr.employees.edit', $emp->id) }}" class="edit-ico"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                </div>
                <div class="kv-grid">
                    <div class="kv"><div class="k">Full Name</div><div class="v">{{ $val($emp->full_name) }}</div></div>
                    <div class="kv"><div class="k">Date of Birth</div><div class="v">{{ $fdate($emp->date_of_birth) }}</div></div>
                    <div class="kv"><div class="k">Gender</div><div class="v">{{ $val($emp->gender) }}</div></div>
                    <div class="kv"><div class="k">Phone Number</div><div class="v">{{ $val($emp->mobile_number) }}</div></div>
                    <div class="kv"><div class="k">Email Address</div><div class="v">{{ $val($emp->email) }}</div></div>
                    <div class="kv"><div class="k">Emergency Contact</div><div class="v">{{ $val($emp->emergency_contact_number) }}</div></div>
                    <div class="kv full"><div class="k">Address</div><div class="v">{{ $val($emp->address) }}{{ $emp->city ? ', '.$emp->city : '' }}{{ $emp->state ? ', '.$emp->state : '' }}{{ $emp->country ? ', '.$emp->country : '' }}</div></div>
                </div>
            </div>

            <div class="info-card">
                <div class="head"><h2>Employment Details</h2>
                    <a href="{{ route('hr.employees.edit', $emp->id) }}" class="edit-ico"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></a>
                </div>
                <div class="kv-grid">
                    <div class="kv"><div class="k">Employee ID</div><div class="v">{{ $val($emp->employee_id) }}</div></div>
                    <div class="kv"><div class="k">Join Date</div><div class="v">{{ $fdate($emp->join_date ?: $emp->date_of_hire) }}</div></div>
                    <div class="kv"><div class="k">Department</div><div class="v">{{ $val($emp->department_name) }}</div></div>
                    <div class="kv"><div class="k">Designation</div><div class="v">{{ $val($emp->designation_name) }}</div></div>
                    <div class="kv"><div class="k">Employment Type</div><div class="v">Full-Time</div></div>
                    <div class="kv"><div class="k">Work Location</div><div class="v">{{ $val($emp->work_location) }}</div></div>
                    <div class="kv"><div class="k">Qualification</div><div class="v">{{ $val($emp->qualification_name) }}</div></div>
                    <div class="kv"><div class="k">Status</div><div class="v"><span class="badge {{ $emp->status ? 'b-active' : 'b-inactive' }}">{{ $emp->status ? 'ACTIVE' : 'INACTIVE' }}</span></div></div>
                </div>
            </div>
        </div>

        <div class="activity-card">
            <h2>Recent Activity</h2>
            <div class="act-row">
                <div class="act-ico"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></div>
                <div>
                    <div class="t">Employee record created</div>
                    <div class="d">{{ $emp->created_at ? $emp->created_at->format('M j, Y \a\t h:i A') : '—' }}</div>
                </div>
                <div class="when">{{ $emp->created_at ? $emp->created_at->diffForHumans() : '' }}</div>
            </div>
        </div>
    </div>

    {{-- Documents --}}
    <div class="tab-pane" data-pane="documents">
        <div class="info-card">
            <div class="head"><h2>Employee Documents</h2>
                <button type="button" class="btn btn-dark" id="uploadDocBtn">
                    <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    Upload Document
                </button>
            </div>
            <div class="doc2-empty {{ count($empDocuments) ? 'hidden' : '' }}" id="docEmpty">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                <div class="t">No documents uploaded yet</div>
                <div class="s">Click "Upload Document" to add files</div>
            </div>
            <div class="doc2-grid" id="docGrid">
                @foreach($empDocuments as $d)
                    <div class="doc2" data-id="{{ $d['id'] }}">
                        <div class="doc2-ico"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>
                        <div class="doc2-name">{{ $d['name'] }}</div>
                        <div class="doc2-meta">Uploaded on {{ $d['uploaded'] }}</div>
                        <div class="doc2-meta">{{ $d['size'] }}</div>
                        <div class="doc2-actions">
                            <a href="{{ $d['url'] }}" download>Download</a>
                            <a href="javascript:void(0)" class="del" data-id="{{ $d['id'] }}">Delete</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Attendance --}}
    <div class="tab-pane" data-pane="attendance">
        <div class="info-card att-card">
            <div class="att-head">
                <h2 id="attTitle">Attendance History</h2>
                <div class="att-controls">
                    <select id="attMonth" class="att-select">
                        @foreach($months as $m)
                            <option value="{{ $m['value'] }}">{{ $m['label'] }}</option>
                        @endforeach
                    </select>
                    <button type="button" class="btn btn-secondary" id="attExport">
                        <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Export
                    </button>
                </div>
            </div>

            <div class="att-summary">
                <div class="asum"><div class="k">Working Days</div><div class="v" id="asWork">0</div></div>
                <div class="asum"><div class="k">Present</div><div class="v" id="asPresent" style="color:var(--success);">0</div></div>
                <div class="asum"><div class="k">Absent</div><div class="v" id="asAbsent" style="color:var(--danger);">0</div></div>
                <div class="asum"><div class="k">Leave</div><div class="v" id="asLeave">0</div></div>
                <div class="asum"><div class="k">Total Hours</div><div class="v" id="asHours">0h</div></div>
            </div>

            <table class="att-table">
                <thead>
                    <tr><th>Date</th><th>Status</th><th>Check In</th><th>Check Out</th><th>Total Hours</th><th>Notes</th></tr>
                </thead>
                <tbody id="attBody"></tbody>
            </table>
            <div class="att-empty" id="attEmpty" hidden>No attendance records found for this month</div>
        </div>
    </div>

    {{-- Leave History --}}
    <div class="tab-pane" data-pane="leave">
        <div class="cols">
            <div class="info-card">
                <div class="head"><h2>Leave Balance</h2></div>
                @forelse($leave['balances'] as $b)
                    @php $pct = $b['total'] > 0 ? min(100, round($b['used'] / $b['total'] * 100)) : 0; @endphp
                    <div class="lv-item">
                        <div class="lv-top">
                            <div>
                                <div class="lv-name">{{ $b['type'] }}</div>
                                <div class="lv-total">Total: {{ $b['total'] }} days</div>
                            </div>
                            <div class="lv-meta">Used: {{ $b['used'] + 0 }} <span class="sep">|</span> <span class="rem">Remaining: {{ $b['remaining'] + 0 }}</span></div>
                        </div>
                        <div class="lv-bar"><span style="width: {{ $pct }}%"></span></div>
                    </div>
                @empty
                    <div class="empty-pane" style="border:none;padding:20px;">No leave types configured.</div>
                @endforelse
            </div>

            <div class="info-card">
                <div class="head"><h2>Leave Statistics</h2></div>
                <div class="lv-stat"><span>Total Allocated ({{ $leave['stats']['year'] }})</span><b>{{ $leave['stats']['allocated'] }} days</b></div>
                <div class="lv-stat"><span>Total Leave Taken ({{ $leave['stats']['year'] }})</span><b>{{ $leave['stats']['taken'] + 0 }} days</b></div>
                <div class="lv-stat"><span>Total Remaining</span><b class="rem">{{ $leave['stats']['remaining'] }} days</b></div>
                <div class="lv-stat"><span>Pending Requests</span><b>{{ $leave['stats']['pending'] }}</b></div>
                <div class="lv-stat" style="border-bottom:none;"><span>Most Used Type</span><b>{{ $leave['stats']['most_used'] }}</b></div>
            </div>
        </div>

        <div class="info-card" style="margin-top:20px;">
            <div class="head"><h2>Leave History</h2></div>
            <table class="att-table">
                <thead>
                    <tr><th>Leave Type</th><th>From Date</th><th>To Date</th><th>Days</th><th>Reason</th><th>Status</th><th>Applied On</th></tr>
                </thead>
                <tbody>
                    @forelse($leave['history'] as $h)
                        <tr>
                            <td>{{ $h['leave_type'] }}</td>
                            <td>{{ $h['from_date'] }}</td>
                            <td>{{ $h['to_date'] }}</td>
                            <td>{{ $h['days'] }}</td>
                            <td>{{ $h['reason'] }}</td>
                            <td>
                                @php $lc = ['approved'=>'st-present','rejected'=>'st-absent','pending'=>'st-half','cancelled'=>'st-other'][$h['status']] ?? 'st-other'; @endphp
                                <span class="att-pill {{ $lc }}">{{ ucfirst($h['status']) }}</span>
                            </td>
                            <td>{{ $h['applied_on'] }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="att-empty">No leave requests found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Payroll --}}
    @php $money = fn($v) => '$'.number_format((float)$v, 2); @endphp
    <div class="tab-pane" data-pane="payroll">
        <div class="cols">
            <div class="info-card">
                <div class="head"><h2>Salary Information</h2>
                    <button type="button" class="edit-ico" id="editSalaryBtn" aria-label="Edit salary"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                </div>
                <div class="sal-row"><span>Base Salary</span><span class="amt" id="salBase">{{ $payroll['base'] > 0 ? $money($payroll['base']) : 'N/A' }}</span></div>
                <div class="sal-row"><span id="salHraLabel">HRA ({{ rtrim(rtrim(number_format($payroll['hra_pct'],2),'0'),'.') }}%)</span><span class="amt {{ $payroll['hra_amt'] === null ? 'na' : '' }}" id="salHraVal">{{ $payroll['hra_amt'] !== null ? $money($payroll['hra_amt']) : 'N/A' }}</span></div>
                <div class="sal-row"><span id="salTaLabel">Transport Allowance ({{ rtrim(rtrim(number_format($payroll['ta_pct'],2),'0'),'.') }}%)</span><span class="amt {{ $payroll['ta_amt'] === null ? 'na' : '' }}" id="salTaVal">{{ $payroll['ta_amt'] !== null ? $money($payroll['ta_amt']) : 'N/A' }}</span></div>
                <div class="sal-row gross"><span>Gross Salary</span><span class="amt" id="salGross">{{ $money($payroll['gross']) }}</span></div>
            </div>

            <div class="info-card">
                <div class="head"><h2>Bank Details</h2>
                    <button type="button" class="edit-ico" id="editBankBtn" aria-label="Edit bank details"><svg viewBox="0 0 24 24"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.12 2.12 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button>
                </div>
                <div class="kv-grid">
                    <div class="kv"><div class="k">Bank Name</div><div class="v" id="bankName">{{ $payroll['bank']['name'] ?: 'N/A' }}</div></div>
                    <div class="kv"><div class="k">Account Number</div><div class="v" id="bankAcc">{{ $payroll['bank']['account'] ?: 'N/A' }}</div></div>
                    <div class="kv"><div class="k">Account Type</div><div class="v">{{ $payroll['bank']['type'] }}</div></div>
                    <div class="kv"><div class="k">IFSC Code</div><div class="v" id="bankIfsc">{{ $payroll['bank']['ifsc'] ?: 'N/A' }}</div></div>
                </div>
            </div>
        </div>

        <div class="info-card" style="margin-top:20px;">
            <div class="head"><h2>Payroll History</h2>
                <button type="button" class="btn btn-secondary" id="payExport">
                    <svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                    Export
                </button>
            </div>
            <table class="att-table" id="payTable">
                <thead>
                    <tr><th>Month</th><th>Working Days</th><th>Present</th><th>Gross Salary</th><th>Deductions</th><th>Net Salary</th><th>Status</th><th>Action</th></tr>
                </thead>
                <tbody>
                    @forelse($payroll['history'] as $p)
                        <tr>
                            <td>{{ $p['month'] }}</td>
                            <td>{{ $p['working_days'] }}</td>
                            <td>{{ $p['present'] }}</td>
                            <td>{{ $money($p['gross']) }}</td>
                            <td>{!! $p['deduction'] > 0 ? '<span class="ded">-'.$money($p['deduction']).'</span>' : '<span class="na">N/A</span>' !!}</td>
                            <td><span class="net">{{ $money($p['net']) }}</span></td>
                            <td>
                                @php $pc = ['paid'=>'st-present','pending'=>'st-half','processing'=>'st-leave','unpaid'=>'st-absent'][strtolower($p['status'])] ?? 'st-other'; @endphp
                                <span class="att-pill {{ $pc }}">{{ ucfirst($p['status']) }}</span>
                            </td>
                            <td><a href="#" class="pay-view">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="att-empty">No payroll records found</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Edit Salary Information modal --}}
<div class="modal-ov" id="salaryModal">
    <div class="modal-bx">
        <div class="mhead">
            <h3>Edit Salary Information</h3>
            <p>Update the salary, HRA, and TA percentage for this employee.</p>
            <button type="button" class="mclose" data-close>&times;</button>
        </div>
        <div class="mbody">
            <div class="mfield"><label>Base Salary</label><input type="number" id="mSalary" step="0.01" min="0" value="{{ $payroll['base'] ?: '' }}"></div>
            <div class="mfield"><label>HRA (%)</label><input type="number" id="mHra" step="0.01" min="0" max="100" placeholder="Enter HRA percentage" value="{{ $payroll['hra_pct'] ?: '' }}"></div>
            <div class="mfield"><label>TA (%)</label><input type="number" id="mTa" step="0.01" min="0" max="100" placeholder="Enter TA percentage" value="{{ $payroll['ta_pct'] ?: '' }}"></div>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-secondary" data-close>Cancel</button>
            <button type="button" class="btn btn-primary" id="saveSalary">Save Changes</button>
        </div>
    </div>
</div>

{{-- Upload Document modal --}}
<div class="modal-ov" id="docModal">
    <div class="modal-bx">
        <div class="mhead">
            <h3>Upload Document</h3>
            <p>Upload a new document for this employee.</p>
            <button type="button" class="mclose" data-close>&times;</button>
        </div>
        <div class="mbody">
            <div class="mfield"><label>Document Name</label><input type="text" id="docName" placeholder="Document Name"></div>
            <div class="mfield">
                <label>Select File</label>
                <input type="file" id="docFile" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                <div class="mhint">Accepted formats: PDF, DOC, DOCX, JPG, JPEG, PNG (Max 10MB)</div>
                <div class="mhint err" id="docErr" hidden></div>
            </div>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-secondary" data-close>Cancel</button>
            <button type="button" class="btn btn-primary" id="docUpload">Upload</button>
        </div>
    </div>
</div>

{{-- Edit Bank Details modal --}}
<div class="modal-ov" id="bankModal">
    <div class="modal-bx">
        <div class="mhead">
            <h3>Edit Bank Details</h3>
            <p>Update the bank account information for this employee.</p>
            <button type="button" class="mclose" data-close>&times;</button>
        </div>
        <div class="mbody">
            <div class="mfield"><label>Bank Name</label><input type="text" id="mBankName" placeholder="Enter bank name" value="{{ $payroll['bank']['name'] }}"></div>
            <div class="mfield"><label>Account Number</label><input type="text" id="mBankAcc" placeholder="Enter account number" value="{{ $payroll['bank']['account'] }}"></div>
            <div class="mfield"><label>IFSC Code</label><input type="text" id="mBankIfsc" placeholder="Enter IFSC code" value="{{ $payroll['bank']['ifsc'] }}"></div>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-secondary" data-close>Cancel</button>
            <button type="button" class="btn btn-primary" id="saveBank">Save Changes</button>
        </div>
    </div>
</div>

<script>
(function () {
    const tabs = document.querySelectorAll('#empTabs .tab');
    const panes = document.querySelectorAll('.tab-pane');
    let attLoaded = false;
    tabs.forEach(t => t.addEventListener('click', function () {
        tabs.forEach(x => x.classList.remove('active'));
        panes.forEach(p => p.classList.remove('active'));
        this.classList.add('active');
        const pane = document.querySelector('.tab-pane[data-pane="' + this.dataset.tab + '"]');
        if (pane) pane.classList.add('active');
        if (this.dataset.tab === 'attendance' && !attLoaded) { attLoaded = true; loadAttendance(); }
    }));

    // ── Attendance tab ──────────────────────────────────────────────
    const attUrl = @json(route('hr.employees.attendance', $emp->id));
    const monthSel = document.getElementById('attMonth');
    let lastData = null;

    function esc(s) { const d = document.createElement('div'); d.textContent = s == null ? '' : s; return d.innerHTML; }

    function loadAttendance() {
        const month = monthSel.value;
        fetch(attUrl + '?month=' + encodeURIComponent(month), { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(renderAttendance)
            .catch(() => {});
    }

    function renderAttendance(d) {
        if (!d || !d.ok) return;
        lastData = d;
        document.getElementById('attTitle').textContent = 'Attendance History - ' + d.month_label;
        document.getElementById('asWork').textContent = d.working_days;
        document.getElementById('asPresent').textContent = d.present;
        document.getElementById('asAbsent').textContent = d.absent;
        document.getElementById('asLeave').textContent = d.leave;
        document.getElementById('asHours').textContent = d.total_hours + 'h';

        const body = document.getElementById('attBody');
        const empty = document.getElementById('attEmpty');
        body.innerHTML = '';
        if (!d.records.length) {
            empty.hidden = false;
            return;
        }
        empty.hidden = true;
        d.records.forEach(function (r) {
            body.insertAdjacentHTML('beforeend',
                '<tr><td>' + esc(r.date) + '</td>' +
                '<td><span class="att-pill ' + r.class + '">' + esc(r.status) + '</span></td>' +
                '<td>' + esc(r.check_in) + '</td>' +
                '<td>' + esc(r.check_out) + '</td>' +
                '<td>' + esc(r.hours) + '</td>' +
                '<td>' + esc(r.remarks) + '</td></tr>');
        });
    }

    if (monthSel) monthSel.addEventListener('change', loadAttendance);

    // ── Payroll edit modals ─────────────────────────────────────────
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const salaryUrl = @json(route('hr.employees.salary', $emp->id));
    const bankUrl   = @json(route('hr.employees.bank', $emp->id));
    const gid = id => document.getElementById(id);

    document.querySelectorAll('.modal-ov').forEach(function (ov) {
        ov.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => ov.classList.remove('open')));
        ov.addEventListener('click', e => { if (e.target === ov) ov.classList.remove('open'); });
    });
    document.addEventListener('keydown', e => { if (e.key === 'Escape') document.querySelectorAll('.modal-ov.open').forEach(m => m.classList.remove('open')); });

    if (gid('editSalaryBtn')) gid('editSalaryBtn').addEventListener('click', () => gid('salaryModal').classList.add('open'));
    if (gid('editBankBtn')) gid('editBankBtn').addEventListener('click', () => gid('bankModal').classList.add('open'));

    if (gid('saveSalary')) gid('saveSalary').addEventListener('click', function () {
        const btn = this; btn.disabled = true;
        fetch(salaryUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: new URLSearchParams({ salary: gid('mSalary').value, hra: gid('mHra').value, ta: gid('mTa').value })
        }).then(r => r.json()).then(d => {
            btn.disabled = false;
            if (!d.ok) return;
            gid('salBase').textContent = d.base_fmt;
            gid('salHraLabel').textContent = d.hra_label;
            gid('salHraVal').textContent = d.hra_fmt; gid('salHraVal').classList.toggle('na', d.hra_fmt === 'N/A');
            gid('salTaLabel').textContent = d.ta_label;
            gid('salTaVal').textContent = d.ta_fmt; gid('salTaVal').classList.toggle('na', d.ta_fmt === 'N/A');
            gid('salGross').textContent = d.gross_fmt;
            gid('topSalary').textContent = d.monthly_fmt;
            gid('mSalary').value = d.base; gid('mHra').value = d.hra_pct; gid('mTa').value = d.ta_pct;
            gid('salaryModal').classList.remove('open');
        }).catch(() => { btn.disabled = false; });
    });

    if (gid('saveBank')) gid('saveBank').addEventListener('click', function () {
        const btn = this; btn.disabled = true;
        fetch(bankUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: new URLSearchParams({ bank_name: gid('mBankName').value, account_number: gid('mBankAcc').value, ifsc_code: gid('mBankIfsc').value })
        }).then(r => r.json()).then(d => {
            btn.disabled = false;
            if (!d.ok) return;
            gid('bankName').textContent = d.name;
            gid('bankAcc').textContent = d.account;
            gid('bankIfsc').textContent = d.ifsc;
            gid('mBankName').value = d.name_raw; gid('mBankAcc').value = d.account_raw; gid('mBankIfsc').value = d.ifsc_raw;
            gid('bankModal').classList.remove('open');
        }).catch(() => { btn.disabled = false; });
    });

    // ── Documents tab ───────────────────────────────────────────────
    const docUrl = @json(route('hr.employees.documents.store', $emp->id));
    const docDelBase = @json(url('hr/employees/' . $emp->id . '/documents'));

    if (gid('uploadDocBtn')) gid('uploadDocBtn').addEventListener('click', () => gid('docModal').classList.add('open'));

    function docCard(d) {
        const el = document.createElement('div');
        el.className = 'doc2';
        el.dataset.id = d.id;
        el.innerHTML =
            '<div class="doc2-ico"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>' +
            '<div class="doc2-name">' + esc(d.name) + '</div>' +
            '<div class="doc2-meta">Uploaded on ' + esc(d.uploaded) + '</div>' +
            '<div class="doc2-meta">' + esc(d.size) + '</div>' +
            '<div class="doc2-actions"><a href="' + esc(d.url) + '" download>Download</a>' +
            '<a href="javascript:void(0)" class="del" data-id="' + d.id + '">Delete</a></div>';
        return el;
    }

    if (gid('docUpload')) gid('docUpload').addEventListener('click', function () {
        const name = gid('docName').value.trim();
        const file = gid('docFile').files[0];
        const err = gid('docErr');
        err.hidden = true;
        if (!name) { err.textContent = 'Please enter a document name.'; err.hidden = false; return; }
        if (!file) { err.textContent = 'Please choose a file.'; err.hidden = false; return; }

        const btn = this; btn.disabled = true;
        const fd = new FormData();
        fd.append('document_name', name);
        fd.append('document_file', file);
        fetch(docUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: fd })
            .then(r => r.json().then(j => ({ status: r.status, j })))
            .then(({ status, j }) => {
                btn.disabled = false;
                if (status === 422) { err.textContent = Object.values(j.errors)[0][0]; err.hidden = false; return; }
                if (!j.ok) { err.textContent = j.message || 'Upload failed.'; err.hidden = false; return; }
                gid('docGrid').prepend(docCard(j.doc));
                gid('docEmpty').classList.add('hidden');
                gid('docName').value = ''; gid('docFile').value = '';
                gid('docModal').classList.remove('open');
            }).catch(() => { btn.disabled = false; err.textContent = 'Upload failed.'; err.hidden = false; });
    });

    // Delete document (event-delegated).
    gid('docGrid').addEventListener('click', function (e) {
        const del = e.target.closest('.del');
        if (!del) return;
        if (!confirm('Delete this document?')) return;
        const card = del.closest('.doc2');
        fetch(docDelBase + '/' + del.dataset.id, { method: 'POST', headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' }, body: new URLSearchParams({ _method: 'DELETE' }) })
            .then(r => r.json()).then(d => {
                if (!d.ok) return;
                card.remove();
                if (!gid('docGrid').querySelector('.doc2')) gid('docEmpty').classList.remove('hidden');
            }).catch(() => {});
    });

    // Payroll history → CSV export (from the rendered table).
    const payExport = document.getElementById('payExport');
    if (payExport) payExport.addEventListener('click', function () {
        const rows = [['Month', 'Working Days', 'Present', 'Gross Salary', 'Deductions', 'Net Salary', 'Status']];
        document.querySelectorAll('#payTable tbody tr').forEach(function (tr) {
            const c = tr.querySelectorAll('td');
            if (c.length < 7) return;
            rows.push([c[0], c[1], c[2], c[3], c[4], c[5], c[6]].map(td => td.innerText.trim()));
        });
        if (rows.length < 2) return;
        const csv = rows.map(r => r.map(v => '"' + String(v).replace(/"/g, '""') + '"').join(',')).join('\n');
        const url = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
        const a = document.createElement('a');
        a.href = url; a.download = 'payroll_history.csv'; a.click();
        URL.revokeObjectURL(url);
    });

    // Export current month's records as CSV.
    const exportBtn = document.getElementById('attExport');
    if (exportBtn) exportBtn.addEventListener('click', function () {
        if (!lastData) return;
        const rows = [['Date', 'Status', 'Check In', 'Check Out', 'Total Hours', 'Notes']];
        lastData.records.forEach(r => rows.push([r.date, r.status, r.check_in, r.check_out, r.hours, r.remarks]));
        const csv = rows.map(row => row.map(c => '"' + String(c).replace(/"/g, '""') + '"').join(',')).join('\n');
        const url = URL.createObjectURL(new Blob([csv], { type: 'text/csv' }));
        const a = document.createElement('a');
        a.href = url; a.download = 'attendance_' + monthSel.value + '.csv'; a.click();
        URL.revokeObjectURL(url);
    });
})();
</script>
</x-layouts.app>
