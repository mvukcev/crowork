<?php

namespace App\Support;

class HzzUrlGuard
{
    public const DEFAULT_FEED_URL = 'http://burzarada.hzz.hr/rss/0xADAA044C9A86446096022A136750DD8F.xml';

    /**
     * @var array<int, string>
     */
    private const ALLOWED_HOST_SUFFIXES = [
        'hzz.hr',
    ];

    public static function isAllowedFeedUrl(?string $url): bool
    {
        return self::isAllowedUrl($url);
    }

    public static function isAllowedApplyUrl(?string $url): bool
    {
        return self::isAllowedUrl($url);
    }

    public static function defaultFeedUrl(): string
    {
        return self::DEFAULT_FEED_URL;
    }

    private static function isAllowedUrl(?string $url): bool
    {
        $value = trim((string) $url);
        if ($value === '') {
            return false;
        }

        $parts = parse_url($value);
        if (! is_array($parts)) {
            return false;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        $host = strtolower((string) ($parts['host'] ?? ''));

        if (! in_array($scheme, ['http', 'https'], true) || $host === '') {
            return false;
        }

        foreach (self::ALLOWED_HOST_SUFFIXES as $allowedSuffix) {
            if ($host === $allowedSuffix || str_ends_with($host, '.' . $allowedSuffix)) {
                return true;
            }
        }

        return false;
    }
}
