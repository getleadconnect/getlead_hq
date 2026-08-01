<?php

namespace App\Http\Controllers\Hr;

use App\Http\Controllers\Controller;
use App\Models\HrApplication;
use App\Models\HrJobCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;


/**
 * HR › Job Application (Register).
 *
 * Ported/re-themed from hr_portal's hr_form_new.blade.php into the getlead_hq
 * design system. `index()` renders the multi-step application form; `store()`
 * validates it, saves the uploaded photo + CV to public/uploads/user_files,
 * and records the application in the hr_applications table.
 */
class RegisterController extends Controller
{
    /** Where applicant photos / CVs are stored (relative to public/). */
    private const UPLOAD_DIR = 'Resume-Getlead/';

    /** Show the job-application form. */
    public function index()
    {
        $jobCategories = HrJobCategory::orderBy('id', 'ASC')->get();

        return view('Hr.register', compact('jobCategories'));
    }

    /** Validate the form, store uploads, and record the application. */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'       => ['required', 'string', 'max:255'],
            'country_code'     => ['nullable', 'string', 'max:5'],
            'mobile'           => ['required', 'digits:10'],
            'email'            => ['required', 'email', 'max:255'],
            'year'             => ['required', 'integer'],
            'month'            => ['required', 'integer', 'between:1,12'],
            'day'              => ['required', 'integer', 'between:1,31'],
            'gender'           => ['required', 'in:Male,Female'],
            'marital_status'   => ['required', 'in:Single,Married,Divorced'],
            'technology_stack' => ['nullable', 'string', 'max:255'],
            'job_category_id'  => ['required', 'integer', 'exists:hr_job_category,id'],
            'father_name'      => ['required', 'string', 'max:255'],
            'address'          => ['required', 'string', 'max:500'],
            'pincode'          => ['required', 'digits:6'],
            'state'            => ['required', 'string', 'max:255'],
            'district'         => ['required', 'string', 'max:255'],
            'qualification'    => ['required', 'string', 'max:255'],
            'experience'       => ['required', 'in:Yes,No'],
            'years_experience' => ['nullable', 'required_if:experience,Yes', 'numeric', 'min:0', 'max:60'],
            'previous_employer'=> ['nullable', 'string', 'max:255'],
            'last_salary'      => ['nullable', 'numeric', 'min:0'],
            'expected_salary'  => ['required', 'numeric', 'min:0'],
            'changing_job'     => ['nullable', 'string', 'max:500'],
            'why_getlead'      => ['required', 'string', 'max:500'],
            'photo'            => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:4096'],
            'cv_file'          => ['required', 'file', 'mimes:pdf,doc,docx', 'max:8192'],
            'declaration'      => ['required'],
        ]);

        // Save uploads into public/uploads/user_files.
        $photoPath = $this->storeUpload($request->file('photo'), $request->first_name);
        $cvPath    = $this->storeUpload($request->file('cv_file'), $request->first_name);

        $dob = sprintf('%04d-%02d-%02d', (int) $request->year, (int) $request->month, (int) $request->day);

        HrApplication::create([
            'name'              => $request->first_name,
            'photo'             => $photoPath,
            'dob'               => $dob,
            'technology_stack'  => $request->technology_stack,
            'gender'            => $request->gender,
            'marital_status'    => $request->marital_status,
            'father_name'       => $request->father_name,
            'address'           => $request->address,
            'pincode'           => $request->pincode,
            'state'             => $request->state,
            'district'          => $request->district,
            'countrycode'       => $request->country_code ?: '91',
            'mobile'            => $request->mobile,
            'email'             => $request->email,
            'experience'        => $request->experience,
            'experience_years'  => $request->experience === 'Yes' ? $request->years_experience : null,
            'previous_employer' => $request->previous_employer,
            'last_drawn_salary' => $request->last_salary,
            'expected_salary'   => $request->expected_salary,
            'why_changing_job'  => $request->changing_job,
            'why_getlead'       => $request->why_getlead,
            'qualification'     => $request->qualification,
            'cv_file'           => $cvPath,
            'job_category_id'   => $request->job_category_id,
            'declaration'       => 'Agreed',
            'status'            => HrApplication::STATUS_NEW,
        ]);

        // Send the applicant to a dedicated "completed" page.
        return redirect()->route('hr.register.finish')
            ->with('hr_application_done', true)
            ->with('applicant_name', $request->first_name);
    }

    /** Application-submitted confirmation page (only reachable right after a submit). */
    public function finish(Request $request)
    {
        if (! $request->session()->get('hr_application_done')) {
            return redirect()->route('hr.register');
        }

        $name = (string) $request->session()->get('applicant_name', '');

        return view('Hr.finish', compact('name'));
    }

    /** Move an uploaded file into public/uploads/user_files with a unique name; return its relative path. */
    private function storeUpload($file, string $applicantName): string
    {
        /*$dir = public_path(self::UPLOAD_DIR);
        File::ensureDirectoryExists($dir);

        $slug = Str::slug($applicantName, '_') ?: 'applicant';
        $name = $slug . '_' . now()->timestamp . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();

        $file->move($dir, $name);*/
        
        $slug = Str::slug($applicantName, '_') ?: 'applicant';
        $name = $slug . '_' . now()->timestamp . '_' . Str::random(6) . '.' . $file->getClientOriginalExtension();
        Storage::disk('spaces')->put(self::UPLOAD_DIR . $name, file_get_contents($file), 'public');

        return $name;

    }
    
}
