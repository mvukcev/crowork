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

            <div class="cw-surface overflow-hidden">
                @if($jobs->count() > 0)
                    <table class="cw-table">
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
                                            <a href="{{ route('employer.jobs.show', $job->id) }}" class="cw-button-secondary">View</a>
                                            <a href="{{ route('employer.jobs.edit', $job->id) }}" class="cw-button-secondary">Edit</a>
                                            <form method="POST" action="{{ route('employer.jobs.destroy', $job->id) }}" onsubmit="return confirm('Delete this job?')">
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
                    <div class="p-8 text-center">
                        <h2 class="text-xl font-semibold text-slate-900 mb-2">No jobs yet</h2>
                        <a href="{{ route('employer.jobs.create') }}" class="cw-button-primary">Create your first job</a>
                    </div>
                @endif
            </div>
        </div>
    </section>
</x-app-layout>
