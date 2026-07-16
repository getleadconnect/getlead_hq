<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Getlead HQ — Application Submitted</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        :root{
            --brand-red:#DC2626; --brand-red-dark:#B91C1C; --brand-red-soft:#FEF2F2;
            --text-1:#0F172A; --text-2:#475569; --text-3:#94A3B8;
            --border:#E5E7EB; --bg-page:#FAFAF9; --bg-card:#FFFFFF; --bg-neutral:#F3F4F6;
            --success:#15803D; --success-soft:#F0FDF4; --success-border:#BBF7D0;
            --radius-sm:6px; --radius-lg:12px;
            --font:'Poppins',-apple-system,sans-serif;
        }
        html,body{height:100%;}
        body{font-family:var(--font);background:var(--bg-page);color:var(--text-1);-webkit-font-smoothing:antialiased;}
        .shell{display:flex;min-height:100vh;}

        .brand-panel{
            width:38%;max-width:460px;flex-shrink:0;position:relative;
            background:linear-gradient(160deg,#B91C1C 0%,#DC2626 55%,#7F1D1D 100%);
            color:#fff;padding:48px 44px;display:flex;flex-direction:column;justify-content:space-between;overflow:hidden;
        }
        .brand-panel::after{content:"";position:absolute;right:-120px;bottom:-120px;width:340px;height:340px;border-radius:50%;background:rgba(255,255,255,.06);}
        .brand-panel::before{content:"";position:absolute;left:-80px;top:-80px;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,.05);}
        .brand-logo{display:flex;align-items:center;gap:12px;position:relative;z-index:1;}
        .brand-logo .mark{width:44px;height:44px;border-radius:12px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;}
        .brand-logo .mark svg{width:24px;height:24px;fill:#fff;}
        .brand-logo .name{font-size:20px;font-weight:700;letter-spacing:-.3px;}
        .brand-copy{position:relative;z-index:1;}
        .brand-copy h1{font-size:30px;font-weight:700;line-height:1.2;letter-spacing:-.5px;margin-bottom:14px;}
        .brand-copy p{font-size:14px;line-height:1.7;color:rgba(255,255,255,.85);max-width:340px;}
        .brand-foot{position:relative;z-index:1;font-size:12px;color:rgba(255,255,255,.6);}
        .brand-foot a{color:#fff;text-decoration:none;}

        .content{flex:1;display:flex;align-items:center;justify-content:center;padding:40px 32px;}
        .done{max-width:460px;text-align:center;}
        .check-badge{
            width:96px;height:96px;border-radius:50%;margin:0 auto 26px;
            background:var(--success-soft);border:1px solid var(--success-border);
            display:flex;align-items:center;justify-content:center;
            animation:pop .45s cubic-bezier(.17,.67,.3,1.33);
        }
        .check-badge svg{width:46px;height:46px;stroke:var(--success);fill:none;stroke-width:2.4;stroke-linecap:round;stroke-linejoin:round;}
        .check-badge svg path{stroke-dasharray:32;stroke-dashoffset:32;animation:draw .5s .25s ease forwards;}
        @keyframes pop{0%{transform:scale(.6);opacity:0;}100%{transform:scale(1);opacity:1;}}
        @keyframes draw{to{stroke-dashoffset:0;}}

        .done h2{font-size:26px;font-weight:700;letter-spacing:-.4px;margin-bottom:10px;}
        .done .lead{font-size:15px;color:var(--text-2);line-height:1.65;margin-bottom:6px;}
        .done .thanks{font-size:14px;color:var(--text-3);margin-bottom:28px;}
        .done .thanks b{color:var(--brand-red-dark);font-weight:600;}
        .actions{display:flex;gap:10px;justify-content:center;flex-wrap:wrap;}
        .btn{display:inline-flex;align-items:center;gap:7px;padding:11px 22px;border-radius:var(--radius-sm);font-family:inherit;font-size:13.5px;font-weight:500;cursor:pointer;border:1px solid transparent;text-decoration:none;transition:all .15s;}
        .btn svg{width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}
        .btn-primary{background:var(--brand-red);color:#fff;}
        .btn-primary:hover{background:var(--brand-red-dark);}
        .btn-ghost{background:transparent;color:var(--text-2);border-color:var(--border);}
        .btn-ghost:hover{background:var(--bg-neutral);color:var(--text-1);}

        @media(max-width:860px){
            .shell{flex-direction:column;}
            .brand-panel{width:100%;max-width:none;padding:32px 28px;flex-direction:row;align-items:center;gap:20px;flex-wrap:wrap;}
            .brand-copy p,.brand-foot{display:none;}
            .brand-copy h1{font-size:22px;margin:0;}
            .content{padding:40px 24px;}
        }
    </style>
</head>
<body>
<div class="shell">
    <aside class="brand-panel">
        <div class="brand-logo">
            <span class="mark"><svg viewBox="0 0 24 24"><path d="M13 3L4 14h7l-2 7 9-11h-7l2-7z"/></svg></span>
            <span class="name">Getlead HQ</span>
        </div>
        <div class="brand-copy">
            <h1>Application received</h1>
            <p>Thanks for applying to Getlead. Our HR team will review your application and get in touch with you soon.</p>
        </div>
        <div class="brand-foot">© {{ date('Y') }} <a href="https://getleadcrm.com/" target="_blank" rel="noopener">Getlead</a></div>
    </aside>

    <main class="content">
        <div class="done">
            <div class="check-badge">
                <svg viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>
            </div>
            <h2>Success!</h2>
            <p class="lead">Your application has been submitted successfully.</p>
            <p class="thanks">
                @if($name !== '')
                    Thank you, <b>{{ $name }}</b>. We'll be in touch.
                @else
                    Thank you. We'll be in touch.
                @endif
            </p>
            <div class="actions">
                <a href="{{ route('hr.register') }}" class="btn btn-primary">
                    <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Submit another application
                </a>
                <a href="https://getleadcrm.com/" target="_blank" rel="noopener" class="btn btn-ghost">
                    Visit Getlead
                    <svg viewBox="0 0 24 24"><path d="M7 17L17 7"/><path d="M7 7h10v10"/></svg>
                </a>
            </div>
        </div>
    </main>
</div>
</body>
</html>
