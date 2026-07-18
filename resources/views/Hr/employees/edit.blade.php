<x-layouts.app title="Edit Employee">
@push('styles')
<style>
    .hr-wrap { padding: 24px 28px 48px; width: 100%; max-width: 1000px; }
    .hr-head { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom: 20px; }
    .hr-eyebrow { font-size: 11px; font-weight: 600; letter-spacing: .09em; text-transform: uppercase; color: var(--brand-red); }
    .hr-head h1 { font-size: 24px; font-weight: 600; letter-spacing: -.5px; color: var(--text-1); margin-top: 4px; }
    .hr-head p { font-size: 13px; color: var(--text-2); margin-top: 4px; }

    .btn { display:inline-flex; align-items:center; gap:6px; padding:9px 16px; border-radius:var(--radius-sm); font-family:inherit; font-size:13px; font-weight:500; cursor:pointer; border:1px solid transparent; text-decoration:none; }
    .btn svg { width:15px; height:15px; stroke:currentColor; fill:none; stroke-width:2; stroke-linecap:round; stroke-linejoin:round; }
    .btn-primary { background: var(--brand-red); color:#fff; } .btn-primary:hover { background: var(--brand-red-dark); }
    .btn-secondary { background: var(--bg-card); color: var(--text-1); border-color: var(--border); } .btn-secondary:hover { border-color: var(--text-3); }

    .flash { padding:11px 15px; border-radius:var(--radius-sm); font-size:13px; margin-bottom:16px; }
    .flash-error { background: var(--danger-soft); color: var(--danger-text); border:1px solid var(--danger-border); }
    .flash-error ul { margin:6px 0 0 18px; }

    .sec { background:var(--bg-card); border:1px solid var(--border); border-radius:var(--radius-lg); padding:22px; margin-bottom:18px; }
    .sec-title { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:.06em; color:var(--brand-red); margin-bottom:16px; padding-bottom:10px; border-bottom:1px solid var(--border-soft); }
    .grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px 16px; }
    @media(max-width:720px){ .grid{ grid-template-columns:1fr 1fr; } }
    @media(max-width:480px){ .grid{ grid-template-columns:1fr; } }
    .field { display:flex; flex-direction:column; gap:6px; }
    .field.full { grid-column:1/-1; }
    .field label { font-size:12px; font-weight:500; color:var(--text-2); }
    .field label .req { color:var(--brand-red); }
    .input, select.input, textarea.input { width:100%; border:1px solid var(--border); border-radius:var(--radius-sm); padding:9px 11px; font-family:inherit; font-size:13px; color:var(--text-1); background:var(--bg-card); outline:none; }
    .input:focus, select.input:focus, textarea.input:focus { border-color:var(--brand-red); box-shadow:0 0 0 3px var(--brand-red-soft); }
    textarea.input { min-height:64px; resize:vertical; }
    input[type=file].input { padding:7px 10px; font-size:12px; }
    .file-hint { font-size:11px; color:var(--text-3); margin-top:2px; }
    .file-hint a { color:var(--brand-red-dark); }

    .profile-row { display:flex; align-items:center; gap:16px; }
    .avatar-prev { width:72px; height:72px; border-radius:50%; object-fit:cover; border:1px solid var(--border); background:var(--bg-neutral-2); display:flex; align-items:center; justify-content:center; color:var(--text-3); flex-shrink:0; overflow:hidden; }
    .avatar-prev img { width:100%; height:100%; object-fit:cover; }
    .avatar-prev svg { width:28px; height:28px; stroke:currentColor; fill:none; stroke-width:1.6; }

    .form-actions { display:flex; justify-content:flex-end; gap:10px; position:sticky; bottom:0; background:linear-gradient(transparent, var(--bg-page) 40%); padding:14px 0 2px; }
</style>
@endpush

@php
    $v  = fn($k) => old($k, $emp->$k);
    $vd = fn($k) => old($k, $emp->$k ? \Illuminate\Support\Carbon::parse($emp->$k)->format('Y-m-d') : '');
    $fileUrl = fn($f) => $f ? asset('uploads/user_files/'.$f) : null;
    $profileUrl = $emp->profile_image ? asset('uploads/user_files/'.$emp->profile_image) : null;
@endphp

