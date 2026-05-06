@extends('layouts.app')

@section('content')

@php
    $priceDisplay = 'Free / Not specified';
    if ($education->price_cents !== null && $education->price_cents > 0) {
        $priceEuros = $education->price_cents / 100;
        $currencySymbol = $education->currency === 'EUR' ? '€' : $education->currency;
        $priceDisplay = $currencySymbol . number_format($priceEuros, 0, '.', ',');
    }

    $locationDisplay = $education->is_online ? 'Online' : ($education->city ?? 'Location TBD');
    $startDateDisplay = $education->start_date ? $education->start_date->format('F j, Y') : 'Date TBD';
    $postedDisplay = $education->published_at ? $education->published_at->diffForHumans() : 'Recently posted';

    $provider = null;
    if ($education->createdByUser && $education->createdByUser->employer) {
        $provider = $education->createdByUser->employer->company_name;
    }
@endphp

@section('canonical', route('educations.show', $education->slug))

<x-hero
    size="sm"
    :title="$education->title"
    :subtitle="($provider ?? 'Education Provider') . ' • ' . $locationDisplay"
    theme="education"
/>

<main class="section-spacing-tight min-h-screen pb-24 lg:pb-0">
    <div class="container-base max-w-7xl mx-auto space-y-6">
        <x-surface variant="base" elevation="1" rounded="card" padding="6" class="premium-glass">
            <nav class="flex flex-wrap items-center gap-2 text-body-sm text-text-secondary mb-4 pb-4 border-b border-border">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors duration-normal">Home</a>
                <span class="text-text-tertiary">/</span>
                <a href="{{ route('educations.index') }}" class="hover:text-primary transition-colors duration-normal">Education</a>
                <span class="text-text-tertiary">/</span>
                <span class="text-text-primary font-medium">{{ $education->title }}</span>
            </nav>

            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="space-y-3">
                    <h1 class="text-display-sm md:text-display-md font-semibold text-text-primary text-balance">{{ $education->title }}</h1>

                    <div class="flex flex-wrap items-center gap-x-3 gap-y-2 text-body text-text-secondary">
                        @if($provider)
                            <span class="font-medium text-text-primary">{{ $provider }}</span>
                            <span class="text-text-tertiary">•</span>
                        @endif
                        <span>{{ $locationDisplay }}</span>
                        <span class="text-text-tertiary">•</span>
                        <span>Posted {{ $postedDisplay }}</span>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        <span class="premium-chip">{{ $education->is_online ? 'Online Program' : 'In-Person Program' }}</span>
                        @if($education->start_date)
                            <span class="premium-chip">Starts {{ $education->start_date->format('M j, Y') }}</span>
                        @endif
                        @if($education->capacity)
                            <span class="premium-chip">{{ $education->capacity }} seats</span>
                        @endif
                    </div>
                </div>

                <x-surface variant="tinted" elevation="0" rounded="card" padding="4" class="min-w-[16rem] max-w-full border border-primary/20 bg-white/70">
                    <p class="text-body-xs uppercase tracking-wide text-text-tertiary mb-1">Program Price</p>
                    <p class="text-title-1 font-semibold text-primary leading-tight">{{ $priceDisplay }}</p>
                </x-surface>
            </div>
        </x-surface>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-7 items-start">
            <div class="lg:col-span-8 space-y-6">
                <section id="description" class="scroll-mt-24">
                    <x-section-header
                        title="About This Program"
                        subtitle="Detailed program description and outcomes"
                        class="mb-4"
                    />
                    <x-card class="premium-glass">
                        <div class="text-body text-text-primary leading-relaxed whitespace-pre-wrap">
                            {{ $education->description ?? 'No description provided.' }}
                        </div>
                    </x-card>
                </section>

                <section id="details" class="scroll-mt-24">
                    <x-section-header
                        title="Program Details"
                        subtitle="Important information before you apply"
                        class="mb-4"
                    />
                    <x-card class="premium-glass">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Format</dt>
                                <dd class="text-title-2 text-text-primary font-medium">{{ $education->is_online ? 'Online' : 'In-Person' }}</dd>
                            </div>

                            <div>
                                <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Location</dt>
                                <dd class="text-title-2 text-text-primary font-medium">{{ $locationDisplay }}</dd>
                            </div>

                            <div>
                                <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Price</dt>
                                <dd class="text-title-2 text-primary font-semibold">{{ $priceDisplay }}</dd>
                            </div>

                            @if($education->start_date)
                                <div>
                                    <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Start Date</dt>
                                    <dd class="text-title-2 text-text-primary font-medium">{{ $startDateDisplay }}</dd>
                                </div>
                            @endif

                            @if($education->capacity)
                                <div>
                                    <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Capacity</dt>
                                    <dd class="text-title-2 text-text-primary font-medium">{{ $education->capacity }} participants</dd>
                                </div>
                            @endif

                            @if($education->expires_at)
                                <div>
                                    <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Application Deadline</dt>
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

            <aside class="lg:col-span-4 space-y-5">
                <x-surface variant="base" elevation="2" rounded="card" padding="6" class="border-stroke-subtle premium-glass">
                    <x-button
                        href="{{ route('educations.apply', $education) }}"
                        variant="primary"
                        class="w-full mb-5 py-3 text-base font-semibold"
                    >
                        Apply for This Program
                    </x-button>

                    <div class="space-y-3 border-t border-border pt-4">
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
                    </div>
                </x-surface>

                <div class="p-4 bg-success-light/80 border border-success-border rounded-2xl">
                    <p class="text-body-xs text-success-text leading-relaxed">
                        <strong>Verified Programs:</strong> CroWork verifies listed education providers and program details.
                    </p>
                </div>

                @if($provider)
                    <x-card class="premium-glass">
                        <h3 class="text-title-1 font-semibold text-text-primary mb-4">About the Provider</h3>
                        <div class="space-y-3">
                            <div>
                                <p class="text-body-sm text-text-secondary mb-1">Provider</p>
                                <p class="text-title-2 font-medium text-text-primary">{{ $provider }}</p>
                            </div>
                            @if($education->createdByUser && $education->createdByUser->employer && $education->createdByUser->employer->description)
                                <div>
                                    <p class="text-body-sm text-text-secondary mb-1">About</p>
                                    <p class="text-body-sm text-text-primary">{{ $education->createdByUser->employer->description }}</p>
                                </div>
                            @endif
                        </div>
                    </x-card>
                @endif

                <x-card class="premium-glass">
                    <h3 class="text-title-1 font-semibold text-text-primary mb-3">Similar Programs</h3>
                    <p class="text-body-sm text-text-secondary mb-4">Find similar opportunities {{ $education->city ? 'in ' . $education->city : '' }}.</p>
                    <a href="{{ route('educations.index', !$education->is_online && $education->city ? ['city' => $education->city] : []) }}" class="text-body-sm text-primary hover:text-primary-hover font-medium transition-colors duration-normal">
                        View Similar Programs
                    </a>
                </x-card>
            </aside>
        </div>
    </div>

    <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-background border-t border-border shadow-lg z-40">
        <div class="container-base py-3">
            <div class="flex items-center gap-3">
                <div class="flex-1 min-w-0">
                    <p class="text-body-xs text-text-tertiary truncate">{{ $locationDisplay }} - {{ $priceDisplay }}</p>
                </div>
                <x-button href="{{ route('educations.apply', $education) }}" variant="primary" size="sm" class="flex-shrink-0">Apply</x-button>
            </div>
        </div>
    </div>

    <div class="lg:hidden h-20"></div>
</main>

@endsection

@push('styles')
    <meta name="canonical" href="{{ route('educations.show', $education->slug) }}">
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($education->description ?? 'Discover education opportunities in Croatia'), 155) }}">
    <title>{{ $education->title }} - CroWork Education</title>
@endpush
