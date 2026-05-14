<?php

namespace App\Filament\Admin\Resources\NotificationDigestResource\Pages;

use App\Filament\Admin\Resources\NotificationDigestResource;
use Filament\Resources\Pages\ListRecords;

class ListNotificationDigests extends ListRecords
{
    protected static string $resource = NotificationDigestResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
