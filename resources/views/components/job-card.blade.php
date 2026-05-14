@props([
    'title',
    'company' => null,
    'company_href' => null,
    'city' => null,
    'salary_min' => null,
    'salary_max' => null,
    'salary_currency' => 'EUR',
    'salary_period' => 'month',
    'employment_type' => null,
    'experience_level' => null,
    'education_required' => null,
    'positions_available' => null,
    'working_hours' => null,
    'start_date' => null,
    'start_flexibility' => null,
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
    $experienceLevelText = $experience_level ? \Illuminate\Support\Str::headline(str_replace(['-', '_'], ' ', $experience_level)) : null;
    $educationText = $education_required ?: null;
    $positionsText = is_numeric($positions_available) ? ((int) $positions_available === 1 ? '1 position' : (int) $positions_available . ' positions') : null;
    $workingHoursText = $working_hours ?: null;
    $startDateText = $start_date ? 'Start ' . \Illuminate\Support\Carbon::parse($start_date)->format('M j') : null;
    $startFlexibilityText = $start_flexibility ?: null;
@endphp

<article class="cw-surface p-5 block cw-hover-lift">
    <div class="flex items-start justify-between gap-3 mb-2">
        <div>
            <p class="text-xs text-slate-500 mb-1">
                @if($company_href && $company)
                    <a href="{{ $company_href }}" class="hover:text-slate-900 underline-offset-2 hover:underline">{{ $company }}</a>
                @else
                    {{ $company ?: 'Employer' }}
                @endif
                @if($city)
                    <span> · {{ $city }}</span>
                @endif
            </p>
            <h3 class="text-lg font-semibold text-slate-900 leading-tight"><a href="{{ $href }}" class="hover:text-slate-700">{{ $title }}</a></h3>
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
        @if($experienceLevelText)
            <span class="cw-chip">{{ $experienceLevelText }}</span>
        @endif
        @if($educationText)
            <span class="cw-chip">{{ $educationText }}</span>
        @endif
        @if($positionsText)
            <span class="cw-chip">{{ $positionsText }}</span>
        @endif
        @if($workingHoursText)
            <span class="cw-chip">{{ $workingHoursText }}</span>
        @endif
        @if($startDateText)
            <span class="cw-chip">{{ $startDateText }}</span>
        @endif
        @if($startFlexibilityText)
            <span class="cw-chip">{{ $startFlexibilityText }}</span>
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

    <div class="mt-4 flex flex-wrap gap-2">
        <a href="{{ $href }}" class="cw-button-secondary">View role</a>
        @if($company_href)
            <a href="{{ $company_href }}" class="cw-button-secondary">Company profile</a>
        @endif
    </div>
</article>
