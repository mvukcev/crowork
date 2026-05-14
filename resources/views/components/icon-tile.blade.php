@props([
    'tone' => 'primary',
    'size' => 'md',
])

@php
    $toneClasses = [
        'primary' => 'bg-blue-50 text-blue-700 border-blue-200',
        'secondary' => 'bg-slate-50 text-slate-700 border-slate-200',
        'accent' => 'bg-amber-50 text-amber-700 border-amber-200',
        'success' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'neutral' => 'bg-white text-slate-700 border-slate-200',
    ];

    $sizeClasses = [
        'sm' => 'w-10 h-10',
        'md' => 'w-12 h-12',
        'lg' => 'w-16 h-16',
        'xl' => 'w-20 h-20',
    ];

    $iconSize = [
        'sm' => 'w-5 h-5',
        'md' => 'w-6 h-6',
        'lg' => 'w-8 h-8',
        'xl' => 'w-10 h-10',
    ];
@endphp

<div {{ $attributes->merge(['class' => 'rounded-xl border inline-flex items-center justify-center cw-hover-lift ' . ($toneClasses[$tone] ?? $toneClasses['primary']) . ' ' . ($sizeClasses[$size] ?? $sizeClasses['md'])]) }}>
    <svg class="{{ $iconSize[$size] ?? $iconSize['md'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
        {{ $slot }}
    </svg>
</div>
