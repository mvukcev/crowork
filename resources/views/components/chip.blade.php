@props([
    'tone' => 'neutral',
])

@php
    $toneClasses = [
        'neutral' => 'cw-chip',
        'info' => 'cw-chip text-blue-700 bg-blue-50 border-blue-200',
        'success' => 'cw-chip text-emerald-700 bg-emerald-50 border-emerald-200',
        'warning' => 'cw-chip text-orange-700 bg-orange-50 border-orange-200',
        'danger' => 'cw-chip text-red-700 bg-red-50 border-red-200',
    ];
@endphp

<span {{ $attributes->merge(['class' => $toneClasses[$tone] ?? $toneClasses['neutral']]) }}>
    {{ $slot }}
</span>
