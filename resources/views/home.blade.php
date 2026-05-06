<x-app-layout>
    <x-slot name="title">CroWork - Job Board for Croatia</x-slot>
    <x-slot name="description">Find your dream job in Croatia. Browse thousands of job opportunities from top employers.</x-slot>
    <x-slot name="canonical">{{ route('home') }}</x-slot>

    <!-- Immersive Hero -->
    <x-hero
        size="lg"
        title="Work, learn, and thrive in Croatia"
        subtitle="A premium platform connecting international workers with verified Croatian employers, clear pathways, and real relocation confidence.">
        <div class="max-w-4xl mx-auto">
            <form action="{{ route('jobs.index') }}" method="GET" class="relative">
                <div class="hero-command flex flex-col sm:flex-row gap-2 items-stretch sm:items-center motion-fade-in" style="animation-delay: 140ms;">
                    <div class="flex-1 flex items-center px-2">
                        <svg class="w-5 h-5 text-text-tertiary mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input
                            type="text"
                            name="search"
                            placeholder="Search roles, employers, or keywords"
                            class="hero-command-input"
                            value="{{ request('search') }}"
                        >
                    </div>
                    <x-button type="submit" variant="primary" size="lg" class="rounded-2xl whitespace-nowrap px-7">
                        Search Jobs
                    </x-button>
                </div>
            </form>

            <div class="flex flex-wrap items-center justify-center gap-2 mt-6 motion-fade-in" style="animation-delay: 180ms;">
                <span class="text-body-sm text-text-secondary">Popular:</span>
                <a href="{{ route('jobs.index', ['category' => 'Hospitality']) }}" class="premium-chip">Hospitality</a>
                <a href="{{ route('jobs.index', ['category' => 'Tourism']) }}" class="premium-chip">Tourism</a>
                <a href="{{ route('jobs.index', ['city' => 'Zagreb']) }}" class="premium-chip">Zagreb</a>
                <a href="{{ route('jobs.index', ['city' => 'Split']) }}" class="premium-chip">Split</a>
            </div>

            <div class="mt-10 md:mt-12 grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4 max-w-3xl mx-auto">
                <div class="premium-glass rounded-2xl p-4 text-left">
                    <p class="text-caption uppercase tracking-[0.1em] text-text-tertiary mb-1">Verified Employers</p>
                    <p class="text-title-2 font-semibold text-text-primary mb-0">Trusted companies only</p>
                </div>
                <div class="premium-glass rounded-2xl p-4 text-left">
                    <p class="text-caption uppercase tracking-[0.1em] text-text-tertiary mb-1">Smart Matching</p>
                    <p class="text-title-2 font-semibold text-text-primary mb-0">Clarity before applying</p>
                </div>
                <div class="premium-glass rounded-2xl p-4 text-left">
                    <p class="text-caption uppercase tracking-[0.1em] text-text-tertiary mb-1">Built for Relocation</p>
                    <p class="text-title-2 font-semibold text-text-primary mb-0">Jobs and education together</p>
                </div>
            </div>
        </div>
    </x-hero>

    <section class="section-spacing scroll-fade-in">
        <div class="container-base">
            <div class="text-center mb-14 md:mb-16">
                <h2 class="text-text-primary mb-4">Featured Jobs</h2>
                <p class="text-body-lg text-text-secondary max-w-2xl mx-auto mb-0">
                    Curated openings from verified employers, designed for fast comparison and confident decisions.
                </p>
            </div>

            @if($featuredJobs->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 stagger-children">
                    @foreach($featuredJobs as $job)
                        <x-job-card
                            :title="$job->title"
                            :company="$job->company_name"
                            :city="$job->location_city"
                            :salary_min="$job->salary_min"
                            :salary_max="$job->salary_max"
                            :salary_currency="$job->salary_currency ?? 'EUR'"
                            :salary_period="$job->salary_period ?? 'month'"
                            :accommodation_provided="$job->accommodation_provided"
                            :languages="$job->languages"
                            :posted_at="$job->published_at ?? $job->created_at"
                            :href="route('jobs.show', $job)"
                        />
                    @endforeach
                </div>

                <div class="text-center mt-12">
                    <x-button href="{{ route('jobs.index') }}" variant="primary" size="lg">
                        View All Jobs
                    </x-button>
                </div>
            @else
                <div class="text-center py-16">
                    <div class="w-16 h-16 bg-surface-tinted rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <p class="text-body text-text-secondary mb-6">
                        No jobs available at the moment. Check back soon!
                    </p>
                    @auth
                        @if(auth()->user()->isEmployer())
                            <x-button href="{{ route('employer.jobs.create') }}" variant="primary">
                                Post Your First Job
                            </x-button>
                        @endif
                    @endauth
                </div>
            @endif
        </div>
    </section>

    <section class="section-spacing scroll-fade-in">
        <div class="container-base">
            <div class="text-center mb-16">
                <h2 class="text-text-primary mb-4">How It Works</h2>
                <p class="text-body-lg text-text-secondary max-w-2xl mx-auto mb-0">
                    Three simple steps to your career in Croatia
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12 stagger-children">
                <div class="premium-glass rounded-3xl p-6 md:p-7 text-center group">
                    <div class="relative inline-flex items-center justify-center mb-6">
                        <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center transition-all duration-300 group-hover:bg-primary/15">
                            <span class="text-2xl font-semibold text-primary">1</span>
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-10 h-10 rounded-xl bg-white shadow-elevation-2 flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-text-primary mb-3">Search Jobs</h3>
                    <p class="text-body text-text-secondary leading-relaxed">
                        Browse thousands of job opportunities from verified Croatian employers
                    </p>
                </div>

                <div class="premium-glass rounded-3xl p-6 md:p-7 text-center group">
                    <div class="relative inline-flex items-center justify-center mb-6">
                        <div class="w-16 h-16 rounded-2xl bg-accent/10 flex items-center justify-center transition-all duration-300 group-hover:bg-accent/15">
                            <span class="text-2xl font-semibold text-accent">2</span>
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-10 h-10 rounded-xl bg-white shadow-elevation-2 flex items-center justify-center">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-text-primary mb-3">Apply Online</h3>
                    <p class="text-body text-text-secondary leading-relaxed">
                        Submit your application with your profile and get instant confirmation
                    </p>
                </div>

                <div class="premium-glass rounded-3xl p-6 md:p-7 text-center group">
                    <div class="relative inline-flex items-center justify-center mb-6">
                        <div class="w-16 h-16 rounded-2xl bg-success/10 flex items-center justify-center transition-all duration-300 group-hover:bg-success/15">
                            <span class="text-2xl font-semibold text-success">3</span>
                        </div>
                        <div class="absolute -bottom-1 -right-1 w-10 h-10 rounded-xl bg-white shadow-elevation-2 flex items-center justify-center">
                            <svg class="w-5 h-5 text-success" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold text-text-primary mb-3">Get Hired</h3>
                    <p class="text-body text-text-secondary leading-relaxed">
                        Connect with employers and start your exciting career journey in Croatia
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="section-spacing scroll-fade-in">
        <div class="container-base">
            <div class="relative premium-glass rounded-[2rem] overflow-hidden px-8 py-12 md:px-12 md:py-16 text-center">
                <div class="absolute -top-16 -right-16 w-64 h-64 rounded-full blur-[70px] opacity-60" style="background: rgba(95, 141, 255, 0.25);"></div>
                <div class="absolute -bottom-16 -left-16 w-64 h-64 rounded-full blur-[74px] opacity-55" style="background: rgba(43, 193, 176, 0.2);"></div>

                <div class="relative z-10">
                    <h2 class="text-text-primary mb-4">
                        Ready to Start Your Career in Croatia?
                    </h2>
                    <p class="text-body-lg text-text-secondary max-w-2xl mx-auto mb-8">
                        Join thousands of international professionals who found their dream jobs through CroWork
                    </p>

                    <div class="flex flex-wrap items-center justify-center gap-4">
                        @guest
                            <x-button href="{{ route('register') }}" variant="primary" size="lg" class="shadow-elevation-2 hover:shadow-elevation-3">
                                Get Started Now
                            </x-button>
                            <x-button href="{{ route('jobs.index') }}" variant="outline" size="lg">
                                Browse All Jobs
                            </x-button>
                        @else
                            <x-button href="{{ route('jobs.index') }}" variant="primary" size="lg" class="shadow-elevation-2 hover:shadow-elevation-3">
                                Browse All Jobs
                            </x-button>
                        @endguest
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
