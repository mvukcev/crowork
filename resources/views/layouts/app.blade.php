<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $enabledLocales = collect(setting('enabled_locales', ['en', 'hr']))
            ->filter(fn ($locale) => is_string($locale) && $locale !== '')
            ->map(fn ($locale) => strtolower(trim((string) $locale)))
            ->values()
            ->all();

        if ($enabledLocales === []) {
            $enabledLocales = ['en'];
        }

        $defaultLocale = strtolower((string) setting('default_platform_locale', config('app.locale', 'en')));
        if (! in_array($defaultLocale, $enabledLocales, true)) {
            $defaultLocale = $enabledLocales[0] ?? 'en';
        }

        $activeLocale = strtolower((string) app()->getLocale());
        if (! in_array($activeLocale, $enabledLocales, true)) {
            $activeLocale = $defaultLocale;
        }

        $baseCanonical = trim($__env->yieldContent('canonical')) ?: ($canonical ?? url()->current());
        $canonicalParts = parse_url($baseCanonical);
        $canonicalQuery = [];
        if (! empty($canonicalParts['query'])) {
            parse_str($canonicalParts['query'], $canonicalQuery);
        }

        $buildLocaleUrl = function (string $locale) use ($canonicalParts, $canonicalQuery, $defaultLocale): string {
            $query = $canonicalQuery;
            if ($locale === $defaultLocale) {
                unset($query['lang']);
            } else {
                $query['lang'] = $locale;
            }

            $scheme = $canonicalParts['scheme'] ?? request()->getScheme();
            $host = $canonicalParts['host'] ?? request()->getHost();
            $port = isset($canonicalParts['port']) ? ':' . $canonicalParts['port'] : '';
            $path = $canonicalParts['path'] ?? request()->getPathInfo();
            $queryString = http_build_query($query);

            return $scheme . '://' . $host . $port . $path . ($queryString !== '' ? '?' . $queryString : '');
        };

        $canonicalUrl = $buildLocaleUrl($activeLocale);
        $xDefaultUrl = $buildLocaleUrl($defaultLocale);
        $hreflangMap = collect($enabledLocales)->mapWithKeys(fn (string $locale) => [$locale => $buildLocaleUrl($locale)])->all();

        $seoTitle = $title ?? config('app.name', 'CroWork');
        $seoDescription = $description ?? __('seo.defaults.description');
        $ogTitle = $ogTitle ?? $seoTitle;
        $ogDescription = $ogDescription ?? $seoDescription;
        $ogType = $ogType ?? 'website';
        $ogImage = $ogImage ?? marketing_image_url('social.og_default') ?? cw_asset('assets/branding/CW-Logo-Dark.png');
        $robots = $robots ?? 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1';
        $localeMap = [
            'en' => 'en_US',
            'hr' => 'hr_HR',
        ];
        $ogLocale = $localeMap[$activeLocale] ?? str_replace('-', '_', app()->getLocale());
    @endphp

    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="robots" content="{{ $robots }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    @foreach($hreflangMap as $locale => $href)
        <link rel="alternate" hreflang="{{ $locale }}" href="{{ $href }}">
    @endforeach
    <link rel="alternate" hreflang="x-default" href="{{ $xDefaultUrl }}">
    <link rel="icon" type="image/svg+xml" href="{{ cw_asset('assets/branding/CW-Favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ cw_asset('assets/branding/CW-Favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ cw_asset('assets/branding/CW-Favicon.png') }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:site_name" content="CroWork">
    <meta property="og:locale" content="{{ $ogLocale }}">
    @foreach($enabledLocales as $locale)
        @if($locale !== $activeLocale && isset($localeMap[$locale]))
            <meta property="og:locale:alternate" content="{{ $localeMap[$locale] }}">
        @endif
    @endforeach
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    @if(filled(setting('google_search_console_verification')))
        <meta name="google-site-verification" content="{{ setting('google_search_console_verification') }}">
    @endif

    <x-theme-init />

    {{-- No slot or variable output before body! --}}

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Analytics & Tracking -->
    @include('components.analytics-head')

    @php
        $queuedTrackEvents = session()->pull('cw_track_queue', []);
    @endphp
    @if(is_array($queuedTrackEvents) && count($queuedTrackEvents) > 0)
        <script>
            window.__cwTrackQueue = @json($queuedTrackEvents);
        </script>
    @endif

    @stack('head')
    @stack('styles')
