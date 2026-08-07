<x-layouts.app title="HR Staff Section">
@push('styles')
<style>
    .hr-wrap { padding: 24px 28px 48px; width: 100%; }
    .hr-head { margin-bottom: 20px; }
    .hr-eyebrow { font-size:11px; font-weight:600; letter-spacing:.09em; text-transform:uppercase; color:var(--brand-red); }
    .hr-head h1 { font-size:24px; font-weight:600; letter-spacing:-.5px; color:var(--text-1); margin-top:4px; }
    .hr-head p { font-size:13px; color:var(--text-2); margin-top:4px; }

    .set-grid { display:grid; grid-template-columns:230px 1fr; gap:20px; align-items:start; }
    @media(max-width:820px){ .set-grid{ grid-template-columns:1fr; } }

    /* Vertical tab rail */
    .set-tabs { display:flex; flex-direction:column; gap:2px; background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:8px; }
    .set-tab { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:var(--radius-sm); font-size:13px; font-weight:500; color:var(--text-2); background:none; border:none; font-family:inherit; text-align:left; cursor:pointer; width:100%; }
    .set-tab svg { width:16px; height:16px; stroke:currentColor; fill:none; stroke-width:1.9; flex-shrink:0; }
    .set-tab:hover { background:var(--bg-page); color:var(--text-1); }
    .set-tab.active { background:var(--brand-red-soft); color:var(--brand-red-dark); font-weight:600; }
    .set-tab .soon { margin-left:auto; font-size:9.5px; font-weight:600; letter-spacing:.03em; color:var(--text-3); background:var(--bg-neutral); border:1px solid var(--border); padding:1px 6px; border-radius:var(--radius-pill); }

    .set-pane { display:none; }
    .set-pane.active { display:block; }

    .placeholder { text-align:center; color:var(--text-3); padding:60px 20px; background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); }
    .placeholder .pi { font-size:34px; margin-bottom:10px; }
    .placeholder h3 { font-size:15px; font-weight:600; color:var(--text-2); }

    {{-- Dashboard styles --}}
    .sd-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:20px; }
    @media(max-width:1100px){ .sd-grid{ grid-template-columns:repeat(2,1fr);} }
    @media(max-width:560px){ .sd-grid{ grid-template-columns:1fr;} }
    .sd-card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:20px; }
    .sd-head { display:flex; align-items:center; gap:10px; margin-bottom:14px; }
    .sd-ico { width:40px; height:40px; flex-shrink:0; display:flex; align-items:center; justify-content:center; border-radius:var(--radius-md); }
    .sd-ico svg { width:19px; height:19px; stroke-width:2; fill:none; }
    .sd-ico.blue { background:#EFF6FF; } .sd-ico.blue svg { stroke:#2563EB; }
    .sd-ico.green { background:#ECFDF5; } .sd-ico.green svg { stroke:#059669; }
    .sd-ico.amber { background:#FFF7ED; } .sd-ico.amber svg { stroke:#EA580C; }
    .sd-ico.red { background:var(--brand-red-soft); } .sd-ico.red svg { stroke:var(--brand-red); }
    .sd-figure { font-size:28px; font-weight:700; color:var(--text-1); letter-spacing:-.02em; line-height:1; padding-left:10px;}
    .sd-figure .sd-sub { font-size:18px; font-weight:600; color:var(--text-3); }
    .sd-label { font-size:13px; font-weight:500; color:var(--text-2); }
    .sd-label.link { color:var(--brand-red-dark); }

    .sd-actions { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:20px; }
    .sd-actions h2 { font-size:16px; font-weight:600; color:var(--text-1); margin-bottom:16px; }
    .sd-btns { display:flex; gap:10px; flex-wrap:wrap; }
    .qa-btn { display:inline-flex; align-items:center; gap:7px; padding:10px 16px; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:500; cursor:pointer; border:1px solid var(--border); background:var(--bg-card); color:var(--text-1); text-decoration:none; }
    .qa-btn svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2; }
    .qa-btn:hover { border-color:var(--text-3); }
    .qa-btn.primary { background:var(--brand-red); color:#fff; border-color:var(--brand-red); }
    .qa-btn.primary:hover { background:var(--brand-red-dark); border-color:var(--brand-red-dark); }

    .btn { display:inline-flex; align-items:center; gap:7px; padding:9px 15px; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:500; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
    .btn-primary { background:var(--brand-red); color:#fff; } .btn-primary:hover { background:var(--brand-red-dark); }
    .btn-secondary { background:var(--bg-card); color:var(--text-1); border-color:var(--border); } .btn-secondary:hover { border-color:var(--text-3); }


</style>
@endpush

@php
    $tabs = [
        ['key' => 'dashboard', 'label' => 'Dashboard', 'ready' => true, 'icon' => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>'],
        ['key' => 'attendance', 'label' => 'Attendance', 'ready' => true, 'icon' => '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>'],
        ['key' => 'leave-management', 'label' => 'Leave Management', 'ready' => true, 'icon' => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/>'],
        ['key' => 'profile', 'label' => 'Profile', 'ready' => true, 'icon' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>'],
    ];

    // Active tab: explicit ?tab, else the tab whose form was just submitted.
    $validTabs = ['dashboard', 'attendance', 'leave-management', 'profile'];
    $leaveTouched   = ($errors->any() || session('leave_error') || session('leave_success'));
    $profileTouched = (session('profile_error') || session('profile_success'));
    $activeTab = in_array(request('tab'), $validTabs, true)
        ? request('tab')
        : ($profileTouched ? 'profile' : ($leaveTouched ? 'leave-management' : 'dashboard'));
@endphp

<div class="hr-wrap">
    <div class="hr-head">
        <div class="hr-eyebrow">HR Management</div>
        <h1>Staff Section</h1>
        <p>Your personal attendance, leave and activity overview</p>
    </div>

    <div class="set-grid">
        <nav class="set-tabs" id="staffTabs">
            @foreach($tabs as $t)
                <button type="button" class="set-tab {{ $activeTab === $t['key'] ? 'active' : '' }}" data-tab="{{ $t['key'] }}">
                    <svg viewBox="0 0 24 24">{!! $t['icon'] !!}</svg>
                    <span>{{ $t['label'] }}</span>
                    @unless($t['ready'])<span class="soon">SOON</span>@endunless
                </button>
            @endforeach
        </nav>

        <div class="set-content">
            <section class="set-pane {{ $activeTab === 'dashboard' ? 'active' : '' }}" data-pane="dashboard">
                @include('Hr.staff.dashboard', ['stats' => $stats, 'charts' => $charts])
            </section>
            <section class="set-pane {{ $activeTab === 'attendance' ? 'active' : '' }}" data-pane="attendance">
                @include('Hr.staff.attendance')
            </section>
            <section class="set-pane {{ $activeTab === 'leave-management' ? 'active' : '' }}" data-pane="leave-management">
                @include('Hr.staff.leave-management', ['leave' => $leave, 'employeeLinked' => $employeeLinked])
            </section>
            <section class="set-pane {{ $activeTab === 'profile' ? 'active' : '' }}" data-pane="profile">
                @include('Hr.staff.profile', ['profile' => $profile])
            </section>
        </div>
    </div>
</div>

<script>
(function () {
    function activateTab(key) {
        document.querySelectorAll('.set-tab').forEach(b => b.classList.toggle('active', b.dataset.tab === key));
        document.querySelectorAll('.set-pane').forEach(p => p.classList.toggle('active', p.dataset.pane === key));
        if (key === 'attendance' && window.initStaffAttendance) window.initStaffAttendance();
        if (key === 'leave-management' && window.initStaffLeave) window.initStaffLeave();
    }

    // Sidebar/rail tabs.
    document.querySelectorAll('.set-tab').forEach(btn => btn.addEventListener('click', () => activateTab(btn.dataset.tab)));

    // Dashboard Quick Actions → jump to a tab.
    document.querySelectorAll('[data-goto-tab]').forEach(btn => btn.addEventListener('click', () => activateTab(btn.dataset.gotoTab)));
})();
</script>
</x-layouts.app>
