<?php

namespace App\Services;

use App\Models\Setting;

class MetaPixelConfigService
{
    public static function isTrackingEnabled(): bool
    {
        return Setting::getBool('meta_tracking_enabled', false);
    }

    public static function getPixelId(): ?string
    {
        return Setting::getString('meta_pixel_id');
    }

    public static function getAccessToken(): ?string
    {
        return Setting::getValue('meta_conversions_api_access_token');
    }

    public static function getTestEventCode(): ?string
    {
        return Setting::getString('meta_test_event_code');
    }

    public static function getDatasetId(): ?string
    {
        return Setting::getString('meta_dataset_id');
    }

    public static function getApiVersion(): string
    {
        return Setting::getString('meta_api_version', 'v18.0');
    }

    public static function isDebugMode(): bool
    {
        return Setting::getBool('meta_debug_mode', false);
    }

    public static function shouldInjectPixel(): bool
    {
        return self::isTrackingEnabled() && ! empty(self::getPixelId());
    }

    public static function canUseCAPI(): bool
    {
        return self::isTrackingEnabled() && ! empty(self::getAccessToken());
    }

    public static function getApiEndpoint(): string
    {
        $version = self::getApiVersion();
        return "https://graph.facebook.com/{$version}/";
    }
}
