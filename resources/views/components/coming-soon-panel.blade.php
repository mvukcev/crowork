@props([
    'title' => 'Coming Soon',
    'description' => 'This feature is being prepared and will be available soon.',
])

<div class="cw-surface p-8 text-center">
    <div class="w-14 h-14 mx-auto mb-4 rounded-2xl bg-slate-100 flex items-center justify-center">
        <svg class="w-7 h-7 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M5.07 19h13.86c1.54 0 2.5-1.67 1.73-3L13.73 4c-.77-1.33-2.69-1.33-3.46 0L3.34 16c-.77 1.33.19 3 1.73 3z" />
        </svg>
    </div>
    <h1 class="cw-display text-3xl md:text-4xl text-slate-900 mb-2">{{ $title }}</h1>
    <p class="text-slate-600 max-w-xl mx-auto mb-6">{{ $description }}</p>
    <div class="flex flex-wrap justify-center gap-2">
        <a href="{{ route('home') }}" class="cw-button-primary">Back home</a>
        <a href="{{ route('contact') }}" class="cw-button-secondary">Contact support</a>
    </div>
</div>
