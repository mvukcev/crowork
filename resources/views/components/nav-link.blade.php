@props([
    'href' => '#',
    'active' => false,
])

<a href="{{ $href }}" {{ $attributes->merge(['class' => ($active ? 'cw-nav-link cw-nav-link-active' : 'cw-nav-link')]) }}>
    {{ $slot }}
</a>
