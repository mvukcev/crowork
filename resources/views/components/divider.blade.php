@props([
    'variant' => 'default',
    'orientation' => 'horizontal',
])

@php
$variantClasses = [
    'default' => 'border-stroke-default',
    'subtle' => 'border-stroke-subtle',
];

$orientationClasses = [
    'horizontal' => 'w-full border-t border-hairline',
    'vertical' => 'h-full border-l border-hairline',
];

$classes = $variantClasses[$variant] ?? $variantClasses['default'];
$orientationClass = $orientationClasses[$orientation] ?? $orientationClasses['horizontal'];
@endphp

<div {{ $attributes->merge(['class' => "{$orientationClass} {$classes}"]) }} role="separator"></div>
