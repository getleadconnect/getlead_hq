<?php

namespace App\Http\Controllers\Crm;

use App\Http\Controllers\Controller;
use App\Support\CrmTelegram;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * CRM › Settings.
 *
 * Ported from crmdemo/admin/settings.php. Landing/branding + Telegram config is
 * stored in the shared `settings` table (key/value). Query-builder only — the
 * watch page (WatchController) reads the very same keys, so saves take effect
 * immediately on the customer-facing demo page.
 */
class SettingsController extends Controller
{
    /** Defaults mirror crmdemo's installer. */
    private const DEFAULTS = [
        'company_name'        => 'GetLead',
        'video_path'          => 'DemoFilmV2.mp4',
        'page_title'          => 'Your Demo',
        'welcome_message'     => "Here's a quick look at how {company} can help. Take a look whenever you're ready.",
        'demo_highlights'     => "A clear walkthrough of the product\nSee it in action in about 2 minutes\nReal features, real results — no fluff",
        'contact_whatsapp'    => '',
        'telegram_bot_token'  => '',
        'telegram_chat_id'    => '',
    ];

    private function requireAdmin(): void
    {
        if (! in_array(Auth::guard('staff')->user()->role, ['admin', 'secretary'], true)) {
            abort(403);
        }
    }

    public function index()
    {
        $this->requireAdmin();

        $settings = [];
        foreach (self::DEFAULTS as $key => $default) {
            $settings[$key] = $this->get($key, $default);
        }

        return view('crm.settings', [
            'settings'         => $settings,
            'telegramReady'    => CrmTelegram::isConfigured(),
        ]);
    }

    /** Save landing-page & branding settings. */
    public function updateLanding(Request $request)
    {
        $this->requireAdmin();

        $data = $request->validate([
            'company_name'     => ['required', 'string', 'max:255'],
            'video_path'       => ['required', 'string', 'max:2048'],
            'page_title'       => ['required', 'string', 'max:255'],
            'welcome_message'  => ['nullable', 'string'],
            'demo_highlights'  => ['nullable', 'string'],
            'contact_whatsapp' => ['nullable', 'string', 'max:30'],
        ]);

        foreach ($data as $key => $value) {
            $this->set($key, trim((string) $value));
        }

        return redirect()->route('crm.settings')->with('success', 'Landing page settings saved.');
    }

    /** Save Telegram bot token + chat id. */
    public function updateTelegram(Request $request)
    {
        $this->requireAdmin();

        $data = $request->validate([
            'telegram_bot_token' => ['nullable', 'string', 'max:255'],
            'telegram_chat_id'   => ['nullable', 'string', 'max:255'],
        ]);

        $this->set('telegram_bot_token', trim((string) ($data['telegram_bot_token'] ?? '')));
        $this->set('telegram_chat_id', trim((string) ($data['telegram_chat_id'] ?? '')));

        return redirect()->route('crm.settings')->with('success', 'Telegram settings saved.');
    }

    /** Send a test Telegram message using the saved config. */
    public function sendTest()
    {
        $this->requireAdmin();

        $company = $this->get('company_name', self::DEFAULTS['company_name']);
        $ok = CrmTelegram::send("✅ Test notification from {$company}\nEverything is configured correctly!");

        return redirect()->route('crm.settings')->with(
            $ok ? 'test_ok' : 'test_fail',
            $ok ? 'Test message sent.' : 'Failed to send test. Check the token and chat ID, then save before testing.'
        );
    }

    // ── settings table helpers ─────────────────────────────────────

    private function get(string $key, string $default = ''): string
    {
        $value = DB::table('settings')->where('key', $key)->value('value');
        return $value !== null && $value !== '' ? (string) $value : $default;
    }

    private function set(string $key, string $value): void
    {
        if (DB::table('settings')->where('key', $key)->exists()) {
            DB::table('settings')->where('key', $key)->update(['value' => $value, 'updated_at' => now()]);
        } else {
            DB::table('settings')->insert(['key' => $key, 'value' => $value, 'created_at' => now(), 'updated_at' => now()]);
        }
    }
}
