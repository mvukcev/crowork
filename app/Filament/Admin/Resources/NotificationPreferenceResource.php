<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NotificationPreferenceResource\Pages;
use App\Models\NotificationPreference;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NotificationPreferenceResource extends Resource
{
    protected static ?string $model = NotificationPreference::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Notification Preferences';

    protected static ?int $navigationSort = 11;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('user.email')
                    ->label('User')
                    ->disabled(),
                Forms\Components\TextInput::make('category')
                    ->disabled(),
                Forms\Components\Toggle::make('email_enabled')
                    ->required(),
                Forms\Components\Toggle::make('database_enabled')
                    ->required(),
                Forms\Components\Select::make('digest_frequency')
                    ->options([
                        'none' => 'None',
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                    ])
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.email')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('category')
                    ->badge()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('email_enabled')
                    ->boolean(),
                Tables\Columns\IconColumn::make('database_enabled')
                    ->boolean(),
                Tables\Columns\TextColumn::make('digest_frequency')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('digest_frequency')
                    ->options([
                        'none' => 'None',
                        'daily' => 'Daily',
                        'weekly' => 'Weekly',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('updated_at', 'desc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotificationPreferences::route('/'),
            'edit' => Pages\EditNotificationPreference::route('/{record}/edit'),
        ];
    }
}
