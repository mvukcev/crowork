<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? config('app.name', 'CroWork') }} - Find Your Career in Croatia</title>
    <meta name="description" content="{{ $description ?? 'CroWork connects international talent with Croatian employers. Find jobs, education opportunities, and build your career in Croatia.' }}">
    <link rel="canonical" href="{{ trim($__env->yieldContent('canonical')) ?: ($canonical ?? url()->current()) }}">

    @php
        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => config('app.name', 'CroWork'),
            'url' => url('/'),
            'logo' => asset('favicon.ico'),
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

    @stack('head')
    @stack('styles')
</head>
<body class="h-full flex flex-col antialiased premium-page" x-data>
    <!-- Glass Header on Scroll -->
    <x-site-header />

    <!-- Main Content -->
    <main class="flex-1 pt-24">
        {{-- Flash Messages --}}
        @if(session('success'))
            <div class="bg-success-50 border-l-4 border-success-600 p-4 mb-6 mx-auto max-w-7xl mt-6" role="alert">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-success-600 flex-shrink-0 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-success-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-danger-50 border-l-4 border-danger-600 p-4 mb-6 mx-auto max-w-7xl mt-6" role="alert">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-danger-600 flex-shrink-0 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-danger-800">{{ session('error') }}</p>
                </div>
            </div>
        @endif

        @if(session('warning'))
            <div class="bg-warning-50 border-l-4 border-warning-600 p-4 mb-6 mx-auto max-w-7xl mt-6" role="alert">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-warning-600 flex-shrink-0 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="text-sm text-warning-800">{{ session('warning') }}</p>
                </div>
            </div>
        @endif

        @if(session('info'))
            <div class="bg-primary-50 border-l-4 border-primary-600 p-4 mb-6 mx-auto max-w-7xl mt-6" role="alert">
                <div class="flex items-start">
                    <svg class="w-5 h-5 text-primary-600 flex-shrink-0 mt-0.5 mr-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-primary-800">{{ session('info') }}</p>
                </div>
            </div>
        @endif

        <div class="fluent-enter">
            @yield('content', $slot ?? '')
        </div>
    </main>

    <!-- Footer -->
    <footer class="premium-footer-shell mt-auto relative z-10">
        <div class="container-base py-14 md:py-16">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-10 md:gap-8">
                <div class="md:col-span-4">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-11 h-11 rounded-2xl bg-white shadow-sm border border-white/80 flex items-center justify-center">
                            <span class="text-primary font-extrabold text-xl">C</span>
                        </div>
                        <div>
                            <p class="text-xl font-semibold text-text-primary mb-0">CroWork</p>
                            <p class="text-caption uppercase tracking-[0.11em] text-text-tertiary mb-0">Work in Croatia</p>
                        </div>
                    </div>
                    <p class="text-body text-text-secondary mb-5 max-w-md">
                        A premium platform for international workers and trusted Croatian employers, designed for clarity, confidence, and real opportunity.
                    </p>
                    <a href="{{ url('/jobs') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-white/80 border border-white text-body-sm font-semibold text-text-primary hover:bg-white transition-all duration-normal">
                        Explore Jobs
                        <span aria-hidden="true">→</span>
                    </a>
                </div>

                <div class="md:col-span-2">
                    <h3 class="text-body-sm uppercase tracking-[0.11em] font-semibold text-text-tertiary mb-4">Workers</h3>
                    <ul class="space-y-2.5">
                        <li><a href="{{ url('/jobs') }}" class="text-body-sm text-text-secondary hover:text-text-primary">Browse Jobs</a></li>
                        <li><a href="{{ url('/educations') }}" class="text-body-sm text-text-secondary hover:text-text-primary">Education</a></li>
                        <li><a href="{{ url('/profile') }}" class="text-body-sm text-text-secondary hover:text-text-primary">Create Profile</a></li>
                    </ul>
                </div>

                <div class="md:col-span-2">
                    <h3 class="text-body-sm uppercase tracking-[0.11em] font-semibold text-text-tertiary mb-4">Employers</h3>
                    <ul class="space-y-2.5">
                        <li><a href="{{ url('/for-employers') }}" class="text-body-sm text-text-secondary hover:text-text-primary">Why CroWork</a></li>
                        <li><a href="{{ url('/employer/register') }}" class="text-body-sm text-text-secondary hover:text-text-primary">Create Account</a></li>
                    </ul>
                </div>

                <div class="md:col-span-2">
                    <h3 class="text-body-sm uppercase tracking-[0.11em] font-semibold text-text-tertiary mb-4">Company</h3>
                    <ul class="space-y-2.5">
                        <li><a href="{{ url('/about') }}" class="text-body-sm text-text-secondary hover:text-text-primary">About</a></li>
                        <li><a href="{{ url('/contact') }}" class="text-body-sm text-text-secondary hover:text-text-primary">Contact</a></li>
                        <li><a href="{{ url('/pricing') }}" class="text-body-sm text-text-secondary hover:text-text-primary">Pricing</a></li>
                    </ul>
                </div>

                <div class="md:col-span-2">
                    <h3 class="text-body-sm uppercase tracking-[0.11em] font-semibold text-text-tertiary mb-4">Legal</h3>
                    <ul class="space-y-2.5">
                        <li><a href="{{ url('/privacy') }}" class="text-body-sm text-text-secondary hover:text-text-primary">Privacy</a></li>
                        <li><a href="{{ url('/terms') }}" class="text-body-sm text-text-secondary hover:text-text-primary">Terms</a></li>
                        <li><a href="{{ url('/cookies') }}" class="text-body-sm text-text-secondary hover:text-text-primary">Cookies</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-10 pt-6 border-t border-border/50 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-3">
                <p class="text-body-sm text-text-tertiary mb-0">© {{ date('Y') }} CroWork. All rights reserved.</p>
                <p class="text-caption text-text-tertiary mb-0">Built for global talent, designed with clarity.</p>
            </div>
        </div>
    </footer>

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
</body>
</html>
