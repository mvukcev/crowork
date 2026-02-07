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
                    ])->columns(1),

                Forms\Components\Section::make('Applicant Information')
                    ->schema([
                        Forms\Components\Textarea::make('profile_snapshot_display')
                            ->label('Profile Summary')
                            ->disabled()
                            ->formatStateUsing(function (JobApplication $record) {
                                $snapshot = $record->profile_snapshot;
                                $employer = auth()->user()->employer;
                                $visibilityService = new ApplicationVisibilityService();
                                
                                $masked = $visibilityService->maskSnapshot($snapshot, $employer);
                                
                                $lines = [];
                                if (isset($masked['first_name']) || isset($masked['last_name'])) {
                                    $name = trim(($masked['first_name'] ?? '') . ' ' . ($masked['last_name'] ?? ''));
                                    if ($name) {
                                        $lines[] = "Name: $name";
                                    }
                                }
                                if (isset($masked['nationality_country_code'])) {
                                    $lines[] = "Nationality: " . strtoupper($masked['nationality_country_code']);
                                }
                                if (isset($masked['birth_year'])) {
                                    $lines[] = "Birth Year: {$masked['birth_year']}";
                                }
                                if (isset($masked['education_summary'])) {
                                    $lines[] = "\nEducation:\n{$masked['education_summary']}";
                                }
                                if (isset($masked['work_experience'])) {
                                    $lines[] = "\nExperience:\n{$masked['work_experience']}";
                                }
                                if (isset($masked['skills']) && is_array($masked['skills'])) {
                                    $lines[] = "\nSkills: " . implode(', ', $masked['skills']);
                                }
                                if (isset($masked['recommendations'])) {
                                    $lines[] = "\nRecommendations:\n{$masked['recommendations']}";
                                }
                                
                                return implode("\n", $lines);
                            })
                            ->columnSpanFull(),
                    ])->columns(1),

                Forms\Components\Section::make('Application Message')
                    ->schema([
                        Forms\Components\Textarea::make('message')
                            ->label('Message to Employer')
                            ->disabled()
                            ->columnSpanFull(),
                    ])->columns(1),

                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'new' => 'New',
                                'reviewed' => 'Reviewed',
                                'shortlisted' => 'Shortlisted',
                                'rejected' => 'Rejected',
                            ])
                            ->required(),
                    ])->columns(1),

                Forms\Components\Section::make('Metadata')
                    ->schema([
                        Forms\Components\TextInput::make('created_at')
                            ->label('Applied At')
                            ->disabled()
                            ->formatStateUsing(fn (JobApplication $record) => $record->created_at->format('M d, Y \a\t g:i A')),
                    ])->columns(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('worker_display')
                    ->label('Applicant')
                    ->formatStateUsing(function (JobApplication $record) {
                        $employer = auth()->user()->employer;
                        $visibilityService = new ApplicationVisibilityService();
                        $visibility = $visibilityService->getEffectiveVisibility($employer);
                        
                        if ($visibility === 'anonymous') {
                            return 'Anonymous Applicant';
                        }
                        
                        $snapshot = $record->profile_snapshot;
                        $masked = $visibilityService->maskSnapshot($snapshot, $employer);
                        
                        $first = $masked['first_name'] ?? '?';
                        $last = $masked['last_name'] ?? '?';
                        
                        return trim("$first $last");
                    })
                    ->searchable(['profile_snapshot->first_name', 'profile_snapshot->last_name']),

                Tables\Columns\TextColumn::make('nationality')
                    ->label('Nationality')
                    ->formatStateUsing(function (JobApplication $record) {
                        $employer = auth()->user()->employer;
                        $visibilityService = new ApplicationVisibilityService();
                        $masked = $visibilityService->maskSnapshot($record->profile_snapshot, $employer);
                        
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
                        $employer = auth()->user()->employer;
                        $visibilityService = new ApplicationVisibilityService();
                        $masked = $visibilityService->maskSnapshot($record->profile_snapshot, $employer);
                        
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
                        'info' => 'reviewed',
                        'success' => 'shortlisted',
                        'danger' => 'rejected',
                    ])
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Applied')
                    ->dateTime('M d, Y')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Application Status')
                    ->options([
                        'new' => 'New',
                        'reviewed' => 'Reviewed',
                        'shortlisted' => 'Shortlisted',
                        'rejected' => 'Rejected',
                    ]),
                SelectFilter::make('job')
                    ->label('Job Position')
                    ->relationship('job', 'title')
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Review'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('mark_reviewed')
                        ->label('Mark as Reviewed')
                        ->action(fn (JobApplication $record) => $record->update(['status' => 'reviewed']))
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('mark_shortlisted')
                        ->label('Mark as Shortlisted')
                        ->action(fn (JobApplication $record) => $record->update(['status' => 'shortlisted']))
                        ->requiresConfirmation(),
                    Tables\Actions\BulkAction::make('mark_rejected')
                        ->label('Mark as Rejected')
                        ->action(fn (JobApplication $record) => $record->update(['status' => 'rejected']))
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
}
