<x-app-layout>
    <x-slot name="title">{{ $job->title }} - Applications</x-slot>

    <section class="cw-section">
        <div class="cw-container">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div>
                    <p class="cw-kicker mb-1">Employer job</p>
                    <h1 class="cw-display text-4xl md:text-6xl">{{ $job->title }}</h1>
                    <p class="text-sm text-slate-600 mt-2">{{ $job->company_name }} · {{ $job->location }} · Posted {{ $job->created_at->format('M j, Y') }}</p>
                </div>
                <a href="{{ route('employer.jobs.index') }}" class="cw-button-secondary">Back to jobs</a>
            </div>

            <div class="space-y-4">
                @if($applications->count() > 0)
                    @foreach($applications as $application)
                        <article class="cw-surface p-5">
                            <div class="flex flex-wrap items-center justify-between gap-3 mb-2">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900">{{ $application->worker->name ?? 'Unknown worker' }}</p>
                                    <p class="text-sm text-slate-600">{{ $application->worker->email ?? 'No email' }}</p>
                                </div>
                                <x-badge tone="info">{{ ucfirst($application->status) }}</x-badge>
                            </div>
                            <p class="text-xs text-slate-500 mb-2">Applied {{ $application->created_at->diffForHumans() }}</p>

                            @if($application->cover_letter || $application->message)
                                <div class="mb-3">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">Motivation</p>
                                    <p class="text-sm text-slate-700">{{ $application->cover_letter ?: $application->message }}</p>
                                </div>
                            @endif

                            @if(is_array($application->profile_snapshot) && count($application->profile_snapshot) > 0)
                                <div class="border-t border-slate-100 pt-3">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Profile snapshot</p>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-slate-700 mb-2">
                                        <p><strong>Nationality:</strong> {{ data_get($application->profile_snapshot, 'nationality_country_code', 'N/A') }}</p>
                                        <p><strong>Current city:</strong> {{ data_get($application->profile_snapshot, 'current_city', 'N/A') }}</p>
                                        <p><strong>Desired city:</strong> {{ data_get($application->profile_snapshot, 'desired_city', 'N/A') }}</p>
                                        <p><strong>Availability:</strong> {{ data_get($application->profile_snapshot, 'availability_date', 'N/A') }}</p>
                                    </div>

                                    @if(is_array(data_get($application->profile_snapshot, 'skills')) && count(data_get($application->profile_snapshot, 'skills')) > 0)
                                        <div class="flex flex-wrap gap-1.5 mb-2">
                                            @foreach(data_get($application->profile_snapshot, 'skills') as $skill)
                                                <span class="cw-chip">{{ $skill }}</span>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if(is_array(data_get($application->profile_snapshot, 'languages')) && count(data_get($application->profile_snapshot, 'languages')) > 0)
                                        <ul class="text-sm text-slate-700 space-y-1">
                                            @foreach(data_get($application->profile_snapshot, 'languages') as $language)
                                                @if(!empty($language['language']))
                                                    <li>{{ $language['language'] }}{{ !empty($language['level']) ? ' (' . $language['level'] . ')' : '' }}</li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            @endif
                        </article>
                    @endforeach
                @else
                    <div class="cw-surface p-8 text-center">
                        <h2 class="text-xl font-semibold text-slate-900 mb-2">No applications yet</h2>
                        <p class="text-slate-600">Applications will appear here once workers apply.</p>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-app-layout>
