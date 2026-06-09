<?php

namespace App\Filament\Admin\Resources\ResourcePostResource\Pages;

use App\Filament\Admin\Resources\ResourcePostResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListResourcePosts extends ListRecords
{
    protected static string $resource = ResourcePostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
