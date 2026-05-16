<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EducationResource\Pages;
use App\Models\Education;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EducationResource extends Resource
{
    protected static ?string $model = Education::class;

    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $navigationGroup = 'Education Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\Section::make(__('educations.basic_information'))
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->options([
                                    'draft' => __('educations.draft'),
                                    'pending' => __('educations.pending'),
                                    'published' => __('educations.published'),
                                    'delisted' => __('educations.delisted'),
                                    'expired' => __('educations.expired'),
                            ])
                            ->required()
                            ->default('pending'),
                    ]),
                Forms\Components\Section::make('Content')
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make(__('educations.location'))
                    ->schema([
                        Forms\Components\TextInput::make('city')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_online')
                            ->default(false),
                    ]),
                Forms\Components\Section::make(__('educations.pricing_and_capacity'))
                    ->schema([
                        Forms\Components\TextInput::make('price_cents')
                            ->numeric(),
                        Forms\Components\TextInput::make('currency')
                            ->default('EUR')
                            ->maxLength(3),
                        Forms\Components\TextInput::make('capacity')
                            ->numeric(),
                    ])->columns(3),
                Forms\Components\Section::make(__('educations.dates'))
                    ->schema([
                        Forms\Components\DatePicker::make('start_date'),
                        Forms\Components\DateTimePicker::make('published_at'),
                        Forms\Components\DateTimePicker::make('expires_at'),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_online')
                    ->boolean(),
                Tables\Columns\TextColumn::make('price_cents')
                    ->money('EUR', divideBy: 100)
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->formatStateUsing(fn (?string $state): string => $state ? __('educations.' . $state) : '-')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'pending',
                        'success' => 'published',
                        'danger' => fn ($state) => in_array($state, ['delisted', 'expired']),
                    ]),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => __('educations.draft'),
                        'pending' => __('educations.pending'),
                        'published' => __('educations.published'),
                        'delisted' => __('educations.delisted'),
                        'expired' => __('educations.expired'),
                    ]),
                Tables\Filters\TernaryFilter::make('is_online')
                    ->label(__('educations.online'))
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\Action::make('publish')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Education $record) {
                        $record->update([
                            'status' => 'published',
                            'published_at' => now(),
                        ]);
                    })
                    ->visible(fn (Education $record) => $record->status !== 'published'),
                Tables\Actions\Action::make('delist')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn (Education $record) => $record->update(['status' => 'delisted']))
                    ->visible(fn (Education $record) => $record->status === 'published'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEducation::route('/'),
            'create' => Pages\CreateEducation::route('/create'),
            'edit' => Pages\EditEducation::route('/{record}/edit'),
        ];
    }
}
