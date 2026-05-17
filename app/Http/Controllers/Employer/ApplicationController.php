<?php

namespace App\Http\Controllers\Employer;

use App\Jobs\SendMetaCapiEvent;
use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\JobApplication;
use App\Notifications\JobApplicationStatusChanged;
use App\Services\ApplicationVisibilityService;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function __construct(
        private ApplicationVisibilityService $visibilityService
    ) {}

    /**
     * Show employer dashboard overview
     */
    public function dashboard()
    {
        $employer = auth()->user()->employer;
        
        // Get jobs with application counts
        $jobs = $employer->jobs()
            ->where('status', 'published')
            ->withCount('applications')
            ->orderBy('published_at', 'desc')
            ->get();

        // Calculate statistics
        $activeJobs = $jobs->filter(fn($job) => $job->expires_at?->isFuture() ?? true)->count();
        $expiredJobs = $jobs->filter(fn($job) => $job->expires_at?->isPast() ?? false)->count();
        $pendingJobs = $employer->jobs()
            ->where('status', 'pending')
            ->count();

        // Get application counts by status using database queries (prevents N+1)
        $applications = JobApplication::whereHas('job', fn($q) => 
            $q->where('employer_id', $employer->id)
        );

        $totalApplications = $applications->count();
        $newApplications = (clone $applications)->where('status', JobApplication::STATUS_NEW)->count();
        $shortlistedCount = (clone $applications)->where('status', JobApplication::STATUS_SHORTLISTED)->count();
        $interviewCount = (clone $applications)->where('status', JobApplication::STATUS_INTERVIEW)->count();
        $offerCount = (clone $applications)->where('status', JobApplication::STATUS_OFFER)->count();
        $hiredCount = (clone $applications)->where('status', JobApplication::STATUS_HIRED)->count();
        $rejectedCount = (clone $applications)->where('status', JobApplication::STATUS_REJECTED)->count();
        $reviewingCount = (clone $applications)->where('status', JobApplication::STATUS_REVIEWING)->count();

        $pipelineBreakdown = [
            [
                'label' => __('employer.dashboard.pipeline.new'),
                'key' => JobApplication::STATUS_NEW,
                'count' => $newApplications,
                'color' => 'text-blue-600',
                'bg' => 'bg-blue-100',
            ],
            [
                'label' => __('employer.dashboard.pipeline.reviewing'),
                'key' => JobApplication::STATUS_REVIEWING,
                'count' => $reviewingCount,
                'color' => 'text-indigo-600',
                'bg' => 'bg-indigo-100',
            ],
            [
                'label' => __('employer.dashboard.pipeline.shortlisted'),
                'key' => JobApplication::STATUS_SHORTLISTED,
                'count' => $shortlistedCount,
                'color' => 'text-violet-600',
                'bg' => 'bg-violet-100',
            ],
            [
                'label' => __('employer.dashboard.pipeline.interview'),
                'key' => JobApplication::STATUS_INTERVIEW,
                'count' => $interviewCount,
                'color' => 'text-purple-600',
                'bg' => 'bg-purple-100',
            ],
            [
                'label' => __('employer.dashboard.pipeline.offer'),
                'key' => JobApplication::STATUS_OFFER,
                'count' => $offerCount,
                'color' => 'text-emerald-600',
                'bg' => 'bg-emerald-100',
            ],
            [
                'label' => __('employer.dashboard.pipeline.hired'),
                'key' => JobApplication::STATUS_HIRED,
                'count' => $hiredCount,
                'color' => 'text-green-700',
                'bg' => 'bg-green-100',
            ],
        ];

        $recentCandidates = JobApplication::query()
            ->whereHas('job', fn($q) => $q->where('employer_id', $employer->id))
            ->with(['job', 'worker'])
            ->latest()
            ->limit(6)
            ->get();

        $jobPerformance = $employer->jobs()
            ->where('status', 'published')
            ->withCount([
                'applications',
                'applications as hired_applications_count' => fn($q) => $q->where('status', JobApplication::STATUS_HIRED),
                'applications as interview_applications_count' => fn($q) => $q->where('status', JobApplication::STATUS_INTERVIEW),
            ])
            ->orderByDesc('applications_count')
            ->limit(4)
            ->get();

        $expiringJobs = $employer->jobs()
            ->where('status', 'published')
            ->whereNotNull('expires_at')
            ->whereBetween('expires_at', [now(), now()->copy()->addDays(14)])
            ->withCount('applications')
            ->orderBy('expires_at')
            ->limit(5)
            ->get();

        $pendingApprovalJobs = $employer->jobs()
            ->where('status', 'pending')
            ->latest()
            ->limit(5)
            ->get();

        return view('employer.dashboard', [
            'employer' => $employer,
            'activeJobs' => $activeJobs,
            'pendingJobs' => $pendingJobs,
            'expiredJobs' => $expiredJobs,
            'totalApplications' => $totalApplications,
            'newApplications' => $newApplications,
            'shortlistedCount' => $shortlistedCount,
            'interviewCount' => $interviewCount,
            'offerCount' => $offerCount,
            'hiredCount' => $hiredCount,
            'rejectedCount' => $rejectedCount,
            'pipelineBreakdown' => $pipelineBreakdown,
            'recentCandidates' => $recentCandidates,
            'jobPerformance' => $jobPerformance,
            'expiringJobs' => $expiringJobs,
            'pendingApprovalJobs' => $pendingApprovalJobs,
            'jobs' => $jobs,
        ]);
    }

    /**
     * Show applications pipeline board
     */
    public function pipeline(Request $request)
    {
        $employer = auth()->user()->employer;
        
        $query = JobApplication::whereHas('job', fn($q) => 
            $q->where('employer_id', $employer->id)
        );

        // Filter by job if specified
        if ($request->has('job_id')) {
            $query->where('job_id', $request->get('job_id'));
        }

        // Filter by status if specified
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        $applications = $query
            ->with(['job', 'worker'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        // Get jobs for filter dropdown
        $jobs = $employer->jobs()
            ->where('status', 'published')
            ->select('id', 'title')
            ->orderBy('title')
            ->get();

        // Mask applications based on visibility
        $applications->getCollection()->transform(function ($application) use ($employer) {
            $application->masked_profile = $this->visibilityService->maskSnapshot(
                $application->profile_snapshot ?? [],
                $employer
            );
            return $application;
        });

        return view('employer.applications.pipeline', [
            'applications' => $applications,
            'jobs' => $jobs,
            'selectedJobId' => $request->get('job_id'),
            'selectedStatus' => $request->get('status'),
        ]);
    }

    /**
     * Show candidate detail view
     */
    public function candidate(JobApplication $application)
    {
        $employer = auth()->user()->employer;
        
        // Authorize: application must belong to this employer's job
        abort_unless(
            $application->job->employer_id === $employer->id,
            403
        );

        $application->load(['job', 'worker']);

        // Mask profile snapshot based on visibility
        $maskedProfile = $this->visibilityService->maskSnapshot(
            $application->profile_snapshot ?? [],
            $employer
        );

        return view('employer.applications.candidate', [
            'application' => $application,
            'job' => $application->job,
            'worker' => $application->worker,
            'maskedProfile' => $maskedProfile,
        ]);
    }

    /**
     * Update application status
     */
    public function updateStatus(Request $request, JobApplication $application)
    {
        $employer = auth()->user()->employer;
        
        // Authorize
        abort_unless(
            $application->job->employer_id === $employer->id,
            403
        );

        $validated = $request->validate([
            'status' => ['required', 'string', 'in:' . implode(',', [
                JobApplication::STATUS_NEW,
                JobApplication::STATUS_REVIEWING,
                JobApplication::STATUS_SHORTLISTED,
                JobApplication::STATUS_INTERVIEW,
                JobApplication::STATUS_OFFER,
                JobApplication::STATUS_HIRED,
                JobApplication::STATUS_REJECTED,
            ])],
        ]);

        $oldStatus = $application->status;

        $application->update([
            'status' => $validated['status'],
            'status_updated_at' => now(),
        ]);

        if ($oldStatus !== $validated['status']) {
            $application->loadMissing('worker', 'job.employer');
            $application->worker?->notify(new JobApplicationStatusChanged($application, $oldStatus));
            SendMetaCapiEvent::dispatch('application_status_changed', $application->id, $validated['status']);
        }

        return response()->json(['success' => true, 'status' => $validated['status']]);
    }

    /**
     * Update internal notes
     */
    public function updateNotes(Request $request, JobApplication $application)
    {
        $employer = auth()->user()->employer;
        
        // Authorize
        abort_unless(
            $application->job->employer_id === $employer->id,
            403
        );

        $validated = $request->validate([
            'internal_note' => ['nullable', 'string', 'max:5000'],
        ]);

        $application->update(['internal_note' => $validated['internal_note']]);

        return response()->json(['success' => true]);
    }

    /**
     * Update score/rating
     */
    public function updateScore(Request $request, JobApplication $application)
    {
        $employer = auth()->user()->employer;
        
        // Authorize
        abort_unless(
            $application->job->employer_id === $employer->id,
            403
        );

        $validated = $request->validate([
            'score' => ['nullable', 'numeric', 'min:0', 'max:5'],
        ]);

        $application->update(['score' => $validated['score']]);

        return response()->json(['success' => true, 'score' => $validated['score']]);
    }

    /**
     * Update interview date
     */
    public function updateInterviewDate(Request $request, JobApplication $application)
    {
        $employer = auth()->user()->employer;
        
        // Authorize
        abort_unless(
            $application->job->employer_id === $employer->id,
            403
        );

        $validated = $request->validate([
            'interview_at' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $application->update(['interview_at' => $validated['interview_at'] ? now()->parse($validated['interview_at']) : null]);

        return response()->json(['success' => true]);
    }

    /**
     * Show company profile settings
     */
    public function profileSettings()
    {
        $employer = auth()->user()->employer;
        
        $readiness = $employer->getProfileReadinessAttribute();
        $missing = $this->getMissingFields($employer);

        return view('employer.settings.profile', [
            'employer' => $employer,
            'readiness' => $readiness,
            'missing' => $missing,
        ]);
    }

    /**
     * Update company profile
     */
    public function updateProfile(Request $request)
    {
        $employer = auth()->user()->employer;
        
        $validated = $request->validate([
            'company_name' => ['nullable', 'string', 'max:255'],
            'company_display_name' => ['nullable', 'string', 'max:255'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:120'],
            'industry' => ['nullable', 'string', 'max:100'],
            'website' => ['nullable', 'url', 'max:255'],
            'contact_email' => ['nullable', 'email:rfc,dns', 'max:255'],
            'contact_phone' => ['nullable', 'string', 'max:60'],
            'company_address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:8192', 'dimensions:ratio=1/1,min_width=180,min_height=180'],
            'relocation_support' => ['boolean'],
            'accommodation_support' => ['boolean'],
        ]);

        $validated['relocation_support'] = $request->boolean('relocation_support');
        $validated['accommodation_support'] = $request->boolean('accommodation_support');

        if ($request->hasFile('logo')) {
            if ($employer->logo_path) {
                Storage::disk('public')->delete($employer->logo_path);
            }

            $validated['logo_path'] = $this->storeProcessedLogo($request->file('logo'), $employer->id);
        }

        unset($validated['logo']);

        $employer->update($validated);

        return redirect()->route('employer.settings.profile')
            ->with('success', __('employer.settings.profile_updated_success'));
    }

    /**
     * Get missing fields for profile completeness
     */
    private function getMissingFields(object $employer): array
    {
        $fields = [
            'company_name' => __('employer.settings.company_name'),
            'company_display_name' => __('employer.settings.company_display_name'),
            'city' => __('employer.settings.city'),
            'country' => __('employer.settings.country'),
            'industry' => __('employer.settings.industry'),
            'website' => __('employer.settings.website'),
            'contact_email' => __('employer.settings.contact_email'),
            'contact_phone' => __('employer.settings.contact_phone'),
            'company_address' => __('employer.settings.company_address'),
            'description' => __('employer.settings.company_description'),
            'logo_path' => __('employer.settings.company_logo'),
        ];

        $missing = [];
        foreach ($fields as $field => $label) {
            if (empty($employer->{$field})) {
                $missing[$field] = $label;
            }
        }

        return $missing;
    }

    private function storeProcessedLogo(UploadedFile $logoFile, int $employerId): string
    {
        $extension = strtolower((string) $logoFile->getClientOriginalExtension());
        $extension = in_array($extension, ['jpg', 'jpeg', 'png'], true) ? $extension : 'jpg';

        $relativePath = 'company-logos/' . $employerId . '_' . time() . '.' . $extension;
        $absolutePath = Storage::disk('public')->path($relativePath);

        $directory = dirname($absolutePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        if (!function_exists('imagecreatefromstring') || !function_exists('imagecreatetruecolor') || !function_exists('imagecopyresampled')) {
            $storedPath = $logoFile->storeAs('company-logos', basename($relativePath), 'public');
            return (string) $storedPath;
        }

        $raw = @file_get_contents($logoFile->getRealPath());
        $source = $raw ? @imagecreatefromstring($raw) : false;

        if (!$source) {
            $storedPath = $logoFile->storeAs('company-logos', basename($relativePath), 'public');
            return (string) $storedPath;
        }

        $width = imagesx($source);
        $height = imagesy($source);
        $cropSize = min($width, $height);
        $cropX = (int) floor(($width - $cropSize) / 2);
        $cropY = (int) floor(($height - $cropSize) / 2);
        $targetSize = min($cropSize, 1024);

        $canvas = imagecreatetruecolor($targetSize, $targetSize);
        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);
        imagefill($canvas, 0, 0, $transparent);

        imagecopyresampled(
            $canvas,
            $source,
            0,
            0,
            $cropX,
            $cropY,
            $targetSize,
            $targetSize,
            $cropSize,
            $cropSize
        );

        if ($extension === 'png') {
            imagepng($canvas, $absolutePath, 8);
        } else {
            imagejpeg($canvas, $absolutePath, 84);
        }

        imagedestroy($canvas);
        imagedestroy($source);

        return $relativePath;
    }
}
