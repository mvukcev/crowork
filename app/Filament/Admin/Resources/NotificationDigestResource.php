<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NotificationDigestResource\Pages;
use App\Models\NotificationDigest;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NotificationDigestResource extends Resource
{
    protected static ?string $model = NotificationDigest::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Notification Digests';

    protected static ?int $navigationSort = 12;

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
                Tables\Columns\TextColumn::make('scheduled_for')
                    ->label(__('admin.scheduled_for'))
                    ->date('Y-m-d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('period')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state ? __('admin.' . $state) : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state ? __('admin.' . $state) : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label(__('admin.user'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('sent_at')
                    ->label(__('admin.sent_at'))
                    ->dateTime('Y-m-d H:i:s')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('period')
                    ->options([
                        'daily' => __('admin.daily'),
                        'weekly' => __('admin.weekly'),
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => __('admin.pending'),
                        'sent' => __('admin.sent'),
                        'failed' => __('admin.failed'),
                    ]),
            ])
            ->actions([])
            ->bulkActions([])
            ->defaultSort('scheduled_for', 'desc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotificationDigests::route('/'),
        ];
    }
}
