<?php

namespace App\Filament\Admin\Resources\WorkerResource\Pages;

use App\Filament\Admin\Resources\WorkerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListWorkers extends ListRecords
{
    protected static string $resource = WorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}