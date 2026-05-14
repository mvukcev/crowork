<?php

namespace App\Filament\Admin\Resources\NotificationPreferenceResource\Pages;

use App\Filament\Admin\Resources\NotificationPreferenceResource;
use Filament\Resources\Pages\ListRecords;

class ListNotificationPreferences extends ListRecords
{
    protected static string $resource = NotificationPreferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
