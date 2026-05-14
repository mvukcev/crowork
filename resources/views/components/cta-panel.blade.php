@props([
    'title' => null,
    'subtitle' => null,
    'centered' => true,
])

<div class="cw-surface p-6 md:p-8 {{ $centered ? 'text-center' : '' }}">
    @if($title)
        <h3 class="cw-display text-3xl md:text-4xl mb-3">{{ $title }}</h3>
    @endif

    @if($subtitle)
        <p class="text-base text-slate-600 mb-6">{{ $subtitle }}</p>
    @endif

    @isset($actions)
        <div class="flex {{ $centered ? 'justify-center' : '' }} gap-2 flex-wrap">{{ $actions }}</div>
    @else
        {{ $slot }}
    @endisset
</div>
