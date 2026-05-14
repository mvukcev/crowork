<?php

namespace App\Filament\Employer\Resources;

use App\Filament\Employer\Resources\JobApplicationResource\Pages;
use App\Models\JobApplication;
use App\Services\ApplicationVisibilityService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JobApplicationResource extends Resource
{
    protected static ?string $model = JobApplication::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Applications';

    protected static ?string $modelLabel = 'Application';

    protected static ?string $pluralModelLabel = 'Applications';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->isEmployer();
    }

    public static function canEdit($record): bool
    {
        return (int) $record->job?->employer_id === (int) auth()->user()?->employer?->id;
    }

    public static function canView($record): bool
    {
        return (int) $record->job?->employer_id === (int) auth()->user()?->employer?->id;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Job Details')
                    ->schema([
                        Forms\Components\TextInput::make('job.title')
                            ->label('Job Position')
                            ->disabled()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('created_at')
                            ->label('Applied At')
                            ->disabled()
                            ->formatStateUsing(fn (JobApplication $record) => $record->created_at?->format('M d, Y \a\t g:i A') ?? '-'),
                    ])->columns(1),

                Forms\Components\Section::make('Applicant Information')
                    ->schema([
                        Forms\Components\Textarea::make('profile_snapshot_display')
                            ->label('Standardized Profile Snapshot')
                            ->disabled()
                            ->formatStateUsing(function (JobApplication $record) {
                                return self::formatSnapshotForDisplay($record);
                            })
                            ->rows(18)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('contact_data')
                            ->label('Contact Data')
                            ->disabled()
                            ->formatStateUsing(function (JobApplication $record): string {
                                $employer = auth()->user()->employer;
                                $visibilityService = app(ApplicationVisibilityService::class);

                                if ($visibilityService->getEffectiveVisibility($employer) !== 'full') {
                                    return 'Hidden by visibility policy';
                                }

                                return $record->worker?->email ?? 'Not provided';
                            }),
                    ])->columns(1),

                Forms\Components\Section::make('Application Message')
                    ->schema([
                        Forms\Components\Textarea::make('message')
                            ->label('Motivation Letter / Message')
                            ->disabled()
                            ->rows(6)
                            ->columnSpanFull(),
                    ])->columns(1),

                Forms\Components\Section::make('Pipeline')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options(JobApplication::statusOptions())
                            ->required()
                            ->live(),
                        Forms\Components\DateTimePicker::make('interview_at')
                            ->label('Interview Date & Time')
                            ->seconds(false),
                        Forms\Components\TextInput::make('score')
                            ->label('Score (1-10)')
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(10),
                        Forms\Components\Textarea::make('internal_note')
                            ->label('Internal Note')
                            ->rows(5)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('status_updated_at')
                            ->label('Last Status Change')
                            ->disabled()
                            ->formatStateUsing(fn (JobApplication $record): string => $record->status_updated_at?->format('M d, Y \a\t g:i A') ?? '-'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('worker_display')
                    ->label('Applicant')
                    ->formatStateUsing(function (JobApplication $record) {
                        return self::applicantDisplayName($record);
                    })
                    ->searchable(['profile_snapshot->first_name', 'profile_snapshot->last_name']),

                Tables\Columns\TextColumn::make('job.title')
                    ->label('Job')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nationality')
                    ->label('Nationality')
                    ->formatStateUsing(function (JobApplication $record) {
                        $masked = self::maskedSnapshot($record);

                        return isset($masked['nationality_country_code']) 
                            ? strtoupper($masked['nationality_country_code']) 
                            : '-';
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy('profile_snapshot->nationality_country_code', $direction);
                    }),

                Tables\Columns\TextColumn::make('skills')
                    ->label('Skills')
                    ->formatStateUsing(function (JobApplication $record) {
                        $masked = self::maskedSnapshot($record);

                        if (isset($masked['skills']) && is_array($masked['skills'])) {
                            return implode(', ', array_slice($masked['skills'], 0, 3)) 
                                . (count($masked['skills']) > 3 ? '...' : '');
                        }
                        
                        return '-';
                    })
                    ->limit(50),

                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'new',
                        'info' => fn (string $state): bool => in_array($state, ['reviewing', 'interview'], true),
                        'success' => fn (string $state): bool => in_array($state, ['shortlisted', 'offer', 'hired'], true),
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('score')
                    ->label('Score')
                    ->sortable(),

                Tables\Columns\TextColumn::make('interview_at')
                    ->label('Interview')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Applied')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Application Status')
                    ->options(JobApplication::statusOptions()),
                SelectFilter::make('job')
                    ->label('Job Position')
                    ->relationship('job', 'title')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Candidate'),
                Tables\Actions\EditAction::make()
                    ->label('Review'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_reviewing')
                        ->label('Mark as Reviewing')
                        ->action(fn ($records) => $records->each(fn (JobApplication $record) => $record->update([
                            'status' => JobApplication::STATUS_REVIEWING,
                            'status_updated_at' => now(),
                        ])))
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('mark_shortlisted')
                        ->label('Mark as Shortlisted')
                        ->action(fn ($records) => $records->each(fn (JobApplication $record) => $record->update([
                            'status' => JobApplication::STATUS_SHORTLISTED,
                            'status_updated_at' => now(),
                        ])))
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('mark_rejected')
                        ->label('Mark as Rejected')
                        ->action(fn ($records) => $records->each(fn (JobApplication $record) => $record->update([
                            'status' => JobApplication::STATUS_REJECTED,
                            'status_updated_at' => now(),
                        ])))
                        ->requiresConfirmation(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => 
                $query->whereHas('job', fn ($q) => 
                    $q->where('employer_id', auth()->user()->employer->id)
                )
            )
            ->defaultSort('created_at', 'desc')
            ->emptyStateIcon('heroicon-o-inbox')
            ->emptyStateHeading('No applications yet')
            ->emptyStateDescription('Applications for your jobs will appear here. When workers apply, you\'ll be able to review their profiles and manage their status.');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobApplications::route('/'),
            'view' => Pages\ViewJobApplication::route('/{record}'),
            'edit' => Pages\EditJobApplication::route('/{record}/edit'),
        ];
    }

    private static function maskedSnapshot(JobApplication $record): array
    {
        $employer = auth()->user()->employer;
        $visibilityService = app(ApplicationVisibilityService::class);

        return $visibilityService->maskSnapshot((array) ($record->profile_snapshot ?? []), $employer);
    }

    private static function applicantDisplayName(JobApplication $record): string
    {
        $employer = auth()->user()->employer;
        $visibilityService = app(ApplicationVisibilityService::class);
        $visibility = $visibilityService->getEffectiveVisibility($employer);

        if ($visibility === 'anonymous') {
            return 'Anonymous Applicant';
        }

        $masked = self::maskedSnapshot($record);
        $first = $masked['first_name'] ?? '?';
        $last = $masked['last_name'] ?? '?';

        return trim("{$first} {$last}");
    }

    private static function formatSnapshotForDisplay(JobApplication $record): string
    {
        $masked = self::maskedSnapshot($record);
        $lines = [];

        $name = trim(($masked['first_name'] ?? '') . ' ' . ($masked['last_name'] ?? ''));
        if ($name !== '') {
            $lines[] = 'Name: ' . $name;
        }

        $lines[] = 'Nationality: ' . (isset($masked['nationality_country_code']) ? strtoupper((string) $masked['nationality_country_code']) : 'Not provided');
        $lines[] = 'Birth Year: ' . ($masked['birth_year'] ?? 'Not provided');
        $lines[] = '';
        $lines[] = 'Skills: ' . (isset($masked['skills']) && is_array($masked['skills']) ? implode(', ', $masked['skills']) : 'Not provided');
        $lines[] = 'Languages: ' . (isset($masked['languages']) && is_array($masked['languages']) ? implode(', ', $masked['languages']) : 'Not provided');
        $lines[] = '';
        $lines[] = 'Education:';
        $lines[] = (string) ($masked['education_summary'] ?? 'Not provided');
        $lines[] = '';
        $lines[] = 'Work Experience:';
        $lines[] = (string) ($masked['work_experience'] ?? 'Not provided');
        $lines[] = '';
        $lines[] = 'Recommendations / Summary:';
        $lines[] = (string) ($masked['recommendations'] ?? 'Not provided');

        return implode("\n", $lines);
    }
}
