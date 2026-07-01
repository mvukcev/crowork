<?php

namespace App\Services\Hzz;

class HzzApplicationContactParser
{
    public function parse(string $content, ?string $fallbackUrl = null): array
    {
        $normalized = trim($content);
        $email = $this->extractEmail($normalized);
        $url = $this->extractUrl($normalized) ?? $this->normalizeUrl($fallbackUrl);

        $contactType = 'unknown';
        if ($email !== null) {
            $contactType = 'email';
        } elseif ($url !== null) {
            $contactType = 'external_url';
        }

        return [
            'email' => $email,
            'apply_url' => $url,
            'contact_type' => $contactType,
            'has_automated_apply' => $email !== null,
            'contact_raw' => $normalized !== '' ? $normalized : null,
        ];
    }

    private function extractEmail(string $content): ?string
    {
        if ($content === '') {
            return null;
        }

        preg_match('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/i', $content, $matches);

        if (! isset($matches[0])) {
            return null;
        }

        $email = strtolower(trim($matches[0]));

        return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
    }

    private function extractUrl(string $content): ?string
    {
        if ($content === '') {
            return null;
        }

        preg_match('/https?:\/\/[^\s<>()"\']+/i', $content, $matches);

        if (! isset($matches[0])) {
            return null;
        }

        return $this->normalizeUrl($matches[0]);
    }

    private function normalizeUrl(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '') {
            return null;
        }

        if (! str_starts_with(strtolower($url), 'http://') && ! str_starts_with(strtolower($url), 'https://')) {
            return null;
        }

        return filter_var($url, FILTER_VALIDATE_URL) ? $url : null;
    }
}
