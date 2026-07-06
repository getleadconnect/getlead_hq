<x-layouts.app title="Customer Detail">
@php $fmt = \App\Http\Controllers\Crm\CustomerDetailController::class; @endphp
@push('styles')
<style>
    .crm-wrap { padding: 24px 28px 48px; width: 100%; }
    .crm-head { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 20px; }
    .crm-eyebrow { font-size: 11px; font-weight: 600; letter-spacing: .09em; text-transform: uppercase; color: var(--brand-red); }
    .crm-head h1 { font-size: 24px; font-weight: 600; letter-spacing: -.5px; color: var(--text-1); margin-top: 4px; }
    .crm-head p { font-size: 13px; color: var(--text-2); margin-top: 4px; }
    .head-actions { display: flex; gap: 8px; flex-shrink: 0; }

    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 15px; border-radius: var(--radius-sm); font-family: inherit; font-size: 13px; font-weight: 500; cursor: pointer; border: 1px solid transparent; text-decoration: none; }
    .btn svg { width: 15px; height: 15px; stroke: currentColor; stroke-width: 2; fill: none; }
    .btn-primary { background: var(--brand-red); color: #fff; }
    .btn-primary:hover { background: var(--brand-red-dark); }
    .btn-secondary { background: var(--bg-card); color: var(--text-1); border-color: var(--border); }
    .btn-secondary:hover { border-color: var(--text-3); }
    .btn-sm { padding: 5px 11px; font-size: 12px; }

    .card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 20px; margin-bottom: 20px; }
    .card-title { font-size: 15px; font-weight: 600; color: var(--text-1); margin-bottom: 14px; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
    @media (max-width: 820px) { .grid-2 { grid-template-columns: 1fr; } }
    .muted { color: var(--text-3); font-size: 12px; margin-bottom: 2px; }
    .val { color: var(--text-1); font-weight: 500; font-size: 13.5px; }

    .link-box { display: flex; gap: 8px; }
    .link-box input { flex: 1; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 8px 11px; font-size: 12.5px; color: var(--text-1); background: var(--bg-card); }

    /* KPI */
    .kpi-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 20px; }
    @media (max-width: 980px) { .kpi-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 520px) { .kpi-grid { grid-template-columns: 1fr; } }
    .kpi-card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 16px 18px; }
    .kpi-label { font-size: 12.5px; color: var(--text-2); font-weight: 500; margin-bottom: 8px; }
    .kpi-figure { font-size: 26px; font-weight: 600; color: var(--text-1); letter-spacing: -.02em; }

    /* Badges */
    .badge { display: inline-block; padding: 3px 10px; border-radius: var(--radius-pill); font-size: 11.5px; font-weight: 600; }
    .badge-success { background: var(--success-soft); color: var(--success-text); border: 1px solid var(--success-border); }
    .badge-warning { background: var(--warning-soft); color: var(--warning-text); border: 1px solid var(--warning-border); }
    .badge-neutral { background: var(--bg-neutral); color: var(--text-2); border: 1px solid var(--border); }

    /* Table */
    .crm-table { width: 100%; border-collapse: collapse; }
    .crm-table thead th { text-align: left; font-size: 11px; font-weight: 600; text-transform: uppercase; letter-spacing: .05em; color: var(--text-3); padding: 10px 12px; border-bottom: 1px solid var(--border); }
    .crm-table tbody td { padding: 11px 12px; font-size: 13px; color: var(--text-2); border-bottom: 1px solid var(--border-soft); }
    .crm-table tbody tr:last-child td { border-bottom: none; }
    .crm-table .num-col, .crm-table .num-cell { text-align: right; font-variant-numeric: tabular-nums; }
    .crm-empty { text-align: center; color: var(--text-3); padding: 22px 12px; }
    .loc-list { list-style: none; color: var(--text-2); font-size: 13px; }
    .loc-list li { padding: 3px 0; }

    /* Heatmap */
    .heatmap-title { font-size: 15px; font-weight: 600; color: var(--text-1); }
    .heatmap-subtitle { font-size: 12.5px; color: var(--text-2); margin: 4px 0 16px; }
    .heatmap-bars { display: flex; align-items: flex-end; gap: 2px; height: 120px; }
    .heatmap-bar { flex: 1; background: var(--brand-red); border-radius: 2px 2px 0 0; min-height: 4px; transition: opacity .15s; }
    .heatmap-bar:hover { outline: 1px solid var(--brand-red-dark); }
    .heatmap-axis { display: flex; justify-content: space-between; font-size: 11px; color: var(--text-3); margin-top: 8px; }
    .heatmap-legend { display: flex; align-items: center; gap: 8px; font-size: 11px; color: var(--text-3); margin-top: 12px; }
    .heatmap-legend-bar { flex: 1; height: 6px; border-radius: 3px; background: linear-gradient(90deg, var(--brand-red-soft), var(--brand-red)); }
</style>
@endpush

@php $c = $summary['customer']; @endphp

