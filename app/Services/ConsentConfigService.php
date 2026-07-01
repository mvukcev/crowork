<?php

namespace App\Services;

use App\Models\ConsentHistory;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class ConsentConfigService
{
    public const CONSENT_TYPE_ANALYTICS = CookieConsentService::CONSENT_TYPE_ANALYTICS;
    public const CONSENT_TYPE_MARKETING = CookieConsentService::CONSENT_TYPE_MARKETING;

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
        return self::hasAnalyticsConsent();
    }

    /**
     * Check if marketing tracking (Meta/etc) is allowed
     */
    public static function isMarketingAllowed(): bool
    {
        return self::hasMarketingConsent();
    }

    public static function isFunctionalAllowed(): bool
    {
        return true;
    }

    public static function hasAnalyticsConsent(?Request $request = null, ?User $user = null): bool
    {
        if (! self::isConsentRequired()) {
            return true;
        }

        $request ??= request();
        $cookieValue = self::parseConsentCookie($request?->cookie('consent_analytics'));
        if ($cookieValue !== null) {
            return $cookieValue;
        }

        $user ??= $request?->user();
        $historyValue = self::latestUserConsent($user, self::CONSENT_TYPE_ANALYTICS);
        if ($historyValue !== null) {
            return $historyValue;
        }

        return false;
    }

    public static function hasMarketingConsent(?Request $request = null, ?User $user = null): bool
    {
        if (! self::isConsentRequired()) {
            return true;
        }

        $request ??= request();
        $cookieValue = self::parseConsentCookie($request?->cookie('consent_marketing'));
        if ($cookieValue !== null) {
            return $cookieValue;
        }

        $user ??= $request?->user();
        $historyValue = self::latestUserConsent($user, self::CONSENT_TYPE_MARKETING);
        if ($historyValue !== null) {
            return $historyValue;
        }

        return false;
    }

    public static function hasImplicitTrackingConsentForAuthenticatedUser(?User $user): bool
    {
        if (! $user) {
            return false;
        }

        return in_array($user->role, [User::ROLE_WORKER, User::ROLE_EMPLOYER], true);
    }

    public static function latestUserConsent(?User $user, string $consentType): ?bool
    {
        if (! $user) {
            return null;
        }

        $latest = ConsentHistory::query()
            ->where('user_id', $user->id)
            ->where('consent_type', $consentType)
            ->orderByDesc('accepted_at')
            ->orderByDesc('id')
            ->first();

        if (! $latest) {
            return null;
        }

        return (bool) $latest->given;
    }

    private static function parseConsentCookie(mixed $value): ?bool
    {
        if ($value === '1' || $value === 1 || $value === true || $value === 'true') {
            return true;
        }

        if ($value === '0' || $value === 0 || $value === false || $value === 'false') {
            return false;
        }

        return null;
    }
}
