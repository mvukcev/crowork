<x-app-layout>
    <x-slot name="title">My Job Applications</x-slot>

    <section class="cw-section">
        <div class="cw-container">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div>
                    <p class="cw-kicker mb-1">Worker applications</p>
                    <h1 class="cw-display text-4xl md:text-6xl">Job applications</h1>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('worker.education-applications.index') }}" class="cw-button-secondary">Education applications</a>
                    <a href="{{ route('worker.settings.edit') }}" class="cw-button-secondary">Settings</a>
                </div>
            </div>

            <div class="cw-surface overflow-hidden">
                @if($applications->count() > 0)
                    <div class="overflow-x-auto">
                    <table class="cw-table min-w-[860px]">
                        <thead>
                            <tr>
                                <th>Job</th>
                                <th>Employer</th>
                                <th>Status</th>
                                <th>Applied</th>
                                <th>Status updated</th>
                                <th>Motivation</th>
                                <th>Snapshot</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $application)
                                <tr>
                                    <td>{{ $application->job?->title ?? 'N/A' }}</td>
                                    <td>{{ $application->job?->employer?->company_name ?? 'N/A' }}</td>
                                    <td><x-badge tone="info">{{ ucfirst($application->status) }}</x-badge></td>
                                    <td>{{ $application->created_at?->format('M j, Y') }}</td>
                                    <td>{{ $application->status_updated_at?->format('M j, Y H:i') ?? 'N/A' }}</td>
                                    <td class="max-w-xs">
                                        <p class="text-sm text-slate-700 line-clamp-2">{{ $application->message ?: 'No motivation message provided.' }}</p>
                                    </td>
                                    <td>{{ !empty($application->profile_snapshot) ? 'Stored' : 'N/A' }}</td>
                                    <td>
                                        @if($application->job)
                                            <a href="{{ route('jobs.show', $application->job) }}" class="cw-button-secondary">View</a>
                                        @else
                                            <span class="text-xs text-slate-500">Unavailable</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    </div>
                    <div class="p-4">{{ $applications->links() }}</div>
                @else
                    <div class="p-12 text-center rounded-2xl border-2 border-dashed border-slate-200 bg-gradient-to-br from-slate-50 to-white">
                        <svg class="mx-auto h-12 w-12 text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                        </svg>
                        <h2 class="text-xl font-semibold text-slate-900 mb-2">Start your job search</h2>
                        <p class="text-slate-600 mb-2">You haven't applied to any jobs yet.</p>
                        <p class="text-sm text-slate-500 mb-6">Your profile details are shared only when you submit an application.</p>
                        <div class="flex flex-wrap gap-3 justify-center">
                            <a href="{{ route('jobs.index') }}" class="cw-button-primary">Browse jobs</a>
                            <a href="{{ route('worker.profile.edit') }}" class="cw-button-secondary">Complete your profile first</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-app-layout>
