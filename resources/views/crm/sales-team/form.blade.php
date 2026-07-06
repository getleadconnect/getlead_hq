<x-layouts.app :title="$member ? 'Edit Sales User' : 'Add Sales User'">
@push('styles')
<style>
    .crm-wrap { padding: 24px 28px 48px; width: 100%; }
    .crm-head { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
    .crm-eyebrow { font-size: 11px; font-weight: 600; letter-spacing: .09em; text-transform: uppercase; color: var(--brand-red); }
    .crm-head h1 { font-size: 24px; font-weight: 600; letter-spacing: -.5px; color: var(--text-1); margin-top: 4px; }
    .crm-head p { font-size: 13px; color: var(--text-2); margin-top: 4px; }

    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 15px; border-radius: var(--radius-sm); font-family: inherit; font-size: 13px; font-weight: 500; cursor: pointer; border: 1px solid transparent; text-decoration: none; }
    .btn-primary { background: var(--brand-red); color: #fff; }
    .btn-primary:hover { background: var(--brand-red-dark); }
    .btn-secondary { background: var(--bg-card); color: var(--text-1); border-color: var(--border); }
    .btn-secondary:hover { border-color: var(--text-3); }

    .card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 22px; max-width: 620px; }
    .card-title { font-size: 15px; font-weight: 600; color: var(--text-1); margin-bottom: 16px; }

    .flash { padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; margin-bottom: 16px; }
    .flash-error { background: var(--danger-soft); color: var(--danger-text); border: 1px solid var(--danger-border); }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    @media (max-width: 560px) { .form-row { grid-template-columns: 1fr; } }
    .field { display: flex; flex-direction: column; gap: 6px; margin-top: 14px; }
    .form-row .field { margin-top: 0; }
    .field-label { font-size: 12.5px; font-weight: 500; color: var(--text-2); }
    .hint { font-size: 11.5px; color: var(--text-3); margin-top: 4px; }
    .input { width: 100%; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 9px 11px; font-family: inherit; font-size: 13px; color: var(--text-1); background: var(--bg-card); outline: none; }
    .input:focus { border-color: var(--brand-red); box-shadow: 0 0 0 3px var(--brand-red-soft); }
    .check { display: flex; align-items: center; gap: 8px; margin-top: 16px; font-size: 13px; color: var(--text-2); }
    .check input { width: 16px; height: 16px; accent-color: var(--brand-red); }
</style>
@endpush

<div class="crm-wrap">
    <div class="crm-head">
        <div>
            <div class="crm-eyebrow">Sales Team</div>
            <h1>{{ $member ? 'Edit Sales User' : 'Add Sales User' }}</h1>
            <p>{{ $member ? 'Update this sales team member.' : 'Creates a staff login with the Sales Rep role.' }}</p>
        </div>
        <a href="{{ route('crm.sales-team') }}" class="btn btn-secondary">Back</a>
    </div>

    <div class="card">
        <h2 class="card-title">Member Details</h2>

        @if(isset($errors) && $errors->any())
            <div class="flash flash-error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ $member ? route('crm.sales-team.update', $member->id) : route('crm.sales-team.store') }}">
            @csrf
            @if($member) @method('PUT') @endif

            <div class="form-row">
                <label class="field">
                    <span class="field-label">Full Name</span>
                    <input type="text" name="name" class="input" required value="{{ old('name', $member->name ?? '') }}">
                </label>
                <label class="field">
                    <span class="field-label">Mobile Number</span>
                    <input type="tel" name="mobile" class="input" required value="{{ old('mobile', $member->mobile ?? '') }}">
                    <span class="hint">Used to log in. Stored as the last 10 digits.</span>
                </label>
            </div>

            <label class="field">
                <span class="field-label">{{ $member ? '4-digit PIN (leave blank to keep current)' : '4-digit PIN' }}</span>
                <input type="text" name="pin" class="input" inputmode="numeric" maxlength="4" pattern="\d{4}" {{ $member ? '' : 'required' }} value="{{ old('pin') }}">
                <span class="hint">Exactly 4 digits. This is the member's login PIN.</span>
            </label>

            <label class="field">
                <span class="field-label">Telegram ID <span style="color:var(--text-3);">(optional)</span></span>
                <input type="text" name="telegram_id" class="input" value="{{ old('telegram_id', $member->telegram_id ?? '') }}">
            </label>

            @if($member)
                <label class="check">
                    <input type="checkbox" name="active" value="1" {{ old('active', $member->active) ? 'checked' : '' }}>
                    Active (can log in)
                </label>
            @endif

            <div style="margin-top:20px;">
                <button type="submit" class="btn btn-primary">{{ $member ? 'Update Member' : 'Create Sales User' }}</button>
            </div>
        </form>
    </div>
</div>
</x-layouts.app>
