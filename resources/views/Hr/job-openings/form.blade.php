<x-layouts.app :title="$isEdit ? 'Edit Job Opening' : 'Add New Job Opening'">
@push('styles')
<link rel="stylesheet" href="{{ asset('assets/summernote/summernote-lite.min.css') }}">
<style>
    .hr-wrap { padding: 24px 28px 48px; width: 100%; }

    .form-head { display:flex; align-items:center; justify-content:space-between; gap:14px; flex-wrap:wrap; margin-bottom:20px; }
    .back-btn { display:inline-flex; align-items:center; gap:6px; flex-shrink:0; padding:8px 14px;
                border:1px solid var(--border); border-radius:var(--radius-sm); background:var(--bg-card);
                font-size:13px; font-weight:500; color:var(--text-2); text-decoration:none; }
    .back-btn:hover { border-color:var(--text-3); color:var(--text-1); }
    .back-btn svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2; }
    .form-head h1 { font-size:20px; font-weight:700; letter-spacing:-.3px; color:var(--text-1); }
    .form-head p { font-size:13px; color:var(--text-3); margin-top:2px; }

    .btn { display:inline-flex; align-items:center; gap:6px; padding:9px 15px; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:500; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn-primary { background:var(--brand-red); color:#fff; } .btn-primary:hover { background:var(--brand-red-dark); }
    .btn-primary:disabled { opacity:.6; cursor:not-allowed; }
    .btn-secondary { background:var(--bg-card); color:var(--text-1); border-color:var(--border); } .btn-secondary:hover { border-color:var(--text-3); }

    .flash { padding:10px 14px; border-radius:var(--radius-sm); font-size:13px; margin-bottom:16px;
             background:var(--danger-soft); color:var(--danger-text); border:1px solid var(--danger-border); }

    .card { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); overflow:hidden; }
    .card-head { padding:18px 22px 0; }
    .card-head h2 { font-size:16px; font-weight:600; color:var(--text-1); }
    .card-body { padding:18px 22px 22px; }

    .grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:16px 20px; margin-bottom:20px; }
    @media(max-width:900px){ .grid-2{ grid-template-columns:1fr; } }
    .fld { display:flex; flex-direction:column; gap:6px; }
    .fld label { font-size:12.5px; font-weight:500; color:var(--text-2); }
    .fld .req { color:var(--brand-red); }
    .fld input, .fld select { height:38px; border:1px solid var(--border); border-radius:var(--radius-sm); padding:0 11px;
                              font-family:inherit; font-size:13px; color:var(--text-1); background:var(--bg-card); outline:none; width:100%; }
    .fld input:focus, .fld select:focus { border-color:var(--brand-red); box-shadow:0 0 0 3px var(--brand-red-soft); }
    .fld .err { font-size:11.5px; color:var(--danger); }

    .ed-fld { margin-bottom:20px; }
    .ed-fld > label { display:block; font-size:12.5px; font-weight:500; color:var(--text-2); margin-bottom:6px; }
    /* Summernote → match the app's palette/font */
    .note-editor.note-frame { border:1px solid var(--border); border-radius:var(--radius-sm); margin-bottom:0; }
    .note-editor.note-frame .note-editing-area .note-editable { font-family:inherit; font-size:13.5px; color:var(--text-1); background:var(--bg-card); }
    .note-toolbar { background:var(--bg-page); border-bottom:1px solid var(--border-soft); }

    .form-foot { display:flex; justify-content:flex-end; gap:8px; padding-top:4px; }
</style>
@endpush

