<x-layouts.app title="HR Settings">
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/datatables.net/css/jquery.dataTables.css') }}">
<style>
    .hr-wrap { padding: 24px 28px 48px; width: 100%; }
    .hr-head { margin-bottom: 20px; }
    .hr-eyebrow { font-size:11px; font-weight:600; letter-spacing:.09em; text-transform:uppercase; color:var(--brand-red); }
    .hr-head h1 { font-size:24px; font-weight:600; letter-spacing:-.5px; color:var(--text-1); margin-top:4px; }
    .hr-head p { font-size:13px; color:var(--text-2); margin-top:4px; }

    .btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:9px 15px; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:500; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2; }
    .btn-primary { background:var(--brand-red); color:#fff; width:100%; } .btn-primary:hover { background:var(--brand-red-dark); }
    .btn-secondary { background:var(--bg-card); color:var(--text-1); border-color:var(--border); } .btn-secondary:hover { border-color:var(--text-3); }
    .btn-danger { background:var(--danger); color:#fff; } .btn-danger:hover { background:#7f1d1d; }

    /* Two-pane settings layout */
    .set-grid { display:grid; grid-template-columns:230px 1fr; gap:20px; align-items:start; }
    @media(max-width:820px){ .set-grid{ grid-template-columns:1fr; } }

    /* Vertical tab rail */
    .set-tabs { display:flex; flex-direction:column; gap:2px; background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:8px; }
    .set-tab { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:var(--radius-sm); font-size:13px; font-weight:500; color:var(--text-2); background:none; border:none; font-family:inherit; text-align:left; cursor:pointer; width:100%; }
    .set-tab svg { width:16px; height:16px; stroke:currentColor; fill:none; stroke-width:1.9; flex-shrink:0; }
    .set-tab:hover { background:var(--bg-page); color:var(--text-1); }
    .set-tab.active { background:var(--brand-red-soft); color:var(--brand-red-dark); font-weight:600; }
    .set-tab .soon { margin-left:auto; font-size:9.5px; font-weight:600; letter-spacing:.03em; color:var(--text-3); background:var(--bg-neutral); border:1px solid var(--border); padding:1px 6px; border-radius:var(--radius-pill); }

    /* Tab panes */
    .set-pane { display:none; }
    .set-pane.active { display:block; }
    .pane-head { margin-bottom:16px; }
    .pane-head h2 { font-size:18px; font-weight:700; color:var(--text-1); }
    .pane-head p { font-size:13px; color:var(--text-2); margin-top:3px; }

    /* Job Category: form + table */
    .jc-grid { display:grid; grid-template-columns:340px 1fr; gap:20px; align-items:start; }
    @media(max-width:1000px){ .jc-grid{ grid-template-columns:1fr; } }
    .card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:20px; }
    .card h3 { font-size:16px; font-weight:600; color:var(--text-1); }
    .card .sub { font-size:12.5px; color:var(--text-3); margin:4px 0 16px; line-height:1.5; }
    .fld label { display:block; font-size:12.5px; font-weight:500; color:var(--text-2); margin-bottom:6px; }
    .fld input, .fld select { width:100%; height:40px; border:1px solid var(--border); border-radius:var(--radius-sm); padding:0 12px; font-family:inherit; font-size:13px; color:var(--text-1); background:var(--bg-card); outline:none; }
    .fld input:focus, .fld select:focus { border-color:var(--brand-red); box-shadow:0 0 0 3px var(--brand-red-soft); }
    .fld select:disabled { background:var(--bg-neutral); color:var(--text-3); cursor:not-allowed; }
    .btn-primary:disabled { background:var(--brand-red); opacity:.5; cursor:not-allowed; }
    .fld-note { font-size:12px; color:var(--brand-red-dark); text-align:center; margin-top:12px; }
    .fld + .fld { margin-top:16px; }
    .fld .err { font-size:11.5px; color:var(--danger); margin-top:5px; display:none; }
    .jc-form .btn-primary { margin-top:14px; }

    .card-table { padding:16px; }
    .badge { display:inline-block; padding:3px 11px; border-radius:var(--radius-pill); font-size:11.5px; font-weight:600; }
    .b-active { background:var(--success-soft); color:var(--success-text); border:1px solid var(--success-border); }
    .b-inactive { background:var(--bg-neutral); color:var(--text-2); border:1px solid var(--border); }
    .creator-badge { display:inline-block; padding:2px 12px; border-radius:var(--radius-pill); font-size:11.5px; font-weight:500; background:var(--bg-neutral); color:var(--text-2); border:1px solid var(--border); }
    .row-acts { display:flex; gap:6px; }
    .ic-btn { display:inline-flex; align-items:center; justify-content:center; width:30px; height:30px; border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--bg-card); color:var(--brand-red-dark); cursor:pointer; }
    .ic-btn svg { width:14px; height:14px; stroke:currentColor; fill:none; stroke-width:2; }
    .ic-btn:hover { border-color:var(--brand-red); background:var(--brand-red-soft); }
    .ic-btn.danger { color:var(--danger); } .ic-btn.danger:hover { border-color:var(--danger); background:var(--danger-soft); }

    /* Modal (edit category) */
    .modal-ov { position:fixed; inset:0; background:rgba(15,23,42,.5); z-index:1200; display:none; align-items:center; justify-content:center; padding:20px; }
    .modal-ov.open { display:flex; }
    .modal-bx { background:var(--bg-card); border-radius:var(--radius-lg); width:100%; max-width:420px; box-shadow:0 24px 60px rgba(0,0,0,.25); overflow:hidden; }
    .mhead { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--border-soft); }
    .mhead h3 { font-size:15px; font-weight:600; color:var(--text-1); }
    .mclose { border:none; background:none; font-size:20px; color:var(--text-3); cursor:pointer; }
    .mbody { padding:18px 20px; }
    .mfoot { display:flex; justify-content:flex-end; gap:8px; padding:14px 20px; border-top:1px solid var(--border-soft); background:var(--bg-page); }

    /* Telegram notifications */
    .tg-head { display:flex; align-items:flex-start; gap:12px; padding:20px; background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); margin-bottom:14px; }
    .tg-head .tg-ico { width:38px; height:38px; flex-shrink:0; display:flex; align-items:center; justify-content:center; border-radius:var(--radius-md); background:#EFF6FF; color:#2563EB; }
    .tg-head .tg-ico svg { width:19px; height:19px; stroke:currentColor; fill:none; stroke-width:2; }
    .tg-head h2 { font-size:16px; font-weight:600; color:var(--text-1); }
    .tg-head p { font-size:12.5px; color:var(--text-2); margin-top:2px; }
    .tg-card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:14px 18px; }
    .tg-row { display:flex; align-items:center; justify-content:space-between; gap:16px; padding:14px 16px; border-radius:var(--radius-md); margin-bottom:12px; }
    .tg-row:last-child { margin-bottom:0; }
    .tg-row.blue { background:#EFF6FF; border:1px solid #DBEAFE; }
    .tg-row.amber { background:#FFF7ED; border:1px solid #FED7AA; }
    .tg-info { display:flex; align-items:center; gap:12px; }
    .tg-info .r-ico { width:34px; height:34px; flex-shrink:0; display:flex; align-items:center; justify-content:center; border-radius:var(--radius-md); background:var(--bg-card); }
    .tg-info .r-ico svg { width:17px; height:17px; stroke-width:2; fill:none; }
    .tg-row.blue .r-ico svg { stroke:#2563EB; } .tg-row.amber .r-ico svg { stroke:#EA580C; }
    .tg-info h4 { font-size:13.5px; font-weight:600; color:var(--text-1); }
    .tg-info p { font-size:12px; color:var(--text-2); margin-top:1px; }
    /* toggle switch */
    .tg-switch { position:relative; display:inline-block; width:44px; height:24px; flex-shrink:0; }
    .tg-switch input { opacity:0; width:0; height:0; }
    .tg-slider { position:absolute; inset:0; cursor:pointer; background:var(--text-3); border-radius:24px; transition:.2s; }
    .tg-slider:before { content:''; position:absolute; height:18px; width:18px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:.2s; }
    .tg-switch input:checked + .tg-slider { background:#2563EB; }
    .tg-switch input:checked + .tg-slider:before { transform:translateX(20px); }
    .tg-switch input:disabled + .tg-slider { opacity:.55; cursor:wait; }
    .tg-note { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:18px 20px; margin-top:18px; }
    .tg-note h4 { font-size:12.5px; font-weight:600; color:var(--text-2); margin-bottom:5px; }
    .tg-note p { font-size:12.5px; color:var(--text-3); line-height:1.5; }
    .tg-alert { display:flex; align-items:flex-start; gap:12px; background:var(--danger-soft); border:1px solid var(--danger-border); border-radius:var(--radius-lg); padding:16px 18px; margin-top:18px; }
    .tg-alert-ico { flex-shrink:0; width:30px; height:30px; display:flex; align-items:center; justify-content:center; border-radius:var(--radius-md); background:#fff; }
    .tg-alert-ico svg { width:17px; height:17px; stroke:var(--danger); fill:none; stroke-width:2; }
    .tg-alert h4 { font-size:13px; font-weight:600; color:var(--danger-text); margin-bottom:3px; }
    .tg-alert p { font-size:12.5px; color:var(--danger-text); line-height:1.5; }
    .tg-alert code { font-family:monospace; font-size:11.5px; background:rgba(0,0,0,.05); padding:1px 5px; border-radius:4px; }

    .placeholder { text-align:center; color:var(--text-3); padding:60px 20px; }
    .placeholder .pi { font-size:34px; margin-bottom:10px; }
    .placeholder h3 { font-size:15px; font-weight:600; color:var(--text-2); }

    #toast { position:fixed; right:20px; bottom:20px; z-index:1300; display:none; padding:12px 16px; border-radius:var(--radius-md); font-size:13px; color:#fff; box-shadow:0 12px 32px rgba(0,0,0,.2); }
    #toast.ok { background:var(--success); } #toast.err { background:var(--danger); }
</style>
@endpush

@php
    $tabs = [
        ['key' => 'job-category', 'label' => 'Job Category', 'ready' => true,  'icon' => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>'],
        ['key' => 'qualifications', 'label' => 'Qualifications', 'ready' => true, 'icon' => '<path d="M22 10L12 5 2 10l10 5 10-5z"/><path d="M6 12v5c0 1 3 3 6 3s6-2 6-3v-5"/>'],
        ['key' => 'departments', 'label' => 'Departments', 'ready' => true, 'icon' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>'],
        ['key' => 'designations', 'label' => 'Designations', 'ready' => true, 'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/>'],
        ['key' => 'leave-settings', 'label' => 'Leave Settings', 'ready' => true, 'icon' => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>'],
        ['key' => 'allowances', 'label' => 'Allowances', 'ready' => true, 'icon' => '<line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'],
        ['key' => 'telegram', 'label' => 'Telegram Notifications', 'ready' => true, 'icon' => '<path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/>'],
    ];
@endphp

<div class="hr-wrap">
    <div class="hr-head">
        <div class="hr-eyebrow">HR Management</div>
        <h1>Settings</h1>
        <p>Manage system configuration and settings</p>
    </div>

    <div class="set-grid">
        {{-- Vertical tab rail --}}
        <nav class="set-tabs" id="setTabs">
            @foreach($tabs as $i => $t)
                <button type="button" class="set-tab {{ $i === 0 ? 'active' : '' }}" data-tab="{{ $t['key'] }}">
                    <svg viewBox="0 0 24 24">{!! $t['icon'] !!}</svg>
                    <span>{{ $t['label'] }}</span>
                    @unless($t['ready'])<span class="soon">SOON</span>@endunless
                </button>
            @endforeach
        </nav>

        {{-- Panes --}}
        <div class="set-content">
            {{-- Job Category --}}
            <section class="set-pane active" data-pane="job-category">
                <div class="pane-head">
                    <h2>Job Category Management</h2>
                    <p>Manage job categories for applications and employees</p>
                </div>

                <div class="jc-grid">
                    <div class="card jc-form">
                        <h3>Job Category</h3>
                        <p class="sub">Manage job categories in your system. Create, edit, or delete categories for organizing applications.</p>
                        <div class="fld">
                            <label for="jcName">Category Name</label>
                            <input type="text" id="jcName" placeholder="Enter category name" autocomplete="off">
                            <div class="err" id="jcErr"></div>
                        </div>
                        <button type="button" class="btn btn-primary" id="jcAddBtn">Add Category</button>
                    </div>

                    <div class="card card-table">
                        <div style="overflow-x:auto;">
                            <table id="jcTable" class="table dataTable" style="width:100%">
                                <thead>
                                    <tr><th>S.No</th><th>Category Name</th><th>Status</th><th>Actions</th></tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Qualifications --}}
            <section class="set-pane" data-pane="qualifications">
                <div class="pane-head">
                    <h2>Qualification Management</h2>
                    <p>Manage educational qualifications and certifications</p>
                </div>

                <div class="jc-grid">
                    <div class="card jc-form">
                        <h3>Qualification</h3>
                        <p class="sub">Manage qualifications in your system. Create, edit, or delete qualifications for organizing employees.</p>
                        <div class="fld">
                            <label for="qlName">Qualification Name</label>
                            <input type="text" id="qlName" placeholder="Enter qualification name" autocomplete="off">
                            <div class="err" id="qlErr"></div>
                        </div>
                        <button type="button" class="btn btn-primary" id="qlAddBtn">Add Qualification</button>
                    </div>

                    <div class="card card-table">
                        <div style="overflow-x:auto;">
                            <table id="qlTable" class="table dataTable" style="width:100%">
                                <thead>
                                    <tr><th>S.No</th><th>Qualification Name</th><th>Created By</th><th>Actions</th></tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Departments --}}
            <section class="set-pane" data-pane="departments">
                <div class="pane-head">
                    <h2>Department Management</h2>
                    <p>Manage organizational departments and divisions</p>
                </div>

                <div class="jc-grid">
                    <div class="card jc-form">
                        <h3>Department</h3>
                        <p class="sub">Manage departments in your system. Create, edit, or delete departments for organizing agents.</p>
                        <div class="fld">
                            <label for="dpName">Department Name</label>
                            <input type="text" id="dpName" placeholder="Enter department name" autocomplete="off">
                            <div class="err" id="dpErr"></div>
                        </div>
                        <button type="button" class="btn btn-primary" id="dpAddBtn">Add Department</button>
                    </div>

                    <div class="card card-table">
                        <div style="overflow-x:auto;">
                            <table id="dpTable" class="table dataTable" style="width:100%">
                                <thead>
                                    <tr><th>S.No</th><th>Department Name</th><th>Created By</th><th>Actions</th></tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Designations --}}
            <section class="set-pane" data-pane="designations">
                <div class="pane-head">
                    <h2>Designation Management</h2>
                    <p>Manage job designations and roles</p>
                </div>

                <div class="jc-grid">
                    <div class="card jc-form">
                        <h3>Designation</h3>
                        <p class="sub">Manage designations in your system. Create, edit, or delete designations for organizing employees.</p>
                        <div class="fld">
                            <label for="dsName">Designation Name</label>
                            <input type="text" id="dsName" placeholder="Enter designation name" autocomplete="off">
                            <div class="err" id="dsErr"></div>
                        </div>
                        <button type="button" class="btn btn-primary" id="dsAddBtn">Add Designation</button>
                    </div>

                    <div class="card card-table">
                        <div style="overflow-x:auto;">
                            <table id="dsTable" class="table dataTable" style="width:100%">
                                <thead>
                                    <tr><th>S.No</th><th>Designation Name</th><th>Created By</th><th>Actions</th></tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Leave Settings --}}
            <section class="set-pane" data-pane="leave-settings">
                <div class="pane-head">
                    <h2>Leave Settings</h2>
                    <p>Configure leave types and allocate days for employees</p>
                </div>

                <div class="jc-grid">
                    <div class="card jc-form">
                        <h3>Leave Type</h3>
                        <p class="sub">Add or edit leave types and their allocated days. Each leave type can only be added once.</p>
                        <div class="fld">
                            <label for="lsType">Leave Type</label>
                            <select id="lsType"><option value="">Select leave type</option></select>
                        </div>
                        <div class="fld">
                            <label for="lsDays">No of Days</label>
                            <input type="number" id="lsDays" min="0" placeholder="Enter number of days" autocomplete="off">
                        </div>
                        <div class="err" id="lsErr" style="margin-top:10px;"></div>
                        <button type="button" class="btn btn-primary" id="lsAddBtn">Add Leave Setting</button>
                        <div class="fld-note" id="lsAllNote" style="display:none;">All leave types have been configured</div>
                    </div>

                    <div class="card card-table">
                        <div style="overflow-x:auto;">
                            <table id="lsTable" class="table dataTable" style="width:100%">
                                <thead>
                                    <tr><th>S.No</th><th>Leave Type</th><th>No of Days</th><th>Created By</th><th>Actions</th></tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Allowances --}}
            <section class="set-pane" data-pane="allowances">
                <div class="pane-head">
                    <h2>Allowance Settings</h2>
                    <p>Configure allowance types and their percentage values</p>
                </div>

                <div class="jc-grid">
                    <div class="card jc-form">
                        <h3>Allowance Type</h3>
                        <p class="sub">Add or edit allowance types and their percentage values.</p>
                        <div class="fld">
                            <label for="alType">Allowance Type</label>
                            <input type="text" id="alType" placeholder="Enter allowance type" autocomplete="off">
                        </div>
                        <div class="fld">
                            <label for="alPct">Percentage (%)</label>
                            <input type="number" id="alPct" min="0" max="100" step="0.01" placeholder="Enter percentage" autocomplete="off">
                        </div>
                        <div class="err" id="alErr" style="margin-top:10px;"></div>
                        <button type="button" class="btn btn-primary" id="alAddBtn">Add Allowance</button>
                    </div>

                    <div class="card card-table">
                        <div style="overflow-x:auto;">
                            <table id="alTable" class="table dataTable" style="width:100%">
                                <thead>
                                    <tr><th>S.No</th><th>Allowance Type</th><th>Percentage</th><th>Created By</th><th>Actions</th></tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Telegram Notifications --}}
            @php
                $tgNew    = $notifications[\App\Models\HrNotificationSetting::KEY_NEW_APPLICATION] ?? null;
                $tgStatus = $notifications[\App\Models\HrNotificationSetting::KEY_STATUS_CHANGE] ?? null;
            @endphp
            <section class="set-pane" data-pane="telegram">
                <div class="tg-head">
                    <span class="tg-ico"><svg viewBox="0 0 24 24"><path d="M22 2L11 13"/><path d="M22 2l-7 20-4-9-9-4 20-7z"/></svg></span>
                    <div>
                        <h2>Telegram Notifications</h2>
                        <p>Configure which events trigger Telegram notifications</p>
                    </div>
                </div>

                <div class="tg-card">
                    <div class="tg-row blue">
                        <div class="tg-info">
                            <span class="r-ico"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
                            <div>
                                <h4>New Job Application</h4>
                                <p>{{ $tgNew->description ?? 'Send Telegram notification when new job application is received' }}</p>
                            </div>
                        </div>
                        <label class="tg-switch">
                            <input type="checkbox" class="tg-toggle" data-key="{{ \App\Models\HrNotificationSetting::KEY_NEW_APPLICATION }}" @checked($tgNew && (int) $tgNew->setting_value === 1)>
                            <span class="tg-slider"></span>
                        </label>
                    </div>

                    <div class="tg-row amber">
                        <div class="tg-info">
                            <span class="r-ico"><svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15"/></svg></span>
                            <div>
                                <h4>Application Status Change</h4>
                                <p>{{ $tgStatus->description ?? 'Send Telegram notification when application status is changed' }}</p>
                            </div>
                        </div>
                        <label class="tg-switch">
                            <input type="checkbox" class="tg-toggle" data-key="{{ \App\Models\HrNotificationSetting::KEY_STATUS_CHANGE }}" @checked($tgStatus && (int) $tgStatus->setting_value === 1)>
                            <span class="tg-slider"></span>
                        </label>
                    </div>
                </div>

                @if($telegramConfigured)
                    <div class="tg-note">
                        <h4>Note</h4>
                        <p>Make sure Telegram Bot Token and Chat ID are configured in the environment settings for notifications to work properly.</p>
                    </div>
                @else
                    <div class="tg-alert">
                        <span class="tg-alert-ico"><svg viewBox="0 0 24 24"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></span>
                        <div>
                            <h4>Telegram is not configured</h4>
                            <p><b>TELEGRAM_HR_BOT_TOKEN</b> and <b>TELEGRAM_HR_CHAT_ID</b> are not set in the <code>.env</code> file. Notifications will not be sent until both are configured in the environment.</p>
                        </div>
                    </div>
                @endif
            </section>

            {{-- Placeholder panes (future) --}}
            @foreach($tabs as $t)
                @if(! $t['ready'])
                    <section class="set-pane" data-pane="{{ $t['key'] }}">
                        <div class="placeholder">
                            <div class="pi">🛠️</div>
                            <h3>{{ $t['label'] }}</h3>
                            <p>This section is coming soon.</p>
                        </div>
                    </section>
                @endif
            @endforeach
        </div>
    </div>
