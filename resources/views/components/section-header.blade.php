@props([
    'title' => null,
    'subtitle' => null,
    'centered' => false,
])

@php
    $alignmentClasses = $centered ? 'text-center' : 'text-left';
@endphp

<div {{ $attributes->merge(['class' => 'mb-8 md:mb-10 ' . $alignmentClasses]) }}>
    @if($title)
        <h2 class="text-3xl md:text-4xl font-semibold tracking-[-0.03em] text-text-primary mb-3">{{ $title }}</h2>
    @endif
    
    @if($subtitle)
        <p class="text-body-lg text-text-secondary max-w-3xl {{ $centered ? 'mx-auto' : '' }}">{{ $subtitle }}</p>
    @endif
    
    @if($slot->isNotEmpty())
        <div class="mt-3">
            {{ $slot }}
        </div>
    @endif
</div>
