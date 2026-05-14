<?php

namespace App\Services;

use App\Models\Setting;

class AnalyticsConfigService
{
    public static function isAnalyticsEnabled(): bool
    {
        return Setting::getBool('analytics_enabled', false);
    }

    public static function getGoogleTagManagerId(): ?string
    {
        return Setting::getString('google_tag_manager_id');
    }

    public static function getGoogleTagId(): ?string
    {
        return Setting::getString('google_tag_id');
    }

    public static function isDebugMode(): bool
    {
        return Setting::getBool('analytics_debug_mode', false);
    }

    public static function shouldInjectGTM(): bool
    {
        return self::isAnalyticsEnabled() && ! empty(self::getGoogleTagManagerId());
    }

    public static function shouldInjectGA4(): bool
    {
        return self::isAnalyticsEnabled() && ! empty(self::getGoogleTagId());
    }
}
