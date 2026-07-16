<x-layouts.app title="Application Details">
@push('styles')
<style>
    .hr-wrap { padding: 24px 28px 48px; width: 100%; max-width: 1000px; }
    .hr-head { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
    .hr-eyebrow { font-size: 11px; font-weight: 600; letter-spacing: .09em; text-transform: uppercase; color: var(--brand-red); }
    .hr-head h1 { font-size: 24px; font-weight: 600; letter-spacing: -.5px; color: var(--text-1); margin-top: 4px; }
    .hr-head p { font-size: 13px; color: var(--text-2); margin-top: 4px; }
    .head-actions { display: flex; gap: 8px; flex-shrink: 0; }

    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 15px; border-radius: var(--radius-sm); font-family: inherit; font-size: 13px; font-weight: 500; cursor: pointer; border: 1px solid transparent; text-decoration: none; }
    .btn svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
    .btn-primary { background: var(--brand-red); color: #fff; }
    .btn-primary:hover { background: var(--brand-red-dark); }
    .btn-secondary { background: var(--bg-card); color: var(--text-1); border-color: var(--border); }
    .btn-secondary:hover { border-color: var(--text-3); }

    .layout { display: grid; grid-template-columns: 280px 1fr; gap: 20px; align-items: start; }
    @media (max-width: 780px) { .layout { grid-template-columns: 1fr; } }

    .card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 22px; }
    .profile { text-align: center; }
    .profile .avatar { width: 120px; height: 120px; border-radius: 16px; object-fit: cover; border: 1px solid var(--border); background: var(--bg-neutral); margin: 0 auto 14px; display: block; }
    .profile .avatar-fallback { width: 120px; height: 120px; border-radius: 16px; background: linear-gradient(135deg,#EF4444,var(--brand-red)); color: #fff; font-size: 38px; font-weight: 600; display: flex; align-items: center; justify-content: center; margin: 0 auto 14px; }
    .profile h2 { font-size: 18px; font-weight: 600; color: var(--text-1); }
    .profile .role { font-size: 12.5px; color: var(--text-2); margin-top: 3px; }
    .badge { display: inline-block; margin-top: 12px; padding: 4px 12px; border-radius: var(--radius-pill); font-size: 12px; font-weight: 600; }
    .b-new { background:#EFF6FF; color:#1D4ED8; border:1px solid #BFDBFE; }
    .b-good { background: var(--success-soft); color: var(--success-text); border:1px solid var(--success-border); }
    .b-bad { background: var(--danger-soft); color: var(--danger-text); border:1px solid var(--danger-border); }
    .b-warn { background: var(--warning-soft); color: var(--warning-text); border:1px solid var(--warning-border); }
    .profile .cv-btn { margin-top: 16px; width: 100%; justify-content: center; }

    .section { margin-bottom: 22px; }
    .section:last-child { margin-bottom: 0; }
    .section h3 { font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: .06em; color: var(--brand-red); margin-bottom: 12px; padding-bottom: 8px; border-bottom: 1px solid var(--border-soft); }
    .rows { display: grid; grid-template-columns: 1fr 1fr; gap: 12px 24px; }
    @media (max-width: 560px) { .rows { grid-template-columns: 1fr; } }
    .row-item .k { font-size: 11.5px; color: var(--text-3); margin-bottom: 2px; }
    .row-item .v { font-size: 13.5px; color: var(--text-1); font-weight: 500; word-break: break-word; }
    .row-item.full { grid-column: 1 / -1; }
</style>
@endpush

@php
    $badge = match(true) {
        in_array($app->status, ['Appointed','Short Listed']) => 'b-good',
        in_array($app->status, ['Rejected','Not fit for this job','Not Interested','Not Joined']) => 'b-bad',
        $app->status === 'No vacancies now' => 'b-warn',
        default => 'b-new',
    };
    $val = fn($v) => ($v === null || $v === '') ? '—' : $v;
@endphp

<div class="hr-wrap">
    <div class="hr-head">
        <div>
            <div class="hr-eyebrow">HR Management</div>
            <h1>Application Details</h1>
            <p>Full details of the submitted application.</p>
        </div>
        <div class="head-actions">
            <a href="{{ route('hr.applications') }}" class="btn btn-secondary">
                <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Back
            </a>
        </div>
    </div>

    <div class="layout">
        {{-- Profile card --}}
        <div class="card profile">
            @if($app->photo_url)
                <img src="{{ $app->photo_url }}" class="avatar" alt="photo"
                     onerror="this.outerHTML='<div class=\'avatar-fallback\'>{{ strtoupper(mb_substr($app->name,0,1)) }}</div>'">
            @else
                <div class="avatar-fallback">{{ strtoupper(mb_substr($app->name, 0, 1)) }}</div>
            @endif
            <h2>{{ strtoupper($app->name) }}</h2>
            <div class="role">{{ $app->category_name ?? '—' }}</div>
            <span class="badge {{ $badge }}">{{ $app->status }}</span>
            @if($app->cv_url)
                <a href="{{ $app->cv_url }}" target="_blank" class="btn btn-primary cv-btn">
                    <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    View CV
                </a>
            @endif
        </div>

        {{-- Details --}}
        <div class="card">
            <div class="section">
                <h3>Personal Information</h3>
                <div class="rows">
                    <div class="row-item"><div class="k">Mobile</div><div class="v">{{ $val(($app->countrycode ?? '').$app->mobile) }}</div></div>
                    <div class="row-item"><div class="k">Email</div><div class="v">{{ $val($app->email) }}</div></div>
                    <div class="row-item"><div class="k">Date of Birth</div><div class="v">{{ $val($app->dob) }}</div></div>
                    <div class="row-item"><div class="k">Gender</div><div class="v">{{ $val($app->gender) }}</div></div>
                    <div class="row-item"><div class="k">Marital Status</div><div class="v">{{ $val($app->marital_status) }}</div></div>
                </div>
            </div>

            <div class="section">
                <h3>Additional Information</h3>
                <div class="rows">
                    <div class="row-item"><div class="k">Father Name</div><div class="v">{{ $val($app->father_name) }}</div></div>
                    <div class="row-item"><div class="k">Pincode</div><div class="v">{{ $val($app->pincode) }}</div></div>
                    <div class="row-item full"><div class="k">Address</div><div class="v">{{ $val($app->address) }}</div></div>
                    <div class="row-item"><div class="k">State</div><div class="v">{{ $val($app->state) }}</div></div>
                    <div class="row-item"><div class="k">District</div><div class="v">{{ $val($app->district) }}</div></div>
                </div>
            </div>

            <div class="section">
                <h3>Professional Information</h3>
                <div class="rows">
                    <div class="row-item"><div class="k">Qualification</div><div class="v">{{ $val($app->qualification) }}</div></div>
                    <div class="row-item"><div class="k">Technology Stack</div><div class="v">{{ $val($app->technology_stack) }}</div></div>
                    <div class="row-item"><div class="k">Experience</div><div class="v">{{ $val($app->experience) }}</div></div>
                    <div class="row-item"><div class="k">Experience Years</div><div class="v">{{ $val($app->experience_years) }}</div></div>
                    <div class="row-item"><div class="k">Previous Employer</div><div class="v">{{ $val($app->previous_employer) }}</div></div>
                    <div class="row-item"><div class="k">Last Drawn Salary</div><div class="v">{{ $val($app->last_drawn_salary) }}</div></div>
                    <div class="row-item"><div class="k">Expected Salary</div><div class="v">{{ $val($app->expected_salary) }}</div></div>
                </div>
            </div>

            <div class="section">
                <h3>General Information</h3>
                <div class="rows">
                    <div class="row-item full"><div class="k">Why Changing Job</div><div class="v">{{ $val($app->why_changing_job) }}</div></div>
                    <div class="row-item full"><div class="k">Why Getlead</div><div class="v">{{ $val($app->why_getlead) }}</div></div>
                    <div class="row-item"><div class="k">Applied For</div><div class="v">{{ $val($app->category_name) }}</div></div>
                    <div class="row-item"><div class="k">Declaration</div><div class="v">{{ $val($app->declaration) }}</div></div>
                    @if($app->rejection_reason)
                        <div class="row-item full"><div class="k">Rejection Reason</div><div class="v">{{ $app->rejection_reason }}</div></div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</x-layouts.app>
