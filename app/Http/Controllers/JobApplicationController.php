<?php

namespace App\Http\Controllers;

use App\Jobs\SendMetaEventJob;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\WorkerProfile;
use App\Notifications\JobApplicationSubmitted;
use App\Notifications\NewJobApplicationReceived;
use App\Services\ConsentConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class JobApplicationController extends Controller
{

    /**
     * Show the application form
     */
    public function create(Job $job)
    {
        // Authorization check: Only workers can apply
        if (Auth::user()->role !== 'worker') {
            abort(403, __('ui.jobs_apply.error_only_workers'));
        }

        // Ensure job is published and active
        if ($job->status !== 'published' || ($job->expires_at && $job->expires_at->isPast())) {
            abort(404, __('ui.jobs_apply.error_no_longer_available'));
        }

        // Load employer relationship for job details
        $job->load('employer');

        // Get worker's profile
        $profile = WorkerProfile::where('user_id', Auth::id())->first();

        // Check if profile exists and is complete
        if (!$profile || !$this->isProfileComplete($profile)) {
            return redirect()
                ->route('worker.profile.edit')
                ->with('warning', __('ui.jobs_apply.flash_complete_profile'));
        }

        // Check if worker already applied
        $existingApplication = JobApplication::where('job_id', $job->id)
            ->where('worker_id', Auth::id())
            ->first();

        $alreadyApplied = $existingApplication !== null;

        $profileSnapshot = $profile->toSnapshot();
        $profileSkills = is_array($profileSnapshot['skills'] ?? null) ? $profileSnapshot['skills'] : [];

        return view('jobs.apply', compact(
            'job',
            'profile',
            'alreadyApplied',
            'existingApplication',
            'profileSnapshot',
            'profileSkills',
        ));
    }

    /**
     * Store the job application
     */
    public function store(Request $request, Job $job)
    {
        // Authorization check: Only workers can apply
        if (Auth::user()->role !== 'worker') {
            abort(403, __('ui.jobs_apply.error_only_workers'));
        }

        // Ensure job is still published and active
        if ($job->status !== 'published' || ($job->expires_at && $job->expires_at->isPast())) {
            return redirect()
                ->route('jobs.show', $job->slug)
                ->with('error', __('ui.jobs_apply.error_no_longer_available'));
        }

        // Get worker's profile
        $profile = WorkerProfile::where('user_id', Auth::id())->first();

        // Check if profile exists and is complete
        if (!$profile || !$this->isProfileComplete($profile)) {
            return redirect()
                ->route('worker.profile.edit')
                ->with('warning', __('ui.jobs_apply.flash_complete_profile'));
        }

        // Check for duplicate application
        $existingApplication = JobApplication::where('job_id', $job->id)
            ->where('worker_id', Auth::id())
            ->first();

        if ($existingApplication) {
            return redirect()
                ->route('jobs.apply', $job->slug)
                ->with('info', __('ui.jobs_apply.flash_already_applied'));
        }

        // Validate request
        $validated = $request->validate([
            'message' => 'nullable|string|max:1000',
            'consent' => 'accepted',
        ]);

        // Create job application with profile snapshot
        $application = JobApplication::create([
            'job_id' => $job->id,
            'worker_id' => Auth::id(),
            'profile_snapshot' => $profile->toSnapshot(),
            'job_snapshot' => $this->jobSnapshot($job),
            'message' => $validated['message'] ?? null,
            'status' => 'new',
        ]);

        $application->loadMissing('job.employer.user', 'worker');
        $application->worker?->notify(new JobApplicationSubmitted($application));
        $application->job?->employer?->user?->notify(new NewJobApplicationReceived($application));

        $metaEventId = null;
        if (ConsentConfigService::hasMarketingConsent($request, $request->user())) {
            $metaEventId = (string) Str::uuid();
            SendMetaEventJob::dispatch(
                'job_application_submitted',
                [
                    'application_id' => $application->id,
                    'event_source_url' => $request->fullUrl(),
                    'client_user_agent' => $request->userAgent(),
                    'client_ip_address' => $request->ip(),
                ],
                $metaEventId,
            );
        }

        $this->queueTrackEvent('job_apply_complete', [
            'source' => 'job_apply_form',
            'event_id' => $metaEventId,
            'job_slug' => $job->slug,
            'job_id' => $job->id,
            'application_id' => $application->id,
        ]);

        // Redirect to job detail with success message
        return redirect()
            ->route('jobs.show', $job->slug)
            ->with('success', __('ui.jobs_apply.flash_submitted_success'));
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

    private function jobSnapshot(Job $job): array
    {
        return [
            'title' => $job->title,
            'company_name' => $job->employer?->company_name,
            'location_city' => $job->location_city,
            'category' => $job->category,
            'contract_type' => $job->contract_type,
            'salary_min' => $job->salary_min,
            'salary_max' => $job->salary_max,
            'salary_currency' => $job->salary_currency,
            'salary_period' => $job->salary_period,
            'languages' => $job->languages,
            'accommodation_provided' => $job->accommodation_provided,
            'visa_support' => $job->visa_support,
            'experience_level' => $job->experience_level,
            'education_required' => $job->education_required,
            'positions_available' => $job->positions_available,
            'posted_at' => $job->published_at?->toDateString(),
            'expires_at' => $job->expires_at?->toDateString(),
        ];
    }

    private function queueTrackEvent(string $event, array $payload = []): void
    {
        $queue = session('cw_track_queue', []);
        if (! is_array($queue)) {
            $queue = [];
        }

        $queue[] = [
            'event' => $event,
            'payload' => $payload,
        ];

        session(['cw_track_queue' => $queue]);
    }
}
