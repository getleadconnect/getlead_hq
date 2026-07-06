{{-- Share panel shown after a demo link is generated. Ported from crmdemo/includes/share-panel.php --}}
@php $first = \Illuminate\Support\Str::of($name)->trim()->explode(' ')->first() ?: $name; @endphp
<div class="share-panel">
    <div class="share-panel-head">
        <span class="share-spark">🎉</span>
        <div>
            <div class="share-title">Demo link ready for {{ $first }}!</div>
            <div class="share-sub">Send it over with a personalised message — pick a channel below.</div>
        </div>
    </div>

    <div class="link-box">
        <input type="text" id="shareLink" value="{{ $generatedLink }}" readonly>
        <button type="button" class="btn btn-secondary btn-sm" data-copy="shareLink">Copy link</button>
    </div>

    <div class="share-actions">
        <a class="btn btn-primary" href="{{ $share['whatsapp_url'] }}" target="_blank" rel="noopener">💬 Send on WhatsApp</a>
        <a class="btn btn-secondary" href="{{ $share['email_url'] }}">✉️ Send via Email</a>
    </div>

    <details class="share-preview">
        <summary>Preview &amp; copy the messages</summary>

        <div class="share-field">
            <div class="share-field-label">
                <span>WhatsApp message</span>
                <button type="button" class="btn btn-ghost btn-sm" data-copy="waMsg">Copy</button>
            </div>
            <textarea id="waMsg" class="input" rows="7" readonly>{{ $share['whatsapp'] }}</textarea>
        </div>

        <div class="share-field">
            <div class="share-field-label">
                <span>Email subject</span>
                <button type="button" class="btn btn-ghost btn-sm" data-copy="emailSubject">Copy</button>
            </div>
            <input type="text" id="emailSubject" class="input" value="{{ $share['email_subject'] }}" readonly>
        </div>

        <div class="share-field">
            <div class="share-field-label">
                <span>Email body</span>
                <button type="button" class="btn btn-ghost btn-sm" data-copy="emailBody">Copy</button>
            </div>
            <textarea id="emailBody" class="input" rows="9" readonly>{{ $share['email_body'] }}</textarea>
        </div>
    </details>
</div>

<script>
(function () {
    function copyFrom(id, btn) {
        var el = document.getElementById(id);
        if (!el) return;
        var done = function () {
            var label = btn.textContent;
            btn.textContent = 'Copied!';
            setTimeout(function () { btn.textContent = label; }, 1500);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(el.value).then(done).catch(function () { el.select(); document.execCommand('copy'); done(); });
        } else { el.select(); document.execCommand('copy'); done(); }
    }
    document.querySelectorAll('[data-copy]').forEach(function (btn) {
        btn.addEventListener('click', function () { copyFrom(btn.getAttribute('data-copy'), btn); });
    });
})();
</script>
