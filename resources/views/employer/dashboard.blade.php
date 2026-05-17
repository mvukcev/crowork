@extends('layouts.app')

@section('content')
<div class="cw-surface min-h-screen">
    <div class="cw-surface-header border-b border-neutral-200">
        <div class="cw-shell-spacing py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="cw-heading-1">{{ __('employer.dashboard.title') }}</h1>
                    <p class="text-neutral-600 mt-1">{{ __('employer.dashboard.welcome_back', ['name' => $employer->company_name ?? auth()->user()->name]) }}</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('home') }}" class="cw-button-secondary">{{ __('employer.dashboard.back_to_homepage') }}</a>
                    <a href="{{ route('employer.jobs.create') }}" class="cw-button-primary" data-cw-track-click="post_job_click" data-cw-item-type="cta">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        {{ __('employer.dashboard.create_job') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="cw-shell-spacing py-8">
        @if($activeJobs === 0 && $pendingJobs === 0 && $totalApplications === 0)
            <div class="mb-6 rounded-xl border border-blue-200 bg-blue-50 p-4">
                <p class="text-sm font-semibold text-blue-900">{{ __('employer.dashboard.first_time_setup_title') }}</p>
                <p class="text-sm text-blue-800 mt-1">{{ __('employer.dashboard.first_time_setup_body') }}</p>
                <div class="mt-3 flex flex-wrap gap-2">
                    <a href="{{ route('employer.settings.profile') }}" class="cw-button-secondary">{{ __('employer.dashboard.complete_company_profile') }}</a>
                    <a href="{{ route('employer.jobs.create') }}" class="cw-button-primary" data-cw-track-click="post_job_click" data-cw-item-type="cta">{{ __('employer.dashboard.create_first_job') }}</a>
                </div>
            </div>
        @endif

        @if($pendingJobs > 0)
            <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-amber-800">{{ __('employer.dashboard.pending_approval_notices') }}</p>
                        <p class="text-sm text-amber-700 mt-0.5">{{ trans_choice('employer.dashboard.pending_jobs_waiting', $pendingJobs, ['count' => $pendingJobs]) }}</p>
                    </div>
                    <a href="{{ route('employer.jobs.index') }}" class="cw-button-secondary">{{ __('employer.dashboard.open_job_listings') }}</a>
                </div>
                @if($pendingApprovalJobs->isNotEmpty())
                    <div class="mt-3 space-y-1.5">
                        @foreach($pendingApprovalJobs as $job)
                            <div class="text-sm text-amber-800">• {{ $job->title }} <span class="text-amber-600">({{ __('employer.dashboard.submitted_on', ['time' => $job->created_at?->diffForHumans()]) }})</span></div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <div class="mb-8">
            <h2 class="cw-heading-2 mb-4">{{ __('employer.dashboard.job_overview') }}</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="cw-card-shell p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-600">{{ __('employer.dashboard.active_jobs') }}</p>
                            <p class="cw-heading-2 mt-2">{{ $activeJobs }}</p>
                        </div>
                        <div class="p-3 bg-emerald-100 rounded-lg">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-neutral-500 mt-4">{{ __('employer.dashboard.active_jobs_hint') }}</p>
                </div>

                <div class="cw-card-shell p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-600">{{ __('employer.dashboard.pending_approval') }}</p>
                            <p class="cw-heading-2 mt-2">{{ $pendingJobs }}</p>
                        </div>
                        <div class="p-3 bg-amber-100 rounded-lg">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-neutral-500 mt-4">{{ __('employer.dashboard.pending_approval_hint') }}</p>
                </div>

                <div class="cw-card-shell p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-600">{{ __('employer.dashboard.expired') }}</p>
                            <p class="cw-heading-2 mt-2">{{ $expiredJobs }}</p>
                        </div>
                        <div class="p-3 bg-neutral-100 rounded-lg">
                            <svg class="w-6 h-6 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-neutral-500 mt-4">{{ __('employer.dashboard.expired_hint') }}</p>
                </div>
            </div>
        </div>

        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="cw-heading-2">{{ __('employer.dashboard.ats_pipeline') }}</h2>
                <a href="{{ route('employer.applications.pipeline') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    {{ __('employer.dashboard.view_full_pipeline') }}
                </a>
            </div>
            <div class="cw-card-shell p-4 mb-4">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm text-neutral-600">{{ __('employer.dashboard.total_applications') }}</p>
                    <p class="text-2xl font-semibold text-neutral-900">{{ $totalApplications }}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($pipelineBreakdown as $stage)
                        @php
                            $ratio = $totalApplications > 0 ? round(($stage['count'] / $totalApplications) * 100) : 0;
                        @endphp
                        <a href="{{ route('employer.applications.pipeline', ['status' => $stage['key']]) }}" class="block cw-card-shell-interactive p-3">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-neutral-800">{{ $stage['label'] }}</p>
                                <span class="text-sm font-semibold {{ $stage['color'] }}">{{ $stage['count'] }}</span>
                            </div>
                            <div class="cw-progress-track h-1.5 bg-neutral-100">
                                <div class="cw-progress-fill {{ $stage['bg'] }}" style="--cw-progress: {{ $ratio }}%;"></div>
                            </div>
                            <p class="text-xs text-neutral-500 mt-1">{{ __('employer.dashboard.pipeline_ratio', ['ratio' => $ratio]) }}</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-8">
            <article class="cw-card-shell p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="cw-heading-3">{{ __('employer.dashboard.candidate_cards') }}</h2>
                    <a href="{{ route('employer.applications.pipeline') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">{{ __('employer.dashboard.pipeline_link') }}</a>
                </div>

                @if($recentCandidates->isEmpty())
                    <div class="cw-empty-state p-4 border border-neutral-200">
                        <svg class="w-8 h-8 text-neutral-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.856-1.487M15 10a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        <p class="text-sm text-neutral-600">{!! __('employer.dashboard.no_applications_yet_hint', ['link' => '<a href="' . route('employer.jobs.create') . '" class="font-semibold text-blue-600 hover:underline">' . __('employer.dashboard.post_a_job') . '</a>']) !!}</p>
                    </div>
                @else
                    <div class="space-y-3">
                        @foreach($recentCandidates as $application)
                            <a href="{{ route('employer.applications.candidate', $application) }}" class="block cw-card-shell-interactive p-3">
                                <div class="flex items-center justify-between gap-2">
                                    <div>
                                        @php($candidateName = ($application->worker && ! $application->worker->pending_deletion)
                                            ? $application->worker->name
                                            : __('employer.dashboard.candidate_fallback'))
                                        <p class="text-sm font-semibold text-neutral-900">{{ $candidateName }}</p>
                                        <p class="text-xs text-neutral-500">{{ $application->job?->title ?? __('employer.dashboard.job_unavailable') }}</p>
                                    </div>
                                    @php($statusKey = 'employer.dashboard.pipeline.' . $application->status)
                                    @php($statusLabel = __($statusKey))
                                    <x-badge tone="info">{{ $statusLabel === $statusKey ? ucfirst((string) $application->status) : $statusLabel }}</x-badge>
                                </div>
                                <p class="text-xs text-neutral-500 mt-2">{{ __('employer.dashboard.applied_when', ['time' => $application->created_at?->diffForHumans()]) }}</p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </article>

            <article class="cw-card-shell p-5">
                <h2 class="cw-heading-3 mb-3">{{ __('employer.dashboard.expiring_jobs') }}</h2>
                @if($expiringJobs->isEmpty())
                    <div class="cw-empty-state p-4 border border-neutral-200">
                        <svg class="w-8 h-8 text-neutral-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm text-neutral-600">{{ __('employer.dashboard.no_jobs_expiring') }}</p>
                    </div>
                @else
                    <div class="space-y-2">
                        @foreach($expiringJobs as $job)
                            <a href="{{ route('employer.jobs.edit', $job) }}" class="block cw-card-shell-interactive p-3">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-semibold text-neutral-900">{{ $job->title }}</p>
                                    <span class="text-xs text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">{{ $job->expires_at?->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-neutral-500 mt-1">{{ __('employer.dashboard.expires_line', ['count' => $job->applications_count, 'date' => $job->expires_at?->format('M d, Y')]) }}</p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </article>
        </div>

        <div class="mb-8">
            <h2 class="cw-heading-2 mb-4">{{ __('employer.dashboard.job_performance_cards') }}</h2>
            @if($jobPerformance->isEmpty())
                <div class="cw-empty-state cw-surface border border-neutral-200 p-8">
                    <svg class="w-10 h-10 text-neutral-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    <h3 class="font-semibold text-neutral-900 mb-1">{{ __('employer.dashboard.no_performance_data') }}</h3>
                    <p class="text-sm text-neutral-600">{!! __('employer.dashboard.no_performance_data_hint', ['link' => '<a href="' . route('employer.jobs.create') . '" class="font-semibold text-blue-600 hover:underline">' . __('employer.dashboard.post_a_job') . '</a>']) !!}</p>
                </div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($jobPerformance as $job)
                        <article class="cw-card-shell p-4">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="font-semibold text-neutral-900">{{ $job->title }}</h3>
                                    <p class="text-xs text-neutral-500 mt-0.5">{{ __('employer.dashboard.published_line', ['date' => $job->published_at?->format('M d, Y') ?? __('employer.dashboard.not_available')]) }}</p>
                                </div>
                                <a href="{{ route('employer.jobs.edit', $job) }}" class="text-xs text-blue-600 hover:text-blue-700">{{ __('employer.dashboard.manage') }}</a>
                            </div>
                            <div class="grid grid-cols-3 gap-2 mt-3 text-center">
                                <div class="rounded-lg bg-slate-50 p-2">
                                    <p class="text-xs text-slate-500">{{ __('employer.dashboard.applications') }}</p>
                                    <p class="text-lg font-semibold text-slate-900">{{ $job->applications_count }}</p>
                                </div>
                                <div class="rounded-lg bg-violet-50 p-2">
                                    <p class="text-xs text-violet-500">{{ __('employer.dashboard.interview') }}</p>
                                    <p class="text-lg font-semibold text-violet-700">{{ $job->interview_applications_count }}</p>
                                </div>
                                <div class="rounded-lg bg-emerald-50 p-2">
                                    <p class="text-xs text-emerald-500">{{ __('employer.dashboard.hired') }}</p>
                                    <p class="text-lg font-semibold text-emerald-700">{{ $job->hired_applications_count }}</p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif
        </div>

        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="cw-heading-2">{{ __('employer.dashboard.your_active_listings') }}</h2>
                <a href="{{ route('employer.jobs.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    {{ __('employer.dashboard.manage_all') }}
                </a>
            </div>

            @if($jobs->count() > 0)
                <div class="space-y-3">
                    @foreach($jobs->take(5) as $job)
                        <div class="cw-card-shell p-4 hover:border-blue-300 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-semibold text-neutral-900">{{ $job->title }}</h3>
                                        @if($job->is_featured)
                                            <span class="cw-chip cw-chip-featured">{{ __('employer.dashboard.featured') }}</span>
                                        @endif
                                        @if($job->is_urgent)
                                            <span class="cw-chip cw-chip-urgent">{{ __('employer.dashboard.urgent') }}</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-neutral-600 mt-1">{{ $job->location_city }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-blue-600">{{ $job->applications_count }}</p>
                                    <p class="text-xs text-neutral-600">{{ __('employer.dashboard.applications') }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 mt-3 pt-3 border-t border-neutral-100">
                                <span class="text-xs text-neutral-600">
                                    {{ __('employer.dashboard.expires_short', ['date' => $job->expires_at?->format('M d, Y') ?? __('employer.dashboard.no_expiration')]) }}
                                </span>
                                @if($job->expires_at?->isFuture())
                                    <span class="cw-badge cw-badge-success">{{ __('common.active') }}</span>
                                @elseif($job->expires_at?->isPast())
                                    <span class="cw-badge cw-badge-neutral">{{ __('common.expired') }}</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="cw-card-shell p-8 text-center">
                    <p class="text-neutral-600">{{ __('employer.dashboard.no_active_listings') }}</p>
                    <div class="mt-4 flex flex-wrap items-center justify-center gap-2">
                        <a href="{{ route('employer.jobs.create') }}" class="cw-button-primary inline-flex">{{ __('employer.dashboard.create_your_first_job') }}</a>
                        <a href="{{ route('employer.settings.profile') }}" class="cw-button-secondary inline-flex">{{ __('employer.dashboard.complete_company_profile') }}</a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Company Profile Status -->
        @if($employer)
            <div class="mt-12 pt-8 border-t border-neutral-200">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="cw-heading-3">{{ __('employer.company_profile') }}</h2>
                    <a href="{{ route('employer.settings.profile') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        {{ __('employer.dashboard.complete_profile_link') }}
                    </a>
                </div>
                <div class="cw-card-shell p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-neutral-600">{{ __('employer.dashboard.profile_completeness') }}</p>
                            <p class="cw-heading-2 mt-1">{{ $employer->getProfileReadinessAttribute() }}%</p>
                        </div>
                        <div class="w-24 h-24">
                            <svg class="w-full h-full" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="45" fill="none" stroke="#e5e7eb" stroke-width="8"/>
                                <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="8" 
                                    class="text-emerald-600 cw-ring-rotate-top" 
                                    stroke-dasharray="{{ $employer->getProfileReadinessAttribute() * 2.83 }},283"
                                    stroke-linecap="round"
                                />
                                <text x="50" y="55" text-anchor="middle" font-size="20" font-weight="bold" fill="currentColor">
                                    {{ $employer->getProfileReadinessAttribute() }}%
                                </text>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
