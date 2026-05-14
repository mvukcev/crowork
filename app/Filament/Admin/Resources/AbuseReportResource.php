<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AbuseReportResource\Pages;
use App\Models\AbuseReport;
use App\Models\Employer;
use App\Models\Job;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AbuseReportResource extends Resource
{
    protected static ?string $model = AbuseReport::class;

    protected static ?string $navigationIcon = 'heroicon-o-flag';

    protected static ?string $navigationGroup = 'Moderation';

    protected static ?string $navigationLabel = 'Abuse Reports';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Report')
                    ->schema([
                        Forms\Components\TextInput::make('type')
                            ->disabled(),
                        Forms\Components\TextInput::make('target_id')
                            ->label('Target ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('reason')
                            ->disabled(),
                        Forms\Components\Textarea::make('message')
                            ->rows(4)
                            ->columnSpanFull()
                            ->disabled(),
                    ])->columns(3),
                Forms\Components\Section::make('Moderation')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'open' => 'Open',
                                'reviewed' => 'Reviewed',
                                'dismissed' => 'Dismissed',
                                'action_taken' => 'Action Taken',
                            ])
                            ->required(),
                        Forms\Components\Textarea::make('admin_notes')
                            ->label('Admin Notes')
                            ->rows(5)
                            ->maxLength(5000)
                            ->columnSpanFull(),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Reported At')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->formatStateUsing(fn (string $state): string => self::statusLabel($state))
                    ->colors([
                        'warning' => fn (string $state): bool => in_array($state, ['new', 'open'], true),
                        'info' => 'reviewed',
                        'gray' => 'dismissed',
                        'success' => 'action_taken',
                    ]),
                Tables\Columns\TextColumn::make('type')
                    ->badge()
                    ->sortable(),
                Tables\Columns\TextColumn::make('target_id')
                    ->label('Reported Target')
                    ->getStateUsing(fn (AbuseReport $record): string => self::targetLabel($record))
                    ->url(fn (AbuseReport $record): ?string => self::publicTargetUrl($record), shouldOpenInNewTab: true)
                    ->color('primary'),
                Tables\Columns\TextColumn::make('reason')
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('Reported By')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('admin_notes')
                    ->limit(60)
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'open' => 'Open',
                        'reviewed' => 'Reviewed',
                        'dismissed' => 'Dismissed',
                        'action_taken' => 'Action Taken',
                        'new' => 'New (Legacy)',
                    ]),
                Tables\Filters\SelectFilter::make('type')
                    ->options(fn (): array => AbuseReport::query()->distinct()->pluck('type', 'type')->toArray()),
            ])
            ->actions([
                Tables\Actions\Action::make('openPublic')
                    ->label('Open Target')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (AbuseReport $record): ?string => self::publicTargetUrl($record), shouldOpenInNewTab: true)
                    ->visible(fn (AbuseReport $record): bool => self::publicTargetUrl($record) !== null),
                Tables\Actions\Action::make('openAdmin')
                    ->label('Admin Link')
                    ->icon('heroicon-o-link')
                    ->url(fn (AbuseReport $record): ?string => self::adminTargetUrl($record), shouldOpenInNewTab: true)
                    ->visible(fn (AbuseReport $record): bool => self::adminTargetUrl($record) !== null),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAbuseReports::route('/'),
            'edit' => Pages\EditAbuseReport::route('/{record}/edit'),
        ];
    }

    private static function statusLabel(string $status): string
    {
        return match ($status) {
            'open', 'new' => 'Open',
            'reviewed' => 'Reviewed',
            'dismissed' => 'Dismissed',
            'action_taken' => 'Action Taken',
            default => ucfirst($status),
        };
    }

    private static function targetLabel(AbuseReport $record): string
    {
        $typeLabel = ucfirst($record->type);
        return "{$typeLabel} #{$record->target_id}";
    }

    private static function publicTargetUrl(AbuseReport $record): ?string
    {
        if ($record->type === 'job') {
            $job = Job::query()->find($record->target_id);
            return $job ? route('jobs.show', $job) : null;
        }

        return null;
    }

    private static function adminTargetUrl(AbuseReport $record): ?string
    {
        if ($record->type === 'job' && Job::query()->whereKey($record->target_id)->exists()) {
            return route('filament.admin.resources.jobs.edit', ['record' => $record->target_id]);
        }

        if ($record->type === 'employer' && Employer::query()->whereKey($record->target_id)->exists()) {
            return route('filament.admin.resources.employers.edit', ['record' => $record->target_id]);
        }

        return null;
    }
}
