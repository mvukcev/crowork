<x-app-layout>
    <x-slot name="title">CroWork - Job Board for Croatia</x-slot>
    <x-slot name="description">Find your dream job in Croatia. Browse thousands of job opportunities from top employers.</x-slot>
    <x-slot name="canonical">{{ route('home') }}</x-slot>

    <!-- Hero Section with Acrylic Search -->
    <x-hero
        size="lg"
        title="Work, learn, and thrive in Croatia"
        subtitle="CroWork brings verified jobs, education pathways, and employer support into one calm, modern marketplace for international talent.">
        <div class="max-w-3xl mx-auto">
            <!-- Acrylic Search Bar -->
            <form action="{{ route('jobs.index') }}" method="GET" class="relative">
                <div class="acrylic-surface rounded-3xl shadow-elevation-3 p-2.5 md:p-3 flex flex-col sm:flex-row gap-2 items-stretch sm:items-center motion-fade-in ring-1 ring-white/70" style="animation-delay: 140ms;">
                    <div class="flex-1 flex items-center px-4 min-h-[52px] bg-white/55 rounded-2xl">
                        <svg class="w-5 h-5 text-text-tertiary mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <input
                            type="text"
                            name="search"
                            placeholder="Job title, keywords, or company"
                            class="flex-1 py-3 text-body-lg bg-transparent border-0 focus:outline-none focus:ring-0 text-text-primary placeholder:text-text-tertiary"
                            value="{{ request('search') }}"
                        >
                    </div>
                    <x-button type="submit" variant="primary" size="lg" class="shadow-md hover:shadow-lg rounded-2xl whitespace-nowrap">
                        Search Jobs
                    </x-button>
                </div>
            </form>

            <!-- Quick Links -->
            <div class="flex flex-wrap items-center justify-center gap-2 mt-5 motion-fade-in" style="animation-delay: 180ms;">
                <span class="text-body-sm text-text-secondary">Popular:</span>
                <a href="{{ route('jobs.index', ['category' => 'Hospitality']) }}" class="text-body-sm text-primary hover:text-primary-hover transition-colors">Hospitality</a>
                <span class="text-text-tertiary">·</span>
                <a href="{{ route('jobs.index', ['category' => 'Tourism']) }}" class="text-body-sm text-primary hover:text-primary-hover transition-colors">Tourism</a>
                <span class="text-text-tertiary">·</span>
                <a href="{{ route('jobs.index', ['city' => 'Zagreb']) }}" class="text-body-sm text-primary hover:text-primary-hover transition-colors">Zagreb</a>
                <span class="text-text-tertiary">·</span>
                <a href="{{ route('jobs.index', ['city' => 'Split']) }}" class="text-body-sm text-primary hover:text-primary-hover transition-colors">Split</a>
            </div>
        </div>
    </x-hero>

    <!-- Featured Jobs -->
    <section class="section-spacing bg-white scroll-fade-in">
        <div class="container-base">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-semibold text-text-primary mb-4">Featured Jobs</h2>
                <p class="text-body-lg text-text-secondary max-w-2xl mx-auto">
                    Hand-picked opportunities for international talent seeking careers in Croatia
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

    <!-- How It Works Section -->
    <section class="section-spacing scroll-fade-in" style="background: linear-gradient(180deg, #FAFAFA 0%, #FFFFFF 100%);">
        <div class="container-base">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-semibold text-text-primary mb-4">How It Works</h2>
                <p class="text-body-lg text-text-secondary max-w-2xl mx-auto">
                    Three simple steps to your career in Croatia
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 lg:gap-12 stagger-children">
                <!-- Step 1 -->
                <div class="text-center group">
                    <div class="relative inline-flex items-center justify-center mb-6">
                        <!-- Step number -->
                        <div class="w-16 h-16 rounded-2xl bg-primary/10 flex items-center justify-center transition-all duration-300 group-hover:bg-primary/15 group-hover:scale-105">
                            <span class="text-2xl font-semibold text-primary">1</span>
                        </div>
                        <!-- Icon -->
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

                <!-- Step 2 -->
                <div class="text-center group">
                    <div class="relative inline-flex items-center justify-center mb-6">
                        <!-- Step number -->
                        <div class="w-16 h-16 rounded-2xl bg-accent/10 flex items-center justify-center transition-all duration-300 group-hover:bg-accent/15 group-hover:scale-105">
                            <span class="text-2xl font-semibold text-accent">2</span>
                        </div>
                        <!-- Icon -->
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

                <!-- Step 3 -->
                <div class="text-center group">
                    <div class="relative inline-flex items-center justify-center mb-6">
                        <!-- Step number -->
                        <div class="w-16 h-16 rounded-2xl bg-success/10 flex items-center justify-center transition-all duration-300 group-hover:bg-success/15 group-hover:scale-105">
                            <span class="text-2xl font-semibold text-success">3</span>
                        </div>
                        <!-- Icon -->
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

    <!-- Call to Action -->
    <section class="section-spacing bg-white scroll-fade-in">
        <div class="container-base">
            <div class="relative rounded-3xl overflow-hidden" style="background: linear-gradient(135deg, #EBF3FF 0%, #E6F5F3 100%);">
                <!-- Subtle shape -->
                <div class="absolute -top-20 -right-20 w-64 h-64 rounded-full"
                     style="background: linear-gradient(135deg, #346AF0 0%, #00B294 100%); filter: blur(60px); opacity: 0.1;">
                </div>

                <div class="relative z-10 px-8 py-12 md:px-12 md:py-16 text-center">
                    <h2 class="text-3xl md:text-4xl font-semibold text-text-primary mb-4">
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
