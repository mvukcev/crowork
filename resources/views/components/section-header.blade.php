@props([
    'title' => null,
    'subtitle' => null,
    'centered' => false,
])

@php
    $alignmentClasses = $centered ? 'text-center' : 'text-left';
@endphp

<div {{ $attributes->merge(['class' => 'mb-6 ' . $alignmentClasses]) }}>
    @if($title)
        <h2 class="text-title-2 font-semibold text-text-primary mb-2">{{ $title }}</h2>
    @endif
    
    @if($subtitle)
        <p class="text-body text-text-secondary">{{ $subtitle }}</p>
    @endif
    
    @if($slot->isNotEmpty())
        <div class="mt-3">
            {{ $slot }}
        </div>
    @endif
</div>
