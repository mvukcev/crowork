<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\EducationApplicationResource\Pages;
use App\Models\EducationApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EducationApplicationResource extends Resource
{
    protected static ?string $model = EducationApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Applications';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('education_id')
                    ->relationship('education', 'title')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('worker_id')
                    ->relationship('worker', 'name')
                    ->required()
                    ->searchable(),
                Forms\Components\Textarea::make('message')
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options([
                        'new' => 'New',
                        'reviewed' => 'Reviewed',
                        'shortlisted' => 'Shortlisted',
                        'rejected' => 'Rejected',
                    ])
                    ->required(),
                Forms\Components\KeyValue::make('profile_snapshot')
                    ->label('Profile Snapshot')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('education.title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('worker.name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('profile_snapshot.first_name')
                    ->label('First Name')
                    ->getStateUsing(fn ($record) => $record->profile_snapshot['first_name'] ?? '-'),
                Tables\Columns\TextColumn::make('profile_snapshot.last_name')
                    ->label('Last Name')
                    ->getStateUsing(fn ($record) => $record->profile_snapshot['last_name'] ?? '-'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'new',
                        'info' => 'reviewed',
                        'success' => 'shortlisted',
                        'danger' => 'rejected',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'reviewed' => 'Reviewed',
                        'shortlisted' => 'Shortlisted',
                        'rejected' => 'Rejected',
                    ]),
                Tables\Filters\SelectFilter::make('education_id')
                    ->relationship('education', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('viewSnapshot')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Worker Profile Snapshot')
                    ->modalContent(fn (EducationApplication $record) => view('filament.admin.view-snapshot', ['snapshot' => $record->profile_snapshot]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close'),
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
            'index' => Pages\ListEducationApplications::route('/'),
            'create' => Pages\CreateEducationApplication::route('/create'),
            'edit' => Pages\EditEducationApplication::route('/{record}/edit'),
        ];
    }
}
