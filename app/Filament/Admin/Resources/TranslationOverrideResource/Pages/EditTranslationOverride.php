<?php

namespace App\Filament\Admin\Resources\TranslationOverrideResource\Pages;

use App\Filament\Admin\Resources\TranslationOverrideResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTranslationOverride extends EditRecord
{
    protected static string $resource = TranslationOverrideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
