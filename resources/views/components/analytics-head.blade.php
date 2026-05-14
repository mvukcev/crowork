@php
    use App\Services\AnalyticsConfigService;
    use App\Services\MetaPixelConfigService;
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
@if(MetaPixelConfigService::shouldInjectPixel() && ConsentConfigService::isMarketingAllowed())
    <!-- Meta Pixel -->
    <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '{{ MetaPixelConfigService::getPixelId() }}');
        fbq('track', 'PageView');
        @if(MetaPixelConfigService::isDebugMode())
        fbq('set', 'testEventCode', '{{ MetaPixelConfigService::getTestEventCode() }}');
        @endif
    </script>
    <!-- End Meta Pixel -->
@endif
