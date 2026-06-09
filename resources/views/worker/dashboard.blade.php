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
                <nav class="cw-dashboard-tabbar" role="tablist" aria-label="{{ __('worker.dashboard.tabs.aria_label') }}">
                    <a href="{{ route('worker.dashboard', ['tab' => 'cv']) }}" role="tab" aria-selected="{{ $activeTab === 'cv' ? 'true' : 'false' }}" aria-current="{{ $activeTab === 'cv' ? 'page' : 'false' }}" class="cw-dashboard-tab">{{ __('worker.dashboard.tabs.cv') }}</a>
                    <a href="{{ route('worker.dashboard', ['tab' => 'applications']) }}" role="tab" aria-selected="{{ $activeTab === 'applications' ? 'true' : 'false' }}" aria-current="{{ $activeTab === 'applications' ? 'page' : 'false' }}" class="cw-dashboard-tab">{{ __('worker.dashboard.tabs.applications_overview') }}</a>
                    <a href="{{ route('worker.dashboard', ['tab' => 'settings']) }}" role="tab" aria-selected="{{ $activeTab === 'settings' ? 'true' : 'false' }}" aria-current="{{ $activeTab === 'settings' ? 'page' : 'false' }}" class="cw-dashboard-tab">{{ __('worker.dashboard.tabs.profile_settings') }}</a>
                </nav>
            </div>

            @if($activeTab === 'cv')
                @if($totalJobApplications === 0 && $totalEducationApplications === 0)
                    <div class="cw-surface p-4 mb-6 border border-blue-200 bg-blue-50">
                        <p class="text-sm text-blue-900 font-semibold">{{ __('worker.dashboard.first_steps_title') }}</p>
                        <p class="text-sm text-blue-800 mt-1">{{ __('worker.dashboard.first_steps_body') }}</p>
                    </div>
                @endif

                @php
                    $desiredRoles = collect((array) ($profile->desired_roles ?? []))->filter()->count();
                    $skillsCount = collect((array) ($profile->skills ?? []))->filter()->count();
                    $languagesCount = collect((array) ($profile->languages ?? []))->filter()->count();
                @endphp

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
                        <p class="text-sm text-slate-600">{{ $completenessHelperText ?? __('worker.dashboard.profile_hint_incomplete') }}</p>

                        @if(count($missingChecklist) > 0)
                            <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mt-4 mb-2">{{ __('worker.dashboard.missing_profile_fields') }}</p>
                            <div class="flex flex-wrap gap-2">
                                @foreach(array_slice($missingChecklist, 0, 12) as $item)
                                    <span class="cw-chip max-w-full break-words">{{ $item }}</span>
                                @endforeach
                            </div>
                        @endif

                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('worker.profile.edit') }}" class="cw-button-primary">{{ __('worker.dashboard.actions.edit_cv') }}</a>
                            <a href="{{ route('worker.profile.preview') }}" class="cw-button-secondary">{{ __('worker.dashboard.actions.preview_cv') }}</a>
                        </div>
                    </article>

                    <article class="cw-surface p-5 flex flex-col">
                        <h2 class="text-lg font-semibold text-slate-900 mb-3">{{ __('worker.dashboard.cv_summary_title') }}</h2>
                        <div class="space-y-2 text-sm text-slate-700">
                            <p><strong>{{ __('worker.dashboard.cv_summary.desired_city') }}:</strong> {{ $profile->desired_city ?: __('worker.dashboard.not_set') }}</p>
                            <p><strong>{{ __('worker.dashboard.cv_summary.desired_roles') }}:</strong> {{ $desiredRoles }}</p>
                            <p><strong>{{ __('worker.dashboard.cv_summary.skills') }}:</strong> {{ $skillsCount }}</p>
                            <p><strong>{{ __('worker.dashboard.cv_summary.languages') }}:</strong> {{ $languagesCount }}</p>
                        </div>
                        <div class="pt-4">
                            <a href="{{ route('worker.profile.edit') }}" class="cw-button-secondary">{{ __('worker.dashboard.actions.edit_cv') }}</a>
                        </div>
                    </article>
                </div>
            @endif

            @if($activeTab === 'applications')
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
                    <article class="cw-surface p-5 lg:col-span-2">
                        <h2 class="text-lg font-semibold text-slate-900 mb-3">{{ __('worker.dashboard.applications_overview') }}</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <p class="text-xs uppercase tracking-wide text-slate-500">{{ __('worker.dashboard.active_applications') }}</p>
                                <p class="text-xl font-semibold text-slate-900 mt-1">{{ $activeApplicationsCount }}</p>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <p class="text-xs uppercase tracking-wide text-slate-500">{{ __('worker.dashboard.job_applications') }}</p>
                                <p class="text-xl font-semibold text-slate-900 mt-1">{{ $totalJobApplications }}</p>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <p class="text-xs uppercase tracking-wide text-slate-500">{{ __('worker.dashboard.education_applications') }}</p>
                                <p class="text-xl font-semibold text-slate-900 mt-1">{{ $totalEducationApplications }}</p>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('worker.applications.index') }}" class="cw-button-primary">{{ __('worker.dashboard.actions.view_all_applications') }}</a>
                            <a href="{{ route('jobs.index') }}" class="cw-button-secondary">{{ __('worker.dashboard.actions.find_jobs') }}</a>
                            <a href="{{ route('educations.index') }}" class="cw-button-secondary">{{ __('worker.dashboard.actions.find_educations') }}</a>
                        </div>
                    </article>

                    <article class="cw-surface p-5">
                        <h2 class="text-lg font-semibold text-slate-900 mb-3">{{ __('worker.dashboard.application_timeline') }}</h2>
                        @if($applicationTimeline->isEmpty())
                            <p class="text-sm text-slate-600">{{ __('worker.dashboard.no_timeline_events') }}</p>
                        @else
                            <div class="space-y-2">
                                @foreach($applicationTimeline->take(4) as $event)
                                    <a href="{{ $event['href'] }}" class="block rounded-xl border border-slate-200 p-3 hover:border-slate-300 transition">
                                        <p class="text-sm font-semibold text-slate-900">{{ $event['title'] }}</p>
                                        <p class="text-xs text-slate-500">{{ $event['status'] }}</p>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </article>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
                    <article class="cw-surface p-5">
                        <h2 class="text-lg font-semibold text-slate-900 mb-3">{{ __('worker.dashboard.latest_job_statuses') }}</h2>
                        @if($latestJobApplications->isEmpty())
                            <p class="text-sm text-slate-600">{!! __('worker.dashboard.no_job_applications_hint', ['link' => '<a href="' . route('jobs.index') . '" class="font-semibold text-blue-600 hover:underline">' . __('worker.dashboard.browse_and_apply_jobs') . '</a>']) !!}</p>
                        @else
                            <div class="space-y-3">
                                @foreach($latestJobApplications as $application)
                                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-2 last:border-0 last:pb-0">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">{{ $application->job?->title ?? __('worker.dashboard.job_unavailable') }}</p>
                                            <p class="text-xs text-slate-500">{{ $application->job?->employer?->company_name ?? __('worker.dashboard.employer_unavailable') }}</p>
                                        </div>
                                        @php
                                            $jobStatusKey = 'worker.dashboard.statuses.' . $application->status;
                                            $jobStatusLabel = __($jobStatusKey);
                                        @endphp
                                        <x-badge tone="info">{{ $jobStatusLabel === $jobStatusKey ? ucfirst((string) $application->status) : $jobStatusLabel }}</x-badge>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </article>

                    <article class="cw-surface p-5">
                        <h2 class="text-lg font-semibold text-slate-900 mb-3">{{ __('worker.dashboard.education_applications_title') }}</h2>
                        @if($latestEducationApplications->isEmpty())
                            <p class="text-sm text-slate-600">{{ __('worker.dashboard.education_empty_plain') }}</p>
                        @else
                            <div class="space-y-3">
                                @foreach($latestEducationApplications as $application)
                                    <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-2 last:border-0 last:pb-0">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-900">{{ $application->education?->title ?? __('worker.dashboard.program_unavailable') }}</p>
                                            <p class="text-xs text-slate-500">{{ __('worker.dashboard.applied_on', ['date' => $application->created_at?->format('M j, Y')]) }}</p>
                                        </div>
                                        @php
                                            $educationStatus = (string) ($application->status ?: 'new');
                                            $educationStatusKey = 'worker.dashboard.statuses.' . $educationStatus;
                                            $educationStatusLabel = __($educationStatusKey);
                                        @endphp
                                        <x-badge tone="info">{{ $educationStatusLabel === $educationStatusKey ? ucfirst($educationStatus) : $educationStatusLabel }}</x-badge>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </article>
                </div>
            @endif

            @if($activeTab === 'settings')
                @php
                    $visibilityKey = 'worker_privacy.visibility_options.' . ($profile->profile_visibility ?: 'employers');
                    $visibilityLabel = __($visibilityKey);
                @endphp

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
                    <article class="cw-surface p-5 lg:col-span-2">
                        <h2 class="text-lg font-semibold text-slate-900 mb-3">{{ __('worker.dashboard.profile_settings_summary_title') }}</h2>
                        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <dt class="text-xs uppercase tracking-wide text-slate-500">{{ __('worker.profile_visibility') }}</dt>
                                <dd class="text-sm font-medium text-slate-900 mt-1">{{ $visibilityLabel === $visibilityKey ? ucfirst((string) $profile->profile_visibility) : $visibilityLabel }}</dd>
                            </div>
                            <div class="rounded-xl border border-slate-200 bg-slate-50 p-3">
                                <dt class="text-xs uppercase tracking-wide text-slate-500">{{ __('auth.email') }}</dt>
                                <dd class="text-sm font-medium text-slate-900 mt-1">{{ $user->email }}</dd>
                            </div>
                        </dl>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <a href="{{ route('worker.settings.edit') }}" class="cw-button-primary">{{ __('worker.dashboard.actions.edit_settings') }}</a>
                            <a href="{{ route('worker.privacy.show') }}" class="cw-button-secondary">{{ __('worker.dashboard.actions.privacy_data') }}</a>
                            <a href="{{ route('notifications.preferences') }}" class="cw-button-secondary">{{ __('worker.dashboard.actions.notification_preferences') }}</a>
                        </div>
                    </article>

                    <article class="cw-surface p-5">
                        <h2 class="text-lg font-semibold text-slate-900 mb-3">{{ __('worker.dashboard.settings_links_title') }}</h2>
                        <div class="space-y-2">
                            <a href="{{ route('worker.settings.edit') }}" class="block rounded-xl border border-slate-200 p-3 hover:border-slate-300 transition text-sm font-medium text-slate-800">{{ __('worker.dashboard.actions.edit_settings') }}</a>
                            <a href="{{ route('worker.privacy.show') }}" class="block rounded-xl border border-slate-200 p-3 hover:border-slate-300 transition text-sm font-medium text-slate-800">{{ __('worker.dashboard.actions.privacy_data') }}</a>
                            <a href="{{ route('notifications.preferences') }}" class="block rounded-xl border border-slate-200 p-3 hover:border-slate-300 transition text-sm font-medium text-slate-800">{{ __('worker.dashboard.actions.notification_preferences') }}</a>
                        </div>
                    </article>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
