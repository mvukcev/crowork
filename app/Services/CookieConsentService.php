<?php

namespace App\Services;

use App\Models\ConsentHistory;
use App\Models\User;
use Illuminate\Contracts\Cookie\QueueingFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Cookie;

class CookieConsentService
{
    public const CONSENT_TYPE_ANALYTICS = 'cookie_analytics';
    public const CONSENT_TYPE_MARKETING = 'cookie_marketing';

    public const SOURCE_BANNER = 'cookie_banner';
    public const SOURCE_WORKER_PRIVACY = 'worker_privacy';

    public const CHOICE_ALL = 'all';
    public const CHOICE_REQUIRED = 'required';
    public const CHOICE_CUSTOM = 'custom';

    private const COOKIE_LIFETIME_MINUTES = 525600;

    public function resolveState(Request $request, ?User $user = null): array
    {
        if (! ConsentConfigService::isConsentRequired()) {
            return [
                'analytics' => true,
                'marketing' => true,
                'choice' => self::CHOICE_ALL,
            ];
        }

        $choiceFromCookie = $this->sanitizeChoice((string) $request->cookie('cw_cookie_choice', ''));
        $analyticsFromCookie = $this->parseConsentCookie($request->cookie('consent_analytics'));
        $marketingFromCookie = $this->parseConsentCookie($request->cookie('consent_marketing'));

        if ($analyticsFromCookie !== null && $marketingFromCookie !== null) {
            return [
                'analytics' => $analyticsFromCookie,
                'marketing' => $marketingFromCookie,
                'choice' => $choiceFromCookie ?? $this->resolveChoice($analyticsFromCookie, $marketingFromCookie),
            ];
        }

        $user ??= $request->user();

        if ($user) {
            $analyticsFromHistory = ConsentConfigService::latestUserConsent($user, self::CONSENT_TYPE_ANALYTICS);
            $marketingFromHistory = ConsentConfigService::latestUserConsent($user, self::CONSENT_TYPE_MARKETING);

            if ($analyticsFromHistory !== null || $marketingFromHistory !== null) {
                $analytics = $analyticsFromHistory ?? false;
                $marketing = $marketingFromHistory ?? false;

                return [
                    'analytics' => $analytics,
                    'marketing' => $marketing,
                    'choice' => $choiceFromCookie ?? $this->resolveChoice($analytics, $marketing),
                ];
            }
        }

        return [
            'analytics' => false,
            'marketing' => false,
            'choice' => $choiceFromCookie,
        ];
    }

    public function persistConsent(
        Request $request,
        bool $analytics,
        bool $marketing,
        string $choice,
        string $source,
        ?User $user = null
    ): void {
        $user ??= $request->user();

        if (! $user) {
            return;
        }

        $acceptedAt = now();
        $version = (string) config('app.gdpr_consent_version', '2026-05-17');
        $versionHash = hash('sha256', $version);
        $normalizedChoice = $this->sanitizeChoice($choice) ?? $this->resolveChoice($analytics, $marketing);
        $normalizedSource = trim($source) !== '' ? trim($source) : self::SOURCE_BANNER;

        ConsentHistory::query()->create([
            'user_id' => $user->id,
            'consent_type' => self::CONSENT_TYPE_ANALYTICS,
            'consent_version' => $version,
            'consent_version_hash' => $versionHash,
            'source' => $normalizedSource,
            'given' => $analytics,
            'accepted_at' => $acceptedAt,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        ConsentHistory::query()->create([
            'user_id' => $user->id,
            'consent_type' => self::CONSENT_TYPE_MARKETING,
            'consent_version' => $version,
            'consent_version_hash' => $versionHash,
            'source' => $normalizedSource . ':' . $normalizedChoice,
            'given' => $marketing,
            'accepted_at' => $acceptedAt,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);
    }

    /**
     * @return array<int, Cookie>
     */
    public function buildConsentCookies(bool $analytics, bool $marketing, string $choice): array
    {
        $normalizedChoice = $this->sanitizeChoice($choice) ?? $this->resolveChoice($analytics, $marketing);

        return [
            cookie('consent_analytics', $analytics ? '1' : '0', self::COOKIE_LIFETIME_MINUTES, '/', null, false, false, false, 'lax'),
            cookie('consent_marketing', $marketing ? '1' : '0', self::COOKIE_LIFETIME_MINUTES, '/', null, false, false, false, 'lax'),
            cookie('cw_cookie_choice', $normalizedChoice, self::COOKIE_LIFETIME_MINUTES, '/', null, false, false, false, 'lax'),
        ];
    }

    public function queueConsentCookies(QueueingFactory $cookies, bool $analytics, bool $marketing, string $choice): void
    {
        foreach ($this->buildConsentCookies($analytics, $marketing, $choice) as $cookie) {
            $cookies->queue($cookie);
        }
    }

    public function resolveChoice(bool $analytics, bool $marketing): string
    {
        if ($analytics && $marketing) {
            return self::CHOICE_ALL;
        }

        if (! $analytics && ! $marketing) {
            return self::CHOICE_REQUIRED;
        }

        return self::CHOICE_CUSTOM;
    }

    private function sanitizeChoice(string $choice): ?string
    {
        $normalized = strtolower(trim($choice));
        if (in_array($normalized, [self::CHOICE_ALL, self::CHOICE_REQUIRED, self::CHOICE_CUSTOM], true)) {
            return $normalized;
        }

        return null;
    }

    private function parseConsentCookie(mixed $value): ?bool
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
