@props([
    'title' => null,
    'company' => null,
    'city' => null,
    'salary_min' => null,
    'salary_max' => null,
    'salary_currency' => 'EUR',
    'salary_period' => 'month',
    'accommodation_provided' => false,
    'languages' => null,
    'posted_at' => null,
    'href' => '#',
])

@php
    // Format salary display
    $salaryDisplay = null;
    $currencySymbol = $salary_currency === 'EUR' ? '€' : $salary_currency;
    $periodText = $salary_period === 'hour' ? 'hour' : 'month';
    
    if ($salary_min && $salary_max) {
        $salaryDisplay = $currencySymbol . number_format($salary_min, 0, '.', ',') . ' – ' . $currencySymbol . number_format($salary_max, 0, '.', ',') . ' / ' . $periodText;
    } elseif ($salary_min) {
        $salaryDisplay = 'From ' . $currencySymbol . number_format($salary_min, 0, '.', ',') . ' / ' . $periodText;
    } elseif ($salary_max) {
        $salaryDisplay = 'Up to ' . $currencySymbol . number_format($salary_max, 0, '.', ',') . ' / ' . $periodText;
    } else {
        $salaryDisplay = 'Salary: Not specified';
    }
    
    // Format posted time
    $postedDisplay = null;
    if ($posted_at) {
        if ($posted_at instanceof \Carbon\Carbon) {
            $postedDisplay = $posted_at->diffForHumans();
        } elseif (is_string($posted_at)) {
            $postedDisplay = \Carbon\Carbon::parse($posted_at)->diffForHumans();
        }
    }
    
    // Format languages display
    $languagesDisplay = null;
    $languagesArray = is_array($languages) ? $languages : [];
    if (!empty($languagesArray)) {
        $displayedLanguages = array_slice($languagesArray, 0, 2);
        $languagesDisplay = implode(', ', $displayedLanguages);
        if (count($languagesArray) > 2) {
            $languagesDisplay .= ' +' . (count($languagesArray) - 2);
        }
    }
    
    // Combine company and city
    $locationDisplay = collect([$company, $city])->filter()->implode(' • ');
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->merge(['class' => 'premium-job-card group']) }}
>
    <article class="space-y-3.5">
        <div class="flex items-start justify-between gap-3">
            <p class="text-title-2 md:text-title-1 font-semibold text-primary leading-tight mb-0">{{ $salaryDisplay }}</p>
            @if($postedDisplay)
                <time class="text-caption text-text-tertiary whitespace-nowrap">{{ $postedDisplay }}</time>
            @endif
        </div>

        <h3 class="text-title-2 font-semibold text-text-primary group-hover:text-primary transition-colors duration-normal mb-0">
            {{ $title }}
        </h3>

        @if($company)
            <p class="text-body-sm font-medium text-text-primary mb-0">{{ $company }}</p>
        @endif

        @if($city)
            <div class="inline-flex items-center gap-1.5 text-body-sm text-text-secondary mb-0">
                <svg class="w-4 h-4 text-text-tertiary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                </svg>
                {{ $city }}
            </div>
        @endif

        <div class="flex flex-wrap items-center gap-2 pt-1">
            @if($accommodation_provided)
                <span class="premium-chip">Accommodation</span>
            @endif

            @if($languagesDisplay)
                <span class="premium-chip">{{ $languagesDisplay }}</span>
            @endif
        </div>

        <div class="pt-2 border-t border-border/40 flex items-center justify-between">
            <span class="text-caption text-text-tertiary">View details</span>
            <span class="text-body-sm font-semibold text-primary">Open</span>
        </div>
    </article>
</a>
