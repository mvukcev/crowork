<?php

namespace App\Http\Controllers;

use App\Jobs\SendMetaEventJob;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\WorkerProfile;
use App\Notifications\JobApplicationSubmitted;
use App\Notifications\NewJobApplicationReceived;
use App\Services\ConsentConfigService;
use App\Services\Hzz\HzzAnalyticsTracker;
use App\Services\Hzz\HzzApplicationService;
use App\Support\HzzUrlGuard;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\File;

class JobApplicationController extends Controller
{

    /**
     * Show the application form
     */
    public function create(Job $job)
    {
        $isHzzFlow = $job->isHzzOfficial();

        if ($isHzzFlow) {
            app(HzzAnalyticsTracker::class)->trackCtaClick($job, request());
        }

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

        if ($isHzzFlow && ! $job->canApplyViaCroWork()) {
            return view('jobs.hzz-apply-gateway', compact('job', 'profileSnapshot', 'profileSkills'));
        }

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
        $isHzzFlow = $job->isHzzOfficial();

        // Authorization check: Only workers can apply
        if (Auth::user()->role !== 'worker') {
            abort(403, __('ui.jobs_apply.error_only_workers'));
        }

        // Ensure job is still published and active
        if ($job->status !== 'published' || ($job->expires_at && $job->expires_at->isPast())) {
            return redirect()
                ->route('jobs.show', $job)
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
                ->route('jobs.apply', $job)
                ->with('info', __('ui.jobs_apply.flash_already_applied'));
        }

        // Validate request
        $validated = $request->validate([
            'message' => 'nullable|string|max:1000',
            'cv_choice' => 'nullable|in:profile,upload',
            'cv_file' => [
                'nullable',
                'required_if:cv_choice,upload',
                File::types(['pdf', 'doc', 'docx'])
                    ->max(10240),
                'mimetypes:application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ],
            'cover_letter_mode' => 'nullable|in:none,preset,custom',
            'cover_letter_preset' => 'nullable|required_if:cover_letter_mode,preset|in:short,standard,detailed',
            'cover_letter_text' => 'nullable|string|max:5000',
            'consent' => 'accepted',
        ]);

        $cvChoice = (string) ($validated['cv_choice'] ?? 'profile');
        $coverLetterMode = (string) ($validated['cover_letter_mode'] ?? 'none');
        $coverLetterText = $this->resolveCoverLetterText(
            $coverLetterMode,
            $validated['cover_letter_preset'] ?? null,
            $validated['cover_letter_text'] ?? null,
            $profile,
            $job,
        );

        $cvFilePath = null;
        if ($cvChoice === 'upload' && $request->hasFile('cv_file')) {
            try {
                $cvFilePath = $request->file('cv_file')->store('job-applications/cv', 'local');
            } catch (\Throwable $exception) {
                Log::error('CV upload failed during job application.', [
                    'job_id' => $job->id,
                    'worker_id' => Auth::id(),
                    'error' => $exception->getMessage(),
                ]);

                return redirect()
                    ->route('jobs.apply', $job)
                    ->with('error', __('ui.jobs_apply.hzz_submit_failed'));
            }
        }

        $cvSnapshot = [
            'source' => $cvChoice,
            'generated_at' => now()->toIso8601String(),
        ];

        if ($cvFilePath !== null) {
            $cvSnapshot['file_path'] = $cvFilePath;
            $cvSnapshot['original_name'] = (string) $request->file('cv_file')->getClientOriginalName();
            $cvSnapshot['mime'] = (string) $request->file('cv_file')->getClientMimeType();
        }

        if ($cvChoice === 'profile') {
            $cvSnapshot['profile_snapshot_version'] = $profile->toSnapshot()['snapshot_version'] ?? null;
        }

        // Create job application with profile snapshot
        $application = JobApplication::create([
            'job_id' => $job->id,
            'apply_channel' => $isHzzFlow ? JobApplication::CHANNEL_HZZ_EMAIL : JobApplication::CHANNEL_INTERNAL,
            'worker_id' => Auth::id(),
            'profile_snapshot' => $profile->toSnapshot(),
            'job_snapshot' => $this->jobSnapshot($job),
            'message' => $validated['message'] ?? $coverLetterText,
            'cover_letter_text' => $coverLetterText,
            'cover_letter_template_key' => $coverLetterMode === 'preset' ? ($validated['cover_letter_preset'] ?? null) : null,
            'cv_snapshot' => $cvSnapshot,
            'cv_file_path' => $cvFilePath,
            'submitted_to_email' => $isHzzFlow ? $job->hzz_apply_email : null,
            'status' => 'new',
            'submission_status' => $isHzzFlow ? 'pending' : 'sent',
        ]);

        if ($isHzzFlow) {
            $sendResult = app(HzzApplicationService::class)->sendToEmployer($application, $job, $request->user(), $profile);

            $application->forceFill([
                'submission_status' => $sendResult['status'] ?? 'failed',
                'submission_log' => $sendResult['log'] ?? null,
                'submitted_at' => now(),
            ])->save();

            app(HzzAnalyticsTracker::class)->trackApplicationSent($job, $request, (bool) ($sendResult['success'] ?? false), [
                'application_id' => $application->id,
                'submission_status' => $sendResult['status'] ?? 'failed',
            ]);
        }

        $application->loadMissing('job.employer.user', 'worker');

        try {
            $application->worker?->notify(new JobApplicationSubmitted($application));
        } catch (\Throwable $exception) {
            Log::warning('Worker application confirmation notification failed.', [
                'application_id' => $application->id,
                'worker_id' => $application->worker_id,
                'error' => $exception->getMessage(),
            ]);
        }

        if (! $isHzzFlow) {
            try {
                $application->job?->employer?->user?->notify(new NewJobApplicationReceived($application));
            } catch (\Throwable $exception) {
                Log::warning('Employer application notification failed.', [
                    'application_id' => $application->id,
                    'job_id' => $job->id,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

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

        if ($isHzzFlow && $application->submission_status !== 'sent') {
            return redirect()
                ->route('jobs.apply', $job->slug)
                ->with('error', __('ui.jobs_apply.hzz_submit_failed'));
        }

        // Redirect to job detail with success message
        return redirect()
            ->route('jobs.show', $job)
            ->with('success', __('ui.jobs_apply.flash_submitted_success'));
    }

    public function openExternal(Job $job, Request $request)
    {
        if (! $job->isHzzOfficial()) {
            abort(404);
        }

        if (Auth::user()->role !== 'worker') {
            abort(403, __('ui.jobs_apply.error_only_workers'));
        }

        $profile = WorkerProfile::where('user_id', Auth::id())->first();
        if (! $profile || ! $this->isProfileComplete($profile)) {
            return redirect()
                ->route('worker.profile.edit')
                ->with('warning', __('ui.jobs_apply.flash_complete_profile'));
        }

        $target = $job->hzz_apply_url ?: $job->source_url;
        if (! filled($target) || ! HzzUrlGuard::isAllowedApplyUrl((string) $target)) {
            return redirect()
                ->route('jobs.show', $job)
                ->with('error', __('ui.jobs_apply.hzz_open_missing_url'));
        }

        $application = JobApplication::query()->firstOrCreate(
            [
                'job_id' => $job->id,
                'worker_id' => Auth::id(),
            ],
            [
                'apply_channel' => JobApplication::CHANNEL_HZZ_EXTERNAL,
                'profile_snapshot' => $profile->toSnapshot(),
                'job_snapshot' => $this->jobSnapshot($job),
                'cv_snapshot' => $profile->toSnapshot(),
                'status' => JobApplication::STATUS_NEW,
                'submission_status' => 'pending',
                'submission_log' => 'User opened external HZZ application link.',
                'submitted_at' => now(),
            ]
        );

        if ($application->wasRecentlyCreated === false && $application->apply_channel !== JobApplication::CHANNEL_HZZ_EXTERNAL) {
            $application->forceFill([
                'apply_channel' => JobApplication::CHANNEL_HZZ_EXTERNAL,
                'submission_status' => $application->submission_status ?: 'pending',
                'submission_log' => 'User opened external HZZ application link.',
                'submitted_at' => now(),
            ])->save();
        }

        app(HzzAnalyticsTracker::class)->trackExternalOpen($job, $request);

        return redirect()->away($target);
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
            'company_name' => $job->employer?->company_display_name ?? $job->employer?->company_name ?? $job->external_company_name,
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

    private function resolveCoverLetterText(
        string $mode,
        ?string $preset,
        ?string $customText,
        WorkerProfile $profile,
        Job $job,
    ): ?string {
        if ($mode === 'custom') {
            return trim((string) $customText) !== '' ? trim((string) $customText) : null;
        }

        if ($mode !== 'preset') {
            return null;
        }

        $candidateName = trim((string) ($profile->first_name . ' ' . $profile->last_name));
        $city = trim((string) ($profile->current_city ?? ''));
        $jobTitle = trim((string) $job->title);

        return match ($preset) {
            'short' => "Poštovani,\n\nzainteresiran/a sam za poziciju {$jobTitle}. Vjerujem da se moj profil i iskustvo dobro uklapaju u traženu ulogu.\n\nSrdačan pozdrav,\n{$candidateName}",
            'detailed' => "Poštovani,\n\nprijavljujem se na poziciju {$jobTitle} putem CroWork platforme. Kroz dosadašnje iskustvo razvio/la sam odgovornost, timski rad i fokus na kvalitetu izvedbe. Posebno me motivira mogućnost rada i razvoja u profesionalnom okruženju.\n\nTrenutno sam dostupan/na" . ($city !== '' ? " iz grada {$city}" : '') . " te sam spreman/na za daljnje korake selekcije.\n\nHvala na vremenu i razmatranju moje prijave.\n\nSrdačan pozdrav,\n{$candidateName}",
            default => "Poštovani,\n\novim putem šaljem prijavu za poziciju {$jobTitle}. Smatram da svojim iskustvom i pristupom radu mogu doprinijeti vašem timu.\n\nVeselim se mogućnosti daljnjeg razgovora.\n\nSrdačan pozdrav,\n{$candidateName}",
        };
    }
}
