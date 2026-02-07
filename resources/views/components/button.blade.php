@props([
    'variant' => 'primary',
    'size' => 'md',
    'href' => null,
    'type' => 'button',
    'disabled' => false,
])

@php
    $baseClasses = 'inline-flex items-center justify-center font-semibold rounded-xl transition-all duration-normal focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed active:scale-[0.98]';
    
    $variantClasses = [
        'primary' => 'bg-primary text-white hover:bg-primary-hover active:bg-primary-pressed shadow-sm hover:shadow-md',
        'secondary' => 'bg-secondary text-white hover:bg-secondary-hover active:bg-secondary-pressed shadow-sm hover:shadow-md',
        'accent' => 'bg-accent text-white hover:bg-accent-hover active:bg-accent-pressed shadow-sm hover:shadow-md',
        'success' => 'bg-success text-white hover:bg-success-hover active:bg-success-pressed shadow-sm hover:shadow-md',
        'warning' => 'bg-warning text-text-primary hover:bg-warning-hover active:bg-warning-pressed shadow-sm hover:shadow-md',
        'danger' => 'bg-danger text-white hover:bg-danger-hover active:bg-danger-pressed shadow-sm hover:shadow-md',
        'subtle' => 'bg-control-fill text-text-primary hover:bg-control-fill-hover active:bg-control-fill-pressed border border-border/50 hover:border-border',
        'ghost' => 'bg-transparent text-text-primary hover:bg-control-fill/80 active:bg-control-fill-hover',
        'outline' => 'bg-transparent border-2 border-primary text-primary hover:bg-primary/10 hover:border-primary-hover active:bg-primary/15 active:scale-[0.98]',
    ];
    
    $sizeClasses = [
        'sm' => 'px-4 py-2 text-body-sm min-h-[36px]',
        'md' => 'px-5 py-2.5 text-body min-h-[40px]',
        'lg' => 'px-6 py-3 text-body-lg min-h-[48px]',
        'xl' => 'px-8 py-4 text-subtitle min-h-[52px]',
    ];
    
    $classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md']);
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
