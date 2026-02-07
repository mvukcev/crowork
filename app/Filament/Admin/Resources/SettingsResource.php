<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\SettingsResource\Pages;
use App\Models\Setting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

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
                Forms\Components\Section::make('Approval Requirements')
                    ->description('Control whether listings require admin approval before publishing')
                    ->schema([
                        Forms\Components\Toggle::make('jobs_require_approval')
                            ->label('Require Approval for Job Postings')
                            ->helperText('When enabled, job postings created by employers will be in pending status until approved by admin')
                            ->default(true)
                            ->afterStateHydrated(function ($component, $state) {
                                // Extract boolean value from stored JSON
                                $component->state($state === true || (is_array($state) && ($state['value'] ?? false)));
                            })
                            ->dehydrateStateUsing(fn ($state) => ['value' => (bool) $state]),
                        Forms\Components\Toggle::make('educations_require_approval')
                            ->label('Require Approval for Education Programs')
                            ->helperText('When enabled, education programs created by employers will be in pending status until approved by admin')
                            ->default(true)
                            ->afterStateHydrated(function ($component, $state) {
                                // Extract boolean value from stored JSON
                                $component->state($state === true || (is_array($state) && ($state['value'] ?? false)));
                            })
                            ->dehydrateStateUsing(fn ($state) => ['value' => (bool) $state]),
                    ]),
                Forms\Components\Section::make('Application Visibility')
                    ->description('Control default visibility level for worker applications')
                    ->schema([
                        Forms\Components\Select::make('employer_application_visibility')
                            ->label('Default Application Visibility Level')
                            ->options([
                                'FULL' => 'Full (All worker information visible)',
                                'LIMITED' => 'Limited (Professional info only)',
                                'ANONYMOUS' => 'Anonymous (No personal identifiers)',
                            ])
                            ->default('LIMITED')
                            ->afterStateHydrated(function ($component, $state) {
                                // Extract visibility value from stored JSON
                                $component->state($state['value'] ?? 'LIMITED');
                            })
                            ->dehydrateStateUsing(fn ($state) => ['value' => $state]),
                        Forms\Components\Toggle::make('employer_can_export_applications')
                            ->label('Allow Employers to Export Applications')
                            ->helperText('When enabled, employers can export applicant data to CSV (respecting visibility settings)')
                            ->default(false)
                            ->afterStateHydrated(function ($component, $state) {
                                // Extract boolean value from stored JSON
                                $component->state($state === true || (is_array($state) && ($state['value'] ?? false)));
                            })
                            ->dehydrateStateUsing(fn ($state) => ['value' => (bool) $state]),
                        Forms\Components\TagsInput::make('employer_visible_fields')
                            ->label('Visible Fields in Limited View')
                            ->helperText('Fields that employers can see when visibility is set to Limited')
                            ->placeholder('Add field names')
                            ->default(['skills', 'experience', 'education', 'languages'])
                            ->afterStateHydrated(function ($component, $state) {
                                // Extract array value from stored JSON
                                $component->state(is_array($state['value'] ?? null) ? $state['value'] : []);
                            })
                            ->dehydrateStateUsing(fn ($state) => ['value' => $state]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('key')
                    ->label('Setting Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('value')
                    ->label('Value')
                    ->getStateUsing(function ($record) {
                        $value = $record->value;
                        if (is_array($value)) {
                            return json_encode($value, JSON_PRETTY_PRINT);
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSettings::route('/'),
            'edit' => Pages\EditSettings::route('/{record}/edit'),
        ];
    }
}
