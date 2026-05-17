@props([
    'title',
    'provider' => null,
    'provider_logo_url' => null,
    'city' => null,
    'is_online' => false,
    'has_certificate' => false,
    'is_beginner_friendly' => false,
    'start_date' => null,
    'price_cents' => null,
    'currency' => 'EUR',
    'posted_at' => null,
    'href' => '#',
])

@php
    $providerName = trim((string) ($provider ?: __('educations.provider_fallback')));
    $logoInitials = collect(preg_split('/\\s+/', $providerName))
        ->filter()
        ->take(2)
        ->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))
        ->join('');
    if ($logoInitials === '') {
        $logoInitials = 'CW';
    }

    $currencyCode = strtoupper((string) $currency);
    $currencySymbol = $currencyCode === 'EUR' ? '€' : $currencyCode . ' ';
    $pricePrimary = !is_null($price_cents)
        ? $currencySymbol . number_format(((float) $price_cents) / 100, 0)
        : __('educations.contact_provider');

    $locationText = $is_online
        ? __('educations.online_program')
        : ($city ?: __('educations.country_fallback'));

    $startDateText = $start_date ? __('educations.starts_on', ['date' => \Carbon\Carbon::parse($start_date)->translatedFormat('j M Y')]) : null;
    $postedText = $posted_at ? __('educations.posted_short', ['time' => \Carbon\Carbon::parse($posted_at)->diffForHumans()]) : null;
@endphp

<article class="cw-listing-card cw-listing-card-education h-full">
    <a href="{{ $href }}" class="cw-listing-card-inner">
        <div class="cw-listing-card-top">
            <div class="min-w-0">
                <p class="cw-listing-company">{{ $providerName }}</p>
                <p class="cw-listing-location">{{ $locationText }}</p>
            </div>
            <div class="cw-employer-logo" aria-label="{{ $providerName }}">
                @if($provider_logo_url)
                    <img src="{{ $provider_logo_url }}" alt="{{ $providerName }} logo" class="h-full w-full object-cover" loading="lazy" decoding="async" width="72" height="72" data-cw-logo-image data-cw-fallback-text="{{ $logoInitials }}" data-cw-fallback-label="{{ $providerName }}">
                @else
                    <span>{{ $logoInitials }}</span>
                @endif
            </div>
        </div>

        <div class="cw-listing-middle">
            <h3 class="cw-listing-title">{{ $title }}</h3>

            <div class="cw-listing-salary" aria-label="{{ __('educations.price_label') }}">
                <p class="cw-listing-salary-primary">{{ $pricePrimary }}</p>
                <p class="cw-listing-salary-secondary">{{ __('educations.program_fee_label') }}</p>
            </div>

            <div class="cw-listing-meta">
                @if($startDateText)
                    <span>{{ $startDateText }}</span>
                @endif
                @if($postedText)
                    <span>{{ $postedText }}</span>
                @endif
            </div>
        </div>

        <div class="cw-listing-bottom mt-auto">
            <div class="cw-listing-chip-row">
                @if($is_online)
                    <span class="cw-listing-chip">{{ __('educations.chip_online') }}</span>
                @endif
                @if($has_certificate)
                    <span class="cw-listing-chip">{{ __('educations.chip_certificate') }}</span>
                @endif
                @if($is_beginner_friendly)
                    <span class="cw-listing-chip">{{ __('educations.chip_beginner_friendly') }}</span>
                @endif
            </div>

            <div class="cw-listing-actions">
                <span class="cw-card-cta-primary">{{ __('educations.view_program') }}</span>
            </div>
        </div>
    </a>
</article>
