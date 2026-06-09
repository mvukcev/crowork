<x-app-layout>
    <x-slot name="title">{{ $company->company_display_name ?: $company->company_name }}</x-slot>
    <x-slot name="description">{{ \Illuminate\Support\Str::limit(trim((string) ($company->description ?: (($company->company_display_name ?: $company->company_name) . ' ' . __('employer.public_company.seo_hiring_suffix')))), 155) }}</x-slot>
    <x-slot name="canonical">{{ route('companies.show', $company) }}</x-slot>

    @php
        $displayName = $company->company_display_name ?: $company->company_name;
        $locationParts = array_values(array_filter([$company->city, $company->country]));
        $locationText = count($locationParts) ? implode(', ', $locationParts) : __('employer.public_company.default_country');
        $logoUrl = $company->logo_path ? asset('storage/' . $company->logo_path) : null;
        $openJobsCount = $openJobs->count();
        $descriptionText = trim((string) ($company->description ?? ''));

        $organizationSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => $displayName,
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
                    'name' => __('navigation.home'),
                    'item' => route('home'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => $displayName,
                    'item' => route('companies.show', $company),
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
        <script type="application/ld+json">{!! json_encode($organizationSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @endpush

    <section class="cw-section">
        <div class="cw-container">
            <div class="mb-6 text-sm text-slate-500">
                <a href="{{ route('home') }}" class="hover:text-slate-900">{{ __('navigation.home') }}</a>
                <span class="mx-1">/</span>
                <span class="text-slate-700">{{ $displayName }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
                <div class="lg:col-span-2 space-y-6">
                    <article class="cw-surface p-6 md:p-8">
                        <div class="flex flex-col md:flex-row md:items-start gap-5">
                            <div class="h-20 w-20 rounded-2xl border border-slate-200 bg-slate-50 overflow-hidden grid place-items-center flex-shrink-0">
                                @if($logoUrl)
                                    <img src="{{ $logoUrl }}" alt="{{ $displayName }} logo" class="h-full w-full object-cover" onerror="this.onerror=null;this.src='{{ asset('assets/placeholders/shared/company-logo-placeholder-400x400.jpg') }}';">
                                @else
                                    <span class="text-2xl font-semibold text-slate-700">{{ \Illuminate\Support\Str::substr($displayName, 0, 1) }}</span>
                                @endif
                            </div>

                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-2 mb-2">
                                    <h1 class="cw-display text-4xl md:text-6xl">{{ $displayName }}</h1>
                                    @if($company->approved_at)
                                        <span class="cw-chip text-emerald-800 bg-emerald-50 border-emerald-200">{{ __('employer.public_company.verified') }}</span>
                                    @endif
                                </div>

                                <div class="flex flex-wrap gap-2 text-sm text-slate-600 mb-4">
                                    <span>{{ $locationText }}</span>
                                    @if($company->industry)
                                        <span>· {{ $company->industry }}</span>
                                    @endif
                                    <span>· {{ trans_choice('employer.public_company.open_jobs_count', $openJobsCount, ['count' => $openJobsCount]) }}</span>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @if($company->relocation_support)
                                        <span class="cw-chip text-blue-800 bg-blue-50 border-blue-200">{{ __('employer.public_company.relocation_support') }}</span>
                                    @endif
                                    @if($company->accommodation_support)
                                        <span class="cw-chip text-amber-800 bg-amber-50 border-amber-200">{{ __('employer.public_company.accommodation_support') }}</span>
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
                            <h2 class="text-xl font-semibold text-slate-900 mb-3">{{ __('employer.public_company.about_company', ['company' => $displayName]) }}</h2>
                            <div class="prose max-w-none text-slate-700 leading-relaxed whitespace-pre-line">{{ $descriptionText }}</div>
                        </article>
                    @endif

                    <article class="cw-surface p-6 md:p-7" id="company-open-jobs">
                        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                            <div>
                                <h2 class="text-xl font-semibold text-slate-900">{{ __('employer.public_company.open_jobs') }}</h2>
                                <p class="text-sm text-slate-600">{{ __('employer.public_company.open_jobs_intro', ['company' => $displayName]) }}</p>
                            </div>
                        </div>

                        @if($openJobsCount > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach($openJobs as $job)
                                    <x-job-card
                                        :title="$job->title"
                                        :href="route('jobs.show', $job)"
                                        :company="$displayName"
                                        :company_href="route('companies.show', $company)"
                                        :employer_logo_url="$logoUrl"
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
                                {{ __('employer.public_company.no_open_jobs') }}
                            </div>
                        @endif
                    </article>
                </div>

                <aside class="space-y-4 lg:sticky lg:top-24">
                    <div class="cw-surface p-5">
                        <h2 class="text-lg font-semibold text-slate-900 mb-3">{{ __('employer.public_company.profile_title') }}</h2>
                        <p class="text-sm text-slate-700 mb-2"><strong>{{ __('employer.public_company.location') }}:</strong> {{ $locationText }}</p>
                        @if($company->company_address)
                            <p class="text-sm text-slate-700 mb-2"><strong>{{ __('employer.public_company.address') }}:</strong> {{ $company->company_address }}</p>
                        @endif
                        @if($company->contact_email)
                            <p class="text-sm text-slate-700 mb-2"><strong>{{ __('employer.public_company.email') }}:</strong> {{ $company->contact_email }}</p>
                        @endif
                        @if($company->contact_phone)
                            <p class="text-sm text-slate-700 mb-2"><strong>{{ __('employer.public_company.phone') }}:</strong> {{ $company->contact_phone }}</p>
                        @endif
                        @if($company->industry)
                            <p class="text-sm text-slate-700 mb-2"><strong>{{ __('employer.public_company.industry') }}:</strong> {{ $company->industry }}</p>
                        @endif
                        <p class="text-sm text-slate-700 mb-2"><strong>{{ __('employer.public_company.status') }}:</strong> {{ $company->approved_at ? __('employer.public_company.verified_status') : __('employer.public_company.pending_status') }}</p>
                        <p class="text-sm text-slate-700 mb-2"><strong>{{ __('employer.public_company.open_jobs_label') }}:</strong> {{ $openJobsCount }}</p>
                        @if($company->relocation_support)
                            <p class="text-sm text-slate-700 mb-2"><strong>{{ __('employer.public_company.relocation_label') }}:</strong> {{ __('employer.public_company.support_available') }}</p>
                        @endif
                        @if($company->accommodation_support)
                            <p class="text-sm text-slate-700 mb-2"><strong>{{ __('employer.public_company.accommodation_label') }}:</strong> {{ __('employer.public_company.support_available') }}</p>
                        @endif

                        <div class="flex flex-col gap-2 mt-4">
                            @if($openJobsCount > 0)
                                <a href="#company-open-jobs" class="cw-button-primary w-full text-center" data-cw-track-click="navigation_click">{{ __('employer.public_company.view_open_jobs') }}</a>
                            @endif
                            @if($primaryJob)
                                <a href="{{ route('jobs.show', $primaryJob) }}" class="cw-button-secondary w-full text-center" data-cw-track-click="apply_start">{{ __('employer.public_company.apply_to_role') }}</a>
                            @endif
                            @if($company->website)
                                <a href="{{ $company->website }}" target="_blank" rel="noopener noreferrer" class="cw-button-secondary w-full text-center">{{ __('employer.public_company.visit_website') }}</a>
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