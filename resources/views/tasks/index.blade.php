<x-layouts.app :title="$scope === 'all' ? 'All Tasks' : 'My Tasks'">
@push('styles')
<style>
  /* Alias old erp tokens → design-system tokens (used by ported detail/modal) */
  .tk-scope{
    --erp-primary:var(--brand-red); --erp-primary-dark:var(--brand-red-dark);
    --erp-border:var(--border); --erp-text:var(--text-1); --erp-text-muted:var(--text-2);
    --erp-text-subtle:var(--text-3); --erp-hover:var(--bg-neutral); --erp-muted:var(--bg-neutral);
    --erp-bg:var(--bg-card); --erp-danger:var(--danger);
  }

  /* ---------- Content ---------- */
  .tk-content{padding:24px 32px 48px;min-width:0;}
  .page-head{display:flex;align-items:center;gap:14px;margin-bottom:20px;flex-wrap:wrap;}
  .page-title{font-size:26px;font-weight:600;letter-spacing:-.6px;color:var(--text-1);}
  .page-count{font-size:13px;color:var(--text-3);font-weight:500;}
  .head-spacer{flex:1;}
  .scope-toggle{display:inline-flex;background:var(--bg-neutral);border-radius:var(--radius-sm);padding:3px;gap:2px;}
  .scope-toggle button{border:none;background:transparent;font-family:inherit;font-size:12.5px;font-weight:500;color:var(--text-2);padding:5px 13px;border-radius:4px;cursor:pointer;}
  .scope-toggle button.on{background:var(--bg-card);color:var(--text-1);box-shadow:0 1px 2px rgba(15,23,42,.08);}
  .btn{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:var(--radius-sm);font-family:inherit;font-size:13px;font-weight:500;cursor:pointer;border:1px solid transparent;text-decoration:none;}
  .btn svg{width:15px;height:15px;stroke:currentColor;stroke-width:2;fill:none;}
  .btn-primary{background:var(--brand-red);color:#fff;}
  .btn-primary:hover{background:var(--brand-red-dark);}
  .btn-secondary{background:var(--bg-card);color:var(--text-1);border-color:var(--border);}
  .btn-secondary:hover{border-color:var(--text-3);}

  /* ---------- Action chips ---------- */
  .chip-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:20px;}
  .chip{text-align:left;border:1px solid;border-radius:var(--radius-lg);padding:13px 16px;cursor:pointer;background:var(--bg-card);font-family:inherit;}
  .chip .tag{font-size:10px;font-weight:600;letter-spacing:.6px;text-transform:uppercase;display:flex;align-items:center;justify-content:space-between;}
  .chip .fig{font-size:24px;font-weight:500;line-height:1.1;margin-top:7px;}
  .chip .sub{font-size:11.5px;margin-top:2px;}
  .chip.danger{background:var(--brand-red-soft);border-color:var(--brand-red-border);color:var(--danger);}
  .chip.danger .fig{color:var(--danger-text);}
  .chip.warning{background:var(--warning-soft);border-color:var(--warning-border);color:var(--warning);}
  .chip.warning .fig{color:var(--warning-text);}
  .chip.info{background:var(--info-soft);border-color:var(--info-border);color:var(--info);}
  .chip.info .fig{color:var(--info-text);}
  .chip.success{background:var(--success-soft);border-color:var(--success-border);color:var(--success);}
  .chip.success .fig{color:var(--success-text);}
  .chip.active{outline:2px solid currentColor;outline-offset:1px;}
  .chip .dot-arrow{opacity:.6;}

  /* ---------- Toolbar / filter pills ---------- */
  .toolbar{display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:14px;}
  .search{position:relative;}
  .search>svg{position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;stroke:var(--text-3);stroke-width:1.8;fill:none;}
  .search input{height:34px;border:1px solid var(--border);border-radius:var(--radius-sm);padding:0 12px 0 32px;font-family:inherit;font-size:13px;color:var(--text-1);background:var(--bg-card);outline:none;width:230px;}
  .search input:focus{border-color:var(--brand-red);box-shadow:0 0 0 3px var(--brand-red-soft);}
  .search input::placeholder{color:var(--text-3);}
  .pill-wrap{position:relative;}
  .filter-pill{display:inline-flex;align-items:center;gap:7px;padding:7px 11px;border:1px solid var(--border);border-radius:var(--radius-sm);background:var(--bg-card);font-size:12.5px;color:var(--text-2);cursor:pointer;font-family:inherit;}
  .filter-pill:hover{border-color:var(--text-3);}
  .filter-pill.has{border-color:var(--brand-red-border);background:var(--brand-red-soft);color:var(--brand-red-dark);}
  .filter-pill svg{width:13px;height:13px;stroke:currentColor;stroke-width:2;fill:none;}
  .filter-pill .cnt{background:var(--brand-red);color:#fff;border-radius:var(--radius-pill);font-size:10px;font-weight:600;min-width:16px;height:16px;display:inline-flex;align-items:center;justify-content:center;padding:0 4px;}
  .popover{position:absolute;top:calc(100% + 6px);left:0;z-index:30;background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-md);box-shadow:0 8px 24px rgba(15,23,42,.12);padding:6px;min-width:190px;max-height:280px;overflow-y:auto;display:none;}
  .popover.open{display:block;}
  .pop-item{display:flex;align-items:center;gap:9px;padding:7px 9px;border-radius:var(--radius-sm);font-size:13px;color:var(--text-1);cursor:pointer;}
  .pop-item:hover{background:var(--bg-neutral);}
  .pop-item input{accent-color:var(--brand-red);width:15px;height:15px;}
  .pop-item .sd{width:7px;height:7px;border-radius:50%;}
  .clear-link{font-size:12.5px;color:var(--text-3);font-weight:500;background:none;border:none;cursor:pointer;font-family:inherit;padding:7px 8px;}
  .clear-link:hover{color:var(--brand-red);}
  .tool-spacer{flex:1;}
  .view-toggle{display:inline-flex;border:1px solid var(--border);border-radius:var(--radius-sm);overflow:hidden;}
  .view-toggle button{border:none;background:var(--bg-card);padding:7px 9px;cursor:pointer;color:var(--text-3);display:flex;align-items:center;}
  .view-toggle button svg{width:15px;height:15px;stroke:currentColor;stroke-width:1.8;fill:none;}
  .view-toggle button.on{background:var(--brand-red-soft);color:var(--brand-red-dark);}
  .view-toggle button+button{border-left:1px solid var(--border);}

  /* ---------- Badges ---------- */
  .badge{display:inline-flex;align-items:center;gap:5px;padding:2px 9px;border-radius:var(--radius-pill);font-size:11px;font-weight:500;white-space:nowrap;}
  .badge .sd{width:6px;height:6px;border-radius:50%;}
  .b-success{background:var(--success-soft);color:var(--success);} .b-success .sd{background:var(--success);}
  .b-warning{background:var(--warning-soft);color:var(--warning);} .b-warning .sd{background:var(--warning);}
  .b-danger{background:var(--brand-red-soft);color:var(--brand-red-dark);} .b-danger .sd{background:var(--brand-red);}
  .b-info{background:var(--info-soft);color:var(--info);} .b-info .sd{background:var(--info);}
  .b-neutral{background:var(--bg-neutral);color:var(--text-2);} .b-neutral .sd{background:var(--text-3);}

  /* ---------- Table ---------- */
  .table-wrap{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);overflow:hidden;}
  .demo-table{width:100%;border-collapse:collapse;font-size:13px;}
  .demo-table thead th{text-align:left;font-size:10px;font-weight:600;color:var(--text-3);letter-spacing:.6px;text-transform:uppercase;padding:12px 14px;border-bottom:1px solid var(--border);user-select:none;white-space:nowrap;background:var(--bg-neutral);}
  .demo-table thead th.sortable{cursor:pointer;}
  .demo-table thead th.sortable:hover{color:var(--text-1);}
  .demo-table thead th .si{opacity:.35;margin-left:4px;font-size:9px;}
  .demo-table thead th.sorted .si{opacity:1;color:var(--brand-red);}
  .demo-table tbody tr{border-bottom:1px solid var(--border-soft);cursor:pointer;}
  .demo-table tbody tr:last-child{border-bottom:none;}
  .demo-table tbody tr:hover{background:var(--bg-neutral);}
  .demo-table tbody tr.urgent-row{background:linear-gradient(90deg,var(--brand-red-soft) 0%,transparent 70%);}
  .demo-table tbody tr.urgent-row:hover{background:linear-gradient(90deg,var(--brand-red-soft) 0%,var(--bg-neutral) 80%);}
  .demo-table td{padding:13px 14px;color:var(--text-2);vertical-align:middle;}
  .demo-table td.primary{color:var(--text-1);font-weight:500;}
  .complete-toggle{width:20px;height:20px;border-radius:50%;border:1.8px solid var(--text-3);background:transparent;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0;}
  .complete-toggle svg{width:12px;height:12px;stroke:#fff;stroke-width:3;fill:none;}
  .complete-toggle:not(.done) svg{opacity:0;stroke:var(--brand-red);}
  .complete-toggle:not(.done):hover{border-color:var(--brand-red);}
  .complete-toggle:not(.done):hover svg{opacity:.45;}
  .complete-toggle.done{background:var(--success);border-color:var(--success);}
  .task-cell{max-width:340px;}
  .task-cell .primary.done-title{color:var(--text-3);text-decoration:line-through;}
  .task-cell .pname{font-size:11px;color:var(--text-3);font-weight:400;margin-top:1px;}
  .avatar-cell{display:flex;align-items:center;gap:8px;color:var(--text-1);}
  .av-sm{width:24px;height:24px;border-radius:50%;background:var(--bg-neutral-2);color:var(--text-2);display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:600;flex-shrink:0;}
  .due{display:inline-flex;align-items:center;gap:6px;}
  .due.over{color:var(--brand-red-dark);font-weight:500;}
  .due.today{color:var(--warning);font-weight:500;}
  .due .lbl{font-size:10px;padding:1px 6px;border-radius:var(--radius-pill);}
  .due.over .lbl{background:var(--brand-red-soft);}
  .due.today .lbl{background:var(--warning-soft);}
  .table-foot{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;border-top:1px solid var(--border);font-size:12.5px;color:var(--text-3);}

  /* ---------- Empty state ---------- */
  .empty{display:none;flex-direction:column;align-items:center;justify-content:center;padding:64px 20px;text-align:center;}
  .empty.show{display:flex;}
  .empty .ring{width:64px;height:64px;border-radius:50%;border:1.5px solid var(--border);display:flex;align-items:center;justify-content:center;margin-bottom:16px;color:var(--text-3);}
  .empty .ring svg{width:26px;height:26px;stroke:currentColor;stroke-width:1.6;fill:none;}
  .empty h3{font-size:16px;font-weight:600;color:var(--text-1);margin-bottom:4px;}
  .empty p{font-size:13px;color:var(--text-3);margin-bottom:18px;}

  /* ---------- Board ---------- */
  .board{display:none;grid-template-columns:repeat(4,1fr);gap:12px;align-items:start;}
  .board.show{display:grid;}
  .board-col{background:var(--bg-card);border:1px solid var(--border);border-radius:var(--radius-lg);padding:10px;}
  .board-col-head{display:flex;align-items:center;gap:7px;padding:4px 6px 10px;font-size:12px;font-weight:600;color:var(--text-1);}
  .board-col-head .sd{width:7px;height:7px;border-radius:50%;}
  .board-col-head .n{margin-left:auto;font-size:11px;color:var(--text-3);font-weight:500;}
  .board-card{border:1px solid var(--border);border-radius:var(--radius-md);padding:11px 12px;margin-bottom:8px;background:var(--bg-card);cursor:pointer;}
  .board-card:last-child{margin-bottom:0;}
  .board-card:hover{border-color:var(--text-3);}
  .board-card .t{font-size:13px;font-weight:500;color:var(--text-1);line-height:1.4;margin-bottom:9px;}
  .board-card .meta{display:flex;align-items:center;justify-content:space-between;gap:8px;}
  .board-card .pname{font-size:10px;color:var(--text-3);margin-top:6px;}

  /* ---------- Modal (ported) ---------- */
  .modal-backdrop{position:fixed;inset:0;background:rgba(0,0,0,.45);backdrop-filter:blur(3px);z-index:500;display:none;align-items:center;justify-content:center;padding:16px;}
  .modal-backdrop.open{display:flex;}
  .modal-box{background:var(--bg-card);border-radius:var(--radius-lg);border:1px solid var(--border);box-shadow:0 25px 50px -12px rgba(0,0,0,.25);width:100%;max-width:540px;max-height:calc(100vh - 32px);display:flex;flex-direction:column;}
  .modal-box.modal-lg{max-width:680px;}
  .modal-head{padding:16px 20px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:center;}
  .modal-head h3{font-size:16px;font-weight:600;color:var(--text-1);margin:0;}
  .modal-close{background:none;border:none;cursor:pointer;color:var(--text-2);padding:2px;width:28px;height:28px;display:flex;align-items:center;justify-content:center;border-radius:4px;transition:all .15s;font-size:20px;line-height:1;}
  .modal-close:hover{background:var(--bg-neutral);color:var(--text-1);}
  .modal-body{padding:20px;overflow-y:auto;flex:1;}
  .modal-foot{padding:12px 20px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:8px;}

  /* Form */
  .f-group{margin-bottom:14px;}
  .f-label{display:block;font-size:12px;font-weight:600;color:var(--text-1);margin-bottom:5px;}
  .f-input{width:100%;padding:7px 10px;font-size:13px;font-family:inherit;border:1px solid var(--border);border-radius:6px;background:var(--bg-card);color:var(--text-1);transition:border-color .15s;}
  .f-input:focus{outline:none;border-color:var(--brand-red);box-shadow:0 0 0 3px var(--brand-red-soft);}
  textarea.f-input{resize:vertical;min-height:72px;}
  .f-row{display:flex;gap:12px;}
  .f-row .f-group{flex:1;}
  .f-err{font-size:11px;color:var(--danger);margin-top:3px;display:none;}
  .chip-group{display:flex;flex-wrap:wrap;gap:6px;}
  .chip-opt input{display:none;}
  .chip-opt{cursor:pointer;}
  .chip-lbl{display:inline-block;padding:4px 10px;background:var(--bg-neutral);border:1px solid var(--border);border-radius:99px;font-size:12px;transition:all .15s;user-select:none;color:var(--text-1);}
  .chip-opt input:checked + .chip-lbl{background:var(--brand-red-soft);border-color:var(--brand-red);color:var(--brand-red-dark);font-weight:600;}

  /* Detail */
  .detail-meta{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:14px;}
  .detail-badge{display:inline-flex;align-items:center;gap:5px;padding:3px 9px;border-radius:20px;font-size:12px;font-weight:500;}
  .dbg-pending{background:var(--warning-soft);color:var(--warning);}
  .dbg-in_progress{background:var(--info-soft);color:var(--info);}
  .dbg-blocked{background:var(--brand-red-soft);color:var(--brand-red-dark);}
  .dbg-done{background:var(--success-soft);color:var(--success);}
  .dbg-urgent{background:var(--brand-red-soft);color:var(--brand-red-dark);}
  .dbg-high{background:var(--warning-soft);color:var(--warning);}
  .dbg-normal{background:var(--info-soft);color:var(--info);}
  .dbg-low{background:var(--bg-neutral);color:var(--text-2);}
  .detail-desc{background:var(--bg-neutral);border-radius:6px;padding:12px;font-size:13px;line-height:1.6;white-space:pre-wrap;margin-bottom:14px;color:var(--text-1);}
  .detail-fields{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:14px;}
  .detail-fields > div{flex:1;min-width:120px;}
  .comment-item{padding:10px;background:var(--bg-neutral);border-radius:6px;margin-bottom:8px;}
  .comment-meta{display:flex;justify-content:space-between;margin-bottom:4px;}
  .comment-author{font-size:12px;font-weight:600;color:var(--text-1);}
  .comment-time{font-size:11px;color:var(--text-2);}
  .comment-text{font-size:13px;line-height:1.5;color:var(--text-2);}
  .history-item{display:flex;justify-content:space-between;align-items:center;gap:8px;padding:7px 0;border-bottom:1px solid var(--border);font-size:12px;}
  .history-item:last-child{border-bottom:none;}
  .section-title{font-size:13px;font-weight:600;color:var(--text-1);margin:16px 0 10px;}

  /* Status dots (detail) */
  .erp-dot{width:7px;height:7px;border-radius:50%;display:inline-block;}
  .dot-pending{background:var(--warning);} .dot-in_progress{background:var(--info);}
  .dot-blocked{background:var(--brand-red);} .dot-done{background:var(--success);}
  .dot-urgent{background:var(--brand-red);} .dot-high{background:var(--warning);}
  .dot-normal{background:var(--info);} .dot-low{background:var(--text-3);}

  /* Toast */
  .toast-stack{position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px;}
  .toast{padding:10px 16px;border-radius:6px;font-size:13px;font-weight:500;box-shadow:0 4px 12px rgba(0,0,0,.12);animation:tslide .25s ease;}
  .toast-ok{background:var(--success-soft);color:var(--success);border:1px solid var(--success-border);}
  .toast-err{background:var(--brand-red-soft);color:var(--brand-red-dark);border:1px solid var(--brand-red-border);}
  @keyframes tslide{from{transform:translateX(110%);opacity:0;}to{transform:none;opacity:1;}}

  @media(max-width:1100px){.chip-strip{grid-template-columns:repeat(2,1fr);}.board{grid-template-columns:repeat(2,1fr);}}
  @media(max-width:768px){.tk-content{padding:16px;}.search input{width:100%;}.search{flex:1;}}
</style>
@endpush

<div class="tk-scope">
<main class="tk-content">
  <div class="page-head">
    <span class="page-title">{{ $scope === 'all' ? 'All tasks' : 'My tasks' }}</span>
    <span class="page-count num" id="count">0 tasks</span>
    <div class="head-spacer"></div>
    @if($scope === 'all')
    {{-- All Tasks: toggle filters this page client-side (no navigation) --}}
    <div class="scope-toggle">
      <button id="scopeMy" onclick="setScope('my')">My tasks</button>
      <button id="scopeAll" class="on" onclick="setScope('all')">All tasks</button>
    </div>
    @else
    {{-- My Tasks: "All tasks" loads the full list (admins only) --}}
    <div class="scope-toggle">
      <button class="on">My tasks</button>
      @if($isAdmin)
      <button onclick="location.href='{{ route('tasks') }}'">All tasks</button>
      @endif
    </div>
    @endif
    <button class="btn btn-primary" onclick="openCreate()"><svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>Add task</button>
  </div>

  {{-- Action chips --}}
  <div class="chip-strip">
    <button class="chip danger" data-quick="overdue">
      <div class="tag">Overdue<span class="dot-arrow">→</span></div>
      <div class="fig num" id="figOverdue">0</div><div class="sub">Past due · clear these first</div>
    </button>
    <button class="chip warning" data-quick="today">
      <div class="tag">Due today<span class="dot-arrow">→</span></div>
      <div class="fig num" id="figToday">0</div><div class="sub">Closing today</div>
    </button>
    <button class="chip info" data-quick="in_progress">
      <div class="tag">In progress<span class="dot-arrow">→</span></div>
      <div class="fig num" id="figProgress">0</div><div class="sub">Actively being worked</div>
    </button>
    <button class="chip success" data-quick="done">
      <div class="tag">Completed<span class="dot-arrow">→</span></div>
      <div class="fig num" id="figDone">0</div><div class="sub">Marked done</div>
    </button>
  </div>

  {{-- Toolbar --}}
  <div class="toolbar">
    <div class="search">
      <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="7"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
      <input id="search" type="text" placeholder="Search tasks…">
    </div>
    <div class="pill-wrap"><button class="filter-pill" data-pop="status"><svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/></svg>Status<svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></button>
      <div class="popover" id="pop-status"></div></div>
    <div class="pill-wrap"><button class="filter-pill" data-pop="priority"><svg viewBox="0 0 24 24"><path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/><line x1="4" y1="22" x2="4" y2="15"/></svg>Priority<svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></button>
      <div class="popover" id="pop-priority"></div></div>
    @if($scope === 'all')
    <div class="pill-wrap"><button class="filter-pill" data-pop="assignee"><svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>Assignee<svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></button>
      <div class="popover" id="pop-assignee"></div></div>
    @endif
    <div class="pill-wrap"><button class="filter-pill" data-pop="project"><svg viewBox="0 0 24 24"><path d="M3 7a2 2 0 0 1 2-2h4l2 3h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/></svg>Project<svg viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg></button>
      <div class="popover" id="pop-project"></div></div>
    <button class="clear-link" id="clearBtn" style="display:none">Clear all</button>
    <div class="tool-spacer"></div>
    <div class="view-toggle">
      <button id="vList" class="on" title="List view"><svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg></button>
      <button id="vBoard" title="Board view"><svg viewBox="0 0 24 24"><rect x="3" y="3" width="6" height="18" rx="1"/><rect x="10" y="3" width="6" height="12" rx="1"/><rect x="17" y="3" width="4" height="8" rx="1"/></svg></button>
    </div>
  </div>

  {{-- List view --}}
  <div class="table-wrap" id="listView">
    <table class="demo-table">
      <thead>
        <tr>
          <th style="width:38px" title="Mark complete"></th>
          <th class="sortable" data-sort="title">Task <span class="si">↕</span></th>
          <th class="sortable" data-sort="status">Status <span class="si">↕</span></th>
          <th class="sortable" data-sort="priority">Priority <span class="si">↕</span></th>
          <th class="sortable" data-sort="assignee">Assignee <span class="si">↕</span></th>
          <th>Project</th>
          <th class="sortable" data-sort="due">Due date <span class="si">↕</span></th>
        </tr>
      </thead>
      <tbody id="tbody"></tbody>
    </table>
    <div class="empty" id="empty">
      <div class="ring"><svg viewBox="0 0 24 24"><path d="m9 11 3 3L22 4"/><path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/></svg></div>
      <h3>No tasks match these filters</h3>
      <p>Adjust the filters above, or create a new task to get started.</p>
      <button class="btn btn-primary" onclick="openCreate()"><svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>Add task</button>
    </div>
    <div class="table-foot" id="tableFoot"><span id="foot" class="num"></span></div>
  </div>

  {{-- Board view --}}
  <div class="board" id="boardView"></div>
</main>
</div>

{{-- ── Create Task Modal ── --}}
<div class="modal-backdrop" id="createModal">
  <div class="modal-box">
    <div class="modal-head">
      <h3>Create Task</h3>
      <button class="modal-close" onclick="closeModal('createModal')">×</button>
    </div>
    <div class="modal-body">
      <div class="f-group">
        <label class="f-label">Title *</label>
        <input type="text" id="ct_title" class="f-input" placeholder="What needs to be done?">
        <div class="f-err" id="ct_title_err">Title is required</div>
      </div>
      <div class="f-group">
        <label class="f-label">Description</label>
        <textarea id="ct_desc" class="f-input" placeholder="Optional details..."></textarea>
      </div>
      @if($isAdmin)
      <div class="f-group">
        <label class="f-label">Assign To</label>
        <div class="chip-group">
          @foreach($staffList as $s)
          <label class="chip-opt"><input type="radio" name="ct_assignee" value="{{ $s->id }}"><span class="chip-lbl">{{ $s->name }}</span></label>
          @endforeach
        </div>
      </div>
      @endif
      <div class="f-row">
        <div class="f-group">
          <label class="f-label">Priority</label>
          <select id="ct_priority" class="f-input">
            <option value="normal">Normal</option><option value="low">Low</option>
            <option value="high">High</option><option value="urgent">Urgent</option>
          </select>
        </div>
        <div class="f-group">
          <label class="f-label">Due Date</label>
          <input type="date" id="ct_due" class="f-input">
        </div>
      </div>
      <div class="f-row">
        <div class="f-group">
          <label class="f-label">Category</label>
          <select id="ct_category" class="f-input">
            @foreach(['sales','development','support','hr','finance','operations','other'] as $c)
            <option value="{{ $c }}">{{ ucfirst($c) }}</option>
            @endforeach
          </select>
        </div>
        <div class="f-group">
          <label class="f-label">Project</label>
          <select id="ct_project" class="f-input">
            <option value="">— None —</option>
            @foreach($projects as $p)
            <option value="{{ $p->id }}">{{ $p->name }}</option>
            @endforeach
          </select>
        </div>
      </div>
    </div>
    <div class="modal-foot">
      <button class="btn btn-secondary" onclick="closeModal('createModal')">Cancel</button>
      <button class="btn btn-primary" id="ct_submit" onclick="submitCreate()">Create Task</button>
    </div>
  </div>
</div>

{{-- ── Detail Modal ── --}}
<div class="modal-backdrop" id="detailModal">
  <div class="modal-box modal-lg">
    <div class="modal-head">
      <h3 id="dt_title">Task</h3>
      <button class="modal-close" onclick="closeModal('detailModal')">×</button>
    </div>
    <div class="modal-body" id="dt_body"></div>
  </div>
</div>

<div class="toast-stack" id="toasts"></div>

<script>
const CSRF      = '{{ csrf_token() }}';
const IS_ADMIN  = {{ $isAdmin ? 'true' : 'false' }};
const MY_ID     = {{ $staff->id }};
const STORE_URL = '{{ route('tasks.store') }}';
const TODAY     = '{{ now()->toDateString() }}';
const STAFF_LIST = @json($staffList->map(fn($s) => ['id' => $s->id, 'name' => $s->name]));
const ALL_PROJECTS = @json($projects->pluck('name')->values());

let tasks = @json($tasks);

const STATUS = {
  pending:    {label:'Pending',     cls:'b-warning'},
  in_progress:{label:'In progress', cls:'b-info'},
  blocked:    {label:'Blocked',     cls:'b-danger'},
  done:       {label:'Done',        cls:'b-success'}
};
const PRIORITY = {
  urgent:{label:'Urgent', cls:'b-danger'},
  high:  {label:'High',   cls:'b-warning'},
  normal:{label:'Normal', cls:'b-info'},
  low:   {label:'Low',    cls:'b-neutral'}
};
const STATUS_DOT = {pending:'var(--warning)',in_progress:'var(--info)',blocked:'var(--brand-red)',done:'var(--success)'};
const CAT_ICON   = {sales:'💼',development:'🖥️',support:'🎫',hr:'👥',finance:'💰',operations:'⚙️',other:'📌'};
const MONTHS     = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];

