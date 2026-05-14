@props([
    'href' => '#',
    'label',
    'tone' => 'blue',
])

<a
    href="{{ $href }}"
    @class([
        'cw-filter-chip' => true,
        'cw-filter-chip-orange' => $tone === 'orange',
    ])
>
    <span>{{ $label }}</span>
    <svg viewBox="0 0 20 20" fill="currentColor" aria-hidden="true" class="h-3.5 w-3.5">
        <path fill-rule="evenodd" d="M4.22 4.22a.75.75 0 011.06 0L10 8.94l4.72-4.72a.75.75 0 111.06 1.06L11.06 10l4.72 4.72a.75.75 0 11-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 01-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 010-1.06z" clip-rule="evenodd" />
    </svg>
</a>
