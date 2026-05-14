<?php

namespace App\Filament\Admin\Resources\NotificationPreferenceResource\Pages;

use App\Filament\Admin\Resources\NotificationPreferenceResource;
use Filament\Resources\Pages\EditRecord;

class EditNotificationPreference extends EditRecord
{
    protected static string $resource = NotificationPreferenceResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
