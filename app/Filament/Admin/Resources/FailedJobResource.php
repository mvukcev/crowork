<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FailedJobResource\Pages;
use App\Models\FailedJob;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Artisan;

class FailedJobResource extends Resource
{
    protected static ?string $model = FailedJob::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Failed Jobs';

    protected static ?int $navigationSort = 9;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('uuid')
                    ->label('UUID')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('connection')
                    ->label('Connection')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('queue')
                    ->label('Queue')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('failed_at')
                    ->label('Failed At')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('exception')
                    ->label('Exception')
                    ->limit(80)
                    ->wrap(),
            ])
            ->headerActions([
                Tables\Actions\Action::make('retryAll')
                    ->label('Retry All')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        $ids = self::retryIdentifiers(FailedJob::query()->get());

                        if ($ids === []) {
                            Notification::make()
                                ->title('No failed jobs to retry')
                                ->success()
                                ->send();

                            return;
                        }

                        try {
                            $success = 0;
                            $failed = [];

                            foreach ($ids as $id) {
                                try {
                                    Artisan::call('queue:retry', ['id' => [$id]]);
                                    $success++;
                                } catch (\Throwable $inner) {
                                    $failed[] = [
                                        'id' => $id,
                                        'message' => $inner->getMessage(),
                                    ];
                                }
                            }

                            if ($success > 0) {
                                Notification::make()
                                    ->title('Retry queued for failed jobs')
                                    ->body("Retried {$success} failed job(s)." . (count($failed) > 0 ? ' Some jobs could not be retried.' : ''))
                                    ->success()
                                    ->send();
                            }

                            if (count($failed) > 0) {
                                $first = $failed[0]['message'] ?? 'Unknown retry failure.';

                                Notification::make()
                                    ->title('Some failed jobs could not be retried')
                                    ->body('Count: ' . count($failed) . '. First error: ' . $first)
                                    ->warning()
                                    ->send();
                            }
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('Retry failed jobs action failed')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\Action::make('clearAll')
                    ->label('Clear All')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (): void {
                        Artisan::call('queue:flush');

                        Notification::make()
                            ->title('Failed jobs cleared')
                            ->success()
                            ->send();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('retry')
                    ->label('Retry')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (FailedJob $record): void {
                        $retryId = filled((string) $record->uuid) ? (string) $record->uuid : (string) $record->id;

                        try {
                            Artisan::call('queue:retry', ['id' => [$retryId]]);

                            Notification::make()
                                ->title('Failed job queued for retry')
                                ->success()
                                ->send();
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('Retry action failed')
                                ->body($exception->getMessage())
                                ->warning()
                                ->send();
                        }
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('retrySelected')
                    ->label('Retry Selected')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function ($records): void {
                        $ids = self::retryIdentifiers($records);

                        if ($ids === []) {
                            return;
                        }

                        try {
                            $success = 0;
                            $failed = [];

                            foreach ($ids as $id) {
                                try {
                                    Artisan::call('queue:retry', ['id' => [$id]]);
                                    $success++;
                                } catch (\Throwable $inner) {
                                    $failed[] = [
                                        'id' => $id,
                                        'message' => $inner->getMessage(),
                                    ];
                                }
                            }

                            if ($success > 0) {
                                Notification::make()
                                    ->title('Selected failed jobs queued for retry')
                                    ->body("Retried {$success} failed job(s)." . (count($failed) > 0 ? ' Some jobs could not be retried.' : ''))
                                    ->success()
                                    ->send();
                            }

                            if (count($failed) > 0) {
                                $first = $failed[0]['message'] ?? 'Unknown retry failure.';

                                Notification::make()
                                    ->title('Some selected jobs could not be retried')
                                    ->body('Count: ' . count($failed) . '. First error: ' . $first)
                                    ->warning()
                                    ->send();
                            }
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('Retry selected action failed')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('failed_at', 'desc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFailedJobs::route('/'),
        ];
    }

    /**
     * @param iterable<int, FailedJob> $records
     * @return array<int, string>
     */
    private static function retryIdentifiers(iterable $records): array
    {
        $ids = [];

        foreach ($records as $record) {
            $identifier = filled((string) $record->uuid) ? (string) $record->uuid : (string) $record->id;

            if ($identifier !== '') {
                $ids[] = $identifier;
            }
        }

        return array_values(array_unique($ids));
    }
}