const state = {status:new Set(),priority:new Set(),assignee:new Set(),project:new Set(),search:'',quick:null,mine:false,sort:{key:'due',dir:1}};
let viewMode = 'list';

/* ── Helpers ── */
function esc(s){const d=document.createElement('div');d.textContent=s==null?'':s;return d.innerHTML;}
function initials(name){return (name||'?').split(' ').map(w=>w[0]).slice(0,2).join('').toUpperCase();}
function toast(msg,type='ok'){const el=document.createElement('div');el.className='toast toast-'+type;el.textContent=msg;document.getElementById('toasts').appendChild(el);setTimeout(()=>el.remove(),3000);}
function openModal(id){document.getElementById(id).classList.add('open');}
function closeModal(id){document.getElementById(id).classList.remove('open');}
function timeAgo(ds){const d=Math.floor((Date.now()-new Date(ds).getTime())/1000);if(d<60)return 'just now';if(d<3600)return Math.floor(d/60)+'m ago';if(d<86400)return Math.floor(d/3600)+'h ago';return Math.floor(d/86400)+'d ago';}
async function req(url,method='GET',body=null){const opt={method,headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}};if(body){opt.headers['Content-Type']='application/json';opt.body=JSON.stringify(body);}const r=await fetch(url,opt);return r.json();}
function dueInfo(iso){
  if(!iso) return null;
  const d=new Date(iso+'T00:00:00'), today=new Date(TODAY+'T00:00:00');
  const offset=Math.round((d-today)/86400000);
  return {text:`${d.getDate()} ${MONTHS[d.getMonth()]} ${d.getFullYear()}`, offset};
}

