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
                    <table class="cw-table">
                        <thead>
                            <tr>
                                <th>Job</th>
                                <th>Employer</th>
                                <th>Status</th>
                                <th>Applied</th>
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
                                    <td><a href="{{ route('jobs.show', $application->job) }}" class="cw-button-secondary">View</a></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="p-4">{{ $applications->links() }}</div>
                @else
                    <div class="p-8 text-center">
                        <h2 class="text-xl font-semibold text-slate-900 mb-2">No job applications yet</h2>
                        <a href="{{ route('jobs.index') }}" class="cw-button-primary">Browse jobs</a>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-app-layout>
