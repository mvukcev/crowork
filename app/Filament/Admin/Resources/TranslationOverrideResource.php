<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TranslationOverrideResource\Pages;
use App\Models\TranslationOverride;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class TranslationOverrideResource extends Resource
{
    protected static ?string $model = TranslationOverride::class;

    protected static ?string $navigationIcon = 'heroicon-o-language';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Translations';

    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('admin.translation_manager'))
                    ->schema([
                        Forms\Components\Select::make('locale')
                            ->label(__('ui.settings.language'))
                            ->options([
                                'en' => 'English',
                                'hr' => 'Croatian',
                                'de' => 'German',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('group')
                            ->label(__('ui.admin.translations_group'))
                            ->required()
                            ->maxLength(191)
                            ->helperText(__('ui.admin.translations_group_helper')),
                        Forms\Components\TextInput::make('key')
                            ->label(__('ui.admin.translations_key'))
                            ->required()
                            ->maxLength(191)
                            ->helperText(__('ui.admin.translations_key_helper')),
                        Forms\Components\Textarea::make('value')
                            ->label(__('ui.admin.translations_value'))
                            ->required()
                            ->rows(3)
                            ->helperText(__('ui.admin.translations_value_helper')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('locale')
                    ->label(__('ui.settings.language'))
                    ->sortable()
                    ->badge()
                    ->color('blue'),
                Tables\Columns\TextColumn::make('group')
                    ->label(__('ui.admin.translations_group'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('key')
                    ->label(__('ui.admin.translations_key'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label(__('ui.admin.translations_value'))
                    ->searchable()
                    ->limit(60)
                    ->wrap(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('ui.admin.updated'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('locale')
                    ->options([
                        'en' => 'English',
                        'hr' => 'Croatian',
                        'de' => 'German',
                    ]),
                Tables\Filters\SelectFilter::make('group')
                    ->options([
                        'admin' => 'admin',
                        'applications' => 'applications',
                        'auth' => 'auth',
                        'common' => 'common',
                        'dashboard' => 'dashboard',
                        'educations' => 'educations',
                        'employer' => 'employer',
                        'jobs' => 'jobs',
                        'navigation' => 'navigation',
                        'notifications' => 'notifications',
                        'settings' => 'settings',
                        'system' => 'system',
                        'validation' => 'validation',
                        'worker' => 'worker',
                        'ui' => 'ui',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('locale', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTranslationOverrides::route('/'),
            'create' => Pages\CreateTranslationOverride::route('/create'),
            'edit' => Pages\EditTranslationOverride::route('/{record}/edit'),
        ];
    }
}
