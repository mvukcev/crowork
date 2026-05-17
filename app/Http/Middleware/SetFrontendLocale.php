<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetFrontendLocale
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $enabledLocales = collect(setting('enabled_locales', ['en', 'hr']))
            ->filter(fn ($locale) => is_string($locale) && $locale !== '')
            ->map(fn (string $locale) => strtolower(trim($locale)))
            ->values()
            ->all();

        if ($enabledLocales === []) {
            $enabledLocales = ['en'];
        }

        $defaultLocale = strtolower((string) setting('default_platform_locale', config('app.locale', 'en')));
        if (! in_array($defaultLocale, $enabledLocales, true)) {
            $defaultLocale = $enabledLocales[0] ?? 'en';
        }

        $candidateLocales = [
            $request->query('lang'),
            optional($request->user())->communication_language,
            $request->session()->get('locale'),
            $request->cookie('cw_locale'),
            $request->getPreferredLanguage($enabledLocales),
            $defaultLocale,
        ];

        $resolvedLocale = $defaultLocale;

        foreach ($candidateLocales as $candidate) {
            if (! is_string($candidate) || $candidate === '') {
                continue;
            }

            $candidate = strtolower(trim($candidate));

            if (in_array($candidate, $enabledLocales, true)) {
                $resolvedLocale = $candidate;
                break;
            }
        }

        app()->setLocale($resolvedLocale);
        $request->setLocale($resolvedLocale);

        return $next($request);
    }
}
