<?php

namespace App\Http\Controllers;

use App\Models\Education;
use Illuminate\Http\Request;

class EducationsController extends Controller
{
    /**
     * Display full educations listing page
     */
    public function index(Request $request)
    {
        $educations = $this->getFilteredEducations($request);
        
        // Get filter options
        $cities = $this->getCities();
        
        // For SEO and initial render
        $filters = [
            'q' => $request->input('q'),
            'city' => $request->input('city'),
            'is_online' => $request->input('is_online'),
            'start_from' => $request->input('start_from'),
            'price_min' => $request->input('price_min'),
            'price_max' => $request->input('price_max'),
            'topics' => $request->input('topics', []),
        ];

        return view('educations.index', compact('educations', 'cities', 'filters'));
    }

    /**
     * Return only the results partial (for AJAX)
     */
    public function partial(Request $request)
    {
        $educations = $this->getFilteredEducations($request);
        
        return view('educations._results', compact('educations'));
    }

    /**
     * Show individual education
     */
    public function show(Education $education)
    {
        // Only show published/active educations
        if ($education->status !== 'published' || ($education->expires_at && $education->expires_at->isPast())) {
            abort(404);
        }

        // Load creator relationship
        $education->load('createdByUser');

        return view('educations.show', compact('education'));
    }

    /**
     * Get filtered and paginated educations
     */
    protected function getFilteredEducations(Request $request)
    {
        $query = Education::query()
            ->with('createdByUser') // Prevent N+1
            ->active(); // Use existing scope for published/active educations

        // Search by title, provider, or description
        if ($request->filled('q')) {
            $search = trim((string) $request->input('q'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%')
                  ->orWhereHas('createdByUser', function ($userQuery) use ($search) {
                      $userQuery->where('name', 'like', '%' . $search . '%')
                          ->orWhereHas('employer', function ($employerQuery) use ($search) {
                              $employerQuery->where('company_name', 'like', '%' . $search . '%')
                                  ->orWhere('company_display_name', 'like', '%' . $search . '%');
                          });
                  });
            });
        }

        // Filter by city
        if ($request->filled('city')) {
            $query->where('city', $request->input('city'));
        }

        // Filter by online/in-person
        if ($request->filled('is_online')) {
            $query->where('is_online', $request->input('is_online') == '1');
        }

        // Filter by start date from
        if ($request->filled('start_from')) {
            $query->where('start_date', '>=', $request->input('start_from'));
        }

        // Filter by minimum price (in cents)
        if ($request->filled('price_min')) {
            $priceMinEuros = (int) $request->input('price_min');
            $priceMinCents = $priceMinEuros * 100;
            $query->where(function($q) use ($priceMinCents) {
                $q->whereNull('price_cents')
                  ->orWhere('price_cents', '>=', $priceMinCents);
            });
        }

        // Filter by maximum price (in cents)
        if ($request->filled('price_max')) {
            $priceMaxEuros = (int) $request->input('price_max');
            $priceMaxCents = $priceMaxEuros * 100;
            $query->where(function($q) use ($priceMaxCents) {
                $q->whereNull('price_cents')
                  ->orWhere('price_cents', '<=', $priceMaxCents);
            });
        }

        $topics = collect($request->input('topics', []))
            ->filter(fn ($topic) => is_string($topic) && $topic !== '')
            ->values();

        if ($topics->isNotEmpty()) {
            $topicTermMap = [
                'certificate' => ['certificate', 'certifikat'],
                'beginner' => ['beginner', 'početnik', 'osnove'],
                'career' => ['career', 'karijera', 'growth', 'razvoj'],
                'language' => ['language', 'jezik'],
                'integration' => ['integration', 'onboarding', 'inkluzija', 'integracija'],
                'croatian' => ['croatian', 'hrvatski'],
                'skills' => ['skills', 'vještine', 'professional'],
            ];

            $query->where(function ($outer) use ($topics, $topicTermMap) {
                foreach ($topics as $topic) {
                    $terms = $topicTermMap[$topic] ?? [];
                    if (empty($terms)) {
                        continue;
                    }

                    $outer->where(function ($inner) use ($terms) {
                        foreach ($terms as $term) {
                            $inner->orWhere('title', 'like', '%' . $term . '%')
                                  ->orWhere('description', 'like', '%' . $term . '%');
                        }
                    });
                }
            });
        }

        // Order by start date (soonest first), then newest
        $query->orderByRaw('start_date IS NULL, start_date ASC')
              ->orderBy('published_at', 'desc')
              ->orderBy('created_at', 'desc');

        // Paginate and preserve query string
        return $query->paginate(12)->withQueryString();
    }

    /**
     * Get distinct cities from educations
     */
    protected function getCities()
    {
        return Education::active()
            ->whereNotNull('city')
            ->where('is_online', false)
            ->pluck('city')
            ->map(fn ($city) => is_string($city) ? trim($city) : '')
            ->filter(fn ($city) => $city !== '')
            ->unique()
            ->sort()
            ->values();
    }
}
