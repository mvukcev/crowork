<x-app-layout>
    <x-slot name="title">{{ $job->title }}</x-slot>
    <x-slot name="description">{{ \Illuminate\Support\Str::limit(strip_tags($job->description), 150) }}</x-slot>

    @php
        $companyName = $job->employer?->company_name ?? 'Employer';
        $location = $job->location_city ?: null;
        $category = $job->category ?: null;

        $salaryDisplay = null;
        if (!is_null($job->salary_min) || !is_null($job->salary_max)) {
            $salaryDisplay = $job->formatted_salary;
        }

        $employmentType = $job->contract_type
            ? \Illuminate\Support\Str::headline(str_replace(['-', '_'], ' ', $job->contract_type))
            : null;

        $experienceLevel = $job->experience_level
            ? \Illuminate\Support\Str::headline(str_replace(['-', '_'], ' ', $job->experience_level))
            : null;

        $languageValues = [];
        if (is_array($job->languages)) {
            $languageValues = $job->languages;
        } elseif (is_string($job->languages) && trim($job->languages) !== '') {
            $decodedLanguages = json_decode($job->languages, true);
            $languageValues = is_array($decodedLanguages) ? $decodedLanguages : preg_split('/[\n,]+/', $job->languages);
        }

        $languageValues = array_values(array_filter(array_map(fn ($lang) => trim((string) $lang), $languageValues)));
        $languageSummary = count($languageValues) ? implode(', ', $languageValues) : null;

        $publishedDate = $job->published_at?->format('M j, Y') ?? $job->created_at?->format('M j, Y');
        $postedAgo = $job->published_at?->diffForHumans() ?? $job->created_at?->diffForHumans();
        $expiryDate = $job->expires_at?->format('M j, Y');

        $aboutText = trim((string) ($job->description ?? ''));
        $responsibilitiesText = trim((string) ($job->responsibilities ?? ''));
        $requirementsText = trim((string) ($job->requirements ?? ''));
        $benefitsText = trim((string) ($job->benefits ?? ''));
        $applicationInstructionsText = trim((string) ($job->application_instructions ?? ''));

        $mobilityDetails = array_values(array_filter([
            $job->accommodation_provided ? 'Accommodation is provided by employer.' : null,
            !empty($job->accommodation_details) ? trim((string) $job->accommodation_details) : null,
            $job->visa_support ? 'Visa/work permit support is available.' : null,
            !empty($job->visa_support_details) ? trim((string) $job->visa_support_details) : null,
        ]));

        $keyFacts = [
            'Employment type' => $employmentType,
            'Category' => $category,
            'City' => $location,
            'Experience level' => $experienceLevel,
            'Education required' => $job->education_required,
            'Contract duration' => $job->contract_duration,
            'Start date' => $job->start_date?->format('M j, Y'),
            'Start flexibility' => $job->start_flexibility,
            'Open positions' => $job->positions_available,
            'Working hours' => $job->working_hours,
            'Shifts' => $job->shift_details,
            'Languages' => $languageSummary,
            'Salary' => $salaryDisplay,
            'Apply before' => $expiryDate,
        ];

        $aboutEmployerText = trim((string) ($job->employer?->description ?? ''));
    @endphp

    <section class="cw-section">
        <div class="cw-container">
            <div class="mb-6 text-sm text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-slate-900">Home</a>
                <span class="mx-1">/</span>
                <a href="{{ route('jobs.index') }}" class="hover:text-slate-900">Jobs</a>
                <span class="mx-1">/</span>
                <span class="text-slate-700">{{ $job->title }}</span>
            </div>

            <article class="cw-surface p-6 md:p-8 mb-6">
                <p class="cw-kicker mb-2">Job overview</p>
                <h1 class="cw-display text-4xl md:text-6xl mb-3">{{ $job->title }}</h1>
                <p class="text-base text-slate-600 mb-4">
                    {{ $companyName }}
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
                    @if($languageSummary)
                        <span class="cw-chip">Language: {{ $languageSummary }}</span>
                    @endif
                    @if($job->accommodation_provided)
                        <span class="cw-chip text-amber-800 bg-amber-50 border-amber-200">Accommodation</span>
                    @endif
                    @if($job->visa_support)
                        <span class="cw-chip text-emerald-800 bg-emerald-50 border-emerald-200">Visa support</span>
                    @endif
                    @if($job->is_urgent)
                        <span class="cw-chip text-red-800 bg-red-50 border-red-200">Urgent</span>
                    @endif
                    @if($job->is_featured)
                        <span class="cw-chip text-indigo-800 bg-indigo-50 border-indigo-200">Featured</span>
                    @endif
                    @if($salaryDisplay)
                        <span class="cw-chip">{{ $salaryDisplay }}</span>
                    @endif
                </div>

                <div class="mt-4 flex flex-wrap gap-5 text-sm text-slate-500">
                    <p>Published {{ $publishedDate }} @if($postedAgo) ({{ $postedAgo }}) @endif</p>
                    @if($expiryDate)
                        <p>Expires {{ $expiryDate }}</p>
                    @endif
                </div>
            </article>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                <div class="lg:col-span-2 space-y-4">
                    <article class="cw-surface p-6 md:p-7">
                        <h2 class="text-xl font-semibold text-slate-900 mb-3">Key facts</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
                            @foreach($keyFacts as $label => $value)
                                @if(!is_null($value) && trim((string) $value) !== '')
                                    <p class="text-slate-700"><strong>{{ $label }}:</strong> {{ $value }}</p>
                                @endif
                            @endforeach
                        </div>
                    </article>

                    @if($aboutText !== '')
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">About this job</h2>
                            <div class="prose max-w-none text-slate-700 leading-relaxed">{!! $aboutText !!}</div>
                        </article>
                    @endif

                    @if($responsibilitiesText !== '')
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">Responsibilities</h2>
                            <div class="prose max-w-none text-slate-700 leading-relaxed whitespace-pre-line">{{ $responsibilitiesText }}</div>
                        </article>
                    @endif

                    @if($requirementsText !== '')
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">Requirements</h2>
                            <div class="prose max-w-none text-slate-700 leading-relaxed whitespace-pre-line">{{ $requirementsText }}</div>
                        </article>
                    @endif

                    @if($benefitsText !== '')
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">Benefits</h2>
                            <div class="prose max-w-none text-slate-700 leading-relaxed whitespace-pre-line">{{ $benefitsText }}</div>
                        </article>
                    @endif

                    @if(count($mobilityDetails) > 0)
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">Relocation, accommodation, and visa support</h2>
                            <ul class="space-y-2 text-slate-700 text-sm">
                                @foreach($mobilityDetails as $mobilityLine)
                                    <li>{{ $mobilityLine }}</li>
                                @endforeach
                            </ul>
                        </article>
                    @endif

                    @if($applicationInstructionsText !== '')
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">Application instructions</h2>
                            <div class="prose max-w-none text-slate-700 leading-relaxed whitespace-pre-line">{{ $applicationInstructionsText }}</div>
                        </article>
                    @endif

                    @if($aboutEmployerText !== '' || $job->employer)
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">About the employer</h2>
                            @if($aboutEmployerText !== '')
                                <div class="prose max-w-none text-slate-700 leading-relaxed mb-3 whitespace-pre-line">{{ $aboutEmployerText }}</div>
                            @endif
                            @if($job->employer)
                                <p class="text-slate-700">{{ $companyName }}@if($job->employer->city) · {{ $job->employer->city }}@endif</p>
                            @endif
                        </article>
                    @endif

                    @if(($similarJobs ?? collect())->count() > 0)
                        <section class="cw-section !pt-2">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">Similar jobs</h2>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($similarJobs as $similarJob)
                                    <x-job-card
                                        :title="$similarJob->title"
                                        :company="$similarJob->employer?->company_name"
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
                        <h2 class="text-lg font-semibold text-slate-900 mb-3">Apply to this role</h2>

                        @if($salaryDisplay)
                            <p class="text-sm text-slate-700 mb-2"><strong>Salary:</strong> {{ $salaryDisplay }}</p>
                        @endif
                        @if($location)
                            <p class="text-sm text-slate-700 mb-2"><strong>Location:</strong> {{ $location }}</p>
                        @endif
                        @if($employmentType)
                            <p class="text-sm text-slate-700 mb-2"><strong>Employment type:</strong> {{ $employmentType }}</p>
                        @endif
                        @if($languageSummary)
                            <p class="text-sm text-slate-700 mb-2"><strong>Language:</strong> {{ $languageSummary }}</p>
                        @endif
                        @if($job->accommodation_provided)
                            <p class="text-sm text-slate-700 mb-2"><strong>Accommodation:</strong> Provided</p>
                        @endif
                        @if($job->visa_support)
                            <p class="text-sm text-slate-700 mb-2"><strong>Visa/work permit:</strong> Supported</p>
                        @endif
                        @if($expiryDate)
                            <p class="text-sm text-slate-700 mb-2"><strong>Apply before:</strong> {{ $expiryDate }}</p>
                        @endif

                        <a href="{{ route('jobs.apply', $job) }}" class="cw-button-primary w-full mt-3">Apply now</a>
                    </div>

                    <div class="cw-surface p-5">
                        <h3 class="text-base font-semibold text-slate-900 mb-2">Need to report this job?</h3>
                        <a href="{{ route('reports.create', ['type' => 'job', 'id' => $job->id]) }}" class="cw-button-secondary w-full text-center">Report job</a>
                    </div>
                </aside>
            </div>
        </div>
    </section>
</x-app-layout>