</head>
@php
    $brandDisplayRoutes = [
        'home',
        'jobs.index',
        'jobs.show',
        'educations.index',
        'educations.show',
        'companies.show',
        'resources.index',
        'resources.show',
        'about',
        'for-employers',
        'pricing',
        'contact',
        'coming-soon',
    ];

    $useBrandDisplay = request()->routeIs($brandDisplayRoutes);
@endphp
@php
    $consentRequired = \App\Services\ConsentConfigService::isConsentRequired();
    $analyticsEnabled = \App\Services\AnalyticsConfigService::isAnalyticsEnabled();
    $marketingEnabled = \App\Services\MetaPixelConfigService::isTrackingEnabled()
        && (bool) config('meta.browser_enabled', true)
        && filled(config('meta.pixel_id'));
    $trackDebug = app()->environment('local') || config('app.debug');
@endphp
<body @class([
    'h-full cw-page' => true,
    'cw-brand-display' => ($useBrandDisplay ?? false),
])
data-cw-consent-required="{{ $consentRequired ? '1' : '0' }}"
data-cw-analytics-enabled="{{ $analyticsEnabled ? '1' : '0' }}"
data-cw-marketing-enabled="{{ $marketingEnabled ? '1' : '0' }}"
data-cw-track-debug="{{ $trackDebug ? '1' : '0' }}"
x-data>
    @php($isHome = request()->routeIs('home'))
    @php($isImpersonating = session('impersonation_original_admin_id'))
    <div class="min-h-screen flex flex-col cw-page-shell">
    <div class="cw-page-ambient cw-organic-bg" aria-hidden="true">
        <span class="cw-orb cw-orb-blue hidden md:block" style="width: 420px; height: 420px; left: -140px; top: 5rem;"></span>
        <span class="cw-orb cw-orb-orange hidden md:block" style="width: 360px; height: 360px; right: -120px; top: 16rem;"></span>
        <span class="cw-orb cw-orb-yellow hidden lg:block" style="width: 240px; height: 240px; right: 18%; bottom: 8rem;"></span>
    </div>
    <x-site-header />
    @include('components.impersonation-banner')

    <!-- Main Content -->
    <main @class([
        'flex-1' => true,
        'pt-0' => $isHome,
        'pt-16 md:pt-[72px]' => ! $isHome,
        'pt-28 md:pt-[124px]' => $isImpersonating,
    ])>
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="cw-container mt-6 mb-6">
                <div class="cw-surface p-4 border-emerald-200 bg-emerald-50" role="alert">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-emerald-700 flex-shrink-0 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-emerald-700">{{ session('success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="cw-container mt-6 mb-6">
                <div class="cw-surface p-4 border-red-200 bg-red-50" role="alert">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-700 flex-shrink-0 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-red-700">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="cw-container mt-6 mb-6">
                <div class="cw-surface p-4 border-amber-200 bg-amber-50" role="alert">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-amber-700 flex-shrink-0 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <p class="text-sm text-amber-700">{{ session('warning') }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="cw-container mt-6 mb-6">
                <div class="cw-surface p-4" role="alert">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-slate-600 flex-shrink-0 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p class="text-sm text-slate-600">{{ session('info') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <div>
            @yield('content', $slot ?? '')
        </div>
    </main>

    <!-- Footer -->
    <footer class="cw-footer mt-auto">
        <div class="cw-orb cw-orb-violet hidden md:block" style="width: 360px; height: 360px; right: -120px; bottom: -170px;"></div>
        <div class="cw-container py-8 md:py-10">
            <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 pb-5 border-b border-slate-200">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <img
                        src="{{ cw_asset('assets/branding/CW-Logo-Dark.svg') }}"
                        alt="CroWork"
                        class="h-7 w-auto cw-logo-on-light"
                        onerror="this.style.display='none';"
                    >
                    <img
                        src="{{ cw_asset('assets/branding/CW-Logo-Light.svg') }}"
                        alt="CroWork"
                        class="h-7 w-auto cw-logo-on-dark"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';"
                    >
                    <span class="hidden text-lg font-semibold text-slate-900">CroWork</span>
                </a>
                <div class="flex flex-wrap items-center gap-4 md:gap-5">
                    <a href="{{ route('jobs.index') }}" class="cw-footer-link" data-cw-track-click="navigation_click">{{ __('navigation.jobs') }}</a>
                    <a href="{{ route('educations.index') }}" class="cw-footer-link" data-cw-track-click="navigation_click">{{ __('navigation.educations') }}</a>
                    <a href="{{ route('resources.index') }}" class="cw-footer-link" data-cw-track-click="navigation_click">{{ __('navigation.resources') }}</a>
                    <a href="{{ route('resources.show', 'work-permits') }}" class="cw-footer-link">{{ __('footer.work_permits') }}</a>
                    <a href="{{ route('resources.show', 'faq-foreign-workers') }}" class="cw-footer-link">{{ __('footer.worker_faq') }}</a>
                    <a href="{{ route('for-employers') }}" class="cw-footer-link" data-cw-track-click="navigation_click">{{ __('navigation.for_employers') }}</a>
                    <a href="{{ route('about') }}" class="cw-footer-link" data-cw-track-click="navigation_click">{{ __('navigation.about') }}</a>
                    <a href="{{ route('privacy') }}" class="cw-footer-link">{{ __('footer.privacy') }}</a>
                    <a href="{{ route('terms') }}" class="cw-footer-link">{{ __('footer.terms') }}</a>
                </div>
            </div>
            <div class="pt-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                <p class="text-sm text-slate-500 mb-0">{{ __('footer.copyright', ['year' => date('Y')]) }}</p>
                <p class="text-xs uppercase tracking-[0.08em] text-slate-500 mb-0">{{ __('footer.slogan') }}</p>
            </div>
        </div>
    </footer>

    <section class="cw-cookie-banner cw-soft-reveal" data-cw-cookie-banner hidden>
        <div class="cw-cookie-inner">
            <p class="cw-cookie-text">
                {!! __('footer.cookie.text', [
                    'link' => '<a href="' . route('cookies') . '" class="font-medium text-slate-900 underline">' . __('footer.cookie.link_label') . '</a>',
                ]) !!}
            </p>
            <div class="cw-cookie-actions">
                <button type="button" class="cw-button-secondary" data-cw-cookie-choice="required">{{ __('footer.cookie.required_only') }}</button>
                <button type="button" class="cw-button-secondary" data-cw-cookie-choice="customize">{{ __('footer.cookie.customize') }}</button>
                <button type="button" class="cw-button-primary" data-cw-cookie-choice="all">{{ __('footer.cookie.allow_all') }}</button>
            </div>
        </div>
    </section>

    <section class="cw-cookie-modal" data-cw-cookie-modal hidden>
        <div class="cw-cookie-modal-card">
            <h3 class="text-lg font-semibold text-slate-900">{{ __('footer.cookie.preferences_title') }}</h3>
            <p class="text-sm text-slate-600">{{ __('footer.cookie.preferences_description') }}</p>
            <label class="cw-cookie-toggle-row">
                <input type="checkbox" data-cw-cookie-analytics>
                <span>{{ __('footer.cookie.analytics_label') }}</span>
            </label>
            <label class="cw-cookie-toggle-row">
                <input type="checkbox" data-cw-cookie-marketing>
                <span>{{ __('footer.cookie.marketing_label') }}</span>
            </label>
            <div class="cw-cookie-actions">
                <button type="button" class="cw-button-secondary" data-cw-cookie-choice="required">{{ __('footer.cookie.required_only') }}</button>
                <button type="button" class="cw-button-primary" data-cw-cookie-save>{{ __('footer.cookie.save_preferences') }}</button>
            </div>
        </div>
    </section>
    </div>

    @stack('scripts')
    
    <!-- Scroll Fade-In Animation -->
    <script>
        // Simple scroll-based fade-in animation
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });

            // Observe all elements with scroll-fade-in class
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.scroll-fade-in').forEach(el => {
                    observer.observe(el);
                });
            });
        } else {
            // Fallback for older browsers: just show everything
            document.addEventListener('DOMContentLoaded', () => {
                document.querySelectorAll('.scroll-fade-in').forEach(el => {
                    el.classList.add('visible');
                });
            });
        }
    </script>

    @include('components.analytics-noscript')
</body>
</html>
