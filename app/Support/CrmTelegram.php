<?php

namespace App\Support;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;

/**
 * CRM Telegram notifications.
 *
 * Ported from crmdemo/includes/telegram.php. Bot token + chat id come from the
 * `settings` table (managed on CRM › Settings). All sends are best-effort and
 * never throw to the caller — a missing/invalid config simply returns false.
 */
class CrmTelegram
{
    /** True when a bot token and chat id are both configured. */
    public static function isConfigured(): bool
    {
        return self::setting('telegram_bot_token') !== '' && self::setting('telegram_chat_id') !== '';
    }

    /** Send a raw HTML message to the configured chat. */
    public static function send(string $message): bool
    {
        $token  = self::setting('telegram_bot_token');
        $chatId = self::setting('telegram_chat_id');

        if ($token === '' || $chatId === '') {
            return false;
        }

        try {
            $response = Http::timeout(10)->asForm()->post(
                "https://api.telegram.org/bot{$token}/sendMessage",
                [
                    'chat_id'                  => $chatId,
                    'text'                     => $message,
                    'parse_mode'               => 'HTML',
                    'disable_web_page_preview' => true,
                ]
            );

            return $response->ok() && (bool) ($response->json('ok') ?? false);
        } catch (\Throwable $e) {
            report($e);
            return false;
        }
    }

    /** Notify that a customer opened (or revisited) their demo. */
    public static function notifyDemoViewed(object $customer, object $view, string $watchUrl): bool
    {
        if (! self::isConfigured()) {
            return false;
        }

        $isFirst = (int) $view->view_number === 1;
        $action  = $isFirst ? 'Viewed the demo video for the first time' : 'Revisited the demo video';
        $e       = fn ($v) => htmlspecialchars((string) $v, ENT_NOQUOTES, 'UTF-8');

        $msg  = "🔔 <b>CRM Demo Alert</b>\n\n";
        $msg .= "👤 <b>{$e($customer->name)}</b>\n";
        $msg .= "📱 {$e($customer->mobile)}\n";
        $msg .= "📝 {$action}\n";
        $msg .= "🔢 View count: {$view->view_number}\n\n";
        $msg .= "📍 <b>Location</b>\n";
        $msg .= "City: {$e($view->city)}\n";
        $msg .= "Region: {$e($view->region)}\n";
        $msg .= "Country: {$e($view->country)}\n\n";
        $msg .= "💻 <b>Device</b>\n";
        $msg .= "Browser: {$e($view->browser)}\n";
        $msg .= "OS: {$e($view->os)}\n";
        $msg .= "Device: {$e($view->device)}\n";
        $msg .= "IP: {$e($view->ip_address)}\n\n";
        $msg .= "🕒 " . self::fmtDate($view->viewed_at) . "\n";
        $msg .= "🔗 {$e($watchUrl)}";

        return self::send($msg);
    }

    /** Notify that a customer finished watching the demo. */
    public static function notifyDemoCompleted(object $customer, object $session): bool
    {
        if (! self::isConfigured()) {
            return false;
        }

        $e = fn ($v) => htmlspecialchars((string) $v, ENT_NOQUOTES, 'UTF-8');

        $msg  = "🎉 <b>Demo Completed</b>\n\n";
        $msg .= "👤 <b>{$e($customer->name)}</b>\n";
        $msg .= "📱 {$e($customer->mobile)}\n";
        $msg .= "⏱ Watched: " . self::fmtDuration((float) ($session->watch_duration ?? 0)) . "\n";
        $msg .= "🕒 " . self::fmtDate($session->ended_at ?? null);

        return self::send($msg);
    }

    // ── helpers ────────────────────────────────────────────────────

    private static function setting(string $key): string
    {
        return trim((string) (DB::table('settings')->where('key', $key)->value('value') ?? ''));
    }

    private static function fmtDate(?string $ts): string
    {
        return $ts ? Carbon::parse($ts)->format('M j, Y g:i A') : '—';
    }

    private static function fmtDuration(?float $seconds): string
    {
        if ($seconds === null) {
            return '—';
        }
        $total = (int) round($seconds);
        return sprintf('%02d:%02d', intdiv($total, 60), $total % 60);
    }
}
