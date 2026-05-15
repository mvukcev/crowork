<x-app-layout>
    <x-slot name="title">{{ $company->company_name }}</x-slot>
    <x-slot name="description">{{ \Illuminate\Support\Str::limit(trim((string) ($company->description ?: ($company->company_name . ' is hiring on CroWork.'))), 155) }}</x-slot>
    <x-slot name="canonical">{{ route('companies.show', $company) }}</x-slot>

    @php
        $locationParts = array_values(array_filter([$company->city, $company->country]));
        $locationText = count($locationParts) ? implode(', ', $locationParts) : 'Croatia';
        $logoUrl = $company->logo_path ? asset('storage/' . $company->logo_path) : null;
        $openJobsCount = $openJobs->count();
        $descriptionText = trim((string) ($company->description ?? ''));

        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $company->company_name,
            'url' => route('companies.show', $company),
            'logo' => $logoUrl,
            'description' => $descriptionText !== '' ? $descriptionText : null,
            'address' => array_filter([
                '@type' => 'PostalAddress',
                'addressLocality' => $company->city,
                'addressCountry' => $company->country,
            ], fn ($value) => !is_null($value) && $value !== ''),
            'industry' => $company->industry,
        ];

        if (isset($organizationSchema['address']) && count($organizationSchema['address']) <= 1) {
            $organizationSchema['address'] = null;
        }

        $organizationSchema = array_filter($organizationSchema, fn ($value) => !is_null($value) && $value !== '');
    @endphp

    @push('head')
        <script type="application/ld+json">{!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Home',
                    'item' => route('home'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $company->company_name,
                    'item' => route('companies.show', $company),
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        <script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <section class="cw-section">
        <div class="cw-container">
            <div class="mb-6 text-sm text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-slate-900">Home</a>
                <span class="mx-1">/</span>
                <span class="text-slate-700">{{ $company->company_name }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                <div class="lg:col-span-2 space-y-6">
                    <article class="cw-surface p-6 md:p-8">
                        <div class="flex flex-col md:flex-row md:items-start gap-5">
                            <div class="h-20 w-20 rounded-2xl border border-slate-200 bg-slate-50 overflow-hidden grid place-items-center flex-shrink-0">
                                @if($logoUrl)
                                    <img src="{{ $logoUrl }}" alt="{{ $company->company_name }} logo" class="h-full w-full object-cover">
                                @else
                                    <span class="text-2xl font-semibold text-slate-700">{{ \Illuminate\Support\Str::substr($company->company_name, 0, 1) }}</span>
                                @endif
                            </div>

                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <h1 class="cw-display text-4xl md:text-6xl">{{ $company->company_name }}</h1>
                                    @if($company->approved_at)
                                        <span class="cw-chip text-emerald-800 bg-emerald-50 border-emerald-200">Verified</span>
                                    @endif
                                </div>

                                <div class="flex flex-wrap gap-2 text-sm text-slate-600 mb-4">
                                    <span>{{ $locationText }}</span>
                                    @if($company->industry)
                                        <span>· {{ $company->industry }}</span>
                                    @endif
                                    <span>· {{ $openJobsCount }} {{ \Illuminate\Support\Str::plural('open job', $openJobsCount) }}</span>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @if($company->relocation_support)
                                        <span class="cw-chip text-blue-800 bg-blue-50 border-blue-200">Relocation support</span>
                                    @endif
                                    @if($company->accommodation_support)
                                        <span class="cw-chip text-amber-800 bg-amber-50 border-amber-200">Accommodation support</span>
                                    @endif
                                    @if($company->industry)
                                        <span class="cw-chip">{{ $company->industry }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </article>

                    @if($descriptionText !== '')
                        <article class="cw-surface p-6 md:p-7">
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">About {{ $company->company_name }}</h2>
                            <div class="prose max-w-none text-slate-700 leading-relaxed whitespace-pre-line">{{ $descriptionText }}</div>
                        </article>
                    @endif

                    <article class="cw-surface p-6 md:p-7" id="company-open-jobs">
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-900">Open jobs</h2>
                                <p class="text-sm text-slate-600">Current roles from {{ $company->company_name }} on CroWork.</p>
                            </div>
                        </div>

                        @if($openJobsCount > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($openJobs as $job)
                                    <x-job-card
                                        :title="$job->title"
                                        :href="route('jobs.show', $job)"
                                        :company="$company->company_name"
                                        :company_href="route('companies.show', $company)"
                                        :city="$job->location_city"
                                        :salary_min="$job->salary_min"
                                        :salary_max="$job->salary_max"
                                        :salary_currency="$job->salary_currency ?? 'EUR'"
                                        :salary_period="$job->salary_period ?? 'month'"
                                        :employment_type="$job->contract_type"
                                        :experience_level="$job->experience_level"
                                        :education_required="$job->education_required"
                                        :positions_available="$job->positions_available"
                                        :working_hours="$job->working_hours"
                                        :start_date="$job->start_date"
                                        :start_flexibility="$job->start_flexibility"
                                        :accommodation_provided="$job->accommodation_provided"
                                        :visa_support="$job->visa_support"
                                        :is_urgent="$job->is_urgent"
                                        :is_featured="$job->is_featured"
                                        :languages="$job->languages ?? []"
                                        :posted_at="$job->published_at ?? $job->created_at"
                                    />
                                @endforeach
                            </div>
                        @else
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-6 text-sm text-slate-600">
                                No open jobs are published for this company right now.
                            </div>
                        @endif
                    </article>
                </div>

                <aside class="space-y-4 lg:sticky lg:top-24">
                    <div class="cw-surface p-5">
                        <h2 class="text-lg font-semibold text-slate-900 mb-3">Company profile</h2>
                        <p class="text-sm text-slate-700 mb-2"><strong>Location:</strong> {{ $locationText }}</p>
                        @if($company->industry)
                            <p class="text-sm text-slate-700 mb-2"><strong>Industry:</strong> {{ $company->industry }}</p>
                        @endif
                        <p class="text-sm text-slate-700 mb-2"><strong>Status:</strong> {{ $company->approved_at ? 'Verified on CroWork' : 'Pending verification' }}</p>
                        <p class="text-sm text-slate-700 mb-2"><strong>Open jobs:</strong> {{ $openJobsCount }}</p>
                        @if($company->relocation_support)
                            <p class="text-sm text-slate-700 mb-2"><strong>Relocation:</strong> Support available</p>
                        @endif
                        @if($company->accommodation_support)
                            <p class="text-sm text-slate-700 mb-2"><strong>Accommodation:</strong> Support available</p>
                        @endif

                        <div class="flex flex-col gap-2 mt-4">
                            @if($openJobsCount > 0)
                                <a href="#company-open-jobs" class="cw-button-primary w-full text-center" data-cw-track-click="navigation_click">View open jobs</a>
                            @endif
                            @if($primaryJob)
                                <a href="{{ route('jobs.show', $primaryJob) }}" class="cw-button-secondary w-full text-center" data-cw-track-click="apply_start">Apply to a role</a>
                            @endif
                            @if($company->website)
                                <a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer" class="cw-button-secondary w-full text-center">Visit website</a>
                            @endif
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </section>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                window.cwTrack?.('company_view', {
                    company_slug: @json($company->slug),
                    open_jobs: {{ $openJobsCount }}
                });
            });
        </script>
    @endpush
</x-app-layout>