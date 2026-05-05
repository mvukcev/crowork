<x-app-layout>
    <x-slot name="title">My Education Applications</x-slot>
    <x-slot name="description">Track your submitted CroWork education applications.</x-slot>

    <div class="section-spacing-tight bg-background">
        <div class="container-base">
            <div class="mb-8 flex flex-col gap-3 md:flex-row md:items-end md:justify-between">
                <div>
                    <p class="text-body-sm font-semibold uppercase tracking-wide text-primary mb-2">Worker dashboard</p>
                    <h1 class="text-title-1 font-semibold text-text-primary mb-2">My Education Applications</h1>
                    <p class="text-body text-text-secondary mb-0">Review education programs you have applied to and follow their current status.</p>
                </div>
                <x-button href="{{ route('worker.applications.index') }}" variant="outline">
                    Job Applications
                </x-button>
            </div>

            <x-card class="border border-border/70 shadow-elevation-1">
                @if($applications->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-border">
                            <thead>
                                <tr class="text-left text-caption font-semibold uppercase tracking-wide text-text-tertiary">
                                    <th class="px-4 py-3">Education</th>
                                    <th class="px-4 py-3">Provider</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Submitted</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-border/70">
                                @foreach($applications as $application)
                                    <tr>
                                        <td class="px-4 py-4">
                                            @if($application->education)
                                                <a href="{{ route('educations.show', $application->education) }}" class="font-semibold text-text-primary hover:text-primary transition-colors">
                                                    {{ $application->education->title }}
                                                </a>
                                            @else
                                                <span class="font-semibold text-text-primary">Unavailable education</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-4 text-body-sm text-text-secondary">
                                            {{ $application->education?->createdByUser?->name ?? 'CroWork education provider' }}
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
                        <h2 class="text-subtitle font-semibold text-text-primary mb-2">No education applications yet</h2>
                        <p class="text-body text-text-secondary mb-6">Explore learning programs and apply when you find a match.</p>
                        <x-button href="{{ route('educations.index') }}" variant="primary">Browse Educations</x-button>
                    </div>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
