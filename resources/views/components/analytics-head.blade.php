@php
    use App\Models\Setting;
    use App\Services\AnalyticsConfigService;
    use App\Services\ConsentConfigService;
    use App\Services\MetaPixelConfigService;

    $analyticsAllowed = ConsentConfigService::isAnalyticsAllowed();
    $marketingAllowed = ConsentConfigService::isMarketingAllowed();

    $gtmId = AnalyticsConfigService::getGoogleTagManagerId();
    $gaId = AnalyticsConfigService::getGoogleTagId();
    $shouldInjectGtm = AnalyticsConfigService::shouldInjectGTM();
    $shouldInjectGa4 = AnalyticsConfigService::shouldInjectGA4();

    $metaEnabled = MetaPixelConfigService::isTrackingEnabled();
    $metaBrowserEnabled = Setting::getBool('meta_browser_enabled', true);
    $metaCapiEnabled = Setting::getBool('meta_capi_enabled', true);
    $metaPixelId = MetaPixelConfigService::getPixelId();
    $metaDatasetId = MetaPixelConfigService::getDatasetId();

    $trackingConfig = [
        'google' => [
            'analytics_enabled' => AnalyticsConfigService::isAnalyticsEnabled(),
            'gtm_id' => $gtmId,
            'ga_measurement_id' => $gaId,
            'analytics_allowed' => $analyticsAllowed,
        ],
        'meta' => [
            'tracking_enabled' => $metaEnabled,
            'browser_enabled' => $metaBrowserEnabled,
            'capi_enabled' => $metaCapiEnabled,
            'pixel_id' => $metaPixelId,
            'dataset_id' => $metaDatasetId,
            'marketing_allowed' => $marketingAllowed,
        ],
    ];
@endphp

<script type="application/json" id="cw-tracking-config">@json($trackingConfig)</script>

@if($shouldInjectGtm || $shouldInjectGa4)
    <script>
        window.dataLayer = window.dataLayer || [];
        window.gtag = window.gtag || function(){dataLayer.push(arguments);};

        gtag('consent', 'default', {
            analytics_storage: @json($analyticsAllowed ? 'granted' : 'denied'),
            ad_storage: @json($marketingAllowed ? 'granted' : 'denied'),
            ad_user_data: @json($marketingAllowed ? 'granted' : 'denied'),
            ad_personalization: @json($marketingAllowed ? 'granted' : 'denied')
        });
    </script>
@endif

{{-- Google Tag Manager script - Injected early in head --}}
@if($shouldInjectGtm)
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
@if($shouldInjectGa4)
    <!-- Google Analytics 4 -->
    <script async src="https://www.googletagmanager.com/gtag/js?id={{ AnalyticsConfigService::getGoogleTagId() }}"></script>
    <script>
        gtag('js', new Date());
        gtag('config', '{{ AnalyticsConfigService::getGoogleTagId() }}', {
            'send_page_view': @json($analyticsAllowed),
            @if(AnalyticsConfigService::isDebugMode())
            'debug_mode': true,
            @endif
        });
    </script>
    <!-- End Google Analytics 4 -->
@endif

{{-- Meta Pixel script --}}
@include('components.meta-pixel')
