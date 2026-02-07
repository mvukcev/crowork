@extends('layouts.app')

@section('content')

@php
    // Format salary for display (reusing same logic as job-card)
    $currencySymbol = $job->salary_currency === 'EUR' ? '€' : $job->salary_currency;
    $periodText = $job->salary_period === 'hour' ? 'hour' : 'month';
    
    if ($job->salary_min && $job->salary_max) {
        $salaryDisplay = $currencySymbol . number_format($job->salary_min, 0, '.', ',') . ' – ' . 
                        $currencySymbol . number_format($job->salary_max, 0, '.', ',') . ' / ' . $periodText;
    } elseif ($job->salary_min) {
        $salaryDisplay = 'From ' . $currencySymbol . number_format($job->salary_min, 0, '.', ',') . ' / ' . $periodText;
    } elseif ($job->salary_max) {
        $salaryDisplay = 'Up to ' . $currencySymbol . number_format($job->salary_max, 0, '.', ',') . ' / ' . $periodText;
    } else {
        $salaryDisplay = 'Not specified';
    }
    
    // Format posted time
    $postedDisplay = $job->published_at ? $job->published_at->diffForHumans() : 'Recently posted';
    
    // Languages array
    $languages = $job->languages ?? [];
    
    // Determine if user can apply
    $canApply = auth()->check() && auth()->user()->isWorker();
    $shouldShowDisabled = auth()->check() && !auth()->user()->isWorker();
@endphp

@push('styles')
    <meta name="canonical" href="{{ route('jobs.show', $job) }}">
@endpush

<!-- Hero Section -->
<x-hero 
    size="sm" 
    :title="$job->title" 
    :subtitle="$job->employer->company_name . ' • ' . $job->location_city">
</x-hero>

