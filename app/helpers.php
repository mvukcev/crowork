<?php

if (! function_exists('setting')) {
    /**
     * Get a platform setting value by key with optional default.
     * Falls back to the default defined in Setting::DEFINITIONS if no DB record exists.
     *
     * @param  string  $key
     * @param  mixed   $default  Explicit default; if null, uses DEFINITIONS default
     * @return mixed
     */
    function setting(string $key, mixed $default = null): mixed
    {
        try {
            $defDefault = \App\Models\Setting::defaultFor($key);
            $fallback = $default ?? $defDefault;
            return \App\Models\Setting::getValue($key, $fallback);
        } catch (\Throwable) {
            return $default;
        }
    }
}