<div class="hr-wrap">
    <div class="hr-head">
        <div>
            <div class="hr-eyebrow">HR Management</div>
            <h1>Edit Employee</h1>
            <p>Update the details for {{ $emp->full_name }}.</p>
        </div>
        <a href="{{ route('hr.employees.show', $emp->id) }}" class="btn btn-secondary">
            <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Back
        </a>
    </div>

    @if(isset($errors) && $errors->any())
        <div class="flash flash-error">
            <strong>Please correct the following:</strong>
            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
    @endif

    <form method="POST" action="{{ route('hr.employees.update', $emp->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- A. Personal --}}
        <div class="sec">
            <div class="sec-title">A. Personal Information</div>
            <div class="field full profile-row" style="margin-bottom:16px;">
                <div class="avatar-prev" id="avatarPrev">
                    @if($profileUrl)
                        <img src="{{ $profileUrl }}" alt="photo">
                    @else
                        <svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    @endif
                </div>
                <label style="flex:1;">
                    <span style="display:block;font-size:12px;font-weight:500;color:var(--text-2);margin-bottom:6px;">Profile Photo</span>
                    <input type="file" name="profile_image" id="profileInput" class="input" accept=".jpg,.jpeg,.png">
                    <span class="file-hint">Leave blank to keep the current photo.</span>
                </label>
            </div>
            <div class="grid">
                <div class="field"><label>Full Name <span class="req">*</span></label><input type="text" name="full_name" class="input" value="{{ $v('full_name') }}" required></div>
                <div class="field"><label>Employee ID <span class="req">*</span></label><input type="text" name="employee_id" class="input" value="{{ $v('employee_id') }}" required></div>
                <div class="field"><label>Date of Birth</label><input type="date" name="date_of_birth" class="input" value="{{ $vd('date_of_birth') }}"></div>
                <div class="field">
                    <label>Gender</label>
                    <select name="gender" class="input">
                        <option value="">--select--</option>
                        @foreach(['Male','Female','Other'] as $g)<option value="{{ $g }}" @selected($v('gender')===$g)>{{ $g }}</option>@endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Marital Status</label>
                    <select name="marital_status" class="input">
                        <option value="">--select--</option>
                        @foreach(['Single','Married','Divorced','Widowed'] as $m)<option value="{{ $m }}" @selected($v('marital_status')===$m)>{{ $m }}</option>@endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Qualification</label>
                    <select name="qualification_id" class="input">
                        <option value="">--select--</option>
                        @foreach($qualifications as $q)<option value="{{ $q->id }}" @selected((string)$v('qualification_id')===(string)$q->id)>{{ $q->qualification }}</option>@endforeach
                    </select>
                </div>
                <div class="field"><label>Technology Stack</label><input type="text" name="technology_stack" class="input" value="{{ $v('technology_stack') }}" placeholder="Eg: Laravel, React"></div>
                <div class="field"><label>Join Date</label><input type="date" name="join_date" class="input" value="{{ $vd('join_date') }}"></div>
                <div class="field"><label>Relieving Date</label><input type="date" name="releaving_date" id="releaving_date" rele class="input" value="{{ $vd('releaving_date') }}"></div>
                <div class="field"><label>Status</label>
                    <select name="status" id="status" class="input" style="font-weight:600;">
                        <option value="">--select--</option>
                        <option value="1" @if($emp->status==1) selected @endif>Active</option>
                        <option value="0" @if($emp->status==0) selected @endif>Inactive</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- B. Contact --}}
        <div class="sec">
            <div class="sec-title">B. Contact Information</div>
            <div class="grid">
                <div class="field"><label>Mobile Number <span class="req">*</span></label><input type="text" name="mobile_number" class="input" value="{{ $v('mobile_number') }}" required></div>
                <div class="field"><label>Alternative Number 1</label><input type="text" name="alternative_number_1" class="input" value="{{ $v('alternative_number_1') }}"></div>
                <div class="field"><label>Alternative Number 2</label><input type="text" name="alternative_number_2" class="input" value="{{ $v('alternative_number_2') }}"></div>
                <div class="field"><label>Email</label><input type="email" name="email" class="input" value="{{ $v('email') }}"></div>
                <div class="field full"><label>Address</label><textarea name="address" class="input">{{ $v('address') }}</textarea></div>
                <div class="field"><label>City</label><input type="text" name="city" class="input" value="{{ $v('city') }}"></div>
                <div class="field"><label>State</label><input type="text" name="state" class="input" value="{{ $v('state') }}"></div>
                <div class="field"><label>Country</label><input type="text" name="country" class="input" value="{{ $v('country') }}"></div>
                <div class="field"><label>Emergency Contact Name</label><input type="text" name="emergency_contact_name" class="input" value="{{ $v('emergency_contact_name') }}"></div>
                <div class="field"><label>Emergency Contact Number</label><input type="text" name="emergency_contact_number" class="input" value="{{ $v('emergency_contact_number') }}"></div>
                <div class="field"><label>Relationship</label><input type="text" name="relationship" class="input" value="{{ $v('relationship') }}"></div>
            </div>
        </div>

        {{-- C. Employment --}}
        <div class="sec">
            <div class="sec-title">C. Employment Details</div>
            <div class="grid">
                <div class="field"><label>Job Title</label><input type="text" name="job_title" class="input" value="{{ $v('job_title') }}"></div>
                <div class="field">
                    <label>Department</label>
                    <select name="department_id" class="input">
                        <option value="">--select--</option>
                        @foreach($departments as $d)<option value="{{ $d->id }}" @selected((string)$v('department_id')===(string)$d->id)>{{ $d->department_name }}</option>@endforeach
                    </select>
                </div>
                <div class="field">
                    <label>Designation</label>
                    <select name="designation_id" class="input">
                        <option value="">--select--</option>
                        @foreach($designations as $ds)<option value="{{ $ds->id }}" @selected((string)$v('designation_id')===(string)$ds->id)>{{ $ds->designation_name }}</option>@endforeach
                    </select>
                </div>
                <div class="field"><label>Date of Hire</label><input type="date" name="date_of_hire" class="input" value="{{ $vd('date_of_hire') }}"></div>
                <div class="field"><label>Work Location</label><input type="text" name="work_location" class="input" value="{{ $v('work_location') }}"></div>
                <div class="field"><label>Salary</label><input type="number" name="salary" class="input" value="{{ $v('salary') }}" min="0" step="0.01"></div>
                <div class="field"><label>HRA (%)</label><input type="number" name="hra" class="input" value="{{ $v('hra') }}" min="0" max="100" step="0.01"></div>
                <div class="field"><label>TA (%)</label><input type="number" name="ta" class="input" value="{{ $v('ta') }}" min="0" max="100" step="0.01"></div>
            </div>
        </div>

        {{-- D. Documents & Bank --}}
        <div class="sec">
            <div class="sec-title">D. Documents &amp; Bank Details</div>
            <div class="grid">
                <div class="field"><label>Aadhar Number</label><input type="text" name="aadhar_number" class="input" value="{{ $v('aadhar_number') }}"></div>
                <div class="field">
                    <label>Aadhar File</label><input type="file" name="aadhar_file" class="input" accept=".pdf,.jpg,.jpeg,.png">
                    @if($fileUrl($emp->aadhar_file))<span class="file-hint">Current: <a href="{{ $fileUrl($emp->aadhar_file) }}" target="_blank">view</a> · leave blank to keep</span>@endif
                </div>
                <div class="field"><label>PAN Number</label><input type="text" name="pancard_number" class="input" value="{{ $v('pancard_number') }}"></div>
                <div class="field">
                    <label>PAN File</label><input type="file" name="pancard_file" class="input" accept=".pdf,.jpg,.jpeg,.png">
                    @if($fileUrl($emp->pancard_file))<span class="file-hint">Current: <a href="{{ $fileUrl($emp->pancard_file) }}" target="_blank">view</a> · leave blank to keep</span>@endif
                </div>
                <div class="field">
                    <label>Experience Certificate</label><input type="file" name="experience_certificate" class="input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    @if($fileUrl($emp->experience_certificate))<span class="file-hint">Current: <a href="{{ $fileUrl($emp->experience_certificate) }}" target="_blank">view</a> · leave blank to keep</span>@endif
                </div>
                <div class="field">
                    <label>Other Document</label><input type="file" name="other_document" class="input" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    @if($fileUrl($emp->other_document))<span class="file-hint">Current: <a href="{{ $fileUrl($emp->other_document) }}" target="_blank">view</a> · leave blank to keep</span>@endif
                </div>
                <div class="field"><label>Bank Name</label><input type="text" name="bank_name" class="input" value="{{ $v('bank_name') }}"></div>
                <div class="field"><label>Account Number</label><input type="text" name="account_number" class="input" value="{{ $v('account_number') }}"></div>
                <div class="field"><label>IFSC Code</label><input type="text" name="ifsc_code" class="input" value="{{ $v('ifsc_code') }}"></div>
            </div>
        </div>

        <div class="form-actions">
            <a href="{{ route('hr.employees.show', $emp->id) }}" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">
                <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Update Employee
            </button>
        </div>
    </form>
</div>

<script src="{{ asset('assets/js/jquery-3.7.1.js') }}"></script>
<script>


(function () {
    const input = document.getElementById('profileInput');
    const prev = document.getElementById('avatarPrev');
    if (input) input.addEventListener('change', function () {
        const f = input.files[0];
        if (!f) return;
        prev.innerHTML = '<img src="' + URL.createObjectURL(f) + '">';
    });
})();

$("#status").change(function(){

var today=new Date().toISOString().slice(0, 10);
var rdate=$("#releaving_date").val();

if($(this).val()==0 && rdate=="")
   $("#releaving_date").val(today);
else
   $("#releaving_date").val(""); 

});


</script>
</x-layouts.app>
