<?php

namespace App\Filament\Admin\Resources\JobResource\Pages;

use App\Filament\Admin\Resources\JobResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Artisan;

class ListJobs extends ListRecords
{
    protected static string $resource = JobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('import_hzz')
                ->label('Import HZZ')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function (): void {
                    Artisan::call('crowork:import-hzz-jobs');
                    $output = trim(Artisan::output());

                    Notification::make()
                        ->title('HZZ import completed')
                        ->body($output !== '' ? $output : 'Import finished successfully.')
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }
}