<div class="crm-wrap">
    <div class="crm-head">
        <div>
            <div class="crm-eyebrow">Customer Details</div>
            <h1>{{ $c->name }}</h1>
            <p>Demo engagement summary and view history.</p>
        </div>
        <div class="head-actions">
            <a href="{{ route('crm.customers') }}" class="btn btn-secondary">Back</a>
            <a href="{{ $customerLink }}" target="_blank" class="btn btn-primary">Open Demo Link</a>
        </div>
    </div>

    {{-- Customer info --}}
    <div class="card">
        <h2 class="card-title">Customer Info</h2>
        <div class="grid-2">
            <div>
                <div class="muted">Mobile</div>
                <div class="val">{{ $c->mobile }}</div>
            </div>
            <div>
                <div class="muted">Demo Link</div>
                <div class="link-box" style="margin-top:4px;">
                    <input type="text" id="copyLink" value="{{ $customerLink }}" readonly>
                    <button type="button" class="btn btn-secondary btn-sm" onclick="crmCopyLink()">Copy</button>
                </div>
            </div>
        </div>
        @if($c->notes)
            <div style="margin-top:14px;">
                <div class="muted">Notes</div>
                <div class="val" style="font-weight:400;">{!! nl2br(e($c->notes)) !!}</div>
            </div>
        @endif
    </div>

    {{-- KPIs --}}
    <div class="kpi-grid">
        <div class="kpi-card"><div class="kpi-label">Total Views</div><div class="kpi-figure num">{{ number_format($summary['total_views']) }}</div></div>
        <div class="kpi-card"><div class="kpi-label">Completed</div><div class="kpi-figure num">{{ number_format($summary['completion_count']) }}</div></div>
        <div class="kpi-card"><div class="kpi-label">Skipped</div><div class="kpi-figure num">{{ number_format($summary['skip_count']) }}</div></div>
        <div class="kpi-card"><div class="kpi-label">Total Watch Time</div><div class="kpi-figure num">{{ $summary['formatted_watch_time'] }}</div></div>
    </div>

    {{-- Engagement + View history --}}
    <div class="grid-2">
        <div class="card">
            <h2 class="card-title">Engagement</h2>
            <p style="font-size:13px;color:var(--text-2);margin-bottom:14px;">
                Interest Level: <span class="badge {{ $levelClass }}">{{ $summary['engagement_level'] }}</span>
            </p>
            <div class="muted">Unique Locations</div>
            @if(empty($summary['unique_locations']))
                <div class="val" style="font-weight:400;">—</div>
            @else
                <ul class="loc-list">
                    @foreach($summary['unique_locations'] as $loc)
                        <li>{{ $loc['city'] }}, {{ $loc['region'] }}, {{ $loc['country'] }}</li>
                    @endforeach
                </ul>
            @endif
        </div>

        <div class="card">
            <h2 class="card-title">View History</h2>
            <table class="crm-table">
                <thead>
                    <tr><th class="num-col">#</th><th>When</th><th>Location</th><th>Device</th></tr>
                </thead>
                <tbody>
                    @forelse($summary['views'] as $v)
                        <tr>
                            <td class="num-cell">{{ (int) $v->view_number }}</td>
                            <td>{{ $v->viewed_at ? \Carbon\Carbon::parse($v->viewed_at)->format('M j, Y g:i A') : '—' }}</td>
                            <td>{{ $v->city }}, {{ $v->country }}</td>
                            <td>{{ $v->browser }} / {{ $v->os }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="crm-empty">No views yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Retention heatmap --}}
    <div class="card">
        <div class="heatmap-title">Video Retention Heatmap</div>
        <div class="heatmap-subtitle">Aggregated watch intensity across all views. Hover a bar to see the time range.</div>
        @if($heatmap['duration'] <= 0)
            <p style="color:var(--text-3);font-size:13px;">No heatmap data available yet. Data is collected as the customer watches the video.</p>
        @else
            <div class="heatmap-bars">
                @foreach($heatmap['data'] as $b)
                    <div class="heatmap-bar"
                         style="height: {{ max(4, $b['intensity']) }}%; opacity: {{ 0.15 + ($b['intensity'] / 100 * 0.85) }};"
                         title="{{ $fmt::formatDuration($b['start']) }} - {{ $fmt::formatDuration($b['end']) }} · {{ number_format($b['intensity'], 1) }}% watched"></div>
                @endforeach
            </div>
            <div class="heatmap-axis">
                <span>0:00</span>
                <span>{{ $fmt::formatDuration($heatmap['duration'] / 2) }}</span>
                <span>{{ $fmt::formatDuration($heatmap['duration']) }}</span>
            </div>
            <div class="heatmap-legend">
                <span>Low</span><div class="heatmap-legend-bar"></div><span>High watch time</span>
            </div>
        @endif
    </div>

    {{-- Watch sessions --}}
    <div class="card">
        <h2 class="card-title">Watch Sessions</h2>
        <table class="crm-table">
            <thead>
                <tr><th>Started</th><th>Duration</th><th class="num-col">Progress</th><th>Completed</th><th>Skipped</th></tr>
            </thead>
            <tbody>
                @forelse($summary['sessions'] as $s)
                    <tr>
                        <td>{{ $s->created_at ? \Carbon\Carbon::parse($s->created_at)->format('M j, Y g:i A') : '—' }}</td>
                        <td>{{ $fmt::formatDuration((float) $s->watch_duration) }}</td>
                        <td class="num-cell">{{ round((float) $s->watch_percentage, 1) }}%</td>
                        <td>{!! $s->completed ? '<span class="badge badge-success">Yes</span>' : '—' !!}</td>
                        <td>{!! $s->skipped ? '<span class="badge badge-warning">Yes</span>' : '—' !!}</td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="crm-empty">No watch sessions recorded yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<script>
function crmCopyLink() {
    var input = document.getElementById('copyLink');
    input.select();
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(input.value);
    } else {
        document.execCommand('copy');
    }
}
</script>
</x-layouts.app>
