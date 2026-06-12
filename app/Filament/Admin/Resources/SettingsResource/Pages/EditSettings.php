<?php

namespace App\Filament\Admin\Resources\SettingsResource\Pages;

use App\Filament\Admin\Resources\SettingsResource;
use App\Models\Setting;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSettings extends EditRecord
{
    protected static string $resource = SettingsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->visible(false), // Don't allow deleting settings
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->previousUrl ?? $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $key = (string) ($data['key'] ?? '');
        $definition = Setting::definition($key) ?? [];
        $type = (string) ($definition['type'] ?? 'text');

        $rawValue = Setting::unwrapValue($data['value'] ?? null, Setting::defaultFor($key));

        $data['value_boolean'] = filter_var($rawValue, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false;
        $data['value_select'] = is_scalar($rawValue) ? (string) $rawValue : null;
        $data['value_integer'] = is_numeric($rawValue) ? (int) $rawValue : null;
        $data['value_email'] = is_scalar($rawValue) ? (string) $rawValue : null;
        $data['value_password'] = '';
        $data['value_array'] = is_array($rawValue) ? array_values(array_filter(array_map('strval', $rawValue))) : [];
        $data['value_text'] = is_scalar($rawValue) ? (string) $rawValue : ($type === 'array' ? '' : json_encode($rawValue));

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $key = (string) ($data['key'] ?? $this->record->key ?? '');
        $definition = Setting::definition($key) ?? [];
        $type = (string) ($definition['type'] ?? 'text');

        $currentValue = Setting::unwrapValue($this->record->value, Setting::defaultFor($key));

        $value = match ($type) {
            'boolean' => (bool) ($data['value_boolean'] ?? false),
            'select' => isset($data['value_select']) ? (string) $data['value_select'] : null,
            'integer' => is_numeric($data['value_integer'] ?? null) ? (int) $data['value_integer'] : null,
            'email' => isset($data['value_email']) ? (string) $data['value_email'] : null,
            'password' => (isset($data['value_password']) && trim((string) $data['value_password']) !== '')
                ? (string) $data['value_password']
                : $currentValue,
            'array' => array_values(array_filter(array_map(
                static fn ($item): string => trim((string) $item),
                is_array($data['value_array'] ?? null) ? $data['value_array'] : []
            ), static fn (string $item): bool => $item !== '')),
            default => isset($data['value_text']) ? (string) $data['value_text'] : null,
        };

        return [
            'key' => $key,
            'value' => $value,
        ];
    }
}
