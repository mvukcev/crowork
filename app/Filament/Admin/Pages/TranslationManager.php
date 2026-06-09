<?php

namespace App\Filament\Admin\Pages;

use App\Models\TranslationOverride;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\File;

class TranslationManager extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-language';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Translation Manager';

    protected static ?string $title = 'Translation Manager';

    protected static ?int $navigationSort = 8;

    protected static string $view = 'filament.admin.pages.translation-manager';

    public string $search = '';

    public bool $missingOnly = false;

    public string $activeGroup = 'common';

    public string $targetLocale = 'hr';

    /** @var array<string, string> */
    public array $overrides = [];

    public function mount(): void
    {
        $this->activeGroup = (string) request()->query('group', 'common');

        if (! in_array($this->activeGroup, $this->getAvailableGroups(), true)) {
            $this->activeGroup = $this->getAvailableGroups()[0] ?? 'common';
        }

        $this->loadOverrides();
    }

    public function loadOverrides(): void
    {
        $keys = TranslationOverride::where('locale', $this->targetLocale)
            ->where('group', $this->activeGroup)
            ->pluck('value', 'key')
            ->toArray();

        $this->overrides = $keys;
    }

    public function getAvailableGroups(): array
    {
        $langPath = base_path('lang/en');

        if (! is_dir($langPath)) {
            return [];
        }

        return collect(File::files($langPath))
            ->map(fn ($file) => pathinfo($file->getFilename(), PATHINFO_FILENAME))
            ->sort()
            ->values()
            ->toArray();
    }

    public function getFilteredGroupRows(): array
    {
        $search = trim($this->search);

        return array_values(array_filter($this->getGroupRows(), function (array $row) use ($search): bool {
            if ($this->missingOnly && trim((string) $row['current']) !== '') {
                return false;
            }

            if ($search === '') {
                return true;
            }

            $haystack = strtolower(implode(' ', [
                (string) $row['key'],
                (string) $row['en'],
                (string) $row['current'],
            ]));

            return str_contains($haystack, strtolower($search));
        }));
    }

    public function getSourceTranslations(): array
    {
        $file = base_path("lang/en/{$this->activeGroup}.php");

        if (! file_exists($file)) {
            return [];
        }

        return require $file;
    }

    public function getTargetFileTranslations(): array
    {
        $file = base_path("lang/{$this->targetLocale}/{$this->activeGroup}.php");

        if (! file_exists($file)) {
            return [];
        }

        return require $file;
    }

    public function getGroupRows(): array
    {
        $source = $this->getSourceTranslations();
        $fileTranslations = $this->getTargetFileTranslations();

        $rows = [];
        foreach ($source as $key => $enValue) {
            $override = $this->overrides[$key] ?? null;
            $fileValue = $fileTranslations[$key] ?? null;
            $current = $override ?? $fileValue ?? '';
            $rows[] = [
                'key' => $key,
                'en' => is_string($enValue) ? $enValue : json_encode($enValue),
                'file_value' => is_string($fileValue) ? $fileValue : json_encode($fileValue),
                'current' => $current,
                'has_override' => $override !== null,
                'is_missing' => trim((string) $current) === '',
                'field' => "overrides.{$key}",
            ];
        }

        return $rows;
    }

    public function selectGroup(string $group): void
    {
        $this->activeGroup = $group;
        $this->overrides = [];
        $this->loadOverrides();
    }

    public function saveTranslations(): void
    {
        $source = $this->getSourceTranslations();

        foreach ($source as $key => $_) {
            $value = $this->overrides[$key] ?? null;

            if ($value !== null && $value !== '') {
                TranslationOverride::setTranslation($this->targetLocale, $this->activeGroup, $key, $value);
            } else {
                // Delete override if empty - fall back to lang file
                TranslationOverride::where([
                    'locale' => $this->targetLocale,
                    'group' => $this->activeGroup,
                    'key' => $key,
                ])->delete();
            }
        }

        Notification::make()
            ->title(__('ui.admin.translations_saved'))
            ->body(__('ui.admin.translations_saved_body', ['group' => $this->activeGroup]))
            ->success()
            ->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('save')
                ->label(__('ui.admin.save_changes'))
                ->icon('heroicon-o-check')
                ->color('primary')
                ->action('saveTranslations'),
        ];
    }
}
