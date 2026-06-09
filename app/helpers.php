<?php

use Illuminate\Support\Str;

if (! function_exists('cw_asset')) {
    /**
     * Generate a versioned URL for files in /public using filemtime cache busting.
     */
    function cw_asset(string $path): string
    {
        $normalizedPath = ltrim($path, '/');
        $url = asset($normalizedPath);
        $absolutePath = public_path($normalizedPath);

        if (! is_file($absolutePath)) {
            return $url;
        }

        $mtime = @filemtime($absolutePath);
        if ($mtime === false) {
            return $url;
        }

        $separator = str_contains($url, '?') ? '&' : '?';

        return $url . $separator . 'v=' . $mtime;
    }
}

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

if (! function_exists('marketing_image_url')) {
    /**
     * Resolve marketing image URL from override storage or fallback registry path.
     */
    function marketing_image_url(string $key): ?string
    {
        try {
            return app(\App\Services\MarketingImageService::class)->url($key);
        } catch (\Throwable) {
            return null;
        }
    }
}

if (! function_exists('marketing_image_alt')) {
    /**
     * Resolve marketing image alt text from override or slot registry metadata.
     */
    function marketing_image_alt(string $key): string
    {
        try {
            return app(\App\Services\MarketingImageService::class)->alt($key);
        } catch (\Throwable) {
            return '';
        }
    }
}

if (! function_exists('cw_localize_language_code')) {
    function cw_localize_language_code(?string $code, ?string $locale = null): ?string
    {
        if ($code === null || trim($code) === '') {
            return null;
        }

        $locale = $locale ?: app()->getLocale();
        $normalized = strtoupper(trim($code));

        $labels = [
            'EN' => ['en' => 'English', 'hr' => 'Engleski'],
            'HR' => ['en' => 'Croatian', 'hr' => 'Hrvatski'],
            'DE' => ['en' => 'German', 'hr' => 'Njemački'],
            'IT' => ['en' => 'Italian', 'hr' => 'Talijanski'],
            'ES' => ['en' => 'Spanish', 'hr' => 'Španjolski'],
            'FR' => ['en' => 'French', 'hr' => 'Francuski'],
            'PT' => ['en' => 'Portuguese', 'hr' => 'Portugalski'],
            'NL' => ['en' => 'Dutch', 'hr' => 'Nizozemski'],
            'PL' => ['en' => 'Polish', 'hr' => 'Poljski'],
            'CS' => ['en' => 'Czech', 'hr' => 'Češki'],
            'SK' => ['en' => 'Slovak', 'hr' => 'Slovački'],
            'SL' => ['en' => 'Slovenian', 'hr' => 'Slovenski'],
            'HU' => ['en' => 'Hungarian', 'hr' => 'Mađarski'],
            'RO' => ['en' => 'Romanian', 'hr' => 'Rumunjski'],
            'BG' => ['en' => 'Bulgarian', 'hr' => 'Bugarski'],
            'UK' => ['en' => 'Ukrainian', 'hr' => 'Ukrajinski'],
            'RU' => ['en' => 'Russian', 'hr' => 'Ruski'],
            'TR' => ['en' => 'Turkish', 'hr' => 'Turski'],
            'SR' => ['en' => 'Serbian', 'hr' => 'Srpski'],
            'BS' => ['en' => 'Bosnian', 'hr' => 'Bosanski'],
            'MK' => ['en' => 'Macedonian', 'hr' => 'Makedonski'],
            'SQ' => ['en' => 'Albanian', 'hr' => 'Albanski'],
        ];

        if (isset($labels[$normalized])) {
            return $labels[$normalized][$locale] ?? $labels[$normalized]['en'];
        }

        return $normalized;
    }
}

if (! function_exists('cw_localize_job_value')) {
    function cw_localize_job_value(?string $field, mixed $value, ?string $locale = null): mixed
    {
        if ($value === null) {
            return null;
        }

        $locale = $locale ?: app()->getLocale();
        if (! is_string($value)) {
            return $value;
        }

        $raw = trim($value);
        if ($raw === '') {
            return $raw;
        }

        $normalized = strtolower(str_replace(['-', ' '], '_', $raw));

        if ($field === 'employment_type' || $field === 'contract_type') {
            $map = [
                'full_time' => __('jobs.employment_types.full_time'),
                'part_time' => __('jobs.employment_types.part_time'),
                'half_time' => __('jobs.employment_types.half_time'),
                'seasonal' => __('jobs.seasonal'),
                'contract' => __('jobs.contract'),
                'temporary' => __('jobs.temporary'),
                'internship' => __('jobs.internship'),
            ];

            return $map[$normalized] ?? Str::headline(str_replace('_', ' ', $normalized));
        }

        if ($field === 'experience_level') {
            $map = [
                'entry' => __('jobs.entry_level'),
                'entry_level' => __('jobs.entry_level'),
                'junior' => __('jobs.junior'),
                'mid' => __('jobs.mid'),
                'middle' => __('jobs.mid'),
                'senior' => __('jobs.senior'),
                'lead' => __('jobs.lead'),
                'principal' => __('jobs.lead'),
            ];

            return $map[$normalized] ?? Str::headline(str_replace('_', ' ', $normalized));
        }

        if ($field === 'category') {
            $key = strtolower($raw);
            $exactMap = [
                'it & software' => 'IT i softver',
                'it and software' => 'IT i softver',
                'hospitality' => 'Ugostiteljstvo',
                'marketing' => 'Marketing',
                'finance' => 'Financije',
                'tourism' => 'Turizam',
                'design' => 'Dizajn',
                'education' => 'Obrazovanje',
                'construction' => 'Građevina',
                'manufacturing' => 'Proizvodnja',
                'logistics' => 'Logistika',
                'transport' => 'Transport',
                'healthcare' => 'Zdravstvo',
                'sales' => 'Prodaja',
                'administration' => 'Administracija',
                'customer support' => 'Korisnička podrška',
                'retail' => 'Maloprodaja',
                'agriculture' => 'Poljoprivreda',
            ];

            if ($locale === 'hr') {
                if (isset($exactMap[$key])) {
                    return $exactMap[$key];
                }

                return cw_translate_known_job_terms_hr($raw);
            }

            return $raw;
        }

        if (in_array($field, ['education_required', 'contract_duration', 'start_flexibility', 'working_hours', 'shift_details'], true)) {
            if ($locale === 'hr') {
                return cw_translate_known_job_terms_hr($raw);
            }

            return $raw;
        }

        return $raw;
    }
}

