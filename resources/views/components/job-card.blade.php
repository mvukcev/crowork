@props([
    'title',
    'company' => null,
    'city' => null,
    'salary_min' => null,
    'salary_max' => null,
    'salary_currency' => 'EUR',
    'salary_period' => 'month',
    'employment_type' => null,
    'accommodation_provided' => false,
    'visa_support' => false,
    'is_featured' => false,
    'is_urgent' => false,
    'languages' => [],
    'posted_at' => null,
    'href' => '#',
])

@php
    $salaryDisplay = null;
    if (!is_null($salary_min) || !is_null($salary_max)) {
        $currency = strtoupper((string) $salary_currency);
        $period = strtolower((string) $salary_period) === 'hour' ? 'hour' : 'month';

        if (!is_null($salary_min) && !is_null($salary_max)) {
            $salaryDisplay = $currency . ' ' . number_format($salary_min) . ' - ' . number_format($salary_max) . ' / ' . $period;
        } elseif (!is_null($salary_min)) {
            $salaryDisplay = 'From ' . $currency . ' ' . number_format($salary_min) . ' / ' . $period;
        } else {
            $salaryDisplay = 'Up to ' . $currency . ' ' . number_format($salary_max) . ' / ' . $period;
        }
    }

    $languageValues = [];
    if (is_array($languages)) {
        $languageValues = $languages;
    } elseif (is_string($languages) && trim($languages) !== '') {
        $decoded = json_decode($languages, true);
        $languageValues = is_array($decoded) ? $decoded : preg_split('/[\n,]+/', $languages);
    }

    $languageValues = array_values(array_filter(array_map(fn ($lang) => trim((string) $lang), $languageValues)));
    $languageText = count($languageValues) ? implode(', ', array_slice($languageValues, 0, 3)) : null;

    $employmentTypeText = $employment_type ? \Illuminate\Support\Str::headline(str_replace(['-', '_'], ' ', $employment_type)) : null;
@endphp

<a href="{{ $href }}" class="cw-surface p-5 block cw-hover-lift">
    <div class="flex items-start justify-between gap-3 mb-2">
        <div>
            <p class="text-xs text-slate-500 mb-1">{{ $company ?: 'Employer' }}{{ $city ? ' · ' . $city : '' }}</p>
            <h3 class="text-lg font-semibold text-slate-900 leading-tight">{{ $title }}</h3>
        </div>
        <div class="flex flex-col gap-1 items-end">
            @if($is_urgent)
                <span class="cw-chip text-red-800 bg-red-50 border-red-200">Urgent</span>
            @endif
            @if($is_featured)
                <span class="cw-chip text-indigo-800 bg-indigo-50 border-indigo-200">Featured</span>
            @endif
        </div>
    </div>

    @if($salaryDisplay)
        <p class="text-sm text-slate-700 mb-2">{{ $salaryDisplay }}</p>
    @endif

    <div class="flex flex-wrap items-center gap-2 mt-3">
        @if($employmentTypeText)
            <span class="cw-chip">{{ $employmentTypeText }}</span>
        @endif
        @if($languageText)
            <span class="cw-chip">{{ $languageText }}</span>
        @endif
        @if($accommodation_provided)
            <span class="cw-chip text-amber-800 bg-amber-50 border-amber-200">Accommodation</span>
        @endif
        @if($visa_support)
            <span class="cw-chip text-emerald-800 bg-emerald-50 border-emerald-200">Visa support</span>
        @endif
        @if($posted_at)
            <span class="cw-chip">Posted {{ \Carbon\Carbon::parse($posted_at)->diffForHumans() }}</span>
        @endif
    </div>
</a>
