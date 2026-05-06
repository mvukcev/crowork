<x-app-layout>
    <x-slot name="title">Page Not Found</x-slot>
    <x-slot name="description">The page you requested does not exist.</x-slot>

    <div class="section-spacing-tight bg-background min-h-screen">
        <div class="container-base max-w-3xl">
            <x-card class="border border-border/70 shadow-elevation-2 text-center">
                <p class="text-caption uppercase tracking-wide font-semibold text-primary mb-3">Error 404</p>
                <h1 class="text-display-sm font-semibold text-text-primary mb-3">Page Not Found</h1>
                <p class="text-body text-text-secondary max-w-xl mx-auto mb-8">
                    The page you requested could not be found. You can continue exploring CroWork using the links below.
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-w-xl mx-auto">
                    <x-button href="{{ route('home') }}" variant="primary" class="w-full">Home</x-button>
                    <x-button href="{{ route('jobs.index') }}" variant="outline" class="w-full">Jobs</x-button>
                    <x-button href="{{ route('educations.index') }}" variant="outline" class="w-full">Educations</x-button>
                    <x-button href="{{ route('contact') }}" variant="outline" class="w-full">Contact</x-button>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