/* ── Filter option lists ── */
// Assignee dropdown: full staff list ∪ any assignee present on a task
const assignees = [...new Set([...STAFF_LIST.map(s=>s.name), ...tasks.map(t=>t.assignee)].filter(Boolean))].sort();
// Projects dropdown: full server list ∪ any project present on a task (so nothing is unfilterable)
const projects  = [...new Set([...ALL_PROJECTS, ...tasks.map(t=>t.project)].filter(Boolean))].sort();

function buildPop(id, items, type){
  const el=document.getElementById('pop-'+id);
  if(!el) return;
  el.innerHTML = items.map(it=>{
    const key=it.key||it, label=it.label||it;
    let dot='';
    if(type==='status') dot=`<span class="sd" style="background:${STATUS_DOT[key]}"></span>`;
    return `<label class="pop-item"><input type="checkbox" data-group="${id}" value="${esc(key)}">${dot}<span>${esc(label)}</span></label>`;
  }).join('') || `<div style="padding:8px 9px;font-size:12px;color:var(--text-3)">No options</div>`;
}
buildPop('status', Object.keys(STATUS).map(k=>({key:k,label:STATUS[k].label})), 'status');
buildPop('priority', Object.keys(PRIORITY).map(k=>({key:k,label:PRIORITY[k].label})));
buildPop('assignee', assignees);
buildPop('project', projects);

