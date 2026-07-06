<x-layouts.app title="New Customer">
@push('styles')
<style>
    .crm-wrap { padding: 24px 28px 48px; width: 100%; }
    .crm-head { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
    .crm-eyebrow { font-size: 11px; font-weight: 600; letter-spacing: .09em; text-transform: uppercase; color: var(--brand-red); }
    .crm-head h1 { font-size: 24px; font-weight: 600; letter-spacing: -.5px; color: var(--text-1); margin-top: 4px; }
    .crm-head p { font-size: 13px; color: var(--text-2); margin-top: 4px; }

    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 15px; border-radius: var(--radius-sm); font-family: inherit; font-size: 13px; font-weight: 500; cursor: pointer; border: 1px solid transparent; text-decoration: none; }
    .btn svg { width: 15px; height: 15px; stroke: currentColor; stroke-width: 2; fill: none; }
    .btn-primary { background: var(--brand-red); color: #fff; }
    .btn-primary:hover { background: var(--brand-red-dark); }
    .btn-secondary { background: var(--bg-card); color: var(--text-1); border-color: var(--border); }
    .btn-secondary:hover { border-color: var(--text-3); }
    .btn-ghost { background: transparent; color: var(--text-2); border-color: var(--border); }
    .btn-ghost:hover { background: var(--bg-neutral); color: var(--text-1); }
    .btn-sm { padding: 5px 11px; font-size: 12px; }

    .card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 22px; max-width: 620px; }
    .card-title { font-size: 15px; font-weight: 600; color: var(--text-1); margin-bottom: 16px; }

    .flash { padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; margin-bottom: 16px; }
    .flash-success { background: var(--success-soft); color: var(--success-text); border: 1px solid var(--success-border); }
    .flash-error { background: var(--danger-soft); color: var(--danger-text); border: 1px solid var(--danger-border); }

    .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    @media (max-width: 560px) { .form-row { grid-template-columns: 1fr; } }
    .field { display: flex; flex-direction: column; gap: 6px; margin-top: 14px; }
    .field:first-child, .form-row .field { margin-top: 0; }
    .field-label { font-size: 12.5px; font-weight: 500; color: var(--text-2); }
    .input, .select, textarea.input { width: 100%; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 9px 11px; font-family: inherit; font-size: 13px; color: var(--text-1); background: var(--bg-card); outline: none; }
    .input:focus, .select:focus, textarea.input:focus { border-color: var(--brand-red); box-shadow: 0 0 0 3px var(--brand-red-soft); }
    textarea.input { min-height: 74px; resize: vertical; }

    /* Share panel */
    .share-panel { border: 1px solid var(--success-border); background: var(--success-soft); border-radius: var(--radius-lg); padding: 18px; margin-bottom: 18px; }
    .share-panel-head { display: flex; align-items: flex-start; gap: 12px; margin-bottom: 14px; }
    .share-spark { font-size: 24px; line-height: 1; }
    .share-title { font-size: 15px; font-weight: 600; color: var(--text-1); }
    .share-sub { font-size: 12.5px; color: var(--text-2); margin-top: 2px; }
    .link-box { display: flex; gap: 8px; margin-bottom: 12px; }
    .link-box input { flex: 1; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 8px 11px; font-size: 12.5px; color: var(--text-1); background: var(--bg-card); }
    .share-actions { display: flex; gap: 8px; flex-wrap: wrap; }
    .share-preview { margin-top: 14px; }
    .share-preview summary { cursor: pointer; font-size: 12.5px; font-weight: 500; color: var(--text-2); }
    .share-field { margin-top: 12px; }
    .share-field-label { display: flex; align-items: center; justify-content: space-between; font-size: 12px; font-weight: 500; color: var(--text-2); margin-bottom: 5px; }
</style>
@endpush

<div class="crm-wrap">
    <div class="crm-head">
        <div>
            <div class="crm-eyebrow">Customers</div>
            <h1>Create Customer</h1>
            <p>Generate a personalized demo link for a new customer.</p>
        </div>
        <a href="{{ route('crm.customers') }}" class="btn btn-secondary">Back to Customers</a>
    </div>

    <div class="card">
        <h2 class="card-title">Customer Details</h2>

        @if(session('success'))
            <div class="flash flash-success">{{ session('success') }}</div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="flash flash-error">{{ $errors->first() }}</div>
        @endif

        @if($generatedLink && $share)
            @include('crm.customers._share-panel', ['generatedLink' => $generatedLink, 'share' => $share, 'name' => $old->name])
        @endif

        <form method="POST" action="{{ route('crm.customers.store') }}">
            @csrf
            <div class="form-row">
                <label class="field">
                    <span class="field-label">Customer Name</span>
                    <input type="text" name="name" class="input" required value="{{ old('name') }}">
                </label>
                <label class="field">
                    <span class="field-label">Mobile Number</span>
                    <input type="tel" name="mobile" class="input" required value="{{ old('mobile') }}">
                </label>
            </div>
            <label class="field">
                <span class="field-label">Assign to Sales Person</span>
                <select name="created_by" class="select">
                    <option value="{{ $currentUser->id }}">{{ $currentUser->name }} (me)</option>
                    @foreach($salesUsers as $u)
                        @if($u->id !== $currentUser->id)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endif
                    @endforeach
                </select>
            </label>
            <label class="field">
                <span class="field-label">Notes</span>
                <textarea name="notes" class="input">{{ old('notes') }}</textarea>
            </label>
            <div style="margin-top:18px;">
                <button type="submit" class="btn btn-primary">Generate Demo Link</button>
            </div>
        </form>
    </div>
</div>
</x-layouts.app>
