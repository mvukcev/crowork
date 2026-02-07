@props([
    'tone' => 'neutral',
    'size' => 'md',
    'icon' => null,
    'dismissible' => false,
])

@php
$toneClasses = [
    'neutral' => 'bg-surface-tinted text-text-secondary border-border/30',
    'info' => 'bg-info-50 text-info-600 border-info-100/50',
    'success' => 'bg-success-50 text-success-600 border-success-100/50',
    'warning' => 'bg-warning-50 text-warning-600 border-warning-100/50',
    'danger' => 'bg-danger-50 text-danger-600 border-danger-100/50',
];

$sizeClasses = [
    'sm' => 'px-2.5 py-1 text-caption gap-1',
    'md' => 'px-3 py-1.5 text-body-sm gap-1.5',
    'lg' => 'px-4 py-2 text-body gap-2',
];

$classes = $toneClasses[$tone] ?? $toneClasses['neutral'];
$sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center font-medium border rounded-full transition-all duration-normal {$classes} {$sizeClass}"]) }}>
    @if($icon)
        {!! $icon !!}
    @endif
    
    {{ $slot }}
    
    @if($dismissible)
        <button type="button" class="ml-1 -mr-0.5 hover:bg-black/10 rounded-full p-0.5 transition-colors duration-normal">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
    @endif
</span>