/* ── Popover open/close ── */
document.querySelectorAll('.filter-pill').forEach(p=>{
  p.addEventListener('click',e=>{
    e.stopPropagation();
    const pop=document.getElementById('pop-'+p.dataset.pop);
    const open=pop.classList.contains('open');
    document.querySelectorAll('.popover').forEach(x=>x.classList.remove('open'));
    if(!open) pop.classList.add('open');
  });
});
document.querySelectorAll('.popover').forEach(p=>p.addEventListener('click',e=>e.stopPropagation()));
document.addEventListener('click',()=>document.querySelectorAll('.popover').forEach(x=>x.classList.remove('open')));
document.querySelectorAll('.popover input').forEach(cb=>{
  cb.addEventListener('change',()=>{
    const g=cb.dataset.group;
    if(cb.checked) state[g].add(cb.value); else state[g].delete(cb.value);
    state.quick=null; render();
  });
});

/* ── Chips ── */
document.querySelectorAll('.chip').forEach(c=>c.addEventListener('click',()=>{
  const q=c.dataset.quick; state.quick = state.quick===q ? null : q; render();
}));

/* ── Search ── */
document.getElementById('search').addEventListener('input',e=>{state.search=e.target.value.toLowerCase();render();});

/* ── Sort ── */
document.querySelectorAll('th.sortable').forEach(th=>th.addEventListener('click',()=>{
  const k=th.dataset.sort;
  if(state.sort.key===k) state.sort.dir*=-1; else {state.sort.key=k;state.sort.dir=1;}
  render();
}));

