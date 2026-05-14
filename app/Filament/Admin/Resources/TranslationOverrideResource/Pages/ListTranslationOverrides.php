<?php

namespace App\Filament\Admin\Resources\TranslationOverrideResource\Pages;

use App\Filament\Admin\Resources\TranslationOverrideResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTranslationOverrides extends ListRecords
{
    protected static string $resource = TranslationOverrideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
