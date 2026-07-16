<x-layouts.app title="HR Job Openings">
@push('styles')
<style>
    .hr-wrap { padding: 24px 28px 48px; width: 100%; }
    .hr-head { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom: 20px; }
    .hr-eyebrow { font-size: 11px; font-weight: 600; letter-spacing: .09em; text-transform: uppercase; color: var(--brand-red); }
    .hr-head h1 { font-size: 24px; font-weight: 600; letter-spacing: -.5px; color: var(--text-1); margin-top: 4px; }
    .hr-head p { font-size: 13px; color: var(--text-2); margin-top: 4px; }

    .btn { display:inline-flex; align-items:center; gap:6px; padding:9px 15px; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:500; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2; }
    .btn-primary { background: var(--brand-red); color:#fff; } .btn-primary:hover { background: var(--brand-red-dark); }
    .btn-secondary { background: var(--bg-card); color: var(--text-1); border-color: var(--border); } .btn-secondary:hover { border-color: var(--text-3); }
    .btn-sm { padding:5px 11px; font-size:12px; }

    .flash { padding:10px 14px; border-radius:var(--radius-sm); font-size:13px; margin-bottom:16px; }
    .flash-success { background: var(--success-soft); color: var(--success-text); border:1px solid var(--success-border); }

    .add-card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:16px; margin-bottom:18px; display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap; }
    .add-card .fld { display:flex; flex-direction:column; gap:5px; flex:1; min-width:220px; }
    .add-card label { font-size:11.5px; font-weight:500; color:var(--text-2); }
    .add-card input { height:38px; border:1px solid var(--border); border-radius:var(--radius-sm); padding:0 11px; font-family:inherit; font-size:13px; color:var(--text-1); outline:none; }
    .add-card input:focus { border-color:var(--brand-red); box-shadow:0 0 0 3px var(--brand-red-soft); }

    .table-card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); overflow:hidden; }
    table.tbl { width:100%; border-collapse:collapse; }
    table.tbl thead th { text-align:left; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.05em; color:var(--text-3); padding:12px 16px; background:var(--bg-page); border-bottom:1px solid var(--border); }
    table.tbl tbody td { font-size:13px; color:var(--text-2); padding:13px 16px; border-bottom:1px solid var(--border-soft); }
    table.tbl tbody tr:last-child td { border-bottom:none; }
    table.tbl tbody tr:hover { background:var(--bg-page); }
    .name-cell { color:var(--text-1); font-weight:500; }
    .num-cell { text-align:right; font-variant-numeric:tabular-nums; }
    .badge { display:inline-block; padding:3px 10px; border-radius:var(--radius-pill); font-size:11.5px; font-weight:600; }
    .b-open { background:var(--success-soft); color:var(--success-text); border:1px solid var(--success-border); }
    .b-closed { background:var(--bg-neutral); color:var(--text-2); border:1px solid var(--border); }
    #toast { position:fixed; right:20px; bottom:20px; z-index:1100; display:none; padding:12px 16px; border-radius:var(--radius-md); font-size:13px; color:#fff; background:var(--success); box-shadow:0 12px 32px rgba(0,0,0,.2); }
</style>
@endpush

<div class="hr-wrap">
    <div class="hr-head">
        <div>
            <div class="hr-eyebrow">HR Management</div>
            <h1>Job Openings</h1>
            <p>Roles applicants can apply for. These populate the “Applied For” list on the public application form.</p>
        </div>
    </div>

    @if(session('success'))
        <div class="flash flash-success">{{ session('success') }}</div>
    @endif
    @if(isset($errors) && $errors->any())
        <div class="flash" style="background:var(--danger-soft);color:var(--danger-text);border:1px solid var(--danger-border);">{{ $errors->first() }}</div>
    @endif

    <form class="add-card" method="POST" action="{{ route('hr.job-openings.store') }}">
        @csrf
        <div class="fld">
            <label>New Job Opening</label>
            <input type="text" name="category_name" placeholder="e.g. Backend Developer" required>
        </div>
        <button type="submit" class="btn btn-primary">
            <svg viewBox="0 0 24 24"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
            Add Opening
        </button>
    </form>

    <div class="table-card">
        <table class="tbl">
            <thead>
                <tr><th>Role</th><th class="num-cell">Applications</th><th>Status</th><th style="text-align:right;">Action</th></tr>
            </thead>
            <tbody>
                @forelse($openings as $o)
                    <tr data-id="{{ $o->id }}">
                        <td class="name-cell">{{ $o->category_name }}</td>
                        <td class="num-cell">
                            <a href="{{ route('hr.applications') }}" class="link-name" style="color:var(--brand-red-dark);text-decoration:none;">{{ number_format($o->applications_count) }}</a>
                        </td>
                        <td>
                            <span class="badge {{ $o->status ? 'b-open' : 'b-closed' }} js-badge">{{ $o->status ? 'Open' : 'Closed' }}</span>
                        </td>
                        <td style="text-align:right;">
                            <button type="button" class="btn btn-sm btn-secondary js-toggle" data-id="{{ $o->id }}">
                                {{ $o->status ? 'Close' : 'Reopen' }}
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" style="text-align:center;color:var(--text-3);padding:28px;">No job openings yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div id="toast"></div>

<script>
(function () {
    const CSRF = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const base = "{{ url('hr/job-openings') }}";
    function toast(m){ const t=document.getElementById('toast'); t.textContent=m; t.style.display='block'; setTimeout(()=>t.style.display='none',2200); }

    document.querySelectorAll('.js-toggle').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.dataset.id;
            fetch(base + '/' + id + '/toggle', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            }).then(r => r.json()).then(res => {
                if (!res.status) return toast('Could not update.');
                const row = this.closest('tr');
                const badge = row.querySelector('.js-badge');
                badge.textContent = res.active ? 'Open' : 'Closed';
                badge.className = 'badge js-badge ' + (res.active ? 'b-open' : 'b-closed');
                this.textContent = res.active ? 'Close' : 'Reopen';
                toast('Job opening updated.');
            }).catch(() => toast('Could not update.'));
        });
    });
})();
</script>
</x-layouts.app>
