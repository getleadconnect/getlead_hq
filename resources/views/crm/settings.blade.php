<x-layouts.app title="CRM Settings">
@push('styles')
<style>
    .crm-wrap { padding: 24px 28px 48px; width: 100%; }
    .crm-head { margin-bottom: 20px; }
    .crm-eyebrow { font-size: 11px; font-weight: 600; letter-spacing: .09em; text-transform: uppercase; color: var(--brand-red); }
    .crm-head h1 { font-size: 24px; font-weight: 600; letter-spacing: -.5px; color: var(--text-1); margin-top: 4px; }
    .crm-head p { font-size: 13px; color: var(--text-2); margin-top: 4px; max-width: 620px; }

    .btn { display: inline-flex; align-items: center; gap: 6px; padding: 9px 15px; border-radius: var(--radius-sm); font-family: inherit; font-size: 13px; font-weight: 500; cursor: pointer; border: 1px solid transparent; text-decoration: none; }
    .btn-primary { background: var(--brand-red); color: #fff; }
    .btn-primary:hover { background: var(--brand-red-dark); }
    .btn-secondary { background: var(--bg-card); color: var(--text-1); border-color: var(--border); }
    .btn-secondary:hover { border-color: var(--text-3); }

    .flash { padding: 10px 14px; border-radius: var(--radius-sm); font-size: 13px; margin-bottom: 16px; }
    .flash-success { background: var(--success-soft); color: var(--success-text); border: 1px solid var(--success-border); }
    .flash-error { background: var(--danger-soft); color: var(--danger-text); border: 1px solid var(--danger-border); }

    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; align-items: start; }
    @media (max-width: 900px) { .grid-2 { grid-template-columns: 1fr; } }

    .card { background: var(--bg-card); border: 1px solid var(--border); border-radius: var(--radius-lg); padding: 22px; }
    .card-title { font-size: 15px; font-weight: 600; color: var(--text-1); margin-bottom: 4px; }
    .card-sub { font-size: 12.5px; color: var(--text-2); margin-bottom: 16px; }

    .field { display: flex; flex-direction: column; gap: 6px; margin-top: 16px; }
    .field:first-of-type { margin-top: 0; }
    .field-label { font-size: 12.5px; font-weight: 500; color: var(--text-2); }
    .hint { font-size: 11.5px; color: var(--text-3); margin-top: 4px; }
    .hint code { background: var(--bg-neutral); padding: 1px 5px; border-radius: 4px; font-size: 11px; }
    .input, textarea.input { width: 100%; border: 1px solid var(--border); border-radius: var(--radius-sm); padding: 9px 11px; font-family: inherit; font-size: 13px; color: var(--text-1); background: var(--bg-card); outline: none; }
    .input:focus, textarea.input:focus { border-color: var(--brand-red); box-shadow: 0 0 0 3px var(--brand-red-soft); }
    textarea.input { min-height: 74px; resize: vertical; }

    .divider { border: none; border-top: 1px solid var(--border); margin: 22px 0; }
    .status-pill { display: inline-flex; align-items: center; gap: 6px; font-size: 11.5px; font-weight: 600; padding: 3px 10px; border-radius: var(--radius-pill); }
    .status-on { background: var(--success-soft); color: var(--success-text); border: 1px solid var(--success-border); }
    .status-off { background: var(--bg-neutral); color: var(--text-2); border: 1px solid var(--border); }
</style>
@endpush

