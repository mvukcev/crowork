<?php

namespace App\Support;

use Illuminate\Support\Arr;
use ResourceBundle;

class CvProfileOptions
{
    /**
     * @return array<string, string>
     */
    public static function countryOptions(?string $locale = null): array
    {
        $locale = self::normalizeLocale($locale);
        $countries = self::rawCountries($locale);

        asort($countries, SORT_NATURAL | SORT_FLAG_CASE);

        return $countries;
    }

    public static function displayCountryName(?string $value, ?string $locale = null): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        $code = self::resolveCountryCode($value, $locale);
        if (! $code) {
            return $value;
        }

        $localized = self::countryOptions($locale)[$code] ?? null;

        return $localized ?: $value;
    }

    public static function normalizeCountryForStorage(?string $value, ?string $locale = null): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        $code = self::resolveCountryCode($value, $locale);

        return $code ?: $value;
    }

    /**
     * @return array<string, string>
     */
    public static function visaStatusOptions(): array
    {
        return [
            'valid_permit' => __('worker_profile.editor.visa_options.valid_permit'),
            'need_permit' => __('worker_profile.editor.visa_options.need_permit'),
            'permit_in_progress' => __('worker_profile.editor.visa_options.permit_in_progress'),
            'eu_eea_no_permit' => __('worker_profile.editor.visa_options.eu_eea_no_permit'),
            'residence' => __('worker_profile.editor.visa_options.residence'),
            'not_applicable' => __('worker_profile.editor.visa_options.not_applicable'),
            'other' => __('worker_profile.editor.visa_options.other'),
        ];
    }

    public static function normalizeVisaStatusForStorage(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        $options = self::visaStatusOptions();
        if (array_key_exists($value, $options)) {
            return $value;
        }

        $needle = self::normalizeText($value);

        foreach (['en', 'hr'] as $locale) {
            foreach (self::visaStatusOptionsForLocale($locale) as $key => $label) {
                if (self::normalizeText($label) === $needle) {
                    return $key;
                }
            }
        }

        return $value;
    }

    public static function displayVisaStatusLabel(?string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        $options = self::visaStatusOptions();
        if (array_key_exists($value, $options)) {
            return $options[$value];
        }

        return $value;
    }

    /**
     * @return array<int, string>
     */
    public static function skillSuggestions(): array
    {
        $suggestions = __('worker_profile.editor.skill_suggestions');

        if (! is_array($suggestions)) {
            return [];
        }

        $normalized = [];
        foreach ($suggestions as $item) {
            if (! is_string($item)) {
                continue;
            }

            $value = trim($item);
            if ($value !== '') {
                $normalized[] = $value;
            }
        }

        return array_values(array_unique($normalized));
    }

    private static function normalizeLocale(?string $locale): string
    {
        $locale = strtolower(trim((string) ($locale ?: app()->getLocale())));

        return in_array($locale, ['hr', 'en'], true) ? $locale : 'en';
    }

    private static function resolveCountryCode(string $value, ?string $locale = null): ?string
    {
        $candidate = strtoupper($value);

        if (preg_match('/^[A-Z]{2}$/', $candidate) === 1) {
            $options = self::countryOptions($locale);

            return array_key_exists($candidate, $options) ? $candidate : null;
        }

        $normalizedNeedle = self::normalizeText($value);
        if ($normalizedNeedle === '') {
            return null;
        }

        foreach ([self::normalizeLocale($locale), 'en', 'hr'] as $lookupLocale) {
            foreach (self::countryOptions($lookupLocale) as $code => $name) {
                if (self::normalizeText($name) === $normalizedNeedle) {
                    return $code;
                }
            }
        }

        return null;
    }

    /**
     * @return array<string, string>
     */
    private static function rawCountries(string $locale): array
    {
        $bundle = ResourceBundle::create($locale, 'ICUDATA-region');
        if (! $bundle instanceof ResourceBundle) {
            $bundle = ResourceBundle::create('en', 'ICUDATA-region');
        }

        $countries = $bundle?->get('Countries');
        if (! $countries instanceof ResourceBundle) {
            return [];
        }

        $items = [];
        foreach ($countries as $code => $name) {
            if (! is_string($code) || preg_match('/^[A-Z]{2}$/', $code) !== 1) {
                continue;
            }

            if (! is_string($name)) {
                continue;
            }

            $trimmed = trim($name);
            if ($trimmed === '') {
                continue;
            }

            $items[$code] = $trimmed;
        }

        return $items;
    }

    /**
     * @return array<string, string>
     */
    private static function visaStatusOptionsForLocale(string $locale): array
    {
        $translations = Arr::wrap(trans('worker_profile.editor.visa_options', [], $locale));

        $allowedKeys = [
            'valid_permit',
            'need_permit',
            'permit_in_progress',
            'eu_eea_no_permit',
            'residence',
            'not_applicable',
            'other',
        ];

        $result = [];
        foreach ($allowedKeys as $key) {
            $label = $translations[$key] ?? null;
            if (is_string($label) && trim($label) !== '') {
                $result[$key] = $label;
            }
        }

        return $result;
    }

    private static function normalizeText(string $value): string
    {
        $value = strtolower(trim($value));
        if ($value === '') {
            return '';
        }

        $transliterated = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
        if (is_string($transliterated)) {
            $value = $transliterated;
        }

        $value = preg_replace('/[^a-z0-9]+/', ' ', $value) ?? '';

        return trim($value);
    }
}