/* ── View toggle ── */
document.getElementById('vList').addEventListener('click',()=>setView('list'));
document.getElementById('vBoard').addEventListener('click',()=>setView('board'));
function setView(v){
  viewMode=v;
  document.getElementById('vList').classList.toggle('on',v==='list');
  document.getElementById('vBoard').classList.toggle('on',v==='board');
  document.getElementById('listView').style.display = v==='list'?'block':'none';
  document.getElementById('boardView').classList.toggle('show',v==='board');
  render();
}

/* ── Scope toggle (All Tasks page: filter to my tasks, no navigation) ── */
function setScope(s){
  state.mine = (s==='my');
  const my=document.getElementById('scopeMy'), all=document.getElementById('scopeAll');
  if(my)  my.classList.toggle('on', state.mine);
  if(all) all.classList.toggle('on', !state.mine);
  render();
}

/* ── Clear ── */
document.getElementById('clearBtn').addEventListener('click',()=>{
  state.status.clear();state.priority.clear();state.assignee.clear();state.project.clear();
  state.search='';state.quick=null;
  document.getElementById('search').value='';
  document.querySelectorAll('.popover input').forEach(c=>c.checked=false);
  render();
});

/* ── Filtering ── */
function passQuick(t){
  const di=dueInfo(t.due_date);
  switch(state.quick){
    case 'overdue': return di && di.offset<0 && t.status!=='done';
    case 'today':   return di && di.offset===0 && t.status!=='done';
    case 'in_progress': return t.status==='in_progress';
    case 'done':    return t.status==='done';
    default: return true;
  }
}
function filtered(){
  return tasks.filter(t=>
    (!state.mine || t.assigned_to === MY_ID) &&
    (!state.status.size   || state.status.has(t.status)) &&
    (!state.priority.size || state.priority.has(t.priority)) &&
    (!state.assignee.size || state.assignee.has(t.assignee)) &&
    (!state.project.size  || state.project.has(t.project)) &&
    (!state.search || (t.title||'').toLowerCase().includes(state.search) || (t.assignee||'').toLowerCase().includes(state.search) || (t.project||'').toLowerCase().includes(state.search)) &&
    passQuick(t)
  );
}
const PRIO_ORDER={urgent:0,high:1,normal:2,low:3};
const STAT_ORDER={blocked:0,in_progress:1,pending:2,done:3};
function sortRows(rows){
  const {key,dir}=state.sort;
  return rows.slice().sort((a,b)=>{
    let av,bv;
    if(key==='priority'){av=PRIO_ORDER[a.priority];bv=PRIO_ORDER[b.priority];}
    else if(key==='status'){av=STAT_ORDER[a.status];bv=STAT_ORDER[b.status];}
    else if(key==='due'){av=dueInfo(a.due_date)?.offset ?? 99999; bv=dueInfo(b.due_date)?.offset ?? 99999;}
    else {av=(a[key]||'').toLowerCase();bv=(b[key]||'').toLowerCase();}
    return av<bv?-1*dir:av>bv?1*dir:0;
  });
}

