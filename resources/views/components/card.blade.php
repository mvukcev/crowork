@props([
    'title' => null,
    'href' => null,
])

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => 'cw-surface block']) }}>
        @if($title)
            <div class="px-5 py-4 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">{{ $title }}</h3>
            </div>
        @endif
        <div class="p-5 md:p-6">{{ $slot }}</div>
    </a>
@else
    <div {{ $attributes->merge(['class' => 'cw-surface']) }}>
        @if($title)
            <div class="px-5 py-4 border-b border-slate-200">
                <h3 class="text-lg font-semibold text-slate-900">{{ $title }}</h3>
            </div>
        @endif
        <div class="p-5 md:p-6">{{ $slot }}</div>
    </div>
@endif
