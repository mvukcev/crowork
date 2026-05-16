<x-app-layout>
    <x-slot name="title">Manage Employer Jobs</x-slot>

    <section class="cw-section">
        <div class="cw-container">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                <div>
                    <p class="cw-kicker mb-1">Employer</p>
                    <h1 class="cw-display text-4xl md:text-6xl">Your jobs</h1>
                </div>
                <a href="{{ route('employer.jobs.create') }}" class="cw-button-primary">Post new job</a>
            </div>

            @if(session('success'))
                <div class="cw-surface p-3 mb-4 text-sm text-emerald-700 bg-emerald-50 border-emerald-200">{{ session('success') }}</div>
            @endif

            <div class="cw-surface overflow-x-auto">
                @if($jobs->count() > 0)
                    <table class="cw-table min-w-[760px]">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Applications</th>
                                <th>Created</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jobs as $job)
                                <tr>
                                    <td>{{ $job->title }}</td>
                                    <td>{{ $job->location }}</td>
                                    <td>
                                        @if($job->is_active)
                                            <x-badge tone="success">Active</x-badge>
                                        @else
                                            <x-badge tone="warning">Draft</x-badge>
                                        @endif
                                    </td>
                                    <td>{{ $job->applications_count ?? 0 }}</td>
                                    <td>{{ $job->created_at?->format('M j, Y') }}</td>
                                    <td>
                                        <div class="flex gap-2">
                                            <a href="{{ route('employer.jobs.show', $job) }}" class="cw-button-secondary">View</a>
                                            <a href="{{ route('employer.jobs.edit', $job) }}" class="cw-button-secondary">Edit</a>
                                            <form method="POST" action="{{ route('employer.jobs.destroy', $job) }}" onsubmit="return confirm('Delete this job?')">
                                                @csrf @method('DELETE')
                                                <button class="cw-button-secondary text-red-700 border-red-200 bg-red-50">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="p-4">{{ $jobs->links() }}</div>
                @else
                    <div class="p-12 text-center rounded-2xl border-2 border-dashed border-slate-200 bg-gradient-to-br from-blue-50 to-white">
                        <svg class="mx-auto h-12 w-12 text-blue-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"></path>
                        </svg>
                        <h2 class="text-xl font-semibold text-slate-900 mb-2">Post your first job</h2>
                        <p class="text-slate-600 mb-2">Start finding qualified candidates today.</p>
                        <p class="text-sm text-slate-500 mb-6">For better trust and conversion, complete your company profile before publishing your first listing.</p>
                        <div class="flex flex-wrap justify-center gap-2">
                            <a href="{{ route('employer.jobs.create') }}" class="cw-button-primary inline-block">Create your first job</a>
                            <a href="{{ route('employer.settings.profile') }}" class="cw-button-secondary inline-block">Complete company profile</a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-app-layout>
