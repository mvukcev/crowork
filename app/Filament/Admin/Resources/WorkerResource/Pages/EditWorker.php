<?php

namespace App\Filament\Admin\Resources\WorkerResource\Pages;

use App\Filament\Admin\Resources\WorkerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditWorker extends EditRecord
{
    protected static string $resource = WorkerResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}