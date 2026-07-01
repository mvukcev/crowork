<x-app-layout>
    <x-slot name="title">{{ $job->title }}</x-slot>
    <x-slot name="description">{{ \Illuminate\Support\Str::limit(strip_tags($job->description), 150) }}</x-slot>
    <x-slot name="canonical">{{ route('jobs.show', $job) }}</x-slot>
    <x-slot name="ogType">article</x-slot>

    @php
        $isPreview = (bool) ($isPreview ?? false);
        $companyProfileUrl = $job->employer?->slug ? route('companies.show', $job->employer) : null;
        $companyName = $job->employer?->company_display_name
            ?? $job->employer?->company_name
            ?? $job->external_company_name
            ?? __('jobs.employer_fallback');
        $location = $job->location_city ?: null;
        $category = cw_localize_job_value('category', $job->category);

        $salaryDisplay = null;
        if (!is_null($job->salary_min) || !is_null($job->salary_max)) {
            $currency = strtoupper((string) ($job->salary_currency ?? 'EUR'));
            $period = strtolower((string) ($job->salary_period ?? 'month')) === 'hour' ? 'hour' : 'month';
            $periodLabel = __('jobs.' . $period);

            if (!is_null($job->salary_min) && !is_null($job->salary_max)) {
                $salaryDisplay = __('jobs.salary_range', [
                    'currency' => $currency,
                    'min' => number_format((float) $job->salary_min),
                    'max' => number_format((float) $job->salary_max),
                    'period' => $periodLabel,
                ]);
            } elseif (!is_null($job->salary_min)) {
                $salaryDisplay = __('jobs.salary_from', [
                    'currency' => $currency,
                    'amount' => number_format((float) $job->salary_min),
                    'period' => $periodLabel,
                ]);
            } else {
                $salaryDisplay = __('jobs.salary_up_to', [
                    'currency' => $currency,
                    'amount' => number_format((float) $job->salary_max),
                    'period' => $periodLabel,
                ]);
            }
        }

        $employmentType = cw_localize_job_value('employment_type', $job->contract_type);
        $employmentTypeMap = [
            'full-time' => 'FULL_TIME',
            'part-time' => 'PART_TIME',
            'contract' => 'CONTRACTOR',
            'temporary' => 'TEMPORARY',
            'internship' => 'INTERN',
            'seasonal' => 'TEMPORARY',
            'freelance' => 'CONTRACTOR',
        ];
        $employmentTypeSchema = $employmentTypeMap[strtolower((string) $job->contract_type)] ?? null;
        $experienceLevel = cw_localize_job_value('experience_level', $job->experience_level);
        $educationRequired = cw_localize_job_value('education_required', $job->education_required);

        $languageValues = [];
        if (is_array($job->languages)) {
            $languageValues = $job->languages;
        } elseif (is_string($job->languages) && trim($job->languages) !== '') {
            $decodedLanguages = json_decode($job->languages, true);
            $languageValues = is_array($decodedLanguages) ? $decodedLanguages : preg_split('/[\n,]+/', $job->languages);
        }

        $languageValues = array_values(array_filter(array_map(fn ($lang) => trim((string) $lang), $languageValues)));
        $languageValues = array_values(array_filter(array_map(fn ($lang) => cw_localize_language_code((string) $lang), $languageValues)));
        $languageSummary = count($languageValues) ? implode(', ', $languageValues) : null;

        $publishedDate = $job->published_at?->translatedFormat('j M Y') ?? $job->created_at?->translatedFormat('j M Y');
        $postedAgo = $job->published_at?->diffForHumans() ?? $job->created_at?->diffForHumans();
        $expiryDate = $job->expires_at?->translatedFormat('j M Y');
        $startDateDisplay = $job->start_date?->translatedFormat('j M Y');
        $workingHoursText = trim((string) ($job->working_hours ?? ''));
        $shiftDetailsText = trim((string) ($job->shift_details ?? ''));
        $applicationInstructionsText = trim((string) ($job->application_instructions ?? ''));
        $contractDurationDisplay = cw_localize_job_value('contract_duration', $job->contract_duration);
        $startFlexibilityDisplay = cw_localize_job_value('start_flexibility', $job->start_flexibility);
        $workingHoursText = cw_localize_job_value('working_hours', $workingHoursText);
        $shiftDetailsText = cw_localize_job_value('shift_details', $shiftDetailsText);

        $aboutText = trim((string) ($job->description ?? ''));
        $responsibilitiesText = trim((string) ($job->responsibilities ?? ''));
        $requirementsText = trim((string) ($job->requirements ?? ''));
        $benefitsText = trim((string) ($job->benefits ?? ''));

        $normalizeImportedLine = static function (?string $value): string {
            $value = trim((string) $value);
            if ($value === '') {
                return '';
            }

            $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $value = strip_tags($value);
            $value = preg_replace('/\s+/u', ' ', $value) ?? $value;
            $value = trim((string) $value);
            if ($value === '') {
                return '';
            }

            $value = preg_replace('/^•\s*/u', '- ', $value) ?? $value;
            $value = preg_replace('/^[-*]\s*/u', '- ', $value) ?? $value;
            $value = preg_replace('/:\s*/u', ': ', $value) ?? $value;

            $upperRatio = preg_match_all('/\p{Lu}/u', $value, $upperMatches) ?: 0;
            $letterCount = preg_match_all('/\p{L}/u', $value, $letterMatches) ?: 0;
            if ($letterCount > 0 && ($upperRatio / $letterCount) > 0.72) {
                $lower = mb_strtolower($value, 'UTF-8');
                $value = mb_strtoupper(mb_substr($lower, 0, 1), 'UTF-8') . mb_substr($lower, 1);
            }

            return $value;
        };

        $normalizeBlock = static function (?string $value) use ($normalizeImportedLine): string {
            $lines = preg_split('/\r\n|\r|\n/', (string) $value) ?: [];
            $result = [];
            $seen = [];

            foreach ($lines as $line) {
                $line = $normalizeImportedLine($line);
                if ($line === '') {
                    continue;
                }

                $hash = mb_strtolower($line, 'UTF-8');
                if (isset($seen[$hash])) {
                    continue;
                }

                if (preg_match('/^(izvorni\s+oglas|službeni\s+izvor)\b/iu', $line)) {
                    continue;
                }

                $seen[$hash] = true;
                $result[] = $line;
            }

            return trim(implode("\n", $result));
        };

        $normalizeForCompare = static function (?string $value): string {
            $value = trim((string) $value);
            if ($value === '') {
                return '';
            }

            $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $value = strip_tags($value);
            $value = mb_strtolower($value, 'UTF-8');
            $value = preg_replace('/^(opis\s+posla|odgovornosti|uvjeti|pogodnosti|benefiti|teren|radno\s+iskustvo|razina\s+obrazovanja)\s*:\s*/u', '', $value) ?? $value;
            $value = preg_replace('/[\p{P}\p{S}]+/u', ' ', $value) ?? $value;
            $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

            return trim((string) $value);
        };

        $dedupeLinesAgainst = static function (?string $value, array $referenceBlocks) use ($normalizeForCompare): string {
            $lines = preg_split('/\r\n|\r|\n/', (string) $value) ?: [];
            if (count($lines) === 0) {
                return '';
            }

            $referencePool = [];
            foreach ($referenceBlocks as $refBlock) {
                $refLines = preg_split('/\r\n|\r|\n/', (string) $refBlock) ?: [];
                foreach ($refLines as $refLine) {
                    $normalizedRef = $normalizeForCompare($refLine);
                    if ($normalizedRef !== '') {
                        $referencePool[$normalizedRef] = true;
                    }
                }
            }

            $kept = [];
            $localSeen = [];
            foreach ($lines as $line) {
                $original = trim((string) $line);
                if ($original === '') {
                    continue;
                }

                $normalized = $normalizeForCompare($original);
                if ($normalized === '') {
                    continue;
                }

                if (isset($localSeen[$normalized]) || isset($referencePool[$normalized])) {
                    continue;
                }

                $localSeen[$normalized] = true;
                $kept[] = $original;
            }

            return trim(implode("\n", $kept));
        };

        $aboutText = $normalizeBlock($aboutText);
        $responsibilitiesText = $normalizeBlock($responsibilitiesText);
        $requirementsText = $normalizeBlock($requirementsText);
        $benefitsText = $normalizeBlock($benefitsText);
        $applicationInstructionsText = $normalizeBlock($applicationInstructionsText);

        $responsibilitiesText = $dedupeLinesAgainst($responsibilitiesText, [$aboutText]);
        $requirementsText = $dedupeLinesAgainst($requirementsText, [$aboutText, $responsibilitiesText]);
        $benefitsText = $dedupeLinesAgainst($benefitsText, [$aboutText, $responsibilitiesText, $requirementsText]);
        $applicationInstructionsText = $dedupeLinesAgainst($applicationInstructionsText, [$aboutText, $responsibilitiesText, $requirementsText, $benefitsText]);

        $renderStructuredBlock = static function (?string $value): string {
            $lines = preg_split('/\r\n|\r|\n/', (string) $value) ?: [];
            $paragraphs = [];
            $bullets = [];

            foreach ($lines as $line) {
                $line = trim((string) $line);
                if ($line === '') {
                    continue;
                }

                if (preg_match('/^[-*]\s*(.+)$/u', $line, $matches)) {
                    $bullets[] = e(trim((string) $matches[1]));
                    continue;
                }

                $paragraphs[] = e($line);
            }

            $html = '';
            foreach ($paragraphs as $paragraph) {
                $html .= '<p>' . $paragraph . '</p>';
            }

            if (count($bullets) > 0) {
                $html .= '<ul class="cw-rich-list">';
                foreach ($bullets as $item) {
                    $html .= '<li>' . $item . '</li>';
                }
                $html .= '</ul>';
            }

            return $html;
        };

        $mobilityDetails = array_values(array_filter([
            $job->accommodation_provided ? __('ui.jobs_show.accommodation_provided_line') : null,
            !empty($job->accommodation_details) ? trim((string) $job->accommodation_details) : null,
            $job->visa_support ? __('ui.jobs_show.visa_support_line') : null,
            !empty($job->visa_support_details) ? trim((string) $job->visa_support_details) : null,
        ]));

        $keyFacts = [
            __('ui.jobs_show.fact_employment_type') => $employmentType,
            __('ui.jobs_show.fact_category') => $category,
            __('ui.jobs_show.fact_city') => $location,
            __('ui.jobs_show.fact_positions_available') => $job->positions_available,
            __('ui.jobs_show.fact_experience_level') => $experienceLevel,
            __('ui.jobs_show.fact_education_required') => $educationRequired,
            __('ui.jobs_show.fact_contract_duration') => $contractDurationDisplay,
            __('ui.jobs_show.fact_start_date') => $startDateDisplay,
            __('ui.jobs_show.fact_start_flexibility') => $startFlexibilityDisplay,
            __('ui.jobs_show.fact_working_hours') => $workingHoursText,
            __('ui.jobs_show.fact_shifts') => $shiftDetailsText,
            __('ui.jobs_show.fact_languages') => $languageSummary,
            __('ui.jobs_show.fact_salary') => $salaryDisplay,
            __('ui.jobs_show.fact_apply_before') => $expiryDate,
        ];

        $aboutEmployerText = trim((string) ($job->employer?->description ?? ''));
        $employerLogoUrl = $job->employer?->logo_path ? asset('storage/' . $job->employer->logo_path) : null;
        $employerCoverUrl = $job->employer?->cover_image_path ? asset('storage/' . $job->employer->cover_image_path) : null;
        $brandColor = preg_match('/^#[A-Fa-f0-9]{6}$/', (string) ($job->employer?->brand_color ?? '')) === 1
            ? strtoupper((string) $job->employer->brand_color)
            : '#0F274D';
        $jobCoverUrl = $job->cover_image_path ? asset('storage/' . $job->cover_image_path) : null;
        $isHzzImported = $job->isImportedFromHzz();
        $isHzzOfficial = $job->isHzzOfficial();
        $hzzLegalNotice = trim((string) ($job->hzz_legal_notice ?? ''));
        $normalizeCompareText = function (?string $value): string {
            $value = trim((string) $value);
            if ($value === '') {
                return '';
            }

            $value = html_entity_decode($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $value = strip_tags($value);
            $value = mb_strtolower($value, 'UTF-8');
            $value = preg_replace('/[^\pL\pN\s]/u', ' ', $value) ?? $value;
            $value = preg_replace('/\s+/u', ' ', $value) ?? $value;

            return trim((string) $value);
        };

        $isMostlyDuplicate = function (?string $left, ?string $right) use ($normalizeCompareText): bool {
            $a = $normalizeCompareText($left);
            $b = $normalizeCompareText($right);

            if ($a === '' || $b === '') {
                return false;
            }

            $short = min(strlen($a), strlen($b));
            $long = max(strlen($a), strlen($b));
            if ($long === 0) {
                return false;
            }

            if (str_contains($a, $b) || str_contains($b, $a)) {
                return ($short / $long) >= 0.67;
            }

            similar_text($a, $b, $percent);

            return ($percent / 100) >= 0.67;
        };

        if ($isHzzOfficial && $isMostlyDuplicate($hzzLegalNotice, $applicationInstructionsText)) {
            $applicationInstructionsText = '';
        }

        if ($isHzzOfficial && $isMostlyDuplicate($aboutText, $applicationInstructionsText)) {
            $applicationInstructionsText = '';
        }

        if ($isMostlyDuplicate($responsibilitiesText, $aboutText)) {
            $responsibilitiesText = '';
        }

        if ($isMostlyDuplicate($requirementsText, $aboutText) || $isMostlyDuplicate($requirementsText, $responsibilitiesText)) {
            $requirementsText = '';
        }

        if ($isMostlyDuplicate($benefitsText, $aboutText) || $isMostlyDuplicate($benefitsText, $responsibilitiesText)) {
            $benefitsText = '';
        }

        $showHzzApplicationInstructions = ! $isHzzOfficial || (auth()->check() && auth()->user()?->isWorker());
        $hzzLogoPath = public_path('assets/branding/hzz-logo.svg');
    $hzzLogoUrl = file_exists($hzzLogoPath)
        ? asset('assets/branding/hzz-logo.svg')
        : (trim((string) ($job->source_logo_url ?? '')) ?: config('services.hzz.logo_url'));

        $jobPostingSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'JobPosting',
            'title' => $job->title,
            'description' => \Illuminate\Support\Str::limit(strip_tags((string) $job->description), 4000, ''),
            'datePosted' => optional($job->published_at ?? $job->created_at)?->toIso8601String(),
            'validThrough' => optional($job->expires_at)?->toIso8601String(),
            'employmentType' => $employmentTypeSchema,
            'hiringOrganization' => [
                '@type' => 'Organization',
                'name' => $companyName,
                'sameAs' => $job->employer?->website,
                'logo' => $employerLogoUrl,
            ],
            'jobLocation' => $location ? [
                '@type' => 'Place',
                'address' => [
                    '@type' => 'PostalAddress',
                    'addressLocality' => $location,
                    'addressCountry' => 'HR',
                ],
            ] : null,
            'baseSalary' => (!is_null($job->salary_min) || !is_null($job->salary_max)) ? [
                '@type' => 'MonetaryAmount',
                'currency' => $job->salary_currency ?? 'EUR',
                'value' => [
                    '@type' => 'QuantitativeValue',
                    'minValue' => $job->salary_min,
                    'maxValue' => $job->salary_max,
                    'unitText' => strtoupper((string) ($job->salary_period ?? 'MONTH')),
                ],
            ] : null,
            'directApply' => $job->canApplyViaCroWork(),
            'url' => route('jobs.show', $job),
            'inLanguage' => app()->getLocale(),
        ];

        $jobPostingSchema = array_filter($jobPostingSchema, fn ($value) => !is_null($value) && $value !== '');

        $breadcrumbSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => __('navigation.home'),
                    'item' => route('home'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => __('navigation.jobs'),
                    'item' => route('jobs.index'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $job->title,
                    'item' => route('jobs.show', $job),
                ],
            ],
        ];
    @endphp

    @push('head')
        <script type="application/ld+json">{!! json_encode($jobPostingSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        <script type="application/ld+json">{!! json_encode($breadcrumbSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <section class="cw-section">
        <div class="cw-container">
            @if($isPreview)
                <article class="cw-surface p-4 md:p-5 mb-6 border border-amber-200 bg-amber-50">
                    <p class="text-sm font-semibold text-amber-900 mb-1">{{ __('ui.jobs_show.preview_mode_title') }}</p>
                    <p class="text-sm text-amber-800">{{ __('ui.jobs_show.preview_mode_body') }}</p>
                </article>
            @endif

            @if($isHzzOfficial)
                <article class="cw-surface p-5 md:p-6 mb-6 border border-slate-200">
                    <div class="flex items-start gap-4">
                        @if($hzzLogoUrl)
                            <img src="{{ $hzzLogoUrl }}" alt="HZZ logo" class="w-11 h-11 shrink-0 rounded-xl border border-slate-200 bg-white object-contain p-1" loading="lazy" decoding="async">
                        @else
                            <div class="w-11 h-11 shrink-0 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center text-sm font-bold text-slate-700">
                                HZZ
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="text-xs font-semibold uppercase tracking-[0.08em] text-slate-500 mb-1">Službeni izvor</p>
                            <h2 class="text-base font-semibold text-slate-900 mb-1">Hrvatski zavod za zapošljavanje</h2>
                            <p class="text-sm text-slate-700 mb-2">Oglas je preuzet iz HZZ sustava.</p>
                            @if($hzzLegalNotice !== '')
                                <div class="text-sm text-slate-600 whitespace-pre-line">{{ $hzzLegalNotice }}</div>
                            @endif
                        </div>
                    </div>
                </article>
            @endif

            <div class="mb-6 text-sm text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-slate-900">{{ __('navigation.home') }}</a>
                <span class="mx-1">/</span>
                <a href="{{ route('jobs.index') }}" class="hover:text-slate-900">{{ __('navigation.jobs') }}</a>
                <span class="mx-1">/</span>
                <span class="text-slate-700">{{ $job->title }}</span>
            </div>

            <article class="cw-surface p-6 md:p-8 mb-6 overflow-hidden" style="background: linear-gradient(150deg, {{ $brandColor }}10, transparent 42%), var(--cw-surface); border: 1px solid color-mix(in srgb, {{ $brandColor }} 16%, var(--cw-hairline));">
                @if($jobCoverUrl)
                    <div class="-mx-6 md:-mx-8 -mt-6 md:-mt-8 mb-5 aspect-[2/1] overflow-hidden rounded-t-[inherit]">
                        <img src="{{ $jobCoverUrl }}" alt="{{ $job->title }}" class="h-full w-full object-cover" loading="eager" fetchpriority="high" decoding="async">
                    </div>
                @endif

                <p class="cw-kicker mb-2">{{ __('ui.jobs_show.kicker') }}</p>
                <h1 class="cw-display text-4xl md:text-6xl mb-3">{{ $job->title }}</h1>
                <p class="text-base text-slate-600 mb-4">
                    @if($companyProfileUrl)
                        <a href="{{ $companyProfileUrl }}" class="hover:text-slate-900 underline-offset-2 hover:underline">{{ $companyName }}</a>
                    @else
                        {{ $companyName }}
                    @endif
                    @if($location)
                        <span> · {{ $location }}</span>
                    @endif
                </p>

                <div class="flex flex-wrap gap-2">
                    @if($category)
                        <span class="cw-chip">{{ $category }}</span>
                    @endif
                    @if($employmentType)
                        <span class="cw-chip">{{ $employmentType }}</span>
                    @endif
                    @if($job->accommodation_provided)
                        <span class="cw-chip text-amber-800 bg-amber-50 border-amber-200">{{ __('jobs.chip_accommodation_included') }}</span>
                    @endif
                    @if($job->visa_support)
                        <span class="cw-chip text-emerald-800 bg-emerald-50 border-emerald-200">{{ __('jobs.chip_visa_support') }}</span>
                    @endif
                    @if($job->is_urgent)
                        <span class="cw-chip text-red-800 bg-red-50 border-red-200">{{ __('jobs.chip_urgent') }}</span>
                    @endif
                    @if($job->is_featured)
                        <span class="cw-chip text-indigo-800 bg-indigo-50 border-indigo-200">{{ __('jobs.featured_tag') }}</span>
                    @endif
                </div>

                <div class="mt-4 flex flex-wrap gap-5 text-sm text-slate-500">
                    <p>{{ __('ui.jobs_show.published_line', ['date' => $publishedDate, 'ago' => $postedAgo]) }}</p>
                    @if($expiryDate)
                        <p>{{ __('ui.jobs_show.expires_line', ['date' => $expiryDate]) }}</p>
                    @endif
                </div>
            </article>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                <div class="lg:col-span-2 space-y-4">
                    <article class="cw-surface p-6 md:p-7">
                        <h2 class="text-xl font-semibold text-slate-900 mb-3">{{ __('ui.jobs_show.key_facts') }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                            @foreach($keyFacts as $label => $value)
                                @if(!is_null($value) && trim((string) $value) !== '')
                                    <p class="text-slate-700 leading-snug"><strong>{{ $label }}:</strong> {{ $value }}</p>
                                @endif
                            @endforeach
                        </div>
                    </article>

                    @if($aboutText !== '')
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">{{ __('ui.jobs_show.about_this_job') }}</h2>
                            <div class="cw-rich-text text-slate-700">{!! $renderStructuredBlock($aboutText) !!}</div>
                        </article>
                    @endif

                    @if($responsibilitiesText !== '')
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">{{ __('ui.jobs_show.responsibilities') }}</h2>
                            <div class="cw-rich-text text-slate-700">{!! $renderStructuredBlock($responsibilitiesText) !!}</div>
                        </article>
                    @endif

                    @if($requirementsText !== '')
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">{{ __('ui.jobs_show.requirements') }}</h2>
                            <div class="cw-rich-text text-slate-700">{!! $renderStructuredBlock($requirementsText) !!}</div>
                        </article>
                    @endif

                    @if($benefitsText !== '')
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">{{ __('ui.jobs_show.benefits') }}</h2>
                            <div class="cw-rich-text text-slate-700">{!! $renderStructuredBlock($benefitsText) !!}</div>
                        </article>
                    @endif

                    @if(count($mobilityDetails) > 0)
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">{{ __('ui.jobs_show.mobility_support') }}</h2>
                            <ul class="space-y-2 text-slate-700 text-sm">
                                @foreach($mobilityDetails as $mobilityLine)
                                    <li>{{ $mobilityLine }}</li>
                                @endforeach
                            </ul>
                        </article>
                    @endif

                    @if($applicationInstructionsText !== '' && $showHzzApplicationInstructions)
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">{{ __('ui.jobs_show.application_instructions') }}</h2>
                            <div class="cw-rich-text text-slate-700">{!! $renderStructuredBlock($applicationInstructionsText) !!}</div>
                        </article>
                    @endif

                    @if($isHzzImported && ! $isHzzOfficial)
                        <article class="cw-hzz-attribution" aria-label="HZZ attribution">
                            <div class="cw-hzz-attribution__head">
                                @if($hzzLogoUrl)
                                    <img src="{{ $hzzLogoUrl }}" alt="HZZ" class="cw-hzz-attribution__logo" loading="lazy" decoding="async" width="120" height="40">
                                @endif
                                <span class="cw-hzz-attribution__badge">{{ __('ui.jobs_show.hzz_source_badge') }}</span>
                            </div>

                            <h2 class="cw-hzz-attribution__title">{{ __('ui.jobs_show.hzz_source_title') }}</h2>
                            <p class="cw-hzz-attribution__line">{{ __('ui.jobs_show.hzz_source_rights') }}</p>
                            <p class="cw-hzz-attribution__line">{{ __('ui.jobs_show.hzz_source_transfer') }}</p>
                            <p class="cw-hzz-attribution__line cw-hzz-attribution__line--disclaimer">{{ __('ui.jobs_show.hzz_source_disclaimer') }}</p>

                        </article>
                    @endif



                    @if(($similarJobs ?? collect())->count() > 0)
                        <section class="cw-section !pt-2">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">{{ __('ui.jobs_show.similar_jobs') }}</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($similarJobs as $similarJob)
                                    <x-job-card
                                        :title="$similarJob->title"
                                        :company="$similarJob->employer?->company_name"
                                        :company_href="$similarJob->employer?->slug ? route('companies.show', $similarJob->employer) : null"
                                        :job_cover_url="$similarJob->cover_image_path ? asset('storage/' . $similarJob->cover_image_path) : null"
                                        :city="$similarJob->location_city"
                                        :salary_min="$similarJob->salary_min"
                                        :salary_max="$similarJob->salary_max"
                                        :salary_currency="$similarJob->salary_currency ?? 'EUR'"
                                        :salary_period="$similarJob->salary_period ?? 'month'"
                                        :employment_type="$similarJob->contract_type"
                                        :accommodation_provided="$similarJob->accommodation_provided"
                                        :visa_support="$similarJob->visa_support"
                                        :is_urgent="$similarJob->is_urgent"
                                        :is_featured="$similarJob->is_featured"
                                        :languages="$similarJob->languages ?? []"
                                        :posted_at="$similarJob->published_at ?? $similarJob->created_at"
                                        :href="route('jobs.show', $similarJob)"
                                    />
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>

                <aside class="space-y-4 lg:sticky lg:top-24">
                    <div class="cw-surface p-5">
                        <h2 class="text-lg font-semibold text-slate-900 mb-3">{{ __('ui.jobs_show.apply_to_role') }}</h2>

                        @if($salaryDisplay)
                            <p class="text-sm text-slate-700 mb-2"><strong>{{ __('ui.jobs_show.fact_salary') }}:</strong> {{ $salaryDisplay }}</p>
                        @endif
                        @if($location)
                            <p class="text-sm text-slate-700 mb-2"><strong>{{ __('ui.jobs_show.fact_city') }}:</strong> {{ $location }}</p>
                        @endif
                        @if($employmentType)
                            <p class="text-sm text-slate-700 mb-2"><strong>{{ __('ui.jobs_show.fact_employment_type') }}:</strong> {{ $employmentType }}</p>
                        @endif
                        @if($experienceLevel)
                            <p class="text-sm text-slate-700 mb-2"><strong>{{ __('ui.jobs_show.fact_experience_level') }}:</strong> {{ $experienceLevel }}</p>
                        @endif
                        @if($educationRequired)
                            <p class="text-sm text-slate-700 mb-2"><strong>{{ __('ui.jobs_show.fact_education_required') }}:</strong> {{ $educationRequired }}</p>
                        @endif
                        @if($workingHoursText)
                            <p class="text-sm text-slate-700 mb-2"><strong>{{ __('ui.jobs_show.fact_working_hours') }}:</strong> {{ $workingHoursText }}</p>
                        @endif
                        @if($shiftDetailsText)
                            <p class="text-sm text-slate-700 mb-2"><strong>{{ __('ui.jobs_show.fact_shifts') }}:</strong> {{ $shiftDetailsText }}</p>
                        @endif
                        @if($startDateDisplay)
                            <p class="text-sm text-slate-700 mb-2"><strong>{{ __('ui.jobs_show.fact_start_date') }}:</strong> {{ $startDateDisplay }}</p>
                        @endif
                        @if($contractDurationDisplay)
                            <p class="text-sm text-slate-700 mb-2"><strong>{{ __('ui.jobs_show.fact_contract_duration') }}:</strong> {{ $contractDurationDisplay }}</p>
                        @endif
                        @if($languageSummary)
                            <p class="text-sm text-slate-700 mb-2"><strong>{{ __('ui.jobs_show.fact_languages') }}:</strong> {{ $languageSummary }}</p>
                        @endif
                        @if($job->accommodation_provided)
                            <p class="text-sm text-slate-700 mb-2"><strong>{{ __('ui.jobs_show.accommodation_label') }}:</strong> {{ __('ui.jobs_show.provided') }}</p>
                        @endif
                        @if($job->visa_support)
                            <p class="text-sm text-slate-700 mb-2"><strong>{{ __('ui.jobs_show.visa_label') }}:</strong> {{ __('ui.jobs_show.supported') }}</p>
                        @endif
                        @if($expiryDate)
                            <p class="text-sm text-slate-700 mb-2"><strong>{{ __('ui.jobs_show.fact_apply_before') }}:</strong> {{ $expiryDate }}</p>
                        @endif

                        <div class="flex flex-col gap-2 mt-3">
                            @if($isPreview)
                                <span class="cw-button-secondary w-full text-center opacity-80 cursor-not-allowed">{{ __('ui.jobs_show.preview_apply_disabled') }}</span>
                            @else
                                <a
                                    href="{{ route('jobs.apply', $job) }}"
                                    class="{{ $isHzzOfficial ? 'cw-button-primary' : 'cw-button-violet' }} w-full text-center"
                                    @if($isHzzOfficial) data-hzz-primary-cta="1" @endif
                                    data-cw-track-click="job_apply_click"
                                    data-cw-item-type="job"
                                    data-cw-item-slug="{{ $job->slug }}"
                                >
                                    {{ $isHzzOfficial ? __('ui.jobs_show.apply_via_crowork') : __('ui.jobs_show.apply_now') }}
                                </a>
                            @endif
                        </div>
                    </div>

                    @if($aboutEmployerText !== '' || $job->employer)
                        <div class="cw-surface p-5">
                            @if($employerCoverUrl)
                                <div class="-mx-5 -mt-5 mb-4 aspect-[13/5] overflow-hidden rounded-t-[inherit] border-b border-slate-200">
                                    <img src="{{ $employerCoverUrl }}" alt="{{ $companyName }} cover" class="h-full w-full object-cover" loading="lazy" decoding="async">
                                </div>
                            @endif

                            <div class="flex gap-3" style="background: linear-gradient(140deg, {{ $brandColor }}10, transparent 45%); border-radius: 0.75rem; padding: 0.75rem;">
                                <div class="flex-1">
                                    <h3 class="text-base font-semibold text-slate-900 mb-3">{{ __('ui.jobs_show.about_employer') }}</h3>
                                    @if($aboutEmployerText !== '')
                                        <div class="text-sm text-slate-700 leading-relaxed mb-3">{{ $aboutEmployerText }}</div>
                                    @endif
                                    @if($job->employer)
                                        <p class="text-sm text-slate-700 mb-3">{{ $companyName }}@if($job->employer->city) · {{ $job->employer->city }}@endif</p>
                                        @if($companyProfileUrl)
                                            <a href="{{ $companyProfileUrl }}" class="cw-button-secondary w-full text-center" data-cw-track-click="company_profile_click" data-cw-item-type="company" data-cw-item-slug="{{ $job->employer?->slug }}">{{ __('ui.jobs_page.company_profile') }}</a>
                                        @endif
                                    @endif
                                </div>
                                @if($employerLogoUrl || $job->employer)
                                    <div class="flex-shrink-0">
                                        <div class="w-14 h-14 rounded-full border border-slate-300 bg-gradient-to-br from-white to-slate-50 flex items-center justify-center overflow-hidden">
                                            @if($employerLogoUrl)
                                                <img src="{{ $employerLogoUrl }}" alt="{{ $companyName }} logo" class="w-full h-full object-cover" loading="lazy" decoding="async" width="56" height="56" onerror="this.onerror=null;this.src='{{ asset('assets/placeholders/shared/company-logo-placeholder-400x400.jpg') }}';">
                                            @else
                                                <span class="text-xs font-bold text-slate-600">{{ substr($companyName, 0, 2) }}</span>
                                            @endif
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="cw-surface p-5">
                        <h3 class="text-base font-semibold text-slate-900 mb-2">{{ __('ui.jobs_show.report_question') }}</h3>
                        <a href="{{ route('reports.create', ['type' => 'job', 'id' => $job->id]) }}" class="cw-button-secondary w-full text-center">{{ __('ui.jobs_show.report_job') }}</a>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.cwTrack?.('job_view', {
                    job_slug: @json($job->slug),
                    company_slug: @json($job->employer?->slug),
                    has_salary: {{ (!is_null($job->salary_min) || !is_null($job->salary_max)) ? 'true' : 'false' }}
                });

                const hzzCta = document.querySelector('[data-hzz-primary-cta="1"]');
                if (hzzCta) {
                    const endpoint = @json(route('jobs.hzz.cta-click', $job));

                    hzzCta.addEventListener('click', function () {
                        const body = new URLSearchParams();
                        body.append('_token', @json(csrf_token()));

                        if (navigator.sendBeacon) {
                            const blob = new Blob([body.toString()], {
                                type: 'application/x-www-form-urlencoded;charset=UTF-8',
                            });
                            navigator.sendBeacon(endpoint, blob);
                            return;
                        }

                        fetch(endpoint, {
                            method: 'POST',
                            credentials: 'same-origin',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded;charset=UTF-8',
                                'X-CSRF-TOKEN': @json(csrf_token()),
                            },
                            body: body.toString(),
                            keepalive: true,
                        }).catch(() => {});
                    });
                }
            });
        </script>
    @endpush
</x-app-layout>
