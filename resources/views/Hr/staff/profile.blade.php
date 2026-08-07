{{-- HR › Staff Section › Profile --}}
<style>
    .pf-card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); overflow:hidden; margin-bottom:18px; }
    .pf-banner { height:74px; background:var(--brand-red-soft); }
    .pf-top { display:flex; align-items:flex-start; gap:18px; padding:0 24px 18px; margin-top:-40px; flex-wrap:wrap; }
    .pf-avatar { width:84px; height:84px; border-radius:50%; background:var(--brand-red); color:#fff; display:flex; align-items:center; justify-content:center; font-size:28px; font-weight:700; flex-shrink:0; border:4px solid var(--bg-card); object-fit:cover; }
    .pf-avatar svg { width:40px; height:40px; }
    .pf-id { flex:1; min-width:200px; padding-top:46px; }
    .pf-id h2 { font-size:20px; font-weight:700; color:var(--text-1); }
    .pf-role { display:inline-flex; align-items:center; gap:6px; font-size:12.5px; color:var(--text-2); margin-top:3px; }
    .pf-role svg { width:14px; height:14px; stroke:var(--text-3); fill:none; stroke-width:2; }
    .pf-changepw { align-self:center; margin-top:46px; }

    .pf-btn { display:inline-flex; align-items:center; gap:7px; padding:9px 15px; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:600; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .pf-btn svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2; }
    .pf-btn.secondary { background:var(--bg-card); color:var(--text-1); border-color:var(--border); } .pf-btn.secondary:hover { border-color:var(--text-3); }
    .pf-btn.primary { background:var(--brand-red); color:#fff; } .pf-btn.primary:hover { background:var(--brand-red-dark); }

    .pf-flash { padding:10px 14px; border-radius:var(--radius-sm); font-size:13px; margin:0 24px 16px; }
    .pf-flash.ok { background:var(--success-soft); color:var(--success-text); border:1px solid var(--success-border); }
    .pf-flash.err { background:var(--danger-soft); color:var(--danger-text); border:1px solid var(--danger-border); }

    /* Quick-fact tiles under the header */
    .pf-facts { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; padding:0 24px 22px; }
    @media(max-width:900px){ .pf-facts{ grid-template-columns:repeat(2,1fr);} }
    @media(max-width:520px){ .pf-facts{ grid-template-columns:1fr;} }
    .pf-fact { border:1px solid var(--border-soft); border-radius:var(--radius-md); padding:14px; text-align:center; }
    .pf-fact.blue { background:#EFF6FF; } .pf-fact.green { background:#ECFDF5; } .pf-fact.purple { background:#F5F3FF; } .pf-fact.amber { background:#FFF7ED; }
    .pf-fact .fi { width:26px; height:26px; margin:0 auto 6px; display:flex; align-items:center; justify-content:center; }
    .pf-fact .fi svg { width:17px; height:17px; fill:none; stroke-width:2; }
    .pf-fact.blue svg { stroke:#2563EB; } .pf-fact.green svg { stroke:#059669; } .pf-fact.purple svg { stroke:#7C3AED; } .pf-fact.amber svg { stroke:#EA580C; }
    .pf-fact .fl { font-size:11px; color:var(--text-2); text-transform:uppercase; letter-spacing:.04em; }
    .pf-fact .fv { font-size:13.5px; font-weight:600; color:var(--text-1); margin-top:3px; }

    /* Info sections */
    .pf-sec { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:20px 22px; margin-bottom:18px; }
    .pf-sec-head { display:flex; align-items:center; gap:10px; margin-bottom:16px; }
    .pf-sec-ico { width:32px; height:32px; border-radius:var(--radius-md); display:flex; align-items:center; justify-content:center; }
    .pf-sec-ico.blue { background:#EFF6FF; } .pf-sec-ico.blue svg { stroke:#2563EB; }
    .pf-sec-ico.purple { background:#F5F3FF; } .pf-sec-ico.purple svg { stroke:#7C3AED; }
    .pf-sec-ico svg { width:17px; height:17px; fill:none; stroke-width:2; }
    .pf-sec-head h3 { font-size:16px; font-weight:700; color:var(--text-1); }

    .pf-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }
    @media(max-width:900px){ .pf-grid{ grid-template-columns:repeat(2,1fr);} }
    @media(max-width:560px){ .pf-grid{ grid-template-columns:1fr;} }
    .pf-item { display:flex; align-items:center; gap:12px; border:1px solid var(--border-soft); border-radius:var(--radius-md); padding:12px 14px; }
    .pf-item .ii { width:34px; height:34px; border-radius:50%; flex-shrink:0; display:flex; align-items:center; justify-content:center; }
    .pf-item .ii svg { width:16px; height:16px; fill:none; stroke-width:2; }
    .ii.c1 { background:#EFF6FF; } .ii.c1 svg { stroke:#2563EB; }
    .ii.c2 { background:#ECFDF5; } .ii.c2 svg { stroke:#059669; }
    .ii.c3 { background:#FDF2F8; } .ii.c3 svg { stroke:#DB2777; }
    .ii.c4 { background:#FFF7ED; } .ii.c4 svg { stroke:#EA580C; }
    .ii.c5 { background:#F0FDFA; } .ii.c5 svg { stroke:#0D9488; }
    .pf-item .il { font-size:10.5px; color:var(--text-2); text-transform:uppercase; letter-spacing:.04em; }
    .pf-item .iv { font-size:13.5px; font-weight:600; color:var(--text-1); margin-top:2px; word-break:break-word; }

    /* Change password modal */
    .pf-modal { position:fixed; inset:0; background:rgba(15,23,42,.5); z-index:1200; display:none; align-items:center; justify-content:center; padding:20px; }
    .pf-modal.open { display:flex; }
    .pf-box { background:var(--bg-card); border-radius:var(--radius-lg); width:100%; max-width:420px; box-shadow:0 24px 60px rgba(0,0,0,.25); overflow:hidden; }
    .pf-mhead { display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--border-soft); }
    .pf-mhead h3 { font-size:15px; font-weight:600; color:var(--text-1); }
    .pf-mclose { border:none; background:none; font-size:20px; color:var(--text-3); cursor:pointer; }
    .pf-mbody { padding:18px 20px; }
    .pf-fld { margin-bottom:14px; }
    .pf-fld:last-child { margin-bottom:0; }
    .pf-fld label { display:block; font-size:12.5px; font-weight:500; color:var(--text-2); margin-bottom:6px; }
    .pf-fld input { width:100%; border:1px solid var(--border); border-radius:var(--radius-sm); padding:9px 11px; font-family:inherit; font-size:14px; letter-spacing:3px; color:var(--text-1); background:var(--bg-card); outline:none; }
    .pf-fld input:focus { border-color:var(--brand-red); box-shadow:0 0 0 3px var(--brand-red-soft); }
    .pf-hint { font-size:11.5px; color:var(--text-3); margin-top:6px; }
    .pf-mfoot { display:flex; justify-content:flex-end; gap:8px; padding:14px 20px; border-top:1px solid var(--border-soft); background:var(--bg-page); }
</style>

@php $p = $profile; @endphp

{{-- Header card --}}
<div class="pf-card">
    <div class="pf-banner"></div>

    @if(session('profile_success'))
        <div class="pf-flash ok" style="margin-top:14px;">{{ session('profile_success') }}</div>
    @endif
    @if(session('profile_error'))
        <div class="pf-flash err" style="margin-top:14px;">{{ session('profile_error') }}</div>
    @endif

    <div class="pf-top">
        @if($p['avatar'])
            <img src="{{ $p['avatar'] }}" alt="{{ $p['name'] }}" class="pf-avatar">
        @else
            <div class="pf-avatar"><svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
        @endif
        <div class="pf-id">
            <h2>{{ $p['name'] }}</h2>
            <span class="pf-role">
                <svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                {{ $p['role'] }}
            </span>
        </div>
        <button type="button" class="pf-btn secondary pf-changepw" id="pfChangeBtn">
            <svg viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
            Change Password
        </button>
    </div>

    <div class="pf-facts">
        <div class="pf-fact blue">
            <div class="fi"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
            <div class="fl">Employee ID</div><div class="fv">{{ $p['employee_id'] }}</div>
        </div>
        <div class="pf-fact green">
            <div class="fi"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="2"/><line x1="9" y1="3" x2="9" y2="21"/><line x1="15" y1="3" x2="15" y2="21"/></svg></div>
            <div class="fl">Department</div><div class="fv">{{ $p['department'] }}</div>
        </div>
        <div class="pf-fact purple">
            <div class="fi"><svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></div>
            <div class="fl">Location</div><div class="fv">{{ $p['location'] }}</div>
        </div>
        <div class="pf-fact amber">
            <div class="fi"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
            <div class="fl">Joined</div><div class="fv">{{ $p['joined'] }}</div>
        </div>
    </div>
</div>

{{-- Contact Information --}}
<div class="pf-sec">
    <div class="pf-sec-head">
        <div class="pf-sec-ico blue"><svg viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 6l-10 7L2 6"/></svg></div>
        <h3>Contact Information</h3>
    </div>
    <div class="pf-grid" style="grid-template-columns:repeat(2,1fr);">
        <div class="pf-item"><span class="ii c1"><svg viewBox="0 0 24 24"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="M22 6l-10 7L2 6"/></svg></span><div><div class="il">Email Address</div><div class="iv">{{ $p['email'] }}</div></div></div>
        <div class="pf-item"><span class="ii c2"><svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z"/></svg></span><div><div class="il">Phone Number</div><div class="iv">{{ $p['phone'] }}</div></div></div>
    </div>
</div>

{{-- Personal Information --}}
<div class="pf-sec">
    <div class="pf-sec-head">
        <div class="pf-sec-ico purple"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></div>
        <h3>Personal Information</h3>
    </div>
    <div class="pf-grid">
        <div class="pf-item"><span class="ii c3"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span><div><div class="il">Full Name</div><div class="iv">{{ $p['name'] }}</div></div></div>
        <div class="pf-item"><span class="ii c4"><svg viewBox="0 0 24 24"><path d="M20 10c0 6-8 11-8 11s-8-5-8-11a8 8 0 0 1 16 0z"/><rect x="8" y="7" width="8" height="4" rx="1"/></svg></span><div><div class="il">Date of Birth</div><div class="iv">{{ $p['dob'] }}</div></div></div>
        <div class="pf-item"><span class="ii c1"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="5"/><path d="M12 13v8M9 18h6"/></svg></span><div><div class="il">Gender</div><div class="iv">{{ $p['gender'] }}</div></div></div>
        <div class="pf-item"><span class="ii c3"><svg viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.6l-1-1a5.5 5.5 0 0 0-7.8 7.8l1 1L12 21l7.8-7.6 1-1a5.5 5.5 0 0 0 0-7.8z"/></svg></span><div><div class="il">Marital Status</div><div class="iv">{{ $p['marital_status'] }}</div></div></div>
        <div class="pf-item"><span class="ii c1"><svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></span><div><div class="il">Date of Joining</div><div class="iv">{{ $p['date_of_joining'] }}</div></div></div>
        <div class="pf-item"><span class="ii c5"><svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg></span><div><div class="il">Work Location</div><div class="iv">{{ $p['work_location'] }}</div></div></div>
    </div>
</div>

{{-- Change password modal --}}
<div class="pf-modal {{ session('profile_error') ? 'open' : '' }}" id="pfPwdModal">
    <div class="pf-box">
        <form method="POST" action="{{ route('hr.staff.profile.password') }}">
            @csrf
            <div class="pf-mhead"><h3>Change Password</h3><button type="button" class="pf-mclose" data-close>&times;</button></div>
            <div class="pf-mbody">
                <div class="pf-fld">
                    <label>New PIN (4 digits)</label>
                    <input type="password" name="new_pin" inputmode="numeric" maxlength="4" pattern="\d{4}" placeholder="••••" autocomplete="new-password" required>
                    <div class="pf-hint">Your login PIN must be exactly 4 digits.</div>
                </div>
                <div class="pf-fld">
                    <label>Confirm New PIN</label>
                    <input type="password" name="new_pin_confirmation" inputmode="numeric" maxlength="4" pattern="\d{4}" placeholder="••••" autocomplete="new-password" required>
                </div>
            </div>
            <div class="pf-mfoot">
                <button type="button" class="pf-btn secondary" data-close>Cancel</button>
                <button type="submit" class="pf-btn primary">Update PIN</button>
            </div>
        </form>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('pfPwdModal');
    document.getElementById('pfChangeBtn').addEventListener('click', () => modal.classList.add('open'));
    modal.querySelectorAll('[data-close]').forEach(b => b.addEventListener('click', () => modal.classList.remove('open')));
    modal.addEventListener('click', e => { if (e.target === modal) modal.classList.remove('open'); });
})();
</script>
