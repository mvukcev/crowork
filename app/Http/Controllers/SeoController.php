<?php

namespace App\Http\Controllers;

use App\Models\Education;
use App\Models\Employer;
use App\Models\Job;
use App\Models\ResourcePost;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class SeoController extends Controller
{
    /**
     * @var array<int, string>
     */
    private array $locales = [];

    public function sitemap(): Response
    {
        $this->locales = $this->enabledLocales();
        $urls = $this->baseSitemapUrls();

        Job::query()
            ->active()
            ->select(['id', 'slug', 'updated_at', 'published_at', 'created_at'])
            ->orderBy('id')
            ->chunkById(500, function (Collection $jobs) use ($urls): void {
                foreach ($jobs as $job) {
                    foreach ($this->localizedUrls(route('jobs.show', $job)) as $localizedUrl) {
                        $urls->push($this->url(
                            $localizedUrl,
                            'daily',
                            '0.85',
                            $job->updated_at ?? $job->published_at ?? $job->created_at
                        ));
                    }
                }
            });

        Education::query()
            ->active()
            ->select(['id', 'slug', 'updated_at', 'published_at', 'created_at'])
            ->orderBy('id')
            ->chunkById(500, function (Collection $educations) use ($urls): void {
                foreach ($educations as $education) {
                    foreach ($this->localizedUrls(route('educations.show', $education)) as $localizedUrl) {
                        $urls->push($this->url(
                            $localizedUrl,
                            'weekly',
                            '0.75',
                            $education->updated_at ?? $education->published_at ?? $education->created_at
                        ));
                    }
                }
            });

        Employer::query()
            ->whereNotNull('approved_at')
            ->whereNotNull('slug')
            ->select(['id', 'slug', 'updated_at', 'approved_at'])
            ->orderBy('id')
            ->chunkById(500, function (Collection $companies) use ($urls): void {
                foreach ($companies as $company) {
                    foreach ($this->localizedUrls(route('companies.show', $company)) as $localizedUrl) {
                        $urls->push($this->url(
                            $localizedUrl,
                            'weekly',
                            '0.7',
                            $company->updated_at ?? $company->approved_at
                        ));
                    }
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
        $urls = collect();

        $baseEntries = [
            [route('home'), 'daily', '1.0'],
            [route('jobs.index'), 'daily', '0.9'],
            [route('educations.index'), 'weekly', '0.85'],
            [route('resources.index'), 'weekly', '0.8'],
            [route('for-employers'), 'weekly', '0.75'],
            [route('about'), 'monthly', '0.7'],
            [route('contact'), 'monthly', '0.6'],
            [route('privacy'), 'yearly', '0.35'],
            [route('terms'), 'yearly', '0.35'],
            [route('cookies'), 'yearly', '0.3'],
        ];

        foreach ($baseEntries as [$url, $changefreq, $priority]) {
            foreach ($this->localizedUrls($url) as $localizedUrl) {
                $urls->push($this->url($localizedUrl, $changefreq, $priority));
            }
        }

        foreach ($this->resourceSlugs() as $slug) {
            foreach ($this->localizedUrls(route('resources.show', $slug)) as $localizedUrl) {
                $urls->push($this->url($localizedUrl, 'monthly', '0.7'));
            }
        }

        return $urls;
    }

    /**
     * @return array<int, string>
     */
    private function enabledLocales(): array
    {
        $locales = collect(setting('enabled_locales', ['en', 'hr']))
            ->filter(fn ($locale) => is_string($locale) && $locale !== '')
            ->map(fn (string $locale) => strtolower(trim($locale)))
            ->values()
            ->all();

        return $locales === [] ? ['en'] : $locales;
    }

    /**
     * @return array<int, string>
     */
    private function localizedUrls(string $url): array
    {
        $defaultLocale = strtolower((string) setting('default_platform_locale', config('app.locale', 'en')));
        if (! in_array($defaultLocale, $this->locales, true)) {
            $defaultLocale = $this->locales[0] ?? 'en';
        }

        $urls = [$url];
        foreach ($this->locales as $locale) {
            if ($locale === $defaultLocale) {
                continue;
            }

            $urls[] = $this->withLocaleQuery($url, $locale);
        }

        return $urls;
    }

    private function withLocaleQuery(string $url, string $locale): string
    {
        $parts = parse_url($url);
        $query = [];
        if (! empty($parts['query'])) {
            parse_str($parts['query'], $query);
        }

        $query['lang'] = $locale;
        $queryString = http_build_query($query);

        $scheme = $parts['scheme'] ?? 'https';
        $host = $parts['host'] ?? parse_url(config('app.url', ''), PHP_URL_HOST);
        $port = isset($parts['port']) ? ':'.$parts['port'] : '';
        $path = $parts['path'] ?? '/';

        return $scheme.'://'.$host.$port.$path.($queryString !== '' ? '?'.$queryString : '');
    }

    /**
     * @return array<int, string>
     */
    private function resourceSlugs(): array
    {
        $staticSlugs = [
            'work-permits',
            'documents-needed',
            'accommodation',
            'working-in-croatia',
            'employer-obligations',
            'faq-foreign-workers',
        ];

        $postSlugs = ResourcePost::query()
            ->published()
            ->pluck('slug')
            ->all();

        return array_values(array_unique(array_merge($staticSlugs, $postSlugs)));
    }
}
