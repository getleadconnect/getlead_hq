<x-layouts.app title="CRM Sales Team">
@push('styles')
<style>
    .crm-wrap { padding: 24px 28px 48px; width: 100%; }
    .crm-head { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
    .crm-eyebrow { font-size: 11px; font-weight: 600; letter-spacing: .09em; text-transform: uppercase; color: var(--brand-red); }
    .crm-head h1 { font-size: 24px; font-weight: 600; letter-spacing: -.5px; color: var(--text-1); margin-top: 4px; }
    .crm-head p { font-size: 13px; color: var(--text-2); margin-top: 4px; max-width: 560px; }

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

    .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 24px; }
    @media (max-width: 980px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 520px) { .kpi-grid { grid-template-columns: 1fr; } }
    .kpi-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 16px 18px; }
    .kpi-label { font-size: 12.5px; color: var(--text-2); font-weight: 500; margin-bottom: 8px; }
    .kpi-figure { font-size: 26px; font-weight: 600; color: var(--text-1); letter-spacing: -.02em; }

    .table-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; }
    .crm-table { width: 100%; border-collapse: collapse; }
    .crm-table thead th { text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: var(--text-3); padding: 12px 16px; border-bottom: 1px solid var(--border); background: var(--bg-page); }
    .crm-table tbody td { padding: 13px 16px; font-size: 13px; color: var(--text-2); border-bottom: 1px solid var(--border-soft); }
    .crm-table tbody tr:last-child td { border-bottom: none; }
    .crm-table tbody tr:hover { background: var(--bg-page); }
    .crm-table .num-col, .crm-table .num-cell { text-align: right; font-variant-numeric: tabular-nums; }
    .crm-empty { text-align: center; color: var(--text-3); padding: 32px 16px; }
    .row-actions { display: flex; gap: 6px; justify-content: flex-end; }

    .rep { display: flex; align-items: center; gap: 10px; }
    .rep-avatar { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg,#EF4444,var(--brand-red)); display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: 600; color: #fff; flex-shrink: 0; }
    .rep-name { color: var(--text-1); font-weight: 500; }
    .badge { display: inline-block; padding: 3px 10px; border-radius: var(--radius-pill); font-size: 11.5px; font-weight: 600; }
    .badge-success { background: var(--success-soft); color: var(--success-text); border: 1px solid var(--success-border); }
    .badge-neutral { background: var(--bg-neutral); color: var(--text-2); border: 1px solid var(--border); }
    .link { color: var(--brand-red-dark); text-decoration: none; font-weight: 500; }
    .link:hover { text-decoration: underline; }
</style>
@endpush

<div class="crm-wrap">
    <div class="crm-head">
        <div>
            <div class="crm-eyebrow">Admin</div>
            <h1>Sales Team</h1>
            <p>Sales staff who can create and track demo links. New members are added to the staff directory with the <strong>Sales Rep</strong> role and can log in with their mobile + PIN.</p>
        </div>
        <a href="{{ route('crm.sales-team.create') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Member
        </a>
    </div>

    @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif

    <div class="kpi-grid">
        <div class="kpi-card"><div class="kpi-label">Team Size</div><div class="kpi-figure num">{{ number_format($summary['team_size']) }}</div></div>
        <div class="kpi-card"><div class="kpi-label">Active</div><div class="kpi-figure num">{{ number_format($summary['active']) }}</div></div>
        <div class="kpi-card"><div class="kpi-label">Customers Created</div><div class="kpi-figure num">{{ number_format($summary['total_customers']) }}</div></div>
        <div class="kpi-card"><div class="kpi-label">Demo Views Generated</div><div class="kpi-figure num">{{ number_format($summary['total_views']) }}</div></div>
    </div>

    <div class="table-card">
        <table class="crm-table">
            <thead>
                <tr>
                    <th>Member</th>
                    <th>Mobile</th>
                    <th class="num-col">Customers</th>
                    <th class="num-col">Views</th>
                    <th class="num-col">Completed</th>
                    <th>Status</th>
                    <th>Last Activity</th>
                    <th style="text-align:right;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reps as $r)
                    <tr>
                        <td>
                            <div class="rep">
                                <div class="rep-avatar">{{ strtoupper(mb_substr($r->name, 0, 2)) }}</div>
                                <span class="rep-name">{{ $r->name }}</span>
                            </div>
                        </td>
                        <td>{{ $r->mobile ?: '—' }}</td>
                        <td class="num-cell">
                            @if($r->customers_count > 0)
                                <a class="link" href="{{ route('crm.customers', ['rep' => $r->id]) }}">{{ number_format($r->customers_count) }}</a>
                            @else
                                0
                            @endif
                        </td>
                        <td class="num-cell">{{ number_format((int) $r->total_views) }}</td>
                        <td class="num-cell">{{ number_format($r->completions) }}</td>
                        <td>
                            @if($r->active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-neutral">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $r->last_activity ? \Carbon\Carbon::parse($r->last_activity)->format('M j, Y') : '—' }}</td>
                        <td>
                            <div class="row-actions">
                                <a href="{{ route('crm.sales-team.edit', $r->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                                <form method="POST" action="{{ route('crm.sales-team.destroy', $r->id) }}" onsubmit="return confirm('Delete this sales user? This removes their staff login.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="crm-empty">No sales team members yet. Click “Add Member” to create one.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</x-layouts.app>
