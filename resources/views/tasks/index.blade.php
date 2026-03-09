<x-layouts.app title="Tasks">
@push('styles')
{{-- DataTables CSS --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/jquery.dataTables.min.css">
<style>
    :root {
        --erp-primary:      #14b8a6;
        --erp-primary-dark: #0d9488;
        --erp-bg:           #ffffff;
        --erp-muted:        #f8fafc;
        --erp-border:       #e2e8f0;
        --erp-text:         #1e293b;
        --erp-text-muted:   #64748b;
        --erp-text-subtle:  #94a3b8;
        --erp-hover:        #f1f5f9;
        --erp-selected:     #f0fdfa;
        --erp-danger:       #dc2626;
        --erp-warning:      #d97706;
        --erp-success:      #16a34a;
        --erp-info:         #2563eb;
        --filter-w:         260px;
    }

    /* ── Layout ── */
    .erp-wrap {
        display: flex;
        min-height: 100vh;
        background: var(--erp-bg);
    }

    /* ── Filter Sidebar ── */
    .erp-filters {
        width: var(--filter-w);
        min-width: var(--filter-w);
        background: var(--erp-bg);
        border-right: 1px solid var(--erp-border);
        display: flex;
        flex-direction: column;
        overflow-y: auto;
    }
    .erp-filters-head {
        padding: 14px 16px 10px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: var(--erp-text-muted);
        border-bottom: 1px solid var(--erp-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .erp-filters-clear {
        background: none;
        border: none;
        font-size: 12px;
        font-weight: 500;
        color: var(--erp-primary);
        cursor: pointer;
        padding: 0;
    }
    .erp-filters-clear:hover { text-decoration: underline; }

    .erp-filter-section {
        padding: 12px 16px;
        border-bottom: 1px solid var(--erp-border);
    }
    .erp-filter-title {
        font-size: 12px;
        font-weight: 600;
        color: var(--erp-text);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        cursor: pointer;
        user-select: none;
    }
    .erp-filter-title svg {
        width: 14px; height: 14px;
        stroke: var(--erp-text-subtle);
        fill: none; stroke-width: 2;
        stroke-linecap: round; stroke-linejoin: round;
        transition: transform 0.2s;
    }
    .erp-filter-section.collapsed .erp-filter-title svg { transform: rotate(-90deg); }
    .erp-filter-section.collapsed .erp-filter-options  { display: none; }

    .erp-filter-options {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }
    .erp-filter-opt {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: var(--erp-text);
        cursor: pointer;
        padding: 3px 0;
    }
    .erp-filter-opt:hover { color: var(--erp-primary); }
    .erp-filter-opt input[type="checkbox"] {
        width: 14px; height: 14px;
        accent-color: var(--erp-primary);
        cursor: pointer;
    }
    .erp-dot {
        width: 8px; height: 8px;
        border-radius: 50%;
        flex-shrink: 0;
        display: inline-block;
    }
    .dot-pending    { background: #fbbf24; }
    .dot-in_progress{ background: #3b82f6; }
    .dot-blocked    { background: #ef4444; }
    .dot-done       { background: #22c55e; }
    .dot-urgent     { background: #dc2626; }
    .dot-high       { background: #f97316; }
    .dot-normal     { background: #3b82f6; }
    .dot-low        { background: #94a3b8; }

    .erp-av-sm {
        width: 20px; height: 20px;
        border-radius: 50%;
        background: var(--erp-primary);
        color: white;
        font-size: 9px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    /* ── Main ── */
    .erp-main {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
    }

    /* Title bar */
    .erp-titlebar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid var(--erp-border);
        gap: 12px;
        flex-wrap: wrap;
    }
    .erp-title-left { display: flex; align-items: center; gap: 14px; }
    .erp-title {
        font-size: 20px;
        font-weight: 600;
        color: var(--erp-text);
        margin: 0;
    }
    .erp-count {
        font-size: 13px;
        color: var(--erp-text-muted);
    }
    .erp-view-tabs {
        display: flex;
        gap: 4px;
    }
    .erp-tab {
        padding: 6px 14px;
        font-size: 13px;
        font-weight: 500;
        border-radius: 6px;
        border: 1px solid var(--erp-border);
        background: var(--erp-bg);
        color: var(--erp-text-muted);
        cursor: pointer;
        transition: all 0.15s;
        font-family: inherit;
    }
    .erp-tab:hover { background: var(--erp-hover); }
    .erp-tab.active {
        background: var(--erp-primary);
        border-color: var(--erp-primary);
        color: white;
    }
    .erp-btn-add {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        font-size: 13px;
        font-weight: 600;
        border-radius: 7px;
        border: none;
        background: var(--erp-primary);
        color: white;
        cursor: pointer;
        font-family: inherit;
        transition: background 0.15s;
    }
    .erp-btn-add:hover { background: var(--erp-primary-dark); }
    .erp-btn-add svg { width: 14px; height: 14px; stroke: currentColor; fill: none; stroke-width: 2.5; }

    /* Toolbar */
    .erp-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 20px;
        border-bottom: 1px solid var(--erp-border);
        gap: 10px;
        flex-wrap: wrap;
    }
    .erp-toolbar-left, .erp-toolbar-right { display: flex; align-items: center; gap: 8px; }

    .erp-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        height: 30px;
        font-size: 13px;
        font-weight: 500;
        border-radius: 6px;
        border: 1px solid var(--erp-border);
        background: var(--erp-bg);
        color: var(--erp-text);
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s;
    }
    .erp-btn:hover { background: var(--erp-hover); }
    .erp-btn svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }
    .erp-btn-ghost { background: transparent; border-color: transparent; }
    .erp-btn-ghost:hover { background: var(--erp-hover); border-color: var(--erp-border); }

    .erp-mode-toggle {
        display: flex;
        background: var(--erp-muted);
        border: 1px solid var(--erp-border);
        border-radius: 6px;
        padding: 2px;
    }
    .erp-mode-btn {
        display: flex; align-items: center; justify-content: center;
        width: 28px; height: 26px;
        border: none; background: transparent;
        color: var(--erp-text-muted);
        border-radius: 4px;
        cursor: pointer; transition: all 0.15s;
    }
    .erp-mode-btn:hover { color: var(--erp-text); }
    .erp-mode-btn.active { background: white; color: var(--erp-text); box-shadow: 0 1px 2px rgba(0,0,0,0.08); }
    .erp-mode-btn svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

    .erp-search-wrap { position: relative; }
    .erp-search-wrap input {
        height: 30px;
        width: 220px;
        padding: 0 10px 0 30px;
        font-size: 13px;
        border: 1px solid var(--erp-border);
        border-radius: 6px;
        background: var(--erp-bg);
        color: var(--erp-text);
        font-family: inherit;
    }
    .erp-search-wrap input:focus { outline: none; border-color: var(--erp-primary); box-shadow: 0 0 0 3px rgba(20,184,166,.1); }
    .erp-search-wrap svg {
        position: absolute; left: 8px; top: 50%; transform: translateY(-50%);
        width: 13px; height: 13px; color: var(--erp-text-subtle);
        stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
    }

    .erp-sort-sel {
        height: 30px;
        padding: 0 10px;
        font-size: 13px;
        border: 1px solid var(--erp-border);
        border-radius: 6px;
        background: var(--erp-bg);
        color: var(--erp-text);
        cursor: pointer;
        font-family: inherit;
    }

    /* ── DataTable overrides ── */
    .erp-table-wrap { flex: 1; overflow: auto; }

    table.dataTable { width: 100% !important; border-collapse: collapse; font-size: 13px; font-family: 'Inter', -apple-system, sans-serif; }
    table.dataTable thead th {
        padding: 10px 12px;
        text-align: left;
        font-weight: 600;
        font-size: 12px;
        color: var(--erp-text-muted);
        border-bottom: 1px solid var(--erp-border);
        background: var(--erp-muted);
        white-space: nowrap;
        border-top: none;
    }
    table.dataTable thead th:first-child { padding-left: 20px; }
    table.dataTable thead th:last-child  { padding-right: 20px; }
    table.dataTable tbody td {
        padding: 9px 12px;
        border-bottom: 1px solid var(--erp-border);
        vertical-align: middle;
        color: var(--erp-text);
    }
    table.dataTable tbody td:first-child { padding-left: 20px; }
    table.dataTable tbody td:last-child  { padding-right: 20px; }
    table.dataTable tbody tr { cursor: pointer; transition: background 0.1s; }
    table.dataTable tbody tr:hover { background: var(--erp-hover); }
    table.dataTable tbody tr.selected { background: var(--erp-selected); }

    /* Hide default DT controls (we have our own toolbar) */
    .dataTables_wrapper .dataTables_filter,
    .dataTables_wrapper .dataTables_length { display: none; }
    .dataTables_wrapper .dataTables_info  { font-size: 13px; color: var(--erp-text-muted); padding: 10px 20px; }
    .dataTables_wrapper .dataTables_paginate {
        padding: 10px 20px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        min-width: 28px; height: 28px;
        padding: 0 8px;
        border: 1px solid var(--erp-border) !important;
        background: var(--erp-bg) !important;
        color: var(--erp-text) !important;
        border-radius: 4px;
        font-size: 13px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-family: inherit;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
        background: var(--erp-hover) !important;
        color: var(--erp-text) !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.current {
        background: var(--erp-primary) !important;
        border-color: var(--erp-primary) !important;
        color: white !important;
    }
    .dataTables_wrapper .dataTables_paginate .paginate_button.disabled { opacity: 0.4; cursor: not-allowed; }

    .dataTables_wrapper .dataTables_processing {
        background: rgba(255,255,255,0.9);
        border: 1px solid var(--erp-border);
        border-radius: 6px;
        font-size: 13px;
        color: var(--erp-text-muted);
        padding: 8px 16px;
    }

    /* Bottom pagination bar */
    .dt-bottom {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 20px;
        border-top: 1px solid var(--erp-border);
        background: var(--erp-bg);
    }

    /* ── Table cell components ── */
    .task-title { font-weight: 500; color: var(--erp-text); max-width: 280px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
    .status-chip, .priority-chip {
        display: inline-flex; align-items: center; gap: 5px;
        font-size: 12px; font-weight: 500;
    }
    .erp-tag {
        display: inline-flex;
        padding: 2px 8px;
        background: var(--erp-muted);
        border: 1px solid var(--erp-border);
        border-radius: 4px;
        font-size: 12px;
        color: var(--erp-text-muted);
        max-width: 140px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .due-overdue { color: var(--erp-danger); font-weight: 500; font-size: 12px; }
    .due-today   { color: var(--erp-warning); font-weight: 500; font-size: 12px; }
    .due-normal  { color: var(--erp-text-muted); font-size: 12px; }
    .erp-av { display: inline-flex; align-items: center; gap: 6px; font-size: 13px; }
    .erp-av-circle {
        width: 22px; height: 22px;
        border-radius: 50%;
        background: var(--erp-primary);
        color: white;
        font-size: 9px; font-weight: 700;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    /* Bulk actions */
    .erp-bulk-wrap { position: relative; }
    .erp-bulk-menu {
        position: absolute; top: calc(100% + 4px); left: 0;
        background: var(--erp-bg);
        border: 1px solid var(--erp-border);
        border-radius: 6px;
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.1);
        min-width: 160px;
        z-index: 100;
        display: none;
    }
    .erp-bulk-menu.open { display: block; }
    .erp-bulk-item {
        display: flex; align-items: center; gap: 8px;
        width: 100%; padding: 8px 12px;
        font-size: 13px; border: none; background: transparent;
        color: var(--erp-text); cursor: pointer; text-align: left;
        font-family: inherit;
    }
    .erp-bulk-item:hover { background: var(--erp-hover); }
    .erp-bulk-item svg { width: 13px; height: 13px; stroke: currentColor; fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

    /* ── Modals ── */
    .modal-backdrop {
        position: fixed; inset: 0;
        background: rgba(0,0,0,0.45);
        backdrop-filter: blur(3px);
        z-index: 500;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .modal-backdrop.open { display: flex; }
    .modal-box {
        background: white;
        border-radius: 10px;
        border: 1px solid var(--erp-border);
        box-shadow: 0 25px 50px -12px rgba(0,0,0,0.25);
        width: 100%;
        max-width: 540px;
        max-height: calc(100vh - 32px);
        display: flex;
        flex-direction: column;
    }
    .modal-box.modal-lg { max-width: 680px; }
    .modal-head {
        padding: 16px 20px;
        border-bottom: 1px solid var(--erp-border);
        display: flex; justify-content: space-between; align-items: center;
    }
    .modal-head h3 { font-size: 16px; font-weight: 600; color: var(--erp-text); margin: 0; }
    .modal-close {
        background: none; border: none; cursor: pointer;
        color: var(--erp-text-muted); padding: 2px;
        width: 28px; height: 28px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 4px; transition: all 0.15s;
        font-size: 20px; line-height: 1;
    }
    .modal-close:hover { background: var(--erp-hover); color: var(--erp-text); }
    .modal-body {
        padding: 20px;
        overflow-y: auto;
        flex: 1;
    }
    .modal-foot {
        padding: 12px 20px;
        border-top: 1px solid var(--erp-border);
        display: flex; justify-content: flex-end; gap: 8px;
    }

    /* Form */
    .f-group { margin-bottom: 14px; }
    .f-label { display: block; font-size: 12px; font-weight: 600; color: var(--erp-text); margin-bottom: 5px; }
    .f-input {
        width: 100%; padding: 7px 10px;
        font-size: 13px; font-family: inherit;
        border: 1px solid var(--erp-border);
        border-radius: 6px;
        background: var(--erp-bg);
        color: var(--erp-text);
        transition: border-color 0.15s;
    }
    .f-input:focus { outline: none; border-color: var(--erp-primary); box-shadow: 0 0 0 3px rgba(20,184,166,.1); }
    textarea.f-input { resize: vertical; min-height: 72px; }
    .f-row { display: flex; gap: 12px; }
    .f-row .f-group { flex: 1; }
    .f-err { font-size: 11px; color: var(--erp-danger); margin-top: 3px; display: none; }

    /* Chip selectors */
    .chip-group { display: flex; flex-wrap: wrap; gap: 6px; }
    .chip-opt input { display: none; }
    .chip-opt { cursor: pointer; }
    .chip-lbl {
        display: inline-block;
        padding: 4px 10px;
        background: var(--erp-muted);
        border: 1px solid var(--erp-border);
        border-radius: 99px;
        font-size: 12px;
        transition: all 0.15s;
        user-select: none;
        color: var(--erp-text);
    }
    .chip-opt input:checked + .chip-lbl {
        background: rgba(20,184,166,.12);
        border-color: var(--erp-primary);
        color: var(--erp-primary-dark);
        font-weight: 600;
    }

    /* Detail modal */
    .detail-meta { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 14px; }
    .detail-badge {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 3px 9px;
        border-radius: 20px;
        font-size: 12px; font-weight: 500;
    }
    .dbg-pending    { background: #fef3c7; color: #92400e; }
    .dbg-in_progress{ background: #eff6ff; color: #1d4ed8; }
    .dbg-blocked    { background: #fef2f2; color: #991b1b; }
    .dbg-done       { background: #f0fdf4; color: #166534; }
    .dbg-urgent     { background: #fef2f2; color: #991b1b; }
    .dbg-high       { background: #fff7ed; color: #9a3412; }
    .dbg-normal     { background: #eff6ff; color: #1d4ed8; }
    .dbg-low        { background: #f8fafc; color: #64748b; }

    .detail-desc {
        background: var(--erp-muted); border-radius: 6px;
        padding: 12px; font-size: 13px; line-height: 1.6;
        white-space: pre-wrap; margin-bottom: 14px;
        color: var(--erp-text);
    }
    .detail-fields { display: flex; gap: 12px; flex-wrap: wrap; margin-bottom: 14px; }
    .detail-fields > div { flex: 1; min-width: 120px; }

    .comment-item {
        padding: 10px; background: var(--erp-muted);
        border-radius: 6px; margin-bottom: 8px;
    }
    .comment-meta {
        display: flex; justify-content: space-between;
        margin-bottom: 4px;
    }
    .comment-author { font-size: 12px; font-weight: 600; }
    .comment-time   { font-size: 11px; color: var(--erp-text-muted); }
    .comment-text   { font-size: 13px; line-height: 1.5; }

    .history-item {
        display: flex; justify-content: space-between; align-items: center;
        padding: 7px 0; border-bottom: 1px solid var(--erp-border);
        font-size: 12px;
    }
    .history-item:last-child { border-bottom: none; }

    /* Toast */
    .toast-stack {
        position: fixed; top: 20px; right: 20px;
        z-index: 9999;
        display: flex; flex-direction: column; gap: 8px;
    }
    .toast {
        padding: 10px 16px;
        border-radius: 6px; font-size: 13px; font-weight: 500;
        box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        animation: tslide 0.25s ease;
    }
    .toast-ok  { background: #dcfce7; color: #166534; border: 1px solid #86efac; }
    .toast-err { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    @keyframes tslide { from { transform: translateX(110%); opacity: 0; } to { transform: none; opacity: 1; } }

    .erp-empty {
        text-align: center; padding: 60px 20px; color: var(--erp-text-muted);
    }
    .erp-empty svg {
        width: 44px; height: 44px; stroke: currentColor; fill: none;
        stroke-width: 1.5; stroke-linecap: round; stroke-linejoin: round;
        margin-bottom: 14px; opacity: 0.5;
    }
    .erp-empty p { font-size: 14px; margin: 0; }
    .erp-empty small { font-size: 12px; color: var(--erp-text-subtle); }

    .section-title {
        font-size: 13px; font-weight: 600;
        color: var(--erp-text); margin: 16px 0 10px;
    }

    /* ── Mobile Filter Button (hidden on desktop) ── */
    .mobile-filter-btn { display: none; }

    /* ── Filter Overlay ── */
    .filter-overlay {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.45);
        z-index: 299;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.25s ease;
    }
    .filter-overlay.open {
        opacity: 1;
        pointer-events: auto;
    }

    /* ── Responsive: Tablet (≤1024px) ── */
    @media (max-width: 1024px) {
        .erp-sort-sel { display: none; }
    }

    /* ── Responsive: Mobile (≤768px) ── */
    @media (max-width: 768px) {
        .erp-filters {
            position: fixed;
            left: 0;
            top: 0;
            height: 100%;
            z-index: 300;
            transform: translateX(-100%);
            transition: transform 0.25s ease;
            box-shadow: 4px 0 24px rgba(0,0,0,0.15);
        }
        .erp-filters.open { transform: translateX(0); }
        .mobile-filter-btn { display: inline-flex; }
        .erp-main { width: 100%; }
        .erp-titlebar { padding: 12px 14px; gap: 8px; }
        .erp-title { font-size: 17px; }
        .erp-toolbar { padding: 8px 14px; }
        .erp-toolbar-right { flex-wrap: wrap; width: 100%; }
        .erp-search-wrap { flex: 1; min-width: 0; }
        .erp-search-wrap input { width: 100%; }

        /* Hide project & due date columns */
        table.dataTable .col-project,
        table.dataTable .col-due,
        table.dataTable thead th.col-project,
        table.dataTable thead th.col-due { display: none; }

        /* Modals → bottom sheet */
        .modal-backdrop { padding: 0; align-items: flex-end; }
        .modal-box, .modal-box.modal-lg {
            max-width: 100%;
            border-radius: 12px 12px 0 0;
            max-height: 92vh;
        }

        #boardView { padding: 12px; }
    }

    /* ── Responsive: Small (≤560px) ── */
    @media (max-width: 560px) {
        .erp-titlebar { flex-direction: column; align-items: flex-start; gap: 10px; }
        .erp-view-tabs .erp-tab { padding: 5px 10px; font-size: 12px; }
        .erp-btn-add-text { display: none; }
        .erp-toolbar-right { gap: 6px; }
        .erp-mode-toggle { display: none; }
        .f-row { flex-direction: column; gap: 0; }
        .f-row .f-group { flex: none; }
        table.dataTable .col-priority,
        table.dataTable thead th.col-priority { display: none; }
    }

    /* ── Responsive: XSmall (≤380px) ── */
    @media (max-width: 380px) {
        .erp-titlebar { padding: 10px 12px; }
        .erp-toolbar { padding: 8px 12px; }
        table.dataTable tbody td { padding: 8px 8px; }
        table.dataTable thead th { padding: 8px 8px; }
    }
</style>
@endpush

<div class="filter-overlay" id="filterOverlay" onclick="closeFilters()"></div>
<div class="erp-wrap">

    {{-- ── Filter Sidebar ── --}}
    <aside class="erp-filters" id="filterSidebar">
        <div class="erp-filters-head">
            <span>Filters</span>
            <div style="display:flex;align-items:center;gap:8px;">
                <button class="erp-filters-clear" onclick="clearFilters()">Clear</button>
                <button class="erp-filters-clear" onclick="closeFilters()" style="font-size:18px;line-height:1;padding:0 2px;" title="Close">×</button>
            </div>
        </div>

        {{-- Status --}}
        <div class="erp-filter-section">
            <div class="erp-filter-title" onclick="toggleSection(this)">
                Status
                <svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
            </div>
            <div class="erp-filter-options">
                @foreach(['pending' => 'Pending', 'in_progress' => 'In Progress', 'blocked' => 'Blocked', 'done' => 'Done'] as $val => $label)
                    <label class="erp-filter-opt">
                        <input type="checkbox" value="{{ $val }}" data-filter="status" onchange="redraw()">
                        <span class="erp-dot dot-{{ $val }}"></span>
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Priority --}}
        <div class="erp-filter-section">
            <div class="erp-filter-title" onclick="toggleSection(this)">
                Priority
                <svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
            </div>
            <div class="erp-filter-options">
                @foreach(['urgent' => 'Urgent', 'high' => 'High', 'normal' => 'Normal', 'low' => 'Low'] as $val => $label)
                    <label class="erp-filter-opt">
                        <input type="checkbox" value="{{ $val }}" data-filter="priority" onchange="redraw()">
                        <span class="erp-dot dot-{{ $val }}"></span>
                        {{ $label }}
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Assignee (admin only) --}}
        @if($isAdmin)
        <div class="erp-filter-section">
            <div class="erp-filter-title" onclick="toggleSection(this)">
                Assignee
                <svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
            </div>
            <div class="erp-filter-options" style="max-height:180px;overflow-y:auto;">
                @foreach($staffList as $s)
                    <label class="erp-filter-opt">
                        <input type="checkbox" value="{{ $s->id }}" data-filter="assigned_to" onchange="redraw()">
                        <span class="erp-av-sm">{{ strtoupper(substr($s->name,0,1)) }}</span>
                        {{ $s->name }}
                    </label>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Project --}}
        @if($projects->isNotEmpty())
        <div class="erp-filter-section">
            <div class="erp-filter-title" onclick="toggleSection(this)">
                Project
                <svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
            </div>
            <div class="erp-filter-options" style="max-height:180px;overflow-y:auto;">
                @foreach($projects as $p)
                    <label class="erp-filter-opt">
                        <input type="checkbox" value="{{ $p->id }}" data-filter="project_id" onchange="redraw()">
                        {{ $p->name }}
                    </label>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Due Date --}}
        <div class="erp-filter-section">
            <div class="erp-filter-title" onclick="toggleSection(this)">
                Due Date
                <svg viewBox="0 0 24 24"><path d="m6 9 6 6 6-6"/></svg>
            </div>
            <div class="erp-filter-options">
                <div>
                    <label style="font-size:11px;color:var(--erp-text-muted);display:block;margin-bottom:3px;">From</label>
                    <input type="date" id="dateFrom" class="f-input" style="font-size:12px;" onchange="redraw()">
                </div>
                <div style="margin-top:8px;">
                    <label style="font-size:11px;color:var(--erp-text-muted);display:block;margin-bottom:3px;">To</label>
                    <input type="date" id="dateTo" class="f-input" style="font-size:12px;" onchange="redraw()">
                </div>
            </div>
        </div>
    </aside>

    {{-- ── Main ── --}}
    <main class="erp-main">

        {{-- Title bar --}}
        <div class="erp-titlebar">
            <div class="erp-title-left">
                <button class="mobile-filter-btn erp-btn erp-btn-ghost" onclick="openFilters()" title="Filters" style="gap:5px;">
                    <svg viewBox="0 0 24 24" style="width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/></svg>
                    <span>Filters</span>
                </button>
                <h1 class="erp-title" id="pageTitle">{{ request()->routeIs('my-tasks') ? 'My Tasks' : 'All Tasks' }}</h1>
                <span class="erp-count" id="taskCount"></span>
            </div>
            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                @if($isAdmin)
                <div class="erp-view-tabs">
                    <button class="erp-tab {{ request()->routeIs('my-tasks') ? 'active' : '' }}" id="tabMy" onclick="switchView('my')">My Tasks</button>
                    <button class="erp-tab {{ request()->routeIs('tasks') ? 'active' : '' }}" id="tabAll" onclick="switchView('all')">All Tasks</button>
                </div>
                @endif
                <button class="erp-btn-add" onclick="openCreate()">
                    <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    <span class="erp-btn-add-text">Add Task</span>
                </button>
            </div>
        </div>

        {{-- Toolbar --}}
        <div class="erp-toolbar">
            <div class="erp-toolbar-left">
                {{-- Bulk actions --}}
                <div class="erp-bulk-wrap" id="bulkWrap" style="display:none;">
                    <button class="erp-btn" onclick="toggleBulkMenu()">
                        <span id="bulkCount">0 selected</span>
                        <svg viewBox="0 0 24 24" style="width:10px;height:10px;"><path d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div class="erp-bulk-menu" id="bulkMenu">
                        <button class="erp-bulk-item" onclick="bulkDone()">
                            <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            Mark as Done
                        </button>
                        <button class="erp-bulk-item" onclick="bulkDelete()" style="color:var(--erp-danger);">
                            <svg viewBox="0 0 24 24" style="stroke:var(--erp-danger);"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            Delete
                        </button>
                    </div>
                </div>
            </div>
            <div class="erp-toolbar-right">
                <div class="erp-mode-toggle">
                    <button class="erp-mode-btn active" data-mode="list" onclick="setMode('list')" title="List">
                        <svg viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                    </button>
                    <button class="erp-mode-btn" data-mode="board" onclick="setMode('board')" title="Board">
                        <svg viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    </button>
                </div>
                <select class="erp-sort-sel" id="sortSel" onchange="redraw()">
                    <option value="">Sort by...</option>
                    <option value="due_date_asc">Due Date (Earliest)</option>
                    <option value="due_date_desc">Due Date (Latest)</option>
                    <option value="priority_asc">Priority (Low→High)</option>
                    <option value="priority_desc">Priority (High→Low)</option>
                    <option value="created_desc">Newest First</option>
                    <option value="created_asc">Oldest First</option>
                </select>
                <button class="erp-btn erp-btn-ghost" onclick="table.ajax.reload()" title="Refresh">
                    <svg viewBox="0 0 24 24"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"/><path d="M16 21h5v-5"/></svg>
                </button>
                <div class="erp-search-wrap">
                    <svg viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <input type="text" id="searchInput" placeholder="Search tasks..." oninput="debSearch()">
                </div>
            </div>
        </div>

        {{-- DataTable --}}
        <div class="erp-table-wrap" id="listView">
            <table id="tasksTable" style="width:100%">
                <thead>
                    <tr>
                        <th style="width:40px;"><input type="checkbox" id="selAll" onchange="toggleAll()"></th>
                        <th>Task</th>
                        <th style="width:110px;">Status</th>
                        <th class="col-priority" style="width:100px;">Priority</th>
                        @if($isAdmin)<th style="width:150px;">Assignee</th>@endif
                        <th class="col-project" style="width:150px;">Project</th>
                        <th class="col-due" style="width:100px;">Due Date</th>
                    </tr>
                </thead>
            </table>
        </div>

        {{-- Board view placeholder --}}
        <div id="boardView" style="display:none;flex:1;overflow-x:auto;padding:16px 20px;background:var(--erp-muted);">
            <div id="boardInner" style="display:flex;gap:12px;min-width:max-content;"></div>
        </div>

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
                    <label class="chip-opt">
                        <input type="radio" name="ct_assignee" value="{{ $s->id }}">
                        <span class="chip-lbl">{{ $s->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            @endif
            <div class="f-row">
                <div class="f-group">
                    <label class="f-label">Priority</label>
                    <select id="ct_priority" class="f-input">
                        <option value="normal">Normal</option>
                        <option value="low">Low</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
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
            <button class="erp-btn" onclick="closeModal('createModal')">Cancel</button>
            <button class="erp-btn" style="background:var(--erp-primary);border-color:var(--erp-primary);color:white;" id="ct_submit" onclick="submitCreate()">Create Task</button>
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

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
const CSRF    = '{{ csrf_token() }}';
const IS_ADMIN= {{ $isAdmin ? 'true' : 'false' }};
const MY_ID   = {{ $staff->id }};
const DT_URL  = '{{ route('tasks.datatable') }}';
const STORE_URL = '{{ route('tasks.store') }}';
const TODAY   = '{{ now()->toDateString() }}';

const STATUS_LABEL = { pending:'Pending', in_progress:'In Progress', blocked:'Blocked', done:'Done' };
const PRIO_LABEL   = { low:'Low', normal:'Normal', high:'High', urgent:'Urgent' };
const CAT_ICON     = { sales:'💼', development:'🖥️', support:'🎫', hr:'👥', finance:'💰', operations:'⚙️', other:'📌' };
const STAFF_LIST   = @json($staffList->map(fn($s) => ['id' => $s->id, 'name' => $s->name]));

let currentDetailId = null;

let currentView = '{{ request()->routeIs('my-tasks') ? 'my' : 'all' }}';
let viewMode    = 'list';
let selected    = new Set();
let searchTimer;
let table;

/* ── Helpers ── */
function esc(s){ const d=document.createElement('div');d.textContent=s||'';return d.innerHTML; }
function initials(n){ const p=(n||'').split(' ');return (p[0]?p[0][0]:'')+(p[1]?p[1][0]:''); }
function toast(msg, type='ok'){
    const el=document.createElement('div');
    el.className='toast toast-'+type; el.textContent=msg;
    document.getElementById('toasts').appendChild(el);
    setTimeout(()=>el.remove(),3000);
}
function timeAgo(ds){
    const d=Math.floor((Date.now()-new Date(ds).getTime())/1000);
    if(d<60) return 'just now';
    if(d<3600) return Math.floor(d/60)+'m ago';
    if(d<86400) return Math.floor(d/3600)+'h ago';
    return Math.floor(d/86400)+'d ago';
}
function openModal(id){ document.getElementById(id).classList.add('open'); }
function closeModal(id){ document.getElementById(id).classList.remove('open'); }
async function req(url,method='GET',body=null){
    const opt={method,headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}};
    if(body){opt.headers['Content-Type']='application/json';opt.body=JSON.stringify(body);}
    const r=await fetch(url,opt);
    return r.json();
}

/* ── Filter params ── */
function getParams(){
    const p={view:currentView};
    const s=document.getElementById('searchInput').value.trim();
    if(s) p.keyword=s;

    ['status','priority','assigned_to','project_id'].forEach(key=>{
        const checked=[...document.querySelectorAll(`[data-filter="${key}"]:checked`)].map(c=>c.value);
        if(checked.length===1) p[key]=checked[0];
        // multiple values: send as comma-separated
        if(checked.length>1) p[key]=checked.join(',');
    });

    const df=document.getElementById('dateFrom').value;
    const dt=document.getElementById('dateTo').value;
    if(df) p.due_after=df;
    if(dt) p.due_before=dt;

    return p;
}

/* ── DataTable init ── */
$(function(){
    table = $('#tasksTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: DT_URL,
            type: 'GET',
            data: function(d){
                Object.assign(d, getParams());
                // custom sort
                const sortVal = document.getElementById('sortSel').value;
                if(sortVal){
                    const [col,dir]=sortVal.split('_');
                    const colMap={due_date:5,priority:3,created:6};
                    d.order=[{column:colMap[col]??0,dir:dir}];
                }
            }
        },
        columnDefs: [
            { className: 'col-priority', targets: IS_ADMIN ? 3 : 3 },
            { className: 'col-project',  targets: IS_ADMIN ? 5 : 4 },
            { className: 'col-due',      targets: IS_ADMIN ? 6 : 5 },
        ],
        columns: [
            { data: 'id',            orderable: false, searchable: false,
              render: (d,t,r) => `<input type="checkbox" ${selected.has(r.id)?'checked':''} onchange="toggleRow(${r.id},this)" onclick="event.stopPropagation()" style="width:14px;height:14px;accent-color:var(--erp-primary);">` },
            { data: 'title',         render: (d) => `<div class="task-title">${esc(d)}</div>` },
            { data: 'status',        render: (d) => `<span class="status-chip"><span class="erp-dot dot-${d}"></span>${STATUS_LABEL[d]||d}</span>` },
            { data: 'priority',      render: (d) => `<span class="priority-chip"><span class="erp-dot dot-${d}"></span>${PRIO_LABEL[d]||d}</span>` },
            @if($isAdmin)
            { data: 'assignee_name', render: (d) => d
                ? `<span class="erp-av"><span class="erp-av-circle">${initials(d).toUpperCase()}</span>${esc(d)}</span>`
                : '<span style="color:var(--erp-text-subtle)">—</span>' },
            @endif
            { data: 'project_name',  render: (d) => d ? `<span class="erp-tag">${esc(d)}</span>` : '—' },
            { data: 'due_date_fmt', render: (d,t,r) => {
                if(!d) return '<span class="due-normal">—</span>';
                if(r.is_overdue)   return `<span class="due-overdue">${esc(d)}</span>`;
                if(r.is_due_today) return `<span class="due-today">${esc(d)}</span>`;
                return `<span class="due-normal">${esc(d)}</span>`;
            }},
        ],
        dom: 'tip',
        pageLength: 20,
        language: {
            processing: 'Loading...',
            emptyTable: '<div class="erp-empty"><svg viewBox="0 0 24 24"><path d="M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z"/><path d="m9 12 2 2 4-4"/></svg><p>No tasks found</p><small>Create a new task to get started</small></div>',
            info: 'Showing _START_–_END_ of _TOTAL_ tasks',
            paginate: { first:'«', last:'»', next:'›', previous:'‹' },
        },
        drawCallback: function(s){
            document.getElementById('taskCount').textContent = s.json?.recordsFiltered + ' tasks';
            // restore checkboxes
            $('#tasksTable tbody tr').each(function(){
                const id = table.row(this).data()?.id;
                if(id && selected.has(id)) $(this).addClass('selected');
            });
            updateBulk();
        },
        rowCallback: function(row, data){
            $(row).on('click', function(){ openDetail(data.id); });
        },
        initComplete: function(){
            updateTitle();
            // Auto-open task if ?open=ID is in the URL (e.g. navigated from Project detail)
            const openId = new URLSearchParams(location.search).get('open');
            if (openId) openDetail(parseInt(openId));
        }
    });
});

/* ── View switch ── */
function switchView(v){
    currentView = v;
    document.getElementById('tabMy').classList.toggle('active', v==='my');
    document.getElementById('tabAll').classList.toggle('active', v==='all');
    updateTitle();
    selected.clear(); updateBulk();
    table.ajax.reload();
}
function updateTitle(){
    document.getElementById('pageTitle').textContent = currentView==='my' ? 'My Tasks' : 'All Tasks';
}

/* ── Mode toggle ── */
function setMode(m){
    viewMode=m;
    document.querySelectorAll('.erp-mode-btn').forEach(b=>b.classList.toggle('active',b.dataset.mode===m));
    document.getElementById('listView').style.display  = m==='list' ? 'block' : 'none';
    document.getElementById('boardView').style.display = m==='board' ? 'block' : 'none';
    if(m==='board') loadBoard();
}

/* ── Filter helpers ── */
function toggleSection(el){ el.parentElement.classList.toggle('collapsed'); }
function clearFilters(){
    document.querySelectorAll('[data-filter]').forEach(c=>c.checked=false);
    document.getElementById('dateFrom').value='';
    document.getElementById('dateTo').value='';
    document.getElementById('searchInput').value='';
    redraw();
}
function redraw(){ table && table.ajax.reload(); }
function debSearch(){ clearTimeout(searchTimer); searchTimer=setTimeout(redraw,300); }

/* ── Bulk selection ── */
function toggleAll(){
    const chk=document.getElementById('selAll').checked;
    table.rows().data().each(r=>{ chk ? selected.add(r.id) : selected.delete(r.id); });
    table.draw(false);
}
function toggleRow(id,cb){
    cb.checked ? selected.add(id) : selected.delete(id);
    updateBulk();
    const row=table.rows().nodes().toArray().find(tr=>table.row(tr).data()?.id===id);
    if(row) row.classList.toggle('selected', selected.has(id));
}
function updateBulk(){
    const c=selected.size;
    document.getElementById('bulkWrap').style.display = c>0 ? 'block' : 'none';
    document.getElementById('bulkCount').textContent = c+' selected';
}
function toggleBulkMenu(){ document.getElementById('bulkMenu').classList.toggle('open'); }

async function bulkDone(){
    for(const id of selected){
        await req(`/tasks/${id}`,'PUT',{status:'done'});
    }
    toast(selected.size+' tasks marked done');
    selected.clear(); updateBulk(); table.ajax.reload();
    document.getElementById('bulkMenu').classList.remove('open');
}
async function bulkDelete(){
    if(!confirm(`Delete ${selected.size} tasks?`)) return;
    for(const id of selected){
        await req(`/tasks/${id}`,'DELETE');
    }
    toast(selected.size+' tasks deleted');
    selected.clear(); updateBulk(); table.ajax.reload();
    document.getElementById('bulkMenu').classList.remove('open');
}

/* ── Create Task ── */
function openCreate(){
    document.getElementById('ct_title').value='';
    document.getElementById('ct_desc').value='';
    document.getElementById('ct_due').value='';
    document.getElementById('ct_priority').value='normal';
    document.getElementById('ct_category').value='other';
    document.getElementById('ct_project').value='';
    document.querySelectorAll('input[name="ct_assignee"]').forEach(r=>r.checked=false);
    document.getElementById('ct_title_err').style.display='none';
    openModal('createModal');
    setTimeout(()=>document.getElementById('ct_title').focus(),100);
}
async function submitCreate(){
    const title=document.getElementById('ct_title').value.trim();
    if(!title){ document.getElementById('ct_title_err').style.display='block'; document.getElementById('ct_title').focus(); return; }
    document.getElementById('ct_title_err').style.display='none';
    const btn=document.getElementById('ct_submit');
    btn.disabled=true; btn.textContent='Creating...';

    const assignee=document.querySelector('input[name="ct_assignee"]:checked');
    const r=await req(STORE_URL,'POST',{
        title,
        description: document.getElementById('ct_desc').value.trim()||null,
        assigned_to: assignee ? assignee.value : null,
        priority:    document.getElementById('ct_priority').value,
        due_date:    document.getElementById('ct_due').value||null,
        category:    document.getElementById('ct_category').value,
        project_id:  document.getElementById('ct_project').value||null,
    });
    btn.disabled=false; btn.textContent='Create Task';

    if(r.ok){ toast('Task successfully created'); closeModal('createModal'); table.ajax.reload(); }
    else toast(r.message||'Failed to create task','err');
}

/* ── Task Detail ── */
async function openDetail(id){
    currentDetailId = id;
    const r=await req(`/tasks/${id}`);
    if(!r.ok){ toast('Not found','err'); return; }
    const t=r.task, comments=r.comments||[], history=r.history||[];

    document.getElementById('dt_title').textContent=t.title;

    const dueDateStr = t.due_date ? (t.due_date+'').slice(0,10) : null; // ensure YYYY-MM-DD
    const isOverdue  = dueDateStr && dueDateStr < TODAY && t.status!=='done';
    const isDueToday = dueDateStr && dueDateStr === TODAY && t.status!=='done';

    /* ── Badges ── */
    let html = `<div class="detail-meta">`;
    html += `<span class="detail-badge dbg-${t.status}"><span class="erp-dot dot-${t.status}"></span>${STATUS_LABEL[t.status]}</span>`;
    html += `<span class="detail-badge dbg-${t.priority}"><span class="erp-dot dot-${t.priority}"></span>${PRIO_LABEL[t.priority]}</span>`;
    if(t.category) html += `<span class="detail-badge" style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;">${CAT_ICON[t.category]||'📌'} ${t.category}</span>`;
    const projectName = t.project?.name || t.project_name || null;
    if(projectName) html += `<span class="detail-badge" style="background:#f1f5f9;color:#475569;border:1px solid #e2e8f0;">📁 ${esc(projectName)}</span>`;
    if(isOverdue)  html += `<span class="detail-badge" style="background:#fef2f2;color:#991b1b;border:1px solid #fecaca;">⚠️ Overdue</span>`;
    if(isDueToday) html += `<span class="detail-badge" style="background:#fffbeb;color:#92400e;border:1px solid #fde68a;">📅 Due Today</span>`;
    html += `</div>`;

    /* ── Description ── */
    if(t.description) html += `<div class="detail-desc">${esc(t.description)}</div>`;

    /* ── Due date + Creator ── */
    if(dueDateStr){
        const fmt = new Date(dueDateStr+'T00:00:00').toLocaleDateString('en-IN',{day:'numeric',month:'long',year:'numeric'});
        const dateColor = isOverdue ? '#dc2626' : isDueToday ? '#d97706' : 'var(--erp-text-muted)';
        html += `<div style="font-size:12px;margin-bottom:4px;">📅 Due: <strong style="color:${dateColor}">${esc(fmt)}</strong></div>`;
    }
    const creatorName = t.creator?.name || null;
    html += `<div style="font-size:12px;color:var(--erp-text-muted);margin-bottom:16px;">Created by <strong style="color:var(--erp-text);">${esc(creatorName||'—')}</strong> · ${timeAgo(t.created_at)}</div>`;

    /* ── Inline fields: Status | Priority | Assigned To ── */
    const sOpts=['pending','in_progress','blocked','done'].map(s=>`<option value="${s}" ${t.status===s?'selected':''}>${STATUS_LABEL[s]}</option>`).join('');
    const pOpts=['urgent','high','normal','low'].map(p=>`<option value="${p}" ${t.priority===p?'selected':''}>${PRIO_LABEL[p]}</option>`).join('');
    html += `<div class="detail-fields">
        <div><label class="f-label">Status</label>
             <select class="f-input" onchange="quickUpdate(${t.id},'status',this.value)" style="height:34px;">${sOpts}</select></div>
        <div><label class="f-label">Priority</label>
             <select class="f-input" onchange="quickUpdate(${t.id},'priority',this.value)" style="height:34px;">${pOpts}</select></div>`;
    if(IS_ADMIN && STAFF_LIST.length){
        const aOpts=`<option value="">Unassigned</option>`+STAFF_LIST.map(s=>`<option value="${s.id}" ${t.assigned_to==s.id?'selected':''}>${esc(s.name)}</option>`).join('');
        html += `<div><label class="f-label">Assigned To</label>
                 <select class="f-input" onchange="quickUpdate(${t.id},'assigned_to',this.value)" style="height:34px;">${aOpts}</select></div>`;
    }
    html += `</div>`;

    /* ── Quick status buttons (all statuses except current) ── */
    if(t.status!=='done'){
        const others=['pending','in_progress','blocked','done'].filter(s=>s!==t.status);
        html += `<div style="display:flex;gap:8px;margin-bottom:16px;flex-wrap:wrap;">`;
        others.forEach(s=>{
            const isTeal = s==='done';
            html += `<button onclick="quickUpdate(${t.id},'status','${s}')"
                style="${isTeal?'background:var(--erp-primary);border-color:var(--erp-primary);color:white;':'background:white;border:1px solid var(--erp-border);color:var(--erp-text);'}padding:6px 16px;border-radius:6px;font-size:13px;font-weight:500;cursor:pointer;font-family:inherit;transition:all .15s;">
                ${STATUS_LABEL[s]}</button>`;
        });
        html += `</div>`;
    }

    html += `<hr style="border:none;border-top:1px solid var(--erp-border);margin:0 0 16px;">`;

    /* ── Comments ── */
    html += `<div class="section-title" style="margin-top:0;">Comments (${comments.length})</div>`;
    if(!comments.length){
        html += `<p style="font-size:13px;color:var(--erp-text-muted);margin:0 0 12px;">No comments yet</p>`;
    } else {
        comments.forEach(c=>{
            html += `<div class="comment-item">
                <div class="comment-meta">
                    <span class="comment-author">${esc(c.staff?.name||'Unknown')}</span>
                    <span class="comment-time">${timeAgo(c.created_at)}</span>
                </div>
                <div class="comment-text">${esc(c.comment).replace(/\n/g,'<br>')}</div>
            </div>`;
        });
    }
    html += `<div style="display:flex;gap:8px;margin-bottom:16px;align-items:flex-end;">
        <textarea id="commentInput" class="f-input" placeholder="Add a comment..." rows="3" style="flex:1;min-height:70px;resize:vertical;"></textarea>
        <button onclick="addComment(${t.id})" style="padding:8px 16px;background:var(--erp-primary);border:none;border-radius:6px;color:white;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;white-space:nowrap;flex-shrink:0;">Post</button>
    </div>`;

    /* ── Mark as Complete ── */
    if(t.status!=='done'){
        html += `<button onclick="quickUpdate(${t.id},'status','done')"
            style="width:100%;display:flex;align-items:center;justify-content:center;gap:6px;padding:11px;background:var(--erp-primary);border:none;border-radius:7px;color:white;font-size:14px;font-weight:600;cursor:pointer;font-family:inherit;">
            <svg viewBox="0 0 24 24" style="width:16px;height:16px;stroke:white;fill:none;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round;"><polyline points="20 6 9 17 4 12"/></svg>
            Mark as Complete</button>`;
    }

    html += `<hr style="border:none;border-top:1px solid var(--erp-border);margin:16px 0 0;">`;

    /* ── History ── */
    if(history.length){
        html += `<div class="section-title">History</div>`;
        history.forEach(h=>{
            const action=(h.action||'').replace(/_/g,' ');
            html += `<div class="history-item">
                <span style="color:var(--erp-text);">${esc(h.staff?.name||'?')} <span style="color:var(--erp-text-muted);">${action}</span></span>
                ${h.new_value?`<span style="color:var(--erp-text-muted);font-size:11px;">→ ${esc(h.new_value)}</span>`:''}
                <span style="color:var(--erp-text-subtle);">${timeAgo(h.created_at)}</span>
            </div>`;
        });
    }

    document.getElementById('dt_body').innerHTML=html;
    openModal('detailModal');
}

async function quickUpdate(id, field, value){
    const r=await req(`/tasks/${id}`,'PUT',{[field]:value});
    if(r.ok){
        toast('Updated');
        table.ajax.reload();
        if(document.getElementById('detailModal').classList.contains('open')){
            openDetail(id);
        }
    } else toast(r.message||'Error','err');
}

async function addComment(id){
    const inp=document.getElementById('commentInput');
    const c=inp.value.trim();
    if(!c) return;
    const r=await req(`/tasks/${id}/comments`,'POST',{comment:c});
    if(r.ok){ toast('Comment added'); openDetail(id); }
    else toast(r.message||'Error','err');
}


/* ── Board (Kanban) ── */
async function loadBoard(){
    // Fetch all tasks for board
    const params=new URLSearchParams({...getParams(),view:currentView,limit:1000});
    const r=await fetch(DT_URL+'?'+params.toString()+'&board=1&draw=1&start=0&length=1000',{headers:{'Accept':'application/json'}});
    const data=await r.json();
    const tasks=data.data||[];

    const COLS=[
        {key:'pending',   label:'Todo',       dotClass:'dot-pending'},
        {key:'in_progress',label:'In Progress',dotClass:'dot-in_progress'},
        {key:'blocked',   label:'Blocked',    dotClass:'dot-blocked'},
        {key:'done',      label:'Done',       dotClass:'dot-done'},
    ];

    const grouped={};
    COLS.forEach(c=>grouped[c.key]=[]);
    tasks.forEach(t=>{ if(grouped[t.status]) grouped[t.status].push(t); });

    const inner=document.getElementById('boardInner');
    inner.innerHTML=COLS.map(col=>{
        const cards=grouped[col.key];
        const cardsHtml=cards.length ? cards.map(t=>`
            <div onclick="openDetail(${t.id})" style="background:white;border:1px solid var(--erp-border);border-radius:6px;padding:10px;cursor:pointer;border-left:3px solid ${borderColor(t.priority)};transition:all .15s;" onmouseenter="this.style.boxShadow='0 2px 6px rgba(0,0,0,.06)'" onmouseleave="this.style.boxShadow=''">
                <div style="font-size:13px;font-weight:500;color:var(--erp-text);margin-bottom:6px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">${esc(t.title)}</div>
                <div style="display:flex;justify-content:space-between;font-size:11px;color:var(--erp-text-muted);">
                    <span>${t.assignee_name||'Unassigned'}</span>
                    ${t.due_date?`<span>${new Date((t.due_date+'').slice(0,10)+'T00:00:00').toLocaleDateString('en-IN',{day:'numeric',month:'short'})}</span>`:''}
                </div>
            </div>`).join('')
            : `<div style="padding:20px 12px;text-align:center;color:var(--erp-text-subtle);font-size:12px;border:1px dashed var(--erp-border);border-radius:6px;">No tasks</div>`;

        return `<div style="width:280px;min-width:280px;background:white;border:1px solid var(--erp-border);border-radius:8px;display:flex;flex-direction:column;">
            <div style="padding:10px 12px;border-bottom:1px solid var(--erp-border);background:var(--erp-muted);border-radius:8px 8px 0 0;display:flex;align-items:center;justify-content:space-between;">
                <span style="font-size:12px;font-weight:600;display:flex;align-items:center;gap:6px;"><span class="erp-dot ${col.dotClass}"></span>${col.label}</span>
                <span style="font-size:11px;color:var(--erp-text-muted);background:white;padding:1px 7px;border-radius:10px;border:1px solid var(--erp-border);">${cards.length}</span>
            </div>
            <div style="padding:8px;display:flex;flex-direction:column;gap:6px;overflow-y:auto;">${cardsHtml}</div>
        </div>`;
    }).join('');
}
function borderColor(p){ return {urgent:'#dc2626',high:'#f97316',normal:'#3b82f6',low:'#94a3b8'}[p]||'#94a3b8'; }

/* ── Filter drawer (mobile) ── */
function openFilters(){
    document.getElementById('filterSidebar').classList.add('open');
    document.getElementById('filterOverlay').classList.add('open');
}
function closeFilters(){
    document.getElementById('filterSidebar').classList.remove('open');
    document.getElementById('filterOverlay').classList.remove('open');
}
// Close filter drawer on resize to desktop
window.addEventListener('resize',()=>{
    if(window.innerWidth > 768) closeFilters();
});

/* ── Close on backdrop ── */
document.querySelectorAll('.modal-backdrop').forEach(m=>{
    m.addEventListener('click',e=>{ if(e.target===m) m.classList.remove('open'); });
});

/* ── Close bulk menu on outside click ── */
document.addEventListener('click',e=>{
    const menu=document.getElementById('bulkMenu');
    if(menu.classList.contains('open') && !menu.closest('.erp-bulk-wrap').contains(e.target)){
        menu.classList.remove('open');
    }
});
</script>
</x-layouts.app>