/* ── Render helpers ── */
function badge(map,key){const m=map[key];return m?`<span class="badge ${m.cls}"><span class="sd"></span>${m.label}</span>`:`<span class="badge b-neutral"><span class="sd"></span>${esc(key)}</span>`;}
function dueCell(t){
  const di=dueInfo(t.due_date);
  if(!di) return '<span style="color:var(--text-3)">—</span>';
  if(t.status==='done') return `<span class="num" style="color:var(--text-3)">${di.text}</span>`;
  if(di.offset<0) return `<span class="due over"><span class="lbl">Overdue</span><span class="num">${di.text}</span></span>`;
  if(di.offset===0) return `<span class="due today"><span class="lbl">Today</span></span>`;
  return `<span class="num">${di.text}</span>`;
}

/* ── Main render ── */
function render(){
  document.getElementById('figOverdue').textContent  = tasks.filter(t=>{const d=dueInfo(t.due_date);return d&&d.offset<0&&t.status!=='done';}).length;
  document.getElementById('figToday').textContent    = tasks.filter(t=>{const d=dueInfo(t.due_date);return d&&d.offset===0&&t.status!=='done';}).length;
  document.getElementById('figProgress').textContent = tasks.filter(t=>t.status==='in_progress').length;
  document.getElementById('figDone').textContent     = tasks.filter(t=>t.status==='done').length;

  ['status','priority','assignee','project'].forEach(g=>{
    const pill=document.querySelector(`.filter-pill[data-pop="${g}"]`);
    if(!pill) return;
    const n=state[g].size;
    pill.classList.toggle('has',n>0);
    let cnt=pill.querySelector('.cnt');
    if(n>0){if(!cnt){cnt=document.createElement('span');cnt.className='cnt';pill.insertBefore(cnt,pill.lastElementChild);}cnt.textContent=n;}
    else if(cnt){cnt.remove();}
  });

  document.querySelectorAll('.chip').forEach(c=>c.classList.toggle('active',c.dataset.quick===state.quick));
  const anyFilter=state.status.size||state.priority.size||state.assignee.size||state.project.size||state.search||state.quick;
  document.getElementById('clearBtn').style.display = anyFilter ? 'inline' : 'none';

  const rows=sortRows(filtered());
  document.getElementById('count').textContent=`${rows.length} task${rows.length!==1?'s':''}`;

  document.querySelectorAll('th.sortable').forEach(th=>{
    const on=th.dataset.sort===state.sort.key;
    th.classList.toggle('sorted',on);
    th.querySelector('.si').textContent = on ? (state.sort.dir>0?'↑':'↓') : '↕';
  });

  /* List */
  const tbody=document.getElementById('tbody');
  tbody.innerHTML=rows.map(t=>{
    const di=dueInfo(t.due_date);
    const urgent=(di && di.offset<0 && t.status!=='done');
    return `<tr class="${urgent?'urgent-row':''}" data-id="${t.id}">
      <td><button class="complete-toggle ${t.status==='done'?'done':''}" data-toggle="${t.id}" title="${t.status==='done'?'Reopen':'Mark complete'}"><svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg></button></td>
      <td class="task-cell"><div class="primary ${t.status==='done'?'done-title':''}">${esc(t.title)}</div></td>
      <td>${badge(STATUS,t.status)}</td>
      <td>${badge(PRIORITY,t.priority)}</td>
      <td><span class="avatar-cell"><span class="av-sm">${initials(t.assignee)}</span>${esc(t.assignee||'—')}</span></td>
      <td>${t.project?esc(t.project):'<span style="color:var(--text-3)">—</span>'}</td>
      <td>${dueCell(t)}</td>
    </tr>`;
  }).join('');
  document.getElementById('empty').classList.toggle('show', rows.length===0 && viewMode==='list');
  document.getElementById('tableFoot').style.display = rows.length===0 ? 'none' : 'flex';
  document.getElementById('foot').textContent = rows.length ? `Showing ${rows.length} of ${rows.length}` : '';

  /* Board */
  const board=document.getElementById('boardView');
  board.innerHTML=Object.keys(STATUS).map(s=>{
    const col=rows.filter(t=>t.status===s);
    const cards=col.map(t=>`<div class="board-card" data-id="${t.id}">
        <div class="t">${esc(t.title)}</div>
        <div class="meta">${badge(PRIORITY,t.priority)}<span class="av-sm" title="${esc(t.assignee||'')}">${initials(t.assignee)}</span></div>
        <div class="pname">${esc(t.project||'No project')}${dueInfo(t.due_date)?' · '+dueInfo(t.due_date).text:''}</div>
      </div>`).join('') || `<div style="font-size:12px;color:var(--text-3);padding:8px 6px;">—</div>`;
    return `<div class="board-col">
      <div class="board-col-head"><span class="sd" style="background:${STATUS_DOT[s]}"></span>${STATUS[s].label}<span class="n num">${col.length}</span></div>
      ${cards}</div>`;
  }).join('');
}

