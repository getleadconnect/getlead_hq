<x-layouts.app title="TouchPoint">

@push('styles')
<style>
/* ── TouchPoint Module Styles ── */
.tp-wrap { padding:20px; --tp-primary:#14b8a6; --tp-primary-dark:#0d9488; --green:#10b981; --amber:#f59e0b; --red:#ef4444; --blue:#0ea5e9; }

.tp-tabs { display:flex; gap:4px; background:#f1f5f9; border-radius:12px; padding:4px; margin-bottom:24px; overflow-x:auto; border:1px solid #e2e8f0; }
.tp-tab { padding:10px 18px; border-radius:10px; font-size:.82rem; font-weight:500; cursor:pointer; white-space:nowrap; background:transparent; border:none; color:#64748b; font-family:inherit; transition:all .2s cubic-bezier(.4,0,.2,1); }
.tp-tab:hover { color:#0f172a; background:rgba(20,184,166,.08); }
.tp-tab.active { background:#fff; color:#14b8a6; box-shadow:0 2px 8px rgba(0,0,0,.08); font-weight:600; }
.tp-panel { display:none; animation:tp-fadeIn .3s ease; }
.tp-panel.active { display:block; }
@keyframes tp-fadeIn { from{opacity:0;transform:translateY(8px)} to{opacity:1;transform:translateY(0)} }

/* Stats Cards */
.tp-stats { display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:14px; margin-bottom:24px; }
.tp-stat { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:18px 16px; position:relative; overflow:hidden; transition:all .2s ease; }
.tp-stat:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(0,0,0,.06); border-color:rgba(20,184,166,.3); }
.tp-stat-label { font-size:.72rem; color:#64748b; font-weight:500; margin-bottom:8px; text-transform:uppercase; letter-spacing:.03em; }
.tp-stat-value { font-size:1.75rem; font-weight:700; letter-spacing:-.02em; line-height:1.2; }
.tp-stat-value.red   { color:#ef4444; }
.tp-stat-value.green { color:#10b981; }
.tp-stat-value.blue  { color:#0ea5e9; }
.tp-stat-value.amber { color:#f59e0b; }
.tp-stat-value.teal  { color:#14b8a6; }

/* Cards */
.tp-card { background:#fff; border:1px solid #e2e8f0; border-radius:14px; margin-bottom:16px; overflow:hidden; transition:box-shadow .2s; }
.tp-card:hover { box-shadow:0 4px 20px rgba(0,0,0,.04); }
.tp-card-header { padding:16px 20px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; background:linear-gradient(to right,#fff,rgba(20,184,166,.02)); }
.tp-card-header h3 { font-size:.88rem; font-weight:600; color:#0f172a; display:flex; align-items:center; gap:8px; }
.tp-card-body { padding:0; }
.tp-card-body.padded { padding:20px; }

/* Table */
.tp-table-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; border-radius:8px; }
.tp-table { width:100%; border-collapse:separate; border-spacing:0; font-size:.82rem; }
.tp-table th { text-align:left; padding:12px 16px; background:#f8fafc; font-weight:600; color:#64748b; font-size:.72rem; text-transform:uppercase; letter-spacing:.04em; border-bottom:1px solid #e2e8f0; }
.tp-table td { padding:12px 16px; border-bottom:1px solid #e2e8f0; transition:background .15s; }
.tp-table tr:hover td { background:rgba(20,184,166,.04); }
.tp-table tr:last-child td { border-bottom:none; }

/* Badges */
.tp-badge { display:inline-flex; align-items:center; padding:4px 10px; border-radius:20px; font-size:.7rem; font-weight:600; transition:transform .15s; }
.tp-badge-green  { background:linear-gradient(135deg,#d1fae5,#a7f3d0); color:#065f46; border:1px solid rgba(16,185,129,.2); }
.tp-badge-amber  { background:linear-gradient(135deg,#fef3c7,#fde68a); color:#92400e; border:1px solid rgba(245,158,11,.2); }
.tp-badge-red    { background:linear-gradient(135deg,#fee2e2,#fecaca); color:#991b1b; border:1px solid rgba(239,68,68,.2); }
.tp-badge-blue   { background:linear-gradient(135deg,#dbeafe,#bfdbfe); color:#1e40af; border:1px solid rgba(59,130,246,.2); }
.tp-badge-slate  { background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0; }
.tp-badge-teal   { background:linear-gradient(135deg,#ccfbf1,#99f6e4); color:#0f766e; border:1px solid rgba(20,184,166,.2); }

/* Health Indicators */
.tp-health { display:inline-block; width:10px; height:10px; border-radius:50%; margin-right:6px; vertical-align:middle; }
.tp-health-healthy  { background:#10b981; box-shadow:0 0 0 3px rgba(16,185,129,.2); }
.tp-health-at_risk  { background:#f59e0b; box-shadow:0 0 0 3px rgba(245,158,11,.2); }
.tp-health-critical,.tp-health-churning { background:#ef4444; box-shadow:0 0 0 3px rgba(239,68,68,.2); animation:tp-pulse 2s infinite; }
.tp-health-unknown  { background:#d1d5db; }
@keyframes tp-pulse { 0%,100%{box-shadow:0 0 0 3px rgba(239,68,68,.2)} 50%{box-shadow:0 0 0 6px rgba(239,68,68,.1)} }

/* Filter Bar */
.tp-filter-bar { display:flex; gap:10px; margin-bottom:18px; flex-wrap:wrap; align-items:center; }
.tp-filter-bar select,.tp-filter-bar input {
    padding:8px 14px; border:1px solid #e2e8f0; border-radius:10px; font-size:.8rem; font-family:inherit;
    background:#fff; transition:all .2s; min-height:38px;
}
.tp-filter-bar select:hover,.tp-filter-bar input:hover { border-color:#14b8a6; }
.tp-filter-bar select:focus,.tp-filter-bar input:focus { outline:none; border-color:#14b8a6; box-shadow:0 0 0 3px rgba(20,184,166,.1); }

/* Empty State */
.tp-empty { text-align:center; padding:48px 24px; color:#64748b; }
.tp-empty-icon { font-size:2.5rem; margin-bottom:12px; opacity:.6; }

/* Progress Bars */
.tp-progress { height:8px; background:#f1f5f9; border-radius:4px; overflow:hidden; flex:1; }
.tp-progress-fill { height:100%; border-radius:4px; transition:width .6s cubic-bezier(.4,0,.2,1); }

/* Metrics */
.tp-metric { display:flex; align-items:center; gap:12px; margin-bottom:12px; font-size:.84rem; }
.tp-metric-label { min-width:120px; color:#64748b; font-weight:500; }
.tp-metric-value { width:40px; text-align:right; font-weight:700; font-size:.9rem; }

/* Grid */
.tp-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(360px,1fr)); gap:18px; margin-bottom:18px; }

/* List Items */
.tp-list-item { display:flex; align-items:center; gap:14px; padding:12px 16px; border-bottom:1px solid #e2e8f0; transition:all .15s; }
.tp-list-item:last-child { border-bottom:none; }
.tp-list-item:hover { background:rgba(20,184,166,.04); }

/* Call Log */
.tp-call-log-item { padding:14px 18px; border-bottom:1px solid #e2e8f0; font-size:.82rem; position:relative; }
.tp-call-log-item:last-child { border-bottom:none; }
.tp-call-log-item::before { content:''; position:absolute; left:0; top:50%; transform:translateY(-50%); width:3px; height:0; background:#14b8a6; transition:height .2s; border-radius:0 2px 2px 0; }
.tp-call-log-item:hover::before { height:60%; }

/* Action Buttons */
.tp-action-btn { background:linear-gradient(135deg,#14b8a6,#0d9488); color:#fff; border:none; padding:6px 14px; border-radius:8px; font-size:.75rem; font-weight:500; cursor:pointer; transition:all .2s; display:inline-flex; align-items:center; gap:4px; }
.tp-action-btn:hover { transform:translateY(-1px); box-shadow:0 4px 12px rgba(20,184,166,.35); }
.tp-action-btn.secondary { background:#f1f5f9; color:#0f172a; border:1px solid #e2e8f0; }
.tp-action-btn.secondary:hover { background:#fff; border-color:#14b8a6; }

/* Modal overrides */
.tp-modal-body .form-row { display:flex; gap:16px; }
.tp-modal-body .form-group { margin-bottom:16px; display:flex; flex-direction:column; gap:4px; }
.tp-modal-body .form-label { font-size:.8rem; font-weight:500; color:#374151; }
.tp-modal-body .form-input { padding:8px 12px; border:1px solid #e2e8f0; border-radius:8px; font-size:.85rem; font-family:inherit; background:#fff; transition:border-color .2s; }
.tp-modal-body .form-input:focus { outline:none; border-color:#14b8a6; box-shadow:0 0 0 3px rgba(20,184,166,.1); }

/* Btn helpers inside tp */
.tp-btn { display:inline-flex; align-items:center; justify-content:center; gap:6px; padding:8px 16px; border-radius:8px; font-size:.82rem; font-weight:500; cursor:pointer; transition:all .2s; border:none; font-family:inherit; }
.tp-btn-primary { background:linear-gradient(135deg,#14b8a6,#0d9488); color:#fff; }
.tp-btn-primary:hover { box-shadow:0 4px 12px rgba(20,184,166,.4); transform:translateY(-1px); }
.tp-btn-secondary { background:#f1f5f9; color:#374151; border:1px solid #e2e8f0; }
.tp-btn-secondary:hover { background:#fff; border-color:#14b8a6; }
.tp-btn-sm { padding:5px 12px; font-size:.75rem; }
.tp-btn-danger { background:linear-gradient(135deg,#ef4444,#dc2626); color:#fff; }
.tp-btn-ghost { background:transparent; color:#64748b; }
.tp-btn-ghost:hover { background:#f1f5f9; color:#0f172a; }
.tp-btn-green { background:linear-gradient(135deg,#10b981,#059669); color:#fff; }

/* Toast */
.tp-toasts { position:fixed; top:22px; right:22px; z-index:9999; display:flex; flex-direction:column; gap:8px; pointer-events:none; }
.tp-toast { padding:12px 18px; border-radius:10px; font-size:.84rem; font-weight:500; box-shadow:0 4px 16px rgba(0,0,0,.15); animation:tp-slideIn .3s ease; pointer-events:auto; }
.tp-toast-success { background:#0f172a; color:#fff; }
.tp-toast-error { background:#ef4444; color:#fff; }
@keyframes tp-slideIn { from{opacity:0;transform:translateY(-8px)} to{opacity:1;transform:translateY(0)} }

/* Modal overlay */
.tp-modal-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:3000; align-items:center; justify-content:center; padding:20px; }
.tp-modal-overlay.active { display:flex; }
.tp-modal { background:#fff; border-radius:16px; width:100%; max-width:580px; max-height:90vh; overflow:hidden; display:flex; flex-direction:column; box-shadow:0 24px 64px rgba(0,0,0,.2); }
.tp-modal-lg { max-width:700px; }
.tp-modal-header { padding:20px 24px 16px; border-bottom:1px solid #e2e8f0; display:flex; justify-content:space-between; align-items:center; }
.tp-modal-header h2 { font-size:1rem; font-weight:600; color:#0f172a; }
.tp-modal-close { background:none; border:none; font-size:1.4rem; cursor:pointer; color:#64748b; line-height:1; padding:4px 8px; border-radius:6px; }
.tp-modal-close:hover { background:#f1f5f9; color:#0f172a; }
.tp-modal-body { padding:24px; overflow-y:auto; flex:1; }
.tp-modal-footer { padding:16px 24px; border-top:1px solid #e2e8f0; display:flex; gap:8px; justify-content:flex-end; align-items:center; }

/* Page header */
.tp-page-header { display:flex; align-items:flex-start; justify-content:space-between; margin-bottom:24px; flex-wrap:wrap; gap:12px; }
.tp-page-title { font-size:1.35rem; font-weight:700; color:#0f172a; display:flex; align-items:center; gap:8px; }
.tp-page-subtitle { font-size:.8rem; color:#64748b; margin-top:2px; }

@media(max-width:768px) {
    .tp-wrap { padding:14px 12px; }
    .tp-grid { grid-template-columns:1fr; }
    .tp-stats { grid-template-columns:repeat(2,1fr); gap:10px; }
    .tp-stat { padding:14px 12px; }
    .tp-stat-value { font-size:1.4rem; }
    .tp-table { font-size:.72rem; min-width:560px; }
    .tp-table th,.tp-table td { padding:9px 10px; white-space:nowrap; }
    .tp-modal-body .form-row,.tp-form-row { flex-direction:column; gap:0; }
    .tp-metric-label { min-width:100px; }
    .tp-filter-bar select,.tp-filter-bar input { flex:1; min-width:130px; }
    .tp-page-title { font-size:1.1rem; }
    .tp-page-header { margin-bottom:16px; }
    /* Modals → bottom sheet */
    .tp-modal-overlay { padding:0; align-items:flex-end; }
    .tp-modal,.tp-modal.tp-modal-lg { max-width:100%; border-radius:16px 16px 0 0; max-height:92vh; }
    .tp-cust-info-grid { grid-template-columns:1fr !important; }
}
@media(max-width:480px) {
    .tp-wrap { padding:12px 10px; }
    .tp-tabs { margin-bottom:16px; overflow-x:scroll; scrollbar-width:none; }
    .tp-tabs::-webkit-scrollbar { display:none; }
    .tp-stats { grid-template-columns:repeat(2,1fr); }
    .tp-card-header { padding:12px 14px; }
    .tp-card-body.padded { padding:14px; }
    .tp-metric-label { min-width:80px; font-size:.75rem; }
    .tp-filter-bar { gap:8px; }
    .tp-filter-bar select,.tp-filter-bar input { width:100%; min-width:0; flex:none; }
    .tp-filter-bar .tp-btn { width:100%; justify-content:center; }
    .tp-modal-body { padding:16px; }
    .tp-modal-footer { padding:12px 16px; }
    .tp-modal-header { padding:14px 16px 12px; }
}
@media(max-width:380px) {
    .tp-stats { grid-template-columns:1fr; }
    .tp-metric-value { font-size:.8rem; }
}
body{background:#fff !important;}
</style>
@endpush

<div class="tp-wrap" >

    {{-- Page Header --}}
    <div class="tp-page-header">
        <div>
            <div class="tp-page-title">💚 TouchPoint</div>
            <div class="tp-page-subtitle">Customer Retention Management</div>
        </div>
    </div>

    {{-- Tabs --}}
    <div class="tp-tabs" id="tpTabs">
        <button class="tp-tab active" data-tab="dashboard">Dashboard</button>
        <button class="tp-tab" data-tab="customers">Customers</button>
        <button class="tp-tab" data-tab="touchpoints">Touchpoints</button>
        <button class="tp-tab" data-tab="calllog">Call Log</button>
        <button class="tp-tab" data-tab="reports">Reports</button>
    </div>

    {{-- ══ DASHBOARD ══ --}}
    <div class="tp-panel active" id="panel-dashboard">
        <div class="tp-stats" id="dashStats">
            <div class="tp-stat"><div class="tp-stat-label">Loading...</div></div>
        </div>
        <div class="tp-grid">
            <div class="tp-card">
                <div class="tp-card-header"><h3>🟢 Customer Health</h3></div>
                <div class="tp-card-body padded" id="dashHealth"></div>
            </div>
            <div class="tp-card">
                <div class="tp-card-header"><h3>📅 Renewal Pipeline</h3></div>
                <div class="tp-card-body padded" id="dashPipeline"></div>
            </div>
        </div>
        <div class="tp-card" id="dashOnboardingCard" style="display:none">
            <div class="tp-card-header">
                <h3>🚀 Onboarding Pipeline</h3>
                <span class="tp-badge tp-badge-blue" id="dashTrialConvBadge"></span>
            </div>
            <div class="tp-card-body padded" id="dashOnboarding"></div>
        </div>
        <div class="tp-card">
            <div class="tp-card-header"><h3>📞 Due / Overdue Touchpoints</h3></div>
            <div class="tp-card-body" id="dashUpcoming"></div>
        </div>
    </div>

    {{-- ══ CUSTOMERS ══ --}}
    <div class="tp-panel" id="panel-customers">
        <div class="tp-filter-bar">
            <input type="text" id="custSearch" placeholder="Search customers..." oninput="debounceCust()">
            <select id="custStatus" onchange="loadCustomers()">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="churned">Churned</option>
                <option value="renewed">Renewed</option>
            </select>
            <select id="custSub" onchange="loadCustomers()">
                <option value="">All Plans</option>
                <option value="free_trial">Free Trial</option>
                <option value="extended_trial">Extended Trial</option>
                <option value="1_month">1 Month</option>
                <option value="3_month">3 Months</option>
                <option value="1_year">1 Year</option>
            </select>
            <button class="tp-btn tp-btn-primary tp-btn-sm" onclick="openCustModal()">+ Add Customer</button>
        </div>
        <div class="tp-card"><div class="tp-card-body" id="custTable"></div></div>
    </div>

    {{-- ══ TOUCHPOINTS ══ --}}
    <div class="tp-panel" id="panel-touchpoints">
        <div class="tp-filter-bar">
            <select id="tpStage" onchange="loadTouchpoints()">
                <option value="">All Stages</option>
                <option value="usage_check">🔍 Usage Check</option>
                <option value="payment">💰 Payment</option>
                <optgroup label="🚀 Onboarding">
                    <option value="welcome_call">🚀 Welcome Call</option>
                    <option value="setup_check">🚀 Setup Check</option>
                    <option value="feature_walkthrough">🚀 Feature Walkthrough</option>
                    <option value="usage_checkin">🚀 Usage Check-in</option>
                    <option value="conversion_nudge">🚀 Conversion Nudge</option>
                    <option value="extended_checkin">🚀 Extended Check-in</option>
                    <option value="final_conversion">🚀 Final Conversion</option>
                </optgroup>
            </select>
            <select id="tpStatus" onchange="loadTouchpoints()">
                <option value="pending">Pending</option>
                <option value="completed">Completed</option>
                <option value="">All</option>
            </select>
            <select id="tpDate" onchange="loadTouchpoints()">
                <option value="">All Dates</option>
                <option value="overdue">Overdue</option>
                <option value="today">Today</option>
                <option value="upcoming">Next 7 Days</option>
            </select>
            @if($isAdmin)
            <select id="tpAssigned" onchange="loadTouchpoints()">
                <option value="">All Assignees</option>
                <option value="unassigned">Unassigned</option>
                @foreach($staffList as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>
            @endif
        </div>
        @if($isAdmin)
        <div class="tp-filter-bar" style="margin-bottom:12px">
            <label style="font-size:.8rem;display:flex;align-items:center;gap:4px">
                <input type="checkbox" id="tpSelectAll" onchange="tpToggleAll(this)"> Select All
            </label>
            <select id="tpBulkAssign" style="min-width:140px">
                <option value="">Assign to...</option>
                @foreach($staffList as $s)
                <option value="{{ $s->id }}">{{ $s->name }}</option>
                @endforeach
            </select>
            <button class="tp-btn tp-btn-secondary tp-btn-sm" onclick="tpBulkAssign()">Assign Selected</button>
        </div>
        @endif
        <div class="tp-card"><div class="tp-card-body" id="tpTable"></div></div>
    </div>

    {{-- ══ CALL LOG ══ --}}
    <div class="tp-panel" id="panel-calllog">
        <p style="font-size:.82rem;color:#64748b;margin-bottom:16px">
            Select a touchpoint from the Touchpoints tab and click 📞 to log calls. Call history appears below each touchpoint.
        </p>
        <div id="callLogContent">
            <div class="tp-empty"><div class="tp-empty-icon">📞</div><p>Select a touchpoint to view call history</p></div>
        </div>
    </div>

    {{-- ══ REPORTS ══ --}}
    <div class="tp-panel" id="panel-reports">
        <div class="tp-filter-bar" style="margin-bottom:16px">
            <button class="tp-btn tp-btn-primary tp-btn-sm" id="repWeek"  onclick="loadReports('week')">This Week</button>
            <button class="tp-btn tp-btn-secondary tp-btn-sm" id="repMonth" onclick="loadReports('month')">This Month</button>
            <button class="tp-btn tp-btn-secondary tp-btn-sm" id="repAll"   onclick="loadReports('all')">All Time</button>
        </div>
        <div id="reportsContent">
            <div class="tp-empty"><div class="tp-empty-icon">📊</div><p>Loading reports...</p></div>
        </div>
    </div>

</div>{{-- /.tp-wrap --}}

{{-- ══ CUSTOMER MODAL ══ --}}
<div class="tp-modal-overlay" id="custModal">
    <div class="tp-modal tp-modal-lg">
        <div class="tp-modal-header">
            <h2 id="custModalTitle">Add Customer</h2>
            <button class="tp-modal-close" onclick="tpCloseModal('custModal')">×</button>
        </div>
        <div class="tp-modal-body">
            <input type="hidden" id="cm_id">
            <div class="form-row" style="display:flex;gap:16px">
                <div style="flex:1;display:flex;flex-direction:column;gap:4px;margin-bottom:16px">
                    <label style="font-size:.8rem;font-weight:500;color:#374151">Name *</label>
                    <input type="text" id="cm_name" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:.85rem;font-family:inherit">
                </div>
                <div style="flex:1;display:flex;flex-direction:column;gap:4px;margin-bottom:16px">
                    <label style="font-size:.8rem;font-weight:500;color:#374151">Company</label>
                    <input type="text" id="cm_company" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:.85rem;font-family:inherit">
                </div>
            </div>
            <div class="form-row" style="display:flex;gap:16px">
                <div style="flex:1;display:flex;flex-direction:column;gap:4px;margin-bottom:16px">
                    <label style="font-size:.8rem;font-weight:500;color:#374151">Phone *</label>
                    <input type="text" id="cm_phone" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:.85rem;font-family:inherit">
                </div>
                <div style="flex:1;display:flex;flex-direction:column;gap:4px;margin-bottom:16px">
                    <label style="font-size:.8rem;font-weight:500;color:#374151">Email</label>
                    <input type="email" id="cm_email" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:.85rem;font-family:inherit">
                </div>
            </div>
            <div class="form-row" style="display:flex;gap:16px">
                <div style="flex:1;display:flex;flex-direction:column;gap:4px;margin-bottom:16px">
                    <label style="font-size:.8rem;font-weight:500;color:#374151">Subscription *</label>
                    <select id="cm_sub" onchange="calcExpiry()" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:.85rem;font-family:inherit">
                        <option value="">Select...</option>
                        <option value="free_trial">🆕 Free Trial (7 days)</option>
                        <option value="extended_trial">🔄 Extended Trial (14 days)</option>
                        <option value="1_month">1 Month</option>
                        <option value="3_month">3 Months</option>
                        <option value="1_year">1 Year</option>
                    </select>
                </div>
                <div style="flex:1;display:flex;flex-direction:column;gap:4px;margin-bottom:16px">
                    <label style="font-size:.8rem;font-weight:500;color:#374151">Start Date *</label>
                    <input type="date" id="cm_start" onchange="calcExpiry()" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:.85rem;font-family:inherit">
                </div>
                <div style="flex:1;display:flex;flex-direction:column;gap:4px;margin-bottom:16px">
                    <label style="font-size:.8rem;font-weight:500;color:#374151">Expiry Date *</label>
                    <input type="date" id="cm_expiry" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:.85rem;font-family:inherit">
                </div>
            </div>
            <div id="cm_extra_row" class="form-row" style="display:none;gap:16px;margin-bottom:16px">
                <div style="flex:1;display:flex;flex-direction:column;gap:4px">
                    <label style="font-size:.8rem;font-weight:500;color:#374151">Status</label>
                    <select id="cm_status" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:.85rem;font-family:inherit">
                        <option value="active">Active</option>
                        <option value="renewed">Renewed</option>
                        <option value="churned">Churned</option>
                    </select>
                </div>
                <div style="flex:1;display:flex;flex-direction:column;gap:4px">
                    <label style="font-size:.8rem;font-weight:500;color:#374151">Health</label>
                    <select id="cm_health" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:.85rem;font-family:inherit">
                        <option value="unknown">Unknown</option>
                        <option value="healthy">Healthy</option>
                        <option value="at_risk">At Risk</option>
                        <option value="churning">Critical</option>
                    </select>
                </div>
            </div>
            <div style="display:flex;flex-direction:column;gap:4px;margin-bottom:16px">
                <label style="font-size:.8rem;font-weight:500;color:#374151">Notes</label>
                <textarea id="cm_notes" rows="2" style="padding:8px 12px;border:1px solid #e2e8f0;border-radius:8px;font-size:.85rem;font-family:inherit;resize:vertical"></textarea>
            </div>
        </div>
        <div class="tp-modal-footer">
            <button class="tp-btn tp-btn-danger tp-btn-sm" id="cm_delete_btn" style="display:none;margin-right:auto" onclick="deleteCust()">Delete</button>
            <button class="tp-btn tp-btn-secondary tp-btn-sm" onclick="tpCloseModal('custModal')">Cancel</button>
            <button class="tp-btn tp-btn-primary tp-btn-sm" onclick="saveCust()">Save Customer</button>
        </div>
    </div>
</div>

{{-- ══ TOUCHPOINT ACTION MODAL ══ --}}
<div class="tp-modal-overlay" id="tpActionModal">
    <div class="tp-modal tp-modal-lg">
        <div class="tp-modal-header">
            <h2 id="tpActionTitle">Touchpoint</h2>
            <button class="tp-modal-close" onclick="tpCloseModal('tpActionModal')">×</button>
        </div>
        <div class="tp-modal-body" id="tpActionBody"></div>
    </div>
</div>

{{-- Toast container --}}
<div class="tp-toasts" id="tpToasts"></div>

<script>
const TP_IS_ADMIN  = @json($isAdmin);
const TP_STAFF_ID  = @json($staffId);
const TP_STAFF_MAP = @json($staffMap);
const TP_STAFF_LIST= @json($staffList);
const TP_STAGES    = @json(\App\Http\Controllers\TouchPointController::STAGES);
const TP_OUTCOMES  = @json(\App\Http\Controllers\TouchPointController::OUTCOMES);
const TP_CALL_OUTCOMES = @json(\App\Http\Controllers\TouchPointController::CALL_OUTCOMES);
const TP_SUB_DAYS  = @json(\App\Http\Controllers\TouchPointController::SUB_DAYS);
const TP_CSRF      = document.querySelector('meta[name="csrf-token"]')?.content || '';

// ── Utilities ──────────────────────────────────────────────────────
function tpEsc(s){ const d=document.createElement('div'); d.textContent=s||''; return d.innerHTML; }
function tpToast(msg, type='success'){
    const d = document.createElement('div');
    d.className = 'tp-toast tp-toast-' + type;
    d.textContent = msg;
    document.getElementById('tpToasts').appendChild(d);
    setTimeout(() => d.remove(), 3200);
}
function tpOpenModal(id)  { document.getElementById(id).classList.add('active'); }
function tpCloseModal(id) { document.getElementById(id).classList.remove('active'); }
document.querySelectorAll('.tp-modal-overlay').forEach(m =>
    m.addEventListener('click', e => { if(e.target===e.currentTarget) e.target.classList.remove('active'); })
);

async function tpGet(path, params={}){
    const qs = new URLSearchParams(params).toString();
    const r = await fetch(path + (qs ? '?'+qs : ''), {
        headers: { 'Accept':'application/json', 'X-CSRF-TOKEN': TP_CSRF }
    });
    return r.json();
}
async function tpPost(path, body={}){
    const r = await fetch(path, {
        method:'POST',
        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': TP_CSRF },
        body: JSON.stringify(body)
    });
    return r.json();
}

// ── Tabs ───────────────────────────────────────────────────────────
document.querySelectorAll('.tp-tab').forEach(tab => {
    tab.addEventListener('click', () => {
        document.querySelectorAll('.tp-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tp-panel').forEach(p => p.classList.remove('active'));
        tab.classList.add('active');
        document.getElementById('panel-' + tab.dataset.tab).classList.add('active');
        const t = tab.dataset.tab;
        if (t === 'dashboard')   loadDashboard();
        if (t === 'customers')   loadCustomers();
        if (t === 'touchpoints') loadTouchpoints();
        if (t === 'reports')     loadReports('week');
    });
});

// ── Helper functions ───────────────────────────────────────────────
function healthDot(h){ return `<span class="tp-health tp-health-${h||'unknown'}"></span>`; }
function healthLabel(h){ return {healthy:'Healthy',at_risk:'At Risk',churning:'Critical',unknown:'Unknown'}[h]||'Unknown'; }
function daysUntil(d){ return Math.ceil((new Date(d) - new Date(new Date().toDateString())) / 86400000); }
function fmtDate(d){ if(!d) return '-'; return new Date(d+'T00:00:00').toLocaleDateString('en-IN',{day:'numeric',month:'short',year:'numeric'}); }
function subLabel(s){ return {'1_month':'1 Month','3_month':'3 Months','1_year':'1 Year','free_trial':'Free Trial','extended_trial':'Extended Trial'}[s]||s; }
const TP_ONBOARDING_STAGES = ['welcome_call','setup_check','feature_walkthrough','usage_checkin','conversion_nudge','extended_checkin','final_conversion'];
function stageIcon(s){ return TP_ONBOARDING_STAGES.includes(s)?'🚀':s==='usage_check'?'🔍':'💰'; }
function stageLabel(s){ return TP_STAGES[s]||s; }

// ═══════════ DASHBOARD ═══════════════════════════════════════════
async function loadDashboard(){
    const d = await tpGet('/touchpoint/api/dashboard');
    if(!d.ok) return;
    document.getElementById('dashStats').innerHTML = `
        <div class="tp-stat"><div class="tp-stat-label">🔴 Overdue</div><div class="tp-stat-value red">${d.overdue}</div></div>
        <div class="tp-stat"><div class="tp-stat-label">📞 Today</div><div class="tp-stat-value blue">${d.today}</div></div>
        <div class="tp-stat"><div class="tp-stat-label">📅 Next 7 Days</div><div class="tp-stat-value amber">${d.upcoming}</div></div>
        <div class="tp-stat"><div class="tp-stat-label">✅ This Week</div><div class="tp-stat-value green">${d.completed_week}</div></div>
        <div class="tp-stat" style="border-left:3px solid #0d9488"><div class="tp-stat-label">🆕 Active Trials</div><div class="tp-stat-value teal">${d.active_trials||0}</div></div>
    `;

    // Onboarding pipeline
    const onbPipe = d.onboarding_pipeline||[];
    if(onbPipe.length > 0 || d.active_trials > 0){
        document.getElementById('dashOnboardingCard').style.display = '';
        document.getElementById('dashTrialConvBadge').textContent = 'Conv: '+(d.trial_conv_rate||0)+'%';
        document.getElementById('dashOnboarding').innerHTML = onbPipe.map(s => {
            const total = s.pending + s.done;
            const pct   = total > 0 ? Math.round(s.done/total*100) : 0;
            return `<div class="tp-metric"><span class="tp-metric-label">${stageIcon(s.stage)} ${stageLabel(s.stage)}</span><div class="tp-progress"><div class="tp-progress-fill" style="width:${pct}%;background:#0d9488"></div></div><span class="tp-metric-value" style="width:60px">${s.done}/${total}</span></div>`;
        }).join('') || '<p style="color:#64748b;font-size:.82rem">No active onboarding touchpoints</p>';
    } else {
        document.getElementById('dashOnboardingCard').style.display = 'none';
    }

    // Health
    const h = d.health;
    const total = (h.healthy + h.at_risk + h.critical + h.unknown) || 1;
    document.getElementById('dashHealth').innerHTML = ['healthy','at_risk','critical','unknown'].map(k => {
        const v = h[k]||0;
        const pct = Math.round(v/total*100);
        const colors = {healthy:'#10b981',at_risk:'#f59e0b',critical:'#ef4444',unknown:'#d1d5db'};
        return `<div class="tp-metric"><span class="tp-metric-label">${healthDot(k)} ${healthLabel(k)}</span><div class="tp-progress"><div class="tp-progress-fill" style="width:${pct}%;background:${colors[k]}"></div></div><span class="tp-metric-value">${v}</span></div>`;
    }).join('');

    // Pipeline
    document.getElementById('dashPipeline').innerHTML = d.pipeline.map(w => `
        <div class="tp-metric">
            <span class="tp-metric-label">${w.week}<br><small style="color:#64748b">${w.range}</small></span>
            <div style="flex:1"></div>
            <span class="tp-badge ${w.count>5?'tp-badge-amber':'tp-badge-slate'}">${w.count} expiring</span>
        </div>
    `).join('');

    // Upcoming list
    const list = d.upcoming_list;
    if(!list.length){
        document.getElementById('dashUpcoming').innerHTML = '<div class="tp-empty"><div class="tp-empty-icon">🎉</div><p>No overdue touchpoints!</p></div>';
    } else {
        document.getElementById('dashUpcoming').innerHTML =
            '<div class="tp-table-wrap"><table class="tp-table"><thead><tr><th>Customer</th><th>Stage</th><th>Due</th><th>Assigned</th><th></th></tr></thead><tbody>' +
            list.map(t => {
                const days = daysUntil(t.due_date);
                const badge = days<0 ? `<span class="tp-badge tp-badge-red">${Math.abs(days)}d overdue</span>` :
                              days===0 ? '<span class="tp-badge tp-badge-blue">Today</span>' :
                              `<span class="tp-badge tp-badge-slate">${days}d</span>`;
                return `<tr>
                    <td><strong>${tpEsc(t.customer_name)}</strong>${t.company?'<br><small style="color:#64748b">'+tpEsc(t.company)+'</small>':''}</td>
                    <td>${stageIcon(t.stage)} ${stageLabel(t.stage)}</td>
                    <td>${fmtDate(t.due_date)} ${badge}</td>
                    <td>${t.assigned_name ? tpEsc(t.assigned_name) : '<span class="tp-badge tp-badge-amber">Unassigned</span>'}</td>
                    <td><button class="tp-btn tp-btn-primary tp-btn-sm" onclick="openTpAction(${t.id})">📞</button></td>
                </tr>`;
            }).join('') + '</tbody></table></div>';
    }
}

// ═══════════ CUSTOMERS ════════════════════════════════════════════
let custTimer;
function debounceCust(){ clearTimeout(custTimer); custTimer = setTimeout(loadCustomers, 300); }

async function loadCustomers(){
    const d = await tpGet('/touchpoint/api/customers', {
        search: document.getElementById('custSearch').value,
        status: document.getElementById('custStatus').value,
        subscription: document.getElementById('custSub').value
    });
    if(!d.ok) return;
    const custs = d.customers;
    if(!custs.length){
        document.getElementById('custTable').innerHTML = '<div class="tp-empty"><div class="tp-empty-icon">👥</div><p>No customers found</p></div>';
        return;
    }
    document.getElementById('custTable').innerHTML =
        '<div class="tp-table-wrap"><table class="tp-table"><thead><tr><th>Customer</th><th>Company</th><th>Phone</th><th>Plan</th><th>Expiry</th><th>Health</th><th>Status</th><th></th></tr></thead><tbody>' +
        custs.map(c => {
            const days = daysUntil(c.expiry_date);
            const exBadge = days<=0 ? '<span class="tp-badge tp-badge-red">Expired</span>' : days<=7 ? `<span class="tp-badge tp-badge-amber">${days}d</span>` : '';
            const sBadge  = c.status==='active'?'tp-badge-green':c.status==='churned'?'tp-badge-red':'tp-badge-blue';
            return `<tr>
                <td><strong>${tpEsc(c.name)}</strong></td>
                <td>${tpEsc(c.company||'-')}</td>
                <td>${tpEsc(c.phone)}</td>
                <td>${subLabel(c.subscription_type)}</td>
                <td>${fmtDate(c.expiry_date)} ${exBadge}</td>
                <td>${healthDot(c.health)} ${healthLabel(c.health)}</td>
                <td><span class="tp-badge ${sBadge}">${c.status}</span></td>
                <td><button class="tp-btn tp-btn-ghost tp-btn-sm" onclick="editCust(${c.id})" title="Edit customer" style="padding:5px 8px"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg></button></td>
            </tr>`;
        }).join('') + '</tbody></table></div>';
}

function openCustModal(data){
    document.getElementById('custModalTitle').textContent = data ? 'Edit Customer' : 'Add Customer';
    document.getElementById('cm_id').value     = data?.id || '';
    document.getElementById('cm_name').value   = data?.name || '';
    document.getElementById('cm_company').value= data?.company || '';
    document.getElementById('cm_phone').value  = data?.phone || '';
    document.getElementById('cm_email').value  = data?.email || '';
    document.getElementById('cm_sub').value    = data?.subscription_type || '';
    document.getElementById('cm_start').value  = data?.start_date || new Date().toISOString().split('T')[0];
    document.getElementById('cm_expiry').value = data?.expiry_date || '';
    document.getElementById('cm_notes').value  = data?.notes || '';
    document.getElementById('cm_extra_row').style.display = data ? 'flex' : 'none';
    document.getElementById('cm_delete_btn').style.display = data ? 'inline-flex' : 'none';
    if(data){
        document.getElementById('cm_status').value = data.status||'active';
        document.getElementById('cm_health').value = data.health||'unknown';
    }
    tpOpenModal('custModal');
}
async function editCust(id){
    const d = await tpGet('/touchpoint/api/customers', {});
    const c = d.customers.find(x => x.id==id);
    if(c) openCustModal(c);
}
async function saveCust(){
    const data = {
        id:                document.getElementById('cm_id').value || null,
        name:              document.getElementById('cm_name').value.trim(),
        company:           document.getElementById('cm_company').value.trim(),
        phone:             document.getElementById('cm_phone').value.trim(),
        email:             document.getElementById('cm_email').value.trim(),
        subscription_type: document.getElementById('cm_sub').value,
        start_date:        document.getElementById('cm_start').value,
        expiry_date:       document.getElementById('cm_expiry').value,
        status:            document.getElementById('cm_status').value || 'active',
        health:            document.getElementById('cm_health').value || 'unknown',
        notes:             document.getElementById('cm_notes').value.trim()
    };
    if(!data.name||!data.phone||!data.subscription_type||!data.start_date||!data.expiry_date){
        tpToast('Fill all required fields','error'); return;
    }
    const r = await tpPost('/touchpoint/api/customer-save', data);
    if(r.ok){ tpToast(r.msg); tpCloseModal('custModal'); loadCustomers(); }
    else tpToast(r.error||'Error','error');
}
async function deleteCust(){
    if(!confirm('Delete this customer and all touchpoints?')) return;
    const id = document.getElementById('cm_id').value;
    const r = await tpPost('/touchpoint/api/customer-delete', {id});
    if(r.ok){ tpToast('Deleted'); tpCloseModal('custModal'); loadCustomers(); }
}
function calcExpiry(){
    const sub   = document.getElementById('cm_sub').value;
    const start = document.getElementById('cm_start').value;
    if(sub && start && TP_SUB_DAYS[sub]){
        const d = new Date(start);
        d.setDate(d.getDate() + TP_SUB_DAYS[sub]);
        document.getElementById('cm_expiry').value = d.toISOString().split('T')[0];
    }
}

// ═══════════ TOUCHPOINTS ══════════════════════════════════════════
async function loadTouchpoints(){
    const params = {
        stage:     document.getElementById('tpStage').value,
        tp_status: document.getElementById('tpStatus').value,
        date_filter: document.getElementById('tpDate').value
    };
    if(TP_IS_ADMIN && document.getElementById('tpAssigned'))
        params.assigned = document.getElementById('tpAssigned').value;

    const d = await tpGet('/touchpoint/api/touchpoints', params);
    if(!d.ok) return;
    const tps = d.touchpoints;
    if(!tps.length){
        document.getElementById('tpTable').innerHTML = '<div class="tp-empty"><div class="tp-empty-icon">✅</div><p>No touchpoints found</p></div>';
        return;
    }
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('tpTable').innerHTML =
        '<div class="tp-table-wrap"><table class="tp-table"><thead><tr>' +
        (TP_IS_ADMIN ? '<th style="width:30px"></th>' : '') +
        '<th>Customer</th><th>Phone</th><th>Stage</th><th>Due</th><th>Assigned</th><th>Status</th><th></th></tr></thead><tbody>' +
        tps.map(t => {
            const days = daysUntil(t.due_date);
            const isOverdue = t.due_date < today && t.status === 'pending';
            const dueBadge  = isOverdue ? `<span class="tp-badge tp-badge-red">${Math.abs(days)}d late</span>` :
                              t.due_date===today ? '<span class="tp-badge tp-badge-blue">Today</span>' : '';
            const statusBadge = t.status==='completed'?'<span class="tp-badge tp-badge-green">Done</span>':'<span class="tp-badge tp-badge-slate">Pending</span>';
            const chk = TP_IS_ADMIN && t.status==='pending' ? `<td><input type="checkbox" class="tp-check" value="${t.id}"></td>` : (TP_IS_ADMIN?'<td></td>':'');
            return `<tr style="${isOverdue?'background:#fff7f7':''}">
                ${chk}
                <td><strong>${tpEsc(t.customer_name)}</strong>${t.company?'<br><small style="color:#64748b">'+tpEsc(t.company)+'</small>':''}</td>
                <td>${tpEsc(t.phone)}</td>
                <td>${stageIcon(t.stage)} ${stageLabel(t.stage)}</td>
                <td>${fmtDate(t.due_date)} ${dueBadge}</td>
                <td>${t.assigned_name ? tpEsc(t.assigned_name) : '<span class="tp-badge tp-badge-amber">Unassigned</span>'}</td>
                <td>${statusBadge}${t.outcome?'<br><small>'+tpEsc(TP_OUTCOMES[t.stage]?.[t.outcome]||t.outcome)+'</small>':''}</td>
                <td><button class="tp-btn ${t.status==='pending'?'tp-btn-primary':'tp-btn-ghost'} tp-btn-sm" onclick="openTpAction(${t.id})">${t.status==='pending'?'📞':'View'}</button></td>
            </tr>`;
        }).join('') + '</tbody></table></div>';
}

function tpToggleAll(cb){ document.querySelectorAll('.tp-check').forEach(c => c.checked = cb.checked); }
async function tpBulkAssign(){
    const ids = [...document.querySelectorAll('.tp-check:checked')].map(c => c.value);
    const to  = document.getElementById('tpBulkAssign').value;
    if(!ids.length || !to){ tpToast('Select tasks and assignee','error'); return; }
    const r = await tpPost('/touchpoint/api/tp-bulk-assign', {ids, assigned_to: to});
    if(r.ok){ tpToast(`${r.count} assigned`); loadTouchpoints(); }
}

async function openTpAction(id){
    const d = await tpGet('/touchpoint/api/touchpoints', {});
    const t = d.touchpoints.find(x => x.id == id);
    if(!t){ tpToast('Not found','error'); return; }

    const logs = await tpGet('/touchpoint/api/call-logs', {touchpoint_id: id});

    document.getElementById('tpActionTitle').textContent = stageIcon(t.stage)+' '+stageLabel(t.stage)+' — '+t.customer_name;
    let html = `<div style="background:#f8fafc;padding:12px 16px;border-radius:10px;margin-bottom:16px;font-size:.82rem">
        <div class="tp-cust-info-grid" style="display:grid;grid-template-columns:1fr 1fr;gap:6px">
            <div><strong>Company:</strong> ${tpEsc(t.company||'N/A')}</div>
            <div><strong>Phone:</strong> <a href="tel:${t.phone}" style="color:#14b8a6">${tpEsc(t.phone)}</a></div>
            <div><strong>Email:</strong> ${tpEsc(t.email||'N/A')}</div>
            <div><strong>Expiry:</strong> ${fmtDate(t.expiry_date)} (${daysUntil(t.expiry_date)}d)</div>
        </div>
    </div>`;

    if(t.status === 'pending'){
        html += `<h4 style="font-size:.85rem;font-weight:600;margin-bottom:10px">📞 Log Call</h4>
        <div class="tp-form-row" style="display:flex;gap:12px;margin-bottom:12px">
            <div style="flex:1;display:flex;flex-direction:column;gap:4px">
                <label style="font-size:.78rem;font-weight:500;color:#374151">Called By</label>
                <select id="ta_caller" style="padding:7px 10px;border:1px solid #e2e8f0;border-radius:8px;font-size:.82rem;font-family:inherit">
                    ${TP_STAFF_LIST.map(s => `<option value="${s.id}" ${s.id==TP_STAFF_ID?'selected':''}>${tpEsc(s.name)}</option>`).join('')}
                </select>
            </div>
            <div style="flex:1;display:flex;flex-direction:column;gap:4px">
                <label style="font-size:.78rem;font-weight:500;color:#374151">Outcome</label>
                <select id="ta_call_outcome" style="padding:7px 10px;border:1px solid #e2e8f0;border-radius:8px;font-size:.82rem;font-family:inherit">
                    ${Object.entries(TP_CALL_OUTCOMES).map(([k,v]) => `<option value="${k}">${v}</option>`).join('')}
                </select>
            </div>
        </div>
        <div style="display:flex;flex-direction:column;gap:4px;margin-bottom:10px">
            <label style="font-size:.78rem;font-weight:500;color:#374151">Notes</label>
            <textarea id="ta_call_notes" rows="2" placeholder="Call notes..." style="padding:7px 10px;border:1px solid #e2e8f0;border-radius:8px;font-size:.82rem;font-family:inherit;resize:vertical"></textarea>
        </div>
        <div style="display:flex;flex-direction:column;gap:4px;margin-bottom:14px">
            <label style="font-size:.78rem;font-weight:500;color:#374151">Follow-up Date</label>
            <input type="date" id="ta_followup" style="padding:7px 10px;border:1px solid #e2e8f0;border-radius:8px;font-size:.82rem;font-family:inherit">
        </div>
        <button class="tp-btn tp-btn-primary" style="width:100%;margin-bottom:20px" onclick="logCall(${t.id})">Log Call</button>
        <hr style="border:none;border-top:1px solid #e2e8f0;margin:16px 0">
        <h4 style="font-size:.85rem;font-weight:600;margin-bottom:10px">✅ Complete Touchpoint</h4>`;

        if(TP_IS_ADMIN){
            html += `<div style="display:flex;flex-direction:column;gap:4px;margin-bottom:10px">
                <label style="font-size:.78rem;font-weight:500;color:#374151">Assign To</label>
                <select id="ta_assign" onchange="assignTp(${t.id},this.value)" style="padding:7px 10px;border:1px solid #e2e8f0;border-radius:8px;font-size:.82rem;font-family:inherit">
                    <option value="">Unassigned</option>
                    ${TP_STAFF_LIST.map(s => `<option value="${s.id}" ${t.assigned_to==s.id?'selected':''}>${tpEsc(s.name)}</option>`).join('')}
                </select>
            </div>`;
        }

        html += `<div style="display:flex;flex-direction:column;gap:4px;margin-bottom:10px">
            <label style="font-size:.78rem;font-weight:500;color:#374151">Final Outcome *</label>
            <select id="ta_outcome" style="padding:7px 10px;border:1px solid #e2e8f0;border-radius:8px;font-size:.82rem;font-family:inherit">
                <option value="">Select outcome...</option>
                ${Object.entries(TP_OUTCOMES[t.stage]||{}).map(([k,v]) => `<option value="${k}">${v}</option>`).join('')}
            </select>
        </div>
        <div style="display:flex;flex-direction:column;gap:4px;margin-bottom:14px">
            <label style="font-size:.78rem;font-weight:500;color:#374151">Notes</label>
            <textarea id="ta_outcome_notes" rows="2" style="padding:7px 10px;border:1px solid #e2e8f0;border-radius:8px;font-size:.82rem;font-family:inherit;resize:vertical"></textarea>
        </div>
        <button class="tp-btn tp-btn-green" style="width:100%;padding:10px" onclick="completeTp(${t.id})">Mark Complete ✅</button>`;
    } else {
        html += `<div style="background:#d1fae5;padding:12px 16px;border-radius:10px;font-size:.82rem;margin-bottom:12px">
            <strong>✅ Completed:</strong> ${tpEsc(TP_OUTCOMES[t.stage]?.[t.outcome]||t.outcome)}${t.outcome_notes?'<br>'+tpEsc(t.outcome_notes):''}
        </div>`;
    }

    // Call logs
    if(logs.ok && logs.logs.length){
        html += `<div style="margin-top:16px"><h4 style="font-size:.85rem;font-weight:600;margin-bottom:8px">📋 Call History</h4>`;
        logs.logs.forEach(l => {
            html += `<div class="tp-call-log-item">
                <div style="display:flex;justify-content:space-between">
                    <div><strong>${tpEsc(l.caller_name)}</strong> — ${TP_CALL_OUTCOMES[l.outcome]||l.outcome}${l.notes?'<br><small style="color:#64748b">'+tpEsc(l.notes)+'</small>':''}</div>
                    <div style="text-align:right;color:#64748b;font-size:.75rem">
                        ${new Date(l.call_time).toLocaleDateString('en-IN',{day:'numeric',month:'short',hour:'2-digit',minute:'2-digit'})}
                        ${l.follow_up_date?'<br>Follow-up: '+fmtDate(l.follow_up_date):''}
                    </div>
                </div>
            </div>`;
        });
        html += '</div>';
    }

    document.getElementById('tpActionBody').innerHTML = html;
    tpOpenModal('tpActionModal');
}

async function logCall(tpId){
    const r = await tpPost('/touchpoint/api/log-call', {
        touchpoint_id: tpId,
        called_by:     document.getElementById('ta_caller').value,
        outcome:       document.getElementById('ta_call_outcome').value,
        notes:         document.getElementById('ta_call_notes').value,
        follow_up_date: document.getElementById('ta_followup').value || null
    });
    if(r.ok){ tpToast('Call logged'); openTpAction(tpId); }
    else tpToast(r.error||'Error','error');
}
async function assignTp(id, to){
    await tpPost('/touchpoint/api/tp-assign', {id, assigned_to: to||null});
    tpToast('Assigned');
}
async function completeTp(id){
    const outcome = document.getElementById('ta_outcome').value;
    if(!outcome){ tpToast('Select outcome','error'); return; }
    const r = await tpPost('/touchpoint/api/tp-complete', {id, outcome, notes: document.getElementById('ta_outcome_notes').value});
    if(r.ok){
        if(r.prompt_plan){
            tpCloseModal('tpActionModal');
            const custTp = await tpGet('/touchpoint/api/touchpoints', {});
            const tp = custTp.touchpoints.find(t => t.id == id);
            if(tp) showPlanPicker(tp.customer_id, tp.customer_name);
        } else if(r.extended){
            tpToast('Trial extended to 14 days! 🔄');
            tpCloseModal('tpActionModal');
        } else {
            tpToast('Completed! 🎉');
            tpCloseModal('tpActionModal');
        }
        loadTouchpoints(); loadDashboard();
    } else tpToast(r.error||'Error','error');
}
function showPlanPicker(custId, custName){
    document.getElementById('tpActionTitle').textContent = '🎉 Convert '+tpEsc(custName)+' to Paid Plan';
    document.getElementById('tpActionBody').innerHTML = `
        <div style="text-align:center;padding:20px">
            <div style="font-size:2.5rem;margin-bottom:12px">🎉</div>
            <p style="font-size:.9rem;font-weight:600;margin-bottom:16px">Which plan did they choose?</p>
            <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap">
                <button class="tp-btn tp-btn-primary" onclick="convertTrial(${custId},'1_month')" style="min-width:120px">1 Month</button>
                <button class="tp-btn tp-btn-primary" onclick="convertTrial(${custId},'3_month')" style="min-width:120px">3 Months</button>
                <button class="tp-btn tp-btn-primary" onclick="convertTrial(${custId},'1_year')"  style="min-width:120px">1 Year</button>
            </div>
            <button class="tp-btn tp-btn-ghost tp-btn-sm" onclick="tpCloseModal('tpActionModal')" style="margin-top:16px">Skip for now</button>
        </div>`;
    tpOpenModal('tpActionModal');
}
async function convertTrial(custId, plan){
    const r = await tpPost('/touchpoint/api/convert-trial', {id: custId, plan});
    if(r.ok){ tpToast(r.msg+' 🎉'); tpCloseModal('tpActionModal'); loadTouchpoints(); loadDashboard(); loadCustomers(); }
    else tpToast(r.error||'Error','error');
}

// ═══════════ REPORTS ══════════════════════════════════════════════
async function loadReports(period='week'){
    document.getElementById('repWeek').className  = 'tp-btn tp-btn-sm ' + (period==='week'?'tp-btn-primary':'tp-btn-secondary');
    document.getElementById('repMonth').className = 'tp-btn tp-btn-sm ' + (period==='month'?'tp-btn-primary':'tp-btn-secondary');
    document.getElementById('repAll').className   = 'tp-btn tp-btn-sm ' + (period==='all'?'tp-btn-primary':'tp-btn-secondary');

    const d = await tpGet('/touchpoint/api/reports', {period});
    if(!d.ok) return;

    const retColor = d.retention>=70?'#10b981':d.retention>=50?'#f59e0b':'#ef4444';
    let html = `<div class="tp-stats">
        <div class="tp-stat"><div class="tp-stat-label">👥 Active</div><div class="tp-stat-value">${d.active}</div></div>
        <div class="tp-stat"><div class="tp-stat-label">✅ Tasks Done</div><div class="tp-stat-value green">${d.completed_tasks}</div></div>
        <div class="tp-stat"><div class="tp-stat-label">⚠️ Overdue</div><div class="tp-stat-value red">${d.overdue_tasks}</div></div>
        <div class="tp-stat"><div class="tp-stat-label">📞 Calls</div><div class="tp-stat-value blue">${d.total_calls}</div></div>
    </div>`;

    // Customer status
    html += `<div class="tp-grid"><div class="tp-card"><div class="tp-card-header"><h3>📊 Customer Status</h3></div><div class="tp-card-body padded">`;
    ['active','renewed','churned'].forEach(s => {
        const v = d[s]; const pct = d.total ? Math.round(v/d.total*100) : 0;
        const c = {active:'#10b981',renewed:'#0ea5e9',churned:'#ef4444'}[s];
        html += `<div class="tp-metric"><span class="tp-metric-label">${s.charAt(0).toUpperCase()+s.slice(1)}</span><div class="tp-progress"><div class="tp-progress-fill" style="width:${pct}%;background:${c}"></div></div><span class="tp-metric-value">${v}</span></div>`;
    });
    html += `<div style="text-align:center;margin-top:16px;padding:12px;background:#f8fafc;border-radius:8px">
        <div style="font-size:1.8rem;font-weight:700;color:${retColor}">${d.retention}%</div>
        <div style="color:#64748b;font-size:.8rem">Retention Rate</div>
    </div></div></div>`;

    // Usage outcomes
    html += `<div class="tp-card"><div class="tp-card-header"><h3>🔍 Usage Outcomes</h3></div><div class="tp-card-body padded">`;
    const uTotal = Object.values(d.usage_outcomes).reduce((a,b)=>a+b,0) || 1;
    Object.entries(TP_OUTCOMES.usage_check).forEach(([k,label]) => {
        const v = d.usage_outcomes[k]||0; const pct = Math.round(v/uTotal*100);
        const c = k==='using_well'?'#10b981':k==='wants_cancel'?'#ef4444':'#f59e0b';
        html += `<div class="tp-metric"><span class="tp-metric-label">${label}</span><div class="tp-progress"><div class="tp-progress-fill" style="width:${pct}%;background:${c}"></div></div><span class="tp-metric-value">${v}</span></div>`;
    });
    html += `</div></div></div>`;

    // Onboarding metrics
    if(d.trial_signups > 0){
        html += `<div class="tp-card" style="border-left:3px solid #0d9488"><div class="tp-card-header"><h3>🚀 Onboarding Metrics</h3></div><div class="tp-card-body padded">
            <div class="tp-stats" style="margin-bottom:16px">
                <div class="tp-stat"><div class="tp-stat-label">Trial Signups</div><div class="tp-stat-value teal">${d.trial_signups}</div></div>
                <div class="tp-stat"><div class="tp-stat-label">Converted</div><div class="tp-stat-value green">${d.trial_converted}</div></div>
                <div class="tp-stat"><div class="tp-stat-label">Conv. Rate</div><div class="tp-stat-value ${d.trial_conv_rate>=50?'green':'amber'}">${d.trial_conv_rate}%</div></div>
                <div class="tp-stat"><div class="tp-stat-label">Avg Days</div><div class="tp-stat-value blue">${d.avg_days_to_convert||'—'}</div></div>
            </div>
            <h4 style="font-size:.82rem;font-weight:600;margin-bottom:10px">Stage Funnel</h4>`;
        const stageNames = ['welcome_call','setup_check','feature_walkthrough','usage_checkin','conversion_nudge'];
        const dropOff = d.stage_drop_off||{};
        const maxDrop = Math.max(...stageNames.map(s=>dropOff[s]||0), 1);
        stageNames.forEach(s => {
            const v = dropOff[s]||0; const pct = Math.round(v/maxDrop*100);
            html += `<div class="tp-metric"><span class="tp-metric-label">${stageLabel(s)}</span><div class="tp-progress"><div class="tp-progress-fill" style="width:${pct}%;background:#0d9488"></div></div><span class="tp-metric-value">${v}</span></div>`;
        });
        html += `</div></div>`;
    }

    // Payment outcomes
    html += `<div class="tp-grid"><div class="tp-card"><div class="tp-card-header"><h3>💰 Payment Outcomes</h3></div><div class="tp-card-body padded">`;
    const pTotal = Object.values(d.payment_outcomes).reduce((a,b)=>a+b,0) || 1;
    Object.entries(TP_OUTCOMES.payment).forEach(([k,label]) => {
        const v = d.payment_outcomes[k]||0; const pct = Math.round(v/pTotal*100);
        const c = k==='paid'?'#10b981':k==='churned'?'#ef4444':'#f59e0b';
        html += `<div class="tp-metric"><span class="tp-metric-label">${label}</span><div class="tp-progress"><div class="tp-progress-fill" style="width:${pct}%;background:${c}"></div></div><span class="tp-metric-value">${v}</span></div>`;
    });
    html += `</div></div>`;

    // Team performance
    if(d.team && d.team.length){
        html += `<div class="tp-card"><div class="tp-card-header"><h3>👥 Team Performance</h3></div><div class="tp-card-body">
            <div class="tp-table-wrap"><table class="tp-table"><thead><tr><th>Name</th><th>Role</th><th>Completed</th><th>Pending</th><th>Calls</th></tr></thead><tbody>`;
        d.team.forEach(m => {
            html += `<tr><td><strong>${tpEsc(m.name)}</strong></td><td>${tpEsc(m.role)}</td><td><span class="tp-badge tp-badge-green">${m.completed}</span></td><td><span class="tp-badge tp-badge-amber">${m.pending}</span></td><td><span class="tp-badge tp-badge-blue">${m.calls}</span></td></tr>`;
        });
        html += `</tbody></table></div></div></div>`;
    }
    html += `</div>`;

    document.getElementById('reportsContent').innerHTML = html;
}

// ── Bootstrap ──────────────────────────────────────────────────────
loadDashboard();
</script>

</x-layouts.app>
