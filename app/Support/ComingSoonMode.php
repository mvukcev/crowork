<?php

namespace App\Support;

use App\Models\Setting;

class ComingSoonMode
{
    public static function mode(): string
    {
        $rawMode = strtolower((string) config('crowork.coming_soon.mode', 'env'));

        return in_array($rawMode, ['env', 'admin'], true) ? $rawMode : 'env';
    }

    public static function isAdminControlled(): bool
    {
        return static::mode() === 'admin';
    }

    public static function isEnvControlled(): bool
    {
        return static::mode() === 'env';
    }

    public static function enabled(): bool
    {
        $envEnabled = (bool) config('crowork.coming_soon.enabled', false);

        if (static::isEnvControlled()) {
            return $envEnabled;
        }

        try {
            return Setting::getBool('coming_soon_enabled', $envEnabled);
        } catch (\Throwable) {
            return $envEnabled;
        }
    }
}
