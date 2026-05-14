<?php

namespace App\Filament\Employer\Resources;

use App\Filament\Employer\Resources\EmployerProfileResource\Pages;
use App\Models\Employer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class EmployerProfileResource extends Resource
{
    protected static ?string $model = Employer::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $navigationLabel = 'Company Profile';

    protected static ?string $modelLabel = 'Company Profile';

    protected static ?string $pluralModelLabel = 'Company Profile';

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
                Forms\Components\Section::make('Company Information')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('industry')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('website')
                            ->url()
                            ->maxLength(255),
                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Company Logo')
                            ->image()
                            ->disk('public')
                            ->directory('company-logos')
                            ->visibility('public'),
                        Forms\Components\Textarea::make('description')
                            ->rows(5)
                            ->columnSpanFull(),
                    ])->columns(2),
                Forms\Components\Section::make('Relocation and Accommodation')
                    ->schema([
                        Forms\Components\Toggle::make('relocation_support')
                            ->label('Relocation Support')
                            ->default(false),
                        Forms\Components\Toggle::make('accommodation_support')
                            ->label('Accommodation Support')
                            ->default(false),
                    ])->columns(2),
                Forms\Components\Section::make('Profile Readiness')
                    ->schema([
                        Forms\Components\Placeholder::make('profile_readiness')
                            ->label('Public Company Profile Readiness')
                            ->content(fn (?Employer $record): string => ($record?->profile_readiness ?? 0) . '% complete'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('company_name')
                    ->label('Company')
                    ->searchable(),
                Tables\Columns\TextColumn::make('city')
                    ->sortable(),
                Tables\Columns\TextColumn::make('industry')
                    ->sortable(),
                Tables\Columns\TextColumn::make('profile_readiness')
                    ->label('Readiness')
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\IconColumn::make('approved_at')
                    ->label('Approved')
                    ->boolean()
                    ->getStateUsing(fn (Employer $record) => $record->approved_at !== null),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit Profile'),
            ])
            ->bulkActions([]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->id());
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployerProfiles::route('/'),
            'edit' => Pages\EditEmployerProfile::route('/{record}/edit'),
        ];
    }
}
