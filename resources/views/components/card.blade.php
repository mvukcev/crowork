@props([
    'title' => null,
    'href' => null,
    'elevated' => false,
    'interactive' => false,
])

@php
    $classes = 'bg-background border border-border rounded-lg overflow-hidden transition-all duration-200';
    
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
            <div class="px-4 py-3 border-b border-border bg-surface">
                <h3 class="text-subtitle font-semibold text-text-primary">{{ $title }}</h3>
            </div>
        @endif
        <div class="p-4">
            {{ $slot }}
        </div>
    </a>
@else
    <div {{ $attributes->merge(['class' => $classes]) }}>
        @if($title)
            <div class="px-4 py-3 border-b border-border bg-surface">
                <h3 class="text-subtitle font-semibold text-text-primary">{{ $title }}</h3>
            </div>
        @endif
        <div class="p-4">
            {{ $slot }}
        </div>
    </div>
@endif
