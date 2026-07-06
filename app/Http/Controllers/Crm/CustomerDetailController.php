<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

/**
 * CRM › Customer Detail.
 *
 * Ported from crmdemo/admin/customer-detail.php + the summary/heatmap helpers
 * in includes/tracker.php. Query-builder only (no Eloquent models).
 */
class CustomerDetailController extends Controller
{
    private const HEATMAP_BUCKETS = 50;

    public function show(int $customer)
    {
        $summary = $this->customerSummary($customer);

        if (empty($summary)) {
            return redirect()->route('crm.customers')->with('success', 'Customer not found.');
        }

        $heatmap       = $this->buildHeatmap($customer, $summary['sessions']);
        $customerLink  = url('/crm/watch') . '?token=' . urlencode($summary['customer']->token);
        $levelClass    = match ($summary['engagement_level']) {
            'High'   => 'badge-success',
            'Medium' => 'badge-warning',
            default  => 'badge-neutral',
        };

        return view('crm.customers.detail', compact('summary', 'heatmap', 'customerLink', 'levelClass'));
    }

    // ── Summary ────────────────────────────────────────────────────

    /** Aggregate a customer's views, sessions, locations and engagement level. */
    private function customerSummary(int $customerId): array
    {
        $customer = DB::table('crm_customers')->where('id', $customerId)->first();
        if (! $customer) {
            return [];
        }

        $views = DB::table('crm_customer_views')
            ->where('customer_id', $customerId)
            ->orderByDesc('viewed_at')
            ->get();

        $viewIds  = $views->pluck('id')->all();
        $sessions = empty($viewIds)
            ? collect()
            : DB::table('crm_watch_sessions')->whereIn('view_id', $viewIds)->orderByDesc('created_at')->get();

        $completionCount = $sessions->where('completed', 1)->count();
        $skipCount       = $sessions->where('skipped', 1)->count();
        $totalWatchTime  = (float) $sessions->sum('watch_duration');
        $totalViews      = $views->count();

        // Unique locations keyed by city|country.
        $uniqueLocations = [];
        foreach ($views as $v) {
            $key = ($v->city ?? '') . '|' . ($v->country ?? '');
            if ($key !== '|') {
                $uniqueLocations[$key] = ['city' => $v->city, 'region' => $v->region, 'country' => $v->country];
            }
        }

        $engagementLevel = 'Low';
        if ($completionCount > 0 && $totalWatchTime > 120) {
            $engagementLevel = 'High';
        } elseif ($completionCount > 0 || $totalWatchTime > 60 || $totalViews > 1) {
            $engagementLevel = 'Medium';
        }

        return [
            'customer'             => $customer,
            'total_views'          => $totalViews,
            'unique_locations'     => array_values($uniqueLocations),
            'completion_count'     => $completionCount,
            'skip_count'           => $skipCount,
            'total_watch_time'     => $totalWatchTime,
            'formatted_watch_time' => $this->formatDuration($totalWatchTime),
            'engagement_level'     => $engagementLevel,
            'views'                => $views,
            'sessions'             => $sessions,
        ];
    }

    // ── Heatmap ────────────────────────────────────────────────────

    /** Build retention heatmap buckets (0-100 intensity) from watched segments. */
    private function buildHeatmap(int $customerId, $sessions): array
    {
        $segments = DB::table('crm_watch_segments as seg')
            ->join('crm_watch_sessions as ws', 'seg.session_id', '=', 'ws.id')
            ->join('crm_customer_views as cv', 'ws.view_id', '=', 'cv.id')
            ->where('cv.customer_id', $customerId)
            ->orderBy('cv.view_number')
            ->orderBy('seg.start_time')
            ->get(['seg.start_time', 'seg.end_time', 'ws.video_duration']);

        $duration = (float) collect($sessions)->max('video_duration');
        if ($duration <= 0) {
            $duration = (float) $segments->max('end_time');
        }

        if ($duration <= 0) {
            return ['duration' => 0, 'buckets' => self::HEATMAP_BUCKETS, 'data' => []];
        }

        $buckets    = self::HEATMAP_BUCKETS;
        $bucketSize = $duration / $buckets;
        $watched    = array_fill(0, $buckets, 0.0);

        foreach ($segments as $seg) {
            $start = (float) $seg->start_time;
            $end   = (float) $seg->end_time;
            $from  = max(0, (int) floor($start / $bucketSize));
            $to    = min($buckets - 1, (int) floor($end / $bucketSize));

            for ($i = $from; $i <= $to; $i++) {
                $overlapStart = max($start, $i * $bucketSize);
                $overlapEnd   = min($end, ($i + 1) * $bucketSize);
                $watched[$i] += max(0, $overlapEnd - $overlapStart);
            }
        }

        $data = [];
        for ($i = 0; $i < $buckets; $i++) {
            $intensity = min(100, ($watched[$i] / $bucketSize) * 100);
            $data[] = [
                'start'     => round($i * $bucketSize, 1),
                'end'       => round(($i + 1) * $bucketSize, 1),
                'intensity' => round($intensity, 1),
            ];
        }

        return ['duration' => round($duration, 1), 'buckets' => $buckets, 'data' => $data];
    }

    // ── Formatting ─────────────────────────────────────────────────

    /** Seconds → mm:ss. */
    public static function formatDuration(?float $seconds): string
    {
        if ($seconds === null) {
            return '—';
        }
        $total = (int) round($seconds);
        return sprintf('%02d:%02d', intdiv($total, 60), $total % 60);
    }
}
