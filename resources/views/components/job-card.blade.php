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
    {{ $attributes->merge(['class' => 'block bg-white border-0 rounded-2xl p-6 shadow-elevation-1 hover:shadow-elevation-2 transition-all duration-normal group hover:-translate-y-1']) }}
>
    <article class="space-y-4">
        <!-- Title -->
        <h3 class="text-lg font-semibold text-text-primary group-hover:text-primary transition-colors duration-normal">
            {{ $title }}
        </h3>
        
        <!-- Company and Location -->
        @if($locationDisplay)
            <div class="flex items-center text-body-sm text-text-secondary">
                <svg class="w-4 h-4 mr-1.5 text-text-tertiary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                {{ $locationDisplay }}
            </div>
        @endif
        
        <!-- Salary -->
        <div class="flex items-center">
            <div class="inline-flex items-center px-3 py-1.5 rounded-xl bg-primary/5 border border-primary/10">
                <svg class="w-4 h-4 mr-1.5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-body-sm font-medium text-primary">{{ $salaryDisplay }}</span>
            </div>
        </div>
        
        <!-- Badges and Posted Time -->
        <div class="flex items-center justify-between gap-2">
            <div class="flex flex-wrap gap-2">
                @if($accommodation_provided)
                    <x-chip tone="success" size="sm">
                        Accommodation
                    </x-chip>
                @endif
                
                @if($languagesDisplay)
                    <x-chip tone="info" size="sm">
                        {{ $languagesDisplay }}
                    </x-chip>
                @endif
            </div>
            
            @if($postedDisplay)
                <time class="text-caption text-text-tertiary whitespace-nowrap">
                    {{ $postedDisplay }}
                </time>
            @endif
        </div>
    </article>
</a>