/* ── Row / card click → detail ── */
document.getElementById('tbody').addEventListener('click',e=>{
  const tg=e.target.closest('.complete-toggle');
  if(tg){ e.stopPropagation(); toggleComplete(+tg.dataset.toggle); return; }
  const tr=e.target.closest('tr[data-id]');
  if(tr) openDetail(+tr.dataset.id);
});
document.getElementById('boardView').addEventListener('click',e=>{
  const card=e.target.closest('.board-card[data-id]');
  if(card) openDetail(+card.dataset.id);
});

/* ── Complete toggle (optimistic) ── */
async function toggleComplete(id){
  const t=tasks.find(x=>x.id===id); if(!t) return;
  const prev=t.status;
  if(t.status==='done'){ t.status=t._prev||'pending'; }
  else { t._prev=t.status; t.status='done'; }
  render();
  const r=await req(`/tasks/${id}`,'PUT',{status:t.status});
  if(!r.ok){ t.status=prev; render(); toast('Could not update','err'); }
}

/* ── Create ── */
function openCreate(){
  ct_title.value='';ct_desc.value='';ct_due.value='';ct_priority.value='normal';ct_category.value='other';ct_project.value='';
  document.querySelectorAll('input[name="ct_assignee"]').forEach(r=>r.checked=false);
  document.getElementById('ct_title_err').style.display='none';
  openModal('createModal');
  setTimeout(()=>ct_title.focus(),100);
}
async function submitCreate(){
  const title=ct_title.value.trim();
  if(!title){document.getElementById('ct_title_err').style.display='block';ct_title.focus();return;}
  const btn=document.getElementById('ct_submit');btn.disabled=true;btn.textContent='Creating...';
  const assignee=document.querySelector('input[name="ct_assignee"]:checked');
  const r=await req(STORE_URL,'POST',{
    title,
    description:ct_desc.value.trim()||null,
    assigned_to:assignee?assignee.value:null,
    priority:ct_priority.value,
    due_date:ct_due.value||null,
    category:ct_category.value,
    project_id:ct_project.value||null,
  });
  btn.disabled=false;btn.textContent='Create Task';
  if(r.ok){ toast('Task created'); closeModal('createModal'); location.reload(); }
  else toast(r.message||'Failed to create task','err');
}

