<x-app-layout>
    <x-slot name="title">Worker Dashboard</x-slot>

    <section class="cw-section">
        <div class="cw-container">
            <div class="flex flex-wrap items-start justify-between gap-3 mb-6">
                <div>
                    <p class="cw-kicker mb-1">Worker dashboard</p>
                    <h1 class="cw-display text-4xl md:text-6xl">Welcome, {{ $user->name }}.</h1>
                    <p class="text-slate-600 mt-2">Complete your profile, follow your timeline, and act on the next best steps.</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('home') }}" class="cw-button-secondary">Homepage</a>
                    <a href="{{ route('worker.profile.edit') }}" class="cw-button-primary">Update profile</a>
                    <a href="{{ route('jobs.index') }}" class="cw-button-secondary">Browse jobs</a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">
                <article class="cw-surface p-5 lg:col-span-2">
                    <div class="flex items-center justify-between mb-2">
                        <h2 class="text-lg font-semibold text-slate-900">Profile completeness</h2>
                        <span class="text-sm font-semibold text-slate-800">{{ $completeness }}%</span>
                    </div>
                    <div class="h-2 rounded-full bg-slate-100 overflow-hidden mb-3">
                        <div class="h-full bg-emerald-500" style="width: {{ $completeness }}%"></div>
                    </div>
                    <div class="flex flex-wrap items-center justify-between gap-2">
                        <p class="text-sm text-slate-600">
                            @if($completeness < 80)
                                Complete a few more fields to improve match quality and employer response rates.
                            @else
                                Your profile is strong. Keep applications active and up to date.
                            @endif
                        </p>
                        <a href="{{ route('worker.profile.edit') }}" class="cw-button-secondary">
                            {{ $completeness < 80 ? 'Finish profile now' : 'Review profile' }}
                        </a>
                    </div>
                </article>

                <article class="cw-surface p-5">
                    <h2 class="text-lg font-semibold text-slate-900 mb-3">Applications overview</h2>
                    <div class="space-y-2 text-sm text-slate-700">
                        <p><strong>Active applications:</strong> {{ $activeApplicationsCount }}</p>
                        <p><strong>Job applications:</strong> {{ $totalJobApplications }}</p>
                        <p><strong>Education applications:</strong> {{ $totalEducationApplications }}</p>
                    </div>
                    <div class="mt-4 flex gap-2">
                        <a href="{{ route('worker.applications.index') }}" class="cw-button-secondary">Track job applications</a>
                    </div>
                </article>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5 mb-6">
                <article class="cw-surface p-5">
                    <h2 class="text-lg font-semibold text-slate-900 mb-3">Onboarding checklist</h2>
                    <div class="space-y-2">
                        @foreach($onboardingChecklist as $check)
                            <a href="{{ $check['href'] }}" class="flex items-center justify-between gap-3 p-3 rounded-xl border {{ $check['done'] ? 'border-emerald-200 bg-emerald-50/40' : 'border-slate-200 bg-white' }} hover:border-slate-300 transition">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex h-5 w-5 items-center justify-center rounded-full {{ $check['done'] ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500' }} text-xs font-semibold">
                                        {{ $check['done'] ? '✓' : '•' }}
                                    </span>
                                    <span class="text-sm {{ $check['done'] ? 'text-emerald-800 font-medium' : 'text-slate-700' }}">{{ $check['label'] }}</span>
                                </div>
                                <span class="text-xs text-slate-500">Open</span>
                            </a>
                        @endforeach
                    </div>

                    @if(count($missingChecklist) > 0)
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mt-4 mb-2">Missing profile fields</p>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach($missingChecklist as $item)
                                <span class="cw-chip">{{ $item }}</span>
                            @endforeach
                        </div>
                    @endif
                </article>

                <article class="cw-surface p-5">
                    <h2 class="text-lg font-semibold text-slate-900 mb-3">Recommended next actions</h2>
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
                    <h2 class="text-lg font-semibold text-slate-900">Application timeline</h2>
                    <a href="{{ route('worker.applications.index') }}" class="text-sm text-slate-600 hover:text-slate-900">Open full history</a>
                </div>

                @if($applicationTimeline->isEmpty())
                    <p class="text-sm text-slate-600">No timeline events yet. Your updates will appear here after you apply.</p>
                @else
                    <div class="space-y-3">
                        @foreach($applicationTimeline as $event)
                            <a href="{{ $event['href'] }}" class="block p-3 rounded-xl border border-slate-200 hover:border-slate-300 transition">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $event['title'] }}</p>
                                        <p class="text-xs text-slate-500 mt-0.5">{{ $event['subtitle'] }}</p>
                                    </div>
                                    <span class="text-xs uppercase tracking-wide px-2 py-1 rounded-full {{ $event['type'] === 'job' ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-700' }}">{{ $event['type'] }}</span>
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
                    <h2 class="text-lg font-semibold text-slate-900 mb-3">Latest job application statuses</h2>
                    @if($latestJobApplications->isEmpty())
                        <p class="text-sm text-slate-600">No job applications yet.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($latestJobApplications as $application)
                                <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-2 last:border-0 last:pb-0">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $application->job?->title ?? 'Job unavailable' }}</p>
                                        <p class="text-xs text-slate-500">{{ $application->job?->employer?->company_name ?? 'Employer unavailable' }} · Applied {{ $application->created_at?->format('M j, Y') }}</p>
                                    </div>
                                    <x-badge tone="info">{{ ucfirst($application->status) }}</x-badge>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <a href="{{ route('worker.applications.index') }}" class="cw-button-secondary mt-4">View all job applications</a>
                </article>

                <article class="cw-surface p-5">
                    <h2 class="text-lg font-semibold text-slate-900 mb-3">Education applications</h2>
                    @if($latestEducationApplications->isEmpty())
                        <p class="text-sm text-slate-600">No education applications yet.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($latestEducationApplications as $application)
                                <div class="flex items-center justify-between gap-3 border-b border-slate-100 pb-2 last:border-0 last:pb-0">
                                    <div>
                                        <p class="text-sm font-semibold text-slate-900">{{ $application->education?->title ?? 'Program unavailable' }}</p>
                                        <p class="text-xs text-slate-500">Applied {{ $application->created_at?->format('M j, Y') }}</p>
                                    </div>
                                    <x-badge tone="info">{{ ucfirst($application->status) }}</x-badge>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <a href="{{ route('worker.education-applications.index') }}" class="cw-button-secondary mt-4">View all education applications</a>
                </article>
            </div>

            <article class="cw-surface p-5">
                <h2 class="text-lg font-semibold text-slate-900 mb-3">Recommended jobs</h2>
                @if($recommendedJobs->isEmpty())
                    <p class="text-sm text-slate-600">No recommendations yet. Try adding your desired city and roles in your profile.</p>
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
