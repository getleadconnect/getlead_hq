<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Support\CrmTelegram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * CRM › Public Watch page + tracking API.
 *
 * Ported from crmdemo/watch/index.php + api/track.php + includes/tracker.php.
 * Customer-facing (NO auth) — identity is the unguessable per-customer token.
 * Query-builder only (no Eloquent models).
 */
class WatchController extends Controller
{
    /** Default demo video (lives in public/). */
    private const DEFAULT_VIDEO = 'DemoFilmV2.mp4';

    // ── Landing page ───────────────────────────────────────────────

    public function show(Request $request)
    {
        $token = trim((string) $request->query('token', ''));

        if ($token === '') {
            abort(404, 'Link not found.');
        }

        $customer = DB::table('crm_customers')->where('token', $token)->first();
        if (! $customer) {
            abort(404, 'This demo link is invalid or has expired.');
        }

        // Record the visit and open an engagement session.
        $view      = $this->recordView($customer->id, $request);
        $sessionId = DB::table('crm_watch_sessions')->insertGetId([
            'view_id'    => $view->id,
            'created_at' => now(),
        ]);

        // Notify the team that the demo was opened (best-effort, only if configured).
        CrmTelegram::notifyDemoViewed($customer, $view, url('/crm/watch') . '?token=' . urlencode($token));

        // Personalisation.
        $company  = $this->companyName();
        $first    = Str::of($customer->name)->trim()->explode(' ')->first() ?: $customer->name;
        $videoUrl = $this->videoUrl();

        $pageTitle = $this->personalise($this->setting('page_title', 'Your Demo'), $customer, $company);
        $welcome   = $this->personalise(
            $this->setting('welcome_message', "Here's a quick look at how {company} can help. Take a look whenever you're ready."),
            $customer,
            $company
        );

        $repName     = (string) (DB::table('staff')->where('id', $customer->created_by)->value('name') ?? '');
        $repInitials = collect(preg_split('/\s+/', trim($repName)))
            ->filter()
            ->take(2)
            ->map(fn ($p) => mb_strtoupper(mb_substr($p, 0, 1)))
            ->implode('');

        $highlightsRaw = $this->setting('demo_highlights', "A clear walkthrough of the product\nSee it in action in about 2 minutes\nReal features, real results — no fluff");
        $highlights    = collect(explode("\n", $highlightsRaw))->map(fn ($h) => trim($h))->filter()->values()->all();

        $contactWhatsapp = $this->whatsappNumber($this->setting('contact_whatsapp', ''));
        $ctaUrl = $contactWhatsapp !== ''
            ? 'https://wa.me/' . $contactWhatsapp . '?text=' . rawurlencode("Hi! I just watched my personalised {$company} demo and I'd love to know more. 😊")
            : '';

        return view('crm.watch', compact(
            'customer', 'token', 'sessionId', 'videoUrl', 'company', 'first',
            'pageTitle', 'welcome', 'repName', 'repInitials', 'highlights', 'ctaUrl'
        ));
    }

    // ── Tracking API ───────────────────────────────────────────────

