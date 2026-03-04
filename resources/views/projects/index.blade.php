<x-layouts.app title="Projects">
@push('styles')
<style>
/* ── Vars (scoped to avoid overriding app.blade.php :root) ── */
.p-wrap, .modal-overlay, .toast-wrap {
    --pp:    #14b8a6;
    --ppd:   #0d9488;
    --bg:    #ffffff;
    --p-muted: #f8fafc;
    --bdr:   #e2e8f0;
    --tx:    #1e293b;
    --txm:   #64748b;
    --txs:   #94a3b8;
    --hov:   #f1f5f9;
    --p-teal:#f0fdfa;
    --red:   #dc2626;
    --warn:  #d97706;
    --grn:   #16a34a;
    --amb:   #f59e0b;
}

/* ── Layout ── */
.p-wrap { display:flex; flex-direction:column; min-height:100vh; background:var(--bg); }

/* ── Header ── */
.p-head {
    display:flex; align-items:center; justify-content:space-between;
    padding:12px 24px; border-bottom:1px solid var(--bdr); background:var(--bg);
    flex-shrink:0; flex-wrap:wrap; gap:10px; position:sticky; top:0; z-index:100;
}
.p-head-left  { display:flex; align-items:center; gap:14px; }
.p-head-right { display:flex; align-items:center; gap:10px; }
.p-title { font-size:1rem; font-weight:700; color:var(--tx); }

/* Tabs */
.p-tabs { display:flex; gap:2px; background:var(--p-muted); border-radius:8px; padding:3px; }
.p-tab {
    padding:5px 14px; font-size:.8rem; font-weight:600; border:none;
    border-radius:6px; cursor:pointer; background:transparent; color:var(--txm);
    transition:all .15s;
}
.p-tab.on { background:var(--pp); color:#fff; box-shadow:0 1px 4px rgba(20,184,166,.25); }

/* View-mode toggle */
.vm-toggle {
    display:flex; gap:2px; background:var(--p-muted);
    border:1px solid var(--bdr); border-radius:7px; padding:3px;
}
.vm-btn {
    background:none; border:none; cursor:pointer; padding:5px 8px;
    border-radius:5px; color:var(--txm); display:flex; align-items:center;
    transition:all .15s;
}
.vm-btn.on { background:var(--bg); color:var(--pp); box-shadow:0 1px 3px rgba(0,0,0,.08); }
.vm-btn svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2; }

