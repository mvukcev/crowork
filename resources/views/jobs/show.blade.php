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

        $aboutText = trim((string) ($contentSections['about'] ?? $job->description));
        $responsibilitiesText = trim((string) ($contentSections['responsibilities'] ?? ''));
        $requirementsText = trim((string) ($contentSections['requirements'] ?? ''));
        $benefitsText = trim((string) ($contentSections['benefits'] ?? ''));
        $applicationInstructionsText = trim((string) ($contentSections['application_instructions'] ?? ''));
        $relocationText = trim((string) ($contentSections['relocation'] ?? $job->accommodation_details ?? ''));
        $aboutEmployerText = trim((string) ($contentSections['about_employer'] ?? ''));
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
                <p class="cw-kicker mb-2">Job detail</p>
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
                        <span class="cw-chip text-amber-800 bg-amber-50 border-amber-200">Accommodation provided</span>
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
                    @if($aboutText !== '')
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">About this job</h2>
                            <div class="prose max-w-none text-slate-700 leading-relaxed">{!! nl2br(e($aboutText)) !!}</div>
                        </article>
                    @endif

                    @if($responsibilitiesText !== '')
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">Responsibilities</h2>
                            <div class="prose max-w-none text-slate-700 leading-relaxed">{!! nl2br(e($responsibilitiesText)) !!}</div>
                        </article>
                    @endif

                    @if($requirementsText !== '')
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">Requirements</h2>
                            <div class="prose max-w-none text-slate-700 leading-relaxed">{!! nl2br(e($requirementsText)) !!}</div>
                        </article>
                    @endif

                    @if($benefitsText !== '')
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">Benefits</h2>
                            <div class="prose max-w-none text-slate-700 leading-relaxed">{!! nl2br(e($benefitsText)) !!}</div>
                        </article>
                    @endif

                    @if($applicationInstructionsText !== '')
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">Application instructions</h2>
                            <div class="prose max-w-none text-slate-700 leading-relaxed">{!! nl2br(e($applicationInstructionsText)) !!}</div>
                        </article>
                    @endif

                    @if($relocationText !== '' || $job->accommodation_provided)
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">Relocation, accommodation, and visa support</h2>
                            @if($job->accommodation_provided)
                                <p class="text-slate-700 mb-3">Accommodation is provided for this role.</p>
                            @endif
                            @if($relocationText !== '')
                                <div class="prose max-w-none text-slate-700 leading-relaxed">{!! nl2br(e($relocationText)) !!}</div>
                            @endif
                        </article>
                    @endif

                    @if($aboutEmployerText !== '' || $job->employer)
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">About the employer</h2>
                            @if($aboutEmployerText !== '')
                                <div class="prose max-w-none text-slate-700 leading-relaxed mb-3">{!! nl2br(e($aboutEmployerText)) !!}</div>
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
                        @if(!empty($job->accommodation_details))
                            <p class="text-sm text-slate-700 mb-2"><strong>Relocation details:</strong> {{ $job->accommodation_details }}</p>
                        @endif
                        @if($expiryDate)
                            <p class="text-sm text-slate-700 mb-2"><strong>Apply before:</strong> {{ $expiryDate }}</p>
                        @endif

                        <a href="{{ route('jobs.apply', $job) }}" class="cw-button-primary w-full mt-3">Apply now</a>
                    </div>

                    <div class="cw-surface p-5">
                        <h3 class="text-base font-semibold text-slate-900 mb-2">Safe and secure applications</h3>
                        <p class="text-sm text-slate-600 mb-0">Never pay upfront fees for interviews, visas, or job placement. Report suspicious listings so our moderation team can review them quickly.</p>
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
