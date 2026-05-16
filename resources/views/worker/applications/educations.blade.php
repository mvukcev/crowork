<x-app-layout>
    <x-slot name="title">My Education Applications</x-slot>

    <section class="cw-section">
        <div class="cw-container">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div>
                    <p class="cw-kicker mb-1">Worker applications</p>
                    <h1 class="cw-display text-4xl md:text-6xl">Education applications</h1>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('worker.applications.index') }}" class="cw-button-secondary">Job applications</a>
                    <a href="{{ route('worker.settings.edit') }}" class="cw-button-secondary">Settings</a>
                </div>
            </div>

            <div class="cw-surface overflow-hidden">
                @if($applications->count() > 0)
                    <div class="overflow-x-auto">
                    <table class="cw-table min-w-[760px]">
                        <thead>
                            <tr>
                                <th>Program</th>
                                <th>Provider</th>
                                <th>Status</th>
                                <th>Applied</th>
                                <th>Motivation</th>
                                <th>Snapshot</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($applications as $application)
                                <tr>
                                    <td>{{ $application->education?->title ?? 'N/A' }}</td>
                                    <td>{{ $application->education?->createdByUser?->name ?? 'N/A' }}</td>
                                    <td><x-badge tone="info">{{ ucfirst($application->status) }}</x-badge></td>
                                    <td>{{ $application->created_at?->format('M j, Y') }}</td>
                                    <td class="max-w-xs">
                                        <p class="text-sm text-slate-700 line-clamp-2">{{ $application->message ?: 'No motivation message provided.' }}</p>
                                    </td>
                                    <td>{{ !empty($application->profile_snapshot) ? 'Stored' : 'N/A' }}</td>
                                    <td>
                                        @if($application->education)
                                            <a href="{{ route('educations.show', $application->education) }}" class="cw-button-secondary">View</a>
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
                    <div class="p-10 text-center border-2 border-dashed border-slate-200 rounded-2xl bg-gradient-to-br from-slate-50 to-white">
                        <h2 class="text-xl font-semibold text-slate-900 mb-2">No education applications yet</h2>
                        <p class="text-sm text-slate-600 mb-5">Start one program application to strengthen your relocation readiness.</p>
                        <div class="flex flex-wrap justify-center gap-2">
                            <a href="{{ route('educations.index') }}" class="cw-button-primary">Browse educations</a>
                            <a href="{{ route('worker.profile.edit') }}" class="cw-button-secondary">Review profile</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-app-layout>
