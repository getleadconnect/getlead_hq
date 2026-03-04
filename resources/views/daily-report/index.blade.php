<x-layouts.app title="Daily Report">
@push('styles')
<style>
    /* ── Wizard wrapper ── */
    .dr-outer {
        flex: 1;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: flex-start;
        padding: 32px 16px 48px;
        background: #f1f5f9;
        min-height: 100%;
    }

    /* ── Progress bar (wizard only) ── */
    .dr-progress-wrap {
        width: 100%;
        max-width: 600px;
        margin: 0 auto 20px;
    }
    .dr-progress-text {
        font-size: 12px;
        font-weight: 500;
        color: #64748b;
        text-align: center;
        margin-bottom: 8px;
    }
    .dr-progress-track {
        width: 100%;
        height: 5px;
        background: #e2e8f0;
        border-radius: 99px;
        overflow: hidden;
    }
    .dr-progress-fill {
        height: 100%;
        background: #14b8a6;
        border-radius: 99px;
        transition: width 0.35s ease;
    }

    /* ── Card ── */
    .dr-card {
        width: 100%;
        max-width: 600px;
        background: white;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.07);
        padding: 36px 32px 28px;
        text-align: center;
    }
    .dr-emoji {
        font-size: 44px;
        line-height: 1;
        margin-bottom: 14px;
        display: block;
    }
    .dr-card h2 {
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 6px;
    }
    .dr-subtitle {
        font-size: 13px;
        color: #64748b;
        margin: 0 0 24px;
    }

    /* ── Date input ── */
    .dr-field-wrap { text-align: left; margin-bottom: 16px; }
    .dr-label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 6px;
    }
    .dr-input {
        width: 100%;
        height: 40px;
        padding: 0 12px;
        font-size: 14px;
        font-family: inherit;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
        color: #1e293b;
        transition: border-color 0.15s;
        box-sizing: border-box;
    }
    .dr-input:focus { outline: none; border-color: #14b8a6; box-shadow: 0 0 0 3px rgba(20,184,166,.12); }

    /* ── Submitted badge ── */
    .dr-submitted-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 14px;
        background: #f0fdf4;
        border: 1px solid #86efac;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500;
        color: #166534;
        margin-bottom: 16px;
        width: 100%;
        box-sizing: border-box;
        justify-content: center;
    }

    /* ── Buttons ── */
    .dr-btn-primary {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 44px;
        background: #0f172a;
        color: white;
        font-size: 15px;
        font-weight: 600;
        border: none;
        border-radius: 9px;
        cursor: pointer;
        font-family: inherit;
        transition: background 0.15s;
        margin-top: 8px;
    }
    .dr-btn-primary:hover { background: #1e293b; }
    .dr-btn-primary:disabled { opacity: 0.6; cursor: not-allowed; }

    /* ── Recent reports ── */
    .dr-recent {
        margin-top: 24px;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
        text-align: left;
    }
    .dr-recent-title {
        font-size: 12px;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .dr-recent-list { display: flex; flex-direction: column; gap: 6px; }
    .dr-recent-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 9px 12px;
        border-radius: 8px;
        text-decoration: none;
        font-size: 13px;
        transition: background 0.1s;
    }
    .dr-recent-item:hover { opacity: 0.85; }
    .dr-recent-item-today { background: #f0fdfa; }
    .dr-recent-item-normal { background: #f8fafc; }
    .dr-recent-item-label { color: #1e293b; font-weight: 500; }
    .dr-recent-item-right {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #64748b;
        font-size: 12px;
    }
    .dr-recent-item-edit { color: #0d9488; font-weight: 600; }

    /* ── Screen wrapper widths (fix flex shrink) ── */
    #dateScreen, #wizardScreen, #successScreen {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* ── Steps container centering ── */
    #stepsContainer {
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    /* ── Step wizard inputs ── */
    .dr-step { display: none; width: 100%; }
    .dr-step-card {
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
        background: white;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.07);
        padding: 36px 32px 28px;
        text-align: center;
    }
    .dr-step-card h2 {
        font-size: 19px;
        font-weight: 700;
        color: #0f172a;
        margin: 0 0 20px;
    }
    .dr-step-input-wrap { text-align: left; }

    .dr-wizard-input {
        width: 100%;
        height: 42px;
        padding: 0 12px;
        font-size: 14px;
        font-family: inherit;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
        color: #1e293b;
        box-sizing: border-box;
        transition: border-color 0.15s;
    }
    .dr-wizard-input:focus { outline: none; border-color: #14b8a6; box-shadow: 0 0 0 3px rgba(20,184,166,.12); background: white; }

    .dr-wizard-textarea {
        width: 100%;
        min-height: 110px;
        padding: 10px 12px;
        font-size: 14px;
        font-family: inherit;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        background: #f8fafc;
        color: #1e293b;
        box-sizing: border-box;
        resize: vertical;
        transition: border-color 0.15s;
        line-height: 1.5;
    }
    .dr-wizard-textarea:focus { outline: none; border-color: #14b8a6; box-shadow: 0 0 0 3px rgba(20,184,166,.12); background: white; }

    /* Dual input */
    .dr-dual { display: flex; gap: 12px; }
    .dr-dual-field { flex: 1; }
    .dr-dual-field label { display: block; font-size: 11px; font-weight: 600; color: #64748b; margin-bottom: 5px; }

    /* Repeatable */
    .dr-repeatable-group { margin-bottom: 8px; }
    .dr-repeatable-row { display: flex; gap: 8px; align-items: center; }
    .dr-repeatable-row .dr-wizard-input { flex: 1; }
    .dr-remove-btn {
        width: 28px; height: 28px; flex-shrink: 0;
        border: none; background: #fee2e2; color: #dc2626;
        border-radius: 6px; cursor: pointer; font-size: 16px;
        display: flex; align-items: center; justify-content: center;
        font-family: inherit; line-height: 1;
    }
    .dr-add-btn {
        display: inline-flex; align-items: center; gap: 5px;
        padding: 6px 12px; margin-top: 8px;
        font-size: 12px; font-weight: 500; font-family: inherit;
        border: 1px dashed #cbd5e1; border-radius: 6px;
        background: transparent; color: #64748b;
        cursor: pointer; transition: all 0.15s;
    }
    .dr-add-btn:hover { border-color: #14b8a6; color: #14b8a6; background: #f0fdfa; }

    /* ── Wizard nav ── */
    .dr-nav {
        width: 100%;
        max-width: 600px;
        margin: 16px auto 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .dr-nav-right { display: flex; gap: 8px; align-items: center; }

    .dr-btn-back {
        height: 40px;
        padding: 0 18px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        background: white;
        color: #1e293b;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s;
    }
    .dr-btn-back:hover { background: #f1f5f9; }

    .dr-btn-skip {
        height: 40px;
        padding: 0 14px;
        border: none;
        background: transparent;
        color: #64748b;
        font-size: 13px;
        font-weight: 500;
        cursor: pointer;
        font-family: inherit;
        transition: color 0.15s;
    }
    .dr-btn-skip:hover { color: #1e293b; }

    .dr-btn-next {
        height: 40px;
        padding: 0 20px;
        border: none;
        border-radius: 8px;
        background: #0f172a;
        color: white;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        font-family: inherit;
        transition: background 0.15s;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .dr-btn-next:hover { background: #1e293b; }
    .dr-btn-next:disabled { opacity: 0.6; cursor: not-allowed; }

    /* ── Success ── */
    .dr-success-card {
        width: 100%;
        max-width: 600px;
        margin: 0 auto;
        background: white;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px rgba(0,0,0,0.07);
        padding: 48px 32px 36px;
        text-align: center;
    }
    .dr-success-card h2 { font-size: 22px; font-weight: 700; color: #0f172a; margin: 0 0 8px; }
    .dr-success-card p { font-size: 13px; color: #64748b; margin: 0 0 28px; }
    .dr-success-btns { display: flex; flex-direction: column; gap: 10px; }
    .dr-btn-secondary {
        display: flex; align-items: center; justify-content: center;
        width: 100%; height: 42px;
        border: 1px solid #e2e8f0; border-radius: 8px;
        background: white; color: #1e293b;
        font-size: 14px; font-weight: 500;
        cursor: pointer; font-family: inherit;
        text-decoration: none;
        transition: background 0.15s;
    }
    .dr-btn-secondary:hover { background: #f8fafc; }
</style>
@endpush

<div class="dr-outer">

    {{-- ── Date Screen ── --}}
    <div id="dateScreen">
        <div class="dr-card">
            <span class="dr-emoji">📝</span>
            <h2>Daily Report</h2>
            <p class="dr-subtitle">{{ $staff->name }} · {{ ucfirst(str_replace('_', ' ', $staff->role)) }}</p>

            <div class="dr-field-wrap">
                <label class="dr-label">Report Date</label>
                <input type="date" id="reportDate" class="dr-input"
                       value="{{ $reportDate }}"
                       max="{{ today()->toDateString() }}">
            </div>

            @if($existing)
            <div class="dr-submitted-badge">
                ✅ Submitted at {{ $existing->submitted_at?->format('h:i A') }}
            </div>
            @endif

            <button class="dr-btn-primary" onclick="startWizard()">
                {{ $existing ? '✏️ Edit Report' : 'Continue →' }}
            </button>

            <div class="dr-recent" id="recentReportsWrap" style="{{ $recentReports->isEmpty() ? 'display:none' : '' }}">
                <div class="dr-recent-title">📋 Recent Reports</div>
                <div class="dr-recent-list" id="recentReportsList">
                    @foreach($recentReports as $rr)
                        @php
                            $d       = $rr->report_date->toDateString();
                            $label   = $rr->report_date->format('D, j M');
                            $time    = $rr->submitted_at?->format('h:i A') ?? '—';
                            $isToday = $d === today()->toDateString();
                        @endphp
                        <a href="{{ route('daily-report') }}?date={{ $d }}"
                           class="dr-recent-item {{ $isToday ? 'dr-recent-item-today' : 'dr-recent-item-normal' }}">
                            <span class="dr-recent-item-label">
                                {{ $label }}{{ $isToday ? ' (Today)' : '' }}
                            </span>
                            <span class="dr-recent-item-right">
                                <span>{{ $time }}</span>
                                <span class="dr-recent-item-edit">Edit ✏️</span>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- ── Wizard Screen ── --}}
    <div id="wizardScreen" style="display:none;">
        <div class="dr-progress-wrap">
            <div class="dr-progress-text">Step <span id="stepNum">1</span> of <span id="stepTotal">3</span></div>
            <div class="dr-progress-track">
                <div class="dr-progress-fill" id="progressFill" style="width:33%"></div>
            </div>
        </div>

        <div id="stepsContainer"></div>

        <div class="dr-nav">
            <button class="dr-btn-back" id="btnBack" onclick="prevStep()">← Back</button>
            <div class="dr-nav-right">
                <button class="dr-btn-skip" id="btnSkip" onclick="nextStep()" style="display:none">Skip</button>
                <button class="dr-btn-next" id="btnNext" onclick="nextStep()">Next →</button>
            </div>
        </div>
    </div>

    {{-- ── Success Screen ── --}}
    <div id="successScreen" style="display:none;">
        <div class="dr-success-card">
            <span class="dr-emoji" id="successEmoji">🎉</span>
            <h2 id="successTitle">Report Submitted!</h2>
            <p id="successMsg">Your daily report has been saved.</p>
            <div class="dr-success-btns">
                <button class="dr-btn-primary" onclick="goToDateScreen()" id="successAction">Submit Another Report</button>
                <a href="{{ route('my-tasks') }}" class="dr-btn-secondary">← Back to Tasks</a>
            </div>
        </div>
    </div>

</div>

<script>
const CSRF      = '{{ csrf_token() }}';
const ROLE      = '{{ $staff->role }}';
const STORE_URL = '{{ route('daily-report.store') }}';
const EXISTING  = @json($existing?->report_data);
const IS_EDIT   = !!EXISTING;

const STEPS = {
    sales_rep: [
        { key:'calls_made',       emoji:'📞', q:'How many calls did you make today?',          type:'number' },
        { key:'calls_connected',  emoji:'📱', q:'How many calls connected?',                   type:'number' },
        { key:'demos',            emoji:'🎯', q:'Demos scheduled / completed?',                type:'dual',
          keys:['demos_scheduled','demos_completed'], labels:['Scheduled','Completed'] },
        { key:'trials',           emoji:'🆕', q:'Trials activated today?',                     type:'number' },
        { key:'payments',         emoji:'💰', q:'Payments closed? Total amount?',              type:'dual',
          keys:['payments_closed','payments_amount'], labels:['Count','Amount ₹'] },
        { key:'hot_leads',        emoji:'🔥', q:'Any hot leads to follow up?',                 type:'textarea', opt:true },
        { key:'notes',            emoji:'📝', q:'Any blockers or notes?',                      type:'textarea', opt:true },
    ],
    secretary: [
        { key:'payments',         emoji:'💰', q:'Payments received today?',                    type:'repeatable',
          fields:['customer','amount','type'], labels:['Customer','Amount ₹','Type'],
          ftypes:['text','number','select'], opts:{ type:['new','renewal'] } },
        { key:'tickets_handled',  emoji:'🎫', q:'Support tickets handled?',                    type:'number' },
        { key:'license_updates',  emoji:'📋', q:'License updates done?',                       type:'number' },
        { key:'followups',        emoji:'✅', q:'Follow-ups completed?',                       type:'number' },
        { key:'notes',            emoji:'📝', q:'Any notes?',                                  type:'textarea', opt:true },
    ],
    support: [
        { key:'tickets_handled',  emoji:'🎫', q:'Tickets handled today?',                      type:'number' },
        { key:'tickets_resolved', emoji:'✅', q:'Tickets resolved?',                           type:'number' },
        { key:'avg_response_time',emoji:'⏱️', q:'Average response time (minutes)?',            type:'number' },
        { key:'escalations',      emoji:'⚠️', q:'Any escalations?',                            type:'dual_text',
          keys:['escalation_count','escalation_details'], labels:['Count','Details'] },
        { key:'notes',            emoji:'📝', q:'Notes?',                                      type:'textarea', opt:true },
    ],
    hr: [
        { key:'attendance',       emoji:'👥', q:'Attendance summary?',                         type:'textarea', ph:'Present / Absent / On Leave' },
        { key:'leave_requests',   emoji:'📋', q:'Leave requests processed?',                   type:'number' },
        { key:'interviews',       emoji:'🤝', q:'Interviews conducted?',                       type:'number' },
        { key:'issues',           emoji:'⚠️', q:'Issues or grievances?',                       type:'textarea', opt:true },
        { key:'notes',            emoji:'📝', q:'Notes?',                                      type:'textarea', opt:true },
    ],
    finance: [
        { key:'invoices',         emoji:'📄', q:'Invoices processed?',                         type:'number' },
        { key:'collected',        emoji:'💰', q:'Payments collected? Total ₹?',                type:'dual',
          keys:['collected_count','collected_amount'], labels:['Count','Amount ₹'] },
        { key:'pending',          emoji:'⏳', q:'Payments pending? Total ₹?',                  type:'dual',
          keys:['pending_count','pending_amount'], labels:['Count','Amount ₹'] },
        { key:'expenses',         emoji:'💸', q:'Expenses logged? Total ₹?',                   type:'dual',
          keys:['expenses_count','expenses_amount'], labels:['Count','Amount ₹'] },
        { key:'notes',            emoji:'📝', q:'Notes?',                                      type:'textarea', opt:true },
    ],
    developer: [
        { key:'tasks',            emoji:'✅', q:'Tasks completed today?',                      type:'textarea', ph:'List your tasks...' },
        { key:'commits',          emoji:'🔀', q:'PRs / commits pushed?',                       type:'number' },
        { key:'bugs_fixed',       emoji:'🐛', q:'Bugs fixed?',                                 type:'number' },
        { key:'blockers',         emoji:'🚧', q:'Any blockers?',                               type:'textarea', opt:true },
        { key:'notes',            emoji:'📝', q:'Notes?',                                      type:'textarea', opt:true },
    ],
    tester: [
        { key:'test_cases',       emoji:'🧪', q:'Test cases executed?',                        type:'number' },
        { key:'bugs_found',       emoji:'🐛', q:'Bugs found?',                                 type:'number' },
        { key:'bugs_verified',    emoji:'✅', q:'Bugs verified/closed?',                       type:'number' },
        { key:'blockers',         emoji:'🚧', q:'Any blockers?',                               type:'textarea', opt:true },
        { key:'notes',            emoji:'📝', q:'Notes?',                                      type:'textarea', opt:true },
    ],
    admin: [
        { key:'tasks',            emoji:'✅', q:'Key tasks completed today?',                  type:'textarea' },
        { key:'decisions',        emoji:'🎯', q:'Key decisions made?',                         type:'textarea', opt:true },
        { key:'notes',            emoji:'📝', q:'Notes?',                                      type:'textarea', opt:true },
    ],
};

let steps       = STEPS[ROLE] || STEPS.admin;
let currentStep = 0;
let wizardData  = {};

/* ── Update button label when date changes ── */
document.getElementById('reportDate').addEventListener('change', function () {
    const urlDate = new URLSearchParams(location.search).get('date') || '{{ today()->toDateString() }}';
    if (this.value !== urlDate) {
        // Unknown state for new date — show neutral label
        document.querySelector('#dateScreen .dr-btn-primary').textContent = 'Continue →';
    }
});

/* ── Start wizard ── */
function startWizard() {
    const date = document.getElementById('reportDate').value;
    if (!date) return;

    // If date changed, navigate to that date's URL
    const urlDate = new URLSearchParams(location.search).get('date') || '{{ today()->toDateString() }}';
    if (date !== urlDate) {
        location.href = '{{ route('daily-report') }}?date=' + date;
        return;
    }

    if (EXISTING) wizardData = { ...EXISTING };
    buildSteps();
    document.getElementById('dateScreen').style.display   = 'none';
    document.getElementById('wizardScreen').style.display = 'flex';
    document.getElementById('stepTotal').textContent      = steps.length;
    showStep(0);
}

/* ── Build step HTML ── */
function buildSteps() {
    const container = document.getElementById('stepsContainer');
    container.innerHTML = '';

    steps.forEach((s, i) => {
        const div = document.createElement('div');
        div.className = 'dr-step';
        div.id = 'step_' + i;

        const v = k => (wizardData[k] !== undefined ? wizardData[k] : '');
        let inp = '';

        switch (s.type) {
            case 'number':
                inp = `<input type="number" class="dr-wizard-input" inputmode="numeric" min="0"
                              data-key="${s.key}" value="${v(s.key)}" placeholder="0">`;
                break;
            case 'textarea':
                inp = `<textarea class="dr-wizard-textarea" data-key="${s.key}"
                                 placeholder="${s.ph || 'Type here...'}">${v(s.key)}</textarea>`;
                break;
            case 'dual':
                inp = `<div class="dr-dual">${s.keys.map((k, j) => `
                    <div class="dr-dual-field">
                        <label>${s.labels[j]}</label>
                        <input type="number" class="dr-wizard-input" inputmode="numeric" min="0"
                               data-key="${k}" value="${v(k)}" placeholder="0">
                    </div>`).join('')}</div>`;
                break;
            case 'dual_text':
                inp = `<div class="dr-dual">
                    <div class="dr-dual-field">
                        <label>${s.labels[0]}</label>
                        <input type="number" class="dr-wizard-input" inputmode="numeric" min="0"
                               data-key="${s.keys[0]}" value="${v(s.keys[0])}" placeholder="0">
                    </div>
                </div>
                <textarea class="dr-wizard-textarea" data-key="${s.keys[1]}"
                          placeholder="Details..." style="margin-top:10px">${v(s.keys[1])}</textarea>`;
                break;
            case 'repeatable':
                const existing = wizardData[s.key] || [{}];
                inp = `<div class="dr-repeatable-wrap" data-key="${s.key}">
                    ${existing.map(e => repeatRow(s, e)).join('')}
                </div>
                <button class="dr-add-btn" onclick="addRow(this,'${s.key}')">+ Add Another</button>`;
                break;
        }

        div.innerHTML = `<div class="dr-step-card">
            <span class="dr-emoji">${s.emoji}</span>
            <h2>${s.q}</h2>
            <div class="dr-step-input-wrap">${inp}</div>
        </div>`;
        container.appendChild(div);
    });
}

function repeatRow(s, e) {
    return `<div class="dr-repeatable-group">
        <div class="dr-repeatable-row">
            ${s.fields.map((f, fi) => {
                if (s.ftypes[fi] === 'select') {
                    const opts = (s.opts[f] || []);
                    return `<select class="dr-wizard-input" data-field="${f}">
                        <option value="">Select...</option>
                        ${opts.map(x => `<option value="${x}" ${e[f]===x?'selected':''}>${x}</option>`).join('')}
                    </select>`;
                }
                return `<input type="${s.ftypes[fi]}" class="dr-wizard-input"
                               data-field="${f}" placeholder="${s.labels[fi]}"
                               value="${e[f] || ''}"
                               ${s.ftypes[fi]==='number' ? 'inputmode="numeric" min="0"' : ''}>`;
            }).join('')}
            <button class="dr-remove-btn" onclick="this.closest('.dr-repeatable-group').remove()">×</button>
        </div>
    </div>`;
}

function addRow(btn, key) {
    const s = steps.find(x => x.key === key);
    btn.previousElementSibling.insertAdjacentHTML('beforeend', repeatRow(s, {}));
}

/* ── Show step ── */
function showStep(i) {
    currentStep = i;
    steps.forEach((_, j) => {
        document.getElementById('step_' + j).style.display = j === i ? 'block' : 'none';
    });

    document.getElementById('stepNum').textContent      = i + 1;
    document.getElementById('progressFill').style.width = ((i + 1) / steps.length * 100) + '%';

    const isFirst = i === 0;
    const isLast  = i === steps.length - 1;

    document.getElementById('btnBack').style.visibility = isFirst ? 'hidden' : 'visible';
    document.getElementById('btnNext').textContent      = isLast ? 'Submit ✅' : 'Next →';
    document.getElementById('btnSkip').style.display    = (steps[i].opt && !isLast) ? 'inline-flex' : 'none';

    setTimeout(() => {
        const inp = document.getElementById('step_' + i).querySelector('input,textarea,select');
        if (inp) inp.focus();
    }, 150);
}

/* ── Collect data from current step ── */
function collectData(i) {
    const el = document.getElementById('step_' + i);
    const s  = steps[i];

    if (s.type === 'repeatable') {
        const rows = [];
        el.querySelectorAll('.dr-repeatable-group').forEach(g => {
            const row = {};
            g.querySelectorAll('[data-field]').forEach(inp => { row[inp.dataset.field] = inp.value; });
            if (Object.values(row).some(v => v)) rows.push(row);
        });
        wizardData[s.key] = rows;
    } else {
        el.querySelectorAll('[data-key]').forEach(inp => {
            wizardData[inp.dataset.key] = inp.value;
        });
    }
}

/* ── Navigation ── */
function nextStep() {
    collectData(currentStep);
    if (currentStep === steps.length - 1) {
        submitReport();
        return;
    }
    showStep(++currentStep);
}

function prevStep() {
    collectData(currentStep);
    if (currentStep > 0) showStep(--currentStep);
}

/* ── Submit ── */
async function submitReport() {
    const btn = document.getElementById('btnNext');
    btn.disabled    = true;
    btn.textContent = 'Submitting...';

    try {
        const res = await fetch(STORE_URL, {
            method:  'POST',
            headers: {
                'Content-Type':  'application/json',
                'X-CSRF-TOKEN':  CSRF,
                'Accept':        'application/json',
            },
            body: JSON.stringify({
                date: document.getElementById('reportDate').value,
                data: wizardData,
            }),
        });
        const r = await res.json();

        if (r.ok) {
            if (r.updated) {
                document.getElementById('successEmoji').textContent = '✏️';
                document.getElementById('successTitle').textContent = 'Report Updated!';
                document.getElementById('successMsg').textContent   = 'Your daily report has been updated successfully.';
            }
            document.getElementById('wizardScreen').style.display  = 'none';
            document.getElementById('successScreen').style.display = 'flex';
            refreshRecentReports();
        } else {
            alert(r.error || 'Submission failed. Please try again.');
            btn.disabled    = false;
            btn.textContent = 'Submit ✅';
        }
    } catch (e) {
        alert('Network error. Please check your connection.');
        btn.disabled    = false;
        btn.textContent = 'Submit ✅';
    }
}

/* ── Refresh recent reports ── */
async function refreshRecentReports() {
    try {
        const res     = await fetch('{{ route('daily-report.recent') }}', {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        });
        const reports = await res.json();
        const wrap    = document.getElementById('recentReportsWrap');
        const list    = document.getElementById('recentReportsList');
        if (!wrap || !list) return;

        if (reports.length === 0) { wrap.style.display = 'none'; return; }

        const baseUrl = '{{ route('daily-report') }}';
        wrap.style.display = '';
        list.innerHTML = reports.map(r => `
            <a href="${baseUrl}?date=${r.date}"
               class="dr-recent-item ${r.is_today ? 'dr-recent-item-today' : 'dr-recent-item-normal'}">
                <span class="dr-recent-item-label">${r.label}${r.is_today ? ' (Today)' : ''}</span>
                <span class="dr-recent-item-right">
                    <span>${r.time}</span>
                    <span class="dr-recent-item-edit">Edit ✏️</span>
                </span>
            </a>`).join('');
    } catch (e) { /* silently ignore */ }
}

/* ── Return to date screen ── */
function goToDateScreen() {
    currentStep = 0;
    wizardData  = {};
    document.getElementById('successScreen').style.display = 'none';
    document.getElementById('dateScreen').style.display    = 'flex';
    // Reset success screen back to default for next submission
    document.getElementById('successEmoji').textContent  = '🎉';
    document.getElementById('successTitle').textContent  = 'Report Submitted!';
    document.getElementById('successMsg').textContent    = 'Your daily report has been saved.';
}

/* ── Enter key support ── */
document.addEventListener('keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey
        && document.getElementById('wizardScreen').style.display !== 'none'
        && document.activeElement?.tagName !== 'TEXTAREA') {
        e.preventDefault();
        nextStep();
    }
});
</script>
</x-layouts.app>
