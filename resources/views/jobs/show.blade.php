@extends('layouts.app')

@section('content')

@php
    $currencySymbol = $job->salary_currency === 'EUR' ? '€' : $job->salary_currency;
    $periodText = $job->salary_period === 'hour' ? 'hour' : 'month';

    if ($job->salary_min && $job->salary_max) {
        $salaryDisplay = $currencySymbol . number_format($job->salary_min, 0, '.', ',') . ' - ' . $currencySymbol . number_format($job->salary_max, 0, '.', ',') . ' / ' . $periodText;
    } elseif ($job->salary_min) {
        $salaryDisplay = 'From ' . $currencySymbol . number_format($job->salary_min, 0, '.', ',') . ' / ' . $periodText;
    } elseif ($job->salary_max) {
        $salaryDisplay = 'Up to ' . $currencySymbol . number_format($job->salary_max, 0, '.', ',') . ' / ' . $periodText;
    } else {
        $salaryDisplay = 'Not specified';
    }

    $postedDisplay = $job->published_at ? $job->published_at->diffForHumans() : 'Recently posted';
    $languages = is_array($job->languages) ? $job->languages : [];

    $employmentType = match (strtolower((string) $job->contract_type)) {
        'full-time', 'full time', 'full_time' => 'FULL_TIME',
        'part-time', 'part time', 'part_time' => 'PART_TIME',
        'temporary', 'seasonal', 'fixed-term', 'fixed term' => 'TEMPORARY',
        'contract', 'contractor' => 'CONTRACTOR',
        'internship' => 'INTERN',
        default => null,
    };

    $jobPostingSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'JobPosting',
        'title' => $job->title,
        'description' => trim(strip_tags($job->description)),
        'datePosted' => optional($job->published_at ?? $job->created_at)->toAtomString(),
        'validThrough' => optional($job->expires_at)->toAtomString(),
        'employmentType' => $employmentType,
        'hiringOrganization' => [
            '@type' => 'Organization',
            'name' => $job->employer?->company_name ?? config('app.name', 'CroWork'),
            'sameAs' => url('/'),
        ],
        'jobLocation' => [
            '@type' => 'Place',
            'address' => [
                '@type' => 'PostalAddress',
                'addressLocality' => $job->location_city,
                'addressCountry' => 'HR',
            ],
        ],
        'identifier' => [
            '@type' => 'PropertyValue',
            'name' => config('app.name', 'CroWork'),
            'value' => (string) $job->id,
        ],
        'url' => route('jobs.show', $job),
    ];

    if ($job->salary_min || $job->salary_max) {
        $jobPostingSchema['baseSalary'] = [
            '@type' => 'MonetaryAmount',
            'currency' => $job->salary_currency ?? 'EUR',
            'value' => [
                '@type' => 'QuantitativeValue',
                'minValue' => $job->salary_min,
                'maxValue' => $job->salary_max,
                'unitText' => $job->salary_period === 'hour' ? 'HOUR' : 'MONTH',
            ],
        ];
    }

    $jobPostingSchema = array_filter($jobPostingSchema, fn ($value) => filled($value));
@endphp

@section('canonical', route('jobs.show', $job))

@push('head')
    <script type="application/ld+json">{!! json_encode($jobPostingSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) !!}</script>
@endpush

<x-hero
    size="sm"
    :title="$job->title"
    :subtitle="($job->employer->company_name ?? 'Employer') . ' • ' . $job->location_city"
    theme="jobs"
/>

