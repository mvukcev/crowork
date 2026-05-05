@extends('layouts.app')

@section('content')

@php
    // Format price for display
    $priceDisplay = 'Free / Not specified';
    if ($education->price_cents !== null && $education->price_cents > 0) {
        $priceEuros = $education->price_cents / 100;
        $currencySymbol = $education->currency === 'EUR' ? '€' : $education->currency;
        $priceDisplay = $currencySymbol . number_format($priceEuros, 0, '.', ',');
    }
    
    // Format location display
    $locationDisplay = $education->is_online ? 'Online' : ($education->city ?? 'Location TBD');
    
    // Format start date
    $startDateDisplay = $education->start_date ? $education->start_date->format('F j, Y') : 'Date TBD';
    
    // Format posted time
    $postedDisplay = $education->published_at ? $education->published_at->diffForHumans() : 'Recently posted';
    
    // Get provider name
    $provider = null;
    if ($education->createdByUser && $education->createdByUser->employer) {
        $provider = $education->createdByUser->employer->company_name;
    }
@endphp

@push('styles')
    <meta name="canonical" href="{{ route('educations.show', $education->slug) }}">
@endpush

<!-- Hero Section -->
<x-hero 
    size="sm" 
    :title="$education->title" 
    :subtitle="($provider ?? 'Education Provider') . ' • ' . $locationDisplay">
</x-hero>

