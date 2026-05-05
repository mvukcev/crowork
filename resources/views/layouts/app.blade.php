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
<body class="h-full flex flex-col antialiased" style="background-color: #FAFAFA;" x-data>
    <!-- Glass Header on Scroll -->
    <x-site-header />

    <!-- Main Content -->
    <main class="flex-1 pt-16">
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
    <footer class="bg-white border-t border-border/50 mt-auto">
        <div class="container-base py-12">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <!-- Brand -->
                <div class="col-span-1">
                    <div class="flex items-center space-x-2 mb-4">
                        <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center shadow-sm">
                            <span class="text-white font-bold text-xl">C</span>
                        </div>
                        <span class="text-xl font-semibold text-text-primary">CroWork</span>
                    </div>
                    <p class="text-body text-text-secondary leading-relaxed">
                        Connecting international talent with Croatian opportunities.
                    </p>
                </div>

                <!-- For Job Seekers -->
                <div>
                    <h3 class="text-body-lg font-semibold text-text-primary mb-4">For Job Seekers</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ url('/jobs') }}" class="text-body text-text-secondary hover:text-primary transition-colors duration-normal">Browse Jobs</a></li>
                        <li><a href="{{ url('/educations') }}" class="text-body text-text-secondary hover:text-primary transition-colors duration-normal">Education Programs</a></li>
                        <li><a href="{{ url('/profile') }}" class="text-body text-text-secondary hover:text-primary transition-colors duration-normal">Create Profile</a></li>
                    </ul>
                </div>

                <!-- For Employers -->
                <div>
                    <h3 class="text-body-lg font-semibold text-text-primary mb-4">For Employers</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ url('/for-employers') }}" class="text-body text-text-secondary hover:text-primary transition-colors duration-normal">Why CroWork</a></li>
                        <li><a href="{{ url('/employer/register') }}" class="text-body text-text-secondary hover:text-primary transition-colors duration-normal">Create Account</a></li>
                    </ul>
                </div>

                <!-- Company -->
                <div>
                    <h3 class="text-body-lg font-semibold text-text-primary mb-4">Company</h3>
                    <ul class="space-y-3">
                        <li><a href="{{ url('/about') }}" class="text-body text-text-secondary hover:text-primary transition-colors duration-normal">About Us</a></li>
                        <li><a href="{{ url('/contact') }}" class="text-body text-text-secondary hover:text-primary transition-colors duration-normal">Contact</a></li>
                        <li><a href="{{ url('/privacy') }}" class="text-body text-text-secondary hover:text-primary transition-colors duration-normal">Privacy Policy</a></li>
                        <li><a href="{{ url('/terms') }}" class="text-body text-text-secondary hover:text-primary transition-colors duration-normal">Terms of Service</a></li>
                        <li><a href="{{ url('/cookies') }}" class="text-body text-text-secondary hover:text-primary transition-colors duration-normal">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t border-border/50 mt-10 pt-8">
                <p class="text-body-sm text-text-tertiary text-center">
                    &copy; {{ date('Y') }} CroWork. All rights reserved.
                </p>
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
