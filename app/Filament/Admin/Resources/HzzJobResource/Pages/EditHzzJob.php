<?php

namespace App\Filament\Admin\Resources\HzzJobResource\Pages;

use App\Filament\Admin\Resources\HzzJobResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHzzJob extends EditRecord
{
    protected static string $resource = HzzJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
