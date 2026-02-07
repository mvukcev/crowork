<?php

namespace App\Filament\Admin\Resources\EducationResource\Pages;

use App\Filament\Admin\Resources\EducationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEducation extends EditRecord
{
    protected static string $resource = EducationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
