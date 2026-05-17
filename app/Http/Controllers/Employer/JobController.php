<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\Job;
use App\Services\ApprovalService;
use App\Services\EmployerCandidateDataAccessService;
use App\Models\Setting;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function __construct(
        private readonly EmployerCandidateDataAccessService $candidateDataAccessService,
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $employer = $this->currentEmployer();

        $jobs = $employer->jobs()
            ->withCount('applications')
            ->orderByDesc('created_at')
            ->paginate(10);

        return view('employer.jobs.index', [
            'jobs' => $jobs,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('employer.jobs.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $employer = $this->currentEmployer();

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'responsibilities' => ['nullable', 'string', 'max:5000'],
            'requirements' => ['nullable', 'string', 'max:5000'],
            'benefits' => ['nullable', 'string', 'max:5000'],
            'location' => ['required', 'string', 'max:255'],
            'job_type' => ['required', 'string', 'max:50'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0'],
            'accommodation_provided' => ['nullable', 'boolean'],
            'accommodation_details' => ['nullable', 'string', 'max:5000'],
            'visa_support' => ['nullable', 'boolean'],
            'visa_support_details' => ['nullable', 'string', 'max:5000'],
            'experience_level' => ['nullable', 'string', 'max:80'],
            'education_required' => ['nullable', 'string', 'max:120'],
            'contract_duration' => ['nullable', 'string', 'max:120'],
            'start_date' => ['nullable', 'date'],
            'start_flexibility' => ['nullable', 'string', 'max:120'],
            'positions_available' => ['nullable', 'integer', 'min:1'],
            'working_hours' => ['nullable', 'string', 'max:120'],
            'shift_details' => ['nullable', 'string', 'max:5000'],
            'application_instructions' => ['nullable', 'string', 'max:5000'],
            'is_featured' => ['nullable', 'boolean'],
            'is_urgent' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $approvalService = app(ApprovalService::class);
        $publishImmediately = (bool) ($validated['is_active'] ?? false);
        $initialStatus = $publishImmediately
            ? $approvalService->getInitialStatus($employer, 'job')
            : 'draft';

        $job = Job::create([
            'employer_id' => $employer->id,
            'created_by_user_id' => auth()->id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'responsibilities' => $validated['responsibilities'] ?? null,
            'requirements' => $validated['requirements'] ?? null,
            'benefits' => $validated['benefits'] ?? null,
            'location_city' => $validated['location'],
            'category' => 'General',
            'contract_type' => $validated['job_type'],
            'salary_min' => $validated['salary_min'] ?? null,
            'salary_max' => $validated['salary_max'] ?? null,
            'salary_currency' => 'EUR',
            'salary_period' => 'month',
            'accommodation_provided' => $request->boolean('accommodation_provided'),
            'accommodation_details' => $validated['accommodation_details'] ?? null,
            'visa_support' => $request->boolean('visa_support'),
            'visa_support_details' => $validated['visa_support_details'] ?? null,
            'experience_level' => $validated['experience_level'] ?? null,
            'education_required' => $validated['education_required'] ?? null,
            'contract_duration' => $validated['contract_duration'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'start_flexibility' => $validated['start_flexibility'] ?? null,
            'positions_available' => $validated['positions_available'] ?? 1,
            'working_hours' => $validated['working_hours'] ?? null,
            'shift_details' => $validated['shift_details'] ?? null,
            'application_instructions' => $validated['application_instructions'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'is_urgent' => $request->boolean('is_urgent'),
            'status' => $initialStatus,
            'published_at' => $initialStatus === 'published' ? now() : null,
            'expires_at' => now()->addDays(max(1, Setting::getInt('default_job_expiry_days', 30))),
        ]);

        return redirect()
            ->route('employer.jobs.edit', $job)
            ->with('success', 'Job created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Job $job)
    {
        $this->authorizeEmployerJob($job);

        $job->load(['applications.worker']);
        $applications = $job->applications()->with('worker')->latest()->get();

        $applications->transform(function ($application) {
            $application->candidate_data_access = $this->candidateDataAccessService->forApplication($application);

            return $application;
        });

        return view('employer.jobs.show', [
            'job' => $job,
            'applications' => $applications,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Job $job)
    {
        $this->authorizeEmployerJob($job);

        return view('employer.jobs.edit', [
            'job' => $job,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Job $job)
    {
        $this->authorizeEmployerJob($job);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'company_name' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'responsibilities' => ['nullable', 'string', 'max:5000'],
            'requirements' => ['nullable', 'string', 'max:5000'],
            'benefits' => ['nullable', 'string', 'max:5000'],
            'location' => ['required', 'string', 'max:255'],
            'job_type' => ['required', 'string', 'max:50'],
            'salary_min' => ['nullable', 'numeric', 'min:0'],
            'salary_max' => ['nullable', 'numeric', 'min:0'],
            'accommodation_provided' => ['nullable', 'boolean'],
            'accommodation_details' => ['nullable', 'string', 'max:5000'],
            'visa_support' => ['nullable', 'boolean'],
            'visa_support_details' => ['nullable', 'string', 'max:5000'],
            'experience_level' => ['nullable', 'string', 'max:80'],
            'education_required' => ['nullable', 'string', 'max:120'],
            'contract_duration' => ['nullable', 'string', 'max:120'],
            'start_date' => ['nullable', 'date'],
            'start_flexibility' => ['nullable', 'string', 'max:120'],
            'positions_available' => ['nullable', 'integer', 'min:1'],
            'working_hours' => ['nullable', 'string', 'max:120'],
            'shift_details' => ['nullable', 'string', 'max:5000'],
            'application_instructions' => ['nullable', 'string', 'max:5000'],
            'is_featured' => ['nullable', 'boolean'],
            'is_urgent' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $approvalService = app(ApprovalService::class);
        $publishImmediately = (bool) ($validated['is_active'] ?? false);
        $shouldPublish = $publishImmediately && ! $approvalService->requiresApprovalForEmployer($job->employer, 'job');

        $job->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'responsibilities' => $validated['responsibilities'] ?? null,
            'requirements' => $validated['requirements'] ?? null,
            'benefits' => $validated['benefits'] ?? null,
            'location_city' => $validated['location'],
            'category' => 'General',
            'contract_type' => $validated['job_type'],
            'salary_min' => $validated['salary_min'] ?? null,
            'salary_max' => $validated['salary_max'] ?? null,
            'accommodation_provided' => $request->boolean('accommodation_provided'),
            'accommodation_details' => $validated['accommodation_details'] ?? null,
            'visa_support' => $request->boolean('visa_support'),
            'visa_support_details' => $validated['visa_support_details'] ?? null,
            'experience_level' => $validated['experience_level'] ?? null,
            'education_required' => $validated['education_required'] ?? null,
            'contract_duration' => $validated['contract_duration'] ?? null,
            'start_date' => $validated['start_date'] ?? null,
            'start_flexibility' => $validated['start_flexibility'] ?? null,
            'positions_available' => $validated['positions_available'] ?? null,
            'working_hours' => $validated['working_hours'] ?? null,
            'shift_details' => $validated['shift_details'] ?? null,
            'application_instructions' => $validated['application_instructions'] ?? null,
            'is_featured' => $request->boolean('is_featured'),
            'is_urgent' => $request->boolean('is_urgent'),
            'status' => $publishImmediately ? ($shouldPublish ? 'published' : 'pending') : 'draft',
            'published_at' => $publishImmediately && $shouldPublish ? ($job->published_at ?? now()) : null,
        ]);

        return redirect()
            ->route('employer.jobs.edit', $job)
            ->with('success', 'Job updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Job $job)
    {
        $this->authorizeEmployerJob($job);

        $job->delete();

        return redirect()->route('employer.jobs.index')
            ->with('success', 'Job deleted successfully.');
    }

    private function authorizeEmployerJob(Job $job): void
    {
        $employerId = auth()->user()?->employer?->id;

        abort_unless((int) $job->employer_id === (int) $employerId, 403);
    }

    private function currentEmployer(): Employer
    {
        $employer = auth()->user()?->employer;

        abort_unless($employer instanceof Employer, 403);

        return $employer;
    }
}
