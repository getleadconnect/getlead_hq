<x-layouts.app title="Assets">

@push('styles')
<style>
/* ── Assets Page Styles ── */
.as-wrap { padding:20px; --as-teal:#14b8a6; --as-teal-dark:#0d9488; }

/* Filter Bar */
.as-filter { display:flex; gap:10px; align-items:center; flex-wrap:wrap; margin-bottom:18px; padding:14px 18px; background:#fff; border:1px solid #e2e8f0; border-radius:12px; }
.as-filter input,.as-filter select { padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:.82rem; font-family:inherit; background:#fff; min-height:36px; transition:border-color .2s; }
.as-filter input:focus,.as-filter select:focus { outline:none; border-color:#14b8a6; box-shadow:0 0 0 3px rgba(20,184,166,.1); }
.as-filter input { flex:1; min-width:180px; }

/* Table */
.as-table-wrap { overflow-x:auto; border-radius:10px; background:#fff; border:1px solid #e2e8f0; }
.as-table { width:100%; border-collapse:separate; border-spacing:0; font-size:.83rem; }
.as-table th { padding:11px 14px; background:#f8fafc; font-weight:600; font-size:.71rem; text-transform:uppercase; letter-spacing:.04em; color:#64748b; border-bottom:1px solid #e2e8f0; text-align:left; }
.as-table td { padding:11px 14px; border-bottom:1px solid #e2e8f0; vertical-align:middle; }
.as-table tr:last-child td { border-bottom:none; }
.as-table tbody tr:hover td { background:rgba(20,184,166,.04); }
.as-table a { color:#14b8a6; font-weight:500; cursor:pointer; }
.as-table a:hover { text-decoration:underline; }

/* KPI Cards */
.as-kpi-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(150px,1fr)); gap:14px; margin-bottom:20px; }
.as-kpi { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:16px; transition:all .2s; }
.as-kpi:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(0,0,0,.06); }
.as-kpi-label { font-size:.7rem; text-transform:uppercase; letter-spacing:.04em; color:#64748b; font-weight:500; margin-bottom:6px; }
.as-kpi-value { font-size:1.6rem; font-weight:700; letter-spacing:-.02em; color:#0f172a; }
.as-kpi-value.teal  { color:#14b8a6; }
.as-kpi-value.amber { color:#f59e0b; }
.as-kpi-value.red   { color:#ef4444; }
.as-kpi-value.green { color:#10b981; }

/* Cards */
.as-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; margin-bottom:16px; overflow:hidden; }
.as-card-title { font-size:.88rem; font-weight:600; color:#0f172a; padding:14px 18px; border-bottom:1px solid #e2e8f0; display:flex; align-items:center; justify-content:space-between; }
.as-card-body { padding:18px; }
.as-row-item { display:flex; justify-content:space-between; align-items:center; padding:7px 0; border-bottom:1px solid #f1f5f9; font-size:.82rem; }
.as-row-item:last-child { border-bottom:none; }

/* Grid */
.as-grid-2 { display:grid; grid-template-columns:repeat(auto-fit,minmax(320px,1fr)); gap:16px; margin-bottom:16px; }

/* Badges */
.as-badge { display:inline-flex; align-items:center; padding:3px 9px; border-radius:20px; font-size:.69rem; font-weight:600; }
.as-badge-teal   { background:rgba(20,184,166,.12); color:#0f766e; }
.as-badge-green  { background:rgba(16,185,129,.12); color:#15803d; }
.as-badge-amber  { background:rgba(245,158,11,.12); color:#b45309; }
.as-badge-red    { background:rgba(239,68,68,.12); color:#dc2626; }
.as-badge-slate  { background:rgba(100,116,139,.1); color:#475569; }
.as-badge-blue   { background:rgba(14,165,233,.1); color:#0369a1; }
.as-badge-xs { padding:2px 6px; font-size:.64rem; }

/* Alert Items */
.as-alert { padding:10px 14px; border-radius:8px; margin-bottom:8px; font-size:.82rem; display:flex; align-items:center; gap:8px; }
.as-alert:last-child { margin-bottom:0; }
.as-alert a { color:inherit; font-weight:600; }
.as-alert-red    { background:rgba(239,68,68,.07); border-left:3px solid #ef4444; }
.as-alert-amber  { background:rgba(245,158,11,.07); border-left:3px solid #f59e0b; }
.as-alert-blue   { background:rgba(14,165,233,.07); border-left:3px solid #0ea5e9; }

/* Detail grid */
.as-detail-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:10px; margin-bottom:16px; }
.as-detail-cell { background:#f8fafc; border:1px solid #e2e8f0; border-radius:8px; padding:12px; }
.as-detail-cell-label { font-size:.7rem; text-transform:uppercase; letter-spacing:.04em; color:#64748b; margin-bottom:4px; }
.as-detail-cell-value { font-size:.9rem; font-weight:600; color:#0f172a; }

/* Buttons */
.as-btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:7px 14px; border-radius:8px; font-size:.8rem; font-weight:500; cursor:pointer; transition:all .15s; border:none; font-family:inherit; text-decoration:none; }
.as-btn-primary { background:linear-gradient(135deg,#14b8a6,#0d9488); color:#fff; }
.as-btn-primary:hover { box-shadow:0 4px 12px rgba(20,184,166,.35); transform:translateY(-1px); }
.as-btn-secondary { background:#f1f5f9; color:#374151; border:1px solid #e2e8f0; }
.as-btn-secondary:hover { background:#fff; border-color:#14b8a6; }
.as-btn-danger { background:linear-gradient(135deg,#ef4444,#dc2626); color:#fff; }
.as-btn-ghost { background:transparent; color:#64748b; }
.as-btn-ghost:hover { background:#f1f5f9; color:#0f172a; }
.as-btn-sm { padding:5px 10px; font-size:.75rem; }
.as-btn-icon { padding:5px 7px; }

/* Modals */
.as-modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:3000; align-items:center; justify-content:center; padding:20px; backdrop-filter:blur(2px); }
.as-modal-overlay.active { display:flex; }
.as-modal { background:#fff; border-radius:14px; width:100%; max-width:600px; max-height:90vh; overflow:hidden; display:flex; flex-direction:column; box-shadow:0 20px 60px rgba(0,0,0,.2); animation:as-popup .2s ease; }
.as-modal-lg { max-width:720px; }
@keyframes as-popup { from{opacity:0;transform:scale(.97) translateY(8px)} to{opacity:1;transform:scale(1) translateY(0)} }
.as-modal-header { padding:18px 22px 14px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; }
.as-modal-header h2 { font-size:.95rem; font-weight:600; }
.as-modal-close { background:none; border:none; font-size:1.3rem; cursor:pointer; color:#64748b; padding:4px 8px; border-radius:6px; }
.as-modal-close:hover { background:#f1f5f9; }
.as-modal-body { padding:22px; overflow-y:auto; flex:1; }
.as-modal-footer { padding:14px 22px; border-top:1px solid #e2e8f0; display:flex; gap:8px; justify-content:flex-end; align-items:center; }

/* Form inside modal */
.as-form-row { display:flex; gap:14px; margin-bottom:14px; }
.as-form-group { flex:1; display:flex; flex-direction:column; gap:4px; }
.as-form-group label { font-size:.78rem; font-weight:500; color:#374151; }
.as-form-group input,.as-form-group select,.as-form-group textarea { padding:8px 11px; border:1px solid #e2e8f0; border-radius:8px; font-size:.84rem; font-family:inherit; }
.as-form-group input:focus,.as-form-group select:focus,.as-form-group textarea:focus { outline:none; border-color:#14b8a6; box-shadow:0 0 0 3px rgba(20,184,166,.1); }
.as-form-group textarea { resize:vertical; min-height:64px; }

/* QR Grid */
.as-qr-grid { display:flex; flex-wrap:wrap; gap:10px; }
.as-qr-label { border:1px solid #e2e8f0; border-radius:8px; padding:10px; text-align:center; background:#fff; position:relative; width:140px; transition:all .15s; }
.as-qr-label:hover { box-shadow:0 2px 10px rgba(0,0,0,.08); }
.as-qr-label.mapped { border-color:#14b8a6; background:rgba(20,184,166,.04); }
.as-qr-label .qr-id { font-weight:700; font-size:.75rem; margin-top:6px; letter-spacing:.5px; }
.as-qr-label .qr-mapped-name { font-size:.66rem; color:#14b8a6; margin-top:3px; font-weight:500; }
.as-qr-dot { position:absolute; top:6px; right:6px; width:8px; height:8px; border-radius:50%; }
.as-qr-dot-green { background:#22c55e; }
.as-qr-dot-gray  { background:#d1d5db; }

/* Toast */
.as-toasts { position:fixed; top:22px; right:22px; z-index:9999; display:flex; flex-direction:column; gap:8px; pointer-events:none; }
.as-toast { padding:11px 18px; border-radius:10px; font-size:.83rem; font-weight:500; box-shadow:0 4px 16px rgba(0,0,0,.15); animation:as-slide .25s ease; pointer-events:auto; }
.as-toast-success { background:#0f172a; color:#fff; }
.as-toast-error   { background:#ef4444; color:#fff; }
@keyframes as-slide { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }

/* Page header */
.as-page-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
.as-page-title { font-size:1.25rem; font-weight:700; color:#0f172a; }
.as-page-actions { display:flex; gap:8px; flex-wrap:wrap; align-items:center; }

/* Pagination */
.as-pagination { display:flex; gap:6px; margin-top:16px; flex-wrap:wrap; }
.as-pagination button { padding:6px 12px; border-radius:6px; border:1px solid #e2e8f0; background:#fff; font-size:.8rem; cursor:pointer; }
.as-pagination button.active { background:#14b8a6; color:#fff; border-color:#14b8a6; }
.as-pagination button:hover:not(.active):not(:disabled) { border-color:#14b8a6; }
.as-pagination button:disabled { opacity:.4; cursor:not-allowed; }

/* Empty state */
.as-empty { text-align:center; padding:48px 20px; color:#64748b; }
.as-empty-icon { font-size:2.5rem; opacity:.5; margin-bottom:12px; }

/* Print styles */
@media print {
    .as-page-header,.as-filter,.as-btn,.as-toasts,.as-modal-overlay,.sidebar,.topbar { display:none !important; }
    .as-qr-grid { gap:4px !important; }
    .as-qr-label { break-inside:avoid; border:1px dashed #ccc !important; box-shadow:none !important; }
}

/* ── Responsive ── */
@media(max-width:768px) {
    .as-grid-2 { grid-template-columns:1fr; }
    .as-kpi-grid { grid-template-columns:repeat(2,1fr); gap:10px; }
    .as-table { min-width:540px; }
    .as-form-row { flex-direction:column; gap:10px; }
    .as-page-header { flex-direction:column; align-items:flex-start; gap:10px; }
    .as-page-actions { flex-wrap:wrap; gap:6px; }

    /* Bottom-sheet modals */
    .as-modal-overlay { padding:0; align-items:flex-end; }
    .as-modal, .as-modal-lg { max-width:100% !important; border-radius:14px 14px 0 0; max-height:92vh; }
    @keyframes as-popup { from{opacity:0;transform:translateY(30px)} to{opacity:1;transform:translateY(0)} }

    /* Filter bar stacks on mobile */
    .as-filter { gap:8px; padding:12px 14px; }
    .as-filter input { min-width:0; width:100%; }

    /* Toast top-right stays but smaller */
    .as-toasts { right:10px; top:10px; }
    .as-toast { font-size:.78rem; padding:9px 14px; }
}

@media(max-width:560px) {
    .as-kpi-grid { grid-template-columns:1fr 1fr; gap:8px; }
    .as-kpi { padding:12px; }
    .as-kpi-value { font-size:1.35rem; }

    /* Page action buttons — wrap into two rows */
    .as-page-actions { flex-wrap:wrap; }
    .as-page-actions .as-btn { white-space:nowrap; flex-shrink:0; }

    /* QR labels — smaller on small screens */
    .as-qr-label { width:120px; }

    /* Detail grid — 2 cols */
    .as-detail-grid { grid-template-columns:repeat(2,1fr); gap:8px; }
}

@media(max-width:380px) {
    .as-kpi-grid { grid-template-columns:1fr 1fr; gap:6px; }
    .as-kpi { padding:10px; }
    .as-kpi-label { font-size:.65rem; }
    .as-kpi-value { font-size:1.2rem; }
    .as-detail-grid { grid-template-columns:1fr; }
    .as-page-header { margin-bottom:14px; }
    .as-qr-label { width:105px; padding:8px 6px; }
}

/* Remove body padding that conflicts with app layout */
body { padding:0; background:inherit; }

/* ── Asset Dashboard (adb-*) ── */
.adb-stats-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:14px; margin-bottom:22px; }
.adb-charts-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:22px; }
.adb-stat-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:18px 20px; }
.adb-stat-label { font-size:.7rem; text-transform:uppercase; letter-spacing:.05em; color:#64748b; font-weight:500; margin-bottom:8px; }
.adb-stat-value { font-size:1.75rem; font-weight:700; letter-spacing:-.03em; color:#0f172a; line-height:1.1; margin-bottom:6px; }
.adb-stat-sub { font-size:.75rem; color:#94a3b8; display:flex; align-items:center; gap:5px; }
.adb-dot { display:inline-block; width:7px; height:7px; border-radius:50%; flex-shrink:0; }
.adb-chart-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:18px 20px; }
.adb-chart-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:16px; }
.adb-chart-title { font-size:.85rem; font-weight:600; color:#0f172a; }
.adb-chart-badge { background:#f1f5f9; color:#64748b; font-size:.7rem; font-weight:600; padding:2px 8px; border-radius:12px; }
.adb-table-card { background:#fff; border:1px solid #e2e8f0; border-radius:12px; overflow:hidden; margin-bottom:14px; }
.adb-table-header { display:flex; align-items:center; justify-content:space-between; padding:14px 18px; border-bottom:1px solid #e2e8f0; }
.adb-table-title { font-size:.85rem; font-weight:600; color:#0f172a; }
.adb-th { padding:8px 6px 8px 10px; background:#f8fafc; font-size:.68rem; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:#64748b; text-align:left; border-bottom:1px solid #e2e8f0; }
.adb-td { padding:0 6px 0 10px; font-size:13px; line-height:30px; border-bottom:1px solid #f1f5f9; vertical-align:middle; }
.adb-tr:last-child .adb-td { border-bottom:none; }
.adb-tr:hover .adb-td { background:#f8fafc; }

@media(max-width:768px){
    .adb-stats-grid { grid-template-columns:repeat(2,1fr); gap:10px; }
    .adb-charts-grid { grid-template-columns:1fr; gap:10px; }
    .adb-stat-card { padding:14px 16px; }
    .adb-stat-value { font-size:1.45rem; }
    .adb-chart-card { padding:14px 16px; }
}
@media(max-width:560px){
    .adb-stats-grid { grid-template-columns:repeat(2,1fr); gap:8px; }
    .adb-charts-grid { gap:8px; }
    .adb-stat-value { font-size:1.25rem; }
    .adb-stat-label { font-size:.65rem; }
}
</style>
@endpush

<div class="as-wrap">

{{-- ══ VIEW: DASHBOARD ══ --}}
<div id="view-dashboard" class="as-view active">
    <div class="as-page-header">
        <div class="as-page-title">📦 Asset Management</div>
        <div class="as-page-actions">
            <button class="as-btn as-btn-secondary as-btn-sm" onclick="showView('list')">View All</button>
            <button class="as-btn as-btn-secondary as-btn-sm" onclick="showView('qr-labels')">🏷️ QR Labels</button>
            <button class="as-btn as-btn-secondary as-btn-sm" onclick="showView('qr-map')">🔗 Map QR</button>
            @if($canEdit)
            <button class="as-btn as-btn-primary as-btn-sm" onclick="openAssetModal()">+ Add Asset</button>
            @endif
        </div>
    </div>
    <div id="dashContent"><div class="as-empty"><div class="as-empty-icon">📦</div><p>Loading...</p></div></div>
</div>

{{-- ══ VIEW: LIST ══ --}}
<div id="view-list" class="as-view" style="display:none">
    <div class="as-page-header">
        <div class="as-page-title">Assets</div>
        <div class="as-page-actions">
            <button class="as-btn as-btn-secondary as-btn-sm" onclick="showView('dashboard')">← Dashboard</button>
            <button class="as-btn as-btn-secondary as-btn-sm" onclick="showView('qr-labels')">🏷️ QR Labels</button>
            <button class="as-btn as-btn-secondary as-btn-sm" onclick="showView('qr-map')">🔗 Map QR</button>
            @if($canEdit)
            <button class="as-btn as-btn-primary as-btn-sm" onclick="openAssetModal()">+ Add Asset</button>
            @endif
        </div>
    </div>
    <div class="as-filter">
        <input type="text" id="asSearch" placeholder="Search by name, brand, serial, tag…" oninput="debounceList()">
        <select id="asType" onchange="loadList()">
            <option value="">All Types</option>
            @foreach($types as $t)<option value="{{ $t }}">{{ ucfirst($t) }}</option>@endforeach
        </select>
        <select id="asStatus" onchange="loadList()">
            <option value="">All Status</option>
            @foreach($statuses as $s)<option value="{{ $s }}">{{ ucfirst(str_replace('_',' ',$s)) }}</option>@endforeach
        </select>
        <select id="asOwner" onchange="loadList()">
            <option value="">All Owners</option>
            @foreach($staffList as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
        </select>
        <button class="as-btn as-btn-ghost as-btn-sm" onclick="clearListFilters()">Clear</button>
    </div>
    <div id="listContent"><div class="as-empty"><div class="as-empty-icon">📋</div><p>Loading...</p></div></div>
    <div id="listPagination" class="as-pagination" style="justify-content:right;margin-top:12px;align-items:center;margin-bottom:30px;"></div>
</div>

{{-- ══ VIEW: DETAIL ══ --}}
<div id="view-detail" class="as-view" style="display:none">
    <div id="detailContent"></div>
</div>

{{-- ══ VIEW: QR LABELS ══ --}}
<div id="view-qr-labels" class="as-view" style="display:none">
    <div class="as-page-header">
        <div class="as-page-title" id="qrLabelTitle">🏷️ QR Labels</div>
        <div class="as-page-actions">
            <select id="qrFilter" onchange="loadQrCodes(1)" style="padding:7px 11px;border:1px solid #e2e8f0;border-radius:8px;font-size:.8rem;font-family:inherit">
                <option value="all">All</option>
                <option value="unmapped">Unmapped</option>
                <option value="mapped">Mapped</option>
            </select>
            <select id="qrSize" onchange="loadQrCodes(currentQrPage)" style="padding:7px 11px;border:1px solid #e2e8f0;border-radius:8px;font-size:.8rem;font-family:inherit">
                <option value="small">Small (3cm)</option>
                <option value="medium" selected>Medium (4cm)</option>
                <option value="large">Large (5cm)</option>
            </select>
            <button class="as-btn as-btn-secondary as-btn-sm" onclick="printQrGrid()">🖨️ Print</button>
            @if($canEdit)
            <button class="as-btn as-btn-primary as-btn-sm" onclick="generateQr()">+ Generate More</button>
            @endif
            <button class="as-btn as-btn-secondary as-btn-sm" onclick="showView('dashboard')">← Back</button>
        </div>
    </div>
    <div id="qrGrid" class="as-qr-grid" style="margin-bottom:16px"></div>
    <div id="qrPagination" class="as-pagination" style="justify-content:flex-end"></div>
</div>

{{-- ══ VIEW: QR MAP ══ --}}
<div id="view-qr-map" class="as-view" style="display:none">
    <div class="as-page-header">
        <div class="as-page-title">🔗 Map QR to Assets</div>
        <div class="as-page-actions">
            <button class="as-btn as-btn-secondary as-btn-sm" onclick="showView('qr-labels')">🏷️ QR Labels</button>
            <button class="as-btn as-btn-secondary as-btn-sm" onclick="showView('dashboard')">← Back</button>
        </div>
    </div>
    <div id="qrMapContent"></div>
</div>

</div>{{-- /.as-wrap --}}

{{-- ══ ASSET MODAL ══ --}}
<div class="as-modal-overlay" id="assetModal">
    <div class="as-modal as-modal-lg">
        <div class="as-modal-header">
            <h2 id="assetModalTitle">Add Asset</h2>
            <button class="as-modal-close" onclick="asCloseModal('assetModal')">×</button>
        </div>
        <div class="as-modal-body">
            <input type="hidden" id="am_id">
            <div class="as-form-row">
                <div class="as-form-group" style="flex:2">
                    <label>Name *</label>
                    <input type="text" id="am_name" placeholder="e.g. Dell Inspiron 15">
                </div>
                <div class="as-form-group" style="flex:1">
                    <label>Type *</label>
                    <select id="am_type">
                        @foreach($types as $t)<option value="{{ $t }}">{{ ucfirst($t) }}</option>@endforeach
                    </select>
                </div>
            </div>
            <div class="as-form-row">
                <div class="as-form-group"><label>Brand</label><input type="text" id="am_brand"></div>
                <div class="as-form-group"><label>Model</label><input type="text" id="am_model"></div>
                <div class="as-form-group"><label>Serial Number</label><input type="text" id="am_serial"></div>
            </div>
            <div class="as-form-row">
                <div class="as-form-group"><label>Purchase Date</label><input type="date" id="am_pdate"></div>
                <div class="as-form-group"><label>Purchase Price (₹)</label><input type="number" id="am_pprice" step="0.01" min="0"></div>
                <div class="as-form-group"><label>Vendor</label><input type="text" id="am_vendor"></div>
            </div>
            <div class="as-form-row">
                <div class="as-form-group">
                    <label>Assigned To</label>
                    <select id="am_assigned">
                        <option value="">— Unassigned —</option>
                        @foreach($staffList as $s)<option value="{{ $s->id }}">{{ $s->name }}</option>@endforeach
                    </select>
                </div>
                <div class="as-form-group">
                    <label>Status *</label>
                    <select id="am_status">
                        @foreach($statuses as $s)<option value="{{ $s }}">{{ ucfirst(str_replace('_',' ',$s)) }}</option>@endforeach
                    </select>
                </div>
                <div class="as-form-group"><label>Warranty Expiry</label><input type="date" id="am_warranty"></div>
            </div>
            <div class="as-form-row">
                <div class="as-form-group">
                    <label>Checkup Interval (days)</label>
                    <input type="number" id="am_interval" value="90" min="7" max="365">
                </div>
                <div class="as-form-group" style="flex:2">
                    <label>Remarks</label>
                    <input type="text" id="am_remarks" placeholder="Any damage, issues…">
                </div>
            </div>
            <div class="as-form-group" style="margin-bottom:0">
                <label>Notes</label>
                <textarea id="am_notes" rows="2"></textarea>
            </div>
        </div>
        <div class="as-modal-footer">
            <button class="as-btn as-btn-danger as-btn-sm" id="am_delete_btn" style="display:none;margin-right:auto" onclick="deleteAsset()">Delete</button>
            <button class="as-btn as-btn-secondary as-btn-sm" onclick="asCloseModal('assetModal')">Cancel</button>
            <button class="as-btn as-btn-primary as-btn-sm" onclick="saveAsset()">Save Asset</button>
        </div>
    </div>
</div>

{{-- ══ REPAIR MODAL ══ --}}
<div class="as-modal-overlay" id="repairModal">
    <div class="as-modal">
        <div class="as-modal-header">
            <h2>🔧 Log Repair</h2>
            <button class="as-modal-close" onclick="asCloseModal('repairModal')">×</button>
        </div>
        <div class="as-modal-body">
            <input type="hidden" id="rm_asset_id">
            <div class="as-form-row">
                <div class="as-form-group"><label>Date *</label><input type="date" id="rm_date"></div>
                <div class="as-form-group">
                    <label>Status</label>
                    <select id="rm_status">
                        <option value="pending">Pending</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>
            <div class="as-form-group" style="margin-bottom:12px"><label>Issue *</label><input type="text" id="rm_issue" placeholder="Describe the issue"></div>
            <div class="as-form-row">
                <div class="as-form-group"><label>Cost (₹)</label><input type="number" id="rm_cost" value="0" min="0"></div>
                <div class="as-form-group"><label>Vendor/Workshop</label><input type="text" id="rm_vendor"></div>
            </div>
            <div class="as-form-group"><label>Notes</label><textarea id="rm_notes" rows="2"></textarea></div>
        </div>
        <div class="as-modal-footer">
            <button class="as-btn as-btn-secondary as-btn-sm" onclick="asCloseModal('repairModal')">Cancel</button>
            <button class="as-btn as-btn-primary as-btn-sm" onclick="saveRepair()">Save Repair</button>
        </div>
    </div>
</div>

{{-- ══ CHECKUP MODAL ══ --}}
<div class="as-modal-overlay" id="checkupModal">
    <div class="as-modal">
        <div class="as-modal-header">
            <h2>🔍 Log Checkup</h2>
            <button class="as-modal-close" onclick="asCloseModal('checkupModal')">×</button>
        </div>
        <div class="as-modal-body">
            <input type="hidden" id="cm2_asset_id">
            <div class="as-form-row">
                <div class="as-form-group"><label>Date *</label><input type="date" id="cm2_date"></div>
                <div class="as-form-group">
                    <label>Condition *</label>
                    <select id="cm2_cond">
                        <option value="good">✅ Good</option>
                        <option value="fair">⚠️ Fair</option>
                        <option value="poor">🔴 Poor</option>
                        <option value="damaged">💥 Damaged</option>
                        <option value="missing">❓ Missing</option>
                    </select>
                </div>
            </div>
            <div class="as-form-group"><label>Remarks</label><textarea id="cm2_remarks" rows="3" placeholder="Describe condition, issues found…"></textarea></div>
        </div>
        <div class="as-modal-footer">
            <button class="as-btn as-btn-secondary as-btn-sm" onclick="asCloseModal('checkupModal')">Cancel</button>
            <button class="as-btn as-btn-primary as-btn-sm" onclick="saveCheckup()">Save Checkup</button>
        </div>
    </div>
</div>

{{-- Confirm Modal --}}
<div class="as-modal-overlay" id="confirmModal">
    <div class="as-modal" style="max-width:400px">
        <div class="as-modal-header" style="border-bottom:none;padding-bottom:0">
            <div style="width:44px;height:44px;border-radius:50%;background:rgba(239,68,68,.1);display:flex;align-items:center;justify-content:center">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            </div>
            <button class="as-modal-close" onclick="asCloseModal('confirmModal')">×</button>
        </div>
        <div class="as-modal-body" style="padding-top:10px;padding-bottom:8px">
            <div id="confirmModalMsg" style="font-size:.9rem;font-weight:600;color:#0f172a;margin-bottom:6px"></div>
            <div id="confirmModalSub" style="font-size:.8rem;color:#64748b"></div>
        </div>
        <div class="as-modal-footer">
            <button class="as-btn as-btn-secondary as-btn-sm" onclick="asCloseModal('confirmModal')">Cancel</button>
            <button class="as-btn as-btn-danger as-btn-sm" id="confirmModalOk">Confirm</button>
        </div>
    </div>
</div>

{{-- Toast container --}}
<div class="as-toasts" id="asToasts"></div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
<script>
const AS_CSRF     = document.querySelector('meta[name="csrf-token"]')?.content || '';
const AS_CAN_EDIT = @json($canEdit);
const AS_STAFF_ID = @json($staffId);
const AS_BASE_URL = window.location.origin;

// ── Utilities ──────────────────────────────────────────────────────
function asEsc(s){ const d=document.createElement('div'); d.textContent=s||''; return d.innerHTML; }
function asToast(msg, type='success'){
    const d=document.createElement('div');
    d.className='as-toast as-toast-'+type;
    d.textContent=msg;
    document.getElementById('asToasts').appendChild(d);
    setTimeout(()=>d.remove(), 3200);
}
function asOpenModal(id)  { document.getElementById(id).classList.add('active'); }
function asCloseModal(id) { document.getElementById(id).classList.remove('active'); }
function asConfirm(msg, sub, onOk, okLabel='Confirm'){
    document.getElementById('confirmModalMsg').textContent = msg;
    document.getElementById('confirmModalSub').textContent = sub || '';
    const btn = document.getElementById('confirmModalOk');
    btn.textContent = okLabel;
    btn.onclick = () => { asCloseModal('confirmModal'); onOk(); };
    asOpenModal('confirmModal');
}
document.querySelectorAll('.as-modal-overlay').forEach(m =>
    m.addEventListener('click', e => { if(e.target===e.currentTarget) e.target.classList.remove('active'); })
);
async function asGet(path, params={}){
    const qs = new URLSearchParams(params).toString();
    const r = await fetch(path+(qs?'?'+qs:''), { headers:{'Accept':'application/json','X-CSRF-TOKEN':AS_CSRF} });
    return r.json();
}
async function asPost(path, body={}){
    const r = await fetch(path, { method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':AS_CSRF}, body:JSON.stringify(body) });
    return r.json();
}

// ── View switching ─────────────────────────────────────────────────
let currentView = 'dashboard';
function showView(v){
    document.querySelectorAll('.as-view').forEach(el => el.style.display='none');
    document.getElementById('view-'+v).style.display='block';
    currentView = v;
    if(v==='dashboard')   loadDashboard();
    if(v==='list')        loadList();
    if(v==='qr-labels')   loadQrCodes(1);
    if(v==='qr-map')      loadQrMapData();
}

// ── Helpers ────────────────────────────────────────────────────────
const STATUS_BADGE = { active:'as-badge-green', in_repair:'as-badge-amber', maintenance:'as-badge-amber', retired:'as-badge-slate', lost:'as-badge-red' };
const COND_BADGE   = { good:'as-badge-green', fair:'as-badge-amber', poor:'as-badge-red', damaged:'as-badge-red', missing:'as-badge-slate' };
function statusBadge(s){ return `<span class="as-badge ${STATUS_BADGE[s]||'as-badge-slate'}">${asEsc((s||'').replace('_',' '))}</span>`; }
function condBadge(c)  { return `<span class="as-badge ${COND_BADGE[c]||'as-badge-slate'}">${asEsc(c||'')}</span>`; }
function typeBadge(t)  { return `<span class="as-badge as-badge-teal">${asEsc(t||'')}</span>`; }
function fmtPrice(v)   { return v ? '₹'+parseFloat(v).toLocaleString('en-IN') : '—'; }
function fmtKpi(v){
    const n = parseFloat(v) || 0;
    if(n >= 10000000) return '₹'+(n/10000000).toFixed(2)+'Cr';
    if(n >= 100000)   return '₹'+(n/100000).toFixed(2)+'L';
    if(n >= 1000)     return '₹'+(n/1000).toFixed(1)+'K';
    return '₹'+n.toFixed(0);
}
function fmtDate(d)    { if(!d) return '—'; return new Date(d+'T00:00:00').toLocaleDateString('en-IN',{day:'numeric',month:'short',year:'numeric'}); }
function assetAge(d)   {
    if(!d) return '—';
    const diff = Math.floor((Date.now() - new Date(d).getTime()) / (86400000*30));
    if(diff < 12) return diff+'m old';
    return Math.floor(diff/12)+'y '+(diff%12)+'m old';
}

// ═══════════ DASHBOARD ═══════════════════════════════════════════
async function loadDashboard(){
    document.getElementById('dashContent').innerHTML = '<div class="as-empty"><div class="as-empty-icon">📦</div><p>Loading…</p></div>';
    const d = await asGet('/assets/api/dashboard');
    if(!d.ok) return;

    // ── Stat cards ───────────────────────────────────────────────
    const activeCount = (d.by_status||[]).find(s=>s.status==='active')?.cnt || 0;
    const statCards = `
    <div class="adb-stats-grid">
        <div class="adb-stat-card">
            <div class="adb-stat-label">Total Assets</div>
            <div class="adb-stat-value">${d.total}</div>
            <div class="adb-stat-sub"><span class="adb-dot" style="background:#10b981"></span>${activeCount} active</div>
        </div>
        <div class="adb-stat-card">
            <div class="adb-stat-label">Active Value</div>
            <div class="adb-stat-value" style="color:#059669">${fmtKpi(d.total_value)}</div>
            <div class="adb-stat-sub">Across all categories</div>
        </div>
        <div class="adb-stat-card">
            <div class="adb-stat-label">In Repair</div>
            <div class="adb-stat-value" style="color:#f59e0b">${d.in_repair}</div>
            <div class="adb-stat-sub"><span class="adb-dot" style="background:#f59e0b"></span>${fmtKpi(d.repair_cost)} repair costs</div>
        </div>
        <div class="adb-stat-card">
            <div class="adb-stat-label">Warranty Alerts</div>
            <div class="adb-stat-value" style="color:#ef4444">${d.expired_warranty}</div>
            <div class="adb-stat-sub"><span class="adb-dot" style="background:#ef4444"></span>Expired warranties</div>
        </div>
    </div>`;

    // ── Chart cards ──────────────────────────────────────────────
    const statusColors = { active:'#059669', in_repair:'#f59e0b', maintenance:'#3b82f6', retired:'#94a3b8', lost:'#ef4444' };
    const byStatus  = d.by_status  || [];
    const byType    = (d.by_type   || []).slice(0, 8);

    const chartCards = `
    <div class="adb-charts-grid">
        <div class="adb-chart-card">
            <div class="adb-chart-header">
                <span class="adb-chart-title">Assets by Type</span>
                <span class="adb-chart-badge">Top ${byType.length}</span>
            </div>
            <div style="position:relative;width:100%;height:220px">
                <canvas id="adbTypeChart"></canvas>
            </div>
        </div>
        <div class="adb-chart-card">
            <div class="adb-chart-header">
                <span class="adb-chart-title">Status Breakdown</span>
                <span class="adb-chart-badge">${byStatus.length} status${byStatus.length!==1?'es':''}</span>
            </div>
            <div style="position:relative;width:100%;height:220px;display:flex;align-items:center;justify-content:center">
                <canvas id="adbStatusChart"></canvas>
            </div>
        </div>
    </div>`;

    // ── My Assets table ──────────────────────────────────────────
    let myAssetsHtml = '';
    if(d.my_assets && d.my_assets.length){
        let rows = '';
        d.my_assets.slice(0,10).forEach(a => {
            const wExp = a.warranty_expired;
            let w = '—';
            if(a.warranty_expiry){
                w = wExp
                    ? `<span style="font-size:.75rem;font-weight:500;color:#ef4444">Expired · ${fmtDate(a.warranty_expiry)}</span>`
                    : `<span style="font-size:.75rem;font-weight:500;color:#059669">Active · ${fmtDate(a.warranty_expiry)}</span>`;
            }
            const brand = [a.brand, a.model].filter(Boolean).join(' ') || '—';
            rows += `<tr class="adb-tr">
                <td class="adb-td" style="font-family:monospace;color:#6b7280;font-weight:500">${asEsc(a.asset_tag||'—')}</td>
                <td class="adb-td"><a onclick="loadDetail(${a.id})" style="color:#059669;font-weight:500;cursor:pointer;text-decoration:none">${asEsc(a.name)}</a></td>
                <td class="adb-td">${adbTypeBadge(a.type)}</td>
                <td class="adb-td" style="color:#374151">${asEsc(brand)}</td>
                <td class="adb-td">${adbStatusCell(a.status)}</td>
                <td class="adb-td">${w}</td>
            </tr>`;
        });
        myAssetsHtml = `
        <div class="adb-table-card">
            <div class="adb-table-header">
                <span class="adb-table-title">My Assets</span>
                <a onclick="showView('list')" style="font-size:.8rem;font-weight:500;color:#059669;cursor:pointer;text-decoration:none">View all →</a>
            </div>
            <div style="overflow-x:auto">
                <table style="width:100%;border-collapse:collapse">
                    <thead><tr>
                        <th class="adb-th">Tag</th><th class="adb-th">Name</th><th class="adb-th">Type</th>
                        <th class="adb-th">Brand / Model</th><th class="adb-th">Status</th><th class="adb-th">Warranty</th>
                    </tr></thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>
        </div>`;
    } else {
        myAssetsHtml = `
        <div class="adb-table-card">
            <div class="adb-table-header"><span class="adb-table-title">My Assets</span></div>
            <div class="as-empty" style="padding:32px"><p>No assets assigned to you</p></div>
        </div>`;
    }

    // ── Alerts row (checkups + attention) ────────────────────────
    let alertsHtml = '';
    if(d.checkups_due_list?.length){
        alertsHtml = `<div style="margin-top:16px">`;
        if(d.checkups_due_list?.length){
            alertsHtml += `<div class="adb-chart-card"><div class="adb-chart-header"><span class="adb-chart-title">🔍 Checkups Due</span><span class="adb-chart-badge">${d.checkups_due_list.length}</span></div>`;
            d.checkups_due_list.slice(0,5).forEach(c=>{
                alertsHtml += `<div class="as-alert as-alert-red"><a onclick="loadDetail(${c.id})" style="color:inherit;cursor:pointer;font-weight:600">${asEsc(c.asset_tag)}</a> ${asEsc(c.name)} — ${fmtDate(c.next_checkup)}</div>`;
            });
            alertsHtml += `</div>`;
        }
        alertsHtml += `</div>`;
}

    document.getElementById('dashContent').innerHTML = statCards + chartCards + myAssetsHtml + alertsHtml;

    // ── Chart.js — Assets by Type (horizontal bar) ───────────────
    if(window.Chart && byType.length){
        const typeCtx = document.getElementById('adbTypeChart')?.getContext('2d');
        if(typeCtx){
            const greens = ['#059669','#10b981','#34d399','#6ee7b7','#a7f3d0','#d1fae5','#ecfdf5','#f0fdf4'];
            new Chart(typeCtx, {
                type: 'bar',
                data: {
                    labels: byType.map(r => r.type),
                    datasets:[{ data: byType.map(r=>r.cnt), backgroundColor: greens.slice(0,byType.length), borderRadius:5, borderSkipped:false, barThickness:20 }]
                },
                options:{
                    indexAxis:'y', responsive:true, maintainAspectRatio:false,
                    plugins:{ legend:{display:false}, tooltip:{ backgroundColor:'#111113', padding:10, cornerRadius:8, displayColors:false, callbacks:{ label: ctx=>`${ctx.parsed.x} assets` } } },
                    scales:{
                        x:{ grid:{color:'#f3f4f6'}, border:{display:false}, ticks:{font:{size:11}} },
                        y:{ grid:{display:false}, border:{display:false}, ticks:{font:{size:12,weight:'500'},color:'#374151'} }
                    }
                }
            });
        }
    }

    // ── Chart.js — Status Breakdown (doughnut) ───────────────────
    if(window.Chart && byStatus.length){
        const statusCtx = document.getElementById('adbStatusChart')?.getContext('2d');
        if(statusCtx){
            const bgColors = byStatus.map(r => statusColors[r.status] || '#94a3b8');
            new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: byStatus.map(r => r.status.replace('_',' ')),
                    datasets:[{ data: byStatus.map(r=>r.cnt), backgroundColor: bgColors, borderWidth:0, spacing:3, borderRadius:5 }]
                },
                options:{
                    responsive:true, maintainAspectRatio:false, cutout:'68%',
                    plugins:{
                        legend:{ position:'right', labels:{ usePointStyle:true, pointStyle:'circle', padding:14, font:{size:12,weight:'500'}, color:'#374151',
                            generateLabels: chart => chart.data.labels.map((label,i) => ({
                                text: `${label.charAt(0).toUpperCase()+label.slice(1)}  ·  ${chart.data.datasets[0].data[i]}`,
                                fillStyle: chart.data.datasets[0].backgroundColor[i], strokeStyle:'transparent', pointStyle:'circle', index:i
                            }))
                        }},
                        tooltip:{ backgroundColor:'#111113', padding:10, cornerRadius:8, displayColors:false, callbacks:{ label: ctx=>{ const t=ctx.dataset.data.reduce((a,b)=>a+b,0); return `${ctx.label}: ${ctx.parsed} (${((ctx.parsed/t)*100).toFixed(1)}%)`; } } }
                    }
                }
            });
        }
    }
}

// ── Dashboard helper renderers ──────────────────────────────────────
function adbTypeBadge(t){
    const map = { laptop:'#eff6ff:#3b82f6', mobile:'#fef3c7:#b45309', phone:'#fef3c7:#b45309', networking:'#eff6ff:#3b82f6', monitor:'#f0fdf4:#059669' };
    const [bg,color] = (map[t]||'#f0fdf4:#059669').split(':');
    return `<span style="display:inline-flex;align-items:center;padding:2px 9px;border-radius:20px;font-size:.72rem;font-weight:500;background:${bg};color:${color}">${asEsc(t||'')}</span>`;
}
function adbStatusCell(s){
    const map = { active:'#10b981', in_repair:'#f59e0b', maintenance:'#3b82f6', retired:'#94a3b8', lost:'#ef4444' };
    const dot = map[s]||'#94a3b8';
    return `<span style="display:inline-flex;align-items:center;gap:6px;font-size:.82rem;color:#6b7280">
        <span style="width:7px;height:7px;border-radius:50%;background:${dot};display:inline-block;flex-shrink:0"></span>
        ${asEsc((s||'').replace('_',' '))}
    </span>`;
}

// ═══════════ ASSET LIST ═══════════════════════════════════════════
let listTimer, currentListPage = 1;
function debounceList(){ clearTimeout(listTimer); listTimer=setTimeout(()=>loadList(1),300); }
function clearListFilters(){
    ['asSearch','asType','asStatus','asOwner'].forEach(id => { const el=document.getElementById(id); if(el.tagName==='INPUT') el.value=''; else el.value=''; });
    loadList(1);
}
async function loadList(page=1){
    currentListPage = page;
    const params = {
        q:       document.getElementById('asSearch').value,
        ftype:   document.getElementById('asType').value,
        fstatus: document.getElementById('asStatus').value,
        fowner:  document.getElementById('asOwner').value,
        p:       page,
    };
    const d = await asGet('/assets/api/list', params);
    if(!d.ok) return;
    const assets = d.assets;
    if(!assets.length){
        document.getElementById('listContent').innerHTML = '<div class="as-empty"><div class="as-empty-icon">📋</div><p>No assets found</p></div>';
        document.getElementById('listPagination').innerHTML = '';
        return;
    }
    let html = `<div class="as-table-wrap"><table class="as-table"><thead><tr>
        <th>Tag</th><th>Name</th><th>Type</th><th>Owner</th><th>Status</th><th>Remarks</th><th>Checkup</th><th>Actions</th>
    </tr></thead><tbody>`;
    assets.forEach(a => {
        const checkup = a.checkup_due ? '<span class="as-badge as-badge-red as-badge-xs">Due!</span>' :
                        a.next_checkup ? `<span class="as-badge as-badge-green as-badge-xs">${fmtDate(a.next_checkup)}</span>` : '—';
        const viewBtn = `<button class="as-btn as-btn-secondary as-btn-sm" onclick="loadDetail(${a.id})" title="View details" style="padding:4px 9px">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>`;
        const editBtn = AS_CAN_EDIT ? `<button class="as-btn as-btn-ghost as-btn-sm" onclick='editAsset(${JSON.stringify(a)})' title="Edit asset" style="padding:4px 9px">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        </button>` : '';
        const delBtn = AS_CAN_EDIT ? `<button class="as-btn as-btn-ghost as-btn-sm" onclick="confirmDeleteAsset(${a.id})" title="Delete asset" style="padding:4px 9px;color:#ef4444">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>
        </button>` : '';
        html += `<tr>
            <td><strong>${asEsc(a.asset_tag)}</strong></td>
            <td style="font-weight:500">${asEsc(a.name)}</td>
            <td>${typeBadge(a.type)}</td>
            <td>${asEsc(a.owner_name||'—')}</td>
            <td>${statusBadge(a.status)}</td>
            <td style="font-size:.76rem;max-width:140px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap" title="${asEsc(a.remarks||'')}">${asEsc(a.remarks)||'—'}</td>
            <td>${checkup}</td>
            <td style="white-space:nowrap">${viewBtn}${editBtn}${delBtn}</td>
        </tr>`;
    });
    html += `</tbody></table></div>`;
    document.getElementById('listContent').innerHTML = html;

    // Pagination
    const pg = document.getElementById('listPagination');
    pg.innerHTML = '';
    if(d.pages > 1){
        // Count label
        const label = document.createElement('span');
        label.style.cssText = 'font-size:.78rem;color:#64748b;margin-right:4px';
        label.textContent = `${((d.page-1)*25)+1}–${Math.min(d.page*25, d.total)} of ${d.total}`;
        pg.appendChild(label);

        // Previous button
        const prev = document.createElement('button');
        prev.textContent = '← Previous';
        prev.style.cssText = 'font-size:.76rem';
        if(d.page <= 1) prev.disabled = true;
        prev.onclick = () => loadList(d.page - 1);
        pg.appendChild(prev);

        // Page number buttons — sliding window of 8
        const winSize = 8;
        let winStart = Math.max(1, d.page - Math.floor(winSize / 2));
        let winEnd   = winStart + winSize - 1;
        if(winEnd > d.pages){ winEnd = d.pages; winStart = Math.max(1, winEnd - winSize + 1); }
        for(let p = winStart; p <= winEnd; p++){
            const btn = document.createElement('button');
            btn.textContent = p;
            if(p === d.page) btn.className = 'active';
            btn.onclick = () => loadList(p);
            pg.appendChild(btn);
        }

        // Next button
        const next = document.createElement('button');
        next.textContent = 'Next →';
        next.style.cssText = 'font-size:.76rem';
        if(d.page >= d.pages) next.disabled = true;
        next.onclick = () => loadList(d.page + 1);
        pg.appendChild(next);
    }
}

// ═══════════ ASSET DETAIL ══════════════════════════════════════════
let _currentAssetId = null;
async function loadDetail(id){
    _currentAssetId = id;
    showView('detail');
    document.getElementById('detailContent').innerHTML = '<div class="as-empty"><div class="as-empty-icon">📦</div><p>Loading…</p></div>';
    const d = await asGet(`/assets/api/detail/${id}`);
    if(!d.ok) return;
    const a = d.asset;

    let html = `<div class="as-page-header">
        <div>
            <div class="as-page-title">${asEsc(a.asset_tag)} — ${asEsc(a.name)}</div>
            <div style="display:flex;gap:6px;margin-top:6px;flex-wrap:wrap">
                ${typeBadge(a.type)}
                ${statusBadge(a.status)}
                ${a.warranty_expiry ? `<span class="as-badge ${a.warranty_expired?'as-badge-red':'as-badge-green'}">${a.warranty_expired?'Warranty Expired':'Warranty Active'}</span>` : ''}
            </div>
        </div>
        <div class="as-page-actions">
            ${AS_CAN_EDIT ? `<button class="as-btn as-btn-primary as-btn-sm" onclick='editAsset(${JSON.stringify(a)})'>✏️ Edit</button>
            <button class="as-btn as-btn-danger as-btn-sm" onclick="confirmDeleteAsset(${a.id})">Delete</button>` : ''}
            <button class="as-btn as-btn-secondary as-btn-sm" onclick="showView('list')">← Back</button>
        </div>
    </div>`;

    // ── Two-column layout: 75% info | 25% QR ──────────────────────
    html += `<div style="display:flex;gap:16px;margin-bottom:16px;align-items:flex-start;flex-wrap:wrap">`;

    // Left column — info grid + remarks + notes
    html += `<div style="flex:3;min-width:0">`;
    html += `<div class="as-detail-grid" style="margin-bottom:12px">
        <div class="as-detail-cell"><div class="as-detail-cell-label">Brand</div><div class="as-detail-cell-value">${asEsc(a.brand||'—')}</div></div>
        <div class="as-detail-cell"><div class="as-detail-cell-label">Model</div><div class="as-detail-cell-value">${asEsc(a.model||'—')}</div></div>
        <div class="as-detail-cell"><div class="as-detail-cell-label">Serial #</div><div class="as-detail-cell-value">${asEsc(a.serial_number||'—')}</div></div>
        <div class="as-detail-cell"><div class="as-detail-cell-label">Assigned To</div><div class="as-detail-cell-value">${asEsc(a.owner_name||'Unassigned')}</div></div>
        <div class="as-detail-cell"><div class="as-detail-cell-label">Purchase Date</div><div class="as-detail-cell-value">${fmtDate(a.purchase_date)||'—'}</div></div>
        <div class="as-detail-cell"><div class="as-detail-cell-label">Purchase Price</div><div class="as-detail-cell-value">${fmtPrice(a.purchase_price)}</div></div>
        <div class="as-detail-cell"><div class="as-detail-cell-label">Vendor</div><div class="as-detail-cell-value">${asEsc(a.vendor||'—')}</div></div>
        <div class="as-detail-cell"><div class="as-detail-cell-label">Age</div><div class="as-detail-cell-value">${assetAge(a.purchase_date)}</div></div>
        <div class="as-detail-cell"><div class="as-detail-cell-label">Warranty Expiry</div><div class="as-detail-cell-value" style="${a.warranty_expired?'color:#ef4444':''}">${fmtDate(a.warranty_expiry)||'—'}</div></div>
        <div class="as-detail-cell"><div class="as-detail-cell-label">Last Checkup</div><div class="as-detail-cell-value">${fmtDate(a.last_checkup)||'Never'}</div></div>
        <div class="as-detail-cell"><div class="as-detail-cell-label">Next Checkup</div><div class="as-detail-cell-value">
            ${fmtDate(a.next_checkup)||'—'}${a.checkup_due?'<span class="as-badge as-badge-red as-badge-xs" style="margin-left:4px">Overdue</span>':''}
        </div></div>
        <div class="as-detail-cell"><div class="as-detail-cell-label">Checkup Every</div><div class="as-detail-cell-value">${a.checkup_interval||90} days</div></div>
    </div>`;
    if(a.remarks) html += `<div class="as-card" style="margin-bottom:10px;border-left:3px solid #f59e0b"><div class="as-card-title">⚠️ Remarks</div><div class="as-card-body" style="font-size:.84rem;line-height:1.7">${asEsc(a.remarks)}</div></div>`;
    if(a.notes)   html += `<div class="as-card" style="margin-bottom:10px"><div class="as-card-title">Notes</div><div class="as-card-body" style="font-size:.84rem;line-height:1.7">${asEsc(a.notes)}</div></div>`;
    html += `</div>`; // end left column

    // Right column — QR code (always rendered, empty card if no QR)
    html += `<div style="flex:1;min-width:160px;max-width:220px">
        <div class="as-card" style="padding:16px;text-align:center">`;
    if(a.qr_code){
        html += `<div id="qr-detail-${id}" style="margin-bottom:8px;display:inline-block"></div>
            <div style="font-weight:700;font-size:.88rem;margin-bottom:2px">${asEsc(a.asset_tag)}</div>
            <div style="font-size:.7rem;color:#64748b;margin-bottom:10px">${asEsc(a.name)}</div>
            <button class="as-btn as-btn-secondary as-btn-sm" style="width:100%" onclick="printQR('${asEsc(a.asset_tag)}','${asEsc(a.name).replace(/'/g,"\\'")}','${asEsc(a.qr_code)}')">🖨️ Print QR</button>`;
    } else {
        html += `<div style="color:#94a3b8;font-size:.78rem;padding:20px 0">
            <div style="font-size:1.8rem;margin-bottom:6px">🏷️</div>
            No QR mapped<br>
            <a onclick="showView('qr-map')" style="color:#14b8a6;cursor:pointer;font-size:.75rem">Map a QR code →</a>
        </div>`;
    }
    html += `</div></div>`; // end right column card + column

    html += `</div>`; // end two-column row

    // Assignment History
    html += `<div class="as-card" style="margin-bottom:12px"><div class="as-card-title">Assignment History</div><div class="as-table-wrap"><table class="as-table"><thead><tr><th>Staff</th><th>Assigned</th><th>Returned</th><th>Duration</th></tr></thead><tbody>`;
    if(d.assignments?.length){
        d.assignments.forEach(r => {
            let dur = '—';
            if(r.assigned_at){ const s=new Date(r.assigned_at); const e=r.returned_at?new Date(r.returned_at):new Date(); const days=Math.floor((e-s)/86400000); dur=days<30?days+'d':(Math.floor(days/30)+'m '+(days%30)+'d')+(r.returned_at?'':' (current)'); }
            html += `<tr><td><strong>${asEsc(r.staff_name||'—')}</strong></td><td>${fmtDate(r.assigned_at)}</td><td>${fmtDate(r.returned_at)||'—'}</td><td>${dur}</td></tr>`;
        });
    } else { html += `<tr><td colspan="4" style="text-align:center;padding:20px;color:#94a3b8">No assignment history</td></tr>`; }
    html += `</tbody></table></div></div>`;

    // Repair History
    html += `<div class="as-card" style="margin-bottom:12px"><div class="as-card-title">🔧 Repair History ${AS_CAN_EDIT?`<button class="as-btn as-btn-primary as-btn-sm" onclick="openRepairModal(${a.id})">+ Add Repair</button>`:''}</div><div class="as-table-wrap"><table class="as-table"><thead><tr><th>Date</th><th>Issue</th><th>Cost</th><th>Vendor</th><th>Status</th><th>Notes</th></tr></thead><tbody>`;
    if(d.repairs?.length){
        d.repairs.forEach(r => {
            html += `<tr><td>${fmtDate(r.date)}</td><td>${asEsc(r.issue)}</td><td>${fmtPrice(r.cost)}</td><td>${asEsc(r.vendor||'—')}</td><td>${r.status==='completed'?'<span class="as-badge as-badge-green">Done</span>':'<span class="as-badge as-badge-amber">Pending</span>'}</td><td style="font-size:.76rem">${asEsc(r.notes||'—')}</td></tr>`;
        });
    } else { html += `<tr><td colspan="6" style="text-align:center;padding:20px;color:#94a3b8">No repairs logged</td></tr>`; }
    html += `</tbody></table></div></div>`;

    // Checkup History
    html += `<div class="as-card"><div class="as-card-title">🔍 Checkup History ${AS_CAN_EDIT?`<button class="as-btn as-btn-primary as-btn-sm" onclick="openCheckupModal(${a.id})">+ Log Checkup</button>`:''}</div><div class="as-table-wrap"><table class="as-table"><thead><tr><th>Date</th><th>Checked By</th><th>Condition</th><th>Remarks</th></tr></thead><tbody>`;
    if(d.checkups?.length){
        d.checkups.forEach(c => {
            html += `<tr><td>${fmtDate(c.checkup_date)}</td><td>${asEsc(c.checker_name||'—')}</td><td>${condBadge(c.conditions)}</td><td style="font-size:.76rem">${asEsc(c.remarks||'—')}</td></tr>`;
        });
    } else { html += `<tr><td colspan="4" style="text-align:center;padding:20px;color:#94a3b8">No checkups recorded</td></tr>`; }
    html += `</tbody></table></div></div>`;

    document.getElementById('detailContent').innerHTML = html;

    // Render QR if mapped
    if(a.qr_code){
        setTimeout(() => {
            const el = document.getElementById(`qr-detail-${id}`);
            if(el){
                const url = `${AS_BASE_URL}/assets/qr-lookup?code=${encodeURIComponent(a.qr_code)}`;
                const qr = qrcode(0,'M'); qr.addData(url); qr.make();
                el.innerHTML = qr.createSvgTag(3,0);
            }
        }, 50);
    }
}

// ── Asset Modal ────────────────────────────────────────────────────
function openAssetModal(){
    document.getElementById('assetModalTitle').textContent = 'Add Asset';
    document.getElementById('am_id').value = '';
    ['am_name','am_brand','am_model','am_serial','am_vendor','am_notes','am_remarks'].forEach(id => document.getElementById(id).value='');
    document.getElementById('am_pprice').value='';
    document.getElementById('am_pdate').value='';
    document.getElementById('am_warranty').value='';
    document.getElementById('am_type').value=@json($types[0] ?? 'laptop');
    document.getElementById('am_status').value='active';
    document.getElementById('am_assigned').value='';
    document.getElementById('am_interval').value='90';
    document.getElementById('am_delete_btn').style.display='none';
    asOpenModal('assetModal');
}
function editAsset(a){
    document.getElementById('assetModalTitle').textContent = 'Edit Asset';
    document.getElementById('am_id').value       = a.id;
    document.getElementById('am_name').value     = a.name||'';
    document.getElementById('am_type').value     = a.type||'';
    document.getElementById('am_brand').value    = a.brand||'';
    document.getElementById('am_model').value    = a.model||'';
    document.getElementById('am_serial').value   = a.serial_number||'';
    document.getElementById('am_pdate').value    = a.purchase_date||'';
    document.getElementById('am_pprice').value   = a.purchase_price||'';
    document.getElementById('am_vendor').value   = a.vendor||'';
    document.getElementById('am_assigned').value = a.assigned_to||'';
    document.getElementById('am_status').value   = a.status||'active';
    document.getElementById('am_warranty').value = a.warranty_expiry||'';
    document.getElementById('am_notes').value    = a.notes||'';
    document.getElementById('am_remarks').value  = a.remarks||'';
    document.getElementById('am_interval').value = a.checkup_interval||90;
    document.getElementById('am_delete_btn').style.display = 'inline-flex';
    asOpenModal('assetModal');
}
async function saveAsset(){
    const data = {
        id:               document.getElementById('am_id').value||null,
        name:             document.getElementById('am_name').value.trim(),
        type:             document.getElementById('am_type').value,
        brand:            document.getElementById('am_brand').value.trim(),
        model:            document.getElementById('am_model').value.trim(),
        serial_number:    document.getElementById('am_serial').value.trim(),
        purchase_date:    document.getElementById('am_pdate').value,
        purchase_price:   document.getElementById('am_pprice').value||null,
        vendor:           document.getElementById('am_vendor').value.trim(),
        assigned_to:      document.getElementById('am_assigned').value||null,
        status:           document.getElementById('am_status').value,
        warranty_expiry:  document.getElementById('am_warranty').value||null,
        notes:            document.getElementById('am_notes').value.trim(),
        remarks:          document.getElementById('am_remarks').value.trim(),
        checkup_interval: parseInt(document.getElementById('am_interval').value)||90,
    };
    if(!data.name){ asToast('Name is required','error'); return; }
    const r = await asPost('/assets/api/save', data);
    if(r.ok){
        asToast(r.msg);
        asCloseModal('assetModal');
        if(currentView==='dashboard') loadDashboard();
        else if(currentView==='list') loadList();
    } else asToast(r.error||'Error','error');
}
async function deleteAsset(){
    const id = document.getElementById('am_id').value;
    asConfirm('Delete this asset?', 'This action cannot be undone. All related records will be removed.', async () => {
        const r = await asPost('/assets/api/delete', {id});
        if(r.ok){ asToast('Asset deleted'); asCloseModal('assetModal'); showView('list'); }
        else asToast(r.error||'Error','error');
    }, 'Delete');
}
async function confirmDeleteAsset(id){
    asConfirm('Delete this asset?', 'This action cannot be undone. All related records will be removed.', async () => {
        const r = await asPost('/assets/api/delete', {id});
        if(r.ok){ asToast('Asset deleted'); showView('list'); }
        else asToast(r.error||'Error','error');
    }, 'Delete');
}

// ── Repair Modal ───────────────────────────────────────────────────
function openRepairModal(assetId){
    document.getElementById('rm_asset_id').value = assetId;
    document.getElementById('rm_date').value = new Date().toISOString().split('T')[0];
    ['rm_issue','rm_vendor','rm_notes'].forEach(id=>document.getElementById(id).value='');
    document.getElementById('rm_cost').value='0';
    document.getElementById('rm_status').value='pending';
    asOpenModal('repairModal');
}
async function saveRepair(){
    const assetId = document.getElementById('rm_asset_id').value;
    const issue   = document.getElementById('rm_issue').value.trim();
    if(!issue){ asToast('Issue is required','error'); return; }
    const r = await asPost('/assets/api/repair', {
        asset_id: assetId, date: document.getElementById('rm_date').value,
        issue, cost: document.getElementById('rm_cost').value||0,
        vendor: document.getElementById('rm_vendor').value.trim(),
        status: document.getElementById('rm_status').value,
        notes:  document.getElementById('rm_notes').value.trim(),
    });
    if(r.ok){ asToast('Repair logged'); asCloseModal('repairModal'); loadDetail(assetId); }
    else asToast(r.error||'Error','error');
}

// ── Checkup Modal ──────────────────────────────────────────────────
function openCheckupModal(assetId){
    document.getElementById('cm2_asset_id').value = assetId;
    document.getElementById('cm2_date').value = new Date().toISOString().split('T')[0];
    document.getElementById('cm2_cond').value = 'good';
    document.getElementById('cm2_remarks').value = '';
    asOpenModal('checkupModal');
}
async function saveCheckup(){
    const assetId = document.getElementById('cm2_asset_id').value;
    const r = await asPost('/assets/api/checkup', {
        asset_id: assetId, checkup_date: document.getElementById('cm2_date').value,
        conditions: document.getElementById('cm2_cond').value,
        remarks: document.getElementById('cm2_remarks').value.trim(),
    });
    if(r.ok){ asToast('Checkup logged'); asCloseModal('checkupModal'); loadDetail(assetId); }
    else asToast(r.error||'Error','error');
}

// ═══════════ QR LABELS ════════════════════════════════════════════
let currentQrPage = 1;
const QR_SIZES = {
    small:  { card: 110, module: 2 },
    medium: { card: 140, module: 3 },
    large:  { card: 175, module: 4 },
};
async function loadQrCodes(page=1){
    currentQrPage = page;
    const filter = document.getElementById('qrFilter').value;
    const size   = document.getElementById('qrSize').value;
    const sz     = QR_SIZES[size] || QR_SIZES.medium;
    const d = await asGet('/assets/api/qr-codes', {qf:filter, p:page});
    if(!d.ok) return;

    // Update title with total count
    document.getElementById('qrLabelTitle').textContent = `🏷️ QR Labels — ${d.total} codes`;

    // Render grid
    const grid = document.getElementById('qrGrid');
    grid.innerHTML = '';
    d.codes.forEach((c, i) => {
        const el = document.createElement('div');
        el.className = 'as-qr-label' + (c.asset_id?' mapped':'');
        el.style.width = sz.card + 'px';
        el.dataset.code = c.qr_code;
        el.innerHTML = `<span class="as-qr-dot ${c.asset_id?'as-qr-dot-green':'as-qr-dot-gray'}"></span>
            <div id="qrl_${i}"></div>
            <div class="qr-id">${asEsc(c.qr_code)}</div>
            ${c.asset_id?`<div class="qr-mapped-name">${asEsc(c.asset_tag)} — ${asEsc(c.asset_name)}</div>`:''}`;
        grid.appendChild(el);
        // Generate QR
        setTimeout(() => {
            const url = `${AS_BASE_URL}/assets/qr-lookup?code=${encodeURIComponent(c.qr_code)}`;
            const qr = qrcode(0,'M'); qr.addData(url); qr.make();
            const target = document.getElementById(`qrl_${i}`);
            if(target) target.innerHTML = qr.createSvgTag(sz.module, 0);
        }, 0);
    });

    // Pagination
    const pg = document.getElementById('qrPagination');
    pg.innerHTML = '';
    for(let p=1; p<=d.pages; p++){
        const btn = document.createElement('button');
        btn.textContent = p;
        if(p===d.page) btn.className='active';
        btn.onclick = ()=>loadQrCodes(p);
        pg.appendChild(btn);
    }
}
function printQrGrid(){
    const items = document.querySelectorAll('#qrGrid .as-qr-label');
    if(!items.length){ asToast('No QR codes to print','error'); return; }
    const w = window.open('','','width=900,height=700');
    let html = `<html><head><title>QR Labels</title><style>
        body{font-family:sans-serif;margin:10px}
        .grid{display:flex;flex-wrap:wrap;gap:6px}
        .label{border:1px dashed #ccc;border-radius:6px;padding:8px;text-align:center;break-inside:avoid}
        .code{font-size:.72rem;font-weight:700;margin-top:4px;letter-spacing:.5px}
        .sub{font-size:.6rem;color:#555;margin-top:2px}
        @media print{body{margin:0}.grid{gap:4px}}
    </style></head><body><div class="grid">`;
    items.forEach(el => {
        const svgEl = el.querySelector('svg');
        const code  = el.dataset.code || '';
        const mapped = el.querySelector('.qr-mapped-name')?.textContent || '';
        html += `<div class="label">${svgEl ? svgEl.outerHTML : ''}<div class="code">${code}</div>${mapped?`<div class="sub">${mapped}</div>`:''}</div>`;
    });
    html += `</div><script>window.onload=()=>window.print();<\/script></body></html>`;
    w.document.write(html);
    w.document.close();
}
async function generateQr(){
    asConfirm('Generate 50 more QR labels?', 'New GL-XXXX codes will be added to the system.', async () => {
        const r = await asPost('/assets/api/qr-generate', {count:50});
        if(r.ok){ asToast(`${r.generated} labels generated`); loadQrCodes(1); }
        else asToast(r.error||'Error','error');
    }, 'Generate');
}
function printQR(tag, name, code){
    const lookupUrl = `${AS_BASE_URL}/assets/qr-lookup?code=${encodeURIComponent(code)}`;
    const qr = qrcode(0,'M'); qr.addData(lookupUrl); qr.make();
    const svg = qr.createSvgTag(4,0);
    const w = window.open('','','width=320,height=420');
    w.document.write(`<html><head><title>QR-${tag}</title><style>body{font-family:sans-serif;text-align:center;padding:20px}svg{width:160px;height:160px}.tag{font-weight:700;font-size:16px;margin-top:8px;letter-spacing:1px}.name{font-size:12px;color:#555;margin-top:4px}@media print{body{padding:10px}}</style></head><body>${svg}<div class="tag">${tag}</div><div class="name">${name}</div><script>setTimeout(()=>{window.print();window.close()},400)<\/script></body></html>`);
    w.document.close();
}

// ═══════════ QR MAP ═══════════════════════════════════════════════
async function loadQrMapData(){
    document.getElementById('qrMapContent').innerHTML = '<div class="as-empty"><div class="as-empty-icon">🔗</div><p>Loading…</p></div>';
    const d = await asGet('/assets/api/qr-map-data');
    if(!d.ok) return;

    let html = '';
    if(AS_CAN_EDIT){
        html += `<div class="as-card" style="margin-bottom:16px">
            <div class="as-card-title">Map QR Label → Asset</div>
            <div class="as-card-body">
                <div style="display:flex;gap:12px;flex-wrap:wrap;align-items:flex-end">
                    <div style="flex:1;min-width:140px">
                        <label style="font-size:.78rem;font-weight:500;display:block;margin-bottom:4px">QR Code</label>
                        <select id="mapQrCode" style="width:100%;padding:8px 11px;border:1px solid #e2e8f0;border-radius:8px;font-size:.82rem;font-family:inherit">
                            <option value="">Select QR label…</option>
                            ${d.unmapped_qr.map(c=>`<option value="${asEsc(c)}">${asEsc(c)}</option>`).join('')}
                        </select>
                    </div>
                    <div style="flex:2;min-width:200px">
                        <label style="font-size:.78rem;font-weight:500;display:block;margin-bottom:4px">Asset</label>
                        <select id="mapAssetId" style="width:100%;padding:8px 11px;border:1px solid #e2e8f0;border-radius:8px;font-size:.82rem;font-family:inherit">
                            <option value="">Select asset…</option>
                            ${d.unmapped_assets.map(a=>`<option value="${a.id}">${asEsc(a.asset_tag)} — ${asEsc(a.name)}</option>`).join('')}
                        </select>
                    </div>
                    <button class="as-btn as-btn-primary" onclick="mapQr()">🔗 Map</button>
                </div>
                <p style="font-size:.74rem;color:#94a3b8;margin-top:8px">${d.unmapped_qr.length} unmapped labels · ${d.unmapped_assets.length} unmapped assets</p>
            </div>
        </div>`;
    }

    if(d.recent_maps?.length){
        html += `<div class="as-card"><div class="as-card-title">Recent Mappings</div>
            <div class="as-table-wrap"><table class="as-table"><thead><tr><th>QR Code</th><th>Asset</th><th>Name</th><th>Mapped By</th><th>Date</th>${AS_CAN_EDIT?'<th></th>':''}</tr></thead><tbody>`;
        d.recent_maps.forEach(m => {
            html += `<tr>
                <td><strong>${asEsc(m.qr_code)}</strong></td>
                <td><span class="as-badge as-badge-teal">${asEsc(m.asset_tag||'')}</span></td>
                <td>${asEsc(m.asset_name||'')}</td>
                <td>${asEsc(m.mapper||'—')}</td>
                <td style="font-size:.76rem">${m.mapped_at||'—'}</td>
                ${AS_CAN_EDIT?`<td><button class="as-btn as-btn-ghost as-btn-sm" style="color:#ef4444" onclick="unmapQr('${asEsc(m.qr_code)}')">Unmap</button></td>`:''}
            </tr>`;
        });
        html += `</tbody></table></div></div>`;
    } else {
        html += '<div class="as-empty"><div class="as-empty-icon">🔗</div><p>No mappings yet</p></div>';
    }

    document.getElementById('qrMapContent').innerHTML = html;
}
async function mapQr(){
    const qr = document.getElementById('mapQrCode').value;
    const id = document.getElementById('mapAssetId').value;
    if(!qr||!id){ asToast('Select both QR code and asset','error'); return; }
    const r = await asPost('/assets/api/qr-map', {qr_code:qr, asset_id:id});
    if(r.ok){ asToast(r.msg); loadQrMapData(); }
    else asToast(r.error||'Error','error');
}
async function unmapQr(code){
    asConfirm(`Unmap QR code ${code}?`, 'The QR code will be unlinked from its asset.', async () => {
        const r = await asPost('/assets/api/qr-unmap', {qr_code:code});
        if(r.ok){ asToast('QR unmapped'); loadQrMapData(); }
        else asToast(r.error||'Error','error');
    }, 'Unmap');
}

// ── Bootstrap ──────────────────────────────────────────────────────
loadDashboard();
</script>
</x-layouts.app>
