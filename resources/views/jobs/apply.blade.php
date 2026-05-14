<x-app-layout>
    <x-slot name="title">Apply - {{ $job->title }}</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-4xl">
            <div class="mb-6 text-sm text-slate-500">
                <a href="{{ route('jobs.index') }}" class="hover:text-slate-900">Jobs</a>
                <span class="mx-1">/</span>
                <a href="{{ route('jobs.show', $job) }}" class="hover:text-slate-900">{{ $job->title }}</a>
                <span class="mx-1">/</span>
                <span class="text-slate-700">Apply</span>
            </div>

            @if($alreadyApplied)
                <div class="cw-surface p-6 text-center">
                    <h1 class="text-2xl font-semibold text-slate-900 mb-2">Application already submitted</h1>
                    <p class="text-slate-600 mb-4">You have already applied to this role{{ $existingApplication?->created_at ? ' on '.$existingApplication->created_at->format('M j, Y') : '' }}.</p>
                    <a href="{{ route('jobs.show', $job) }}" class="cw-button-secondary">Back to job</a>
                </div>
            @else
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
                    <div class="lg:col-span-2 cw-surface p-6">
                        <h1 class="cw-display text-3xl md:text-5xl mb-3">Apply for {{ $job->title }}</h1>
                        <p class="text-slate-600 mb-6">Submit your application and include a short note for the employer.</p>

                        <form method="POST" action="{{ route('jobs.apply.store', $job) }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="cw-label" for="message">Message (optional)</label>
                                <textarea id="message" name="message" rows="6" class="cw-field" placeholder="Introduce yourself and explain your fit.">{{ old('message') }}</textarea>
                                @error('message')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                            </div>
                            <button type="submit" class="cw-button-primary">Submit application</button>
                        </form>
                    </div>

                    <aside class="cw-surface p-5">
                        <h2 class="text-lg font-semibold text-slate-900 mb-3">Profile snapshot</h2>
                        <p class="text-sm text-slate-700 mb-1"><strong>Name:</strong> {{ $profile?->first_name }} {{ $profile?->last_name }}</p>
                        <p class="text-sm text-slate-700 mb-1"><strong>Nationality:</strong> {{ $profile?->nationality_country_code ?? 'N/A' }}</p>
                        <p class="text-sm text-slate-700 mb-3"><strong>Birth year:</strong> {{ $profile?->birth_year ?? 'N/A' }}</p>
                        @if(!empty($profileSkills))
                            <div class="flex flex-wrap gap-1.5 mb-3">
                                @foreach($profileSkills as $skill)
                                    <span class="cw-chip">{{ $skill }}</span>
                                @endforeach
                            </div>
                        @endif
                        <a href="{{ route('worker.profile.edit') }}" class="cw-button-secondary w-full">Update profile</a>
                    </aside>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
