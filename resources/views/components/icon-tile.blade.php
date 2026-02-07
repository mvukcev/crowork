@props([
    'tone' => 'primary',
    'size' => 'md',
])

@php
    $toneClasses = [
        'primary' => 'bg-gradient-to-br from-primary-light to-primary/10',
        'secondary' => 'bg-gradient-to-br from-secondary-light to-secondary/10',
        'accent' => 'bg-gradient-to-br from-accent-light to-accent/10',
        'success' => 'bg-gradient-to-br from-success-light to-success/10',
        'neutral' => 'bg-gradient-to-br from-surface-light to-surface-secondary',
    ];
    
    $toneIconClasses = [
        'primary' => 'text-primary',
        'secondary' => 'text-secondary',
        'accent' => 'text-accent',
        'success' => 'text-success',
        'neutral' => 'text-text-primary',
    ];
    
    $sizeClasses = [
        'sm' => 'w-10 h-10',
        'md' => 'w-12 h-12',
        'lg' => 'w-16 h-16',
        'xl' => 'w-20 h-20',
    ];
    
    $iconSizeClasses = [
        'sm' => 'w-5 h-5',
        'md' => 'w-6 h-6',
        'lg' => 'w-8 h-8',
        'xl' => 'w-10 h-10',
    ];
    
    $baseClasses = [
        'rounded-control',
        'flex items-center justify-center',
        'shadow-e1',
        'transition-all duration-160',
        'fluent-hover-lift',
        $toneClasses[$tone] ?? $toneClasses['primary'],
        $sizeClasses[$size] ?? $sizeClasses['md'],
    ];
    
    $combinedClasses = implode(' ', $baseClasses);
    $iconClasses = ($iconSizeClasses[$size] ?? $iconSizeClasses['md']) . ' ' . ($toneIconClasses[$tone] ?? $toneIconClasses['primary']);
@endphp

<div {{ $attributes->merge(['class' => $combinedClasses]) }}>
    <svg class="{{ $iconClasses }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        {{ $slot }}
    </svg>
</div>