<div class="crm-wrap">
    <div class="crm-head">
        <div class="crm-eyebrow">Admin</div>
        <h1>CRM Settings</h1>
        <p>Configure the demo video, the customer-facing landing page, and Telegram notifications. Changes apply immediately to every customer demo link.</p>
    </div>

    @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif
    @if(session('test_ok'))
        <div class="flash flash-success">{{ session('test_ok') }}</div>
    @endif
    @if(session('test_fail'))
        <div class="flash flash-error">{{ session('test_fail') }}</div>
    @endif
    @if(isset($errors) && $errors->any())
        <div class="flash flash-error">{{ $errors->first() }}</div>
    @endif

    <div class="grid-2">
        {{-- Landing page & branding --}}
        <div class="card">
            <div class="card-title">Landing Page &amp; Branding</div>
            <div class="card-sub">Shown to customers on the demo watch page.</div>

            <form method="POST" action="{{ route('crm.settings.landing') }}">
                @csrf
                <label class="field">
                    <span class="field-label">Company / Brand Name</span>
                    <input type="text" name="company_name" class="input" required value="{{ old('company_name', $settings['company_name']) }}">
                    <span class="hint">Used on the demo page and in WhatsApp / email messages.</span>
                </label>

                <label class="field">
                    <span class="field-label">Video Path or URL</span>
                    <input type="text" name="video_path" class="input" required value="{{ old('video_path', $settings['video_path']) }}">
                    <span class="hint">A file in <code>public/</code> (e.g. <code>DemoFilmV2.mp4</code>) or a full <code>https://</code> URL.</span>
                </label>

                <label class="field">
                    <span class="field-label">Landing Page Title</span>
                    <input type="text" name="page_title" class="input" required value="{{ old('page_title', $settings['page_title']) }}">
                </label>

                <label class="field">
                    <span class="field-label">Welcome Message</span>
                    <textarea name="welcome_message" class="input">{{ old('welcome_message', $settings['welcome_message']) }}</textarea>
                    <span class="hint">Tokens: <code>{first_name}</code>, <code>{name}</code>, <code>{company}</code>.</span>
                </label>

                <label class="field">
                    <span class="field-label">“What you'll see” Highlights</span>
                    <textarea name="demo_highlights" class="input" rows="3">{{ old('demo_highlights', $settings['demo_highlights']) }}</textarea>
                    <span class="hint">One per line. Shown as bullet cards under the video.</span>
                </label>

                <label class="field">
                    <span class="field-label">Contact WhatsApp Number</span>
                    <input type="text" name="contact_whatsapp" class="input" value="{{ old('contact_whatsapp', $settings['contact_whatsapp']) }}" placeholder="e.g. 919876543210">
                    <span class="hint">Optional. Powers the “Chat with us” button. Include country code.</span>
                </label>

                <div style="margin-top:20px;">
                    <button type="submit" class="btn btn-primary">Save Landing Settings</button>
                </div>
            </form>
        </div>

        {{-- Telegram --}}
        <div class="card">
            <div class="card-title">
                Telegram Notifications
                @if($telegramReady)
                    <span class="status-pill status-on" style="margin-left:6px;">● Configured</span>
                @else
                    <span class="status-pill status-off" style="margin-left:6px;">○ Not set</span>
                @endif
            </div>
            <div class="card-sub">Get an alert when a customer opens or completes their demo.</div>

            <form method="POST" action="{{ route('crm.settings.telegram') }}">
                @csrf
                <label class="field">
                    <span class="field-label">Bot Token</span>
                    <input type="text" name="telegram_bot_token" class="input" value="{{ old('telegram_bot_token', $settings['telegram_bot_token']) }}" autocomplete="off">
                    <span class="hint">From @BotFather, e.g. <code>123456:ABC...</code></span>
                </label>

                <label class="field">
                    <span class="field-label">Chat ID</span>
                    <input type="text" name="telegram_chat_id" class="input" value="{{ old('telegram_chat_id', $settings['telegram_chat_id']) }}" autocomplete="off">
                    <span class="hint">User ID, group ID, or channel ID.</span>
                </label>

                <div style="margin-top:20px;">
                    <button type="submit" class="btn btn-primary">Save Telegram Settings</button>
                </div>
            </form>

            <hr class="divider">

            <div class="card-title" style="font-size:14px;">Test Notification</div>
            <div class="card-sub">Send a test message to your saved Telegram chat.</div>
            <form method="POST" action="{{ route('crm.settings.test') }}">
                @csrf
                <button type="submit" class="btn btn-secondary">Send Test Message</button>
            </form>
        </div>
    </div>
</div>
</x-layouts.app>