if (! function_exists('cw_translate_known_job_terms_hr')) {
    function cw_translate_known_job_terms_hr(string $text): string
    {
        $source = trim($text);
        if ($source === '') {
            return $source;
        }

        $exactMap = [
            'High school or relevant qualification' => 'Srednja škola ili odgovarajuća kvalifikacija',
            'High school' => 'Srednja škola',
            'Vocational school' => 'Strukovna škola',
            'Vocational' => 'Strukovno obrazovanje',
            'Diploma' => 'Diploma',
            'Bachelor' => 'Prvostupnik',
            "Bachelor's degree" => 'Diploma prvostupnika',
            'Master' => 'Magistar',
            "Master's degree" => 'Diploma magistra',
            'PhD' => 'Doktorat',
            'Doctorate' => 'Doktorat',
            'No formal education required' => 'Formalno obrazovanje nije uvjet',
            'Permanent (unless stated otherwise)' => 'Na neodređeno (osim ako nije drugačije navedeno)',
            'Permanent' => 'Na neodređeno',
            'Fixed-term' => 'Na određeno',
            'Fixed term' => 'Na određeno',
            'Temporary' => 'Privremeno',
            'Seasonal' => 'Sezonski',
            'Negotiable' => 'Po dogovoru',
            'Immediate' => 'Odmah',
            'Flexible' => 'Fleksibilno',
            '40h/week' => '40h/tjedno',
        ];

        if (isset($exactMap[$source])) {
            return $exactMap[$source];
        }

        $translated = mb_strtolower($source, 'UTF-8');
        $replacements = [
            "bachelor's degree" => 'diploma prvostupnika',
            "master's degree" => 'diploma magistra',
            'relevant qualification' => 'odgovarajuća kvalifikacija',
            'high school' => 'srednja škola',
            'vocational school' => 'strukovna škola',
            'vocational' => 'strukovno',
            'qualification' => 'kvalifikacija',
            'certificate' => 'certifikat',
            'degree' => 'diploma',
            'doctorate' => 'doktorat',
            'phd' => 'doktorat',
            'master' => 'magistar',
            'bachelor' => 'prvostupnik',
            'negotiable' => 'po dogovoru',
            'immediate' => 'odmah',
            'flexible' => 'fleksibilno',
            'permanent' => 'na neodređeno',
            'fixed-term' => 'na određeno',
            'fixed term' => 'na određeno',
            'temporary' => 'privremeno',
            'seasonal' => 'sezonski',
            'full-time' => 'puno radno vrijeme',
            'full time' => 'puno radno vrijeme',
            'part-time' => 'nepuno radno vrijeme',
            'part time' => 'nepuno radno vrijeme',
            'hour/week' => 'sat/tjedno',
            'hours/week' => 'sati/tjedno',
            'h/week' => 'h/tjedno',
            '/week' => '/tjedno',
            '/month' => '/mjesečno',
            'it & software' => 'IT i softver',
            'software' => 'softver',
            'hospitality' => 'ugostiteljstvo',
            'tourism' => 'turizam',
            'finance' => 'financije',
            'marketing' => 'marketing',
            'education' => 'obrazovanje',
            'design' => 'dizajn',
            'construction' => 'građevina',
            'manufacturing' => 'proizvodnja',
            'logistics' => 'logistika',
            'transport' => 'transport',
            'healthcare' => 'zdravstvo',
            'sales' => 'prodaja',
            'administration' => 'administracija',
            'customer support' => 'korisnička podrška',
            'retail' => 'maloprodaja',
            'agriculture' => 'poljoprivreda',
        ];

        uksort($replacements, fn ($a, $b) => strlen((string) $b) <=> strlen((string) $a));

        foreach ($replacements as $search => $replace) {
            $translated = str_replace($search, $replace, $translated);
        }

        $translated = mb_convert_case($translated, MB_CASE_TITLE, 'UTF-8');
        $translated = str_replace('It I Softver', 'IT i softver', $translated);

        return $translated;
    }
}
