<?php

namespace App\Filament\Admin\Resources\TranslationOverrideResource\Pages;

use App\Filament\Admin\Resources\TranslationOverrideResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTranslationOverride extends CreateRecord
{
    protected static string $resource = TranslationOverrideResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