    public function track(Request $request)
    {
        $raw     = $request->input('data', $request->getContent());
        $payload = is_array($raw) ? $raw : json_decode((string) $raw, true);

        if (! $payload || empty($payload['sessionId']) || empty($payload['token']) || empty($payload['eventType'])) {
            return response()->json(['success' => false, 'error' => 'Invalid payload'], 400);
        }

        $sessionId = (int) $payload['sessionId'];
        $token     = trim((string) $payload['token']);
        $eventType = (string) $payload['eventType'];

        // Verify the session belongs to the customer identified by the token.
        $session = DB::table('crm_watch_sessions as ws')
            ->join('crm_customer_views as cv', 'ws.view_id', '=', 'cv.id')
            ->join('crm_customers as c', 'cv.customer_id', '=', 'c.id')
            ->where('ws.id', $sessionId)
            ->select('ws.*', 'cv.customer_id', 'c.token')
            ->first();

        if (! $session || $session->token !== $token) {
            return response()->json(['success' => false, 'error' => 'Invalid session'], 403);
        }

        try {
            $this->saveEngagementEvent($sessionId, $eventType, $payload);

            // Learn the true video duration once it's known.
            $videoDuration = (float) ($payload['videoDuration'] ?? 0);
            if ($videoDuration > 0 && (float) ($session->video_duration ?? 0) <= 0) {
                DB::table('crm_watch_sessions')->where('id', $sessionId)->update(['video_duration' => $videoDuration]);
                $session->video_duration = $videoDuration;
            }

            // Persist watched segments (heatmap data).
            if (in_array($eventType, ['segments', 'session_end'], true) && ! empty($payload['segments']) && is_array($payload['segments'])) {
                $this->saveWatchSegments($sessionId, $payload['segments']);
            }

            // Roll up the session summary on progress milestones.
            if (in_array($eventType, ['milestone', 'complete', 'session_end', 'heartbeat'], true)) {
                $completed = ! empty($payload['completed']) || $eventType === 'complete' ? 1 : 0;
                DB::table('crm_watch_sessions')->where('id', $sessionId)->update([
                    'video_duration'   => (float) ($payload['videoDuration'] ?? $session->video_duration ?? 0),
                    'watch_duration'   => (float) ($payload['watchDuration'] ?? 0),
                    'watch_percentage' => (float) ($payload['watchPercentage'] ?? 0),
                    'completed'        => $completed,
                    'skipped'          => ! empty($payload['skipped']) ? 1 : 0,
                    'ended_at'         => in_array($eventType, ['complete', 'session_end'], true) ? now() : null,
                ]);

                // Notify once, when the customer actually finishes the video.
                if ($eventType === 'complete') {
                    $customer    = DB::table('crm_customers')->where('id', $session->customer_id)->first();
                    $freshSession = DB::table('crm_watch_sessions')->where('id', $sessionId)->first();
                    if ($customer && $freshSession) {
                        CrmTelegram::notifyDemoCompleted($customer, $freshSession);
                    }
                }
            }

            return response()->json(['success' => true]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['success' => false, 'error' => 'Server error'], 500);
        }
    }

    // ── View recording ─────────────────────────────────────────────

    private function recordView(int $customerId, Request $request): object
    {
        $ip = $this->clientIp($request);
        $ua = $this->parseUserAgent((string) $request->userAgent());
        $loc = $this->locationFromIp($ip);

        DB::table('crm_customers')->where('id', $customerId)->update([
            'views_count'    => DB::raw('views_count + 1'),
            'last_viewed_at' => now(),
        ]);

        $viewNumber = (int) DB::table('crm_customer_views')->where('customer_id', $customerId)->count() + 1;

        $viewId = DB::table('crm_customer_views')->insertGetId([
            'customer_id' => $customerId,
            'view_number' => $viewNumber,
            'ip_address'  => $ip,
            'browser'     => $ua['browser'],
            'os'          => $ua['os'],
            'device'      => $ua['device'],
            'city'        => $loc['city'],
            'region'      => $loc['region'],
            'country'     => $loc['country'],
            'latitude'    => $loc['lat'],
            'longitude'   => $loc['lon'],
            'referrer'    => (string) $request->headers->get('referer', ''),
            'viewed_at'   => now(),
        ]);

        return DB::table('crm_customer_views')->where('id', $viewId)->first();
    }

    private function saveEngagementEvent(int $sessionId, string $eventType, array $payload): void
    {
        DB::table('crm_engagement_events')->insert([
            'session_id' => $sessionId,
            'event_type' => $eventType,
            'event_time' => (float) ($payload['currentTime'] ?? 0),
            'payload'    => json_encode($payload),
            'created_at' => now(),
        ]);
    }

    private function saveWatchSegments(int $sessionId, array $segments): void
    {
        $rows = [];
        foreach ($segments as $seg) {
            $start = (float) ($seg['start'] ?? 0);
            $end   = (float) ($seg['end'] ?? 0);
            if ($end > $start && $end > 0) {
                $rows[] = ['session_id' => $sessionId, 'start_time' => $start, 'end_time' => $end, 'created_at' => now()];
            }
        }
        if ($rows) {
            DB::table('crm_watch_segments')->insert($rows);
        }
    }

