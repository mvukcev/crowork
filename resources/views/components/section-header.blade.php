@props([
    'title' => null,
    'subtitle' => null,
    'centered' => false,
])

<div {{ $attributes->merge(['class' => 'mb-8 md:mb-10 ' . ($centered ? 'text-center' : 'text-left')]) }}>
    @if($title)
        <h2 class="cw-display text-3xl md:text-5xl mb-3">{{ $title }}</h2>
    @endif

    @if($subtitle)
        <p class="text-base text-slate-600 max-w-3xl {{ $centered ? 'mx-auto' : '' }}">{{ $subtitle }}</p>
    @endif

    @if($slot->isNotEmpty())
        <div class="mt-3">{{ $slot }}</div>
    @endif
</div>
