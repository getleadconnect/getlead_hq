<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Getlead HQ — Job Application</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&family=Geist:wght@400;500;600&display=swap" rel="stylesheet">

    <link href="{{url('assets/toastr/js/toastr.min.css')}}" rel="stylesheet" >

    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
        :root{
            --brand-red:#DC2626; --brand-red-dark:#B91C1C; --brand-red-soft:#FEF2F2; --brand-red-border:#FECACA;
            --text-1:#0F172A; --text-2:#475569; --text-3:#94A3B8;
            --border:#E5E7EB; --border-soft:#F1F5F9; --bg-page:#FAFAF9; --bg-card:#FFFFFF; --bg-neutral:#F3F4F6;
            --success:#15803D; --success-soft:#F0FDF4; --success-border:#BBF7D0; --success-text:#173404;
            --danger:#991B1B; --danger-soft:#FEF2F2; --danger-border:#FECACA; --danger-text:#501313;
            --radius-sm:6px; --radius-md:8px; --radius-lg:12px; --radius-xl:16px; --radius-pill:999px;
            --font:'Poppins',-apple-system,sans-serif;
        }
        html,body{height:100%;}
        body{font-family:var(--font);background:var(--bg-page);color:var(--text-1);-webkit-font-smoothing:antialiased;}
        .num{font-family:'Geist',var(--font);font-variant-numeric:tabular-nums;}

        .shell{display:flex;min-height:100vh;}

        /* Left brand panel */
        .brand-panel{
            width:38%;max-width:460px;flex-shrink:0;position:relative;
            background:linear-gradient(160deg,#B91C1C 0%,#DC2626 55%,#7F1D1D 100%);
            color:#fff;padding:48px 44px;display:flex;flex-direction:column;justify-content:space-between;overflow:hidden;
        }
        .brand-panel::after{content:"";position:absolute;right:-120px;bottom:-120px;width:340px;height:340px;border-radius:50%;background:rgba(255,255,255,.06);}
        .brand-panel::before{content:"";position:absolute;left:-80px;top:-80px;width:220px;height:220px;border-radius:50%;background:rgba(255,255,255,.05);}
        .brand-logo{display:flex;align-items:center;gap:12px;position:relative;z-index:1;}
        .brand-logo .mark{width:44px;height:44px;border-radius:12px;background:rgba(255,255,255,.15);display:flex;align-items:center;justify-content:center;}
        .brand-logo .mark svg{width:24px;height:24px;fill:#fff;}
        .brand-logo .name{font-size:20px;font-weight:700;letter-spacing:-.3px;}
        .brand-copy{position:relative;z-index:1;}
        .brand-copy h1{font-size:30px;font-weight:700;line-height:1.2;letter-spacing:-.5px;margin-bottom:14px;}
        .brand-copy p{font-size:14px;line-height:1.7;color:rgba(255,255,255,.85);max-width:340px;}
        .brand-steps{position:relative;z-index:1;display:flex;flex-direction:column;gap:14px;margin-top:8px;}
        .brand-step{display:flex;align-items:center;gap:12px;font-size:13.5px;color:rgba(255,255,255,.7);transition:color .2s;}
        .brand-step .dot{width:26px;height:26px;border-radius:50%;border:1.5px solid rgba(255,255,255,.4);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;flex-shrink:0;transition:all .2s;}
        .brand-step.active{color:#fff;font-weight:500;}
        .brand-step.active .dot{background:#fff;color:var(--brand-red-dark);border-color:#fff;}
        .brand-step.done .dot{background:rgba(255,255,255,.25);border-color:rgba(255,255,255,.5);color:#fff;}
        .brand-foot{position:relative;z-index:1;font-size:12px;color:rgba(255,255,255,.6);}
        .brand-foot a{color:#fff;text-decoration:none;}

        /* Right form area */
        .form-area{flex:1;display:flex;flex-direction:column;padding:40px 44px 32px;overflow-y:auto;}
        .form-inner{width:100%;max-width:640px;margin:0 auto;flex:1;display:flex;flex-direction:column;}

        /* Banners */
        .banner{padding:12px 16px;border-radius:var(--radius-md);font-size:13px;margin-bottom:18px;line-height:1.5;}
        .banner-success{background:var(--success-soft);color:var(--success-text);border:1px solid var(--success-border);}
        .banner-error{background:var(--danger-soft);color:var(--danger-text);border:1px solid var(--danger-border);}
        .banner-error ul{margin:6px 0 0 18px;}

        /* Progress */
        .progress-head{margin-bottom:26px;}
        .progress-meta{display:flex;justify-content:space-between;align-items:baseline;margin-bottom:10px;}
        .progress-meta .step-title{font-size:20px;font-weight:600;color:var(--text-1);letter-spacing:-.3px;}
        .progress-meta .step-count{font-size:12.5px;color:var(--text-3);font-weight:500;}
        .progress-track{height:6px;background:var(--bg-neutral);border-radius:var(--radius-pill);overflow:hidden;}
        .progress-fill{height:100%;background:var(--brand-red);border-radius:var(--radius-pill);width:25%;transition:width .35s ease;}

        /* Steps */
        .step-panel{display:none;animation:fade .3s ease;}
        .step-panel.active{display:block;}
        @keyframes fade{from{opacity:0;transform:translateY(6px);}to{opacity:1;transform:none;}}

        .grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
        .grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;}
        @media(max-width:560px){.grid,.grid-3{grid-template-columns:1fr;}}
        .field{display:flex;flex-direction:column;gap:6px;margin-bottom:16px;}
        .field.full{grid-column:1 / -1;}
        .field label{font-size:12.5px;font-weight:500;color:var(--text-2);}
        .field label .req{color:var(--brand-red);}
        .input,select.input,textarea.input{
            width:100%;border:1px solid var(--border);border-radius:var(--radius-sm);padding:10px 12px;
            font-family:inherit;font-size:13.5px;color:var(--text-1);background:var(--bg-card);outline:none;transition:border-color .12s,box-shadow .12s;
        }
        .input:focus,select.input:focus,textarea.input:focus{border-color:var(--brand-red);box-shadow:0 0 0 3px var(--brand-red-soft);}
        .input::placeholder{color:var(--text-3);}
        textarea.input{min-height:80px;resize:vertical;}
        .input.invalid{border-color:var(--brand-red);box-shadow:0 0 0 3px var(--brand-red-soft);}
        .mobile-group{display:flex;}
        .mobile-group .prefix{display:flex;align-items:center;padding:0 12px;background:var(--bg-neutral);border:1px solid var(--border);border-right:none;border-radius:var(--radius-sm) 0 0 var(--radius-sm);font-size:13px;color:var(--text-2);font-weight:500;}
        .mobile-group .input{border-radius:0 var(--radius-sm) var(--radius-sm) 0;}
        .hint{font-size:11.5px;color:var(--text-3);margin-top:2px;}

        /* File upload */
        .file-drop{border:1.5px dashed var(--border);border-radius:var(--radius-md);padding:18px;display:flex;align-items:center;gap:14px;cursor:pointer;transition:border-color .15s,background .15s;}
        .file-drop:hover{border-color:var(--brand-red);background:var(--brand-red-soft);}
        .file-drop .ico{width:40px;height:40px;border-radius:10px;background:var(--brand-red-soft);color:var(--brand-red);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
        .file-drop .ico svg{width:20px;height:20px;stroke:currentColor;fill:none;stroke-width:1.8;stroke-linecap:round;stroke-linejoin:round;}
        .file-drop .meta{font-size:13px;color:var(--text-2);}
        .file-drop .meta strong{color:var(--text-1);font-weight:600;display:block;font-size:13.5px;}
        .file-drop input[type=file]{display:none;}
        .file-name{font-size:12.5px;color:var(--success);margin-top:6px;font-weight:500;}

        .declaration{background:var(--bg-neutral);border:1px solid var(--border);border-radius:var(--radius-md);padding:14px 16px;font-size:12.5px;color:var(--text-2);line-height:1.6;margin-top:6px;}
        .declaration b{color:var(--text-1);}
        .check{display:flex;align-items:flex-start;gap:9px;margin-top:12px;font-size:13px;color:var(--text-2);cursor:pointer;}
        .check input{width:16px;height:16px;margin-top:1px;accent-color:var(--brand-red);flex-shrink:0;}

        /* Footer nav */
        .form-nav{display:flex;justify-content:space-between;align-items:center;margin-top:auto;padding-top:24px;border-top:1px solid var(--border-soft);}
        .btn{display:inline-flex;align-items:center;gap:7px;padding:11px 22px;border-radius:var(--radius-sm);font-family:inherit;font-size:13.5px;font-weight:500;cursor:pointer;border:1px solid transparent;text-decoration:none;transition:all .15s;}
        .btn svg{width:16px;height:16px;stroke:currentColor;fill:none;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;}
        .btn-primary{background:var(--brand-red);color:#fff;}
        .btn-primary:hover{background:var(--brand-red-dark);}
        .btn-ghost{background:transparent;color:var(--text-2);border-color:var(--border);}
        .btn-ghost:hover{background:var(--bg-neutral);color:var(--text-1);}
        .btn[hidden]{display:none;}

        @media(max-width:860px){
            .shell{flex-direction:column;}
            .brand-panel{width:100%;max-width:none;padding:32px 28px;flex-direction:row;align-items:center;gap:20px;flex-wrap:wrap;}
            .brand-copy h1{font-size:22px;margin-bottom:6px;}
            .brand-copy p,.brand-steps,.brand-foot{display:none;}
            .form-area{padding:28px 22px;}
        }
    </style>
</head>
<body>
@php
    $sdt = (int) date('Y') - 18;
    $edt = (int) date('Y') - 60;
    $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
@endphp
<div class="shell">
    {{-- Left brand panel --}}
    <aside class="brand-panel">
        <div class="brand-logo">
            <span class="mark"><svg viewBox="0 0 24 24"><path d="M13 3L4 14h7l-2 7 9-11h-7l2-7z"/></svg></span>
            <span class="name">Getlead HQ</span>
        </div>
        <div class="brand-copy">
            <h1>Join the Getlead team</h1>
            <p>Submit your details to apply for your desired position. Our HR team will review your application and get in touch with you soon.</p>
            <div class="brand-steps" id="brandSteps">
                <div class="brand-step active" data-s="1"><span class="dot">1</span> Personal details</div>
                <div class="brand-step" data-s="2"><span class="dot">2</span> Additional information</div>
                <div class="brand-step" data-s="3"><span class="dot">3</span> Professional information</div>
                <div class="brand-step" data-s="4"><span class="dot">4</span> Documents</div>
            </div>
        </div>
        <div class="brand-foot">© {{ date('Y') }} <a href="https://getleadcrm.com/" target="_blank" rel="noopener">Getlead</a></div>
    </aside>

    {{-- Right form --}}
    <main class="form-area">
        <form class="form-inner" id="hrForm" method="POST" action="{{ route('hr.register.store') }}" enctype="multipart/form-data" novalidate>
            @csrf
            <input type="hidden" name="country_code" value="91">

            @if(session('success'))
                <div class="banner banner-success">{{ session('success') }}</div>
            @endif
            @if(isset($errors) && $errors->any())
                <div class="banner banner-error">
                    <strong>Please correct the following:</strong>
                    <ul>
                        @foreach($errors->all() as $err)<li>{{ $err }}</li>@endforeach
                    </ul>
                </div>
            @endif

            <div class="progress-head">
                <div class="progress-meta">
                    <span class="step-title" id="stepTitle">Register now!</span>
                    <span class="step-count"><span id="stepNow">1</span> of 4</span>
                </div>
                <div class="progress-track"><div class="progress-fill" id="progressFill"></div></div>
            </div>

            {{-- Step 1 — Personal --}}
            <section class="step-panel active" data-step="1">
                <div class="grid">
                    <div class="field">
                        <label>First Name <span class="req">*</span></label>
                        <input type="text" name="first_name" class="input" placeholder="Name" value="{{ old('first_name') }}" required>
                    </div>
                    <div class="field">
                        <label>Mobile <span class="req">*</span></label>
                        <div class="mobile-group">
                            <span class="prefix">+91</span>
                            <input type="tel" name="mobile" class="input" placeholder="Mobile" inputmode="numeric" pattern="\d{10}" maxlength="10" value="{{ old('mobile') }}" required>
                        </div>
                    </div>
                    <div class="field full">
                        <label>Email <span class="req">*</span></label>
                        <input type="email" name="email" class="input" placeholder="Email" value="{{ old('email') }}" required>
                    </div>
                    <div class="field full">
                        <label>Date of Birth <span class="req">*</span></label>
                        <div class="grid-3">
                            <select name="year" class="input" required>
                                <option value="">YYYY</option>
                                @for($y = $sdt; $y >= $edt; $y--)<option value="{{ $y }}" {{ (string) old('year') === (string) $y ? 'selected' : '' }}>{{ $y }}</option>@endfor
                            </select>
                            <select name="month" class="input" required>
                                <option value="">MM</option>
                                @foreach($months as $i => $m)<option value="{{ $i + 1 }}" {{ (string) old('month') === (string) ($i + 1) ? 'selected' : '' }}>{{ $m }}</option>@endforeach
                            </select>
                            <select name="day" class="input" required>
                                <option value="">DD</option>
                                @for($d = 1; $d <= 31; $d++)<option value="{{ $d }}" {{ (string) old('day') === (string) $d ? 'selected' : '' }}>{{ $d }}</option>@endfor
                            </select>
                        </div>
                    </div>
                    <div class="field">
                        <label>Gender <span class="req">*</span></label>
                        <select name="gender" class="input" required>
                            <option value="">--select--</option>
                            <option value="Male" {{ old('gender') === 'Male' ? 'selected' : '' }}>Male</option>
                            <option value="Female" {{ old('gender') === 'Female' ? 'selected' : '' }}>Female</option>
                        </select>
                    </div>
                    <div class="field">
                        <label>Marital Status <span class="req">*</span></label>
                        <select name="marital_status" class="input" required>
                            <option value="">--select--</option>
                            @foreach(['Single','Married','Divorced'] as $ms)
                                <option value="{{ $ms }}" {{ old('marital_status') === $ms ? 'selected' : '' }}>{{ $ms }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="field full">
                        <label>Skills</label>
                        <input type="text" name="technology_stack" class="input" placeholder="Eg: Laravel, React JS, UI/UX Designer" value="{{ old('technology_stack') }}">
                    </div>
                    <div class="field full">
                        <label>Applied For <span class="req">*</span></label>
                        <select name="job_category_id" class="input" required>
                            <option value="">--Select--</option>
                            @foreach($jobCategories as $row)
                                <option value="{{ $row->id }}" {{ (string) old('job_category_id') === (string) $row->id ? 'selected' : '' }}>{{ $row->category_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </section>

            {{-- Step 2 — Additional --}}
            <section class="step-panel" data-step="2">
                <div class="grid">
                    <div class="field full">
                        <label>Father Name <span class="req">*</span></label>
                        <input type="text" name="father_name" class="input" placeholder="Father Name" value="{{ old('father_name') }}" required>
                    </div>
                    <div class="field full">
                        <label>Address <span class="req">*</span></label>
                        <input type="text" name="address" class="input" placeholder="Address" value="{{ old('address') }}" required>
                    </div>
                    <div class="field">
                        <label>Pin Code <span class="req">*</span></label>
                        <input type="text" name="pincode" class="input" placeholder="Pin Code" inputmode="numeric" pattern="\d{6}" maxlength="6" value="{{ old('pincode') }}" required>
                    </div>
                    <div class="field">
                        <label>State <span class="req">*</span></label>
                        <input type="text" name="state" class="input" placeholder="State" value="{{ old('state') }}" required>
                    </div>
                    <div class="field full">
                        <label>District <span class="req">*</span></label>
                        <input type="text" name="district" class="input" placeholder="District" value="{{ old('district') }}" required>
                    </div>
                </div>
            </section>

            {{-- Step 3 — Professional --}}
            <section class="step-panel" data-step="3">
                <div class="grid">
                    <div class="field full">
                        <label>Qualification <span class="req">*</span></label>
                        <input type="text" name="qualification" class="input" placeholder="Qualification" value="{{ old('qualification') }}" required>
                    </div>
                    <div class="field">
                        <label>Are You Experienced? <span class="req">*</span></label>
                        <select name="experience" id="experience" class="input" required>
                            <option value="">--Select--</option>
                            <option value="Yes" {{ old('experience') === 'Yes' ? 'selected' : '' }}>Yes</option>
                            <option value="No" {{ old('experience') === 'No' ? 'selected' : '' }}>No</option>
                        </select>
                    </div>
                    <div class="field">
                        <label id="yearsLabel">If Yes, How Many Years?</label>
                        <input type="number" name="years_experience" id="years_experience" class="input" placeholder="Years" min="0" value="{{ old('years_experience') }}">
                    </div>
                    <div class="field full">
                        <label>Previous Employer</label>
                        <input type="text" name="previous_employer" class="input" placeholder="Employer" value="{{ old('previous_employer') }}">
                    </div>
                    <div class="field">
                        <label>Last Drawn Salary</label>
                        <input type="number" name="last_salary" class="input" placeholder="Last Salary" min="0" value="{{ old('last_salary') }}">
                    </div>
                    <div class="field">
                        <label>Expected Salary <span class="req">*</span></label>
                        <input type="number" name="expected_salary" class="input" placeholder="Expected Salary" min="0" value="{{ old('expected_salary') }}" required>
                    </div>
                    <div class="field full">
                        <label>Why changing Job?</label>
                        <input type="text" name="changing_job" class="input" placeholder="Details" value="{{ old('changing_job') }}">
                    </div>
                    <div class="field full">
                        <label>Why Getlead? <span class="req">*</span></label>
                        <input type="text" name="why_getlead" class="input" placeholder="Why Getlead" value="{{ old('why_getlead') }}" required>
                    </div>
                </div>
            </section>

            {{-- Step 4 — Documents --}}
            <section class="step-panel" data-step="4">
                <div class="field full">
                    <label>Upload Your Photo <span class="req">*</span></label>
                    <label class="file-drop" for="photo">
                        <span class="ico"><svg viewBox="0 0 24 24"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg></span>
                        <span class="meta"><strong>Upload photo</strong>JPG, JPEG or PNG (max 4 MB)</span>
                        <input type="file" name="photo" id="photo" accept=".jpg,.jpeg,.jpe,.png" required>
                    </label>
                    <div class="file-name" id="photoName" hidden></div>
                </div>
                <div class="field full">
                    <label>Upload Your CV <span class="req">*</span></label>
                    <label class="file-drop" for="cv_file">
                        <span class="ico"><svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
                        <span class="meta"><strong>Upload CV</strong>PDF, DOC or DOCX (max 8 MB)</span>
                        <input type="file" name="cv_file" id="cv_file" accept=".pdf,.doc,.docx" required>
                    </label>
                    <div class="file-name" id="cvName" hidden></div>
                </div>
                <div class="field full">
                    <label><b>Declaration</b></label>
                    <div class="declaration">I hereby declare that all the statements made in this application are true and complete to the best of my knowledge and belief.</div>
                    <label class="check">
                        <input type="checkbox" name="declaration" value="Agreed" {{ old('declaration') ? 'checked' : '' }} required>
                        I agree to the declaration above.
                    </label>
                </div>
            </section>

            {{-- Nav --}}
            <div class="form-nav">
                <button type="button" class="btn btn-ghost" id="prevBtn" hidden>
                    <svg viewBox="0 0 24 24"><polyline points="15 18 9 12 15 6"/></svg> Prev
                </button>
                <span></span>
                <div>
                    <button type="button" class="btn btn-primary" id="nextBtn">
                        Next <svg viewBox="0 0 24 24"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                    <button type="submit" class="btn btn-primary" id="submitBtn" hidden>
                        <svg viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg> Submit Application
                    </button>
                </div>
            </div>
        </form>
    </main>
</div>

<script src="{{url('assets/toastr/js/toastr.min.js')}}"></script>

@if(Session::get('success'))
	<script>
		toastr.success("{{Session::get('success')}}");
	</script>
@endif

@if (Session::get('fail'))
	<script>
		toastr.error("{{Session::get('fail')}}");
	</script>
@endif

<script>


(function () {
    const form   = document.getElementById('hrForm');
    const panels = Array.from(form.querySelectorAll('.step-panel'));
    const titles = ['Register now!', 'Additional Information!', 'Professional Information!', 'Upload Documents'];
    const total  = panels.length;
    let current  = 1;

    const fill   = document.getElementById('progressFill');
    const title  = document.getElementById('stepTitle');
    const stepNow= document.getElementById('stepNow');
    const prevBtn= document.getElementById('prevBtn');
    const nextBtn= document.getElementById('nextBtn');
    const submit = document.getElementById('submitBtn');
    const brandSteps = Array.from(document.querySelectorAll('.brand-step'));

    function render() {
        panels.forEach(p => p.classList.toggle('active', +p.dataset.step === current));
        fill.style.width = (current / total * 100) + '%';
        title.textContent = titles[current - 1];
        stepNow.textContent = current;
        prevBtn.hidden = current === 1;
        nextBtn.hidden = current === total;
        submit.hidden  = current !== total;
        brandSteps.forEach(b => {
            const s = +b.dataset.s;
            b.classList.toggle('active', s === current);
            b.classList.toggle('done', s < current);
        });
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    // Validate only the fields inside the current step before advancing.
    function validateStep() {
        const panel = panels[current - 1];
        let ok = true, firstBad = null;
        panel.querySelectorAll('input, select, textarea').forEach(el => {
            el.classList.remove('invalid');
            if (el.disabled) return;
            if (!el.checkValidity()) {
                ok = false;
                el.classList.add('invalid');
                if (!firstBad) firstBad = el;
            }
        });
        if (firstBad) firstBad.focus();
        return ok;
    }

    nextBtn.addEventListener('click', function () {
        if (!validateStep()) return;
        if (current < total) { current++; render(); }
    });
    prevBtn.addEventListener('click', function () {
        if (current > 1) { current--; render(); }
    });

    // Experience → require years only when "Yes".
    const exp  = document.getElementById('experience');
    const years= document.getElementById('years_experience');
    const yearsLabel = document.getElementById('yearsLabel');
    function syncExperience() {
        const yes = exp.value === 'Yes';
        years.required = yes;
        yearsLabel.innerHTML = yes ? 'If Yes, How Many Years? <span class="req">*</span>' : 'If Yes, How Many Years?';
        if (!yes) { years.value = ''; years.classList.remove('invalid'); }
    }
    exp.addEventListener('change', syncExperience);
    syncExperience();

    // File pickers: show chosen name + validate extension.
    function wireFile(id, labelId, re, msg) {
        const input = document.getElementById(id);
        const out   = document.getElementById(labelId);
        input.addEventListener('change', function () {
            const f = input.files[0];
            if (!f) { out.hidden = true; return; }
            if (!re.test(f.name)) { alert(msg); input.value = ''; out.hidden = true; return; }
            out.textContent = '✓ ' + f.name;
            out.hidden = false;
        });
    }
    wireFile('photo', 'photoName', /(\.jpg|\.jpeg|\.jpe|\.png)$/i, 'Invalid file type — select a JPG, JPEG or PNG image only.');
    wireFile('cv_file', 'cvName', /(\.pdf|\.doc|\.docx)$/i, 'Invalid file type — select a PDF, DOC or DOCX file only.');

    // Final guard: validate the last step on submit.
    form.addEventListener('submit', function (e) {
        if (!validateStep()) e.preventDefault();
    });

    // On server-side validation error, jump to the first step that has an errored field.
    const serverErrors = @json(isset($errors) ? $errors->keys() : []);
    render();
    if (serverErrors.length) {
        for (let i = 0; i < panels.length; i++) {
            const hit = panels[i].querySelector(serverErrors.map(n => '[name="' + n + '"]').join(','));
            if (hit) { current = i + 1; render();
                serverErrors.forEach(n => { const el = form.querySelector('[name="' + n + '"]'); if (el && panels[i].contains(el)) el.classList.add('invalid'); });
                break;
            }
        }
    }
})();
</script>
</body>
</html>
