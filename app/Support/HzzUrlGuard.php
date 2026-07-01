<?php

namespace App\Support;

class HzzUrlGuard
{
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
