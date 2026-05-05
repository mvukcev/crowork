<?php

namespace App\Http\Controllers;

use App\Models\Education;
use App\Models\Job;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect([
            $this->url(route('home'), 'daily', '1.0'),
            $this->url(route('jobs.index'), 'daily', '0.9'),
            $this->url(route('educations.index'), 'weekly', '0.8'),
            $this->url(route('about'), 'monthly', '0.6'),
            $this->url(route('for-employers'), 'monthly', '0.7'),
            $this->url(route('pricing'), 'monthly', '0.5'),
            $this->url(route('contact'), 'monthly', '0.5'),
            $this->url(route('privacy'), 'yearly', '0.3'),
            $this->url(route('terms'), 'yearly', '0.3'),
            $this->url(route('cookies'), 'yearly', '0.3'),
        ]);

        Job::query()
            ->with('employer')
            ->active()
            ->latest('updated_at')
            ->get()
            ->each(fn (Job $job) => $urls->push($this->url(
                route('jobs.show', $job),
                'weekly',
                '0.8',
                $job->updated_at ?? $job->published_at
            )));

        Education::query()
            ->active()
            ->latest('updated_at')
            ->get()
            ->each(fn (Education $education) => $urls->push($this->url(
                route('educations.show', $education),
                'weekly',
                '0.7',
                $education->updated_at ?? $education->published_at
            )));

        // Add public employer/company URLs if a route is introduced in the future.
        if (Route::has('employers.show')) {
            // Intentionally left empty until public employer pages exist.
        }

        $xml = view('seo.sitemap', ['urls' => $urls])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    private function url(string $loc, string $changefreq, string $priority, $lastmod = null): array
    {
        return [
            'loc' => $loc,
            'lastmod' => optional($lastmod)->toAtomString(),
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }
}