<main class="min-h-screen flex flex-col section-spacing">
    <div class="container-base">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Main Content Area -->
            <div class="lg:col-span-2">
                <!-- Hero Content Card -->
                <x-card class="border-0 shadow-sm mb-6">
                    <!-- Breadcrumb -->
                    <nav class="flex items-center text-body-sm text-text-secondary mb-6 pb-6 border-b border-border">
                        <a href="{{ route('home') }}" class="hover:text-primary transition-colors duration-normal">Home</a>
                        <svg class="w-4 h-4 mx-2 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                        <a href="{{ route('educations.index') }}" class="hover:text-primary transition-colors duration-normal">Education</a>
                        <svg class="w-4 h-4 mx-2 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <span class="text-text-primary font-medium">{{ $education->title }}</span>
                        </nav>

                        <!-- Education Title -->
                        <h1 class="text-display-md font-semibold text-text-primary mb-4">
                            {{ $education->title }}
                        </h1>

                        <!-- Provider and Location -->
                        <div class="flex flex-col md:flex-row md:items-center gap-4 mb-6">
                            <div class="flex items-center text-title-2 text-text-secondary">
                                @if($provider)
                                    <span class="font-medium text-text-primary">{{ $provider }}</span>
                                    <span class="mx-2 text-text-tertiary">•</span>
                                @endif
                                @if($education->is_online)
                                    <svg class="w-5 h-5 mr-1.5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                    </svg>
                                @else
                                    <svg class="w-5 h-5 mr-1.5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                @endif
                                <span>{{ $locationDisplay }}</span>
                            </div>
                            <span class="hidden md:inline text-text-tertiary">•</span>
                            <span class="text-body-sm text-text-tertiary">Posted {{ $postedDisplay }}</span>
                        </div>

                        <!-- Price Highlight -->
                        <div class="bg-primary-light rounded-lg p-4 mb-6 border border-primary-border">
                            <div class="flex items-baseline">
                                <svg class="w-6 h-6 text-primary mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="text-title-1 font-semibold text-primary">{{ $priceDisplay }}</span>
                            </div>
                        </div>

                        <!-- Badges Row -->
                        <div class="flex flex-wrap gap-2 mb-4">
                            @if($education->is_online)
                                <x-badge variant="accent" size="md" class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                                    </svg>
                                    Online Course
                                </x-badge>
                            @else
                                <x-badge variant="primary" size="md" class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    In-Person
                                </x-badge>
                            @endif

                            @if($education->start_date)
                                <x-badge variant="secondary" size="md" class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    Starts {{ $education->start_date->format('M j, Y') }}
                                </x-badge>
                            @endif

                            @if($education->capacity)
                                <x-badge variant="secondary" size="md" class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                    {{ $education->capacity }} spots
                                </x-badge>
                            @endif
                        </div>
                    </x-card>
                </div>

                <!-- Sidebar Apply Panel -->
                <div class="lg:col-span-1">
                    <!-- Desktop Apply Card -->
                    <div class="hidden lg:block sticky top-24">
                        <x-card class="border border-border shadow-md">
                            <!-- CTA Button -->
                            <x-button 
                                href="{{ route('educations.apply', $education) }}"
                                variant="primary" 
                                class="w-full mb-6 py-3 text-base font-semibold"
                            >
                                <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                                </svg>
                                Apply for this Education
                            </x-button>

                            <!-- Quick Summary -->
                            <div class="space-y-4 border-t border-border pt-4">
                                <div>
                                    <p class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Format</p>
                                    <p class="text-body-sm text-text-primary font-medium">{{ $locationDisplay }}</p>
                                </div>
                                <div>
                                    <p class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Price</p>
                                    <p class="text-body-sm text-text-primary font-medium">{{ $priceDisplay }}</p>
                                </div>
                                @if($education->start_date)
                                    <div>
                                        <p class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Start Date</p>
                                        <p class="text-body-sm text-text-primary font-medium">{{ $startDateDisplay }}</p>
                                    </div>
                                @endif
                                @if($education->capacity)
                                    <div>
                                        <p class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Capacity</p>
                                        <p class="text-body-sm text-text-primary">{{ $education->capacity }} participants</p>
                                    </div>
                                @endif
                            </div>

                            <!-- Report Link -->
                            <div class="border-t border-border mt-4 pt-4">
                                <a href="#" class="text-body-xs text-text-tertiary hover:text-danger transition-colors duration-normal flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    Report Issue
                                </a>
                            </div>
                        </x-card>

                        <!-- Trust Info -->
                        <div class="mt-6 p-4 bg-success-light border border-success-border rounded-lg">
                            <p class="text-body-xs text-success-text leading-relaxed">
                                <strong>Verified Programs:</strong> CroWork verifies all education providers. Check program details carefully before applying.
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
                    <!-- Description -->
                    <section class="scroll-mt-24" id="description">
                        <x-section-header 
                            title="About This Program"
                            subtitle="Detailed program description and curriculum"
                            class="mb-6"
                        />
                        <x-card class="prose prose-sm max-w-none">
                            <div class="text-body text-text-primary leading-relaxed whitespace-pre-wrap">
                                {{ $education->description ?? 'No description provided.' }}
                            </div>
                        </x-card>
                    </section>

                    <!-- Key Details -->
                    <section class="scroll-mt-24" id="details">
                        <x-section-header 
                            title="Program Details"
                            subtitle="Important information about this education program"
                            class="mb-6"
                        />
                        <x-card>
                            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Format -->
                                <div class="border-b md:border-b-0 pb-4 md:pb-0">
                                    <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-2">Format</dt>
                                    <dd class="text-title-2 text-text-primary font-medium">
                                        {{ $education->is_online ? 'Online' : 'In-Person' }}
                                    </dd>
                                </div>

                                <!-- Location -->
                                @if(!$education->is_online && $education->city)
                                    <div class="border-b md:border-b-0 pb-4 md:pb-0">
                                        <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-2">Location</dt>
                                        <dd class="text-title-2 text-text-primary font-medium">{{ $education->city }}</dd>
                                    </div>
                                @endif

                                <!-- Price -->
                                <div class="border-b md:border-b-0 pb-4 md:pb-0">
                                    <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-2">Price</dt>
                                    <dd class="text-title-2 text-primary font-semibold">{{ $priceDisplay }}</dd>
                                </div>

                                <!-- Start Date -->
                                @if($education->start_date)
                                    <div class="border-b md:border-b-0 pb-4 md:pb-0">
                                        <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-2">Start Date</dt>
                                        <dd class="text-title-2 text-text-primary font-medium">{{ $startDateDisplay }}</dd>
                                    </div>
                                @endif

                                <!-- Capacity -->
                                @if($education->capacity)
                                    <div class="border-b md:border-b-0 pb-4 md:pb-0">
                                        <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-2">Capacity</dt>
                                        <dd class="text-title-2 text-text-primary font-medium">{{ $education->capacity }} participants</dd>
                                    </div>
                                @endif

                                <!-- Expires At -->
                                @if($education->expires_at)
                                    <div class="pb-4 md:pb-0">
                                        <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-2">Application Deadline</dt>
                                        <dd class="text-title-2 font-medium">
                                            @if($education->expires_at->isFuture())
                                                <span class="text-text-primary">{{ $education->expires_at->format('M d, Y') }}</span>
                                                <span class="text-body-xs text-text-tertiary">({{ $education->expires_at->diffForHumans() }})</span>
                                            @else
                                                <span class="text-danger">Applications closed</span>
                                            @endif
                                        </dd>
                                    </div>
                                @endif
                            </dl>
                        </x-card>
                    </section>
                </div>

                <!-- Secondary Sidebar (Desktop) -->
                <div class="hidden lg:block">
                    <!-- Provider Info Card -->
                    @if($provider)
                        <x-card class="mb-6 sticky top-24">
                            <h3 class="text-title-1 font-semibold text-text-primary mb-4">About the Provider</h3>
                            <div class="space-y-3">
                                <div>
                                    <p class="text-body-sm text-text-secondary mb-1">Provider</p>
                                    <p class="text-title-2 font-medium text-text-primary">{{ $provider }}</p>
                                </div>
                                @if($education->createdByUser->employer->description)
                                    <div>
                                        <p class="text-body-sm text-text-secondary mb-1">About</p>
                                        <p class="text-body-sm text-text-primary">{{ $education->createdByUser->employer->description }}</p>
                                    </div>
                                @endif
                            </div>
                        </x-card>
                    @endif

                    <!-- Related Programs Card -->
                    <x-card>
                        <h3 class="text-title-1 font-semibold text-text-primary mb-4">Similar Programs</h3>
                        <p class="text-body-sm text-text-secondary">
                            Browse similar education opportunities
                            @if(!$education->is_online && $education->city)
                                in <strong>{{ $education->city }}</strong>
                            @endif
                            .
                        </p>
                        <div class="mt-4 pt-4 border-t border-border">
                            <a href="{{ route('educations.index', !$education->is_online && $education->city ? ['city' => $education->city] : []) }}" class="text-body-sm text-primary hover:text-primary-hover font-medium transition-colors duration-normal flex items-center gap-2">
                                View Similar Programs
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
                    <p class="text-text-tertiary">{{ $locationDisplay }} • {{ $priceDisplay }}</p>
                </div>
                
                <!-- CTA -->
                <x-button 
                    href="{{ route('educations.apply', $education) }}"
                    variant="primary" 
                    size="sm"
                    class="flex-shrink-0"
                >
                    Apply
                </x-button>
            </div>
        </div>
    </div>

    <!-- Mobile Bottom Padding (to prevent content from hiding under sticky bar) -->
    <div class="lg:hidden h-20"></div>
</main>

@endsection

@push('styles')
    <meta name="canonical" href="{{ route('educations.show', $education->slug) }}">
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($education->description ?? 'Discover education opportunities in Croatia'), 155) }}">
    <title>{{ $education->title }} – CroWork Education</title>
@endpush