</div>

{{-- Delete confirmation modal --}}
<div class="modal-ov" id="jcDelModal">
    <div class="modal-bx">
        <div class="mhead"><h3>Delete Category</h3><button type="button" class="mclose" data-close>&times;</button></div>
        <div class="mbody">
            <p style="font-size:13.5px;color:var(--text-2);line-height:1.5;">Are you sure you want to delete <b id="jcDelName" style="color:var(--text-1);"></b>? This action cannot be undone.</p>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-secondary" data-close>Cancel</button>
            <button type="button" class="btn btn-danger" style="width:auto;" id="jcDelConfirm">Delete</button>
        </div>
    </div>
</div>

{{-- Edit category modal --}}
<div class="modal-ov" id="jcModal">
    <div class="modal-bx">
        <div class="mhead"><h3>Edit Category</h3><button type="button" class="mclose" data-close>&times;</button></div>
        <div class="mbody">
            <div class="fld">
                <label for="jcEditName">Category Name</label>
                <input type="text" id="jcEditName" autocomplete="off">
                <div class="err" id="jcEditErr"></div>
            </div>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-secondary" data-close>Cancel</button>
            <button type="button" class="btn btn-primary" style="width:auto;" id="jcEditSave">Update</button>
        </div>
    </div>
</div>

