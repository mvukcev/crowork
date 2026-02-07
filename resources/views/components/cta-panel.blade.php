@props([
    'title' => null,
    'subtitle' => null,
    'centered' => true,
])

@php
    $alignClass = $centered ? 'text-center' : '';
@endphp

<x-surface variant="tinted" elevation="1" class="border-primary/10">
    <div class="{{ $alignClass }}">
        @if($title)
            <h3 class="text-title-3 font-semibold text-text-primary mb-3">
                {{ $title }}
            </h3>
        @endif
        
        @if($subtitle)
            <p class="text-body text-text-secondary mb-6">
                {{ $subtitle }}
            </p>
        @endif
        
        @isset($actions)
            <div class="{{ $centered ? 'flex justify-center gap-3' : 'flex gap-3' }}">
                {{ $actions }}
            </div>
        @else
            {{ $slot }}
        @endisset
    </div>
</x-surface>
