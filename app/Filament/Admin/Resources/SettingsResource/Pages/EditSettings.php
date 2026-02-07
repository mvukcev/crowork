<?php

namespace App\Filament\Admin\Resources\SettingsResource\Pages;

use App\Filament\Admin\Resources\SettingsResource;
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
}
