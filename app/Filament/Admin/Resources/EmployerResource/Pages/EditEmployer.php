<?php

namespace App\Filament\Admin\Resources\EmployerResource\Pages;

use App\Filament\Admin\Resources\EmployerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployer extends EditRecord
{
    protected static string $resource = EmployerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
