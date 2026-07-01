<?php

namespace App\Services\Hzz;

use App\Support\HzzUrlGuard;

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

        $normalized = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');

        // Handle common obfuscations like "name (at) domain dot hr" in imported feeds.
        $normalized = preg_replace('/\s*(\[at\]|\(at\)|\bat\b)\s*/iu', '@', $normalized) ?? $normalized;
        $normalized = preg_replace('/\s*(\[dot\]|\(dot\)|\bdot\b)\s*/iu', '.', $normalized) ?? $normalized;

        preg_match('/mailto:\s*([A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,})/iu', $normalized, $mailtoMatches);
        if (isset($mailtoMatches[1])) {
            $email = strtolower(trim((string) $mailtoMatches[1]));

            return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null;
        }

        preg_match('/[A-Z0-9._%+\-]+@[A-Z0-9.\-]+\.[A-Z]{2,}/iu', $normalized, $matches);

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

        if (! filter_var($url, FILTER_VALIDATE_URL)) {
            return null;
        }

        return HzzUrlGuard::isAllowedApplyUrl($url) ? $url : null;
    }
}
