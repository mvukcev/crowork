<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @php
            $guestTitle = ($title ?? __('auth.page_title_default')).' - '.config('app.name', 'CroWork');
            $guestDescription = $description ?? __('seo.auth.access_description');
            $guestCanonical = $canonical ?? url()->current();
            $guestRobots = $robots ?? 'noindex,nofollow';
        @endphp

        <title>{{ $guestTitle }}</title>
        <meta name="description" content="{{ $guestDescription }}">
        <meta name="robots" content="{{ $guestRobots }}">
        <link rel="canonical" href="{{ $guestCanonical }}">
        <meta property="og:title" content="{{ $guestTitle }}">
        <meta property="og:description" content="{{ $guestDescription }}">
        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ $guestCanonical }}">
        <meta property="og:image" content="{{ asset('assets/branding/CW-Logo-Dark.png') }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $guestTitle }}">
        <meta name="twitter:description" content="{{ $guestDescription }}">
        <meta name="twitter:image" content="{{ asset('assets/branding/CW-Logo-Dark.png') }}">
        <link rel="icon" type="image/svg+xml" href="{{ asset('assets/branding/CW-Favicon.svg') }}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/branding/CW-Favicon.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('assets/branding/CW-Favicon.png') }}">

        <x-theme-init />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Analytics & Tracking -->
        @include('components.analytics-head')

        @php
            $queuedTrackEvents = session()->pull('cw_track_queue', []);
        @endphp
        @if(is_array($queuedTrackEvents) && count($queuedTrackEvents) > 0)
            <script>
                window.__cwTrackQueue = @json($queuedTrackEvents);
            </script>
        @endif
    </head>
    @php
        $consentRequired = \App\Services\ConsentConfigService::isConsentRequired();
        $analyticsEnabled = \App\Services\AnalyticsConfigService::isAnalyticsEnabled();
        $marketingEnabled = \App\Services\MetaPixelConfigService::isTrackingEnabled();
        $trackDebug = app()->environment('local') || config('app.debug');
    @endphp
    <body
        class="h-full cw-page overflow-x-hidden"
        data-cw-consent-required="{{ $consentRequired ? '1' : '0' }}"
        data-cw-analytics-enabled="{{ $analyticsEnabled ? '1' : '0' }}"
        data-cw-marketing-enabled="{{ $marketingEnabled ? '1' : '0' }}"
        data-cw-track-debug="{{ $trackDebug ? '1' : '0' }}"
    >
        <div class="min-h-screen flex flex-col cw-page-shell">
            <div class="cw-page-ambient cw-organic-bg" aria-hidden="true">
                <span class="cw-orb cw-orb-blue hidden md:block" style="width: 360px; height: 360px; left: -120px; top: 5rem;"></span>
                <span class="cw-orb cw-orb-orange hidden md:block" style="width: 300px; height: 300px; right: -90px; top: 12rem;"></span>
                <span class="cw-orb cw-orb-yellow hidden lg:block" style="width: 180px; height: 180px; right: 15%; bottom: 5rem;"></span>
            </div>
            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8 lg:py-8 cw-section-atmosphere">
                <span class="cw-corner-glow cw-orb-blue hidden lg:block" style="width: 360px; height: 360px; top: -110px; right: 14%;"></span>
                <span class="cw-corner-glow cw-orb-cyan hidden lg:block" style="width: 300px; height: 300px; bottom: -130px; left: 9%;"></span>

                <div class="cw-auth-shell mx-auto">
                    <aside class="cw-auth-aside hidden lg:block">
                        <div class="relative h-full">
                            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 text-slate-900">
                                <img
                                    src="{{ asset('assets/branding/CW-Logo-Dark.svg') }}"
                                    alt="CroWork"
                                    class="h-6 w-auto cw-logo-on-light"
                                    onerror="this.style.display='none';"
                                >
                                <img
                                    src="{{ asset('assets/branding/CW-Logo-Light.svg') }}"
                                    alt="CroWork"
                                    class="h-6 w-auto cw-logo-on-dark"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';"
                                >
                                <span class="hidden text-base font-semibold">CroWork</span>
                            </a>
                            <p class="text-[2rem] leading-tight font-semibold text-slate-800 mt-12 max-w-[260px]">{{ __('auth.marketing_title') }}</p>
                            <p class="text-sm text-slate-600 mt-4 max-w-[260px]">{{ __('auth.marketing_subtitle') }}</p>

                            <div class="absolute inset-x-0 bottom-0 h-[240px] rounded-2xl bg-white/55 border border-white/70 overflow-hidden">
                                <span class="cw-orb cw-orb-violet" style="width: 180px; height: 180px; left: -40px; bottom: 14px;"></span>
                                <span class="cw-orb cw-orb-orange" style="width: 140px; height: 140px; right: -30px; top: 16px;"></span>
                                <span class="cw-orb cw-orb-yellow" style="width: 120px; height: 120px; right: 58px; bottom: -28px;"></span>
                            </div>
                        </div>
                    </aside>

                    <section class="cw-auth-main">
                        <div class="flex items-center justify-between mb-5 lg:hidden">
                            <a href="{{ url('/') }}" class="inline-flex items-center">
                                <img
                                    src="{{ asset('assets/branding/CW-Logo-Dark.svg') }}"
                                    alt="CroWork"
                                    class="h-5 w-auto cw-logo-on-light"
                                    onerror="this.style.display='none';"
                                >
                                <img
                                    src="{{ asset('assets/branding/CW-Logo-Light.svg') }}"
                                    alt="CroWork"
                                    class="h-5 w-auto cw-logo-on-dark"
                                    onerror="this.style.display='none'; this.nextElementSibling.style.display='inline';"
                                >
                                <span class="hidden text-[15px] font-semibold text-slate-900">CroWork</span>
                            </a>
                            <a href="{{ route('home') }}" class="text-sm text-slate-600 hover:text-slate-900">{{ __('auth.back_home') }}</a>
                        </div>
                        <div class="max-w-[520px] mx-auto">
                            {{ $slot }}
                        </div>
                    </section>
                </div>
            </main>

            <div class="cw-container pb-7 pt-2">
                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 text-sm text-slate-600">
                    <p class="m-0">{{ __('footer.copyright', ['year' => date('Y')]) }}</p>
                    <div class="flex items-center gap-5">
                        <a href="{{ url('/about') }}" class="hover:text-slate-900 transition-colors">{{ __('navigation.about') }}</a>
                        <a href="{{ url('/contact') }}" class="hover:text-slate-900 transition-colors">{{ __('auth.contact') }}</a>
                        <a href="{{ url('/privacy') }}" class="hover:text-slate-900 transition-colors">{{ __('footer.privacy') }}</a>
                        <a href="{{ url('/terms') }}" class="hover:text-slate-900 transition-colors">{{ __('footer.terms') }}</a>
                    </div>
                </div>
            </div>

            <section class="cw-cookie-banner cw-soft-reveal" data-cw-cookie-banner hidden>
                <div class="cw-cookie-inner">
                    <p class="cw-cookie-text">
                        {!! __('footer.cookie.text', [
                            'link' => '<a href="' . route('cookies') . '" class="font-medium text-slate-900 underline">' . __('footer.cookie.link_label') . '</a>',
                        ]) !!}
                    </p>
                    <div class="cw-cookie-actions">
                        <button type="button" class="cw-button-secondary" data-cw-cookie-choice="required">{{ __('footer.cookie.required_only') }}</button>
                        <button type="button" class="cw-button-primary" data-cw-cookie-choice="all">{{ __('footer.cookie.allow_all') }}</button>
                    </div>
                </div>
            </section>
        </div>

        @include('components.analytics-noscript')
    </body>
</html>
