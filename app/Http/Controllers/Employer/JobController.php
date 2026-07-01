<?php

namespace App\Http\Controllers\Employer;

use App\Http\Controllers\Controller;
use App\Models\Employer;
use App\Models\Job;
use App\Services\ApprovalService;
use App\Services\EmployerCandidateDataAccessService;
use App\Models\Setting;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

        $jobsQuery = $employer->jobs();

        if (Schema::hasColumn('job_postings', 'source_system')) {
            $jobsQuery->where(function ($query): void {
                $query->whereNull('source_system')
                    ->orWhere('source_system', '!=', 'hzz');
            });
        }

        if (Schema::hasColumn('job_postings', 'hzz_is_official')) {
            $jobsQuery->where(function ($query): void {
                $query->whereNull('hzz_is_official')
                    ->orWhere('hzz_is_official', false);
            });
        }

        $jobs = $jobsQuery
            ->withCount('applications')
            ->orderByDesc('created_at')
            ->paginate(10);

        $jobs->getCollection()->each(function (Job $job): void {
            $job->ensurePreviewToken();
        });

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
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:12288', 'dimensions:min_width=1200,min_height=600'],
            'cover_crop_zoom' => ['nullable', 'numeric', 'min:1', 'max:3'],
            'cover_crop_x' => ['nullable', 'numeric', 'min:-100', 'max:100'],
            'cover_crop_y' => ['nullable', 'numeric', 'min:-100', 'max:100'],
        ]);

        $approvalService = app(ApprovalService::class);
        $initialStatus = $approvalService->getInitialStatus($employer, 'job');

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
            'is_featured' => false,
            'is_urgent' => false,
            'status' => $initialStatus,
            'published_at' => $initialStatus === 'published' ? now() : null,
            'expires_at' => now()->addDays(max(1, Setting::getInt('default_job_expiry_days', 30))),
        ]);

        if ($request->hasFile('cover_image')) {
            $job->cover_image_path = $this->storeProcessedCoverImage(
                $request->file('cover_image'),
                (string) $job->id,
                $this->extractCropMeta($request, 'cover_crop')
            );
            $job->save();
        }

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
        $job->ensurePreviewToken();

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
        $job->ensurePreviewToken();

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
            'cover_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:12288', 'dimensions:min_width=1200,min_height=600'],
            'cover_crop_zoom' => ['nullable', 'numeric', 'min:1', 'max:3'],
            'cover_crop_x' => ['nullable', 'numeric', 'min:-100', 'max:100'],
            'cover_crop_y' => ['nullable', 'numeric', 'min:-100', 'max:100'],
        ]);

        $approvalService = app(ApprovalService::class);
        $nextStatus = $approvalService->getInitialStatus($job->employer, 'job');

        $payload = [
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
            'status' => $nextStatus,
            'published_at' => $nextStatus === 'published' ? ($job->published_at ?? now()) : null,
        ];

        $previousCoverImagePath = $job->cover_image_path;

        if ($request->hasFile('cover_image')) {
            $payload['cover_image_path'] = $this->storeProcessedCoverImage(
                $request->file('cover_image'),
                (string) $job->id,
                $this->extractCropMeta($request, 'cover_crop')
            );
        }

        $job->update($payload);

        if ($request->hasFile('cover_image') && filled($previousCoverImagePath) && $previousCoverImagePath !== ($payload['cover_image_path'] ?? null)) {
            Storage::disk('public')->delete((string) $previousCoverImagePath);
        }

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
        $isHzz = ($job->source_system === 'hzz') || (bool) $job->hzz_is_official;

        abort_unless((int) $job->employer_id === (int) $employerId && ! $isHzz, 404);
    }

    private function currentEmployer(): Employer
    {
        $employer = auth()->user()?->employer;

        abort_unless($employer instanceof Employer, 403);

        return $employer;
    }

    /**
     * @return array{zoom: float, x: float, y: float}
     */
    private function extractCropMeta(Request $request, string $prefix): array
    {
        return [
            'zoom' => max(1.0, min(3.0, (float) $request->input($prefix . '_zoom', 1))),
            'x' => max(-100.0, min(100.0, (float) $request->input($prefix . '_x', 0))),
            'y' => max(-100.0, min(100.0, (float) $request->input($prefix . '_y', 0))),
        ];
    }

    /**
     * @param array{zoom: float, x: float, y: float} $crop
     */
    private function storeProcessedCoverImage(UploadedFile $file, string $jobId, array $crop): string
    {
        $directory = 'job-covers';
        $extension = function_exists('imagewebp') ? 'webp' : 'jpg';
        $relativePath = $directory . '/' . $jobId . '_' . time() . '_' . Str::random(6) . '.' . $extension;
        $absolutePath = Storage::disk('public')->path($relativePath);

        $dirPath = dirname($absolutePath);
        if (! is_dir($dirPath)) {
            mkdir($dirPath, 0755, true);
        }

        if (! function_exists('imagecreatefromstring') || ! function_exists('imagecreatetruecolor') || ! function_exists('imagecopyresampled')) {
            return (string) $file->storeAs($directory, basename($relativePath), 'public');
        }

        $raw = @file_get_contents($file->getRealPath());
        $source = $raw ? @imagecreatefromstring($raw) : false;
        if (! $source) {
            return (string) $file->storeAs($directory, basename($relativePath), 'public');
        }

        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $targetAspect = 2.0;
        $targetWidth = 1800;
        $targetHeight = 900;

        $sourceAspect = $sourceWidth / max(1, $sourceHeight);
        if ($sourceAspect >= $targetAspect) {
            $baseCropHeight = $sourceHeight;
            $baseCropWidth = $sourceHeight * $targetAspect;
        } else {
            $baseCropWidth = $sourceWidth;
            $baseCropHeight = $sourceWidth / $targetAspect;
        }

        $zoom = max(1.0, min(3.0, (float) ($crop['zoom'] ?? 1.0)));
        $cropWidth = max(1.0, $baseCropWidth / $zoom);
        $cropHeight = max(1.0, $baseCropHeight / $zoom);

        $maxShiftX = max(0.0, ($sourceWidth - $cropWidth) / 2);
        $maxShiftY = max(0.0, ($sourceHeight - $cropHeight) / 2);

        $shiftX = (($crop['x'] ?? 0.0) / 100.0) * $maxShiftX;
        $shiftY = (($crop['y'] ?? 0.0) / 100.0) * $maxShiftY;

        $centerX = $sourceWidth / 2;
        $centerY = $sourceHeight / 2;

        $cropX = (int) round(max(0.0, min($sourceWidth - $cropWidth, $centerX - ($cropWidth / 2) + $shiftX)));
        $cropY = (int) round(max(0.0, min($sourceHeight - $cropHeight, $centerY - ($cropHeight / 2) + $shiftY)));

        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);
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
            $targetWidth,
            $targetHeight,
            (int) round($cropWidth),
            (int) round($cropHeight)
        );

        if ($extension === 'webp' && function_exists('imagewebp')) {
            imagewebp($canvas, $absolutePath, 84);
        } else {
            imagejpeg($canvas, $absolutePath, 84);
        }

        imagedestroy($canvas);
        imagedestroy($source);

        return $relativePath;
    }
}
