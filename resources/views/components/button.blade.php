@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'disabled' => false,
])

@php
    $variantClasses = [
        'primary' => 'cw-button-primary',
        'secondary' => 'cw-button-secondary',
        'accent' => 'cw-button-accent',
    ];

    $sizeClasses = [
        'sm' => 'px-3.5 py-2 text-xs',
        'md' => 'px-5 py-2.5 text-sm',
        'lg' => 'px-6 py-3 text-sm',
        'xl' => 'px-7 py-3.5 text-base',
    ];

    $classes = ($variantClasses[$variant] ?? $variantClasses['primary']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']) . ' cw-press';
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $disabled ? 'disabled' : '' }} {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif
