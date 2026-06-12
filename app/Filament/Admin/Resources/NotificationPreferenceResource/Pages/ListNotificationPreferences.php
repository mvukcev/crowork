<?php

namespace App\Filament\Admin\Resources\NotificationPreferenceResource\Pages;

use App\Filament\Admin\Resources\NotificationPreferenceResource;
use App\Services\NotificationPreferenceService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListNotificationPreferences extends ListRecords
{
    protected static string $resource = NotificationPreferenceResource::class;

    public function mount(): void
    {
        parent::mount();

        app(NotificationPreferenceService::class)->ensureDefaultsForAllUsers();
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('syncDefaults')
                ->label('Sync defaults')
                ->icon('heroicon-o-arrow-path')
                ->action(function (): void {
                    $created = app(NotificationPreferenceService::class)->ensureDefaultsForAllUsers();

                    Notification::make()
                        ->title('Notification preferences synchronized')
                        ->body($created > 0 ? "Created {$created} missing records." : 'All users already have default records.')
                        ->success()
                        ->send();
                }),
        ];
    }
}
