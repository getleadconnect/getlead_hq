@props(['title' => 'Dashboard'])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title }} — Getlead HQ</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --sidebar-w: 200px;
            --topbar-h:  52px;
            --border:    #e2e8f0;
            --teal:      #0d9488;
            --teal-bg:   #f0fdfa;
            --teal-text: #0f766e;
            --fg:        #0f172a;
            --muted:     #64748b;
            --bg:        #f8fafc;
            --card:      #ffffff;
            --radius:    10px;
            --font:      'Inter', -apple-system, sans-serif;
        }

        html, body { height: 100%; width: 100%; }

        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--fg);
            -webkit-font-smoothing: antialiased;
        }

        /* ── Mobile topbar ───────────────────────────────────────────────────── */
        .mobile-topbar {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0;
            height: var(--topbar-h);
            background: var(--card);
            border-bottom: 1px solid var(--border);
            align-items: center;
            padding: 0 16px;
            gap: 12px;
            z-index: 300;
        }
        .hamburger {
            background: none;
            border: none;
            cursor: pointer;
            color: var(--fg);
            padding: 6px;
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            -webkit-tap-highlight-color: transparent;
        }
        .hamburger:hover { background: #f1f5f9; }
        .hamburger svg {
            width: 20px; height: 20px;
            stroke: currentColor; fill: none;
            stroke-width: 2; stroke-linecap: round;
        }
        .topbar-logo {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            flex: 1;
        }
        .topbar-logo .logo-icon {
            width: 28px; height: 28px;
            background: #ef4444;
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
        }
        .topbar-logo .logo-icon svg { width: 14px; height: 14px; fill: white; }
        .topbar-logo .logo-name { font-size: 0.875rem; font-weight: 700; color: var(--fg); }
        .topbar-avatar {
            width: 32px; height: 32px;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.65rem;
            font-weight: 700;
            color: white;
            flex-shrink: 0;
        }

        /* ── Sidebar overlay (mobile) ────────────────────────────────────────── */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.45);
            z-index: 290;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.25s ease;
        }
        .sidebar-overlay.visible {
            opacity: 1;
            pointer-events: auto;
        }

        /* ── Sidebar ─────────────────────────────────────────────────────────── */
        .sidebar {
            position: fixed;
            top: 0; left: 0; bottom: 0;
            width: var(--sidebar-w);
            background: var(--card);
            border-right: 1px solid var(--border);
            display: flex;
            flex-direction: column;
            z-index: 300;
            overflow-y: auto;
            transition: transform 0.25s ease;
        }
        .sidebar-logo {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 16px 14px;
            border-bottom: 1px solid var(--border);
            text-decoration: none;
        }
        .logo-icon {
            width: 30px; height: 30px;
            background: #ef4444;
            border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .logo-icon svg { width: 16px; height: 16px; fill: white; }
        .logo-name { font-size: 0.875rem; font-weight: 700; color: var(--fg); line-height: 1; }
        .logo-badge {
            font-size: 0.55rem; font-weight: 700;
            background: #22c55e; color: white;
            padding: 1px 5px; border-radius: 4px; letter-spacing: 0.03em;
        }
        .logo-meta { display: flex; flex-direction: column; gap: 3px; }

        /* Nav */
        .sidebar-nav { flex: 1; padding: 8px 0; }
        .nav-section {
            padding: 8px 14px 4px;
            font-size: 0.6rem; font-weight: 600;
            color: #94a3b8; text-transform: uppercase; letter-spacing: 0.08em;
        }
        .nav-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 9px 14px;
            font-size: 0.78rem; font-weight: 500;
            color: var(--muted);
            text-decoration: none;
            transition: all 0.15s ease;
            white-space: nowrap;
            overflow: hidden;
            -webkit-tap-highlight-color: transparent;
        }
        .nav-item:hover { background: #f1f5f9; color: var(--fg); }
        .nav-item.active { background: var(--teal-bg); color: var(--teal-text); font-weight: 600; }
        .nav-item svg {
            width: 17px; height: 17px; flex-shrink: 0;
            stroke: currentColor; fill: none;
            stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
        }

        /* Sidebar bottom user */
        .sidebar-user {
            padding: 12px 14px;
            border-top: 1px solid var(--border);
            display: flex; align-items: center; gap: 8px;
        }
        .user-avatar {
            width: 30px; height: 30px;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.65rem; font-weight: 700; color: white; flex-shrink: 0;
        }
        .user-info { flex: 1; overflow: hidden; }
        .user-name {
            font-size: 0.72rem; font-weight: 600; color: var(--fg);
            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        }
        .user-role { font-size: 0.62rem; color: var(--muted); text-transform: capitalize; }
        .logout-btn {
            background: none; border: none; cursor: pointer;
            color: var(--muted); padding: 4px; border-radius: 6px;
            display: flex; align-items: center; transition: color 0.15s; flex-shrink: 0;
        }
        .logout-btn:hover { color: #ef4444; }
        .logout-btn svg {
            width: 15px; height: 15px; stroke: currentColor; fill: none;
            stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
        }

        /* ── Main content ─────────────────────────────────────────────────────── */
        .app-content {
            margin-left: var(--sidebar-w);
            width: calc(100% - var(--sidebar-w));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── Mobile breakpoint ───────────────────────────────────────────────── */
        @media (max-width: 768px) {
            .mobile-topbar { display: flex; }
            .sidebar-overlay { display: block; }

            .sidebar {
                transform: translateX(-100%);
                top: 0;
                box-shadow: 4px 0 24px rgba(0,0,0,0.12);
            }
            .sidebar.open { transform: translateX(0); }

            /* Hide the logo inside sidebar on mobile — topbar has it */
            .sidebar-logo { display: none; }

            .app-content {
                margin-left: 0;
                width: 100%;
                padding-top: var(--topbar-h);
            }
        }
    </style>
    @stack('styles')
</head>
<body>

{{-- Mobile top bar --}}
<header class="mobile-topbar" id="mobileTopbar">
    <button class="hamburger" id="menuToggle" aria-label="Menu">
        <svg viewBox="0 0 24 24"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
    </button>
    <a href="{{ route('dashboard') }}" class="topbar-logo">
        <div class="logo-icon"><svg viewBox="0 0 24 24"><path d="M13 3L4 14h7l-2 7 9-11h-7l2-7z"/></svg></div>
        <span class="logo-name">Getlead HQ</span>
    </a>
    <div class="topbar-avatar">{{ strtoupper(substr(Auth::guard('staff')->user()->name, 0, 2)) }}</div>
</header>

{{-- Sidebar overlay --}}
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div style="display:flex; min-height:100vh; width:100%;">

    {{-- Sidebar --}}
    <aside class="sidebar" id="sidebar">
        {{-- Logo (desktop only) --}}
        <a href="{{ route('dashboard') }}" class="sidebar-logo">
            <div class="logo-icon">
                <svg viewBox="0 0 24 24"><path d="M13 3L4 14h7l-2 7 9-11h-7l2-7z"/></svg>
            </div>
            <div class="logo-meta">
                <span class="logo-name">Getlead</span>
                <span class="logo-badge">CRM</span>
            </div>
        </a>

        {{-- Navigation --}}
        @php $isAdmin = in_array(Auth::guard('staff')->user()->role, ['admin', 'secretary']); @endphp
        <nav class="sidebar-nav">
            @if($isAdmin)
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}" onclick="closeSidebar()">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                Dashboard
            </a>
            @endif

            <a href="{{ route('my-tasks') }}" class="nav-item {{ request()->routeIs('my-tasks') ? 'active' : '' }}" onclick="closeSidebar()">
                <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                My Tasks
            </a>

            @if($isAdmin)
            <a href="{{ route('tasks') }}" class="nav-item {{ request()->routeIs('tasks') ? 'active' : '' }}" onclick="closeSidebar()">
                <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                All Tasks
            </a>
            @endif

            <a href="{{ route('daily-report') }}" class="nav-item {{ request()->routeIs('daily-report') ? 'active' : '' }}" onclick="closeSidebar()">
                <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                Daily Report
            </a>

            @if($isAdmin)
            <a href="{{ route('touchpoint') }}" class="nav-item {{ request()->routeIs('touchpoint*') ? 'active' : '' }}" onclick="closeSidebar()">
                <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                TouchPoint
            </a>
            @endif

            <a href="{{ route('projects') }}" class="nav-item {{ request()->routeIs('projects*') ? 'active' : '' }}" onclick="closeSidebar()">
                <svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                Projects
            </a>
            <a href="{{ route('assets') }}" class="nav-item {{ request()->routeIs('assets*') ? 'active' : '' }}" onclick="closeSidebar()">
                <svg viewBox="0 0 24 24"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                Assets
            </a>

            @if($isAdmin)
            <a href="{{ route('reports') }}" class="nav-item {{ request()->routeIs('reports*') ? 'active' : '' }}" onclick="closeSidebar()">
                <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                Reports
            </a>
            <a href="{{ route('analytics') }}" class="nav-item {{ request()->routeIs('analytics*') ? 'active' : '' }}" onclick="closeSidebar()">
                <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                Analytics
            </a>
            <a href="{{ route('team') }}" class="nav-item {{ request()->routeIs('team*') ? 'active' : '' }}" onclick="closeSidebar()">
                <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                Team
            </a>
            <a href="{{ route('settings') }}" class="nav-item {{ request()->routeIs('settings*') ? 'active' : '' }}" onclick="closeSidebar()">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06A1.65 1.65 0 0 0 4.68 15a1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06A1.65 1.65 0 0 0 9 4.68a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06A1.65 1.65 0 0 0 19.4 9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                Settings
            </a>
            @endif
        </nav>

        {{-- User + Logout --}}
        <div class="sidebar-user">
            <div class="user-avatar">
                {{ strtoupper(substr(Auth::guard('staff')->user()->name, 0, 2)) }}
            </div>
            <div class="user-info">
                <div class="user-name">{{ Auth::guard('staff')->user()->name }}</div>
                <div class="user-role">{{ Auth::guard('staff')->user()->role }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="logout-btn" title="Logout">
                    <svg viewBox="0 0 24 24"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </button>
            </form>
        </div>
    </aside>

    {{-- Page content --}}
    <main class="app-content">
        {{ $slot }}
    </main>

</div>

<script>
    var sidebar  = document.getElementById('sidebar');
    var overlay  = document.getElementById('sidebarOverlay');
    var menuBtn  = document.getElementById('menuToggle');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('visible');
        document.body.style.overflow = 'hidden';
    }
    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('visible');
        document.body.style.overflow = '';
    }
    if (menuBtn)  menuBtn.addEventListener('click', openSidebar);
    if (overlay)  overlay.addEventListener('click', closeSidebar);

    // Close sidebar on desktop resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) closeSidebar();
    });
</script>
</body>
</html>
