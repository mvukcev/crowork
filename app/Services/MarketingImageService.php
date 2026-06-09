<?php

namespace App\Services;

use App\Models\MarketingImageOverride;
use App\Support\MarketingImageSlots;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MarketingImageService
{
    private static ?Collection $activeOverrides = null;

    public function flushCache(): void
    {
        self::$activeOverrides = null;
    }

    public function url(string $key): ?string
    {
        $slot = MarketingImageSlots::get($key);
        if (! $slot) {
            return null;
        }

        $override = $this->activeOverrideFor($key);
        if ($override && $this->fileExists($override->disk ?? 'public', $override->path)) {
            return $this->toAbsoluteUrl(Storage::disk($override->disk ?? 'public')->url($override->path));
        }

        $fallback = (string) ($slot['fallback_path'] ?? '');
        if ($fallback === '') {
            return null;
        }

        return function_exists('cw_asset') ? cw_asset($fallback) : asset($fallback);
    }

    public function alt(string $key): string
    {
        $slot = MarketingImageSlots::get($key);
        if (! $slot) {
            return '';
        }

        $override = $this->activeOverrideFor($key);
        if ($override && $this->fileExists($override->disk ?? 'public', $override->path)) {
            $altText = trim((string) ($override->alt_text ?? ''));
            if ($altText !== '') {
                return $altText;
            }
        }

        $label = trim((string) ($slot['label'] ?? ''));
        if ($label !== '') {
            return $label;
        }

        return trim((string) ($slot['description'] ?? ''));
    }

    private function activeOverrideFor(string $key): ?MarketingImageOverride
    {
        return $this->activeOverrides()->firstWhere('key', $key);
    }

    private function activeOverrides(): Collection
    {
        if (self::$activeOverrides !== null) {
            return self::$activeOverrides;
        }

        self::$activeOverrides = MarketingImageOverride::query()
            ->where('is_active', true)
            ->whereNotNull('path')
            ->get();

        return self::$activeOverrides;
    }

    private function fileExists(string $disk, ?string $path): bool
    {
        if (! $path) {
            return false;
        }

        try {
            return Storage::disk($disk)->exists($path);
        } catch (\Throwable) {
            return false;
        }
    }

    private function toAbsoluteUrl(string $url): string
    {
        if (Str::startsWith($url, ['http://', 'https://'])) {
            return $url;
        }

        return url($url);
    }
}