<main class="min-h-screen flex flex-col section-spacing">
    <div class="container-base">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content Area -->
            <div class="lg:col-span-2">
                <!-- Hero Content Card -->
                <x-surface variant="base" elevation="1" rounded="card" padding="6" class="mb-6">
                    <!-- Breadcrumb -->
                    <nav class="flex items-center text-body-sm text-text-secondary mb-6 pb-6 border-b border-border">
                        <a href="{{ route('home') }}" class="hover:text-primary transition-colors duration-normal">Home</a>
                        <svg class="w-4 h-4 mx-2 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                            <a href="{{ route('jobs.index') }}" class="hover:text-primary transition-colors duration-normal">Jobs</a>
                            <svg class="w-4 h-4 mx-2 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <span class="text-text-primary font-medium">{{ $job->title }}</span>
                        </nav>

                        <!-- Job Title -->
                        <h1 class="text-display-md font-semibold text-text-primary mb-4">
                            {{ $job->title }}
                        </h1>

                        <!-- Company and Location -->
                        <div class="flex flex-col md:flex-row md:items-center gap-4 mb-6">
                            <div class="flex items-center text-title-2 text-text-secondary">
                                @if($job->employer)
                                    <span class="font-medium text-text-primary">{{ $job->employer->company_name }}</span>
                                    <span class="mx-2 text-text-tertiary">•</span>
                                @endif
                                <svg class="w-5 h-5 mr-1.5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span>{{ $job->location_city }}</span>
                            </div>
                            <span class="hidden md:inline text-text-tertiary">•</span>
                            <span class="text-body-sm text-text-tertiary">Posted {{ $postedDisplay }}</span>
                        </div>

                        <!-- Salary Highlight -->
                        <x-surface variant="tinted" elevation="0" rounded="control" padding="4" class="mb-6 border-primary/20">
                            <div class="flex items-baseline">
                                <svg class="w-6 h-6 text-primary mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-title-1 font-semibold text-primary">{{ $salaryDisplay }}</span>
                            </div>
                        </x-surface>

                        <!-- Badges Row -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            @if($job->accommodation_provided)
                                <x-chip tone="success" size="md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                                    </svg>
                                    Accommodation Provided
                                </x-chip>
                            @endif

                            @if(!empty($languages))
                                <x-chip tone="info" size="md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"></path>
                                    </svg>
                                    {{ implode(', ', array_slice($languages, 0, 3)) }}{{ count($languages) > 3 ? ' +' . (count($languages) - 3) : '' }}
                                </x-chip>
                            @endif

                            @if($job->contract_type)
                                <x-chip tone="neutral" size="md">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    {{ ucfirst($job->contract_type) }}
                                </x-chip>
                            @endif
                        </div>
                    </x-card>
                </div>

                <!-- Sidebar Apply Panel -->
                <div class="lg:col-span-1">
                    <!-- Desktop Apply Card -->
                    <div class="hidden lg:block sticky top-24">
                        <x-surface variant="base" elevation="2" rounded="card" padding="6" class="border-stroke-subtle">
                            <!-- CTA Button -->
                            @auth
                                @if(auth()->user()->isWorker())
                                    <x-button 
                                        href="{{ route('jobs.apply', $job) }}"
                                        variant="primary" 
                                        class="w-full mb-6 py-3 text-base font-semibold"
                                    >
                                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        Apply Now
                                    </x-button>
                                @else
                                    <div class="mb-6 p-3 bg-warning-light border border-warning-border rounded-lg">
                                        <p class="text-body-sm text-warning-text">
                                            <strong>Note:</strong> You're logged in as an employer or admin. Switch to a worker account to apply.
                                        </p>
                                    </div>
                                @endif
                            @else
                                <x-button 
                                    href="{{ route('login') }}?redirect={{ route('jobs.show', $job) }}"
                                    variant="primary" 
                                    class="w-full mb-6 py-3 text-base font-semibold"
                                >
                                    <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v2a2 2 0 01-2 2H7a2 2 0 01-2-2v-2"></path>
                                    </svg>
                                    Sign In to Apply
                                </x-button>
                            @endauth

                            <!-- Quick Summary -->
                            <div class="space-y-4 border-t border-border pt-4">
                                <div>
                                    <p class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Location</p>
                                    <p class="text-body-sm text-text-primary font-medium">{{ $job->location_city }}</p>
                                </div>
                                <div>
                                    <p class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Salary</p>
                                    <p class="text-body-sm text-text-primary font-medium">{{ $salaryDisplay }}</p>
                                </div>
                                @if($job->accommodation_provided && $job->accommodation_details)
                                    <div>
                                        <p class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Accommodation</p>
                                        <p class="text-body-sm text-text-primary">{{ $job->accommodation_details }}</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Report Link -->
                            <div class="border-t border-border mt-4 pt-4">
                                <a href="#" class="text-body-xs text-text-tertiary hover:text-danger transition-colors duration-normal flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 6v2M4.08 4.08a10 10 0 1114.14 14.14"></path>
                                    </svg>
                                    Report Job
                                </a>
                            </div>
                        </x-card>

                        <!-- Trust Info -->
                        <div class="mt-6 p-4 bg-success-light border border-success-border rounded-lg">
                            <p class="text-body-xs text-success-text leading-relaxed">
                                <strong>Safe & Secure:</strong> CroWork verifies all employers and protects your personal information. Never pay fees to apply.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Section -->
    <div class="flex-1 py-12 bg-background">
        <div class="container-base">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Job Description -->
                    <section class="scroll-mt-24" id="description">
                        <x-section-header 
                            title="About This Job"
                            subtitle="Detailed role description and responsibilities"
                            class="mb-6"
                        />
                        <x-card class="prose prose-sm max-w-none">
                            <div class="text-body text-text-primary leading-relaxed whitespace-pre-wrap">
                                {{ $job->description }}
                            </div>
                        </x-card>
                    </section>

                    <!-- Key Details -->
                    <section class="scroll-mt-24" id="details">
                        <x-section-header 
                            title="Key Details"
                            subtitle="Important information about the position"
                            class="mb-6"
                        />
                        <x-card>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Location -->
                                <div class="border-b md:border-b-0 pb-4 md:pb-0">
                                    <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-2">Location</dt>
                                    <dd class="text-title-2 text-text-primary font-medium">{{ $job->location_city }}</dd>
                                </div>

                                <!-- Category -->
                                <div class="border-b md:border-b-0 pb-4 md:pb-0">
                                    <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-2">Category</dt>
                                    <dd class="text-title-2 text-text-primary font-medium">{{ $job->category }}</dd>
                                </div>

                                <!-- Salary -->
                                <div class="border-b md:border-b-0 pb-4 md:pb-0">
                                    <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-2">Salary</dt>
                                    <dd class="text-title-2 text-primary font-semibold">{{ $salaryDisplay }}</dd>
                                </div>

                                <!-- Languages -->
                                @if(!empty($languages))
                                    <div class="border-b md:border-b-0 pb-4 md:pb-0">
                                        <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-2">Languages Required</dt>
                                        <dd class="text-title-2 text-text-primary font-medium">{{ implode(', ', $languages) }}</dd>
                                    </div>
                                @endif

                                <!-- Contract Type -->
                                @if($job->contract_type)
                                    <div class="border-b md:border-b-0 pb-4 md:pb-0">
                                        <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-2">Employment Type</dt>
                                        <dd class="text-title-2 text-text-primary font-medium">{{ ucfirst($job->contract_type) }}

                                </dd>
                                    </div>
                                @endif

                                <!-- Start Date -->
                                @if($job->start_date)
                                    <div class="border-b md:border-b-0 pb-4 md:pb-0">
                                        <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-2">Start Date</dt>
                                        <dd class="text-title-2 text-text-primary font-medium">{{ $job->start_date->format('M d, Y') }}</dd>
                                    </div>
                                @endif

                                <!-- Expires At -->
                                @if($job->expires_at)
                                    <div class="pb-4 md:pb-0">
                                        <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-2">Application Deadline</dt>
                                        <dd class="text-title-2 font-medium">
                                            @if($job->expires_at->isFuture())
                                                <span class="text-text-primary">{{ $job->expires_at->format('M d, Y') }}</span>
                                                <span class="text-body-xs text-text-tertiary">({{ $job->expires_at->diffForHumans() }})</span>
                                            @else
                                                <span class="text-danger">Applications closed</span>
                                            @endif
                                        </dd>
                                    </div>
                                @endif

                                <!-- Accommodation Details -->
                                @if($job->accommodation_provided && $job->accommodation_details)
                                    <div class="md:col-span-2">
                                        <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-2">Accommodation</dt>
                                        <dd class="text-title-2 text-text-primary font-medium">{{ $job->accommodation_details }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </x-card>
                    </section>
                </div>

                <!-- Secondary Sidebar (Desktop) -->
                <div class="hidden lg:block">
                    <!-- Company Info Card -->
                    @if($job->employer)
                        <x-card class="mb-6 sticky top-24">
                            <h3 class="text-title-1 font-semibold text-text-primary mb-4">About the Employer</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-body-sm text-text-secondary mb-1">Company</p>
                                    <p class="text-title-2 font-medium text-text-primary">{{ $job->employer->company_name }}</p>
                                </div>
                                @if($job->employer->description)
                                    <div>
                                        <p class="text-body-sm text-text-secondary mb-1">About</p>
                                        <p class="text-body-sm text-text-primary">{{ $job->employer->description }}</p>
                                    </div>
                                @endif
                                <div class="pt-3 border-t border-border">
                                    <a href="#" class="text-body-sm text-primary hover:text-primary-hover font-medium transition-colors duration-normal flex items-center gap-2">
                                        Visit Company
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        </x-card>
                    @endif

                    <!-- Related Jobs Card -->
                    <x-card>
                        <h3 class="text-title-1 font-semibold text-text-primary mb-4">Similar Jobs</h3>
                        <p class="text-body-sm text-text-secondary">
                            Browse similar job opportunities in <strong>{{ $job->location_city }}</strong> and <strong>{{ $job->category }}</strong>.
                        </p>
                        <div class="mt-4 pt-4 border-t border-border">
                            <a href="{{ route('jobs.index', ['city' => $job->location_city, 'category' => $job->category]) }}" class="text-body-sm text-primary hover:text-primary-hover font-medium transition-colors duration-normal flex items-center gap-2">
                                View Similar Jobs
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </x-card>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Sticky Apply Bar -->
    <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-background border-t border-border shadow-lg z-40 animate-in slide-in-from-bottom duration-300">
        <div class="container-base py-3">
            <div class="flex gap-3 items-center">
                <!-- Quick Info -->
                <div class="flex-1 text-body-xs">
                    <p class="text-text-tertiary">{{ $job->location_city }} • {{ $salaryDisplay }}</p>
                </div>
                
                <!-- CTA -->
                @auth
                    @if(auth()->user()->isWorker())
                        <x-button 
                            href="{{ route('jobs.apply', $job) }}"
                            variant="primary" 
                            size="sm"
                            class="flex-shrink-0"
                        >
                            Apply
                        </x-button>
                    @else
                        <x-button 
                            variant="secondary" 
                            size="sm"
                            disabled
                            class="flex-shrink-0"
                        >
                            Apply
                        </x-button>
                    @endif
                @else
                    <x-button 
                        href="{{ route('login') }}?redirect={{ route('jobs.show', $job) }}"
                        variant="primary" 
                        size="sm"
                        class="flex-shrink-0"
                    >
                        Sign In
                    </x-button>
                @endauth
            </div>
        </div>
    </div>

    <!-- Mobile Bottom Padding (to prevent content from hiding under sticky bar) -->
    <div class="lg:hidden h-20"></div>
</main>

@endsection

@push('styles')
    <meta name="canonical" href="{{ route('jobs.show', $job) }}">
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($job->description), 155) }}">
    <title>{{ $job->title }} in {{ $job->location_city }} – CroWork</title>
@endpush
