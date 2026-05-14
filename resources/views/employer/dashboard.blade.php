@extends('layouts.app')

@section('content')
<div class="cw-surface min-h-screen">
    <!-- Header -->
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
        <!-- Job Statistics -->
        <div class="mb-8">
            <h2 class="cw-heading-2 mb-4">Active Listings</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Active Jobs Card -->
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

                <!-- Pending Jobs Card -->
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

                <!-- Expired Jobs Card -->
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

        <!-- Application Statistics -->
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="cw-heading-2">Application Pipeline</h2>
                <a href="{{ route('employer.applications.pipeline') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                    View Full Pipeline →
                </a>
            </div>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <!-- Total Applications -->
                <div class="cw-surface border border-neutral-200 rounded-lg p-4">
                    <p class="text-xs font-medium text-neutral-600 uppercase tracking-wide">Total Applications</p>
                    <p class="cw-heading-1 mt-2">{{ $totalApplications }}</p>
                </div>

                <!-- New Applications -->
                <div class="cw-surface border border-neutral-200 rounded-lg p-4">
                    <p class="text-xs font-medium text-neutral-600 uppercase tracking-wide">New</p>
                    <p class="cw-heading-1 mt-2 text-blue-600">{{ $newApplications }}</p>
                </div>

                <!-- Shortlisted -->
                <div class="cw-surface border border-neutral-200 rounded-lg p-4">
                    <p class="text-xs font-medium text-neutral-600 uppercase tracking-wide">Shortlisted</p>
                    <p class="cw-heading-1 mt-2 text-indigo-600">{{ $shortlistedCount }}</p>
                </div>

                <!-- Interview -->
                <div class="cw-surface border border-neutral-200 rounded-lg p-4">
                    <p class="text-xs font-medium text-neutral-600 uppercase tracking-wide">Interview</p>
                    <p class="cw-heading-1 mt-2 text-purple-600">{{ $interviewCount }}</p>
                </div>

                <!-- Offer -->
                <div class="cw-surface border border-neutral-200 rounded-lg p-4">
                    <p class="text-xs font-medium text-neutral-600 uppercase tracking-wide">Offer</p>
                    <p class="cw-heading-1 mt-2 text-green-600">{{ $offerCount }}</p>
                </div>

                <!-- Hired -->
                <div class="cw-surface border border-neutral-200 rounded-lg p-4">
                    <p class="text-xs font-medium text-neutral-600 uppercase tracking-wide">Hired</p>
                    <p class="cw-heading-1 mt-2 text-emerald-600">{{ $hiredCount }}</p>
                </div>

                <!-- Rejected -->
                <div class="cw-surface border border-neutral-200 rounded-lg p-4">
                    <p class="text-xs font-medium text-neutral-600 uppercase tracking-wide">Rejected</p>
                    <p class="cw-heading-1 mt-2 text-red-600">{{ $rejectedCount }}</p>
                </div>
            </div>
        </div>

        <!-- Your Active Jobs -->
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
