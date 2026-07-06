<x-layouts.app title="CRM Customers">
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
    .btn-ghost { background: transparent; color: var(--text-2); border-color: transparent; }
    .btn-ghost:hover { background: var(--bg-neutral); color: var(--text-1); }
    .btn-danger { background: var(--danger-soft); color: var(--danger); border-color: var(--danger-border); }
    .btn-danger:hover { background: var(--danger); color: #fff; }
    .btn-sm { padding: 5px 11px; font-size: 12px; }

    .flash { padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; margin-bottom: 16px; }
    .flash-success { background: var(--success-soft); color: var(--success-text); border: 1px solid var(--success-border); }

    .toolbar { display: flex; align-items: center; gap: 8px; flex-wrap: wrap; margin-bottom: 14px; }
    .search { position: relative; }
    .search > svg { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 15px; height: 15px; stroke: var(--text-3); stroke-width: 1.8; fill: none; }
    .search input { height: 36px; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 0 12px 0 32px; font-family: inherit; font-size: 13px; color: var(--text-1); background: var(--bg-card); outline: none; width: 280px; max-width: 100%; }
    .search input:focus { border-color: var(--brand-red); box-shadow: 0 0 0 3px var(--brand-red-soft); }

    .table-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; }
    .crm-table { width: 100%; border-collapse: collapse; }
    .crm-table thead th { text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: var(--text-3); padding: 12px 16px; border-bottom: 1px solid var(--border); background: var(--bg-page); }
    .crm-table tbody td { padding: 13px 16px; font-size: 13px; color: var(--text-2); border-bottom: 1px solid var(--border-soft); }
    .crm-table tbody tr:last-child td { border-bottom: none; }
    .crm-table tbody tr:hover { background: var(--bg-page); }
    .crm-table td.primary { color: var(--text-1); font-weight: 500; }
    .crm-table .num-col, .crm-table .num-cell { text-align: right; font-variant-numeric: tabular-nums; }
    .row-actions { display: flex; gap: 6px; justify-content: flex-end; }
    .crm-empty { text-align: center; color: var(--text-3); padding: 32px 16px; }
</style>
@endpush

<div class="crm-wrap">
    <div class="crm-head">
        <div>
            <div class="crm-eyebrow">Manage</div>
            <h1>Customers</h1>
            <p>View and manage all customer demo links and engagement.</p>
        </div>
        <a href="{{ route('crm.customers.create') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Customer
        </a>
    </div>

    @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif

    @if(($rep ?? 0) > 0)
        <div class="flash" style="background:var(--brand-red-soft);color:var(--brand-red-dark);border:1px solid var(--brand-red-border);display:flex;align-items:center;justify-content:space-between;">
            <span>Showing customers created by <strong>{{ $repName ?: 'this sales person' }}</strong></span>
            <a href="{{ route('crm.customers') }}" class="btn btn-sm btn-ghost">Show all</a>
        </div>
    @endif

    <form method="GET" class="toolbar">
        @if(($rep ?? 0) > 0)<input type="hidden" name="rep" value="{{ $rep }}">@endif
        <div class="search">
            <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="search" placeholder="Search name or mobile" value="{{ $search }}">
        </div>
        <button type="submit" class="btn btn-secondary">Search</button>
        @if($search !== '')
            <a href="{{ route('crm.customers', ($rep ?? 0) > 0 ? ['rep' => $rep] : []) }}" class="btn btn-ghost">Clear</a>
        @endif
    </form>

    <div class="table-card">
        <table class="crm-table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Mobile</th>
                    <th>Sales Person</th>
                    <th class="num-col">Views</th>
                    <th>Last Viewed</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $c)
                    <tr>
                        <td class="primary">{{ $c->name }}</td>
                        <td>{{ $c->mobile }}</td>
                        <td>{{ $c->sales_name ?? 'Unknown' }}</td>
                        <td class="num-cell">{{ number_format((int) $c->views_count) }}</td>
                        <td>{{ $c->last_viewed_at ? \Carbon\Carbon::parse($c->last_viewed_at)->format('M j, Y g:i A') : '—' }}</td>
                        <td>
                            <div class="row-actions">
                                <a href="{{ route('crm.customers.show', $c->id) }}" class="btn btn-sm btn-secondary">Details</a>
                                <a href="{{ url('/crm/watch') }}?token={{ $c->token }}" target="_blank" class="btn btn-sm btn-ghost">Open</a>
                                <form method="POST" action="{{ route('crm.customers.destroy', $c->id) }}" onsubmit="return confirm('Delete this customer and all its data?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="crm-empty">No customers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-layouts.app>
