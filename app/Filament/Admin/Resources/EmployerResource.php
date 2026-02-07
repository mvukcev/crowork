<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EmployerResource\Pages;
use App\Models\Employer;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EmployerResource extends Resource
{
    protected static ?string $model = Employer::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office';

    protected static ?string $navigationGroup = 'User Management';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Account')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),
                        Forms\Components\DateTimePicker::make('approved_at')
                            ->label('Approval Date'),
                    ]),
                Forms\Components\Section::make('Company Info')
                    ->schema([
                        Forms\Components\TextInput::make('company_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('city')
                            ->maxLength(255),
                    ]),
                Forms\Components\Section::make('Approval Settings')
                    ->description('Override global approval requirements for this employer')
                    ->schema([
                        Forms\Components\Select::make('require_approval_override')
                            ->label('Require Approval for Listings')
                            ->options([
                                null => 'Use Global Setting',
                                true => 'Require Approval',
                                false => 'Auto-publish',
                            ])
                            ->native(false),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('company_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('city')
                    ->searchable(),
                Tables\Columns\IconColumn::make('approved_at')
                    ->label('Approved')
                    ->boolean()
                    ->getStateUsing(fn ($record) => $record->approved_at !== null)
                    ->sortable(),
                Tables\Columns\TextColumn::make('approved_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('approved')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('approved_at')),
                Tables\Filters\Filter::make('pending')
                    ->query(fn (Builder $query): Builder => $query->whereNull('approved_at')),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(fn (Employer $record) => $record->update(['approved_at' => now()]))
                    ->visible(fn (Employer $record) => $record->approved_at === null),
                Tables\Actions\Action::make('unapprove')
                    ->icon('heroicon-o-x-mark')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(fn (Employer $record) => $record->update(['approved_at' => null]))
                    ->visible(fn (Employer $record) => $record->approved_at !== null),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->icon('heroicon-o-check-badge')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['approved_at' => now()])),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEmployers::route('/'),
            'create' => Pages\CreateEmployer::route('/create'),
            'edit' => Pages\EditEmployer::route('/{record}/edit'),
        ];
    }
}
