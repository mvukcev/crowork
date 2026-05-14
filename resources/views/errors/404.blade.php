<x-app-layout>
    <x-slot name="title">Page Not Found</x-slot>
    <x-slot name="robots">noindex,follow</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-3xl text-center">
            <p class="cw-kicker mb-2">Error 404</p>
            <h1 class="cw-display text-5xl md:text-7xl mb-4">This page does not exist.</h1>
            <p class="text-base text-slate-600 mb-7">The link may be outdated or the page was moved.</p>
            <div class="flex flex-wrap justify-center gap-2">
                <a href="{{ route('home') }}" class="cw-button-primary">Go home</a>
                <a href="{{ route('jobs.index') }}" class="cw-button-secondary">Browse jobs</a>
                <a href="{{ route('educations.index') }}" class="cw-button-secondary">Browse educations</a>
            </div>
        </div>
    </section>
</x-app-layout>
