<?php

namespace App\Http\Controllers;

use App\Models\Job;

class HomeController extends Controller
{
    public function index()
    {
        $limit = 6;

        $featuredJobs = Job::query()
            ->with('employer')
            ->published()
            ->active()
            ->whereNotNull('slug')
            ->where('slug', '!=', '')
            ->where('is_featured', true)
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->take($limit)
            ->get();

        if ($featuredJobs->count() < $limit) {
            $missing = $limit - $featuredJobs->count();
            $excludeIds = $featuredJobs->pluck('id')->all();

            $fallbackJobs = Job::query()
                ->with('employer')
                ->published()
                ->active()
                ->whereNotNull('slug')
                ->where('slug', '!=', '')
                ->whereNotIn('id', $excludeIds)
                ->orderByDesc('is_urgent')
                ->orderByDesc('published_at')
                ->orderByDesc('created_at')
                ->take($missing)
                ->get();

            $featuredJobs = $featuredJobs->concat($fallbackJobs)->values();
        }

        return view('home', compact('featuredJobs'));
    }
}
