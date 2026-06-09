<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AuditLogResource\Pages;
use App\Models\AuditLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AuditLogResource extends Resource
{
    protected static ?string $model = AuditLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?string $navigationLabel = 'Audit Logs';

    protected static ?int $navigationSort = 10;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
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
                Forms\Components\Section::make('Audit Details')
                    ->schema([
                        Forms\Components\TextInput::make('action')
                            ->label('Action')
                            ->disabled(),
                        Forms\Components\TextInput::make('subject_type')
                            ->label('Subject Type')
                            ->disabled(),
                        Forms\Components\TextInput::make('subject_id')
                            ->label('Subject ID')
                            ->disabled(),
                        Forms\Components\TextInput::make('user.name')
                            ->label('User')
                            ->disabled(),
                        Forms\Components\TextInput::make('ip_address')
                            ->label('IP Address')
                            ->disabled(),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->disabled(),
                        Forms\Components\Textarea::make('changes')
                            ->label('Changes')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('created_at')
                            ->label('Created At')
                            ->disabled(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date & Time')
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),
                Tables\Columns\TextColumn::make('action')
                    ->label('Action')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('subject_type')
                    ->label('Subject')
                    ->getStateUsing(fn (AuditLog $record) => $record->subject_type ? "{$record->subject_type}({$record->subject_id})" : '-')
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.email')
                    ->label('User')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP Address')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('description')
                    ->label('Description')
                    ->limit(50)
                    ->wrap(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('action')
                    ->options([
                        'settings_updated' => 'Settings Updated',
                        'employer_approved' => 'Employer Approved',
                        'employer_rejected' => 'Employer Rejected',
                        'job_approved' => 'Job Approved',
                        'job_rejected' => 'Job Rejected',
                        'abuse_report_reviewed' => 'Abuse Report Reviewed',
                        'content_page_created' => 'Content Page Created',
                        'content_page_updated' => 'Content Page Updated',
                    ]),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('From'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'] ?? null, fn (Builder $q, $date) => $q->whereDate('created_at', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // No bulk delete for audit logs
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAuditLogs::route('/'),
            'view' => Pages\ViewAuditLog::route('/{record}'),
        ];
    }
}
