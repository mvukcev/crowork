<x-app-layout>
    <x-slot name="title">My Job Applications</x-slot>
    <x-slot name="description">Track your submitted CroWork job applications.</x-slot>

    <div class="section-spacing-tight bg-background">
        <div class="container-base">
            <div class="mb-8 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-body-sm font-semibold uppercase tracking-wide text-primary mb-2">Worker dashboard</p>
                    <h1 class="text-title-1 font-semibold text-text-primary mb-2">My Job Applications</h1>
                    <p class="text-body text-text-secondary mb-0">Review jobs you have applied to and follow their current status.</p>
                </div>
                <x-button href="{{ route('worker.education-applications.index') }}" variant="outline">
                    Education Applications
                </x-button>
            </div>

            <x-card class="border border-border/70 shadow-elevation-1">
                @if($applications->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-border">
                            <thead>
                                <tr class="text-left text-caption font-semibold uppercase tracking-wide text-text-tertiary">
                                    <th class="px-4 py-3">Job</th>
                                    <th class="px-4 py-3">Employer</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Submitted</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border/70">
                                @foreach($applications as $application)
                                    <tr>
                                        <td class="px-4 py-4">
                                            @if($application->job)
                                                <a href="{{ route('jobs.show', $application->job) }}" class="font-semibold text-text-primary hover:text-primary transition-colors">
                                                    {{ $application->job->title }}
                                                </a>
                                            @else
                                                <span class="font-semibold text-text-primary">Unavailable job</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-body-sm text-text-secondary">
                                            {{ $application->job?->employer?->company_name ?? 'Unknown employer' }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <x-badge tone="info">{{ ucfirst($application->status) }}</x-badge>
                                        </td>
                                        <td class="px-4 py-4 text-body-sm text-text-secondary">
                                            {{ $application->created_at->format('M j, Y') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $applications->links() }}
                    </div>
                @else
                    <div class="py-12 text-center">
                        <h2 class="text-subtitle font-semibold text-text-primary mb-2">No job applications yet</h2>
                        <p class="text-body text-text-secondary mb-6">Browse open roles and submit your first application.</p>
                        <x-button href="{{ route('jobs.index') }}" variant="primary">Browse Jobs</x-button>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
