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
        $isHzzImported = $job->isImportedFromHzz();
        $isHzzOfficial = $job->isHzzOfficial();
        $hzzLegalNotice = trim((string) ($job->hzz_legal_notice ?? ''));
        $contractDurationDisplay = cw_localize_job_value('contract_duration', $job->contract_duration);
        $startFlexibilityDisplay = cw_localize_job_value('start_flexibility', $job->start_flexibility);
        $workingHoursText = cw_localize_job_value('working_hours', $workingHoursText);
        $shiftDetailsText = cw_localize_job_value('shift_details', $shiftDetailsText);

        $aboutTextRaw = trim((string) ($job->description ?? ''));
        if ($isHzzOfficial && preg_match('/^hzz imported listing\.?$/iu', $aboutTextRaw) === 1) {
            $aboutTextRaw = '';
        }
        $aboutTextHasHtml = $aboutTextRaw !== strip_tags($aboutTextRaw);
        $aboutText = $aboutTextRaw;
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

        $aboutTextPlain = $normalizeBlock($aboutTextRaw);
        if (! $aboutTextHasHtml) {
            $aboutText = $aboutTextPlain;
        }
        $responsibilitiesText = $normalizeBlock($responsibilitiesText);
        $requirementsText = $normalizeBlock($requirementsText);
        $benefitsText = $normalizeBlock($benefitsText);
        $applicationInstructionsText = $normalizeBlock($applicationInstructionsText);

        $responsibilitiesText = $dedupeLinesAgainst($responsibilitiesText, [$aboutTextPlain]);
        $requirementsText = $dedupeLinesAgainst($requirementsText, [$aboutTextPlain, $responsibilitiesText]);
        $benefitsText = $dedupeLinesAgainst($benefitsText, [$aboutTextPlain, $responsibilitiesText, $requirementsText]);
        $applicationInstructionsText = $dedupeLinesAgainst($applicationInstructionsText, [$aboutTextPlain, $responsibilitiesText, $requirementsText, $benefitsText]);

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

        $renderRichEditorBlock = static function (?string $value): string {
            $html = trim((string) $value);
            if ($html === '') {
                return '';
            }

            $html = preg_replace('/<(script|style|iframe|object|embed|form)[^>]*>.*?<\/\1>/is', '', $html) ?? $html;
            $html = preg_replace('/\son[a-z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $html) ?? $html;

            $html = preg_replace_callback(
                '/<figure[^>]*data-trix-attachment=("|\')(.*?)\1[^>]*>.*?<\/figure>/is',
                static function (array $matches): string {
                    $json = html_entity_decode((string) ($matches[2] ?? ''), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $attachment = json_decode($json, true);

                    if (!is_array($attachment)) {
                        return '';
                    }

                    $contentType = strtolower((string) ($attachment['contentType'] ?? ''));
                    $url = trim((string) ($attachment['url'] ?? $attachment['href'] ?? ''));
                    if ($url === '' || !preg_match('/^(https?:\/\/|\/)/i', $url)) {
                        return '';
                    }

                    if (str_starts_with($contentType, 'image/')) {
                        $safeUrl = e($url);
                        $alt = e((string) ($attachment['filename'] ?? ''));

                        return '<p><img src="' . $safeUrl . '" alt="' . $alt . '" loading="lazy" decoding="async"></p>';
                    }

                    $name = trim((string) ($attachment['filename'] ?? $url));

                    return '<p><a href="' . e($url) . '" target="_blank" rel="noopener noreferrer">' . e($name) . '</a></p>';
                },
                $html
            ) ?? $html;

            $allowedTags = '<p><br><strong><em><b><i><u><s><ul><ol><li><a><blockquote><h3><h4><img><figure><figcaption>';
            $html = strip_tags($html, $allowedTags);

            $html = preg_replace_callback('/<a\b[^>]*href=("|\')(.*?)\1[^>]*>/i', static function (array $matches): string {
                $href = trim((string) ($matches[2] ?? ''));
                if ($href === '' || !preg_match('/^(https?:\/\/|mailto:|tel:|\/)/i', $href)) {
                    return '<a>';
                }

                return '<a href="' . e($href) . '" target="_blank" rel="noopener noreferrer">';
            }, $html) ?? $html;

            $html = preg_replace_callback('/<img\b[^>]*src=("|\')(.*?)\1[^>]*>/i', static function (array $matches): string {
                $src = trim((string) ($matches[2] ?? ''));
                if ($src === '' || !preg_match('/^(https?:\/\/|\/)/i', $src)) {
                    return '';
                }

                return '<img src="' . e($src) . '" alt="" loading="lazy" decoding="async">';
            }, $html) ?? $html;

            return $html;
        };

        $mobilityDetails = array_values(array_filter([
            $job->accommodation_provided ? __('ui.jobs_show.accommodation_provided_line') : null,
            !empty($job->accommodation_details) ? trim((string) $job->accommodation_details) : null,
            $job->visa_support ? __('ui.jobs_show.visa_support_line') : null,
            !empty($job->visa_support_details) ? trim((string) $job->visa_support_details) : null,
        ]));

        $sourcePayload = is_array($job->source_payload) ? $job->source_payload : [];
        $payloadValue = static function (array|string $keys) use ($sourcePayload) {
            foreach ((array) $keys as $key) {
                $value = \Illuminate\Support\Arr::get($sourcePayload, $key);
                if (is_array($value)) {
                    $value = implode(', ', array_filter(array_map(static fn ($item) => trim((string) $item), $value)));
                }

                if (trim((string) $value) !== '') {
                    return trim((string) $value);
                }
            }

            return null;
        };

        $isMeaningfulValue = static function ($value): bool {
            $value = trim((string) $value);
            if ($value === '') {
                return false;
            }

            return !in_array(mb_strtolower($value, 'UTF-8'), ['—', '-', 'n/a', 'na', 'unknown', 'nije poznato', 'nije specificirano'], true);
        };

        $hzzContactPerson = $normalizeImportedLine($payloadValue(['kontaktOsoba', 'kontakt_osoba']) ?? '');
        $hzzPhone = $normalizeImportedLine($payloadValue(['telefon', 'telefonBroj', 'mobitel']) ?? '');
        $hzzDrivingLicence = $normalizeBlock($payloadValue(['vozackiIspit', 'vozackaDozvola']) ?? '');
        $hzzLanguages = $normalizeBlock($payloadValue(['straniJezik', 'strani_jezik']) ?? '');
        $hzzComputerSkills = $normalizeBlock($payloadValue(['informatika', 'informatičkaZnanja']) ?? '');
        $hzzFood = $normalizeImportedLine($payloadValue(['prehrana']) ?? '');
        $hzzTransport = $normalizeImportedLine($payloadValue(['prijevoz']) ?? '');
        $hzzAccommodation = $normalizeImportedLine($payloadValue(['smjestaj']) ?? '');
        $hzzTerrain = $normalizeImportedLine($payloadValue(['teren']) ?? '');
        $hzzShiftMode = $normalizeImportedLine($payloadValue(['nacinRada']) ?? '');
        $hzzContactEmail = trim((string) ($job->hzz_apply_email ?? ''));
        $hzzApplyUrl = trim((string) ($job->hzz_apply_url ?? ''));
        $hzzHasBottomContact = $isHzzOfficial && array_filter([
            $isMeaningfulValue($hzzContactEmail) ? $hzzContactEmail : null,
            $isMeaningfulValue($hzzPhone) ? $hzzPhone : null,
            $isMeaningfulValue($hzzContactPerson) ? $hzzContactPerson : null,
            filter_var($hzzApplyUrl, FILTER_VALIDATE_URL) ? $hzzApplyUrl : null,
        ]) !== [];

        if ($isHzzOfficial && preg_match('/^[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}$/iu', $applicationInstructionsText) === 1) {
            $applicationInstructionsText = '';
        }

        if ($isHzzOfficial && filter_var($applicationInstructionsText, FILTER_VALIDATE_URL)) {
            $applicationInstructionsText = '';
        }

        $hzzSummaryText = null;
        if ($isHzzOfficial) {
            $summaryParts = [];
            $factCount = 0;

            if ($companyName && $job->title) {
                $lead = $companyName . ' traži';
                if ($job->positions_available) {
                    $lead .= ' ' . $job->positions_available . ' ' . ($job->positions_available === 1 ? 'radnika' : 'radnika');
                    $factCount++;
                }
                $lead .= ' za poziciju ' . mb_strtolower($job->title, 'UTF-8');
                if ($location) {
                    $lead .= ' u ' . $location;
                    $factCount++;
                }
                $summaryParts[] = rtrim($lead, '.') . '.';
            }

            $terms = [];
            if ($employmentType) {
                $terms[] = mb_strtolower($employmentType, 'UTF-8');
                $factCount++;
            }
            if ($workingHoursText) {
                $terms[] = mb_strtolower($workingHoursText, 'UTF-8');
                $factCount++;
            }
            if ($terms !== []) {
                $summaryParts[] = 'Radni odnos je ' . implode(' uz ', $terms) . '.';
            }

            $closing = [];
            if ($experienceLevel) {
                $closing[] = 'Radno iskustvo: ' . mb_strtolower($experienceLevel, 'UTF-8');
                $factCount++;
            }
            if ($expiryDate) {
                $closing[] = 'Prijave su otvorene do ' . $expiryDate;
                $factCount++;
            }
            if ($closing !== []) {
                $summaryParts[] = implode('. ', $closing) . '.';
            }

            if ($factCount >= 3) {
                $hzzSummaryText = implode(' ', $summaryParts);
            }
        }

        $keyFacts = $isHzzOfficial
            ? [
                __('ui.jobs_show.fact_location') => $location,
                __('ui.jobs_show.fact_employer') => $companyName,
                __('ui.jobs_show.fact_employment_type') => $employmentType,
                __('ui.jobs_show.fact_working_hours') => $workingHoursText,
                __('ui.jobs_show.fact_positions_available') => $job->positions_available,
                __('ui.jobs_show.fact_education_required') => $educationRequired,
                __('ui.jobs_show.fact_experience_level') => $experienceLevel,
                __('ui.jobs_show.fact_category') => $category,
                __('ui.jobs_show.fact_apply_before') => $expiryDate,
            ]
            : [
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

        if ($isHzzOfficial && $isMostlyDuplicate($aboutTextPlain, $applicationInstructionsText)) {
            $applicationInstructionsText = '';
        }

        if ($isMostlyDuplicate($responsibilitiesText, $aboutTextPlain)) {
            $responsibilitiesText = '';
        }

        if ($isMostlyDuplicate($requirementsText, $aboutTextPlain) || $isMostlyDuplicate($requirementsText, $responsibilitiesText)) {
            $requirementsText = '';
        }

        if ($isMostlyDuplicate($benefitsText, $aboutTextPlain) || $isMostlyDuplicate($benefitsText, $responsibilitiesText)) {
            $benefitsText = '';
        }

        if ($isHzzOfficial && ($isMostlyDuplicate($requirementsText, $aboutTextPlain) || $isMostlyDuplicate($requirementsText, $responsibilitiesText))) {
            $requirementsText = '';
        }

        $showHzzApplicationInstructions = ! $isHzzOfficial || (auth()->check() && auth()->user()?->isWorker());
        $hzzLogoPath = public_path('assets/branding/hzz-logo.svg');
    $hzzLogoUrl = file_exists($hzzLogoPath)
        ? asset('assets/branding/hzz-logo.svg')
        : (trim((string) ($job->source_logo_url ?? '')) ?: config('services.hzz.logo_url'));

        $workingConditionLines = $isHzzOfficial
            ? array_filter([
                $isMeaningfulValue($hzzAccommodation) ? __('ui.jobs_show.accommodation_label') . ': ' . $hzzAccommodation : null,
                $isMeaningfulValue($hzzTransport) ? __('ui.jobs_show.transport_label') . ': ' . $hzzTransport : null,
                $isMeaningfulValue($hzzFood) ? __('ui.jobs_show.food_label') . ': ' . $hzzFood : null,
                $isMeaningfulValue($hzzShiftMode) ? __('ui.jobs_show.work_mode_label') . ': ' . $hzzShiftMode : null,
                $isMeaningfulValue($hzzTerrain) ? __('ui.jobs_show.terrain_label') . ': ' . $hzzTerrain : null,
                $isMeaningfulValue($shiftDetailsText) ? __('ui.jobs_show.fact_shifts') . ': ' . $shiftDetailsText : null,
            ])
            : $mobilityDetails;

        $hzzRequirementFacts = array_filter([
            __('ui.jobs_show.fact_education_required') => $educationRequired,
            __('ui.jobs_show.requirement_experience') => $experienceLevel,
            __('ui.jobs_show.requirement_driving_licence') => $hzzDrivingLicence,
            __('ui.jobs_show.requirement_languages') => $hzzLanguages !== '' ? $hzzLanguages : $languageSummary,
            __('ui.jobs_show.requirement_computer_skills') => $hzzComputerSkills,
        ], fn ($value) => $isMeaningfulValue($value));

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

            @if($hzzSummaryText)
                <article class="cw-surface p-5 md:p-6 mb-6">
                    <p class="text-sm md:text-[15px] leading-relaxed text-slate-700">{{ $hzzSummaryText }}</p>
                </article>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                <div class="lg:col-span-2 space-y-4">
                    <article class="cw-surface p-6 md:p-7">
                        <h2 class="text-xl font-semibold text-slate-900 mb-3">{{ __('ui.jobs_show.key_facts') }}</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-sm">
                            @foreach($keyFacts as $label => $value)
                                @if(!is_null($value) && $isMeaningfulValue($value))
                                    <p class="text-slate-700 leading-snug"><strong>{{ $label }}:</strong> {{ $value }}</p>
                                @endif
                            @endforeach
                        </div>
                    </article>

                    @if($aboutText !== '')
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">{{ __('ui.jobs_show.about_this_job') }}</h2>
                            <div class="cw-rich-text text-slate-700">
                                @if($aboutTextHasHtml)
                                    {!! $renderRichEditorBlock($aboutText) !!}
                                @else
                                    {!! $renderStructuredBlock($aboutText) !!}
                                @endif
                            </div>
                        </article>
                    @endif

                    @if($responsibilitiesText !== '')
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">{{ __('ui.jobs_show.responsibilities') }}</h2>
                            <div class="cw-rich-text text-slate-700">{!! $renderStructuredBlock($responsibilitiesText) !!}</div>
                        </article>
                    @endif

                    @if($isHzzOfficial ? (count($hzzRequirementFacts) > 0 || $requirementsText !== '') : $requirementsText !== '')
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">{{ __('ui.jobs_show.requirements') }}</h2>
                            @if($isHzzOfficial && count($hzzRequirementFacts) > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 text-sm mb-4">
                                    @foreach($hzzRequirementFacts as $label => $value)
                                        <p class="text-slate-700 leading-snug"><strong>{{ $label }}:</strong> {{ $value }}</p>
                                    @endforeach
                                </div>
                            @endif
                            @if($requirementsText !== '')
                                <div class="cw-rich-text text-slate-700">{!! $renderStructuredBlock($requirementsText) !!}</div>
                            @endif
                        </article>
                    @endif

                    @if($benefitsText !== '')
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">{{ __('ui.jobs_show.benefits') }}</h2>
                            <div class="cw-rich-text text-slate-700">{!! $renderStructuredBlock($benefitsText) !!}</div>
                        </article>
                    @endif

                    @if(count($workingConditionLines) > 0)
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">{{ __('ui.jobs_show.working_conditions') }}</h2>
                            <ul class="space-y-2 text-slate-700 text-sm">
                                @foreach($workingConditionLines as $mobilityLine)
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

                    @if($hzzHasBottomContact)
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">{{ __('ui.jobs_show.contact_title') }}</h2>
                            <p class="text-sm text-slate-600 mb-4">{{ __('ui.jobs_show.contact_intro') }}</p>
                            <div class="space-y-3 text-sm text-slate-700">
                                @if($hzzContactEmail !== '')
                                    <p><strong>{{ __('ui.jobs_show.contact_email') }}:</strong> <a href="mailto:{{ $hzzContactEmail }}" class="font-medium underline underline-offset-2 decoration-slate-300 hover:text-slate-900">{{ $hzzContactEmail }}</a></p>
                                @endif
                                @if($hzzPhone !== '')
                                    <p><strong>{{ __('ui.jobs_show.contact_phone') }}:</strong> <a href="tel:{{ preg_replace('/[^0-9+]/', '', $hzzPhone) }}" class="hover:text-slate-900">{{ $hzzPhone }}</a></p>
                                @endif
                                @if($hzzContactPerson !== '')
                                    <p><strong>{{ __('ui.jobs_show.contact_person') }}:</strong> {{ $hzzContactPerson }}</p>
                                @endif
                                @if(filter_var($hzzApplyUrl, FILTER_VALIDATE_URL))
                                    <p><strong>{{ __('ui.jobs_show.contact_external_apply') }}:</strong> <a href="{{ $hzzApplyUrl }}" target="_blank" rel="noopener" class="font-medium underline underline-offset-2 decoration-slate-300 hover:text-slate-900">{{ __('ui.jobs_show.open_external_apply') }}</a></p>
                                @endif
                            </div>
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

                        @unless($isHzzOfficial)
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
                        @endunless

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

                        @if($isHzzOfficial && $hzzHasBottomContact)
                            <div class="mt-4 pt-4 border-t border-slate-200">
                                <h3 class="text-sm font-semibold text-slate-900 mb-3">{{ __('ui.jobs_show.contact_title') }}</h3>
                                <div class="space-y-2 text-sm text-slate-700">
                                    @if($hzzContactEmail !== '')
                                        <p><a href="mailto:{{ $hzzContactEmail }}" class="font-medium underline underline-offset-2 decoration-slate-300 hover:text-slate-900">{{ $hzzContactEmail }}</a></p>
                                    @endif
                                    @if($hzzPhone !== '')
                                        <p><a href="tel:{{ preg_replace('/[^0-9+]/', '', $hzzPhone) }}" class="hover:text-slate-900">{{ $hzzPhone }}</a></p>
                                    @endif
                                    @if($hzzContactPerson !== '')
                                        <p>{{ $hzzContactPerson }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>

                    @if(! $isHzzOfficial && ($aboutEmployerText !== '' || $job->employer))
                        <div class="cw-surface p-5" style="background: linear-gradient(145deg, {{ $brandColor }}2a 0%, {{ $brandColor }}1a 36%, rgba(255, 255, 255, 0.96) 100%); border: 1px solid color-mix(in srgb, {{ $brandColor }} 28%, var(--cw-hairline));">
                            @if($employerCoverUrl)
                                <div class="-mx-5 -mt-5 mb-4 aspect-[13/5] overflow-hidden rounded-t-[inherit] border-b border-slate-200">
                                    <img src="{{ $employerCoverUrl }}" alt="{{ $companyName }} cover" class="h-full w-full object-cover" loading="lazy" decoding="async">
                                </div>
                            @endif

                            <div class="flex flex-col gap-3 sm:flex-row sm:items-start">
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
                                    <div class="flex-shrink-0 self-start">
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

                    @if(! $isHzzOfficial)
                        <div class="cw-surface p-5">
                            <h3 class="text-base font-semibold text-slate-900 mb-2">{{ __('ui.jobs_show.report_question') }}</h3>
                            <a href="{{ route('reports.create', ['type' => 'job', 'id' => $job->id]) }}" class="cw-button-secondary w-full text-center">{{ __('ui.jobs_show.report_job') }}</a>
                        </div>
                    @endif
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
