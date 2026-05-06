@props([
    'title' => null,
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
    // Format price display
    $priceDisplay = 'Free / Not specified';
    if ($price_cents !== null && $price_cents > 0) {
        $priceEuros = $price_cents / 100;
        $currencySymbol = $currency === 'EUR' ? '€' : $currency;
        $priceDisplay = $currencySymbol . number_format($priceEuros, 0, '.', ',');
    }
    
    // Format location display
    $locationDisplay = $is_online ? 'Online' : ($city ?? 'Location TBD');
    
    // Format start date
    $startDateDisplay = null;
    if ($start_date) {
        if ($start_date instanceof \Carbon\Carbon) {
            $startDateDisplay = $start_date->format('M j, Y');
        } elseif (is_string($start_date)) {
            $startDateDisplay = \Carbon\Carbon::parse($start_date)->format('M j, Y');
        }
    }
    
    // Format posted time
    $postedDisplay = null;
    if ($posted_at) {
        if ($posted_at instanceof \Carbon\Carbon) {
            $postedDisplay = $posted_at->diffForHumans();
        } elseif (is_string($posted_at)) {
            $postedDisplay = \Carbon\Carbon::parse($posted_at)->diffForHumans();
        }
    }
@endphp

<a
    href="{{ $href }}"
    {{ $attributes->merge(['class' => 'premium-job-card group']) }}
>
    <article class="space-y-3.5">
        <div class="flex items-start justify-between gap-3">
            <p class="text-title-2 md:text-title-1 font-semibold text-primary leading-tight mb-0">{{ $priceDisplay }}</p>
            @if($postedDisplay)
                <time class="text-caption text-text-tertiary whitespace-nowrap">{{ $postedDisplay }}</time>
            @endif
        </div>

        <h3 class="text-title-2 font-semibold text-text-primary group-hover:text-primary transition-colors duration-normal mb-0">
            {{ $title }}
        </h3>

        @if($provider)
            <p class="text-body-sm font-medium text-text-primary mb-0">{{ $provider }}</p>
        @endif

        <div class="flex flex-wrap items-center gap-2 text-body-sm text-text-secondary">
            <span class="premium-chip">{{ $locationDisplay }}</span>
            @if($startDateDisplay)
                <span class="premium-chip">Starts {{ $startDateDisplay }}</span>
            @endif
        </div>

        <div class="pt-2 border-t border-border/40 flex items-center justify-between">
            <span class="text-caption text-text-tertiary">View details</span>
            <span class="text-body-sm font-semibold text-primary">Open</span>
        </div>
    </article>
</a>
