@props([
    'title' => null,
    'subtitle' => null,
    'centered' => false,
    'spacing' => 'normal',
])

@php
    $spacingClasses = [
        'tight' => 'mb-6',
        'normal' => 'mb-8',
        'relaxed' => 'mb-12',
    ];
    
    $alignClass = $centered ? 'text-center' : '';
@endphp

<div class="{{ $spacingClasses[$spacing] ?? $spacingClasses['normal'] }}">
    @if($title || $subtitle)
        <div class="{{ $alignClass }}">
            @if($title)
                <h2 class="text-title-2 font-semibold text-text-primary mb-3">
                    {{ $title }}
                </h2>
            @endif
            
            @if($subtitle)
                <p class="text-body text-text-secondary {{ $centered ? 'max-w-3xl mx-auto' : 'max-w-3xl' }}">
                    {{ $subtitle }}
                </p>
            @endif
            
            @isset($actions)
                <div class="mt-4 {{ $centered ? 'flex justify-center gap-3' : 'flex gap-3' }}">
                    {{ $actions }}
                </div>
            @endisset
        </div>
    @endif
    
    @isset($content)
        <div class="mt-8">
            {{ $content }}
        </div>
    @else
        {{ $slot }}
    @endisset
</div>
