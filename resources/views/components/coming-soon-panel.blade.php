@props([
    'title' => 'Coming Soon',
    'description' => 'This feature is being prepared and will be available soon.',
])

<x-card class="border border-border/70 shadow-elevation-2 text-center">
    <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-primary/10 flex items-center justify-center">
        <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />
        </svg>
    </div>
    <h1 class="text-title-1 font-semibold text-text-primary mb-2">{{ $title }}</h1>
    <p class="text-body text-text-secondary max-w-xl mx-auto mb-6">{{ $description }}</p>
    <div class="flex flex-wrap justify-center gap-3">
        <x-button href="{{ route('home') }}" variant="primary">Back Home</x-button>
        <x-button href="{{ route('contact') }}" variant="outline">Contact Support</x-button>
    </div>
</x-card>
