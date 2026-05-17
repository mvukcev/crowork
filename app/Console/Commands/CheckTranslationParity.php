<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CheckTranslationParity extends Command
{
    protected $signature = 'crowork:translations:check
        {--base=en : Base locale used as source of truth}
        {--locales=en,hr : Comma-separated locales to compare}
        {--fail-on-missing : Return non-zero exit code when parity issues exist}';

    protected $description = 'Check translation parity, placeholder consistency, and locale drift across lang files.';

    public function handle(): int
    {
        $baseLocale = (string) $this->option('base');
        $locales = array_values(array_filter(array_map('trim', explode(',', (string) $this->option('locales')))));

        if ($locales === []) {
            $this->error('No locales provided.');
            return self::FAILURE;
        }

        if (! in_array($baseLocale, $locales, true)) {
            $locales[] = $baseLocale;
        }

        $all = [];
        foreach ($locales as $locale) {
            $all[$locale] = $this->loadLocale($locale);
        }

        if (! isset($all[$baseLocale])) {
            $this->error("Base locale [{$baseLocale}] is not available.");
            return self::FAILURE;
        }

        $base = $all[$baseLocale];
        $hasIssues = false;

        foreach ($all as $locale => $keys) {
            if ($locale === $baseLocale) {
                continue;
            }

            $missingInLocale = array_values(array_diff(array_keys($base), array_keys($keys)));
            $extraInLocale = array_values(array_diff(array_keys($keys), array_keys($base)));
            $placeholderMismatches = $this->collectPlaceholderMismatches($base, $keys);

            $this->line('');
            $this->info("Locale: {$locale}");
            $this->line('------------------------------');
            $this->line('Base keys: '.count($base));
            $this->line("{$locale} keys: ".count($keys));
            $this->line('Missing keys: '.count($missingInLocale));
            $this->line('Extra keys: '.count($extraInLocale));
            $this->line('Placeholder mismatches: '.count($placeholderMismatches));

            if ($missingInLocale !== []) {
                $hasIssues = true;
                $this->warn('Missing key samples:');
                foreach (array_slice($missingInLocale, 0, 15) as $key) {
                    $this->line("  - {$key}");
                }
            }

            if ($extraInLocale !== []) {
                $hasIssues = true;
                $this->warn('Extra key samples:');
                foreach (array_slice($extraInLocale, 0, 15) as $key) {
                    $this->line("  - {$key}");
                }
            }

            if ($placeholderMismatches !== []) {
                $hasIssues = true;
                $this->warn('Placeholder mismatch samples:');
                foreach (array_slice($placeholderMismatches, 0, 15) as $item) {
                    $this->line("  - {$item}");
                }
            }
        }

        if ($hasIssues) {
            $this->newLine();
            $this->warn('Translation parity issues detected.');
            return $this->option('fail-on-missing') ? self::FAILURE : self::SUCCESS;
        }

        $this->newLine();
        $this->info('Translation parity check passed.');
        return self::SUCCESS;
    }

    /**
     * @return array<string, string>
     */
    private function loadLocale(string $locale): array
    {
        $langPath = lang_path($locale);
        if (! File::isDirectory($langPath)) {
            return [];
        }

        $flattened = [];

        foreach (File::files($langPath) as $file) {
            $group = $file->getFilenameWithoutExtension();
            $data = require $file->getPathname();
            if (! is_array($data)) {
                continue;
            }

            foreach ($this->dot($data, $group) as $key => $value) {
                $flattened[$key] = is_scalar($value) ? (string) $value : json_encode($value);
            }
        }

        ksort($flattened);
        return $flattened;
    }

    /**
     * @param array<string, mixed> $array
     * @return array<string, mixed>
     */
    private function dot(array $array, string $prefix = ''): array
    {
        $results = [];

        foreach ($array as $key => $value) {
            $dotKey = $prefix === '' ? (string) $key : $prefix.'.'.$key;

            if (is_array($value)) {
                $results += $this->dot($value, $dotKey);
                continue;
            }

            $results[$dotKey] = $value;
        }

        return $results;
    }

    /**
     * @param array<string, string> $base
     * @param array<string, string> $locale
     * @return array<int, string>
     */
    private function collectPlaceholderMismatches(array $base, array $locale): array
    {
        $mismatches = [];

        foreach ($base as $key => $baseValue) {
            if (! array_key_exists($key, $locale)) {
                continue;
            }

            $baseTokens = $this->extractTokens($baseValue);
            $localeTokens = $this->extractTokens($locale[$key]);

            sort($baseTokens);
            sort($localeTokens);

            if ($baseTokens !== $localeTokens) {
                $mismatches[] = $key;
            }
        }

        return $mismatches;
    }

    /**
     * @return array<int, string>
     */
    private function extractTokens(string $value): array
    {
        preg_match_all('/:([a-zA-Z0-9_]+)/', $value, $matches);
        return array_values(array_unique($matches[1] ?? []));
    }
}
