@props([
    'variant' => 'base',
    'elevation' => '1',
    'padding' => true,
    'rounded' => 'xl',
    'class' => '',
])

@php
    $variantClasses = [
        'base' => 'bg-surface-base',
        'surface' => 'bg-surface-secondary',
        'tinted' => 'bg-surface-tinted',
    ];
    
    $elevationClasses = [
        '0' => 'shadow-elevation-0',
        '1' => 'shadow-elevation-1',
        '2' => 'shadow-elevation-2',
        '3' => 'shadow-elevation-3',
    ];
    
    $paddingClass = $padding === true ? 'p-6' : ($padding === false ? '' : "p-{$padding}");
    $roundedClass = "rounded-{$rounded}";
    
    $baseClasses = [
        $variantClasses[$variant] ?? $variantClasses['base'],
        $elevationClasses[$elevation] ?? $elevationClasses['1'],
        $paddingClass,
        $roundedClass,
        'border border-border-subtle',
        'transition-all duration-200',
    ];
    
    $combinedClasses = implode(' ', array_filter($baseClasses)) . ' ' . $class;
@endphp

<div {{ $attributes->merge(['class' => $combinedClasses]) }}>
    {{ $slot }}
</div>
