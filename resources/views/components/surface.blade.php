@props([
    'class' => '',
])

<div {{ $attributes->merge(['class' => 'cw-surface ' . $class]) }}>
    {{ $slot }}
</div>