/* ── Detail ── */
async function openDetail(id){
  const r=await req(`/tasks/${id}`);
  if(!r.ok){toast('Not found','err');return;}
  const t=r.task, comments=r.comments||[], history=r.history||[];
  document.getElementById('dt_title').textContent=t.title;

  const dueDateStr=t.due_date?(t.due_date+'').slice(0,10):null;
  const isOverdue=dueDateStr && dueDateStr<TODAY && t.status!=='done';
  const isDueToday=dueDateStr && dueDateStr===TODAY && t.status!=='done';

  let html=`<div class="detail-meta">`;
  html+=`<span class="detail-badge dbg-${t.status}"><span class="erp-dot dot-${t.status}"></span>${STATUS[t.status]?.label||t.status}</span>`;
  html+=`<span class="detail-badge dbg-${t.priority}"><span class="erp-dot dot-${t.priority}"></span>${PRIORITY[t.priority]?.label||t.priority}</span>`;
  if(t.category) html+=`<span class="detail-badge dbg-low">${CAT_ICON[t.category]||'📌'} ${esc(t.category)}</span>`;
  const projectName=t.project?.name||null;
  if(projectName) html+=`<span class="detail-badge dbg-low">📁 ${esc(projectName)}</span>`;
  if(isOverdue) html+=`<span class="detail-badge dbg-blocked">⚠️ Overdue</span>`;
  if(isDueToday) html+=`<span class="detail-badge dbg-pending">📅 Due Today</span>`;
  html+=`</div>`;

  if(t.description) html+=`<div class="detail-desc">${esc(t.description)}</div>`;

  if(dueDateStr){
    const fmt=new Date(dueDateStr+'T00:00:00').toLocaleDateString('en-IN',{day:'numeric',month:'long',year:'numeric'});
    const c=isOverdue?'var(--brand-red-dark)':isDueToday?'var(--warning)':'var(--text-2)';
    html+=`<div style="font-size:12px;margin-bottom:4px;color:var(--text-2)">📅 Due: <strong style="color:${c}">${esc(fmt)}</strong></div>`;
  }
  const creatorName=t.creator?.name||null;
  html+=`<div style="font-size:12px;color:var(--text-2);margin-bottom:16px;">Created by <strong style="color:var(--text-1);">${esc(creatorName||'—')}</strong> · ${timeAgo(t.created_at)}</div>`;

  const sOpts=['pending','in_progress','blocked','done'].map(s=>`<option value="${s}" ${t.status===s?'selected':''}>${STATUS[s].label}</option>`).join('');
  const pOpts=['urgent','high','normal','low'].map(p=>`<option value="${p}" ${t.priority===p?'selected':''}>${PRIORITY[p].label}</option>`).join('');
  html+=`<div class="detail-fields">
    <div><label class="f-label">Status</label><select class="f-input" onchange="quickUpdate(${t.id},'status',this.value)" style="height:34px;">${sOpts}</select></div>
    <div><label class="f-label">Priority</label><select class="f-input" onchange="quickUpdate(${t.id},'priority',this.value)" style="height:34px;">${pOpts}</select></div>`;
  if(IS_ADMIN && STAFF_LIST.length){
    const aOpts=`<option value="">Unassigned</option>`+STAFF_LIST.map(s=>`<option value="${s.id}" ${t.assigned_to==s.id?'selected':''}>${esc(s.name)}</option>`).join('');
    html+=`<div><label class="f-label">Assigned To</label><select class="f-input" onchange="quickUpdate(${t.id},'assigned_to',this.value)" style="height:34px;">${aOpts}</select></div>`;
  }
  html+=`</div>`;

  html+=`<hr style="border:none;border-top:1px solid var(--border);margin:0 0 16px;">`;

  html+=`<div class="section-title" style="margin-top:0;">Comments (${comments.length})</div>`;
  if(!comments.length) html+=`<p style="font-size:13px;color:var(--text-2);margin:0 0 12px;">No comments yet</p>`;
  else comments.forEach(c=>{
    html+=`<div class="comment-item"><div class="comment-meta"><span class="comment-author">${esc(c.staff?.name||'Unknown')}</span><span class="comment-time">${timeAgo(c.created_at)}</span></div><div class="comment-text">${esc(c.comment).replace(/\n/g,'<br>')}</div></div>`;
  });
  html+=`<div style="display:flex;gap:8px;margin-bottom:16px;align-items:flex-end;">
    <textarea id="commentInput" class="f-input" placeholder="Add a comment..." rows="3" style="flex:1;min-height:70px;resize:vertical;"></textarea>
    <button class="btn btn-primary" onclick="addComment(${t.id})" style="flex-shrink:0;">Post</button></div>`;

  if(t.status!=='done'){
    html+=`<button class="btn btn-primary" onclick="quickUpdate(${t.id},'status','done')" style="width:100%;justify-content:center;padding:11px;font-size:14px;">
      <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Mark as Complete</button>`;
  }

  if(history.length){
    html+=`<hr style="border:none;border-top:1px solid var(--border);margin:16px 0;"><div class="section-title">History</div>`;
    history.forEach(h=>{
      const action=(h.action||'').replace(/_/g,' ');
      html+=`<div class="history-item"><span style="color:var(--text-1);">${esc(h.staff?.name||'?')} <span style="color:var(--text-2);">${action}</span></span>${h.new_value?`<span style="color:var(--text-2);font-size:11px;">→ ${esc(h.new_value)}</span>`:''}<span style="color:var(--text-3);">${timeAgo(h.created_at)}</span></div>`;
    });
  }

  document.getElementById('dt_body').innerHTML=html;
  openModal('detailModal');
}

async function quickUpdate(id,field,value){
  const r=await req(`/tasks/${id}`,'PUT',{[field]:value});
  if(!r.ok){ toast(r.message||'Error','err'); return; }
  toast('Updated');
  const t=tasks.find(x=>x.id===id);
  if(t){
    if(field==='assigned_to'){ t.assigned_to=value||null; const s=STAFF_LIST.find(s=>s.id==value); t.assignee=s?s.name:null; }
    else t[field]=value;
  }
  render();
  if(document.getElementById('detailModal').classList.contains('open')) openDetail(id);
}

async function addComment(id){
  const inp=document.getElementById('commentInput');
  const c=inp.value.trim(); if(!c) return;
  const r=await req(`/tasks/${id}/comments`,'POST',{comment:c});
  if(r.ok){ toast('Comment added'); openDetail(id); }
  else toast(r.message||'Error','err');
}

render();
</script>
</x-layouts.app>
