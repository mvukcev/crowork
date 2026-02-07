<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\WorkerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class JobApplicationController extends Controller
{
    /**
     * Constructor - Apply authentication middleware
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application form
     */
    public function create(Job $job)
    {
        // Authorization check: Only workers can apply
        if (Auth::user()->role !== 'worker') {
            abort(403, 'Only workers can apply to jobs.');
        }

        // Ensure job is published and active
        if ($job->status !== 'published' || ($job->expires_at && $job->expires_at->isPast())) {
            abort(404, 'This job is no longer available.');
        }

        // Load employer relationship for job details
        $job->load('employer');

        // Get worker's profile
        $profile = WorkerProfile::where('user_id', Auth::id())->first();

        // Check if profile exists and is complete
        if (!$profile || !$this->isProfileComplete($profile)) {
            return redirect()
                ->route('worker.profile.edit')
                ->with('warning', 'Please complete your profile before applying to jobs.');
        }

        // Check if worker already applied
        $existingApplication = JobApplication::where('job_id', $job->id)
            ->where('worker_id', Auth::id())
            ->first();

        $alreadyApplied = $existingApplication !== null;

        return view('jobs.apply', compact('job', 'profile', 'alreadyApplied', 'existingApplication'));
    }

    /**
     * Store the job application
     */
    public function store(Request $request, Job $job)
    {
        // Authorization check: Only workers can apply
        if (Auth::user()->role !== 'worker') {
            abort(403, 'Only workers can apply to jobs.');
        }

        // Ensure job is still published and active
        if ($job->status !== 'published' || ($job->expires_at && $job->expires_at->isPast())) {
            return redirect()
                ->route('jobs.show', $job->slug)
                ->with('error', 'This job is no longer available.');
        }

        // Get worker's profile
        $profile = WorkerProfile::where('user_id', Auth::id())->first();

        // Check if profile exists and is complete
        if (!$profile || !$this->isProfileComplete($profile)) {
            return redirect()
                ->route('worker.profile.edit')
                ->with('warning', 'Please complete your profile before applying to jobs.');
        }

        // Check for duplicate application
        $existingApplication = JobApplication::where('job_id', $job->id)
            ->where('worker_id', Auth::id())
            ->first();

        if ($existingApplication) {
            return redirect()
                ->route('jobs.apply', $job->slug)
                ->with('info', 'You have already applied to this job.');
        }

        // Validate request
        $validated = $request->validate([
            'message' => 'nullable|string|max:1000',
        ]);

        // Create job application with profile snapshot
        JobApplication::create([
            'job_id' => $job->id,
            'worker_id' => Auth::id(),
            'profile_snapshot' => $profile->toSnapshot(),
            'message' => $validated['message'] ?? null,
            'status' => 'new',
        ]);

        // Redirect to job detail with success message
        return redirect()
            ->route('jobs.show', $job->slug)
            ->with('success', 'Your application has been submitted successfully! The employer will review your profile and contact you if you are a good fit.');
    }

    /**
     * Check if worker profile is complete enough to apply
     */
    private function isProfileComplete(WorkerProfile $profile): bool
    {
        // Required fields for application
        return !empty($profile->first_name)
            && !empty($profile->last_name)
            && !empty($profile->nationality_country_code)
            && !empty($profile->birth_year);
    }
}
