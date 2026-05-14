@props([
    'title' => null,
    'subtitle' => null,
    'size' => 'md',
    'align' => 'center',
])

@php
    $sizeClasses = [
        'lg' => 'py-20 md:py-24',
        'md' => 'py-14 md:py-16',
        'sm' => 'py-10 md:py-12',
    ];
@endphp

<section class="relative {{ $sizeClasses[$size] ?? $sizeClasses['md'] }}">
    <div class="cw-hero-glow" aria-hidden="true"></div>
    <div class="cw-container relative z-10 {{ $align === 'left' ? 'text-left' : 'text-center' }}">
        @if($title)
            <h1 class="cw-display text-4xl md:text-6xl mb-4 {{ $align === 'left' ? '' : 'max-w-4xl mx-auto' }}">{{ $title }}</h1>
        @endif

        @if($subtitle)
            <p class="text-base text-slate-600 {{ $align === 'left' ? 'max-w-3xl' : 'max-w-3xl mx-auto' }} mb-6">{{ $subtitle }}</p>
        @endif

        {{ $slot }}
    </div>
</section>