<div class="hr-wrap">
    <div class="form-head">
        <div>
            <h1>{{ $isEdit ? 'Edit Job Opening' : 'Add New Job Opening' }}</h1>
            <p>{{ $isEdit ? 'Update job opening details' : 'Create a new job opening' }}</p>
        </div>
        <a href="{{ route('hr.job-openings') }}" class="back-btn" aria-label="Back to job openings">
            <svg viewBox="0 0 24 24"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back
        </a>
    </div>

    @if(isset($errors) && $errors->any())
        <div class="flash">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ $isEdit ? route('hr.job-openings.update', $opening->id) : route('hr.job-openings.store') }}" id="joForm">
        @csrf
        @if($isEdit) @method('PUT') @endif

        <div class="card">
            <div class="card-head"><h2>Job Opening Details</h2></div>
            <div class="card-body">
                <div class="grid-2">
                    <div class="fld">
                        <label for="job_title">Job Title <span class="req">*</span></label>
                        <input type="text" id="job_title" name="job_title" value="{{ old('job_title', $opening->job_title) }}" required>
                        @error('job_title')<span class="err">{{ $message }}</span>@enderror
                    </div>

                    <div class="fld">
                        <label for="job_category_id">Job Category</label>
                        <select id="job_category_id" name="job_category_id">
                            <option value="">Select job category</option>
                            @foreach($categories as $c)
                                <option value="{{ $c->id }}" @selected(old('job_category_id', $opening->job_category_id) == $c->id)>{{ $c->category_name }}</option>
                            @endforeach
                        </select>
                        @error('job_category_id')<span class="err">{{ $message }}</span>@enderror
                    </div>

                    <div class="fld">
                        <label for="job_designation_id">Job Position</label>
                        <select id="job_designation_id" name="job_designation_id">
                            <option value="">Select job position</option>
                            @foreach($designations as $d)
                                <option value="{{ $d->id }}" @selected(old('job_designation_id', $opening->job_designation_id) == $d->id)>{{ $d->designation_name }}</option>
                            @endforeach
                        </select>
                        @error('job_designation_id')<span class="err">{{ $message }}</span>@enderror
                    </div>

                    <div class="fld">
                        <label for="job_location">Job Location</label>
                        <input type="text" id="job_location" name="job_location" placeholder="e.g., New York, Remote"
                               value="{{ old('job_location', $opening->job_location) }}">
                        @error('job_location')<span class="err">{{ $message }}</span>@enderror
                    </div>

                    <div class="fld">
                        <label for="job_closing_date">Job Closing Date</label>
                        @php $closing = old('job_closing_date', $opening->job_closing_date?->format('Y-m-d')); @endphp
                        <input type="date" id="job_closing_date" name="job_closing_date" value="{{ $closing }}">
                        @error('job_closing_date')<span class="err">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="ed-fld">
                    <label for="job_description">Job Description</label>
                    <textarea id="job_description" name="job_description">{{ old('job_description', $opening->job_description) }}</textarea>
                </div>

                <div class="ed-fld">
                    <label for="job_details">Job Details</label>
                    <textarea id="job_details" name="job_details">{{ old('job_details', $opening->job_details) }}</textarea>
                </div>

                <div class="form-foot">
                    <a href="{{ route('hr.job-openings') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary" id="saveBtn">{{ $isEdit ? 'Update Job Opening' : 'Create Job Opening' }}</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script src="{{ asset('assets/js/jquery-3.7.1.js') }}"></script>
<script src="{{ asset('assets/summernote/summernote-lite.min.js') }}"></script>
<script>
(function () {
    const toolbar = [
        ['style', ['style']],
        ['font', ['bold', 'underline', 'clear']],
        ['color', ['color']],
        ['para', ['ul', 'ol', 'paragraph']],
        ['table', ['table']],
        ['insert', ['link']],
        ['view', ['fullscreen', 'codeview', 'help']],
    ];

    $('#job_description, #job_details').summernote({ height: 250, toolbar: toolbar });

    // Summernote hides the textarea but keeps it in sync on submit, so the POST carries the HTML.
    $('#joForm').on('submit', function () {
        $('#saveBtn').prop('disabled', true).text('Saving...');
    });
})();
</script>
</x-layouts.app>
