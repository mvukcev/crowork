@props([
    'title',
    'company' => null,
    'company_href' => null,
    'employer_logo_url' => null,
    'job_cover_url' => null,
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
    $currencyCode = strtoupper((string) $salary_currency);
    $currencySymbol = $currencyCode === 'EUR' ? '€' : $currencyCode . ' ';
    $period = strtolower((string) $salary_period) === 'hour' ? 'hour' : 'month';

    $salaryPrimary = null;
    $salarySecondary = $period === 'hour' ? __('jobs.salary_hourly_gross') : __('jobs.salary_monthly_gross');
    if (!is_null($salary_min) || !is_null($salary_max)) {
        if (!is_null($salary_min) && !is_null($salary_max)) {
            $salaryPrimary = $currencySymbol . number_format((float) $salary_min, 0) . ' - ' . $currencySymbol . number_format((float) $salary_max, 0);
        } elseif (!is_null($salary_min)) {
            $salaryPrimary = __('jobs.salary_from_short', [
                'currency' => $currencySymbol,
                'amount' => number_format((float) $salary_min, 0),
            ]);
        } else {
            $salaryPrimary = __('jobs.salary_to_short', [
                'currency' => $currencySymbol,
                'amount' => number_format((float) $salary_max, 0),
            ]);
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
    $languageValues = array_values(array_filter(array_map(fn ($lang) => cw_localize_language_code((string) $lang), $languageValues)));
    $languageText = count($languageValues) ? implode(', ', array_slice($languageValues, 0, 2)) : null;
    $languageOverflow = max(count($languageValues) - 2, 0);

    $employmentKey = strtolower(str_replace(['-', ' '], '_', (string) $employment_type));
    $employmentChipText = cw_localize_job_value('employment_type', $employment_type);
    $experienceLevelText = cw_localize_job_value('experience_level', $experience_level);
    $educationText = cw_localize_job_value('education_required', $education_required);
    $postedText = $posted_at ? __('jobs.posted_short', ['time' => \Carbon\Carbon::parse($posted_at)->diffForHumans()]) : null;

    $companyName = trim((string) ($company ?: __('jobs.employer_fallback')));
    $logoInitials = collect(preg_split('/\\s+/', $companyName))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->join('');
    if ($logoInitials === '') {
        $logoInitials = 'CW';
    }

    $isRemote = str_contains(strtolower((string) $title), 'remote')
        || str_contains(strtolower((string) $city), 'remote')
        || $employmentKey === 'remote';
@endphp

<article class="cw-listing-card cw-listing-card-job h-full">
    <div class="cw-listing-card-inner">
        @if($job_cover_url)
            <div class="cw-job-cover-strip">
                <img src="{{ $job_cover_url }}" alt="{{ $title }}" class="h-full w-full object-cover" loading="lazy" decoding="async">
            </div>
        @endif

        <div class="cw-listing-card-top">
            <div class="min-w-0">
                <p class="cw-listing-company">
                    @if($company_href && $company)
                        <a href="{{ $company_href }}" class="hover:text-slate-900 underline-offset-2 hover:underline" data-cw-track-click="company_profile_click" data-cw-item-type="company" data-cw-item-slug="{{ $company }}">{{ $company }}</a>
                    @else
                        {{ $companyName }}
                    @endif
                </p>
                <p class="cw-listing-location">{{ $city ?: __('jobs.location_not_specified') }}</p>
            </div>
            @if($company_href)
                <a href="{{ $company_href }}" class="cw-employer-logo {{ $job_cover_url ? 'cw-employer-logo-float' : '' }}" aria-label="{{ $companyName }}" data-cw-track-click="employer_logo_click" data-cw-item-type="company" data-cw-item-slug="{{ $company }}">
                    @if($employer_logo_url)
                        <img src="{{ $employer_logo_url }}" alt="{{ $companyName }} logo" class="h-full w-full object-cover" loading="lazy" decoding="async" width="72" height="72" data-cw-logo-image data-cw-fallback-text="{{ $logoInitials }}" data-cw-fallback-label="{{ $companyName }}" onerror="this.onerror=null;this.src='{{ asset('assets/placeholders/shared/company-logo-placeholder-400x400.jpg') }}';">
                    @else
                        <span>{{ $logoInitials }}</span>
                    @endif
                </a>
            @else
                <div class="cw-employer-logo {{ $job_cover_url ? 'cw-employer-logo-float' : '' }}" aria-label="{{ $companyName }}">
                    @if($employer_logo_url)
                        <img src="{{ $employer_logo_url }}" alt="{{ $companyName }} logo" class="h-full w-full object-cover" loading="lazy" decoding="async" width="72" height="72" data-cw-logo-image data-cw-fallback-text="{{ $logoInitials }}" data-cw-fallback-label="{{ $companyName }}" onerror="this.onerror=null;this.src='{{ asset('assets/placeholders/shared/company-logo-placeholder-400x400.jpg') }}';">
                    @else
                        <span>{{ $logoInitials }}</span>
                    @endif
                </div>
            @endif
        </div>

        <div class="cw-listing-middle">
            <h3 class="cw-listing-title">
                <a href="{{ $href }}" class="hover:text-slate-700" data-cw-track-click="job_view" data-cw-item-type="job">{{ $title }}</a>
            </h3>

            <div class="cw-listing-salary" aria-label="{{ __('jobs.salary_label') }}">
                <p class="cw-listing-salary-primary">{{ $salaryPrimary ?: __('jobs.salary_not_disclosed') }}</p>
                <p class="cw-listing-salary-secondary">{{ $salarySecondary }}</p>
            </div>

            <div class="cw-listing-meta">
                @if($experienceLevelText)
                    <span>{{ __('jobs.metadata_experience', ['value' => $experienceLevelText]) }}</span>
                @endif
                @if($educationText)
                    <span>{{ __('jobs.metadata_education', ['value' => $educationText]) }}</span>
                @endif
                @if($languageText)
                    <span>{{ __('jobs.metadata_languages', ['value' => $languageText, 'extra' => $languageOverflow > 0 ? '+' . $languageOverflow : '']) }}</span>
                @endif
                @if($postedText)
                    <span>{{ $postedText }}</span>
                @endif
            </div>
        </div>

        <div class="cw-listing-bottom mt-auto">
            <div class="cw-listing-chip-row">
                @if($employmentChipText)
                    <span class="cw-listing-chip">{{ $employmentChipText }}</span>
                @endif
                @if($accommodation_provided)
                    <span class="cw-listing-chip">{{ __('jobs.chip_accommodation_included') }}</span>
                @endif
                @if($visa_support)
                    <span class="cw-listing-chip">{{ __('jobs.chip_visa_support') }}</span>
                @endif
                @if($isRemote)
                    <span class="cw-listing-chip">{{ __('jobs.chip_remote') }}</span>
                @endif
                @if($is_urgent)
                    <span class="cw-listing-chip is-urgent">{{ __('jobs.chip_urgent') }}</span>
                @endif
            </div>

            <div class="cw-listing-actions">
                <a href="{{ $href }}" class="cw-card-cta-primary" data-cw-track-click="job_view" data-cw-item-type="job">{{ __('ui.jobs_page.view_role') }}</a>
                @if($company_href)
                    <a href="{{ $company_href }}" class="cw-card-cta-secondary" data-cw-track-click="company_profile_click" data-cw-item-type="company" data-cw-item-slug="{{ $company }}">{{ __('ui.jobs_page.company_profile') }}</a>
                @endif
            </div>
        </div>
    </div>
</article>