{{-- Qualification delete confirmation modal --}}
<div class="modal-ov" id="qlDelModal">
    <div class="modal-bx">
        <div class="mhead"><h3>Delete Qualification</h3><button type="button" class="mclose" data-close>&times;</button></div>
        <div class="mbody">
            <p style="font-size:13.5px;color:var(--text-2);line-height:1.5;">Are you sure you want to delete <b id="qlDelName" style="color:var(--text-1);"></b>? This action cannot be undone.</p>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-secondary" data-close>Cancel</button>
            <button type="button" class="btn btn-danger" style="width:auto;" id="qlDelConfirm">Delete</button>
        </div>
    </div>
</div>

{{-- Qualification edit modal --}}
<div class="modal-ov" id="qlModal">
    <div class="modal-bx">
        <div class="mhead"><h3>Edit Qualification</h3><button type="button" class="mclose" data-close>&times;</button></div>
        <div class="mbody">
            <div class="fld">
                <label for="qlEditName">Qualification Name</label>
                <input type="text" id="qlEditName" autocomplete="off">
                <div class="err" id="qlEditErr"></div>
            </div>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-secondary" data-close>Cancel</button>
            <button type="button" class="btn btn-primary" style="width:auto;" id="qlEditSave">Update</button>
        </div>
    </div>
</div>

{{-- Department delete confirmation modal --}}
<div class="modal-ov" id="dpDelModal">
    <div class="modal-bx">
        <div class="mhead"><h3>Delete Department</h3><button type="button" class="mclose" data-close>&times;</button></div>
        <div class="mbody">
            <p style="font-size:13.5px;color:var(--text-2);line-height:1.5;">Are you sure you want to delete <b id="dpDelName" style="color:var(--text-1);"></b>? This action cannot be undone.</p>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-secondary" data-close>Cancel</button>
            <button type="button" class="btn btn-danger" style="width:auto;" id="dpDelConfirm">Delete</button>
        </div>
    </div>
</div>

