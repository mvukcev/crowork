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

    protected static ?string $navigationGroup = 'System';

    protected static ?string $navigationLabel = 'Translations';

    protected static ?int $navigationSort = 11;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Translation Override')
                    ->schema([
                        Forms\Components\Select::make('locale')
                            ->label('Language')
                            ->options([
                                'en' => 'English',
                                'hr' => 'Croatian',
                                'de' => 'German',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('group')
                            ->label('Translation Group')
                            ->required()
                            ->maxLength(191)
                            ->helperText('e.g., auth, dashboard, common, worker, employer'),
                        Forms\Components\TextInput::make('key')
                            ->label('Translation Key')
                            ->required()
                            ->maxLength(191)
                            ->helperText('e.g., welcome, login_button, page_title'),
                        Forms\Components\Textarea::make('value')
                            ->label('Translation Value')
                            ->required()
                            ->rows(3)
                            ->helperText('This text will override the default translation file value.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('locale')
                    ->label('Language')
                    ->sortable()
                    ->badge()
                    ->color('blue'),
                Tables\Columns\TextColumn::make('group')
                    ->label('Group')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('key')
                    ->label('Key')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->searchable()
                    ->limit(60)
                    ->wrap(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
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
                        'auth' => 'auth',
                        'dashboard' => 'dashboard',
                        'common' => 'common',
                        'worker' => 'worker',
                        'employer' => 'employer',
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