    // ── Helpers (ported from functions.php) ────────────────────────

    private function videoUrl(): string
    {
        $path = trim((string) $this->setting('video_path', self::DEFAULT_VIDEO));
        if ($path === '') {
            $path = self::DEFAULT_VIDEO;
        }
        return preg_match('#^https?://#i', $path) ? $path : asset(ltrim($path, '/'));
    }

    private function setting(string $key, string $default = ''): string
    {
        $value = DB::table('settings')->where('key', $key)->value('value');
        return $value !== null && $value !== '' ? (string) $value : $default;
    }

    private function companyName(): string
    {
        $name = trim($this->setting('company_name', ''));
        return $name !== '' ? $name : 'GetLead';
    }

    private function personalise(string $text, object $customer, string $company): string
    {
        $first = Str::of($customer->name)->trim()->explode(' ')->first() ?: $customer->name;
        return strtr($text, ['{name}' => $customer->name, '{first_name}' => $first, '{company}' => $company]);
    }

    private function whatsappNumber(string $mobile): string
    {
        $digits = preg_replace('/\D+/', '', $mobile);
        return strlen($digits) === 10 ? '91' . $digits : $digits;
    }

    private function clientIp(Request $request): string
    {
        foreach (['CF-Connecting-IP', 'X-Forwarded-For'] as $header) {
            $val = $request->headers->get($header);
            if ($val) {
                $ip = trim(explode(',', $val)[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        return $request->ip() ?: '0.0.0.0';
    }

    private function parseUserAgent(string $ua): array
    {
        $browser = 'Unknown';
        $os      = 'Unknown';
        $device  = 'Desktop';

        if (preg_match('/Edg\//', $ua))              $browser = 'Edge';
        elseif (preg_match('/Chrome\//', $ua))       $browser = 'Chrome';
        elseif (preg_match('/Firefox\//', $ua))      $browser = 'Firefox';
        elseif (preg_match('/Safari\//', $ua))       $browser = 'Safari';
        elseif (preg_match('/Opera|OPR\//', $ua))    $browser = 'Opera';

        if (preg_match('/Windows/i', $ua))                       $os = 'Windows';
        elseif (preg_match('/Macintosh|Mac OS/i', $ua))          $os = 'macOS';
        elseif (preg_match('/Android/i', $ua))                   $os = 'Android';
        elseif (preg_match('/iPhone|iPad|iPod/i', $ua))          $os = 'iOS';
        elseif (preg_match('/Linux/i', $ua))                     $os = 'Linux';

        if (preg_match('/Mobile|Android|iPhone|iPad|iPod/i', $ua)) $device = 'Mobile';

        return compact('browser', 'os', 'device');
    }

    private function locationFromIp(string $ip): array
    {
        $default = ['city' => 'Unknown', 'region' => 'Unknown', 'country' => 'Unknown', 'lat' => null, 'lon' => null];

        if (in_array($ip, ['127.0.0.1', '0.0.0.0', '::1'], true)) {
            $default['city'] = 'Local';
            return $default;
        }

        try {
            $url = 'http://ip-api.com/json/' . urlencode($ip) . '?fields=status,city,regionName,country,lat,lon';
            $ctx = stream_context_create(['http' => ['timeout' => 2, 'user_agent' => 'GetLeadCRM/1.0']]);
            $response = @file_get_contents($url, false, $ctx);
            if ($response) {
                $data = json_decode($response, true);
                if (($data['status'] ?? '') === 'success') {
                    return [
                        'city'    => $data['city'] ?? 'Unknown',
                        'region'  => $data['regionName'] ?? 'Unknown',
                        'country' => $data['country'] ?? 'Unknown',
                        'lat'     => $data['lat'] ?? null,
                        'lon'     => $data['lon'] ?? null,
                    ];
                }
            }
        } catch (\Throwable) {
            // best-effort only
        }

        return $default;
    }
}
