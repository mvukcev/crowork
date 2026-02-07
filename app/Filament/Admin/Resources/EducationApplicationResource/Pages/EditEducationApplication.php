<?php

namespace App\Filament\Admin\Resources\EducationApplicationResource\Pages;

use App\Filament\Admin\Resources\EducationApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEducationApplication extends EditRecord
{
    protected static string $resource = EducationApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