<main class="section-spacing-tight min-h-screen pb-24 lg:pb-0">
    <div class="container-base max-w-7xl mx-auto space-y-6">
        <x-surface variant="base" elevation="1" rounded="card" padding="6" class="premium-glass">
            <nav class="flex flex-wrap items-center gap-2 text-body-sm text-text-secondary mb-4 pb-4 border-b border-border">
                <a href="{{ route('home') }}" class="hover:text-primary transition-colors duration-normal">Home</a>
                <span class="text-text-tertiary">/</span>
                <a href="{{ route('jobs.index') }}" class="hover:text-primary transition-colors duration-normal">Jobs</a>
                <span class="text-text-tertiary">/</span>
                <span class="text-text-primary font-medium">{{ $job->title }}</span>
            </nav>

            <div class="flex flex-col gap-5 lg:flex-row lg:items-start lg:justify-between">
                <div class="space-y-3">
                    <h1 class="text-display-sm md:text-display-md font-semibold text-text-primary text-balance">{{ $job->title }}</h1>

                    <div class="flex flex-wrap items-center gap-x-3 gap-y-2 text-body text-text-secondary">
                        @if($job->employer)
                            <span class="font-medium text-text-primary">{{ $job->employer->company_name }}</span>
                            <span class="text-text-tertiary">•</span>
                        @endif
                        <span>{{ $job->location_city }}</span>
                        <span class="text-text-tertiary">•</span>
                        <span>Posted {{ $postedDisplay }}</span>
                    </div>

                    <div class="flex flex-wrap gap-2">
                        @if($job->accommodation_provided)
                            <x-chip tone="success" size="md">Accommodation Provided</x-chip>
                        @endif

                        @if(!empty($languages))
                            <x-chip tone="info" size="md">
                                {{ implode(', ', array_slice($languages, 0, 3)) }}{{ count($languages) > 3 ? ' +' . (count($languages) - 3) : '' }}
                            </x-chip>
                        @endif

                        @if($job->contract_type)
                            <x-chip tone="neutral" size="md">{{ ucfirst($job->contract_type) }}</x-chip>
                        @endif
                    </div>
                </div>

                <x-surface variant="tinted" elevation="0" rounded="card" padding="4" class="min-w-[17rem] max-w-full border border-primary/20 bg-white/70">
                    <p class="text-body-xs uppercase tracking-wide text-text-tertiary mb-1">Salary</p>
                    <p class="text-title-1 font-semibold text-primary leading-tight">{{ $salaryDisplay }}</p>
                </x-surface>
            </div>
        </x-surface>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-7 items-start">
            <div class="lg:col-span-8 space-y-6">
                <section id="description" class="scroll-mt-24">
                    <x-section-header
                        title="About This Job"
                        subtitle="Detailed role description and responsibilities"
                        class="mb-4"
                    />
                    <x-card class="premium-glass">
                        <div class="text-body text-text-primary leading-relaxed whitespace-pre-wrap">
                            {{ $job->description }}
                        </div>
                    </x-card>
                </section>

                <section id="details" class="scroll-mt-24">
                    <x-section-header
                        title="Key Details"
                        subtitle="Important information about the position"
                        class="mb-4"
                    />
                    <x-card class="premium-glass">
                        <dl class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div>
                                <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Location</dt>
                                <dd class="text-title-2 text-text-primary font-medium">{{ $job->location_city }}</dd>
                            </div>

                            <div>
                                <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Category</dt>
                                <dd class="text-title-2 text-text-primary font-medium">{{ $job->category }}</dd>
                            </div>

                            <div>
                                <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Salary</dt>
                                <dd class="text-title-2 text-primary font-semibold">{{ $salaryDisplay }}</dd>
                            </div>

                            @if(!empty($languages))
                                <div>
                                    <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Languages Required</dt>
                                    <dd class="text-title-2 text-text-primary font-medium">{{ implode(', ', $languages) }}</dd>
                                </div>
                            @endif

                            @if($job->contract_type)
                                <div>
                                    <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Employment Type</dt>
                                    <dd class="text-title-2 text-text-primary font-medium">{{ ucfirst($job->contract_type) }}</dd>
                                </div>
                            @endif

                            @if($job->start_date)
                                <div>
                                    <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Start Date</dt>
                                    <dd class="text-title-2 text-text-primary font-medium">{{ $job->start_date->format('M d, Y') }}</dd>
                                </div>
                            @endif

                            @if($job->expires_at)
                                <div>
                                    <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Application Deadline</dt>
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

                            @if($job->accommodation_provided && $job->accommodation_details)
                                <div class="md:col-span-2">
                                    <dt class="text-body-xs text-text-tertiary uppercase tracking-wide font-semibold mb-1">Accommodation</dt>
                                    <dd class="text-title-2 text-text-primary font-medium">{{ $job->accommodation_details }}</dd>
                                </div>
                            @endif
                        </dl>
                    </x-card>
                </section>
            </div>

            <aside class="lg:col-span-4 space-y-5">
                <x-surface variant="base" elevation="2" rounded="card" padding="6" class="border-stroke-subtle premium-glass">
                    @auth
                        @if(auth()->user()->isWorker())
                            <x-button
                                href="{{ route('jobs.apply', $job) }}"
                                variant="primary"
                                class="w-full mb-5 py-3 text-base font-semibold"
                            >
                                Apply Now
                            </x-button>
                        @else
                            <div class="mb-5 p-3 bg-warning-light border border-warning-border rounded-lg">
                                <p class="text-body-sm text-warning-text">
                                    <strong>Note:</strong> You are logged in as an employer or admin. Switch to a worker account to apply.
                                </p>
                            </div>
                        @endif
                    @else
                        <x-button
                            href="{{ route('login') }}?redirect={{ route('jobs.show', $job) }}"
                            variant="primary"
                            class="w-full mb-5 py-3 text-base font-semibold"
                        >
                            Sign In to Apply
                        </x-button>
                    @endauth

                    <div class="space-y-3 border-t border-border pt-4">
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

                    <div class="border-t border-border mt-4 pt-4">
                        <a href="{{ route('reports.create', ['type' => 'job', 'id' => $job->id]) }}" class="text-body-xs text-text-tertiary hover:text-danger transition-colors duration-normal flex items-center gap-1">
                            Report Job
                        </a>
                    </div>
                </x-surface>

                <div class="p-4 bg-success-light/80 border border-success-border rounded-2xl">
                    <p class="text-body-xs text-success-text leading-relaxed">
                        <strong>Safe & Secure:</strong> CroWork verifies employers and protects your personal information. Never pay fees to apply.
                    </p>
                </div>

                @if($job->employer)
                    <x-card class="premium-glass">
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
                                <a
                                    href="{{ \Illuminate\Support\Facades\Route::has('employers.show') ? route('employers.show', $job->employer) : route('coming-soon', ['feature' => 'company-profile']) }}"
                                    class="text-body-sm text-primary hover:text-primary-hover font-medium transition-colors duration-normal"
                                >
                                    Visit Company
                                </a>
                            </div>
                        </div>
                    </x-card>
                @endif

                <x-card class="premium-glass">
                    <h3 class="text-title-1 font-semibold text-text-primary mb-3">Similar Jobs</h3>
                    <p class="text-body-sm text-text-secondary mb-4">
                        Browse similar opportunities in <strong>{{ $job->location_city }}</strong> and <strong>{{ $job->category }}</strong>.
                    </p>
                    <a href="{{ route('jobs.index', ['city' => $job->location_city, 'category' => $job->category]) }}" class="text-body-sm text-primary hover:text-primary-hover font-medium transition-colors duration-normal">
                        View Similar Jobs
                    </a>
                </x-card>
            </aside>
        </div>
    </div>

    <div class="lg:hidden fixed bottom-0 left-0 right-0 bg-background border-t border-border shadow-lg z-40">
        <div class="container-base py-3">
            <div class="flex items-center gap-3">
                <div class="flex-1 min-w-0">
                    <p class="text-body-xs text-text-tertiary truncate">{{ $job->location_city }} - {{ $salaryDisplay }}</p>
                </div>
                @auth
                    @if(auth()->user()->isWorker())
                        <x-button href="{{ route('jobs.apply', $job) }}" variant="primary" size="sm" class="flex-shrink-0">Apply</x-button>
                    @else
                        <x-button variant="secondary" size="sm" disabled class="flex-shrink-0">Apply</x-button>
                    @endif
                @else
                    <x-button href="{{ route('login') }}?redirect={{ route('jobs.show', $job) }}" variant="primary" size="sm" class="flex-shrink-0">Sign In</x-button>
                @endauth
            </div>
        </div>
    </div>

    <div class="lg:hidden h-20"></div>
</main>

@endsection

@push('styles')
    <meta name="canonical" href="{{ route('jobs.show', $job) }}">
    <meta name="description" content="{{ \Illuminate\Support\Str::limit(strip_tags($job->description), 155) }}">
    <title>{{ $job->title }} in {{ $job->location_city }} - CroWork</title>
@endpush
