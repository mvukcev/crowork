<x-app-layout>
    <x-slot name="title">Apply - {{ $education->title }}</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-3xl">
            <div class="mb-6 text-sm text-slate-500">
                <a href="{{ route('educations.index') }}" class="hover:text-slate-900">Educations</a>
                <span class="mx-1">/</span>
                <a href="{{ route('educations.show', $education) }}" class="hover:text-slate-900">{{ $education->title }}</a>
                <span class="mx-1">/</span>
                <span class="text-slate-700">Apply</span>
            </div>

            @if($alreadyApplied)
                <div class="cw-surface p-6 text-center">
                    <h1 class="text-2xl font-semibold text-slate-900 mb-2">Application already sent</h1>
                    <p class="text-slate-600 mb-4">You have already applied{{ $existingApplication?->created_at ? ' on '.$existingApplication->created_at->format('M j, Y') : '' }}.</p>
                    <a href="{{ route('worker.education-applications.index') }}" class="cw-button-secondary">View my applications</a>
                </div>
            @else
                <div class="cw-surface p-6 md:p-7">
                    <h1 class="cw-display text-3xl md:text-5xl mb-3">Apply for {{ $education->title }}</h1>
                    <p class="text-slate-600 mb-6">Send your application and include a short motivation message.</p>

                    <form method="POST" action="{{ route('educations.apply.store', $education) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="cw-label" for="message">Message (optional)</label>
                            <textarea id="message" name="message" rows="6" class="cw-field" placeholder="Share your goals and readiness.">{{ old('message') }}</textarea>
                            @error('message')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <button type="submit" class="cw-button-primary">Submit application</button>
                    </form>
                </div>
            @endif
        </div>
    </section>
</x-app-layout>
