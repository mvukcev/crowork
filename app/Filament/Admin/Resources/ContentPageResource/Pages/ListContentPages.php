<?php

namespace App\Filament\Admin\Resources\ContentPageResource\Pages;

use App\Filament\Admin\Resources\ContentPageResource;
use Filament\Resources\Pages\ListRecords;

class ListContentPages extends ListRecords
{
    protected static string $resource = ContentPageResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