{{-- Department edit modal --}}
<div class="modal-ov" id="dpModal">
    <div class="modal-bx">
        <div class="mhead"><h3>Edit Department</h3><button type="button" class="mclose" data-close>&times;</button></div>
        <div class="mbody">
            <div class="fld">
                <label for="dpEditName">Department Name</label>
                <input type="text" id="dpEditName" autocomplete="off">
                <div class="err" id="dpEditErr"></div>
            </div>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-secondary" data-close>Cancel</button>
            <button type="button" class="btn btn-primary" style="width:auto;" id="dpEditSave">Update</button>
        </div>
    </div>
</div>

{{-- Designation delete confirmation modal --}}
<div class="modal-ov" id="dsDelModal">
    <div class="modal-bx">
        <div class="mhead"><h3>Delete Designation</h3><button type="button" class="mclose" data-close>&times;</button></div>
        <div class="mbody">
            <p style="font-size:13.5px;color:var(--text-2);line-height:1.5;">Are you sure you want to delete <b id="dsDelName" style="color:var(--text-1);"></b>? This action cannot be undone.</p>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-secondary" data-close>Cancel</button>
            <button type="button" class="btn btn-danger" style="width:auto;" id="dsDelConfirm">Delete</button>
        </div>
    </div>
</div>

{{-- Designation edit modal --}}
<div class="modal-ov" id="dsModal">
    <div class="modal-bx">
        <div class="mhead"><h3>Edit Designation</h3><button type="button" class="mclose" data-close>&times;</button></div>
        <div class="mbody">
            <div class="fld">
                <label for="dsEditName">Designation Name</label>
                <input type="text" id="dsEditName" autocomplete="off">
                <div class="err" id="dsEditErr"></div>
            </div>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-secondary" data-close>Cancel</button>
            <button type="button" class="btn btn-primary" style="width:auto;" id="dsEditSave">Update</button>
        </div>
    </div>
</div>

{{-- Leave Setting delete confirmation modal --}}
<div class="modal-ov" id="lsDelModal">
    <div class="modal-bx">
        <div class="mhead"><h3>Delete Leave Setting</h3><button type="button" class="mclose" data-close>&times;</button></div>
        <div class="mbody">
            <p style="font-size:13.5px;color:var(--text-2);line-height:1.5;">Are you sure you want to delete <b id="lsDelName" style="color:var(--text-1);"></b>? This action cannot be undone.</p>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-secondary" data-close>Cancel</button>
            <button type="button" class="btn btn-danger" style="width:auto;" id="lsDelConfirm">Delete</button>
        </div>
    </div>
</div>

{{-- Leave Setting edit modal --}}
<div class="modal-ov" id="lsModal">
    <div class="modal-bx">
        <div class="mhead"><h3>Edit Leave Setting</h3><button type="button" class="mclose" data-close>&times;</button></div>
        <div class="mbody">
            <div class="fld">
                <label for="lsEditType">Leave Type</label>
                <input type="text" id="lsEditType" readonly>
            </div>
            <div class="fld">
                <label for="lsEditDays">No of Days</label>
                <input type="number" id="lsEditDays" min="0" autocomplete="off">
                <div class="err" id="lsEditErr"></div>
            </div>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-secondary" data-close>Cancel</button>
            <button type="button" class="btn btn-primary" style="width:auto;" id="lsEditSave">Update</button>
        </div>
    </div>
</div>

{{-- Allowance delete confirmation modal --}}
<div class="modal-ov" id="alDelModal">
    <div class="modal-bx">
        <div class="mhead"><h3>Delete Allowance</h3><button type="button" class="mclose" data-close>&times;</button></div>
        <div class="mbody">
            <p style="font-size:13.5px;color:var(--text-2);line-height:1.5;">Are you sure you want to delete <b id="alDelName" style="color:var(--text-1);"></b>? This action cannot be undone.</p>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-secondary" data-close>Cancel</button>
            <button type="button" class="btn btn-danger" style="width:auto;" id="alDelConfirm">Delete</button>
        </div>
    </div>
</div>

{{-- Telegram not-configured modal --}}
<div class="modal-ov" id="tgCfgModal">
    <div class="modal-bx">
        <div class="mhead"><h3>Telegram Not Configured</h3><button type="button" class="mclose" data-close>&times;</button></div>
        <div class="mbody">
            <p style="font-size:13.5px;color:var(--text-2);line-height:1.6;">
                <b>TELEGRAM_HR_BOT_TOKEN</b> and <b>TELEGRAM_HR_CHAT_ID</b> are not set in the <code style="font-family:monospace;font-size:12px;background:var(--bg-neutral);padding:1px 5px;border-radius:4px;">.env</code> file.
                Please configure them in the environment before enabling Telegram notifications.
            </p>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-primary" style="width:auto;" data-close>OK</button>
        </div>
    </div>
</div>

{{-- Allowance edit modal --}}
<div class="modal-ov" id="alModal">
    <div class="modal-bx">
        <div class="mhead"><h3>Edit Allowance</h3><button type="button" class="mclose" data-close>&times;</button></div>
        <div class="mbody">
            <div class="fld">
                <label for="alEditType">Allowance Type</label>
                <input type="text" id="alEditType" autocomplete="off">
            </div>
            <div class="fld">
                <label for="alEditPct">Percentage (%)</label>
                <input type="number" id="alEditPct" min="0" max="100" step="0.01" autocomplete="off">
                <div class="err" id="alEditErr"></div>
            </div>
        </div>
        <div class="mfoot">
            <button type="button" class="btn btn-secondary" data-close>Cancel</button>
            <button type="button" class="btn btn-primary" style="width:auto;" id="alEditSave">Update</button>
        </div>
    </div>
</div>
<div id="toast"></div>

