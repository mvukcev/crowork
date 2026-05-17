@php
    use App\Services\AnalyticsConfigService;
    use App\Services\ConsentConfigService;
@endphp

{{-- Google Tag Manager script - Injected early in head --}}
@if(AnalyticsConfigService::shouldInjectGTM() && ConsentConfigService::isAnalyticsAllowed())
    <!-- Google Tag Manager -->
    <script>
        (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','{{ AnalyticsConfigService::getGoogleTagManagerId() }}');
    </script>
    <!-- End Google Tag Manager -->
@endif

{{-- Google Analytics 4 script --}}
@if(AnalyticsConfigService::shouldInjectGA4() && ConsentConfigService::isAnalyticsAllowed())
    <!-- Google Analytics 4 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ AnalyticsConfigService::getGoogleTagId() }}"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '{{ AnalyticsConfigService::getGoogleTagId() }}', {
            @if(AnalyticsConfigService::isDebugMode())
            'debug_mode': true,
            @endif
        });
    </script>
    <!-- End Google Analytics 4 -->
@endif

{{-- Meta Pixel script --}}
@include('components.meta-pixel')
