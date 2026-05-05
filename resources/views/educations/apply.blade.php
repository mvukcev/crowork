<x-app-layout>
    <x-slot name="title">Apply for {{ $education->title }}</x-slot>
    <x-slot name="description">Submit your CroWork profile for this education program.</x-slot>

    <div class="section-spacing-tight bg-background">
        <div class="container-base max-w-3xl">
            <nav class="flex items-center text-body-sm text-text-secondary mb-6">
                <a href="{{ route('educations.index') }}" class="hover:text-primary transition-colors">Educations</a>
                <span class="mx-2 text-text-tertiary">/</span>
                <a href="{{ route('educations.show', $education) }}" class="hover:text-primary transition-colors">{{ $education->title }}</a>
                <span class="mx-2 text-text-tertiary">/</span>
                <span class="text-text-primary font-medium">Apply</span>
            </nav>

            <x-card class="border border-border/70 shadow-elevation-1">
                <div class="mb-6">
                    <p class="text-body-sm font-semibold uppercase tracking-wide text-primary mb-2">Education application</p>
                    <h1 class="text-title-1 font-semibold text-text-primary mb-2">Apply for {{ $education->title }}</h1>
                    <p class="text-body text-text-secondary mb-0">Your CroWork profile snapshot will be included with this application.</p>
                </div>

                @if($alreadyApplied)
                    <div class="rounded-xl border border-info-100 bg-info-50 p-4 mb-6">
                        <p class="text-body-sm text-info-600 mb-0">
                            You already applied on {{ $existingApplication->created_at->format('M j, Y') }}.
                        </p>
                    </div>
                    <x-button href="{{ route('worker.education-applications.index') }}" variant="primary">
                        View Application Tracking
                    </x-button>
                @else
                    <form method="POST" action="{{ route('educations.apply.store', $education) }}" class="space-y-6">
                        @csrf

                        <div>
                            <label for="message" class="block text-body-sm font-semibold text-text-primary mb-2">
                                Message to provider <span class="text-text-tertiary font-normal">(optional)</span>
                            </label>
                            <textarea
                                id="message"
                                name="message"
                                rows="5"
                                maxlength="1000"
                                class="w-full rounded-xl border-border text-body text-text-primary shadow-sm focus:border-primary focus:ring-primary/30"
                                placeholder="Share why this program is a good fit for you."
                            >{{ old('message') }}</textarea>
                            <x-input-error :messages="$errors->get('message')" class="mt-2" />
                        </div>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <x-button type="submit" variant="primary">Submit Application</x-button>
                            <x-button href="{{ route('educations.show', $education) }}" variant="outline">Cancel</x-button>
                        </div>
                    </form>
                @endif
            </x-card>
        </div>
    </div>
</x-app-layout>