/* Buttons */
.btn {
    display:inline-flex; align-items:center; gap:6px; padding:7px 14px;
    font-size:.82rem; font-weight:600; border:none; border-radius:8px;
    cursor:pointer; transition:all .15s; white-space:nowrap; text-decoration:none;
}
.btn svg { width:14px; height:14px; stroke:currentColor; fill:none; stroke-width:2.2; }
.btn-primary { background:var(--pp); color:#fff; }
.btn-primary:hover { background:var(--ppd); }
.btn-ghost { background:var(--p-muted); color:var(--tx); border:1px solid var(--bdr); }
.btn-ghost:hover { background:var(--hov); }
.btn-danger { background:#fef2f2; color:var(--red); border:1px solid #fecaca; }
.btn-danger:hover { background:#fee2e2; }
.btn-sm { padding:5px 11px; font-size:.78rem; }

/* ── Page body ── */
.p-body { flex:1; padding:22px 24px; overflow-y:auto; }

/* ── KPI Grid ── */
.kpi-row { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:22px; }
.kpi-card {
    background:var(--bg); border:1px solid var(--bdr); border-radius:12px;
    padding:16px 20px;
}
.kpi-lbl { font-size:.72rem; font-weight:600; color:var(--txm); text-transform:uppercase; letter-spacing:.05em; margin-bottom:6px; }
.kpi-val { font-size:1.7rem; font-weight:800; color:var(--tx); line-height:1; }
.kpi-val.c-teal { color:var(--ppd); }
.kpi-val.c-amb  { color:var(--warn); }
.kpi-val.c-grn  { color:var(--grn); }

/* ── Dashboard grid ── */
.dash-cols { display:grid; grid-template-columns:1fr 1fr; gap:18px; }
.card { background:var(--bg); border:1px solid var(--bdr); border-radius:12px; overflow:hidden; }
.card-hd { padding:12px 16px; border-bottom:1px solid var(--bdr); font-size:.85rem; font-weight:700; color:var(--tx); }
.card-bd { padding:14px 16px; }

/* ── Project card ── */
.proj-card {
    border:1px solid var(--bdr); border-radius:10px; padding:13px 14px;
    margin-bottom:10px; cursor:pointer; transition:all .2s;
}
.proj-card:last-child { margin-bottom:0; }
.proj-card:hover { border-color:rgba(20,184,166,.3); box-shadow:0 4px 16px -4px rgba(20,184,166,.15); transform:translateY(-1px); }
.proj-card.ov { border-left:4px solid var(--red); }

/* ── Progress ── */
.prog-wrap { width:100%; height:6px; background:var(--bdr); border-radius:9999px; overflow:hidden; }
.prog-fill { height:100%; background:linear-gradient(90deg,#14b8a6,#34d399); border-radius:9999px; transition:width .5s ease; }

/* ── Activity ── */
.act-item { display:flex; align-items:flex-start; gap:10px; padding:9px 0; border-bottom:1px solid var(--bdr); font-size:.8rem; color:var(--tx); line-height:1.45; }
.act-item:last-child { border-bottom:none; }
.act-icon { width:26px; height:26px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.7rem; font-weight:700; flex-shrink:0; }
.act-icon.i-created { background:var(--teal); color:var(--ppd); }
.act-icon.i-status  { background:#fffbeb; color:var(--warn); }
.act-icon.i-comment { background:#eff6ff; color:#2563eb; }

/* ── Filter bar ── */
.fbar { display:flex; align-items:center; gap:10px; padding:12px 0; margin-bottom:12px; flex-wrap:wrap; }
.fsel, .finp {
    padding:7px 11px; border:1px solid var(--bdr); border-radius:8px;
    font-size:.82rem; color:var(--tx); background:var(--bg); outline:none;
    transition:border-color .15s;
}
.fsel:focus, .finp:focus { border-color:var(--pp); box-shadow:0 0 0 3px rgba(20,184,166,.1); }
.finp { min-width:200px; }

/* ── Table ── */
.tbl-wrap { overflow-x:auto; }
.p-tbl { width:100%; border-collapse:collapse; font-size:.83rem; }
.p-tbl th {
    padding:9px 12px; text-align:left; font-size:.7rem; font-weight:700;
    text-transform:uppercase; letter-spacing:.05em; color:var(--txm);
    background:var(--p-muted); border-bottom:1px solid var(--bdr);
    white-space:nowrap; cursor:pointer; user-select:none;
}
.p-tbl th:hover { color:var(--tx); }
.p-tbl td { padding:10px 12px; border-bottom:1px solid var(--bdr); color:var(--tx); vertical-align:middle; }
.p-tbl tbody tr:hover { background:var(--hov); cursor:pointer; }
.p-tbl tbody tr:last-child td { border-bottom:none; }
.si { opacity:.35; font-size:.65rem; margin-left:4px; }
.si.on { opacity:1; color:var(--pp); }

.dt-bar { display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; font-size:.78rem; color:var(--txm); }

/* Status badges */
.badge { display:inline-block; padding:3px 10px; border-radius:9999px; font-size:.7rem; font-weight:600; white-space:nowrap; }
.b-active    { background:rgba(20,184,166,.12); color:#0f766e; border:1px solid rgba(20,184,166,.25); }
.b-on_hold   { background:rgba(245,158,11,.12); color:#92400e; border:1px solid rgba(245,158,11,.25); }
.b-completed { background:rgba(34,197,94,.12);  color:#14532d; border:1px solid rgba(34,197,94,.25); }
.b-archived  { background:rgba(100,116,139,.12); color:#334155; border:1px solid rgba(100,116,139,.25); }
.b-danger    { background:rgba(220,38,38,.1); color:var(--red); border:1px solid rgba(220,38,38,.25); }

/* Pagination */
.pager { display:flex; align-items:center; justify-content:flex-end; gap:4px; padding-top:12px; }
.pg-btn { padding:5px 10px; border:1px solid var(--bdr); border-radius:6px; background:var(--bg); font-size:.78rem; cursor:pointer; color:var(--tx); transition:all .15s; }
.pg-btn:hover:not(:disabled) { background:var(--hov); }
.pg-btn:disabled { opacity:.4; cursor:default; }
.pg-btn.on { background:var(--pp); color:#fff; border-color:var(--pp); }

/* ── Kanban ── */
.board-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; min-height:400px; }
.kb-col { background:var(--p-muted); border-radius:10px; border:1px solid var(--bdr); display:flex; flex-direction:column; }
.kb-col-hd {
    padding:10px 14px; border-bottom:1px solid var(--bdr);
    display:flex; align-items:center; justify-content:space-between;
    font-size:.8rem; font-weight:700; color:var(--tx);
}
.kb-cnt { background:var(--bdr); border-radius:9999px; padding:1px 8px; font-size:.7rem; font-weight:700; color:var(--txm); }
.kb-body { flex:1; padding:10px; display:flex; flex-direction:column; gap:8px; min-height:60px; }
.kb-body.drag-over { background:var(--teal); border-radius:6px; }
.kb-card {
    background:var(--bg); border:1px solid var(--bdr); border-radius:9px;
    padding:11px 13px; cursor:pointer; transition:all .2s; user-select:none;
}
.kb-card:hover { border-color:rgba(20,184,166,.3); box-shadow:0 4px 12px -4px rgba(0,0,0,.1); transform:translateY(-2px); }
.kb-card.ov { border-left:4px solid var(--red); }
.kb-card.dragging { opacity:.45; transform:rotate(2deg); }
.kb-card-title { font-size:.82rem; font-weight:600; color:var(--tx); margin-bottom:7px; }
.kb-card-meta { display:flex; align-items:center; justify-content:space-between; font-size:.72rem; color:var(--txm); flex-wrap:wrap; gap:4px; }
.kb-empty { font-size:.78rem; color:var(--txs); padding:12px 0; text-align:center; }

/* ── Detail ── */
.det-hd { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:18px; flex-wrap:wrap; }
.det-title { font-size:1.2rem; font-weight:700; color:var(--tx); margin-bottom:8px; }
.det-acts { display:flex; gap:8px; flex-shrink:0; }
.meta-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:12px; margin-bottom:18px; }
.meta-item { background:var(--p-muted); border-radius:10px; padding:13px 14px; border:1px solid var(--bdr); }
.meta-lbl { font-size:.63rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--txm); margin-bottom:5px; }
.meta-val { font-size:.9rem; font-weight:700; color:var(--tx); }
.prog-card { background:var(--bg); border:1px solid var(--bdr); border-radius:12px; padding:14px 16px; margin-bottom:18px; }

/* Task groups in detail */
.tg { margin-bottom:16px; }
.tg-hd { display:flex; align-items:center; gap:10px; margin-bottom:10px; font-size:.85rem; font-weight:700; color:var(--tx); padding-bottom:7px; border-bottom:1px solid var(--bdr); }
.tg-cnt { background:var(--p-muted); padding:2px 8px; border-radius:9999px; font-size:.7rem; font-weight:700; color:var(--txm); }
.tg-item { display:flex; align-items:center; gap:10px; padding:8px 11px; border-radius:8px; border:1px solid var(--bdr); margin-bottom:5px; cursor:pointer; transition:background .15s; font-size:.82rem; }
.tg-item:hover { background:var(--hov); }
.tg-item.ov { border-left:3px solid var(--red); }
.p-dot { width:8px; height:8px; border-radius:50%; flex-shrink:0; }
.d-red   { background:#dc2626; }
.d-amb   { background:#f59e0b; }
.d-blue  { background:#3b82f6; }
.d-slate { background:#94a3b8; }
.tg-title { flex:1; font-weight:500; color:var(--tx); }
.tg-meta { font-size:.72rem; color:var(--txm); }

/* ── Modals ── */
.modal-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,.4); z-index:1000; align-items:center; justify-content:center; padding:20px; }
.modal-overlay.open { display:flex; }
.modal { background:var(--bg); border-radius:14px; width:100%; max-width:540px; max-height:90vh; overflow-y:auto; box-shadow:0 20px 60px -12px rgba(0,0,0,.25); }
.modal-hd { display:flex; align-items:center; justify-content:space-between; padding:15px 20px; border-bottom:1px solid var(--bdr); }
.modal-title { font-size:.93rem; font-weight:700; color:var(--tx); }
.modal-x { background:none; border:none; cursor:pointer; font-size:1.3rem; color:var(--txm); padding:2px 6px; border-radius:6px; line-height:1; }
.modal-x:hover { background:var(--hov); color:var(--tx); }
.modal-bd { padding:18px 20px; }
.modal-ft { display:flex; justify-content:flex-end; gap:10px; padding:13px 20px; border-top:1px solid var(--bdr); }
.fg { margin-bottom:13px; }
.flbl { display:block; font-size:.77rem; font-weight:600; color:var(--tx); margin-bottom:5px; }
.fi { width:100%; padding:8px 11px; border:1px solid var(--bdr); border-radius:8px; font-size:.83rem; color:var(--tx); background:var(--bg); outline:none; transition:border-color .15s; }
.fi:focus { border-color:var(--pp); box-shadow:0 0 0 3px rgba(20,184,166,.1); }
textarea.fi { resize:vertical; }
.frow { display:flex; gap:12px; }
.frow .fg { flex:1; }

/* ── Alert Dialog ── */
.alert-overlay { display:none; position:fixed; inset:0; background:rgba(15,23,42,.45); z-index:2000; align-items:center; justify-content:center; padding:20px; }
.alert-overlay.open { display:flex; }
.alert-box { background:var(--bg); border-radius:14px; width:100%; max-width:400px; box-shadow:0 20px 60px -12px rgba(0,0,0,.3); overflow:hidden; }
.alert-icon-wrap { display:flex; justify-content:center; padding:24px 24px 0; }
.alert-icon { width:48px; height:48px; border-radius:50%; background:#fef2f2; display:flex; align-items:center; justify-content:center; }
.alert-icon svg { width:22px; height:22px; stroke:#dc2626; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
.alert-body { padding:14px 24px 20px; text-align:center; }
.alert-title { font-size:.97rem; font-weight:700; color:var(--tx); margin-bottom:7px; }
.alert-desc  { font-size:.83rem; color:var(--txm); line-height:1.55; }
.alert-ft { display:flex; gap:10px; padding:0 24px 20px; }
.alert-ft .btn { flex:1; justify-content:center; }

/* ── Toast ── */
.toast-wrap { position:fixed; top:22px; right:22px; display:flex; flex-direction:column; gap:8px; z-index:9999; }
.toast { padding:10px 16px; border-radius:9px; font-size:.83rem; font-weight:600; color:#fff; box-shadow:0 4px 16px rgba(0,0,0,.15); animation:tst .25s ease; }
.toast-success { background:#16a34a; }
.toast-error   { background:#dc2626; }
@keyframes tst  { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }

/* ── Misc ── */
.back-lnk { display:inline-flex; align-items:center; gap:6px; font-size:.8rem; color:var(--txm); text-decoration:none; margin-bottom:16px; transition:color .15s; }
.back-lnk svg { width:14px; height:14px; stroke:currentColor; fill:none; stroke-width:2; }
.back-lnk:hover { color:var(--tx); }
.empty-st { text-align:center; padding:28px 16px; color:var(--txm); font-size:.85rem; }
.skel { background:var(--bdr); border-radius:6px; }
@@keyframes sk { 0%{opacity:.5} 100%{opacity:1} }
.skel { animation:sk 1.4s infinite alternate; }
.back-desc { font-size:.82rem; color:var(--txm); margin-bottom:16px; line-height:1.6; }
</style>
@endpush

<div class="p-wrap">

{{-- ══ HEADER ══ --}}
<div class="p-head">
    <div class="p-head-left">
        <span class="p-title">Projects</span>
    </div>
    <div class="p-head-right">
        {{-- Dashboard / List tabs ── always visible (hidden in detail) --}}
        <div class="p-tabs" id="tabBar">
            <button class="p-tab on" id="btnDash" onclick="switchView('dashboard')">Dashboard</button>
            <button class="p-tab"   id="btnList" onclick="switchView('list')">List</button>
        </div>
        {{-- Table / Board toggle ── hidden only in detail view --}}
        <div class="vm-toggle" id="modeToggle">
            <button class="vm-btn on" id="btnTable" data-mode="table" onclick="setMode('table')" title="Table view">
                <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
            </button>
            <button class="vm-btn" id="btnBoard" data-mode="board" onclick="setMode('board')" title="Board view">
                <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            </button>
        </div>
        @if($isAdmin)
        <button class="btn btn-primary btn-sm" id="btnNewProj" onclick="openCreateModal()">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            New Project
        </button>
        @endif
    </div>
</div>

{{-- ══ BODY ══ --}}
<div class="p-body">

    {{-- ── DASHBOARD VIEW ── --}}
    <div id="dashView">
        <div class="kpi-row" id="kpiRow">
            @for($i=0;$i<4;$i++)
            <div class="kpi-card"><div class="skel" style="height:14px;width:55%;margin-bottom:10px"></div><div class="skel" style="height:28px;width:38%"></div></div>
            @endfor
        </div>
        <div class="dash-cols">
            <div class="card">
                <div class="card-hd">Active Projects</div>
                <div class="card-bd" id="activeList"><div class="skel" style="height:70px;border-radius:10px"></div></div>
            </div>
            <div class="card">
                <div class="card-hd">Recent Activity</div>
                <div class="card-bd" id="actList"><div class="skel" style="height:70px;border-radius:10px"></div></div>
            </div>
        </div>
    </div>

    {{-- ── LIST VIEW ── --}}
    <div id="listView" style="display:none">
        <div class="fbar">
            <select class="fsel" id="fStatus" onchange="onFChange()">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="on_hold">On Hold</option>
                <option value="completed">Completed</option>
                <option value="archived">Archived</option>
            </select>
            <select class="fsel" id="fLead" onchange="onFChange()">
                <option value="">All Leads</option>
                @foreach($staffList as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>
            <input type="text" class="finp" id="fSearch" placeholder="Search projects…" oninput="debF()">
        </div>
        {{-- Table view --}}
        <div id="tblWrap"></div>
        {{-- Board view --}}
        <div id="boardWrap" class="board-grid" style="display:none">
            @foreach(['active'=>'🚀 Active','on_hold'=>'⏸ On Hold','completed'=>'✅ Completed','archived'=>'📦 Archived'] as $st=>$lbl)
            <div class="kb-col">
                <div class="kb-col-hd">
                    <span>{{ $lbl }}</span>
                    <span class="kb-cnt" id="kc-{{ $st }}">0</span>
                </div>
                <div class="kb-body" id="kb-{{ $st }}" data-status="{{ $st }}"></div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- ── DETAIL VIEW ── --}}
    <div id="detailView" style="display:none">
        <a href="#" class="back-lnk" onclick="backTo();return false">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg>
            Back to Projects
        </a>
        <div id="detailContent"></div>
    </div>

</div>
</div>

{{-- ══ CREATE / EDIT PROJECT MODAL ══ --}}
<div class="modal-overlay" id="projModal">
    <div class="modal">
        <div class="modal-hd">
            <span class="modal-title" id="projModalTitle">New Project</span>
            <button class="modal-x" onclick="closeModal('projModal')">×</button>
        </div>
        <div class="modal-bd">
            <input type="hidden" id="editId">
            <div class="fg">
                <label class="flbl">Project Name *</label>
                <input type="text" class="fi" id="fName" placeholder="Enter project name">
            </div>
            <div class="fg">
                <label class="flbl">Description</label>
                <textarea class="fi" id="fDesc" rows="3" placeholder="What is this project about?"></textarea>
            </div>
            <div class="frow">
                <div class="fg">
                    <label class="flbl">Status</label>
                    <select class="fi" id="fStatusM">
                        <option value="active">Active</option>
                        <option value="on_hold">On Hold</option>
                        <option value="completed">Completed</option>
                        <option value="archived">Archived</option>
                    </select>
                </div>
                <div class="fg">
                    <label class="flbl">Project Lead</label>
                    <select class="fi" id="fLeadM">
                        <option value="">— None —</option>
                        @foreach($staffList as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="frow">
                <div class="fg">
                    <label class="flbl">Start Date</label>
                    <input type="date" class="fi" id="fStart">
                </div>
                <div class="fg">
                    <label class="flbl">Target Date</label>
                    <input type="date" class="fi" id="fTarget">
                </div>
            </div>
        </div>
        <div class="modal-ft">
            <button class="btn btn-ghost btn-sm" onclick="closeModal('projModal')">Cancel</button>
            <button class="btn btn-primary btn-sm" id="projSaveBtn" onclick="saveProject()">Create Project</button>
        </div>
    </div>
</div>

{{-- ══ ADD TASK TO PROJECT MODAL ══ --}}
<div class="modal-overlay" id="addTaskModal">
    <div class="modal">
        <div class="modal-hd">
            <span class="modal-title">Add Task to Project</span>
            <button class="modal-x" onclick="closeModal('addTaskModal')">×</button>
        </div>
        <div class="modal-bd">
            <input type="hidden" id="atProjId">
            <div class="fg">
                <label class="flbl">Title *</label>
                <input type="text" class="fi" id="atTitle" placeholder="Task title">
            </div>
            <div class="fg">
                <label class="flbl">Description</label>
                <textarea class="fi" id="atDesc" rows="2" placeholder="Optional description"></textarea>
            </div>
            <div class="frow">
                <div class="fg">
                    <label class="flbl">Assign To</label>
                    <select class="fi" id="atAssignee">
                        <option value="">— Unassigned —</option>
                        @foreach($staffList as $s)
                        <option value="{{ $s->id }}">{{ $s->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fg">
                    <label class="flbl">Priority</label>
                    <select class="fi" id="atPriority">
                        <option value="normal">Normal</option>
                        <option value="low">Low</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
            </div>
            <div class="fg">
                <label class="flbl">Due Date</label>
                <input type="date" class="fi" id="atDue">
            </div>
        </div>
        <div class="modal-ft">
            <button class="btn btn-ghost btn-sm" onclick="closeModal('addTaskModal')">Cancel</button>
            <button class="btn btn-primary btn-sm" onclick="saveTask()">Add Task</button>
        </div>
    </div>
</div>

{{-- ══ ALERT / CONFIRM DIALOG ══ --}}
<div class="alert-overlay" id="alertDialog">
    <div class="alert-box">
        <div class="alert-icon-wrap">
            <div class="alert-icon">
                <svg viewBox="0 0 24 24"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
            </div>
        </div>
        <div class="alert-body">
            <div class="alert-title" id="alertTitle">Delete Project?</div>
            <div class="alert-desc"  id="alertDesc">This action cannot be undone.</div>
        </div>
        <div class="alert-ft">
            <button class="btn btn-ghost btn-sm" id="alertCancelBtn" onclick="closeAlert()">Cancel</button>
            <button class="btn btn-danger btn-sm" id="alertConfirmBtn" onclick="confirmAlert()">Delete</button>
        </div>
    </div>
</div>

<div class="toast-wrap" id="toastWrap"></div>

<script>
/* ── Constants ── */
const IS_ADMIN = {{ $isAdmin ? 'true' : 'false' }};
const CSRF     = document.querySelector('meta[name="csrf-token"]').content;
const URL_DASH = '{{ route('projects.dashboard') }}';
const URL_LIST = '{{ route('projects.list') }}';
const URL_SAVE = '{{ route('projects.store') }}';
const URL_TASK = '{{ route('tasks.store') }}';
const STAT_LBL = { active:'Active', on_hold:'On Hold', completed:'Completed', archived:'Archived' };
const PRI_DOT  = { urgent:'d-red', high:'d-amb', normal:'d-blue', low:'d-slate' };

/* ── State ── */
let view     = 'dashboard';   // dashboard | list | detail
let mode     = 'table';       // table | board
let prevView = 'dashboard';   // for back button
let projects = [];             // cached list
let pgSize   = 10;
let pgCur    = 1;
let sortC    = '';
let sortD    = 'asc';
let fTimer   = null;
let dragCard = null;
let dragDone = false;          // prevent click-after-drag
let curProj  = null;           // current detail project object

/* ── Helpers ── */
function esc(s) {
    const d = document.createElement('div');
    d.textContent = s || '';
    return d.innerHTML;
}
function toast(msg, type = 'success') {
    const el = document.createElement('div');
    el.className = `toast toast-${type}`;
    el.textContent = msg;
    document.getElementById('toastWrap').appendChild(el);
    setTimeout(() => el.remove(), 3200);
}
function openModal(id)  { document.getElementById(id).classList.add('open'); }
function closeModal(id) { document.getElementById(id).classList.remove('open'); }

async function apiFetch(url, opts = {}) {
    const { headers: extraHeaders = {}, ...restOpts } = opts;
    const r = await fetch(url, {
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF, ...extraHeaders },
        ...restOpts,
    });
    return r.json();
}
function apiGet(url, params = {}) {
    const u = new URL(url, location.origin);
    Object.entries(params).forEach(([k, v]) => { if (v !== '' && v != null) u.searchParams.set(k, v); });
    return apiFetch(u.toString());
}
function apiSend(url, data, method = 'POST') {
    return apiFetch(url, {
        method,
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data),
    });
}

/* ── View switching ── */
function switchView(v) {
    if (v !== 'detail') prevView = v;
    view = v;

    document.getElementById('dashView').style.display   = v === 'dashboard' ? '' : 'none';
    document.getElementById('listView').style.display   = v === 'list'      ? '' : 'none';
    document.getElementById('detailView').style.display = v === 'detail'    ? '' : 'none';

    // Header controls
    document.getElementById('tabBar').style.display     = v === 'detail'    ? 'none' : '';
    document.getElementById('modeToggle').style.display = v === 'detail'    ? 'none' : '';
    const nb = document.getElementById('btnNewProj');
    if (nb) nb.style.display = v === 'detail' ? 'none' : '';

    // Tab active state
    document.getElementById('btnDash').classList.toggle('on', v === 'dashboard');
    document.getElementById('btnList').classList.toggle('on', v === 'list');

    // Load data
    if (v === 'dashboard') loadDash();
    if (v === 'list') { if (mode === 'board') loadBoard(); else loadList(); }
}

function setMode(m) {
    mode = m;
    document.getElementById('btnTable').classList.toggle('on', m === 'table');
    document.getElementById('btnBoard').classList.toggle('on', m === 'board');
    document.getElementById('tblWrap').style.display   = m === 'table' ? '' : 'none';
    document.getElementById('boardWrap').style.display = m === 'board' ? 'grid' : 'none';
    if (m === 'board') loadBoard(); else loadList();
}

function backTo() {
    switchView(prevView || 'dashboard');
}

/* ── Filters ── */
function getF() {
    return {
        status: document.getElementById('fStatus').value,
        lead:   document.getElementById('fLead').value,
        search: document.getElementById('fSearch').value,
    };
}
function onFChange() { pgCur = 1; if (mode === 'board') loadBoard(); else loadList(); }
function debF() { clearTimeout(fTimer); fTimer = setTimeout(() => { pgCur = 1; if (mode === 'board') loadBoard(); else loadList(); }, 320); }

/* ══ DASHBOARD ══ */
async function loadDash() {
    document.getElementById('kpiRow').innerHTML = Array(4).fill(
        '<div class="kpi-card"><div class="skel" style="height:13px;width:55%;margin-bottom:10px"></div><div class="skel" style="height:26px;width:38%"></div></div>'
    ).join('');
    document.getElementById('activeList').innerHTML = '<div class="skel" style="height:80px;border-radius:10px"></div>';
    document.getElementById('actList').innerHTML    = '<div class="skel" style="height:80px;border-radius:10px"></div>';

    const d = await apiGet(URL_DASH);
    if (!d.ok) return;

    // KPIs
    const s = d.stats;
    document.getElementById('kpiRow').innerHTML = `
        <div class="kpi-card"><div class="kpi-lbl">Total Projects</div><div class="kpi-val">${s.total}</div></div>
        <div class="kpi-card"><div class="kpi-lbl">Active</div><div class="kpi-val c-teal">${s.active}</div></div>
        <div class="kpi-card"><div class="kpi-lbl">On Hold</div><div class="kpi-val c-amb">${s.on_hold}</div></div>
        <div class="kpi-card"><div class="kpi-lbl">Completed</div><div class="kpi-val c-grn">${s.completed}</div></div>
    `;

    // Active projects list
    const today = new Date().toISOString().slice(0, 10);
    const ap    = d.activeProjects || [];
    document.getElementById('activeList').innerHTML = !ap.length
        ? '<p class="empty-st">No active projects</p>'
        : ap.map(p => {
            const pct = p.total_tasks > 0 ? Math.round((p.done_tasks / p.total_tasks) * 100) : 0;
            return `<div class="proj-card ${p.overdue ? 'ov' : ''}" onclick="openDetail(${p.id})">
                <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                    <span style="font-weight:600;font-size:.87rem">${esc(p.name)}</span>
                    ${p.overdue ? '<span class="badge b-danger" style="font-size:.68rem">Overdue</span>' : ''}
                </div>
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:6px">
                    <div class="prog-wrap" style="flex:1"><div class="prog-fill" style="width:${pct}%"></div></div>
                    <span style="font-size:.74rem;font-weight:700;color:var(--ppd);min-width:32px;text-align:right">${pct}%</span>
                </div>
                <div style="display:flex;gap:12px;font-size:.72rem;color:var(--txm)">
                    <span>${p.done_tasks}/${p.total_tasks} tasks</span>
                    ${p.lead_name ? `<span>👤 ${esc(p.lead_name)}</span>` : ''}
                    ${p.target_date ? `<span>📅 ${p.target_date}</span>` : ''}
                </div>
            </div>`;
        }).join('');

    // Recent activity
    const acts = d.activity || [];
    document.getElementById('actList').innerHTML = !acts.length
        ? '<p class="empty-st">No recent activity</p>'
        : acts.map(a => {
            const icon = a.action === 'created' ? '✚' : a.action === 'status_changed' ? '↻' : '💬';
            const cls  = a.action === 'created' ? 'i-created' : a.action === 'status_changed' ? 'i-status' : 'i-comment';
            const lbl  = a.action.replace(/_/g, ' ');
            return `<div class="act-item">
                <div class="act-icon ${cls}">${icon}</div>
                <div><strong>${esc(a.staff_name)}</strong> ${lbl} <em>${esc(a.task_title)}</em>${a.project_name ? ` in <strong>${esc(a.project_name)}</strong>` : ''}</div>
            </div>`;
        }).join('');
}

/* ══ LIST / TABLE ══ */
async function loadList() {
    document.getElementById('tblWrap').innerHTML = '<div class="skel" style="height:120px;border-radius:10px;margin-top:4px"></div>';
    const d = await apiGet(URL_LIST, getF());
    if (!d.ok) return;
    projects = d.projects || [];
    renderTable();
}

function renderTable() {
    const wrap = document.getElementById('tblWrap');
    let rows = [...projects];

    // Sort
    if (sortC) {
        rows.sort((a, b) => {
            let va = a[sortC] ?? '', vb = b[sortC] ?? '';
            if (sortC === 'status')   { const o={active:0,on_hold:1,completed:2,archived:3}; va=o[va]??9; vb=o[vb]??9; }
            if (sortC === 'progress') { va=a.total_tasks>0?a.done_tasks/a.total_tasks:0; vb=b.total_tasks>0?b.done_tasks/b.total_tasks:0; }
            if (typeof va==='number'&&typeof vb==='number') return sortD==='asc'?va-vb:vb-va;
            va=String(va).toLowerCase(); vb=String(vb).toLowerCase();
            return sortD==='asc'?va.localeCompare(vb):vb.localeCompare(va);
        });
    }

    const total = rows.length;
    const pages = Math.max(1, Math.ceil(total / pgSize));
    if (pgCur > pages) pgCur = pages;
    const start = (pgCur - 1) * pgSize;
    const paged = rows.slice(start, start + pgSize);

    if (!rows.length) { wrap.innerHTML = '<div class="empty-st">No projects found</div>'; return; }

    const si = c => sortC !== c ? `<span class="si">⇅</span>` : sortD==='asc' ? `<span class="si on">▲</span>` : `<span class="si on">▼</span>`;

    let html = `<div class="dt-bar">
        <span style="font-size:.78rem;color:var(--txm)">Showing ${start+1}–${Math.min(start+pgSize,total)} of ${total} projects</span>
        <label style="display:flex;align-items:center;gap:6px;font-size:.78rem;color:var(--txm)">
            <select class="fsel" style="padding:4px 9px;font-size:.77rem" onchange="pgSize=+this.value;pgCur=1;renderTable()">
                <option value="10" ${pgSize===10?'selected':''}>10</option>
                <option value="25" ${pgSize===25?'selected':''}>25</option>
                <option value="50" ${pgSize===50?'selected':''}>50</option>
            </select> per page
        </label>
    </div>
    <div class="tbl-wrap"><table class="p-tbl"><thead><tr>
        <th onclick="tsort('name')">Name ${si('name')}</th>
        <th onclick="tsort('lead_name')">Lead ${si('lead_name')}</th>
        <th onclick="tsort('status')">Status ${si('status')}</th>
        <th>Tasks</th>
        <th onclick="tsort('progress')">Progress ${si('progress')}</th>
        <th onclick="tsort('target_date')">Target Date ${si('target_date')}</th>
    </tr></thead><tbody>`;

    paged.forEach(p => {
        const pct = p.total_tasks > 0 ? Math.round((p.done_tasks / p.total_tasks) * 100) : 0;
        html += `<tr onclick="openDetail(${p.id})">
            <td style="font-weight:600">${esc(p.name)}</td>
            <td>${p.lead_name ? esc(p.lead_name) : '—'}</td>
            <td><span class="badge b-${p.status}">${STAT_LBL[p.status]||p.status}</span></td>
            <td style="color:var(--txm)">${p.done_tasks}/${p.total_tasks}</td>
            <td><div style="display:flex;align-items:center;gap:8px">
                <div class="prog-wrap" style="width:80px"><div class="prog-fill" style="width:${pct}%"></div></div>
                <span style="font-size:.74rem;font-weight:600">${pct}%</span>
            </div></td>
            <td style="font-size:.81rem;color:var(--txm)">${p.target_date||'—'}</td>
        </tr>`;
    });

    html += '</tbody></table></div>';

    if (pages > 1) {
        html += '<div class="pager">';
        html += `<button class="pg-btn" onclick="goPage(1)" ${pgCur<=1?'disabled':''}>«</button>`;
        html += `<button class="pg-btn" onclick="goPage(${pgCur-1})" ${pgCur<=1?'disabled':''}>‹</button>`;
        for (let i = Math.max(1,pgCur-2); i <= Math.min(pages,pgCur+2); i++)
            html += `<button class="pg-btn ${i===pgCur?'on':''}" onclick="goPage(${i})">${i}</button>`;
        html += `<button class="pg-btn" onclick="goPage(${pgCur+1})" ${pgCur>=pages?'disabled':''}>›</button>`;
        html += `<button class="pg-btn" onclick="goPage(${pages})" ${pgCur>=pages?'disabled':''}>»</button>`;
        html += '</div>';
    }
    wrap.innerHTML = html;
}

function tsort(c) {
    if (sortC === c) sortD = sortD === 'asc' ? 'desc' : 'asc'; else { sortC = c; sortD = 'asc'; }
    renderTable();
}
function goPage(p) {
    const pages = Math.max(1, Math.ceil(projects.length / pgSize));
    if (p < 1 || p > pages) return;
    pgCur = p;
    renderTable();
}

/* ══ BOARD ══ */
async function loadBoard() {
    // Show loading in each column
    ['active','on_hold','completed','archived'].forEach(s => {
        const col = document.getElementById('kb-' + s);
        col.innerHTML = '<div class="skel" style="height:60px;border-radius:9px"></div>';
        document.getElementById('kc-' + s).textContent = '…';
    });

    const d = await apiGet(URL_LIST, getF());
    if (!d.ok) return;
    const ps    = d.projects || [];
    const today = new Date().toISOString().slice(0, 10);

    ['active','on_hold','completed','archived'].forEach(status => {
        const col   = document.getElementById('kb-' + status);
        const cnt   = document.getElementById('kc-' + status);
        const items = ps.filter(p => p.status === status);
        cnt.textContent = items.length;

        if (!items.length) { col.innerHTML = '<div class="kb-empty">No projects</div>'; return; }

        col.innerHTML = items.map(p => {
            const pct    = p.total_tasks > 0 ? Math.round((p.done_tasks / p.total_tasks) * 100) : 0;
            const isOver = p.overdue;
            return `<div class="kb-card ${isOver?'ov':''}" draggable="true" data-id="${p.id}" onclick="kbClick(event,${p.id})">
                <div class="kb-card-title">${esc(p.name)}</div>
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
                    <div class="prog-wrap" style="flex:1"><div class="prog-fill" style="width:${pct}%"></div></div>
                    <span style="font-size:.7rem;font-weight:600;color:var(--ppd)">${pct}%</span>
                </div>
                <div class="kb-card-meta">
                    ${p.lead_name ? `<span>👤 ${esc(p.lead_name)}</span>` : ''}
                    <span>${p.done_tasks}/${p.total_tasks} tasks</span>
                    ${p.target_date ? `<span style="${isOver?'color:var(--red)':''}">${p.target_date}</span>` : ''}
                </div>
            </div>`;
        }).join('');

        attachDrag(col);
    });
}

// Open detail only if a real click (not end of drag)
function kbClick(e, id) {
    if (dragDone) { dragDone = false; return; }
    openDetail(id);
}

function attachDrag(col) {
    col.querySelectorAll('.kb-card').forEach(card => {
        card.addEventListener('dragstart', e => {
            dragCard = card;
            dragDone = false;
            card.classList.add('dragging');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/plain', card.dataset.id);
        });
        card.addEventListener('dragend', () => {
            card.classList.remove('dragging');
            dragDone = true;
            setTimeout(() => dragDone = false, 350);
            dragCard = null;
            document.querySelectorAll('.kb-body').forEach(c => c.classList.remove('drag-over'));
        });
    });
}

/* Board column drop events (set once on DOMContentLoaded) */
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.kb-body').forEach(col => {
        col.addEventListener('dragover', e => {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
            col.classList.add('drag-over');
            if (dragCard) {
                const after = dragAfter(col, e.clientY);
                after ? col.insertBefore(dragCard, after) : col.appendChild(dragCard);
            }
        });
        col.addEventListener('dragleave', e => {
            if (!col.contains(e.relatedTarget)) col.classList.remove('drag-over');
        });
        col.addEventListener('drop', async e => {
            e.preventDefault();
            col.classList.remove('drag-over');
            const id        = e.dataTransfer.getData('text/plain');
            const newStatus = col.dataset.status;
            if (!id || !newStatus) return;
            const r = await apiSend(`/projects/${id}`, { status: newStatus }, 'PUT');
            if (r.ok) { toast('Project moved to ' + STAT_LBL[newStatus]); loadBoard(); }
            else { toast(r.message || 'Move failed', 'error'); loadBoard(); }
        });
    });
});

function dragAfter(container, y) {
    const els = [...container.querySelectorAll('.kb-card:not(.dragging)')];
    return els.reduce((closest, child) => {
        const box    = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        if (offset < 0 && offset > closest.offset) return { offset, element: child };
        return closest;
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}

/* ══ DETAIL ══ */
async function openDetail(id) {
    switchView('detail');
    document.getElementById('detailContent').innerHTML =
        '<div class="skel" style="height:180px;border-radius:12px"></div>';

    const d = await apiFetch(`/projects/${id}`, { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF } });
    if (!d.ok || !d.project) {
        document.getElementById('detailContent').innerHTML = '<p class="empty-st">Project not found.</p>';
        return;
    }

    curProj = d.project;
    const p     = curProj;
    const tasks = d.tasks || [];
    const done  = tasks.filter(t => t.status === 'done').length;
    const pct   = tasks.length > 0 ? Math.round((done / tasks.length) * 100) : 0;
    const today = new Date().toISOString().slice(0, 10);

    let html = `<div class="det-hd">
        <div>
            <div class="det-title">${esc(p.name)}</div>
            <span class="badge b-${p.status}">${STAT_LBL[p.status]||p.status}</span>
        </div>
        <div class="det-acts">
            <button class="btn btn-primary btn-sm" onclick="openAddTask(${p.id})">+ Add Task</button>
            ${IS_ADMIN ? `
            <button class="btn btn-ghost btn-sm" onclick="openEditModal()">Edit</button>
            <button class="btn btn-danger btn-sm" onclick="deleteProject(${p.id})">Delete</button>` : ''}
        </div>
    </div>`;

    if (p.description) {
        html += `<div class="back-desc">${esc(p.description).replace(/\n/g,'<br>')}</div>`;
    }

    html += `<div class="meta-grid">
        <div class="meta-item"><div class="meta-lbl">Project Lead</div><div class="meta-val">${p.lead_name ? esc(p.lead_name) : '—'}</div></div>
        <div class="meta-item"><div class="meta-lbl">Start Date</div><div class="meta-val">${p.start_date || '—'}</div></div>
        <div class="meta-item"><div class="meta-lbl">Target Date</div><div class="meta-val">${p.target_date || '—'}</div></div>
        <div class="meta-item"><div class="meta-lbl">Created By</div><div class="meta-val">${p.creator_name ? esc(p.creator_name) : '—'}</div></div>
    </div>`;

    html += `<div class="prog-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
            <span style="font-size:.83rem;font-weight:700">Overall Progress</span>
            <span style="font-size:.9rem;font-weight:800;color:var(--ppd)">${pct}%</span>
        </div>
        <div class="prog-wrap" style="height:9px"><div class="prog-fill" style="width:${pct}%;height:9px"></div></div>
        <div style="display:flex;gap:16px;margin-top:10px;flex-wrap:wrap">
            <span style="font-size:.75rem;color:var(--grn);font-weight:600">${done} Done</span>
            <span style="font-size:.75rem;color:#3b82f6;font-weight:600">${tasks.filter(t=>t.status==='in_progress').length} In Progress</span>
            <span style="font-size:.75rem;color:var(--txm);font-weight:600">${tasks.filter(t=>t.status==='pending').length} Pending</span>
            <span style="font-size:.75rem;color:var(--red);font-weight:600">${tasks.filter(t=>t.status==='blocked').length} Blocked</span>
        </div>
    </div>`;

    [['pending','Pending'],['in_progress','In Progress'],['blocked','Blocked'],['done','Done']].forEach(([key, label]) => {
        const g = tasks.filter(t => t.status === key);
        html += `<div class="tg">
            <div class="tg-hd">${label} <span class="tg-cnt">${g.length}</span></div>`;
        if (!g.length) {
            html += `<p style="font-size:.78rem;color:var(--txs);padding:4px 0">No tasks in this group</p>`;
        } else {
            g.forEach(t => {
                const ov  = t.due_date && t.due_date < today && t.status !== 'done';
                const due = t.due_date ? new Date(t.due_date + 'T00:00:00').toLocaleDateString('en-IN', {day:'numeric',month:'short'}) : '';
                html += `<div class="tg-item ${ov?'ov':''}" onclick="location.href='/tasks?open=${t.id}'" title="Open task in Tasks page">
                    <span class="p-dot ${PRI_DOT[t.priority]||'d-blue'}"></span>
                    <span class="tg-title">${esc(t.title)}</span>
                    ${t.assignee_name ? `<span class="tg-meta">👤 ${esc(t.assignee_name)}</span>` : ''}
                    ${due ? `<span class="tg-meta" style="${ov?'color:var(--red)':''}">${due}</span>` : ''}
                    <span class="tg-meta" style="color:var(--pp);font-size:.68rem">↗</span>
                </div>`;
            });
        }
        html += '</div>';
    });

    document.getElementById('detailContent').innerHTML = html;
}

/* ══ PROJECT CRUD ══ */
function openCreateModal() {
    document.getElementById('editId').value   = '';
    document.getElementById('fName').value    = '';
    document.getElementById('fDesc').value    = '';
    document.getElementById('fStatusM').value = 'active';
    document.getElementById('fLeadM').value   = '';
    document.getElementById('fStart').value   = '';
    document.getElementById('fTarget').value  = '';
    document.getElementById('projModalTitle').textContent = 'New Project';
    document.getElementById('projSaveBtn').textContent    = 'Create Project';
    openModal('projModal');
    setTimeout(() => document.getElementById('fName').focus(), 80);
}

function openEditModal() {
    if (!curProj) return;
    const p = curProj;
    document.getElementById('editId').value   = p.id;
    document.getElementById('fName').value    = p.name || '';
    document.getElementById('fDesc').value    = p.description || '';
    document.getElementById('fStatusM').value = p.status || 'active';
    document.getElementById('fLeadM').value   = p.project_lead || '';
    document.getElementById('fStart').value   = p.start_date || '';
    document.getElementById('fTarget').value  = p.target_date || '';
    document.getElementById('projModalTitle').textContent = 'Edit Project';
    document.getElementById('projSaveBtn').textContent    = 'Update Project';
    openModal('projModal');
    setTimeout(() => document.getElementById('fName').focus(), 80);
}

async function saveProject() {
    const name = document.getElementById('fName').value.trim();
    if (!name) { toast('Project name is required', 'error'); return; }

    const editId  = document.getElementById('editId').value;
    const payload = {
        name,
        description:  document.getElementById('fDesc').value.trim() || null,
        status:       document.getElementById('fStatusM').value,
        project_lead: document.getElementById('fLeadM').value || null,
        start_date:   document.getElementById('fStart').value || null,
        target_date:  document.getElementById('fTarget').value || null,
    };

    const btn = document.getElementById('projSaveBtn');
    btn.disabled    = true;
    btn.textContent = editId ? 'Updating…' : 'Creating…';

    const r = editId
        ? await apiSend(`/projects/${editId}`, payload, 'PUT')
        : await apiSend(URL_SAVE, payload);

    btn.disabled = false;
    document.getElementById('projSaveBtn').textContent = editId ? 'Update Project' : 'Create Project';

    if (r.ok) {
        toast(editId ? 'Project updated' : 'Project created');
        closeModal('projModal');
        if (view === 'detail') {
            openDetail(editId || r.project_id);
        } else {
            loadDash();
            if (view === 'list') { if (mode === 'board') loadBoard(); else loadList(); }
        }
    } else {
        toast(r.message || 'Save failed', 'error');
    }
}

/* ── Alert Dialog ── */
let _alertCb = null;
function showAlert(title, desc, confirmLabel, cb) {
    document.getElementById('alertTitle').textContent      = title;
    document.getElementById('alertDesc').textContent       = desc;
    document.getElementById('alertConfirmBtn').textContent = confirmLabel || 'Confirm';
    _alertCb = cb;
    document.getElementById('alertDialog').classList.add('open');
}
function closeAlert() {
    document.getElementById('alertDialog').classList.remove('open');
    _alertCb = null;
}
function confirmAlert() {
    closeAlert();
    if (typeof _alertCb === 'function') _alertCb();
}

async function deleteProject(id) {
    showAlert(
        'Delete Project?',
        'This will permanently delete the project. All linked tasks will be unlinked. This action cannot be undone.',
        'Delete',
        async () => {
            const r = await apiSend(`/projects/${id}`, {}, 'DELETE');
            if (r.ok) { toast('Project deleted'); switchView('dashboard'); }
            else toast(r.message || 'Delete failed', 'error');
        }
    );
}

/* ══ ADD TASK ══ */
function openAddTask(projId) {
    document.getElementById('atProjId').value   = projId;
    document.getElementById('atTitle').value    = '';
    document.getElementById('atDesc').value     = '';
    document.getElementById('atDue').value      = '';
    document.getElementById('atPriority').value = 'normal';
    document.getElementById('atAssignee').value = '';
    openModal('addTaskModal');
    setTimeout(() => document.getElementById('atTitle').focus(), 80);
}

async function saveTask() {
    const title = document.getElementById('atTitle').value.trim();
    if (!title) { toast('Task title is required', 'error'); return; }
    const projId = document.getElementById('atProjId').value;
    const r = await apiSend(URL_TASK, {
        title,
        description: document.getElementById('atDesc').value.trim() || null,
        assigned_to: document.getElementById('atAssignee').value || null,
        priority:    document.getElementById('atPriority').value,
        due_date:    document.getElementById('atDue').value || null,
        category:    'other',
        project_id:  parseInt(projId),
        status:      'pending',
    });
    if (r.ok) { toast('Task added successfully'); closeModal('addTaskModal'); openDetail(projId); }
    else toast(r.message || 'Failed to add task', 'error');
}

/* Close modals on backdrop click */
document.querySelectorAll('.modal-overlay').forEach(m => {
    m.addEventListener('click', e => { if (e.target === e.currentTarget) closeModal(m.id); });
});
document.getElementById('alertDialog').addEventListener('click', e => {
    if (e.target === e.currentTarget) closeAlert();
});

/* ── Init ── */
loadDash();
</script>
</x-layouts.app>
