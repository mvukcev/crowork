<x-app-layout>
    <x-slot name="title">{{ __('errors.404.title') }}</x-slot>
    <x-slot name="robots">noindex,follow</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-3xl text-center">
            <p class="cw-kicker mb-2">{{ __('errors.404.kicker') }}</p>
            <h1 class="cw-display text-5xl md:text-7xl mb-4">{{ __('errors.404.heading') }}</h1>
            <p class="text-base text-slate-600 mb-7">{{ __('errors.404.body') }}</p>
            <div class="flex flex-wrap justify-center gap-2">
                <a href="{{ route('home') }}" class="cw-button-primary">{{ __('errors.actions.go_home') }}</a>
                <a href="{{ route('jobs.index') }}" class="cw-button-secondary">{{ __('errors.actions.browse_jobs') }}</a>
                <a href="{{ route('educations.index') }}" class="cw-button-secondary">{{ __('errors.actions.browse_educations') }}</a>
            </div>
        </div>
    </section>
</x-app-layout>
