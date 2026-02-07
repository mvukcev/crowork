<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

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
        
        // For SEO and initial render
        $filters = [
            'q' => $request->input('q'),
            'city' => $request->input('city'),
            'category' => $request->input('category'),
            'salary_min' => $request->input('salary_min'),
            'accommodation' => $request->input('accommodation'),
            'language' => $request->input('language'),
        ];

        return view('jobs.index', compact('jobs', 'cities', 'categories', 'languages', 'filters'));
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

        // Load employer relationship for company info
        $job->load('employer');

        return view('jobs.show', compact('job'));
    }

    /**
     * Get filtered and paginated jobs
     */
    protected function getFilteredJobs(Request $request)
    {
        $query = Job::query()
            ->with('employer') // Prevent N+1
            ->active(); // Use existing scope for published/active jobs

        // Search by title or company
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhereHas('employer', function ($q) use ($search) {
                      $q->where('company_name', 'like', '%' . $search . '%');
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

        // Filter by minimum salary
        if ($request->filled('salary_min')) {
            $query->where('salary_min', '>=', $request->input('salary_min'));
        }

        // Filter by accommodation
        if ($request->input('accommodation') == '1') {
            $query->where('accommodation_provided', true);
        }

        // Filter by language
        if ($request->filled('language')) {
            $language = $request->input('language');
            $query->whereJsonContains('languages', $language);
        }

        // Order by newest first
        $query->orderBy('published_at', 'desc')
              ->orderBy('created_at', 'desc');

        // Paginate and preserve query string
        return $query->paginate(12)->withQueryString();
    }

    /**
     * Get distinct cities from jobs
     */
    protected function getCities()
    {
        return Cache::remember('job_cities', 3600, function () {
            return Job::active()
                ->whereNotNull('location_city')
                ->distinct()
                ->pluck('location_city')
                ->sort()
                ->values();
        });
    }

    /**
     * Get distinct categories from jobs
     */
    protected function getCategories()
    {
        return Cache::remember('job_categories', 3600, function () {
            return Job::active()
                ->whereNotNull('category')
                ->distinct()
                ->pluck('category')
                ->sort()
                ->values();
        });
    }

    /**
     * Get available languages
     */
    protected function getLanguages()
    {
        // Common languages in Croatia for international workers
        return [
            'EN' => 'English',
            'HR' => 'Croatian',
            'DE' => 'German',
            'IT' => 'Italian',
            'ES' => 'Spanish',
            'FR' => 'French',
        ];
    }
}

