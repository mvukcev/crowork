<?php

namespace App\Filament\Admin\Resources\ErrorLogResource\Pages;

use App\Filament\Admin\Resources\ErrorLogResource;
use Filament\Resources\Pages\ListRecords;

class ListErrorLogs extends ListRecords
{
    protected static string $resource = ErrorLogResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
