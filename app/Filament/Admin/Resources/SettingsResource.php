<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SettingsResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SettingsResource extends Resource
{
    protected static ?string $model = Setting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Settings';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Setting')
                    ->description('Edit platform behavior for this setting key')
                    ->schema([
                        Forms\Components\TextInput::make('key')
                            ->label('Setting Key')
                            ->disabled(),
                        Forms\Components\Placeholder::make('setting_label')
                            ->label('Setting Label')
                            ->content(fn (?Setting $record) => Setting::definition($record?->key ?? '')['label'] ?? $record?->key ?? '-'),
                        Forms\Components\Placeholder::make('setting_group')
                            ->label('Group')
                            ->content(fn (?Setting $record) => Setting::definition($record?->key ?? '')['group'] ?? 'System'),
                        Forms\Components\Toggle::make('value_boolean')
                            ->label('Value')
                            ->statePath('value')
                            ->default(false)
                            ->visible(fn (?Setting $record) => self::settingType($record) === 'boolean')
                            ->dehydrateStateUsing(fn ($state) => (bool) $state),
                        Forms\Components\Select::make('value_select')
                            ->label('Value')
                            ->statePath('value')
                            ->options(fn (?Setting $record) => self::settingOptions($record))
                            ->native(false)
                            ->visible(fn (?Setting $record) => self::settingType($record) === 'select'),
                        Forms\Components\TextInput::make('value_integer')
                            ->label('Value')
                            ->statePath('value')
                            ->numeric()
                            ->minValue(1)
                            ->visible(fn (?Setting $record) => self::settingType($record) === 'integer')
                            ->dehydrateStateUsing(fn ($state) => is_numeric($state) ? (int) $state : null),
                        Forms\Components\TextInput::make('value_email')
                            ->label('Value')
                            ->statePath('value')
                            ->email()
                            ->maxLength(255)
                            ->visible(fn (?Setting $record) => self::settingType($record) === 'email'),
                        Forms\Components\TagsInput::make('value_array')
                            ->label('Value')
                            ->statePath('value')
                            ->visible(fn (?Setting $record) => self::settingType($record) === 'array')
                            ->helperText('Used for advanced visibility field control in limited mode.'),
                        Forms\Components\Textarea::make('value_text')
                            ->label('Value')
                            ->statePath('value')
                            ->rows(4)
                            ->visible(fn (?Setting $record) => !in_array(self::settingType($record), ['boolean', 'select', 'integer', 'email', 'array'], true)),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Setting Key')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('setting_label')
                    ->label('Label')
                    ->getStateUsing(fn (Setting $record) => Setting::definition($record->key)['label'] ?? $record->key)
                    ->searchable(),
                Tables\Columns\TextColumn::make('setting_group')
                    ->label('Group')
                    ->getStateUsing(fn (Setting $record) => Setting::definition($record->key)['group'] ?? 'System')
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->getStateUsing(function (Setting $record) {
                        $value = Setting::unwrapValue($record->value);

                        if (is_bool($value)) {
                            return $value ? 'Enabled' : 'Disabled';
                        }

                        if (is_array($value)) {
                            return implode(', ', $value);
                        }

                        return (string) $value;
                    })
                    ->wrap(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // No bulk delete for settings
                ]),
            ])
            ->defaultSort('key');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereIn('key', Setting::adminManagedKeys());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'edit' => Pages\EditSettings::route('/{record}/edit'),
        ];
    }

    private static function settingType(?Setting $record): string
    {
        if (! $record) {
            return 'text';
        }

        return Setting::definition($record->key)['type'] ?? 'text';
    }

    private static function settingOptions(?Setting $record): array
    {
        if (! $record) {
            return [];
        }

        return Setting::definition($record->key)['options'] ?? [];
    }
}
