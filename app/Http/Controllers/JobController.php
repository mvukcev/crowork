<?php

namespace App\Http\Controllers;

use App\Models\Job;
use App\Services\Hzz\HzzAnalyticsTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class JobController extends Controller
{
    /**
     * Display full jobs listing page
     */
    public function index(Request $request)
    {
        $jobs = $this->getFilteredJobs($request);
        
        // Get filter options
        $cities = $this->getCities();
        $categories = $this->getCategories();
        $languages = $this->getLanguages();
        $employmentTypes = $this->getEmploymentTypes();
        $experienceLevels = $this->getExperienceLevels();
        $educationRequirements = $this->getEducationRequirements();

        // For SEO and initial render
        $filters = [
            'q' => $request->input('q'),
            'city' => $request->input('city'),
            'category' => $request->input('category'),
            'employment_type' => $request->input('employment_type'),
            'experience_level' => $request->input('experience_level'),
            'salary_min' => $request->input('salary_min'),
            'salary_max' => $request->input('salary_max'),
            'accommodation' => $request->input('accommodation'),
            'visa_support' => $request->input('visa_support'),
            'featured' => $request->input('featured'),
            'urgent' => $request->input('urgent'),
            'language' => $request->input('language'),
            'education_required' => $request->input('education_required'),
        ];

        return view('jobs.index', compact('jobs', 'cities', 'categories', 'languages', 'employmentTypes', 'experienceLevels', 'educationRequirements', 'filters'));
    }

    /**
     * Return only the results partial (for AJAX)
     */
    public function partial(Request $request)
    {
        $jobs = $this->getFilteredJobs($request);
        
        return view('jobs._results', compact('jobs'));
    }

    /**
     * Show individual job
     */
    public function show(Job $job)
    {
        // Only show published/active jobs
        if ($job->status !== 'published' || ($job->expires_at && $job->expires_at->isPast())) {
            abort(404);
        }

        $job->loadMissing(['employer', 'translations']);

        if ($job->isHzzOfficial()) {
            app(HzzAnalyticsTracker::class)->trackView($job, request());
        }

        $similarJobs = $this->getSimilarJobs($job);

        return view('jobs.show', [
            'job' => $job,
            'similarJobs' => $similarJobs,
            'isPreview' => false,
        ]);
    }

    public function previewByToken(string $token)
    {
        $job = Job::query()
            ->with(['employer', 'translations'])
            ->where('preview_token', trim($token))
            ->firstOrFail();

        $similarJobs = $this->getSimilarJobs($job);

        return view('jobs.show', [
            'job' => $job,
            'similarJobs' => $similarJobs,
            'isPreview' => true,
        ]);
    }

    public function trackHzzCtaClick(Job $job, Request $request): Response
    {
        if (! $job->isHzzOfficial()) {
            return response()->noContent();
        }

        app(HzzAnalyticsTracker::class)->trackCtaClick($job, $request);

        return response()->noContent();
    }

    /**
     * Get filtered and paginated jobs
     */
    protected function getFilteredJobs(Request $request)
    {
        $query = Job::query()
            ->with(['employer', 'translations']) // Prevent N+1
            ->active(); // Use existing scope for published/active jobs

        // Search by title or company
        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('external_company_name', 'like', '%' . $search . '%')
                  ->orWhere('location_city', 'like', '%' . $search . '%')
                  ->orWhereHas('employer', function ($q) use ($search) {
                      $q->where('company_name', 'like', '%' . $search . '%')
                          ->orWhere('company_display_name', 'like', '%' . $search . '%');
                  });
            });
        }

        // Filter by city
        if ($request->filled('city')) {
            $query->where('location_city', $request->input('city'));
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        // Filter by employment type
        if ($request->filled('employment_type')) {
            $query->where('contract_type', $request->input('employment_type'));
        }

        // Filter by experience level
        if ($request->filled('experience_level')) {
            $query->where('experience_level', $request->input('experience_level'));
        }

        // Filter by minimum salary
        if ($request->filled('salary_min')) {
            $salaryMin = (int) $request->input('salary_min');

            $query->where(function ($q) use ($salaryMin) {
                $q->where(function ($inner) use ($salaryMin) {
                    $inner->whereNotNull('salary_max')
                        ->where('salary_max', '>=', $salaryMin);
                })->orWhere(function ($inner) use ($salaryMin) {
                    $inner->whereNull('salary_max')
                        ->where('salary_min', '>=', $salaryMin);
                });
            });
        }

        // Filter by maximum salary
        if ($request->filled('salary_max')) {
            $salaryMax = (int) $request->input('salary_max');
            $query->where('salary_min', '<=', $salaryMax);
        }

        // Filter by accommodation
        if ($request->input('accommodation') == '1') {
            $query->where('accommodation_provided', true);
        }

        if ($request->input('visa_support') == '1') {
            $query->where('visa_support', true);
        }

        if ($request->input('featured') == '1') {
            $query->where('is_featured', true);
        }

        if ($request->input('urgent') == '1') {
            $query->where('is_urgent', true);
        }

        // Filter by language
        if ($request->filled('language')) {
            $language = strtoupper((string) $request->input('language'));
            $query->whereJsonContains('languages', $language);
        }

        if ($request->filled('education_required')) {
            $query->where('education_required', $request->input('education_required'));
        }

        // Order by newest first
        $query->orderBy('published_at', 'desc')
              ->orderBy('created_at', 'desc');

        // Paginate and preserve query string
        return $query->paginate(18)->withQueryString();
    }

    protected function getSimilarJobs(Job $job)
    {
        // Load employer relationship for company info
        $job->loadMissing(['employer', 'translations']);

        $baseSimilarQuery = Job::query()
            ->with(['employer', 'translations'])
            ->active()
            ->where('id', '!=', $job->id);

        $similarQuery = (clone $baseSimilarQuery);

        if (! empty($job->category) || ! empty($job->location_city)) {
            $similarQuery->where(function ($query) use ($job) {
                if (! empty($job->category)) {
                    $query->orWhere('category', $job->category);
                }

                if (! empty($job->location_city)) {
                    $query->orWhere('location_city', $job->location_city);
                }
            });
        }

        $similarJobs = $similarQuery
            ->orderBy('published_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        if ($similarJobs->count() < 3) {
            $missing = 3 - $similarJobs->count();
            $excludeIds = $similarJobs->pluck('id')->push($job->id)->values();

            $fallbackJobs = Job::query()
                ->with(['employer', 'translations'])
                ->active()
                ->whereNotIn('id', $excludeIds)
                ->orderBy('published_at', 'desc')
                ->orderBy('created_at', 'desc')
                ->limit($missing)
                ->get();

            $similarJobs = $similarJobs->concat($fallbackJobs);
        }

        return $similarJobs;
    }

    /**
     * Get distinct cities from jobs
     */
    protected function getCities()
    {
        return Job::active()
            ->whereNotNull('location_city')
            ->pluck('location_city')
            ->map(fn ($city) => is_string($city) ? trim($city) : '')
            ->filter(fn ($city) => $city !== '')
            ->unique()
            ->sort()
            ->values();
    }

    /**
     * Get distinct categories from jobs
     */
    protected function getCategories()
    {
        return Job::active()
            ->whereNotNull('category')
            ->pluck('category')
            ->map(fn ($category) => is_string($category) ? trim($category) : '')
            ->filter(fn ($category) => $category !== '')
            ->unique()
            ->sort()
            ->values();
    }

    /**
     * Get available languages
     */
    protected function getLanguages()
    {
        $defaultCodes = ['EN', 'HR', 'DE', 'IT', 'ES', 'FR'];

        $allLanguageCodes = Job::active()
            ->whereNotNull('languages')
            ->get(['languages'])
            ->flatMap(function (Job $job) {
                $languages = $job->languages;

                if (is_string($languages)) {
                    $decoded = json_decode($languages, true);
                    $languages = is_array($decoded) ? $decoded : preg_split('/[\n,]+/', $languages);
                }

                return is_array($languages) ? $languages : [];
            })
            ->filter(fn ($code) => is_string($code) && trim($code) !== '')
            ->map(fn ($code) => strtoupper(trim($code)))
            ->unique()
            ->sort()
            ->values();

        $result = [];
        foreach ($allLanguageCodes as $code) {
            $result[$code] = cw_localize_language_code($code) ?? $code;
        }

        foreach ($defaultCodes as $code) {
            if (!array_key_exists($code, $result)) {
                $result[$code] = cw_localize_language_code($code) ?? $code;
            }
        }

        return $result;
    }

    protected function getEmploymentTypes()
    {
        return Job::active()
            ->whereNotNull('contract_type')
            ->distinct()
            ->pluck('contract_type')
            ->filter()
            ->sort()
            ->values();
    }

    protected function getExperienceLevels()
    {
        return Job::active()
            ->whereNotNull('experience_level')
            ->distinct()
            ->pluck('experience_level')
            ->filter()
            ->sort()
            ->values();
    }

    protected function getEducationRequirements()
    {
        return Cache::remember('job_education_requirements', 3600, function () {
            return Job::active()
                ->whereNotNull('education_required')
                ->distinct()
                ->pluck('education_required')
                ->filter()
                ->sort()
                ->values();
        });
    }
}
