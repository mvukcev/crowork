@props([
    'title',
    'provider' => null,
    'city' => null,
    'is_online' => false,
    'start_date' => null,
    'price_cents' => null,
    'currency' => 'EUR',
    'posted_at' => null,
    'href' => '#',
])

@php
    $priceDisplay = !is_null($price_cents) ? $currency . ' ' . number_format($price_cents / 100, 2) : 'Contact provider';
@endphp

<a href="{{ $href }}" class="cw-surface p-5 block cw-hover-lift">
    <p class="text-xs text-slate-500 mb-1">{{ $provider ?: 'Education provider' }}</p>
    <h3 class="text-lg font-semibold text-slate-900 leading-tight mb-2">{{ $title }}</h3>
    <p class="text-sm text-slate-700 mb-3">{{ $is_online ? 'Online program' : ($city ?: 'Croatia') }}</p>

    <div class="flex flex-wrap gap-2">
        <span class="cw-chip">{{ $priceDisplay }}</span>
        @if($start_date)
            <span class="cw-chip">Starts {{ \Carbon\Carbon::parse($start_date)->format('M j, Y') }}</span>
        @endif
        @if($posted_at)
            <span class="cw-chip">Posted {{ \Carbon\Carbon::parse($posted_at)->diffForHumans() }}</span>
        @endif
    </div>
</a>
