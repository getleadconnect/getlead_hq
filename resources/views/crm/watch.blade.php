<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <meta name="robots" content="noindex, nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        :root{
            --bg:#0B0F17; --panel:#131926; --panel-2:#1A2130; --border:#232C3D;
            --text:#E7ECF3; --muted:#95A2B8; --dim:#64748B;
            --red:#DC2626; --red-dark:#B91C1C; --red-soft:rgba(220,38,38,.14);
        }
        html,body{height:100%;}
        body{
            font-family:'Poppins',-apple-system,sans-serif;
            background:radial-gradient(1200px 600px at 50% -10%, #172033 0%, var(--bg) 60%);
            color:var(--text); min-height:100vh; -webkit-font-smoothing:antialiased;
        }
        .watch-shell{max-width:920px;margin:0 auto;padding:24px 20px 60px;}

        .watch-topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;}
        .watch-brand{display:flex;align-items:center;gap:10px;font-weight:600;font-size:18px;letter-spacing:-.3px;}
        .watch-brand .logo-dot{width:30px;height:30px;border-radius:8px;background:var(--red);display:flex;align-items:center;justify-content:center;}
        .watch-brand .logo-dot svg{width:16px;height:16px;fill:#fff;}
        .watch-eyebrow{font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:var(--dim);
            border:1px solid var(--border);border-radius:999px;padding:6px 12px;}

        .watch-hero{text-align:center;margin-bottom:26px;}
        .watch-kicker{font-size:13px;color:var(--red);font-weight:500;margin-bottom:8px;}
        .watch-title{font-size:34px;font-weight:600;letter-spacing:-.6px;line-height:1.15;}
        .watch-name{color:var(--red);}
        .watch-lead{font-size:15px;color:var(--muted);margin-top:12px;max-width:560px;margin-left:auto;margin-right:auto;line-height:1.6;}
        .watch-rep{display:inline-flex;align-items:center;gap:10px;margin-top:20px;background:var(--panel);
            border:1px solid var(--border);border-radius:999px;padding:6px 14px 6px 6px;}
        .watch-rep-avatar{width:34px;height:34px;border-radius:50%;background:linear-gradient(135deg,#EF4444,var(--red));
            display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;color:#fff;}
        .watch-rep-meta{display:flex;flex-direction:column;text-align:left;line-height:1.25;}
        .watch-rep-name{font-size:13px;font-weight:500;}
        .watch-rep-role{font-size:11px;color:var(--dim);}

        .watch-stage{margin:8px 0 28px;}
        .video-frame{position:relative;border-radius:16px;overflow:hidden;border:1px solid var(--border);
            background:#000;box-shadow:0 24px 60px rgba(0,0,0,.45);aspect-ratio:16/9;}
        .video-frame video{width:100%;height:100%;display:block;object-fit:contain;background:#000;}

        .v-overlay{position:absolute;pointer-events:none;opacity:0;transition:opacity .4s ease;}
        .v-overlay.is-visible{opacity:1;}
        .v-watermark{top:14px;right:16px;font-size:11px;color:rgba(255,255,255,.55);background:rgba(0,0,0,.35);
            padding:4px 10px;border-radius:999px;backdrop-filter:blur(4px);}
        .v-intro{left:24px;bottom:64px;max-width:60%;}
        .v-intro-pill{display:inline-block;background:var(--red);color:#fff;font-size:11px;font-weight:600;
            padding:3px 10px;border-radius:999px;margin-bottom:8px;}
        .v-intro-title{font-size:22px;font-weight:600;text-shadow:0 2px 12px rgba(0,0,0,.6);}
        .v-intro-body{font-size:13px;color:rgba(255,255,255,.85);margin-top:4px;text-shadow:0 2px 12px rgba(0,0,0,.6);}
        .v-lower{left:24px;bottom:24px;}
        .v-lower-content{display:flex;flex-direction:column;background:rgba(0,0,0,.4);backdrop-filter:blur(6px);
            padding:8px 14px;border-radius:10px;}
        .v-lower-label{font-size:10px;text-transform:uppercase;letter-spacing:.08em;color:rgba(255,255,255,.6);}
        .v-lower-name{font-size:14px;font-weight:500;}

        .watch-highlights{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:28px;}
        @media(max-width:640px){.watch-highlights{grid-template-columns:1fr;}}
        .watch-highlight{display:flex;align-items:center;gap:10px;background:var(--panel);border:1px solid var(--border);
            border-radius:12px;padding:14px 16px;font-size:13px;color:var(--muted);}
        .watch-highlight-tick{width:22px;height:22px;flex-shrink:0;border-radius:50%;background:var(--red-soft);
            color:#F87171;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;}

        .watch-cta{display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;
            background:var(--panel);border:1px solid var(--border);border-radius:16px;padding:24px;margin-bottom:24px;}
        .watch-cta h2{font-size:19px;font-weight:600;}
        .watch-cta p{font-size:13px;color:var(--muted);margin-top:6px;max-width:440px;line-height:1.5;}
        .btn{display:inline-flex;align-items:center;gap:8px;text-decoration:none;font-weight:500;font-size:14px;
            border-radius:10px;padding:12px 20px;cursor:pointer;border:1px solid transparent;white-space:nowrap;}
        .btn-primary{background:var(--red);color:#fff;}
        .btn-primary:hover{background:var(--red-dark);}

        .watch-footer{text-align:center;font-size:12px;color:var(--dim);padding-top:12px;border-top:1px solid var(--border);}
    </style>
</head>
<body>
    <div class="watch-shell">
        <header class="watch-topbar">
            <div class="watch-brand">
                <span class="logo-dot"><svg viewBox="0 0 24 24"><path d="M13 3L4 14h7l-2 7 9-11h-7l2-7z"/></svg></span>
                {{ $company }}
            </div>
            <span class="watch-eyebrow">Personalised demo</span>
        </header>

        <section class="watch-hero">
            <p class="watch-kicker">A quick demo from {{ $company }}</p>
            <h1 class="watch-title">Hi <span class="watch-name">{{ $first }}</span> 👋</h1>
            <p class="watch-lead">{{ $welcome }}</p>

            @if($repName !== '')
                <div class="watch-rep">
                    <div class="watch-rep-avatar">{{ $repInitials ?: 'GL' }}</div>
                    <div class="watch-rep-meta">
                        <span class="watch-rep-name">{{ $repName }}</span>
                        <span class="watch-rep-role">Shared this with you</span>
                    </div>
                </div>
            @endif
        </section>

        <section class="watch-stage">
            <div class="video-frame" id="videoFrame">
                <video id="demoVideo" controls playsinline preload="metadata"
                       data-session-id="{{ $sessionId }}" data-token="{{ $token }}">
                    <source src="{{ $videoUrl }}" type="video/mp4">
                    Your browser does not support the video tag.
                </video>

                <div class="v-overlay v-watermark is-visible" id="vWatermark">Shared with {{ $customer->name }}</div>
                <div class="v-overlay v-intro" id="vIntro">
                    <div class="v-intro-pill">Welcome</div>
                    <h2 class="v-intro-title">Hi {{ $first }} 👋</h2>
                    <p class="v-intro-body">Here's a quick look at how {{ $company }} can help. Watch till the end to see how it fits your workflow.</p>
                </div>
                <div class="v-overlay v-lower" id="vLower">
                    <div class="v-lower-content">
                        <span class="v-lower-label">Shared by</span>
                        <span class="v-lower-name">{{ $repName ?: $company }}</span>
                    </div>
                </div>
            </div>
        </section>

        @if(!empty($highlights))
        <section class="watch-highlights">
            @foreach($highlights as $h)
                <div class="watch-highlight"><span class="watch-highlight-tick">✓</span><span>{{ $h }}</span></div>
            @endforeach
        </section>
        @endif

        <section class="watch-cta">
            <div class="watch-cta-text">
                <h2>Ready for the next step, {{ $first }}?</h2>
                <p>
                    @if($repName !== '')
                        Have a question or want to see more? {{ $repName }} is just a message away.
                    @else
                        Have a question or want to see more? We'd love to hear from you.
                    @endif
                </p>
            </div>
            @if($ctaUrl !== '')
                <a class="btn btn-primary" href="{{ $ctaUrl }}" target="_blank" rel="noopener">💬 Chat with us</a>
            @endif
        </section>

        <footer class="watch-footer">Shared with {{ $customer->name }} · {{ $company }}</footer>
    </div>

    <script>
    (function () {
        const video = document.getElementById('demoVideo');
        if (!video) return;

        const sessionId = video.dataset.sessionId;
        const token = video.dataset.token;
        const apiUrl = @json(route('crm.watch.track'));
        const csrf = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        let watchStartTime = null, totalWatchTime = 0, lastReportedTime = 0;
        let hasCompleted = false, hasSkipped = false, maxWatchedTime = 0;
        let sentEvents = new Set(), pendingSegments = [];
        let currentSegmentStart = null, currentSegmentEnd = null, lastSegmentCloseTime = 0, videoDuration = 0;

        const vWatermark = document.getElementById('vWatermark');
        const vIntro = document.getElementById('vIntro');
        const vLower = document.getElementById('vLower');
        function setOverlay(el, v){ if(el) el.classList.toggle('is-visible', v); }
        function updateOverlays(t){ setOverlay(vWatermark,true); setOverlay(vIntro, t>=3&&t<9); setOverlay(vLower, t>=12&&t<18); }

        function sendEvent(type, payload) {
            payload = payload || {};
            payload.sessionId = sessionId;
            payload.token = token;
            payload.eventType = type;
            payload.videoDuration = video.duration || videoDuration || 0;
            payload.currentTime = video.currentTime || 0;
            const form = new URLSearchParams();
            form.append('data', JSON.stringify(payload));
            form.append('_token', csrf);
            if (navigator.sendBeacon) {
                navigator.sendBeacon(apiUrl, form);
            } else {
                fetch(apiUrl, { method:'POST', body:form, keepalive:true, headers:{'X-CSRF-TOKEN':csrf} }).catch(function(){});
            }
        }

        function flushSegments(){ if(pendingSegments.length===0) return; sendEvent('segments',{segments:pendingSegments}); pendingSegments=[]; }
        function closeCurrentSegment(){
            if(currentSegmentStart!==null && currentSegmentEnd!==null && currentSegmentEnd>currentSegmentStart){
                pendingSegments.push({start:Math.max(0,currentSegmentStart),end:currentSegmentEnd});
                if(pendingSegments.length>=5) flushSegments();
            }
            currentSegmentStart=null; currentSegmentEnd=null;
        }
        function startSegment(t){ currentSegmentStart=t; currentSegmentEnd=t; }
        function updateSegment(t){ if(currentSegmentStart===null) startSegment(t); currentSegmentEnd=t; }

        function sendHeartbeat(){
            if(watchStartTime){ totalWatchTime += (Date.now()-watchStartTime)/1000; watchStartTime=Date.now(); }
            closeCurrentSegment(); flushSegments();
            const d = video.duration||videoDuration||0;
            const progress = d>0 ? (video.currentTime/d)*100 : 0;
            sendEvent('heartbeat',{
                watchDuration:Math.round(totalWatchTime*10)/10,
                watchPercentage:Math.round(progress*10)/10,
                maxWatchedTime:Math.round(maxWatchedTime*10)/10,
                completed:hasCompleted?1:0, skipped:hasSkipped?1:0
            });
        }

        video.addEventListener('loadedmetadata', function(){ videoDuration=video.duration; sendEvent('loadedmetadata',{videoDuration:video.duration}); });
        video.addEventListener('play', function(){ watchStartTime=Date.now(); if(currentSegmentStart===null) startSegment(video.currentTime); sendEvent('play',{currentTime:video.currentTime}); });
        video.addEventListener('pause', function(){
            if(watchStartTime){ totalWatchTime += (Date.now()-watchStartTime)/1000; watchStartTime=null; }
            updateSegment(video.currentTime); closeCurrentSegment(); flushSegments();
            sendEvent('pause',{watchDuration:Math.round(totalWatchTime*10)/10,currentTime:video.currentTime});
        });
        video.addEventListener('timeupdate', function(){
            const current=video.currentTime; updateOverlays(current);
            if(current>maxWatchedTime) maxWatchedTime=current;
            const delta=current-lastReportedTime, now=Date.now();
            if(delta<0||delta>3){ if(delta>3) hasSkipped=true; closeCurrentSegment(); startSegment(current); }
            else if(delta>0){ updateSegment(current); }
            const d=video.duration||videoDuration||1, percent=Math.floor((current/d)*100);
            [25,50,75,90].forEach(function(m){ if(percent>=m && !sentEvents.has('m_'+m)){ sentEvents.add('m_'+m); sendEvent('milestone',{milestone:m}); } });
            lastReportedTime=current;
            if(now-lastSegmentCloseTime>10000){ closeCurrentSegment(); flushSegments(); lastSegmentCloseTime=now; if(currentSegmentStart===null) startSegment(current); }
        });
        video.addEventListener('seeking', function(){ closeCurrentSegment(); });
        video.addEventListener('seeked', function(){ startSegment(video.currentTime); lastReportedTime=video.currentTime; });
        video.addEventListener('ended', function(){
            hasCompleted=true;
            if(watchStartTime){ totalWatchTime += (Date.now()-watchStartTime)/1000; watchStartTime=null; }
            updateSegment(video.duration||videoDuration||0); closeCurrentSegment(); flushSegments();
            const d=video.duration||videoDuration||0, progress=d>0?(maxWatchedTime/d)*100:0;
            sendEvent('complete',{watchDuration:Math.round(totalWatchTime*10)/10,watchPercentage:Math.round(progress*10)/10});
        });
        setInterval(function(){ if(watchStartTime) sendHeartbeat(); }, 10000);
        window.addEventListener('beforeunload', function(){
            if(watchStartTime){ totalWatchTime += (Date.now()-watchStartTime)/1000; watchStartTime=null; }
            updateSegment(video.currentTime); closeCurrentSegment();
            const d=video.duration||videoDuration||0, progress=d>0?(maxWatchedTime/d)*100:0;
            sendEvent('session_end',{
                watchDuration:Math.round(totalWatchTime*10)/10,
                watchPercentage:Math.round(progress*10)/10,
                completed:hasCompleted?1:0, skipped:hasSkipped?1:0, segments:pendingSegments
            });
            pendingSegments=[];
        });
    })();
    </script>
</body>
</html>
