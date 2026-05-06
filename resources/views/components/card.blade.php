@props([
    'title' => null,
    'href' => null,
    'elevated' => false,
    'interactive' => false,
])

@php
    $classes = 'premium-glass rounded-2xl overflow-hidden border border-white/80 transition-all duration-normal';
    
    if ($elevated) {
        $classes .= ' shadow-card';
    }
    
    if ($interactive) {
        $classes .= ' hover:shadow-hover card-interactive cursor-pointer';
    }
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        @if($title)
            <div class="px-5 py-4 border-b border-border/60 bg-white/55">
                <h3 class="text-subtitle font-semibold text-text-primary">{{ $title }}</h3>
            </div>
        @endif
        <div class="p-5 md:p-6">
            {{ $slot }}
        </div>
    </a>
@else
    <div {{ $attributes->merge(['class' => $classes]) }}>
        @if($title)
            <div class="px-5 py-4 border-b border-border/60 bg-white/55">
                <h3 class="text-subtitle font-semibold text-text-primary">{{ $title }}</h3>
            </div>
        @endif
        <div class="p-5 md:p-6">
            {{ $slot }}
        </div>
    </div>
@endif
