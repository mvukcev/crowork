<x-app-layout>
    <x-slot name="title">{{ __('worker.dashboard.title') }}</x-slot>

    <section class="cw-section">
        <div class="cw-container">
            <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
                <div>
                    <p class="cw-kicker mb-1">{{ __('worker.dashboard.kicker') }}</p>
                    <h1 class="cw-display text-4xl md:text-6xl">{{ __('worker.dashboard.welcome', ['name' => $user->name]) }}</h1>
                    <p class="text-slate-600 mt-2">{{ __('worker.dashboard.intro') }}</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('home') }}" class="cw-button-secondary">{{ __('worker.dashboard.homepage') }}</a>
                    <a href="{{ route('worker.profile.edit') }}" class="cw-button-primary">{{ __('worker.dashboard.update_profile') }}</a>
                    <a href="{{ route('jobs.index') }}" class="cw-button-secondary">{{ __('worker.dashboard.browse_jobs') }}</a>
                </div>
            </div>

            @if($totalJobApplications === 0 && $totalEducationApplications === 0)
                <div class="cw-surface p-4 mb-6 border border-blue-200 bg-blue-50">
                    <p class="text-sm text-blue-900 font-semibold">{{ __('worker.dashboard.first_steps_title') }}</p>
                    <p class="text-sm text-blue-800 mt-1">{{ __('worker.dashboard.first_steps_body') }}</p>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
                <article class="cw-surface p-5 lg:col-span-2">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-lg font-semibold text-slate-900">{{ __('worker.dashboard.profile_completeness') }}</h2>
                        <span class="text-sm font-semibold text-slate-800">{{ $completeness }}%</span>
                    </div>
                    <div class="cw-progress-track mb-3">
                        <div class="cw-progress-fill bg-emerald-500" style="--cw-progress: {{ $completeness }}%;"></div>
                    </div>
                    <p class="text-sm font-medium text-slate-700 mb-1">{{ $completenessStateLabel ?? '' }}</p>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm text-slate-600">{{ $completenessHelperText ?? __('worker.dashboard.profile_hint_incomplete') }}</p>
                        <a href="{{ route('worker.profile.edit') }}" class="cw-button-secondary">
                            {{ $completeness < 80 ? __('worker.dashboard.finish_profile_now') : __('worker.dashboard.review_profile') }}
                        </a>
                    </div>
                </article>

                <article class="cw-surface p-5">
                    <h2 class="text-lg font-semibold text-slate-900 mb-3">{{ __('worker.dashboard.applications_overview') }}</h2>
                    <div class="space-y-2 text-sm text-slate-700">
                        <p><strong>{{ __('worker.dashboard.active_applications') }}:</strong> {{ $activeApplicationsCount }}</p>
                        <p><strong>{{ __('worker.dashboard.job_applications') }}:</strong> {{ $totalJobApplications }}</p>
                        <p><strong>{{ __('worker.dashboard.education_applications') }}:</strong> {{ $totalEducationApplications }}</p>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('worker.applications.index') }}" class="cw-button-secondary">{{ __('worker.dashboard.track_job_applications') }}</a>
                    </div>
                </article>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
                <article class="cw-surface p-5">
                    <h2 class="text-lg font-semibold text-slate-900 mb-3">{{ __('worker.dashboard.onboarding_checklist') }}</h2>
                    <div class="space-y-2">
                        @foreach($onboardingChecklist as $check)
                            <a href="{{ $check['href'] }}" class="flex items-center justify-between gap-3 p-3 rounded-xl border {{ $check['done'] ? 'border-emerald-200 bg-emerald-50/40' : 'border-slate-200 bg-white' }} hover:border-slate-300 transition">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full {{ $check['done'] ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500' }} text-xs font-semibold">
                                        {{ $check['done'] ? '✓' : '•' }}
                                    </span>
                                    <span class="text-sm {{ $check['done'] ? 'text-emerald-800 font-medium' : 'text-slate-700' }}">{{ $check['label'] }}</span>
                                </div>
                                <span class="text-xs text-slate-500">{{ __('worker.dashboard.open') }}</span>
                            </a>
                        @endforeach
                    </div>

                    @if(count($missingChecklist) > 0)
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mt-4 mb-2">{{ __('worker.dashboard.missing_profile_fields') }}</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach(array_slice($missingChecklist, 0, 10) as $item)
                                <span class="cw-chip max-w-full break-words">{{ $item }}</span>
                            @endforeach
                        </div>
                    @endif
                </article>

                <article class="cw-surface p-5">
                    <h2 class="text-lg font-semibold text-slate-900 mb-3">{{ __('worker.dashboard.recommended_next_actions') }}</h2>
                    <div class="space-y-3">
                        @foreach($recommendedNextActions as $action)
                            <div class="p-3 rounded-xl border border-slate-200">
                                <h3 class="text-sm font-semibold text-slate-900">{{ $action['title'] }}</h3>
                                <p class="text-sm text-slate-600 mt-1">{{ $action['description'] }}</p>
                                <a href="{{ $action['href'] }}" class="cw-button-secondary mt-3">{{ $action['label'] }}</a>
                            </div>
                        @endforeach
                    </div>
                </article>
            </div>

            <article class="cw-surface p-5 mb-6">
                <div class="flex items-center justify-between gap-3 mb-3">
                    <h2 class="text-lg font-semibold text-slate-900">{{ __('worker.dashboard.application_timeline') }}</h2>
                    <a href="{{ route('worker.applications.index') }}" class="text-sm text-slate-600 hover:text-slate-900">{{ __('worker.dashboard.open_full_history') }}</a>
                </div>

                @if($applicationTimeline->isEmpty())
                    <div class="cw-empty-state p-6">
                        <svg class="mx-auto h-8 w-8 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-slate-600">{{ __('worker.dashboard.no_timeline_events') }}</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($applicationTimeline as $event)
                            <a href="{{ $event['href'] }}" class="block p-3 rounded-xl border border-slate-200 hover:border-slate-300 transition">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $event['title'] }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $event['subtitle'] }}</p>
                                    </div>
                                    <span class="text-xs uppercase tracking-wide px-2 py-1 rounded-full {{ $event['type'] === 'job' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">{{ $event['type_label'] }}</span>
                                </div>
                                <div class="flex items-center justify-between mt-2">
                                    <x-badge tone="info">{{ $event['status'] }}</x-badge>
                                    <p class="text-xs text-slate-500">{{ optional($event['date'])->diffForHumans() }}</p>
                                </div>
                            </a>
                        @endforeach
                    </div>
                @endif
            </article>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
                <article class="cw-surface p-5">
                    <h2 class="text-lg font-semibold text-slate-900 mb-3">{{ __('worker.dashboard.latest_job_statuses') }}</h2>
                    @if($latestJobApplications->isEmpty())
                        <div class="p-6 text-center border border-slate-200 rounded-xl bg-gradient-to-br from-slate-50 to-white">
                            <svg class="mx-auto h-8 w-8 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            <p class="text-sm text-slate-600">{!! __('worker.dashboard.no_job_applications_hint', ['link' => '<a href="' . route('jobs.index') . '" class="font-semibold text-blue-600 hover:underline">' . __('worker.dashboard.browse_and_apply_jobs') . '</a>']) !!}</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($latestJobApplications as $application)
                                <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-2 last:border-0 last:pb-0">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $application->job?->title ?? __('worker.dashboard.job_unavailable') }}</p>
                                        <p class="text-xs text-slate-500">{{ $application->job?->employer?->company_name ?? __('worker.dashboard.employer_unavailable') }} · {{ __('worker.dashboard.applied_on', ['date' => $application->created_at?->format('M j, Y')]) }}</p>
                                    </div>
                                    @php($jobStatusKey = 'worker.dashboard.statuses.' . $application->status)
                                    @php($jobStatusLabel = __($jobStatusKey))
                                    <x-badge tone="info">{{ $jobStatusLabel === $jobStatusKey ? ucfirst((string) $application->status) : $jobStatusLabel }}</x-badge>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <a href="{{ route('worker.applications.index') }}" class="cw-button-secondary mt-4">{{ __('worker.dashboard.view_all_job_applications') }}</a>
                </article>

                <article class="cw-surface p-5">
                    <h2 class="text-lg font-semibold text-slate-900 mb-3">{{ __('worker.dashboard.education_applications_title') }}</h2>
                    @if($latestEducationApplications->isEmpty())
                        <div class="p-6 text-center border border-slate-200 rounded-xl bg-gradient-to-br from-slate-50 to-white">
                            <svg class="mx-auto h-8 w-8 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 1 10.334 1 15.25c0 4.915 5.5 9 11 9s11-4.085 11-9c0-4.916-5.5-8.997-11-9.25m0 13V6.253m9-4.5H3"></path>
                            </svg>
                            <p class="text-sm text-slate-600">{!! __('worker.dashboard.no_education_applications_hint', ['link' => '<a href="' . route('educations.index') . '" class="font-semibold text-blue-600 hover:underline">' . __('worker.dashboard.explore_education_programs') . '</a>']) !!}</p>
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($latestEducationApplications as $application)
                                <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-2 last:border-0 last:pb-0">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $application->education?->title ?? __('worker.dashboard.program_unavailable') }}</p>
                                        <p class="text-xs text-slate-500">{{ __('worker.dashboard.applied_on', ['date' => $application->created_at?->format('M j, Y')]) }}</p>
                                    </div>
                                    @php($educationStatus = (string) ($application->status ?: 'new'))
                                    @php($educationStatusKey = 'worker.dashboard.statuses.' . $educationStatus)
                                    @php($educationStatusLabel = __($educationStatusKey))
                                    <x-badge tone="info">{{ $educationStatusLabel === $educationStatusKey ? ucfirst($educationStatus) : $educationStatusLabel }}</x-badge>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <a href="{{ route('worker.education-applications.index') }}" class="cw-button-secondary mt-4">{{ __('worker.dashboard.view_all_education_applications') }}</a>
                </article>
            </div>

            <article class="cw-surface p-5">
                <h2 class="text-lg font-semibold text-slate-900 mb-3">{{ __('worker.dashboard.recommended_jobs') }}</h2>
                @if($recommendedJobs->isEmpty())
                    <div class="p-6 text-center border-2 border-dashed border-slate-200 rounded-xl bg-slate-50">
                        <svg class="mx-auto h-8 w-8 text-slate-400 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <p class="text-sm text-slate-600">{!! __('worker.dashboard.no_recommendations_hint', ['link' => '<a href="' . route('worker.profile.edit') . '" class="font-semibold text-blue-600 hover:underline">' . __('worker.dashboard.complete_profile_link') . '</a>']) !!}</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($recommendedJobs as $job)
                            <x-job-card
                                :href="route('jobs.show', $job)"
                                :title="$job->title"
                                :company="$job->employer?->company_name"
                                :company_href="$job->employer?->slug ? route('companies.show', $job->employer) : null"
                                :city="$job->location_city"
                                :salary_min="$job->salary_min"
                                :salary_max="$job->salary_max"
                                :salary_currency="$job->salary_currency"
                                :salary_period="$job->salary_period"
                                :category="$job->category"
                                :languages="$job->languages"
                                :employment_type="$job->contract_type"
                                :accommodation_provided="$job->accommodation_provided"
                                :visa_support="$job->visa_support"
                                :is_urgent="$job->is_urgent"
                                :is_featured="$job->is_featured"
                                :posted_at="$job->published_at"
                            />
                        @endforeach
                    </div>
                @endif
            </article>
        </div>
    </section>
</x-app-layout>
