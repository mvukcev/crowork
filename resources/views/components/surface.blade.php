@props([
    'variant' => 'base',
    'elevation' => '1',
    'padding' => true,
    'rounded' => 'xl',
    'class' => '',
])

@php
    $variantClasses = [
        'base' => 'bg-white/80',
        'surface' => 'bg-white/70',
        'tinted' => 'bg-primary/5',
    ];
    
    $elevationClasses = [
        '0' => 'shadow-elevation-0',
        '1' => 'shadow-elevation-1',
        '2' => 'shadow-elevation-2',
        '3' => 'shadow-elevation-3',
    ];
    
    $paddingClass = $padding === true ? 'p-6 md:p-7' : ($padding === false ? '' : "p-{$padding}");
    $roundedClass = "rounded-{$rounded}";
    
    $baseClasses = [
        $variantClasses[$variant] ?? $variantClasses['base'],
        $elevationClasses[$elevation] ?? $elevationClasses['1'],
        $paddingClass,
        $roundedClass,
        'border border-white/80',
        'backdrop-blur-xl',
        'transition-all duration-normal',
    ];
    
    $combinedClasses = implode(' ', array_filter($baseClasses)) . ' ' . $class;
@endphp

<div {{ $attributes->merge(['class' => $combinedClasses]) }}>
    {{ $slot }}
</div>
