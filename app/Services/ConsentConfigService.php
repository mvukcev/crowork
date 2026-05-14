<?php

namespace App\Services;

use App\Models\Setting;

class ConsentConfigService
{
    public static function isBannerEnabled(): bool
    {
        return Setting::getBool('cookie_banner_enabled', true);
    }

    public static function isConsentRequired(): bool
    {
        return Setting::getBool('consent_required', true);
    }

    public static function getCookieStatementUrl(): ?string
    {
        return Setting::getString('cookie_statement_url');
    }

    /**
     * Check if analytics tracking is allowed based on consent
     * If consent is not required, always allow. Otherwise check localStorage/cookie
     */
    public static function isAnalyticsAllowed(): bool
    {
        if (! self::isConsentRequired()) {
            return true;
        }

        // Client-side JavaScript will check consent in localStorage
        // Server needs to know the intent via cookie/session
        return request()->cookie('consent_analytics', false);
    }

    /**
     * Check if marketing tracking (Meta/etc) is allowed
     */
    public static function isMarketingAllowed(): bool
    {
        if (! self::isConsentRequired()) {
            return true;
        }

        return request()->cookie('consent_marketing', false);
    }
}
