<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\JobApplicationResource\Pages;
use App\Models\JobApplication;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JobApplicationResource extends Resource
{
    protected static ?string $model = JobApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Applications';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('job_id')
                    ->relationship('job', 'title')
                    ->disabled()
                    ->dehydrated(false)
                    ->searchable(),
                Forms\Components\Select::make('worker_id')
                    ->relationship('worker', 'name')
                    ->disabled()
                    ->dehydrated(false)
                    ->searchable(),
                Forms\Components\Textarea::make('message')
                    ->disabled()
                    ->dehydrated(false)
                    ->columnSpanFull(),
                Forms\Components\Select::make('status')
                    ->options([
                        'new' => 'New',
                        'reviewed' => 'Reviewed',
                        'shortlisted' => 'Shortlisted',
                        'rejected' => 'Rejected',
                    ])
                    ->disabled()
                    ->dehydrated(false),
                Forms\Components\Textarea::make('profile_snapshot_display')
                    ->label('Profile Snapshot (Read-Only - For Reference Only)')
                    ->disabled()
                    ->formatStateUsing(fn (JobApplication $record): string => json_encode($record->profile_snapshot ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                    ->rows(12)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('job_snapshot_display')
                    ->label('Job Snapshot (Read-Only - For Reference Only)')
                    ->disabled()
                    ->formatStateUsing(fn (JobApplication $record): string => json_encode($record->job_snapshot ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))
                    ->rows(8)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('job.title')
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
                Tables\Filters\SelectFilter::make('job_id')
                    ->relationship('job', 'title')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('viewSnapshot')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Worker Profile Snapshot')
                    ->modalContent(fn (JobApplication $record) => view('filament.admin.view-snapshot', ['snapshot' => $record->profile_snapshot]))
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
            'index' => Pages\ListJobApplications::route('/'),
            'create' => Pages\CreateJobApplication::route('/create'),
            'edit' => Pages\EditJobApplication::route('/{record}/edit'),
        ];
    }
}
