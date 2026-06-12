@php
    use App\Models\Setting;
    use App\Services\AnalyticsConfigService;
    use App\Services\ConsentConfigService;
    use App\Services\MetaPixelConfigService;

    $metaEnabled = MetaPixelConfigService::isTrackingEnabled();
    $pixelEnabled = Setting::getBool('meta_browser_enabled', true);
    $pixelId = (string) (MetaPixelConfigService::getPixelId() ?? '');
@endphp

{{-- Google Tag Manager (noscript) - Rendered if GTM is configured and enabled --}}
@if(AnalyticsConfigService::shouldInjectGTM() && ConsentConfigService::isAnalyticsAllowed())
    <!-- Google Tag Manager (noscript) -->
    <noscript>
        <iframe src="https://www.googletagmanager.com/ns.html?id={{ AnalyticsConfigService::getGoogleTagManagerId() }}"
                height="0" width="0" style="display:none;visibility:hidden"></iframe>
    </noscript>
    <!-- End Google Tag Manager (noscript) -->
@elseif(AnalyticsConfigService::shouldInjectGTM())
    <!-- Google Tag Manager configured but blocked until analytics consent is granted. -->
@endif

{{-- Meta Pixel (noscript) - Rendered if Pixel is configured and enabled --}}
@if($metaEnabled && $pixelEnabled && $pixelId !== '' && ConsentConfigService::isMarketingAllowed())
    <!-- Meta Pixel (noscript) -->
    <noscript>
        <img height="1" width="1" style="display:none" 
             src="https://www.facebook.com/tr?id={{ $pixelId }}&ev=PageView&noscript=1" />
    </noscript>
    <!-- End Meta Pixel (noscript) -->
@endif
