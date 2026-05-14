<?php

namespace App\Filament\Employer\Resources\EmployerProfileResource\Pages;

use App\Filament\Employer\Resources\EmployerProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEmployerProfile extends EditRecord
{
    protected static string $resource = EmployerProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
