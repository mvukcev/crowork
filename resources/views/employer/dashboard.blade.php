@extends('layouts.app')

@section('content')
<div class="cw-surface min-h-screen">
    <div class="cw-surface-header border-b border-neutral-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="cw-heading-1">ATS Dashboard</h1>
                    <p class="text-neutral-600 mt-1">Welcome back, {{ $employer->company_name ?? auth()->user()->name }}</p>
                </div>
                <a href="{{ route('employer.jobs.create') }}" class="cw-button-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Job
                </a>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if($pendingJobs > 0)
            <div class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-sm font-semibold text-amber-800">Pending approval notices</p>
                        <p class="text-sm text-amber-700 mt-0.5">{{ $pendingJobs }} job listing(s) are awaiting admin review.</p>
                    </div>
                    <a href="{{ route('employer.jobs.index') }}" class="cw-button-secondary">Open job listings</a>
                </div>
                @if($pendingApprovalJobs->isNotEmpty())
                    <div class="mt-3 space-y-1.5">
                        @foreach($pendingApprovalJobs as $job)
                            <div class="text-sm text-amber-800">• {{ $job->title }} <span class="text-amber-600">(submitted {{ $job->created_at?->diffForHumans() }})</span></div>
                        @endforeach
                    </div>
                @endif
            </div>
        @endif

        <div class="mb-8">
            <h2 class="cw-heading-2 mb-4">Job overview</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="cw-surface border border-neutral-200 rounded-lg p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-600">Active Jobs</p>
                            <p class="cw-heading-2 mt-2">{{ $activeJobs }}</p>
                        </div>
                        <div class="p-3 bg-emerald-100 rounded-lg">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-neutral-500 mt-4">Currently published and accepting applications</p>
                </div>

                <div class="cw-surface border border-neutral-200 rounded-lg p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-600">Pending Approval</p>
                            <p class="cw-heading-2 mt-2">{{ $pendingJobs }}</p>
                        </div>
                        <div class="p-3 bg-amber-100 rounded-lg">
                            <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-neutral-500 mt-4">Awaiting admin review</p>
                </div>

                <div class="cw-surface border border-neutral-200 rounded-lg p-6">
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-sm font-medium text-neutral-600">Expired</p>
                            <p class="cw-heading-2 mt-2">{{ $expiredJobs }}</p>
                        </div>
                        <div class="p-3 bg-neutral-100 rounded-lg">
                            <svg class="w-6 h-6 text-neutral-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l-2-2m0 0l-2-2m2 2l2-2m-2 2l-2 2"/>
                            </svg>
                        </div>
                    </div>
                    <p class="text-xs text-neutral-500 mt-4">Past expiration date</p>
                </div>
            </div>
        </div>

        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="cw-heading-2">ATS pipeline</h2>
                <a href="{{ route('employer.applications.pipeline') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    View Full Pipeline →
                </a>
            </div>
            <div class="cw-surface border border-neutral-200 rounded-lg p-4 mb-4">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-sm text-neutral-600">Total applications</p>
                    <p class="text-2xl font-semibold text-neutral-900">{{ $totalApplications }}</p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                    @foreach($pipelineBreakdown as $stage)
                        @php
                            $ratio = $totalApplications > 0 ? round(($stage['count'] / $totalApplications) * 100) : 0;
                        @endphp
                        <a href="{{ route('employer.applications.pipeline', ['status' => $stage['key']]) }}" class="block rounded-lg border border-neutral-200 p-3 hover:border-neutral-300 transition">
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-neutral-800">{{ $stage['label'] }}</p>
                                <span class="text-sm font-semibold {{ $stage['color'] }}">{{ $stage['count'] }}</span>
                            </div>
                            <div class="h-1.5 rounded-full bg-neutral-100 overflow-hidden">
                                <div class="h-full {{ $stage['bg'] }}" style="width: {{ $ratio }}%"></div>
                            </div>
                            <p class="text-xs text-neutral-500 mt-1">{{ $ratio }}% of pipeline</p>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-8">
            <article class="cw-surface border border-neutral-200 rounded-lg p-5">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="cw-heading-3">Candidate cards</h2>
                    <a href="{{ route('employer.applications.pipeline') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">Pipeline →</a>
                </div>

                @if($recentCandidates->isEmpty())
                    <p class="text-sm text-neutral-600">No candidates yet.</p>
                @else
                    <div class="space-y-3">
                        @foreach($recentCandidates as $application)
                            <a href="{{ route('employer.applications.candidate', $application) }}" class="block rounded-lg border border-neutral-200 p-3 hover:border-neutral-300 transition">
                                <div class="flex items-center justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-semibold text-neutral-900">{{ $application->worker?->name ?? 'Candidate' }}</p>
                                        <p class="text-xs text-neutral-500">{{ $application->job?->title ?? 'Job unavailable' }}</p>
                                    </div>
                                    <x-badge tone="info">{{ ucfirst($application->status) }}</x-badge>
                                </div>
                                <p class="text-xs text-neutral-500 mt-2">Applied {{ $application->created_at?->diffForHumans() }}</p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </article>

            <article class="cw-surface border border-neutral-200 rounded-lg p-5">
                <h2 class="cw-heading-3 mb-3">Expiring jobs</h2>
                @if($expiringJobs->isEmpty())
                    <p class="text-sm text-neutral-600">No jobs expiring in the next 14 days.</p>
                @else
                    <div class="space-y-2">
                        @foreach($expiringJobs as $job)
                            <a href="{{ route('employer.jobs.edit', $job) }}" class="block rounded-lg border border-neutral-200 p-3 hover:border-neutral-300 transition">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-semibold text-neutral-900">{{ $job->title }}</p>
                                    <span class="text-xs text-amber-700 bg-amber-100 px-2 py-0.5 rounded-full">{{ $job->expires_at?->diffForHumans() }}</span>
                                </div>
                                <p class="text-xs text-neutral-500 mt-1">{{ $job->applications_count }} applications • Expires {{ $job->expires_at?->format('M d, Y') }}</p>
                            </a>
                        @endforeach
                    </div>
                @endif
            </article>
        </div>

        <div class="mb-8">
            <h2 class="cw-heading-2 mb-4">Job performance cards</h2>
            @if($jobPerformance->isEmpty())
                <div class="cw-surface border border-neutral-200 rounded-lg p-6 text-sm text-neutral-600">No published job performance data yet.</div>
            @else
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($jobPerformance as $job)
                        <article class="cw-surface border border-neutral-200 rounded-lg p-4">
                            <div class="flex items-start justify-between gap-2">
                                <div>
                                    <h3 class="font-semibold text-neutral-900">{{ $job->title }}</h3>
                                    <p class="text-xs text-neutral-500 mt-0.5">Published {{ $job->published_at?->format('M d, Y') ?? 'N/A' }}</p>
                                </div>
                                <a href="{{ route('employer.jobs.edit', $job) }}" class="text-xs text-blue-600 hover:text-blue-700">Manage</a>
                            </div>
                            <div class="grid grid-cols-3 gap-2 mt-3 text-center">
                                <div class="rounded-lg bg-slate-50 p-2">
                                    <p class="text-xs text-slate-500">Applications</p>
                                    <p class="text-lg font-semibold text-slate-900">{{ $job->applications_count }}</p>
                                </div>
                                <div class="rounded-lg bg-violet-50 p-2">
                                    <p class="text-xs text-violet-500">Interview</p>
                                    <p class="text-lg font-semibold text-violet-700">{{ $job->interview_applications_count }}</p>
                                </div>
                                <div class="rounded-lg bg-emerald-50 p-2">
                                    <p class="text-xs text-emerald-500">Hired</p>
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
                <h2 class="cw-heading-2">Your Active Listings</h2>
                <a href="{{ route('employer.jobs.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    Manage All →
                </a>
            </div>

            @if($jobs->count() > 0)
                <div class="space-y-3">
                    @foreach($jobs->take(5) as $job)
                        <div class="cw-surface border border-neutral-200 rounded-lg p-4 hover:border-blue-300 transition">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-semibold text-neutral-900">{{ $job->title }}</h3>
                                        @if($job->is_featured)
                                            <span class="cw-chip cw-chip-featured">Featured</span>
                                        @endif
                                        @if($job->is_urgent)
                                            <span class="cw-chip cw-chip-urgent">Urgent</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-neutral-600 mt-1">{{ $job->location_city }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-blue-600">{{ $job->applications_count }}</p>
                                    <p class="text-xs text-neutral-600">applications</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-4 mt-3 pt-3 border-t border-neutral-100">
                                <span class="text-xs text-neutral-600">
                                    Expires: {{ $job->expires_at?->format('M d, Y') ?? 'No expiration' }}
                                </span>
                                @if($job->expires_at?->isFuture())
                                    <span class="cw-badge cw-badge-success">Active</span>
                                @elseif($job->expires_at?->isPast())
                                    <span class="cw-badge cw-badge-neutral">Expired</span>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="cw-surface border border-neutral-200 rounded-lg p-8 text-center">
                    <p class="text-neutral-600">No active job listings yet.</p>
                    <a href="{{ route('employer.jobs.create') }}" class="cw-button-primary mt-4 inline-flex">
                        Create Your First Job
                    </a>
                </div>
            @endif
        </div>

        <!-- Company Profile Status -->
        @if($employer)
            <div class="mt-12 pt-8 border-t border-neutral-200">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="cw-heading-3">Company Profile</h2>
                    <a href="{{ route('employer.settings.profile') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        Complete Profile →
                    </a>
                </div>
                <div class="cw-surface border border-neutral-200 rounded-lg p-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-neutral-600">Profile Completeness</p>
                            <p class="cw-heading-2 mt-1">{{ $employer->getProfileReadinessAttribute() }}%</p>
                        </div>
                        <div class="w-24 h-24">
                            <svg class="w-full h-full" viewBox="0 0 100 100">
                                <circle cx="50" cy="50" r="45" fill="none" stroke="#e5e7eb" stroke-width="8"/>
                                <circle cx="50" cy="50" r="45" fill="none" stroke="currentColor" stroke-width="8" 
                                    class="text-emerald-600" 
                                    stroke-dasharray="{{ $employer->getProfileReadinessAttribute() * 2.83 }},283"
                                    stroke-linecap="round"
                                    style="transform: rotate(-90deg); transform-origin: 50% 50%;"
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
