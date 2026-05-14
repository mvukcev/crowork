<?php

namespace App\Http\Controllers;

use App\Models\Education;
use App\Models\Employer;
use App\Models\Job;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = $this->baseSitemapUrls();

        Job::query()
            ->active()
            ->select(['id', 'slug', 'updated_at', 'published_at', 'created_at'])
            ->orderBy('id')
            ->chunkById(500, function (Collection $jobs) use ($urls): void {
                foreach ($jobs as $job) {
                    $urls->push($this->url(
                        route('jobs.show', $job),
                        'daily',
                        '0.85',
                        $job->updated_at ?? $job->published_at ?? $job->created_at
                    ));
                }
            });

        Education::query()
            ->active()
            ->select(['id', 'slug', 'updated_at', 'published_at', 'created_at'])
            ->orderBy('id')
            ->chunkById(500, function (Collection $educations) use ($urls): void {
                foreach ($educations as $education) {
                    $urls->push($this->url(
                        route('educations.show', $education),
                        'weekly',
                        '0.75',
                        $education->updated_at ?? $education->published_at ?? $education->created_at
                    ));
                }
            });

        Employer::query()
            ->whereNotNull('approved_at')
            ->whereNotNull('slug')
            ->select(['id', 'slug', 'updated_at', 'approved_at'])
            ->orderBy('id')
            ->chunkById(500, function (Collection $companies) use ($urls): void {
                foreach ($companies as $company) {
                    $urls->push($this->url(
                        route('companies.show', $company),
                        'weekly',
                        '0.7',
                        $company->updated_at ?? $company->approved_at
                    ));
                }
            });

        $xml = view('seo.sitemap', ['urls' => $urls])->render();

        return response($xml, 200, ['Content-Type' => 'application/xml; charset=UTF-8']);
    }

    public function llms(): Response
    {
        $appName = config('app.name', 'CroWork');
        $baseUrl = rtrim(config('app.url', url('/')), '/');

        $lines = [
            '# About CroWork',
            $appName.' is a public platform for jobs, employer/company profiles, education programs, and Croatia work guidance for international workers.',
            '',
            '# Public content',
            '- Home: '.$baseUrl.'/',
            '- Jobs listing: '.$baseUrl.'/jobs',
            '- Educations listing: '.$baseUrl.'/educations',
            '- Companies (public employer profiles): '.$baseUrl.'/companies/{company-slug}',
            '- Croatia Work Guide: '.$baseUrl.'/resources',
            '- About: '.$baseUrl.'/about',
            '- Contact: '.$baseUrl.'/contact',
            '',
            '# Job listings',
            'Public job listing and job detail pages are crawlable and indexable.',
            '',
            '# Employer/company profiles',
            'Approved company profile pages are public and indexable.',
            '',
            '# Croatia Work Guide',
            'Resources pages under /resources and /resources/{slug} are public and indexable.',
            '',
            '# Sitemap',
            '- XML sitemap: '.route('sitemap'),
            '',
            '# Crawl guidance',
            'Crawl public pages only. Do not crawl private areas such as /admin, /employer, /worker, or authentication/internal endpoints.',
        ];

        return response(implode("\n", $lines)."\n", 200, ['Content-Type' => 'text/plain; charset=UTF-8']);
    }

    private function url(string $loc, string $changefreq, string $priority, $lastmod = null): array
    {
        return [
            'loc' => $loc,
            'lastmod' => $lastmod instanceof Carbon ? $lastmod->toAtomString() : null,
            'changefreq' => $changefreq,
            'priority' => $priority,
        ];
    }

    private function baseSitemapUrls(): Collection
    {
        $urls = collect([
            $this->url(route('home'), 'daily', '1.0'),
            $this->url(route('jobs.index'), 'daily', '0.9'),
            $this->url(route('educations.index'), 'weekly', '0.85'),
            $this->url(route('resources.index'), 'weekly', '0.8'),
            $this->url(route('for-employers'), 'weekly', '0.75'),
            $this->url(route('about'), 'monthly', '0.7'),
            $this->url(route('contact'), 'monthly', '0.6'),
            $this->url(route('privacy'), 'yearly', '0.35'),
            $this->url(route('terms'), 'yearly', '0.35'),
            $this->url(route('cookies'), 'yearly', '0.3'),
        ]);

        foreach ($this->resourceSlugs() as $slug) {
            $urls->push($this->url(route('resources.show', $slug), 'monthly', '0.7'));
        }

        return $urls;
    }

    /**
     * @return array<int, string>
     */
    private function resourceSlugs(): array
    {
        return [
            'work-permits',
            'documents-needed',
            'accommodation',
            'working-in-croatia',
            'employer-obligations',
            'faq-foreign-workers',
        ];
    }
}
