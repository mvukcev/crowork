<?php

namespace App\Filament\Admin\Resources\HzzJobResource\Pages;

use App\Filament\Admin\Resources\HzzJobResource;
use App\Models\Setting;
use App\Services\Hzz\HzzJobImportService;
use App\Support\HzzUrlGuard;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListHzzJobs extends ListRecords
{
    protected static string $resource = HzzJobResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('quickResyncHzzJobs')
                ->label('Quick Resync')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->modalHeading('Quick Resync HZZ Jobs')
                ->form([
                    TextInput::make('url')
                        ->label('HZZ Feed URL')
                        ->url()
                        ->required()
                        ->default(fn (): string => (string) (Setting::getString('hzz_feed_url', '') ?? '')),
                    Toggle::make('allow_updates')
                        ->label('Update existing HZZ jobs')
                        ->default(fn (): bool => Setting::getBool('hzz_allow_updates_on_sync', true)),
                ])
                ->action(function (array $data): void {
                    $url = (string) ($data['url'] ?? '');
                    $allowUpdates = (bool) ($data['allow_updates'] ?? false);

                    if (! HzzUrlGuard::isAllowedFeedUrl($url)) {
                        Notification::make()
                            ->title('HZZ quick resync failed')
                            ->danger()
                            ->body('Invalid HZZ feed URL. Only official HZZ domains are allowed.')
                            ->send();

                        return;
                    }

                    Setting::setValue('hzz_feed_url', $url);
                    Setting::setValue('hzz_allow_updates_on_sync', $allowUpdates);

                    try {
                        $summary = app(HzzJobImportService::class)->importFromUrl($url, false, $allowUpdates);

                        Notification::make()
                            ->title('HZZ quick resync completed')
                            ->success()
                            ->body(sprintf(
                                'Created: %d, Updated: %d, Skipped existing: %d, Total feed items: %d.',
                                (int) ($summary['created'] ?? 0),
                                (int) ($summary['updated'] ?? 0),
                                (int) ($summary['skipped_existing'] ?? 0),
                                (int) ($summary['total_items'] ?? 0),
                            ))
                            ->send();
                    } catch (\Throwable $exception) {
                        report($exception);

                        Notification::make()
                            ->title('HZZ quick resync failed')
                            ->danger()
                            ->body((string) $exception->getMessage())
                            ->send();
                    }
                }),
        ];
    }
}
