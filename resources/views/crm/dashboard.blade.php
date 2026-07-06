<x-layouts.app title="CRM Dashboard">
@push('styles')
<style>
    .crm-wrap { padding: 24px 28px 48px; width: 100%; }

    .crm-head {
        display: flex; align-items: flex-start; justify-content: space-between;
        flex-wrap: wrap; gap: 12px; margin-bottom: 22px;
    }
    .crm-eyebrow {
        font-size: 11px; font-weight: 600; letter-spacing: .09em; text-transform: uppercase;
        color: var(--brand-red);
    }
    .crm-head h1 { font-size: 24px; font-weight: 600; letter-spacing: -.5px; color: var(--text-1); margin-top: 4px; }
    .crm-head p  { font-size: 13px; color: var(--text-2); margin-top: 4px; max-width: 560px; }

    .btn {
        display: inline-flex; align-items: center; gap: 6px; padding: 9px 15px;
        border-radius: var(--radius-sm); font-family: inherit; font-size: 13px; font-weight: 500;
        cursor: pointer; border: 1px solid transparent; text-decoration: none;
    }
    .btn svg { width: 15px; height: 15px; stroke: currentColor; stroke-width: 2; fill: none; }
    .btn-primary { background: var(--brand-red); color: #fff; }
    .btn-primary:hover { background: var(--brand-red-dark); }
    .btn-secondary { background: var(--bg-card); color: var(--text-1); border-color: var(--border); }
    .btn-secondary:hover { border-color: var(--text-3); }
    .btn-sm { padding: 5px 11px; font-size: 12px; }

    /* KPI grid */
    .kpi-grid {
        display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 28px;
    }
    @media (max-width: 980px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 520px) { .kpi-grid { grid-template-columns: 1fr; } }
    .kpi-card {
        background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg);
        padding: 18px 18px 16px; display: flex; flex-direction: column; gap: 12px;
    }
    .kpi-label { font-size: 12.5px; color: var(--text-2); font-weight: 500; }
    .kpi-figure { font-size: 30px; font-weight: 600; color: var(--text-1); letter-spacing: -.02em; }
    .kpi-icon {
        width: 34px; height: 34px; border-radius: 9px; display: flex; align-items: center; justify-content: center;
        background: var(--brand-red-soft); color: var(--brand-red); flex-shrink: 0;
    }
    .kpi-icon svg { width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 1.9; stroke-linecap: round; stroke-linejoin: round; }
    .kpi-top { display: flex; align-items: center; justify-content: space-between; }

    /* Sparkline */
    .sparkline { display: flex; align-items: flex-end; gap: 4px; height: 34px; }
    .sparkline .bar { flex: 1; border-radius: 3px 3px 0 0; background: var(--bg-neutral-2); }
    .sparkline .bar.live { background: var(--brand-red); opacity: .85; }
    .sparkline .bar.live-success { background: var(--success); opacity: .8; }

    /* Section + table */
    .crm-section { margin-top: 8px; }
    .crm-section-head { display: flex; align-items: center; justify-content: space-between; margin-bottom: 14px; }
    .crm-section-head h2 { font-size: 16px; font-weight: 600; color: var(--text-1); }

    .table-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); overflow: hidden; }
    .crm-table { width: 100%; border-collapse: collapse; }
    .crm-table thead th {
        text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em;
        color: var(--text-3); padding: 12px 16px; border-bottom: 1px solid var(--border); background: var(--bg-page);
    }
    .crm-table tbody td { padding: 13px 16px; font-size: 13px; color: var(--text-2); border-bottom: 1px solid var(--border-soft); }
    .crm-table tbody tr:last-child td { border-bottom: none; }
    .crm-table tbody tr:hover { background: var(--bg-page); }
    .crm-table td.primary { color: var(--text-1); font-weight: 500; }
    .crm-table .num-col, .crm-table .num-cell { text-align: right; font-variant-numeric: tabular-nums; }
    .crm-empty { text-align: center; color: var(--text-3); padding: 32px 16px; }
</style>
@endpush

<div class="crm-wrap">
    <div class="crm-head">
        <div>
            <div class="crm-eyebrow">Overview</div>
            <h1>Getlead CRM Dashboard</h1>
            <p>Track demo engagement, manage customers, and oversee your sales team.</p>
        </div>
        <a href="{{ route('crm.customers.create') }}" class="btn btn-primary">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Customer
        </a>
    </div>

    {{-- KPI cards --}}
    <div class="kpi-grid">
        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-label">Total Customers</div>
                <div class="kpi-icon"><svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
            </div>
            <div class="kpi-figure num">{{ number_format($totalCustomers) }}</div>
            <div class="sparkline"><div class="bar live" style="height:60%"></div><div class="bar live" style="height:80%"></div><div class="bar live" style="height:70%"></div><div class="bar live" style="height:90%"></div><div class="bar live" style="height:100%"></div></div>
        </div>

        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-label">Demo Views</div>
                <div class="kpi-icon"><svg viewBox="0 0 24 24"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></div>
            </div>
            <div class="kpi-figure num">{{ number_format($totalViews) }}</div>
            <div class="sparkline"><div class="bar live" style="height:40%"></div><div class="bar live" style="height:55%"></div><div class="bar live" style="height:65%"></div><div class="bar live" style="height:75%"></div><div class="bar live" style="height:85%"></div></div>
        </div>

        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-label">Completed Demos</div>
                <div class="kpi-icon"><svg viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg></div>
            </div>
            <div class="kpi-figure num">{{ number_format($totalCompletions) }}</div>
            <div class="sparkline"><div class="bar live-success" style="height:30%"></div><div class="bar live-success" style="height:50%"></div><div class="bar live-success" style="height:60%"></div><div class="bar live-success" style="height:80%"></div><div class="bar live-success" style="height:95%"></div></div>
        </div>

        <div class="kpi-card">
            <div class="kpi-top">
                <div class="kpi-label">Sales Team</div>
                <div class="kpi-icon"><svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg></div>
            </div>
            <div class="kpi-figure num">{{ number_format($totalSales) }}</div>
            <div class="sparkline"><div class="bar" style="height:70%"></div><div class="bar" style="height:70%"></div><div class="bar" style="height:70%"></div><div class="bar" style="height:70%"></div><div class="bar" style="height:70%"></div></div>
        </div>
    </div>

    {{-- Recent customers --}}
    <section class="crm-section">
        <div class="crm-section-head">
            <h2>Recent Customers</h2>
        </div>

        <div class="table-card">
            <table class="crm-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Mobile</th>
                        <th>Sales Person</th>
                        <th class="num-col">Views</th>
                        <th>Created</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentCustomers as $c)
                        <tr>
                            <td class="primary">{{ $c->name }}</td>
                            <td>{{ $c->mobile }}</td>
                            <td>{{ $c->sales_name ?? 'Unknown' }}</td>
                            <td class="num-cell">{{ number_format((int) $c->views_count) }}</td>
                            <td>{{ $c->created_at ? \Carbon\Carbon::parse($c->created_at)->format('M j, Y') : '—' }}</td>
                            <td><a href="{{ route('crm.customers.show', $c->id) }}" class="btn btn-sm btn-secondary">View</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="crm-empty">No customers yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
</x-layouts.app>
