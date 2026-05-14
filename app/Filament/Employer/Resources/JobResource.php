<?php

namespace App\Filament\Employer\Resources;

use App\Filament\Employer\Resources\JobResource\Pages;
use App\Models\Job;
use App\Services\ApprovalService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class JobResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Jobs';

    public static function canViewAny(): bool
    {
        return auth()->check() && auth()->user()->isEmployer();
    }

    public static function canEdit($record): bool
    {
        return (int) $record->employer_id === (int) auth()->user()?->employer?->id;
    }

    public static function canDelete($record): bool
    {
        return (int) $record->employer_id === (int) auth()->user()?->employer?->id;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('location_city')
                            ->label('City')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('category')
                            ->maxLength(255),
                        Forms\Components\Toggle::make('is_featured')
                            ->default(false),
                        Forms\Components\Toggle::make('is_urgent')
                            ->default(false),
                    ])->columns(2),
                Forms\Components\Section::make('Job Content')
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->label('About this job')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('responsibilities')
                            ->rows(6)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('requirements')
                            ->rows(6)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('benefits')
                            ->rows(5)
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Employment and Compensation')
                    ->schema([
                        Forms\Components\Select::make('contract_type')
                            ->label('Employment type')
                            ->options([
                                'full-time' => 'Full-time',
                                'part-time' => 'Part-time',
                                'seasonal' => 'Seasonal',
                                'contract' => 'Contract',
                                'temporary' => 'Temporary',
                                'internship' => 'Internship',
                            ])
                            ->native(false),
                        Forms\Components\Select::make('experience_level')
                            ->options([
                                'entry' => 'Entry level',
                                'junior' => 'Junior',
                                'mid' => 'Mid',
                                'senior' => 'Senior',
                                'lead' => 'Lead',
                            ])
                            ->default('mid')
                            ->native(false),
                        Forms\Components\TextInput::make('education_required')
                            ->maxLength(120),
                        Forms\Components\TextInput::make('contract_duration')
                            ->maxLength(120)
                            ->placeholder('e.g. 6 months, permanent'),
                        Forms\Components\TextInput::make('salary_min')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('salary_max')
                            ->numeric()
                            ->minValue(0),
                        Forms\Components\TextInput::make('salary_currency')
                            ->default('EUR')
                            ->maxLength(3),
                        Forms\Components\Select::make('salary_period')
                            ->options([
                                'hour' => 'Hour',
                                'month' => 'Month',
                            ])
                            ->default('month')
                            ->native(false),
                        Forms\Components\TagsInput::make('languages')
                            ->label('Language requirements')
                            ->placeholder('EN, HR, DE'),
                    ])->columns(3),
                Forms\Components\Section::make('Operations and Mobility')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date'),
                        Forms\Components\TextInput::make('start_flexibility')
                            ->placeholder('e.g. Immediate, within 2 weeks')
                            ->default('Negotiable')
                            ->maxLength(120),
                        Forms\Components\TextInput::make('positions_available')
                            ->numeric()
                            ->default(1)
                            ->minValue(1),
                        Forms\Components\TextInput::make('working_hours')
                            ->maxLength(120)
                            ->default('40h/week')
                            ->placeholder('e.g. 40h/week'),
                        Forms\Components\Textarea::make('shift_details')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('accommodation_provided')
                            ->default(false),
                        Forms\Components\Textarea::make('accommodation_details')
                            ->rows(3)
                            ->columnSpanFull(),
                        Forms\Components\Toggle::make('visa_support')
                            ->default(false),
                        Forms\Components\Textarea::make('visa_support_details')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])->columns(3),
                Forms\Components\Section::make('Application and Dates')
                    ->schema([
                        Forms\Components\Textarea::make('application_instructions')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('expires_at'),
                    ])->columns(2),
                Forms\Components\Section::make('Publishing')
                    ->schema([
                        Forms\Components\Placeholder::make('status_hint')
                            ->label('Current Status')
                            ->content(fn (?Job $record): string => $record ? ucfirst($record->status) : 'Draft (created automatically)'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location_city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('contract_type')
                    ->label('Employment')
                    ->formatStateUsing(fn (?string $state): string => $state ? str($state)->replace(['-', '_'], ' ')->title() : '-')
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_urgent')
                    ->label('Urgent')
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean(),
                Tables\Columns\TextColumn::make('applications_count')
                    ->label('Applications')
                    ->counts('applications')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'pending',
                        'success' => 'published',
                        'danger' => fn ($state) => in_array($state, ['rejected', 'delisted']),
                        'gray' => 'expired',
                    ]),
                Tables\Columns\TextColumn::make('expires_at')
                    ->label('Expires')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending Approval',
                        'published' => 'Published',
                        'rejected' => 'Rejected',
                        'delisted' => 'Delisted',
                        'expired' => 'Expired',
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->options(fn () => Job::query()->where('employer_id', auth()->user()->employer->id)->whereNotNull('category')->distinct()->pluck('category', 'category')->toArray()),
                Tables\Filters\SelectFilter::make('contract_type')
                    ->label('Employment type')
                    ->options(fn () => Job::query()->where('employer_id', auth()->user()->employer->id)->whereNotNull('contract_type')->distinct()->pluck('contract_type', 'contract_type')->toArray()),
            ])
            ->actions([
                Tables\Actions\Action::make('preview')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Job $record) => route('jobs.show', $record), shouldOpenInNewTab: true)
                    ->visible(fn (Job $record): bool => in_array($record->status, ['published', 'expired', 'delisted'], true)),
                Tables\Actions\Action::make('previewDraft')
                    ->label('Preview')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn (Job $record): string => 'Preview: ' . $record->title)
                    ->modalContent(fn (Job $record) => view('filament.employer.job-preview', ['job' => $record]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->visible(fn (Job $record): bool => in_array($record->status, ['draft', 'pending', 'rejected'], true)),
                Tables\Actions\Action::make('submitForApproval')
                    ->label('Submit for Approval')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Job $record): void {
                        $approvalService = app(ApprovalService::class);

                        if (! $approvalService->requiresApprovalForEmployer(auth()->user()->employer, 'job')) {
                            $approvalService->publish($record);
                            return;
                        }

                        $record->update([
                            'status' => 'pending',
                            'published_at' => null,
                        ]);
                    })
                    ->visible(fn (Job $record): bool => in_array($record->status, ['draft', 'rejected'], true)),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->recordUrl(fn (Job $record): string => static::getUrl('edit', ['record' => $record]))
            ->modifyQueryUsing(fn (Builder $query) => $query->where('employer_id', auth()->user()->employer->id))
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobs::route('/'),
            'create' => Pages\CreateJob::route('/create'),
            'edit' => Pages\EditJob::route('/{record}/edit'),
        ];
    }
}
