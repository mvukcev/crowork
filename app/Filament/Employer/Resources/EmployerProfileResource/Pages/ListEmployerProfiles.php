<?php

namespace App\Filament\Employer\Resources\EmployerProfileResource\Pages;

use App\Filament\Employer\Resources\EmployerProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEmployerProfiles extends ListRecords
{
    protected static string $resource = EmployerProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
