@php
    use App\Models\Setting;
    use App\Services\AnalyticsConfigService;
    use App\Services\ConsentConfigService;
    use App\Services\MetaPixelConfigService;

    $analyticsAllowed = ConsentConfigService::isAnalyticsAllowed();
    $marketingAllowed = ConsentConfigService::isMarketingAllowed();

    $gtmId = AnalyticsConfigService::getGoogleTagManagerId();
    $gaId = AnalyticsConfigService::getGoogleTagId();
    $metaEnabled = MetaPixelConfigService::isTrackingEnabled();
    $metaBrowserEnabled = Setting::getBool('meta_browser_enabled', true);
    $metaCapiEnabled = Setting::getBool('meta_capi_enabled', true);
    $metaPixelId = MetaPixelConfigService::getPixelId();
    $metaDatasetId = MetaPixelConfigService::getDatasetId();
    $metaTestEventCode = MetaPixelConfigService::getTestEventCode();

    $trackingConfig = [
        'google' => [
            'analytics_enabled' => AnalyticsConfigService::isAnalyticsEnabled(),
            'gtm_id' => $gtmId,
            'ga_measurement_id' => $gaId,
            'debug_mode' => AnalyticsConfigService::isDebugMode(),
            'analytics_allowed' => $analyticsAllowed,
        ],
        'meta' => [
            'tracking_enabled' => $metaEnabled,
            'browser_enabled' => $metaBrowserEnabled,
            'capi_enabled' => $metaCapiEnabled,
            'pixel_id' => $metaPixelId,
            'dataset_id' => $metaDatasetId,
            'test_event_code' => $metaTestEventCode,
            'marketing_allowed' => $marketingAllowed,
        ],
    ];
@endphp

<script type="application/json" id="cw-tracking-config">@json($trackingConfig)</script>
