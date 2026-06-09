<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EmailSendLogResource\Pages;
use App\Models\EmailSendLog;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmailSendLogResource extends Resource
{
    protected static ?string $model = EmailSendLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Email Send Log';

    protected static ?int $navigationSort = 8;

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
                Tables\Columns\TextColumn::make('sent_at')
                    ->label('Sent At')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('to_address')
                    ->label('Recipient')
                    ->searchable()
                    ->copyable(),
                Tables\Columns\TextColumn::make('template')
                    ->label('Template')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject')
                    ->label('Subject')
                    ->searchable()
                    ->limit(80)
                    ->tooltip(fn (?string $state): ?string => is_string($state) && mb_strlen($state) > 80 ? $state : null),
                Tables\Columns\TextColumn::make('body_preview')
                    ->label('Body Preview')
                    ->wrap()
                    ->limit(120)
                    ->tooltip(fn (?string $state): ?string => is_string($state) && mb_strlen($state) > 120 ? $state : null),
                Tables\Columns\TextColumn::make('message_id')
                    ->label('Message ID')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(50),
                Tables\Columns\TextColumn::make('context_hash')
                    ->label('Context Hash')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->limit(20),
            ])
            ->filters([
                Tables\Filters\Filter::make('last_24h')
                    ->label('Last 24h')
                    ->query(fn ($query) => $query->where('sent_at', '>=', now()->subDay())),
                Tables\Filters\SelectFilter::make('template')
                    ->options(fn () => EmailSendLog::query()
                        ->select('template')
                        ->distinct()
                        ->orderBy('template')
                        ->pluck('template', 'template')
                        ->all()),
            ])
            ->actions([])
            ->bulkActions([])
            ->defaultSort('sent_at', 'desc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmailSendLogs::route('/'),
        ];
    }
}
