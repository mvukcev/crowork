@props([
    'href' => '#',
    'active' => false,
])

@php
    $classes = 'px-3 py-2 text-body-sm font-medium rounded-md transition-colors duration-normal';
    
    if ($active) {
        $classes .= ' bg-primary-light text-primary';
    } else {
        $classes .= ' text-text-secondary hover:text-text-primary hover:bg-surface';
    }
@endphp

<a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
