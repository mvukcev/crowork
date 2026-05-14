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

    protected static ?string $navigationGroup = 'System';

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
                        $uuids = FailedJob::query()->pluck('uuid')->filter()->values()->all();

                        if ($uuids === []) {
                            Notification::make()
                                ->title('No failed jobs to retry')
                                ->success()
                                ->send();

                            return;
                        }

                        Artisan::call('queue:retry', ['id' => $uuids]);

                        Notification::make()
                            ->title('Retry queued for failed jobs')
                            ->body(sprintf('Retried %d failed job(s).', count($uuids)))
                            ->success()
                            ->send();
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
                        Artisan::call('queue:retry', ['id' => [$record->uuid]]);

                        Notification::make()
                            ->title('Failed job queued for retry')
                            ->success()
                            ->send();
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
                        $uuids = $records->pluck('uuid')->filter()->values()->all();

                        if ($uuids === []) {
                            return;
                        }

                        Artisan::call('queue:retry', ['id' => $uuids]);

                        Notification::make()
                            ->title('Selected failed jobs queued for retry')
                            ->body(sprintf('Retried %d failed job(s).', count($uuids)))
                            ->success()
                            ->send();
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
}
