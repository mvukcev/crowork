<?php

namespace App\Filament\Admin\Resources\EmailTemplateResource\Pages;

use App\Filament\Admin\Resources\EmailTemplateResource;
use App\Services\EmailTemplateService;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListEmailTemplates extends ListRecords
{
    protected static string $resource = EmailTemplateResource::class;

    public function mount(): void
    {
        parent::mount();

        app(EmailTemplateService::class)->syncDefaultTemplates(['en', 'hr']);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('syncDefaults')
                ->label('Sync defaults (EN/HR)')
                ->icon('heroicon-o-arrow-path')
                ->action(function (): void {
                    $result = app(EmailTemplateService::class)->syncDefaultTemplates(['en', 'hr']);

                    Notification::make()
                        ->title('Templates synchronized')
                        ->body("Created: {$result['created']}, Updated: {$result['updated']}, Deleted obsolete: {$result['deleted']}, Skipped: {$result['skipped']}")
                        ->success()
                        ->send();
                }),
            Actions\CreateAction::make(),
        ];
    }
}
