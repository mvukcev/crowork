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
    {{ $attributes->merge(['class' => 'block bg-white border-0 rounded-2xl p-6 shadow-elevation-1 hover:shadow-elevation-2 transition-all duration-normal group hover:-translate-y-1']) }}
>
    <article class="space-y-4">
        <!-- Title -->
        <h3 class="text-lg font-semibold text-text-primary group-hover:text-primary transition-colors duration-normal">
            {{ $title }}
        </h3>
        
        <!-- Provider -->
        @if($provider)
            <div class="flex items-center text-body-sm text-text-secondary">
                <svg class="w-4 h-4 mr-1.5 text-text-tertiary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                {{ $provider }}
            </div>
        @endif
        
        <!-- Location & Start Date -->
        <div class="flex items-center flex-wrap gap-4 text-body-sm text-text-secondary">
            <!-- Location -->
            <div class="flex items-center">
                @if($is_online)
                    <svg class="w-4 h-4 mr-1.5 text-text-tertiary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path>
                    </svg>
                @else
                    <svg class="w-4 h-4 mr-1.5 text-text-tertiary flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                @endif
                <span>{{ $locationDisplay }}</span>
            </div>
            
            <!-- Start Date -->
            @if($startDateDisplay)
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-1.5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span>{{ $startDateDisplay }}</span>
                </div>
            @endif
        </div>
        
        <!-- Price -->
        <div class="flex items-center text-body-sm text-text-primary font-medium">
            <svg class="w-4 h-4 mr-1.5 text-text-tertiary" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ $priceDisplay }}
        </div>
        
        <!-- Posted Time -->
        @if($postedDisplay)
            <div class="pt-2 border-t border-border">
                <span class="text-caption text-text-tertiary">Posted {{ $postedDisplay }}</span>
            </div>
        @endif
    </article>
</a>
