<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class FrontendPreferenceController extends Controller
{
    public function locale(Request $request): RedirectResponse
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

        $requestedLocale = strtolower((string) $request->input('locale', $defaultLocale));
        $locale = in_array($requestedLocale, $enabledLocales, true) ? $requestedLocale : $defaultLocale;

        $request->session()->put('locale', $locale);
        Cookie::queue(Cookie::make('cw_locale', $locale, 60 * 24 * 365, null, null, $request->isSecure(), false, false, 'lax'));

        if ($request->user()) {
            $request->user()->forceFill([
                'communication_language' => $locale,
            ])->save();
        }

        return redirect()->to($this->safeRedirect($request));
    }

    public function theme(Request $request): RedirectResponse
    {
        $theme = (string) $request->input('theme', 'system');

        if (! in_array($theme, ['light', 'dark', 'system'], true)) {
            $theme = 'system';
        }

        $request->session()->put('theme', $theme);
        Cookie::queue(Cookie::make('cw_theme', $theme, 60 * 24 * 365, null, null, $request->isSecure(), false, false, 'lax'));

        return redirect()->to($this->safeRedirect($request));
    }

    private function safeRedirect(Request $request): string
    {
        $fallback = url('/');
        $redirect = (string) $request->input('redirect', '');

        if ($redirect === '') {
            return $request->headers->get('referer', $fallback) ?: $fallback;
        }

        if (str_starts_with($redirect, '/')) {
            return url($redirect);
        }

        if (str_starts_with($redirect, url('/'))) {
            return $redirect;
        }

        return $fallback;
    }
}
