<x-layouts.app title="Dashboard">
    @push('styles')
    <style>
        /* ── Page shell ── */
        .page-wrap {
            padding: 24px 28px;
            width: 100%;
        }

        /* Breadcrumb */
        .breadcrumb {
            font-size: 0.72rem;
            color: #94a3b8;
            margin-bottom: 14px;
        }
        .breadcrumb a { color: #94a3b8; text-decoration: none; }
        .breadcrumb a:hover { color: #0f172a; }
        .breadcrumb span { margin: 0 5px; }

        /* Page top */
        .page-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 24px;
        }
        .page-greeting h1 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #0f172a;
            letter-spacing: -0.02em;
        }
        .page-greeting p {
            font-size: 0.82rem;
            color: #64748b;
            margin-top: 3px;
        }
        .page-actions {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-shrink: 0;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 16px;
            border-radius: 8px;
            font-family: inherit;
            font-size: 0.82rem;
            font-weight: 600;
            cursor: pointer;
            border: none;
            text-decoration: none;
            transition: all 0.15s ease;
            white-space: nowrap;
        }
        .btn-outline { background: white; color: #0f172a; border: 1px solid #e2e8f0; }
        .btn-outline:hover { border-color: #0d9488; color: #0d9488; }
        .btn-dark    { background: #0f172a; color: white; }
        .btn-dark:hover { background: #1e293b; }
        .btn svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

        /* ── Stat Cards ── */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px;
            margin-bottom: 20px;
        }
        .stat-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px 18px;
            display: flex;
            align-items: center;
            gap: 14px;
            transition: box-shadow 0.2s ease;
        }
        .stat-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
        .stat-icon {
            width: 44px; height: 44px;
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .stat-icon svg { width: 22px; height: 22px; stroke: currentColor; fill: none; stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round; }
        .stat-icon-blue   { background: #eff6ff; color: #3b82f6; }
        .stat-icon-teal   { background: #f0fdfa; color: #0d9488; }
        .stat-icon-orange { background: #fff7ed; color: #f97316; }
        .stat-icon-purple { background: #f5f3ff; color: #8b5cf6; }
        .stat-body { flex: 1; min-width: 0; }
        .stat-value { font-size: 1.5rem; font-weight: 700; color: #0f172a; line-height: 1.1; }
        .stat-label { font-size: 0.65rem; font-weight: 600; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.06em; margin-top: 2px; }
        .stat-sub   { font-size: 0.72rem; color: #64748b; margin-top: 4px; }

        /* ── 3-column widget grid ── */
        .widget-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 14px;
            margin-bottom: 20px;
        }
        .widget-card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            overflow: hidden;
        }
        .widget-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 18px;
            border-bottom: 1px solid #f1f5f9;
        }
        .widget-title {
            display: flex;
            align-items: center;
            gap: 7px;
            font-size: 0.85rem;
            font-weight: 600;
            color: #0f172a;
        }
        .widget-title svg { width: 15px; height: 15px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; color: #0d9488; }
        .widget-action { color: #94a3b8; cursor: pointer; background: none; border: none; padding: 4px; border-radius: 6px; display: flex; align-items: center; }
        .widget-action:hover { background: #f1f5f9; color: #0f172a; }
        .widget-action svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
        .widget-body { padding: 16px 18px; }

        /* Empty state */
        .empty-state { text-align: center; padding: 32px 16px; color: #94a3b8; }
        .empty-state svg { width: 32px; height: 32px; stroke: currentColor; fill: none; stroke-width: 1.5; stroke-linecap: round; stroke-linejoin: round; margin-bottom: 10px; opacity: 0.5; display: block; margin-left: auto; margin-right: auto; }
        .empty-state p { font-size: 0.85rem; font-weight: 500; color: #64748b; }
        .empty-state small { font-size: 0.72rem; color: #94a3b8; }

        /* Task items */
        .task-item { display: flex; align-items: flex-start; gap: 10px; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
        .task-item:last-child { border-bottom: none; }
        .task-dot { width: 8px; height: 8px; border-radius: 50%; background: #e2e8f0; flex-shrink: 0; margin-top: 5px; }
        .task-dot.high   { background: #ef4444; }
        .task-dot.medium { background: #f97316; }
        .task-dot.low    { background: #22c55e; }
        .task-title { font-size: 0.8rem; font-weight: 500; color: #0f172a; }
        .task-meta  { font-size: 0.7rem; color: #94a3b8; margin-top: 2px; }

        /* Activity items */
        .activity-item { display: flex; align-items: flex-start; gap: 10px; padding: 8px 0; border-bottom: 1px solid #f1f5f9; }
        .activity-item:last-child { border-bottom: none; }
        .act-avatar {
            width: 28px; height: 28px; border-radius: 50%;
            background: linear-gradient(135deg, #14b8a6, #0d9488);
            display: flex; align-items: center; justify-content: center;
            font-size: 0.6rem; font-weight: 700; color: white; flex-shrink: 0;
        }
        .act-text { font-size: 0.78rem; color: #0f172a; line-height: 1.4; }
        .act-text strong { font-weight: 600; }
        .act-time { font-size: 0.68rem; color: #94a3b8; margin-top: 2px; }

        /* ── Calendar widget ── */
        .cal-nav { display: flex; align-items: center; justify-content: space-between; }
        .cal-month { font-size: 0.85rem; font-weight: 600; color: #0f172a; }
        .cal-btn { background: none; border: none; cursor: pointer; color: #94a3b8; padding: 4px; border-radius: 6px; display: flex; align-items: center; }
        .cal-btn:hover { background: #f1f5f9; color: #0f172a; }
        .cal-btn svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
        .cal-grid { margin-top: 12px; display: grid; grid-template-columns: repeat(7, 1fr); gap: 2px; text-align: center; }
        .cal-day-header { font-size: 0.62rem; font-weight: 600; color: #94a3b8; padding: 4px 0; }
        .cal-day { font-size: 0.72rem; padding: 5px 2px; border-radius: 6px; cursor: pointer; color: #0f172a; transition: background 0.15s; }
        .cal-day:hover { background: #f1f5f9; }
        .cal-day.empty { color: transparent; cursor: default; }
        .cal-day.today { background: #0d9488; color: white; font-weight: 700; }
        .cal-day.other-month { color: #cbd5e1; }

        /* ── Projects section ── */
        .section-card { background: white; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; margin-bottom: 20px; }
        .project-row { display: flex; align-items: center; gap: 12px; padding: 12px 18px; border-bottom: 1px solid #f1f5f9; flex-wrap: wrap; }
        .project-row:last-child { border-bottom: none; }
        .project-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .project-name { flex: 1; font-size: 0.82rem; font-weight: 500; color: #0f172a; min-width: 100px; }
        .project-status { font-size: 0.68rem; font-weight: 600; padding: 3px 9px; border-radius: 20px; white-space: nowrap; }
        .status-active  { background: #f0fdfa; color: #0d9488; }
        .status-pending { background: #fff7ed; color: #f97316; }
        .status-done    { background: #f0fdf4; color: #16a34a; }
        .status-paused  { background: #f8fafc; color: #64748b; }
        .project-progress { font-size: 0.72rem; color: #94a3b8; min-width: 32px; text-align: right; }

        /* ── Bottom 2-col row ── */
        .bottom-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 20px; }

        /* Asset alert rows */
        .alert-row { display: flex; align-items: flex-start; gap: 10px; padding: 9px 0; border-bottom: 1px solid #f1f5f9; }
        .alert-row:last-child { border-bottom: none; }
        .alert-dot { width: 8px; height: 8px; background: #ef4444; border-radius: 50%; flex-shrink: 0; margin-top: 5px; }
        .alert-name  { font-size: 0.8rem; font-weight: 500; color: #0f172a; }
        .alert-issue { font-size: 0.7rem; color: #64748b; margin-top: 2px; }

        /* TouchPoint */
        .tp-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px; }
        .tp-cell { background: #f8fafc; border-radius: 10px; padding: 16px 14px; text-align: center; }
        .tp-value { font-size: 1.8rem; font-weight: 800; line-height: 1; margin-bottom: 6px; }
        .tp-label { font-size: 0.6rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.07em; }
        .tp-red    { color: #ef4444; }
        .tp-orange { color: #f97316; }
        .tp-green  { color: #22c55e; }
        .tp-blue   { color: #3b82f6; }
        .tp-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .tp-btn { padding: 10px; border: none; border-radius: 8px; font-family: inherit; font-size: 0.78rem; font-weight: 600; cursor: pointer; text-decoration: none; text-align: center; transition: opacity 0.15s; }
        .tp-btn:hover { opacity: 0.85; }
        .tp-btn-risk     { background: #fff1f2; color: #ef4444; }
        .tp-btn-renewals { background: #f0fdfa; color: #0d9488; }

        /* ── Responsive ─────────────────────────────────────────────────────── */
        /* Tablet: 1025px → 769px */
        @media (max-width: 1024px) {
            .stats-grid  { grid-template-columns: repeat(2, 1fr); }
            .widget-grid { grid-template-columns: 1fr 1fr; }
        }

        /* Mobile: ≤ 768px */
        @media (max-width: 768px) {
            .page-wrap        { padding: 16px; }
            .breadcrumb       { display: none; }
            .page-top         { margin-bottom: 16px; }
            .page-greeting h1 { font-size: 1.2rem; }
            .widget-grid      { grid-template-columns: 1fr; }
            .bottom-grid      { grid-template-columns: 1fr; }
        }

        /* Small phone: ≤ 560px */
        @media (max-width: 560px) {
            .stats-grid   { grid-template-columns: 1fr 1fr; gap: 10px; }
            .stat-card    { padding: 12px 12px; gap: 10px; }
            .stat-icon    { width: 38px; height: 38px; border-radius: 8px; }
            .stat-icon svg { width: 18px; height: 18px; }
            .stat-value   { font-size: 1.3rem; }
            .stat-sub     { display: none; }
            /* Icon-only buttons on very small screens */
            .btn-text     { display: none; }
            .btn          { padding: 9px 12px; }
            .widget-body  { padding: 12px 14px; }
            .widget-header { padding: 12px 14px; }
        }

        /* Extra small: ≤ 380px */
        @media (max-width: 380px) {
            .stats-grid { gap: 8px; }
            .page-wrap  { padding: 12px; }
        }
    </style>
    @endpush

    <div class="page-wrap">

        {{-- Breadcrumb --}}
        <div class="breadcrumb">
            <a href="#">Home</a>
            <span>/</span>
            <span>Dashboard</span>
        </div>

        {{-- Greeting + Actions --}}
        <div class="page-top">
            <div class="page-greeting">
                <h1>{{ $greeting }}, {{ $staff->name }} 👋</h1>
                <p>Here's what's happening with your team today</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('tasks') }}" class="btn btn-outline">
                    <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    <span class="btn-text">View All Tasks</span>
                </a>
                <a href="{{ route('daily-report') }}" class="btn btn-dark">
                    <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    <span class="btn-text">Submit Report</span>
                </a>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon stat-icon-blue">
                    <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                </div>
                <div class="stat-body">
                    <div class="stat-value">{{ $myTasksCount }}</div>
                    <div class="stat-label">My Tasks</div>
                    <div class="stat-sub">{{ $myTasksCount }} total assigned</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-teal">
                    <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
                <div class="stat-body">
                    <div class="stat-value">{{ $teamOnlineCount }}</div>
                    <div class="stat-label">Team Online</div>
                    <div class="stat-sub">{{ $staffTotal }} total members</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-orange">
                    <svg viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>
                </div>
                <div class="stat-body">
                    <div class="stat-value">{{ $assetsNeedCheckup }}</div>
                    <div class="stat-label">Assets Checkup</div>
                    <div class="stat-sub">{{ $assetsNeedCheckup > 0 ? $assetsNeedCheckup . ' need attention' : 'All healthy' }}</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon stat-icon-purple">
                    <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                </div>
                <div class="stat-body">
                    <div class="stat-value">{{ $reportsTodayCount }}/{{ $reportableStaffCount }}</div>
                    <div class="stat-label">Reports Today</div>
                    <div class="stat-sub">{{ $submissionRate }}% submitted</div>
                </div>
            </div>
        </div>

        {{-- Widget Row: My Tasks | Team Activity | Calendar --}}
        <div class="widget-grid">

            {{-- My Tasks --}}
            <div class="widget-card">
                <div class="widget-header">
                    <div class="widget-title">
                        <svg viewBox="0 0 24 24"><path d="M9 11l3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg>
                        My Tasks
                    </div>
                    <button class="widget-action">
                        <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.08-8.36L23 10"/></svg>
                    </button>
                </div>
                <div class="widget-body">
                    @if ($myTasks->isEmpty())
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            <p>All caught up!</p>
                            <small>No pending tasks assigned to you.</small>
                        </div>
                    @else
                        @foreach ($myTasks as $task)
                            <div class="task-item">
                                <div class="task-dot {{ $task->priority ?? '' }}"></div>
                                <div>
                                    <div class="task-title">{{ $task->title }}</div>
                                    <div class="task-meta">{{ \Carbon\Carbon::parse($task->created_at)->diffForHumans() }}</div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Team Activity --}}
            <div class="widget-card">
                <div class="widget-header">
                    <div class="widget-title">
                        <svg viewBox="0 0 24 24"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        Team Activity
                    </div>
                    <button class="widget-action">
                        <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.08-8.36L23 10"/></svg>
                    </button>
                </div>
                <div class="widget-body">
                    @if ($teamActivity->isEmpty())
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                            <p>No activity yet</p>
                            <small>Team comments will show here.</small>
                        </div>
                    @else
                        @foreach ($teamActivity as $act)
                            <div class="activity-item">
                                <div class="act-avatar">{{ strtoupper(substr($act->staff_name, 0, 2)) }}</div>
                                <div>
                                    <div class="act-text">
                                        <strong>{{ $act->staff_name }}</strong> commented on
                                        <em>{{ \Illuminate\Support\Str::limit($act->task_title, 28) }}</em>
                                    </div>
                                    <div class="act-time">{{ \Carbon\Carbon::parse($act->created_at)->diffForHumans() }}</div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Calendar --}}
            <div class="widget-card">
                <div class="widget-header">
                    <div class="widget-title">
                        <svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        Calendar
                    </div>
                    <button class="widget-action">
                        <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.08-8.36L23 10"/></svg>
                    </button>
                </div>
                <div class="widget-body">
                    @php
                        $today       = now('Asia/Kolkata');
                        $firstDay    = $today->copy()->startOfMonth();
                        $daysInMonth = $today->daysInMonth;
                        $startDow    = $firstDay->dayOfWeek;
                    @endphp
                    <div class="cal-nav">
                        <button class="cal-btn" id="calPrev"><svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg></button>
                        <span class="cal-month" id="calMonth">{{ $today->format('F Y') }}</span>
                        <button class="cal-btn" id="calNext"><svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg></button>
                    </div>
                    <div class="cal-grid" id="calGrid">
                        @foreach (['S','M','T','W','T','F','S'] as $dh)
                            <div class="cal-day-header">{{ $dh }}</div>
                        @endforeach
                        @for ($i = 0; $i < $startDow; $i++)
                            <div class="cal-day empty">·</div>
                        @endfor
                        @for ($d = 1; $d <= $daysInMonth; $d++)
                            <div class="cal-day {{ $d == $today->day ? 'today' : '' }}">{{ $d }}</div>
                        @endfor
                    </div>
                </div>
            </div>

        </div>

        {{-- Projects Status --}}
        <div class="section-card">
            <div class="widget-header">
                <div class="widget-title">
                    <svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                    Projects Status
                </div>
                <button class="widget-action">
                    <svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.08-8.36L23 10"/></svg>
                </button>
            </div>

            @if ($projects->isEmpty())
                <div class="empty-state" style="padding: 40px 20px;">
                    <svg viewBox="0 0 24 24"><path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/></svg>
                    <p>No projects yet</p>
                    <small>Projects will appear here once created.</small>
                </div>
            @else
                @foreach ($projects as $project)
                    @php
                        $statusClass = match($project->status ?? 'active') {
                            'active'            => 'status-active',
                            'pending'           => 'status-pending',
                            'done','completed'  => 'status-done',
                            default             => 'status-paused',
                        };
                        $dotColor = match($project->status ?? 'active') {
                            'active'            => '#0d9488',
                            'pending'           => '#f97316',
                            'done','completed'  => '#16a34a',
                            default             => '#94a3b8',
                        };
                    @endphp
                    <div class="project-row">
                        <div class="project-dot" style="background: {{ $dotColor }}"></div>
                        <div class="project-name">{{ $project->name ?? $project->title ?? 'Untitled' }}</div>
                        <span class="project-status {{ $statusClass }}">{{ ucfirst($project->status ?? 'active') }}</span>
                        <span class="project-progress">{{ $project->progress ?? 0 }}%</span>
                    </div>
                @endforeach
            @endif
        </div>

        {{-- Assets Alert + TouchPoint --}}
        <div class="bottom-grid">

            {{-- Assets Alert --}}
            <div class="widget-card">
                <div class="widget-header">
                    <div class="widget-title">
                        <span style="width:26px;height:26px;background:#fff1f2;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <svg viewBox="0 0 24 24" style="width:14px;height:14px;stroke:#ef4444;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;">
                                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                            </svg>
                        </span>
                        Assets Alert
                    </div>
                    <div style="display:flex;gap:4px;">
                        <button class="widget-action"><svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.08-8.36L23 10"/></svg></button>
                        <button class="widget-action"><svg viewBox="0 0 24 24"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg></button>
                    </div>
                </div>
                <div class="widget-body">
                    @if ($assetAlerts->isEmpty())
                        <div class="empty-state">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            <p>No asset alerts</p>
                            <small>All assets are in good condition.</small>
                        </div>
                    @else
                        @foreach ($assetAlerts as $alert)
                            <div class="alert-row">
                                <div class="alert-dot"></div>
                                <div>
                                    <div class="alert-name">{{ $alert->asset_name }}</div>
                                    <div class="alert-issue">{{ $alert->issue }}</div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- TouchPoint --}}
            <div class="widget-card">
                <div class="widget-header">
                    <div class="widget-title">
                        <span style="width:26px;height:26px;background:#fef9c3;border-radius:8px;display:inline-flex;align-items:center;justify-content:center;flex-shrink:0;font-size:14px;">
                            🤝
                        </span>
                        TouchPoint
                    </div>
                    <div style="display:flex;gap:4px;">
                        <button class="widget-action"><svg viewBox="0 0 24 24"><polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-.08-8.36L23 10"/></svg></button>
                        <button class="widget-action"><svg viewBox="0 0 24 24"><line x1="7" y1="17" x2="17" y2="7"/><polyline points="7 7 17 7 17 17"/></svg></button>
                    </div>
                </div>
                <div class="widget-body">
                    <div class="tp-grid">
                        <div class="tp-cell">
                            <div class="tp-value tp-red">{{ $touchpointAtRisk }}</div>
                            <div class="tp-label">At Risk</div>
                        </div>
                        <div class="tp-cell">
                            <div class="tp-value tp-orange">{{ $touchpointRenewals }}</div>
                            <div class="tp-label">Renewals</div>
                        </div>
                        <div class="tp-cell">
                            <div class="tp-value tp-green">{{ $touchpointHealthy }}</div>
                            <div class="tp-label">Healthy</div>
                        </div>
                        <div class="tp-cell">
                            <div class="tp-value tp-blue">{{ $touchpointTotal }}</div>
                            <div class="tp-label">Total</div>
                        </div>
                    </div>
                    <div class="tp-actions">
                        <a href="#" class="tp-btn tp-btn-risk">View At Risk</a>
                        <a href="#" class="tp-btn tp-btn-renewals">View Renewals</a>
                    </div>
                </div>
            </div>

        </div>

    </div>
</x-layouts.app>
