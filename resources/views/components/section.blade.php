@props([
    'title' => null,
    'subtitle' => null,
    'centered' => false,
])

<div class="mb-10">
    @if($title || $subtitle)
        <div class="{{ $centered ? 'text-center' : '' }} mb-6">
            @if($title)
                <h2 class="cw-display text-3xl md:text-5xl">{{ $title }}</h2>
            @endif

            @if($subtitle)
                <p class="text-base text-slate-600 mt-3 {{ $centered ? 'max-w-3xl mx-auto' : 'max-w-3xl' }}">{{ $subtitle }}</p>
            @endif

            @isset($actions)
                <div class="mt-4 flex {{ $centered ? 'justify-center' : '' }} gap-2">
                    {{ $actions }}
                </div>
            @endisset
        </div>
    @endif

    {{ $slot }}
</div>
