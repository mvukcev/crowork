<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @php
        $seoTitle = ($title ?? config('app.name', 'CroWork')).' - Find Your Career in Croatia';
        $seoDescription = $description ?? 'CroWork connects international talent with Croatian employers. Find jobs, education opportunities, and build your career in Croatia.';
        $canonicalUrl = trim($__env->yieldContent('canonical')) ?: ($canonical ?? url()->current());
        $ogTitle = $ogTitle ?? ($title ?? config('app.name', 'CroWork'));
        $ogDescription = $ogDescription ?? $seoDescription;
        $ogType = $ogType ?? 'website';
        $ogImage = $ogImage ?? asset('assets/branding/CW-Logo-Dark.png');
        $robots = $robots ?? 'index,follow,max-image-preview:large,max-snippet:-1,max-video-preview:-1';
    @endphp

    <title>{{ $seoTitle }}</title>
    <meta name="description" content="{{ $seoDescription }}">
    <meta name="robots" content="{{ $robots }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">
    <link rel="icon" type="image/svg+xml" href="{{ asset('assets/branding/CW-Favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/branding/CW-Favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('assets/branding/CW-Favicon.png') }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:site_name" content="CroWork">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $ogTitle }}">
    <meta name="twitter:description" content="{{ $ogDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    <x-theme-init />

    @php
        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => config('app.name', 'CroWork'),
            'url' => url('/'),
            'logo' => asset('assets/branding/CW-Logo-Dark.png'),
        ];

        $websiteSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => config('app.name', 'CroWork'),
            'url' => url('/'),
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => url('/jobs').'?q={search_term_string}',
                'query-input' => 'required name=search_term_string',
            ],
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    <script type="application/ld+json">{!! json_encode($websiteSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Analytics & Tracking -->
    @include('components.analytics-head')

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
    $marketingEnabled = \App\Services\MetaPixelConfigService::isTrackingEnabled();
@endphp
<body @class([
    'h-full cw-page' => true,
    'cw-brand-display' => $useBrandDisplay,
])
data-cw-consent-required="{{ $consentRequired ? '1' : '0' }}"
data-cw-analytics-enabled="{{ $analyticsEnabled ? '1' : '0' }}"
data-cw-marketing-enabled="{{ $marketingEnabled ? '1' : '0' }}"
x-data>
    @php($isHome = request()->routeIs('home'))
    @php($isImpersonating = session('impersonation_original_admin_id'))
    <div class="min-h-screen flex flex-col">
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
                <div class="cw-surface p-4 bg-slate-50" role="alert">
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
                        src="{{ asset('assets/branding/CW-Logo-Dark.svg') }}"
                        alt="CroWork"
                        class="h-7 w-auto cw-logo-on-light"
                        onerror="this.style.display='none';"
                    >
                    <img
                        src="{{ asset('assets/branding/CW-Logo-Light.svg') }}"
                        alt="CroWork"
                        class="h-7 w-auto cw-logo-on-dark"
                        onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';"
                    >
                    <span class="hidden text-lg font-semibold text-slate-900">CroWork</span>
                    <div>
                        <p class="text-xs uppercase tracking-[0.08em] text-slate-500 mb-0">Migration platform</p>
                    </div>
                </a>
                <div class="flex flex-wrap items-center gap-4 md:gap-5">
                    <a href="{{ route('jobs.index') }}" class="cw-footer-link" data-cw-track-click="navigation_click">Jobs</a>
                    <a href="{{ route('educations.index') }}" class="cw-footer-link" data-cw-track-click="navigation_click">Educations</a>
                    <a href="{{ route('resources.index') }}" class="cw-footer-link" data-cw-track-click="navigation_click">Resources</a>
                    <a href="{{ route('resources.show', 'work-permits') }}" class="cw-footer-link">Work Permits</a>
                    <a href="{{ route('resources.show', 'faq-foreign-workers') }}" class="cw-footer-link">Worker FAQ</a>
                    <a href="{{ route('for-employers') }}" class="cw-footer-link" data-cw-track-click="navigation_click">For Employers</a>
                    <a href="{{ route('about') }}" class="cw-footer-link" data-cw-track-click="navigation_click">About</a>
                    <a href="{{ route('privacy') }}" class="cw-footer-link">Privacy</a>
                    <a href="{{ route('terms') }}" class="cw-footer-link">Terms</a>
                </div>
            </div>
            <div class="pt-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-2">
                <p class="text-sm text-slate-500 mb-0">© {{ date('Y') }} CroWork. All rights reserved.</p>
                <p class="text-xs uppercase tracking-[0.08em] text-slate-500 mb-0">Clear paths for work in Croatia</p>
            </div>
        </div>
    </footer>

    <section class="cw-cookie-banner cw-soft-reveal" data-cw-cookie-banner hidden>
        <div class="cw-cookie-inner">
            <p class="cw-cookie-text">
                We use cookies to improve your CroWork experience.
                Read our
                <a href="{{ route('cookies') }}" class="font-medium text-slate-900 underline">Cookie Statement</a>
                and choose which cookies you would like to accept.
            </p>
            <div class="cw-cookie-actions">
                <button type="button" class="cw-button-secondary" data-cw-cookie-choice="required">Required only</button>
                <button type="button" class="cw-button-primary" data-cw-cookie-choice="all">Allow all</button>
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