<script src="{{ asset('assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('assets/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const base = "{{ url('hr/settings/job-categories') }}";

    function toast(msg, ok) {
        const t = document.getElementById('toast');
        t.textContent = msg; t.className = ok ? 'ok' : 'err'; t.style.display = 'block';
        setTimeout(() => { t.style.display = 'none'; }, 2400);
    }

    // ── Tab switching ──────────────────────────────────────────────
    document.querySelectorAll('.set-tab').forEach(btn => btn.addEventListener('click', function () {
        const key = this.dataset.tab;
        document.querySelectorAll('.set-tab').forEach(b => b.classList.toggle('active', b === this));
        document.querySelectorAll('.set-pane').forEach(p => p.classList.toggle('active', p.dataset.pane === key));
        // DataTables mis-measures columns while hidden — init/adjust when the tab becomes visible.
        if (key === 'qualifications') { initQualTable(); if (qlTable) qlTable.columns.adjust(); }
        if (key === 'departments')   { initDeptTable(); if (dpTable) dpTable.columns.adjust(); }
        if (key === 'designations')  { initDesigTable(); if (dsTable) dsTable.columns.adjust(); }
        if (key === 'leave-settings') { initLeaveTable(); if (lsTable) lsTable.columns.adjust(); refreshLeaveTypes(); }
        if (key === 'allowances')    { initAllowTable(); if (alTable) alTable.columns.adjust(); }
    }));

    // ── Job Category DataTable ─────────────────────────────────────
    const table = $('#jcTable').DataTable({
        processing: true,
        serverSide: true,
        pageLength: 10,
        lengthChange: true,
        pagingType: 'simple_numbers',
        order: [[0, 'asc']],
        language: { search: '', searchPlaceholder: 'Search categories...', lengthMenu: 'Rows per page: _MENU_' },
        ajax: { url: "{{ route('hr.settings.job-categories.data') }}" },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '60px' },
            { data: 'category_name', name: 'category_name' },
            { data: 'status_badge', name: 'status', orderable: false, searchable: false },
            { data: 'action', name: 'action', orderable: false, searchable: false, width: '90px' },
        ],
    });

    // ── Add category ───────────────────────────────────────────────
    const addBtn = document.getElementById('jcAddBtn');
    const nameInput = document.getElementById('jcName');
    const addErr = document.getElementById('jcErr');

    function addCategory() {
        const name = nameInput.value.trim();
        addErr.style.display = 'none';
        if (!name) { addErr.textContent = 'Please enter a category name.'; addErr.style.display = 'block'; return; }
        addBtn.disabled = true;
        $.ajax({
            url: base, type: 'POST', dataType: 'json',
            data: { _token: CSRF, category_name: name },
            success: (res) => {
                addBtn.disabled = false;
                if (!res.status) { addErr.textContent = res.msg || 'Could not add.'; addErr.style.display = 'block'; return; }
                nameInput.value = ''; toast(res.msg, true); table.ajax.reload(null, false);
            },
            error: (xhr) => {
                addBtn.disabled = false;
                addErr.textContent = xhr.responseJSON?.errors?.category_name?.[0] || xhr.responseJSON?.msg || 'Could not add.';
                addErr.style.display = 'block';
            },
        });
    }
    addBtn.addEventListener('click', addCategory);
    nameInput.addEventListener('keydown', e => { if (e.key === 'Enter') addCategory(); });

    // ── Edit category modal ────────────────────────────────────────
    const modal = document.getElementById('jcModal');
    const editName = document.getElementById('jcEditName');
    const editErr = document.getElementById('jcEditErr');
    let editId = null;

    function openEdit(id, name) {
        editId = id; editName.value = name; editErr.style.display = 'none';
        modal.classList.add('open'); setTimeout(() => editName.focus(), 60);
    }
    modal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => modal.classList.remove('open')));
    modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('open'); });

    $('#jcTable tbody').on('click', '.edit-jc', function () { openEdit($(this).data('id'), $(this).data('name')); });

    const editSaveBtn = document.getElementById('jcEditSave');
    editSaveBtn.addEventListener('click', function () {
        const name = editName.value.trim();
        editErr.style.display = 'none';
        if (!name) { editErr.textContent = 'Please enter a category name.'; editErr.style.display = 'block'; return; }
        editSaveBtn.disabled = true;
        $.ajax({
            url: base + '/' + editId, type: 'POST', dataType: 'json',
            data: { _token: CSRF, _method: 'PUT', category_name: name },
            success: (res) => {
                editSaveBtn.disabled = false;
                if (!res.status) { editErr.textContent = res.msg || 'Could not update.'; editErr.style.display = 'block'; return; }
                modal.classList.remove('open'); toast(res.msg, true); table.ajax.reload(null, false);
            },
            error: (xhr) => {
                editSaveBtn.disabled = false;
                editErr.textContent = xhr.responseJSON?.errors?.category_name?.[0] || xhr.responseJSON?.msg || 'Could not update.';
                editErr.style.display = 'block';
            },
        });
    });

    // ── Delete category (confirmation modal) ───────────────────────
    const delModal = document.getElementById('jcDelModal');
    const delName = document.getElementById('jcDelName');
    const delConfirmBtn = document.getElementById('jcDelConfirm');
    let delId = null;

    delModal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => delModal.classList.remove('open')));
    delModal.addEventListener('click', e => { if (e.target === delModal) delModal.classList.remove('open'); });

    $('#jcTable tbody').on('click', '.del-jc', function () {
        delId = $(this).data('id');
        delName.textContent = $(this).data('name') || 'this category';
        delModal.classList.add('open');
    });

    delConfirmBtn.addEventListener('click', function () {
        if (!delId) return;
        delConfirmBtn.disabled = true;
        $.ajax({
            url: base + '/' + delId, type: 'POST', dataType: 'json',
            data: { _token: CSRF, _method: 'DELETE' },
            success: (res) => {
                delConfirmBtn.disabled = false; delModal.classList.remove('open');
                toast(res.msg, res.status); table.ajax.reload(null, false);
            },
            error: () => { delConfirmBtn.disabled = false; delModal.classList.remove('open'); toast('Could not delete.', false); },
        });
    });

    // ══════════════ QUALIFICATIONS ═════════════════════════════════
    const qlBase = "{{ url('hr/settings/qualifications') }}";
    let qlTable = null;

    function initQualTable() {
        if (qlTable) return;
        qlTable = $('#qlTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            lengthChange: true,
            pagingType: 'simple_numbers',
            order: [[0, 'asc']],
            language: { search: '', searchPlaceholder: 'Search qualifications...', lengthMenu: 'Rows per page: _MENU_' },
            ajax: { url: "{{ route('hr.settings.qualifications.data') }}" },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '60px' },
                { data: 'qualification', name: 'qualification' },
                { data: 'creator', name: 'users.name', orderable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, width: '90px' },
            ],
        });
    }

    // ── Add qualification ──────────────────────────────────────────
    const qlAddBtn = document.getElementById('qlAddBtn');
    const qlName = document.getElementById('qlName');
    const qlErr = document.getElementById('qlErr');

    function addQualification() {
        const name = qlName.value.trim();
        qlErr.style.display = 'none';
        if (!name) { qlErr.textContent = 'Please enter a qualification name.'; qlErr.style.display = 'block'; return; }
        qlAddBtn.disabled = true;
        $.ajax({
            url: qlBase, type: 'POST', dataType: 'json',
            data: { _token: CSRF, qualification: name },
            success: (res) => {
                qlAddBtn.disabled = false;
                if (!res.status) { qlErr.textContent = res.msg || 'Could not add.'; qlErr.style.display = 'block'; return; }
                qlName.value = ''; toast(res.msg, true); if (qlTable) qlTable.ajax.reload(null, false);
            },
            error: (xhr) => {
                qlAddBtn.disabled = false;
                qlErr.textContent = xhr.responseJSON?.errors?.qualification?.[0] || xhr.responseJSON?.msg || 'Could not add.';
                qlErr.style.display = 'block';
            },
        });
    }
    qlAddBtn.addEventListener('click', addQualification);
    qlName.addEventListener('keydown', e => { if (e.key === 'Enter') addQualification(); });

    // ── Edit qualification ─────────────────────────────────────────
    const qlModal = document.getElementById('qlModal');
    const qlEditName = document.getElementById('qlEditName');
    const qlEditErr = document.getElementById('qlEditErr');
    const qlEditSave = document.getElementById('qlEditSave');
    let qlEditId = null;

    qlModal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => qlModal.classList.remove('open')));
    qlModal.addEventListener('click', e => { if (e.target === qlModal) qlModal.classList.remove('open'); });

    // Delegate from the table element — tbody doesn't exist until the table is lazy-initialized.
    $('#qlTable').on('click', '.edit-ql', function () {
        qlEditId = $(this).data('id'); qlEditName.value = $(this).data('name'); qlEditErr.style.display = 'none';
        qlModal.classList.add('open'); setTimeout(() => qlEditName.focus(), 60);
    });

    qlEditSave.addEventListener('click', function () {
        const name = qlEditName.value.trim();
        qlEditErr.style.display = 'none';
        if (!name) { qlEditErr.textContent = 'Please enter a qualification name.'; qlEditErr.style.display = 'block'; return; }
        qlEditSave.disabled = true;
        $.ajax({
            url: qlBase + '/' + qlEditId, type: 'POST', dataType: 'json',
            data: { _token: CSRF, _method: 'PUT', qualification: name },
            success: (res) => {
                qlEditSave.disabled = false;
                if (!res.status) { qlEditErr.textContent = res.msg || 'Could not update.'; qlEditErr.style.display = 'block'; return; }
                qlModal.classList.remove('open'); toast(res.msg, true); if (qlTable) qlTable.ajax.reload(null, false);
            },
            error: (xhr) => {
                qlEditSave.disabled = false;
                qlEditErr.textContent = xhr.responseJSON?.errors?.qualification?.[0] || xhr.responseJSON?.msg || 'Could not update.';
                qlEditErr.style.display = 'block';
            },
        });
    });

    // ── Delete qualification (confirmation modal) ──────────────────
    const qlDelModal = document.getElementById('qlDelModal');
    const qlDelName = document.getElementById('qlDelName');
    const qlDelConfirm = document.getElementById('qlDelConfirm');
    let qlDelId = null;

    qlDelModal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => qlDelModal.classList.remove('open')));
    qlDelModal.addEventListener('click', e => { if (e.target === qlDelModal) qlDelModal.classList.remove('open'); });

    $('#qlTable').on('click', '.del-ql', function () {
        qlDelId = $(this).data('id'); qlDelName.textContent = $(this).data('name') || 'this qualification';
        qlDelModal.classList.add('open');
    });

    qlDelConfirm.addEventListener('click', function () {
        if (!qlDelId) return;
        qlDelConfirm.disabled = true;
        $.ajax({
            url: qlBase + '/' + qlDelId, type: 'POST', dataType: 'json',
            data: { _token: CSRF, _method: 'DELETE' },
            success: (res) => {
                qlDelConfirm.disabled = false; qlDelModal.classList.remove('open');
                toast(res.msg, res.status); if (qlTable) qlTable.ajax.reload(null, false);
            },
            error: () => { qlDelConfirm.disabled = false; qlDelModal.classList.remove('open'); toast('Could not delete.', false); },
        });
    });

    // ══════════════ DEPARTMENTS ════════════════════════════════════
    const dpBase = "{{ url('hr/settings/departments') }}";
    let dpTable = null;

    function initDeptTable() {
        if (dpTable) return;
        dpTable = $('#dpTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            lengthChange: true,
            pagingType: 'simple_numbers',
            order: [[0, 'asc']],
            language: { search: '', searchPlaceholder: 'Search departments...', lengthMenu: 'Rows per page: _MENU_' },
            ajax: { url: "{{ route('hr.settings.departments.data') }}" },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '60px' },
                { data: 'department_name', name: 'department_name' },
                { data: 'creator', name: 'users.name', orderable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, width: '90px' },
            ],
        });
    }

    // ── Add department ─────────────────────────────────────────────
    const dpAddBtn = document.getElementById('dpAddBtn');
    const dpName = document.getElementById('dpName');
    const dpErr = document.getElementById('dpErr');

    function addDepartment() {
        const name = dpName.value.trim();
        dpErr.style.display = 'none';
        if (!name) { dpErr.textContent = 'Please enter a department name.'; dpErr.style.display = 'block'; return; }
        dpAddBtn.disabled = true;
        $.ajax({
            url: dpBase, type: 'POST', dataType: 'json',
            data: { _token: CSRF, department_name: name },
            success: (res) => {
                dpAddBtn.disabled = false;
                if (!res.status) { dpErr.textContent = res.msg || 'Could not add.'; dpErr.style.display = 'block'; return; }
                dpName.value = ''; toast(res.msg, true); if (dpTable) dpTable.ajax.reload(null, false);
            },
            error: (xhr) => {
                dpAddBtn.disabled = false;
                dpErr.textContent = xhr.responseJSON?.errors?.department_name?.[0] || xhr.responseJSON?.msg || 'Could not add.';
                dpErr.style.display = 'block';
            },
        });
    }
    dpAddBtn.addEventListener('click', addDepartment);
    dpName.addEventListener('keydown', e => { if (e.key === 'Enter') addDepartment(); });

    // ── Edit department ────────────────────────────────────────────
    const dpModal = document.getElementById('dpModal');
    const dpEditName = document.getElementById('dpEditName');
    const dpEditErr = document.getElementById('dpEditErr');
    const dpEditSave = document.getElementById('dpEditSave');
    let dpEditId = null;

    dpModal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => dpModal.classList.remove('open')));
    dpModal.addEventListener('click', e => { if (e.target === dpModal) dpModal.classList.remove('open'); });

    // Delegate from the table element — tbody doesn't exist until the table is lazy-initialized.
    $('#dpTable').on('click', '.edit-dp', function () {
        dpEditId = $(this).data('id'); dpEditName.value = $(this).data('name'); dpEditErr.style.display = 'none';
        dpModal.classList.add('open'); setTimeout(() => dpEditName.focus(), 60);
    });

    dpEditSave.addEventListener('click', function () {
        const name = dpEditName.value.trim();
        dpEditErr.style.display = 'none';
        if (!name) { dpEditErr.textContent = 'Please enter a department name.'; dpEditErr.style.display = 'block'; return; }
        dpEditSave.disabled = true;
        $.ajax({
            url: dpBase + '/' + dpEditId, type: 'POST', dataType: 'json',
            data: { _token: CSRF, _method: 'PUT', department_name: name },
            success: (res) => {
                dpEditSave.disabled = false;
                if (!res.status) { dpEditErr.textContent = res.msg || 'Could not update.'; dpEditErr.style.display = 'block'; return; }
                dpModal.classList.remove('open'); toast(res.msg, true); if (dpTable) dpTable.ajax.reload(null, false);
            },
            error: (xhr) => {
                dpEditSave.disabled = false;
                dpEditErr.textContent = xhr.responseJSON?.errors?.department_name?.[0] || xhr.responseJSON?.msg || 'Could not update.';
                dpEditErr.style.display = 'block';
            },
        });
    });

    // ── Delete department (confirmation modal) ─────────────────────
    const dpDelModal = document.getElementById('dpDelModal');
    const dpDelName = document.getElementById('dpDelName');
    const dpDelConfirm = document.getElementById('dpDelConfirm');
    let dpDelId = null;

    dpDelModal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => dpDelModal.classList.remove('open')));
    dpDelModal.addEventListener('click', e => { if (e.target === dpDelModal) dpDelModal.classList.remove('open'); });

    $('#dpTable').on('click', '.del-dp', function () {
        dpDelId = $(this).data('id'); dpDelName.textContent = $(this).data('name') || 'this department';
        dpDelModal.classList.add('open');
    });

    dpDelConfirm.addEventListener('click', function () {
        if (!dpDelId) return;
        dpDelConfirm.disabled = true;
        $.ajax({
            url: dpBase + '/' + dpDelId, type: 'POST', dataType: 'json',
            data: { _token: CSRF, _method: 'DELETE' },
            success: (res) => {
                dpDelConfirm.disabled = false; dpDelModal.classList.remove('open');
                toast(res.msg, res.status); if (dpTable) dpTable.ajax.reload(null, false);
            },
            error: () => { dpDelConfirm.disabled = false; dpDelModal.classList.remove('open'); toast('Could not delete.', false); },
        });
    });

    // ══════════════ DESIGNATIONS ═══════════════════════════════════
    const dsBase = "{{ url('hr/settings/designations') }}";
    let dsTable = null;

    function initDesigTable() {
        if (dsTable) return;
        dsTable = $('#dsTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            lengthChange: true,
            pagingType: 'simple_numbers',
            order: [[0, 'asc']],
            language: { search: '', searchPlaceholder: 'Search designations...', lengthMenu: 'Rows per page: _MENU_' },
            ajax: { url: "{{ route('hr.settings.designations.data') }}" },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '60px' },
                { data: 'designation_name', name: 'designation_name' },
                { data: 'creator', name: 'users.name', orderable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, width: '90px' },
            ],
        });
    }

    // ── Add designation ────────────────────────────────────────────
    const dsAddBtn = document.getElementById('dsAddBtn');
    const dsName = document.getElementById('dsName');
    const dsErr = document.getElementById('dsErr');

    function addDesignation() {
        const name = dsName.value.trim();
        dsErr.style.display = 'none';
        if (!name) { dsErr.textContent = 'Please enter a designation name.'; dsErr.style.display = 'block'; return; }
        dsAddBtn.disabled = true;
        $.ajax({
            url: dsBase, type: 'POST', dataType: 'json',
            data: { _token: CSRF, designation_name: name },
            success: (res) => {
                dsAddBtn.disabled = false;
                if (!res.status) { dsErr.textContent = res.msg || 'Could not add.'; dsErr.style.display = 'block'; return; }
                dsName.value = ''; toast(res.msg, true); if (dsTable) dsTable.ajax.reload(null, false);
            },
            error: (xhr) => {
                dsAddBtn.disabled = false;
                dsErr.textContent = xhr.responseJSON?.errors?.designation_name?.[0] || xhr.responseJSON?.msg || 'Could not add.';
                dsErr.style.display = 'block';
            },
        });
    }
    dsAddBtn.addEventListener('click', addDesignation);
    dsName.addEventListener('keydown', e => { if (e.key === 'Enter') addDesignation(); });

    // ── Edit designation ───────────────────────────────────────────
    const dsModal = document.getElementById('dsModal');
    const dsEditName = document.getElementById('dsEditName');
    const dsEditErr = document.getElementById('dsEditErr');
    const dsEditSave = document.getElementById('dsEditSave');
    let dsEditId = null;

    dsModal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => dsModal.classList.remove('open')));
    dsModal.addEventListener('click', e => { if (e.target === dsModal) dsModal.classList.remove('open'); });

    // Delegate from the table element — tbody doesn't exist until the table is lazy-initialized.
    $('#dsTable').on('click', '.edit-ds', function () {
        dsEditId = $(this).data('id'); dsEditName.value = $(this).data('name'); dsEditErr.style.display = 'none';
        dsModal.classList.add('open'); setTimeout(() => dsEditName.focus(), 60);
    });

    dsEditSave.addEventListener('click', function () {
        const name = dsEditName.value.trim();
        dsEditErr.style.display = 'none';
        if (!name) { dsEditErr.textContent = 'Please enter a designation name.'; dsEditErr.style.display = 'block'; return; }
        dsEditSave.disabled = true;
        $.ajax({
            url: dsBase + '/' + dsEditId, type: 'POST', dataType: 'json',
            data: { _token: CSRF, _method: 'PUT', designation_name: name },
            success: (res) => {
                dsEditSave.disabled = false;
                if (!res.status) { dsEditErr.textContent = res.msg || 'Could not update.'; dsEditErr.style.display = 'block'; return; }
                dsModal.classList.remove('open'); toast(res.msg, true); if (dsTable) dsTable.ajax.reload(null, false);
            },
            error: (xhr) => {
                dsEditSave.disabled = false;
                dsEditErr.textContent = xhr.responseJSON?.errors?.designation_name?.[0] || xhr.responseJSON?.msg || 'Could not update.';
                dsEditErr.style.display = 'block';
            },
        });
    });

    // ── Delete designation (confirmation modal) ────────────────────
    const dsDelModal = document.getElementById('dsDelModal');
    const dsDelName = document.getElementById('dsDelName');
    const dsDelConfirm = document.getElementById('dsDelConfirm');
    let dsDelId = null;

    dsDelModal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => dsDelModal.classList.remove('open')));
    dsDelModal.addEventListener('click', e => { if (e.target === dsDelModal) dsDelModal.classList.remove('open'); });

    $('#dsTable').on('click', '.del-ds', function () {
        dsDelId = $(this).data('id'); dsDelName.textContent = $(this).data('name') || 'this designation';
        dsDelModal.classList.add('open');
    });

    dsDelConfirm.addEventListener('click', function () {
        if (!dsDelId) return;
        dsDelConfirm.disabled = true;
        $.ajax({
            url: dsBase + '/' + dsDelId, type: 'POST', dataType: 'json',
            data: { _token: CSRF, _method: 'DELETE' },
            success: (res) => {
                dsDelConfirm.disabled = false; dsDelModal.classList.remove('open');
                toast(res.msg, res.status); if (dsTable) dsTable.ajax.reload(null, false);
            },
            error: () => { dsDelConfirm.disabled = false; dsDelModal.classList.remove('open'); toast('Could not delete.', false); },
        });
    });

    // ══════════════ LEAVE SETTINGS ═════════════════════════════════
    const lsBase = "{{ url('hr/settings/leave-settings') }}";
    let lsTable = null;

    function initLeaveTable() {
        if (lsTable) return;
        lsTable = $('#lsTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            lengthChange: true,
            pagingType: 'simple_numbers',
            order: [[0, 'asc']],
            language: { search: '', searchPlaceholder: 'Search leave types...', lengthMenu: 'Rows per page: _MENU_' },
            ajax: { url: "{{ route('hr.settings.leave-settings.data') }}" },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '60px' },
                { data: 'leave_type', name: 'leave_type' },
                { data: 'no_of_days', name: 'no_of_days', width: '110px' },
                { data: 'creator', name: 'users.name', orderable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, width: '90px' },
            ],
        });
    }

    // Rebuild the leave-type dropdown from the still-available types.
    const lsType = document.getElementById('lsType');
    const lsAddBtn = document.getElementById('lsAddBtn');
    const lsAllNote = document.getElementById('lsAllNote');
    function refreshLeaveTypes() {
        $.getJSON("{{ route('hr.settings.leave-settings.types') }}", (res) => {
            if (!res.status) return;
            lsType.innerHTML = '<option value="">Select leave type</option>'
                + res.available.map(t => `<option value="${t}">${t}</option>`).join('');
            const allUsed = res.available.length === 0;
            lsType.disabled = allUsed;
            lsAddBtn.disabled = allUsed;
            lsAllNote.style.display = allUsed ? 'block' : 'none';
        });
    }

    // ── Add leave setting ──────────────────────────────────────────
    const lsDays = document.getElementById('lsDays');
    const lsErr = document.getElementById('lsErr');

    function addLeaveSetting() {
        const type = lsType.value, days = lsDays.value.trim();
        lsErr.style.display = 'none';
        if (!type) { lsErr.textContent = 'Please select a leave type.'; lsErr.style.display = 'block'; return; }
        if (days === '' || parseInt(days) < 0) { lsErr.textContent = 'Please enter the number of days.'; lsErr.style.display = 'block'; return; }
        lsAddBtn.disabled = true;
        $.ajax({
            url: lsBase, type: 'POST', dataType: 'json',
            data: { _token: CSRF, leave_type: type, no_of_days: days },
            success: (res) => {
                lsAddBtn.disabled = false;
                if (!res.status) { lsErr.textContent = res.msg || 'Could not add.'; lsErr.style.display = 'block'; return; }
                lsType.value = ''; lsDays.value = ''; toast(res.msg, true);
                if (lsTable) lsTable.ajax.reload(null, false); refreshLeaveTypes();
            },
            error: (xhr) => {
                lsAddBtn.disabled = false;
                lsErr.textContent = xhr.responseJSON?.errors?.leave_type?.[0] || xhr.responseJSON?.errors?.no_of_days?.[0] || xhr.responseJSON?.msg || 'Could not add.';
                lsErr.style.display = 'block';
            },
        });
    }
    lsAddBtn.addEventListener('click', addLeaveSetting);
    lsDays.addEventListener('keydown', e => { if (e.key === 'Enter') addLeaveSetting(); });

    // ── Edit leave setting (days only; type is fixed) ──────────────
    const lsModal = document.getElementById('lsModal');
    const lsEditType = document.getElementById('lsEditType');
    const lsEditDays = document.getElementById('lsEditDays');
    const lsEditErr = document.getElementById('lsEditErr');
    const lsEditSave = document.getElementById('lsEditSave');
    let lsEditId = null;

    lsModal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => lsModal.classList.remove('open')));
    lsModal.addEventListener('click', e => { if (e.target === lsModal) lsModal.classList.remove('open'); });

    $('#lsTable').on('click', '.edit-ls', function () {
        lsEditId = $(this).data('id'); lsEditType.value = $(this).data('type'); lsEditDays.value = $(this).data('days');
        lsEditErr.style.display = 'none'; lsModal.classList.add('open'); setTimeout(() => lsEditDays.focus(), 60);
    });

    lsEditSave.addEventListener('click', function () {
        const days = lsEditDays.value.trim();
        lsEditErr.style.display = 'none';
        if (days === '' || parseInt(days) < 0) { lsEditErr.textContent = 'Please enter the number of days.'; lsEditErr.style.display = 'block'; return; }
        lsEditSave.disabled = true;
        $.ajax({
            url: lsBase + '/' + lsEditId, type: 'POST', dataType: 'json',
            data: { _token: CSRF, _method: 'PUT', no_of_days: days },
            success: (res) => {
                lsEditSave.disabled = false;
                if (!res.status) { lsEditErr.textContent = res.msg || 'Could not update.'; lsEditErr.style.display = 'block'; return; }
                lsModal.classList.remove('open'); toast(res.msg, true); if (lsTable) lsTable.ajax.reload(null, false);
            },
            error: (xhr) => {
                lsEditSave.disabled = false;
                lsEditErr.textContent = xhr.responseJSON?.errors?.no_of_days?.[0] || xhr.responseJSON?.msg || 'Could not update.';
                lsEditErr.style.display = 'block';
            },
        });
    });

    // ── Delete leave setting (confirmation modal) ──────────────────
    const lsDelModal = document.getElementById('lsDelModal');
    const lsDelName = document.getElementById('lsDelName');
    const lsDelConfirm = document.getElementById('lsDelConfirm');
    let lsDelId = null;

    lsDelModal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => lsDelModal.classList.remove('open')));
    lsDelModal.addEventListener('click', e => { if (e.target === lsDelModal) lsDelModal.classList.remove('open'); });

    $('#lsTable').on('click', '.del-ls', function () {
        lsDelId = $(this).data('id'); lsDelName.textContent = $(this).data('name') || 'this leave setting';
        lsDelModal.classList.add('open');
    });

    lsDelConfirm.addEventListener('click', function () {
        if (!lsDelId) return;
        lsDelConfirm.disabled = true;
        $.ajax({
            url: lsBase + '/' + lsDelId, type: 'POST', dataType: 'json',
            data: { _token: CSRF, _method: 'DELETE' },
            success: (res) => {
                lsDelConfirm.disabled = false; lsDelModal.classList.remove('open');
                toast(res.msg, res.status);
                if (lsTable) lsTable.ajax.reload(null, false); refreshLeaveTypes();
            },
            error: () => { lsDelConfirm.disabled = false; lsDelModal.classList.remove('open'); toast('Could not delete.', false); },
        });
    });

    // ══════════════ ALLOWANCES ═════════════════════════════════════
    const alBase = "{{ url('hr/settings/allowances') }}";
    let alTable = null;

    function initAllowTable() {
        if (alTable) return;
        alTable = $('#alTable').DataTable({
            processing: true,
            serverSide: true,
            pageLength: 10,
            lengthChange: true,
            pagingType: 'simple_numbers',
            order: [[0, 'asc']],
            language: { search: '', searchPlaceholder: 'Search allowance types...', lengthMenu: 'Rows per page: _MENU_' },
            ajax: { url: "{{ route('hr.settings.allowances.data') }}" },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '60px' },
                { data: 'allowance_type', name: 'allowance_type' },
                { data: 'percentage', name: 'percentage', width: '130px' },
                { data: 'creator', name: 'users.name', orderable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false, width: '90px' },
            ],
        });
    }

    // ── Add allowance ──────────────────────────────────────────────
    const alType = document.getElementById('alType');
    const alPct = document.getElementById('alPct');
    const alErr = document.getElementById('alErr');
    const alAddBtn = document.getElementById('alAddBtn');

    function addAllowance() {
        const type = alType.value.trim(), pct = alPct.value.trim();
        alErr.style.display = 'none';
        if (!type) { alErr.textContent = 'Please enter an allowance type.'; alErr.style.display = 'block'; return; }
        if (pct === '' || parseFloat(pct) < 0 || parseFloat(pct) > 100) { alErr.textContent = 'Please enter a percentage between 0 and 100.'; alErr.style.display = 'block'; return; }
        alAddBtn.disabled = true;
        $.ajax({
            url: alBase, type: 'POST', dataType: 'json',
            data: { _token: CSRF, allowance_type: type, percentage: pct },
            success: (res) => {
                alAddBtn.disabled = false;
                if (!res.status) { alErr.textContent = res.msg || 'Could not add.'; alErr.style.display = 'block'; return; }
                alType.value = ''; alPct.value = ''; toast(res.msg, true); if (alTable) alTable.ajax.reload(null, false);
            },
            error: (xhr) => {
                alAddBtn.disabled = false;
                alErr.textContent = xhr.responseJSON?.errors?.allowance_type?.[0] || xhr.responseJSON?.errors?.percentage?.[0] || xhr.responseJSON?.msg || 'Could not add.';
                alErr.style.display = 'block';
            },
        });
    }
    alAddBtn.addEventListener('click', addAllowance);
    alPct.addEventListener('keydown', e => { if (e.key === 'Enter') addAllowance(); });

    // ── Edit allowance ─────────────────────────────────────────────
    const alModal = document.getElementById('alModal');
    const alEditType = document.getElementById('alEditType');
    const alEditPct = document.getElementById('alEditPct');
    const alEditErr = document.getElementById('alEditErr');
    const alEditSave = document.getElementById('alEditSave');
    let alEditId = null;

    alModal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => alModal.classList.remove('open')));
    alModal.addEventListener('click', e => { if (e.target === alModal) alModal.classList.remove('open'); });

    $('#alTable').on('click', '.edit-al', function () {
        alEditId = $(this).data('id'); alEditType.value = $(this).data('type'); alEditPct.value = $(this).data('pct');
        alEditErr.style.display = 'none'; alModal.classList.add('open'); setTimeout(() => alEditType.focus(), 60);
    });

    alEditSave.addEventListener('click', function () {
        const type = alEditType.value.trim(), pct = alEditPct.value.trim();
        alEditErr.style.display = 'none';
        if (!type) { alEditErr.textContent = 'Please enter an allowance type.'; alEditErr.style.display = 'block'; return; }
        if (pct === '' || parseFloat(pct) < 0 || parseFloat(pct) > 100) { alEditErr.textContent = 'Please enter a percentage between 0 and 100.'; alEditErr.style.display = 'block'; return; }
        alEditSave.disabled = true;
        $.ajax({
            url: alBase + '/' + alEditId, type: 'POST', dataType: 'json',
            data: { _token: CSRF, _method: 'PUT', allowance_type: type, percentage: pct },
            success: (res) => {
                alEditSave.disabled = false;
                if (!res.status) { alEditErr.textContent = res.msg || 'Could not update.'; alEditErr.style.display = 'block'; return; }
                alModal.classList.remove('open'); toast(res.msg, true); if (alTable) alTable.ajax.reload(null, false);
            },
            error: (xhr) => {
                alEditSave.disabled = false;
                alEditErr.textContent = xhr.responseJSON?.errors?.allowance_type?.[0] || xhr.responseJSON?.errors?.percentage?.[0] || xhr.responseJSON?.msg || 'Could not update.';
                alEditErr.style.display = 'block';
            },
        });
    });

    // ── Delete allowance (confirmation modal) ──────────────────────
    const alDelModal = document.getElementById('alDelModal');
    const alDelName = document.getElementById('alDelName');
    const alDelConfirm = document.getElementById('alDelConfirm');
    let alDelId = null;

    alDelModal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => alDelModal.classList.remove('open')));
    alDelModal.addEventListener('click', e => { if (e.target === alDelModal) alDelModal.classList.remove('open'); });

    $('#alTable').on('click', '.del-al', function () {
        alDelId = $(this).data('id'); alDelName.textContent = $(this).data('name') || 'this allowance';
        alDelModal.classList.add('open');
    });

    alDelConfirm.addEventListener('click', function () {
        if (!alDelId) return;
        alDelConfirm.disabled = true;
        $.ajax({
            url: alBase + '/' + alDelId, type: 'POST', dataType: 'json',
            data: { _token: CSRF, _method: 'DELETE' },
            success: (res) => {
                alDelConfirm.disabled = false; alDelModal.classList.remove('open');
                toast(res.msg, res.status); if (alTable) alTable.ajax.reload(null, false);
            },
            error: () => { alDelConfirm.disabled = false; alDelModal.classList.remove('open'); toast('Could not delete.', false); },
        });
    });

    // ══════════════ TELEGRAM NOTIFICATIONS ═════════════════════════
    const tgCfgModal = document.getElementById('tgCfgModal');
    tgCfgModal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => tgCfgModal.classList.remove('open')));
    tgCfgModal.addEventListener('click', e => { if (e.target === tgCfgModal) tgCfgModal.classList.remove('open'); });

    document.querySelectorAll('.tg-toggle').forEach(sw => sw.addEventListener('change', function () {
        const enabled = this.checked;
        this.disabled = true;
        $.ajax({
            url: "{{ route('hr.settings.notifications.toggle') }}",
            type: 'POST', dataType: 'json',
            data: { _token: CSRF, setting_key: this.dataset.key, enabled: enabled ? 1 : 0 },
            success: (res) => {
                this.disabled = false;
                if (!res.status) { this.checked = !enabled; toast(res.msg || 'Could not update.', false); return; }
                toast(res.enabled ? 'Notification enabled.' : 'Notification disabled.', true);
            },
            error: (xhr) => {
                this.disabled = false;
                this.checked = !enabled; // keep it OFF (revert)
                // Telegram env not configured → show the modal instead of a toast.
                if (xhr.responseJSON?.configured === false) {
                    tgCfgModal.classList.add('open');
                } else {
                    toast(xhr.responseJSON?.msg || 'Could not update.', false);
                }
            },
        });
    }));
})();
</script>
</x-layouts.app>
