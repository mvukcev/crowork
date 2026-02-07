@props([
    'tone' => 'neutral',
    'variant' => null, // Legacy support
    'size' => 'md',
    'icon' => null,
])

@php
    // Map old variant to new tone for backwards compatibility
    if ($variant && !$tone) {
        $toneMap = [
            'default' => 'neutral',
            'primary' => 'info',
            'info' => 'info',
        ];
        $tone = $toneMap[$variant] ?? $variant;
    }
    
    $toneClasses = [
        'neutral' => 'bg-surface-secondary text-text-secondary border-border-subtle',
        'info' => 'bg-info-50 text-info-600 border-info-100',
        'success' => 'bg-success-50 text-success-600 border-success-100',
        'warning' => 'bg-warning-50 text-warning-600 border-warning-100',
        'danger' => 'bg-danger-50 text-danger-600 border-danger-100',
        'primary' => 'bg-primary-light text-primary border-primary',
        'secondary' => 'bg-secondary-light text-secondary border-secondary',
        'accent' => 'bg-accent-light text-accent border-accent',
    ];
    
    $sizeClasses = [
        'sm' => 'px-2 py-0.5 text-caption',
        'md' => 'px-2.5 py-1 text-body-sm',
        'lg' => 'px-3 py-1.5 text-body',
    ];
    
    $baseClasses = [
        'inline-flex items-center gap-1',
        'rounded-md',
        'border',
        'font-medium',
        'transition-colors duration-120',
        $toneClasses[$tone] ?? $toneClasses['neutral'],
        $sizeClasses[$size] ?? $sizeClasses['md'],
    ];
    
    $combinedClasses = implode(' ', $baseClasses);
@endphp

<span {{ $attributes->merge(['class' => $combinedClasses]) }}>
    @if($icon)
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            {!! $icon !!}
        </svg>
    @endif
    {{ $slot }}
</span>
