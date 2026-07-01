<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\JobResource\Pages;
use App\Filament\Admin\Resources\JobResource\RelationManagers;
use App\Models\Job;
use App\Services\ApprovalService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class JobResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Job Management';

    protected static ?string $navigationLabel = 'Jobs';

    protected static ?int $navigationSort = 1;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where(function (Builder $query): void {
                $query->whereNull('source_system')
                    ->orWhere('source_system', '!=', 'hzz');
            })
            ->where(function (Builder $query): void {
                $query->whereNull('hzz_is_official')
                    ->orWhere('hzz_is_official', false);
            });
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('jobs.basic_information'))
                    ->description('Fields marked with * are required before saving.')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Title *')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Forms\Set $set, Forms\Get $get, ?string $state, ?string $old): void {
                                $currentSlug = (string) ($get('slug') ?? '');
                                $oldSlug = Str::slug((string) ($old ?? ''));

                                if ($currentSlug === '' || $currentSlug === $oldSlug) {
                                    $set('slug', Str::slug((string) ($state ?? '')));
                                }
                            })
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->helperText('Auto-generated from title; you can still adjust it if needed.')
                            ->maxLength(255),
                        Forms\Components\Select::make('employer_id')
                            ->label('Employer *')
                            ->relationship('employer', 'company_name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        Forms\Components\TextInput::make('location_city')
                            ->label(__('jobs.city'))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('category')
                            ->maxLength(255),
                        Forms\Components\Select::make('status')
                            ->label('Status *')
                            ->options([
                                'draft' => __('jobs.draft'),
                                'pending' => __('jobs.pending'),
                                'published' => __('jobs.published'),
                                'delisted' => __('jobs.delisted'),
                                'expired' => __('jobs.expired'),
                            ])
                            ->required()
                            ->default('pending'),
                        Forms\Components\Toggle::make('is_featured')
                            ->default(false),
                        Forms\Components\Toggle::make('is_urgent')
                            ->default(false),
                    ])->columns(2),
                Forms\Components\Section::make(__('jobs.job_content'))
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->label(__('jobs.about_this_job') . ' *')
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
                Forms\Components\Section::make(__('jobs.employment_and_compensation'))
                    ->schema([
                        Forms\Components\Select::make('contract_type')
                            ->label(__('jobs.employment_type'))
                            ->options([
                                'full-time' => __('jobs.full_time'),
                                'part-time' => __('jobs.part_time'),
                                'seasonal' => __('jobs.seasonal'),
                                'contract' => __('jobs.contract'),
                                'temporary' => __('jobs.temporary'),
                                'internship' => __('jobs.internship'),
                            ])
                            ->native(false),
                        Forms\Components\Select::make('experience_level')
                            ->options([
                                'entry' => __('jobs.entry_level'),
                                'junior' => __('jobs.junior'),
                                'mid' => __('jobs.mid'),
                                'senior' => __('jobs.senior'),
                                'lead' => __('jobs.lead'),
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
                        Forms\Components\TextInput::make('salary_currency')
                            ->default('EUR')
                            ->maxLength(3),
                        Forms\Components\Select::make('salary_period')
                            ->options([
                                'hour' => __('jobs.hour'),
                                'month' => __('jobs.month'),
                            ])
                            ->default('month')
                            ->native(false),
                        Forms\Components\TagsInput::make('languages')
                            ->label(__('jobs.language_requirements'))
                            ->placeholder('EN, HR, DE'),
                    ])->columns(3),
                Forms\Components\Section::make(__('jobs.operations_and_mobility'))
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
                Forms\Components\Section::make(__('jobs.application_and_dates'))
                    ->schema([
                        Forms\Components\Textarea::make('application_instructions')
                            ->rows(4)
                            ->columnSpanFull(),
                        Forms\Components\DateTimePicker::make('published_at'),
                        Forms\Components\DateTimePicker::make('expires_at'),
                    ])->columns(2),
                Forms\Components\Section::make('Source & HZZ')
                    ->schema([
                        Forms\Components\Select::make('source_system')
                            ->options([
                                'manual' => 'Manual',
                                'hzz' => 'HZZ',
                            ])
                            ->native(false),
                        Forms\Components\Toggle::make('hzz_is_official')
                            ->label('Official HZZ source')
                            ->default(false),
                        Forms\Components\TextInput::make('source_reference')
                            ->maxLength(190),
                        Forms\Components\TextInput::make('source_url')
                            ->url()
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('hzz_apply_email')
                            ->email()
                            ->maxLength(190),
                        Forms\Components\TextInput::make('hzz_apply_contact_type')
                            ->maxLength(40),
                        Forms\Components\TextInput::make('hzz_apply_url')
                            ->url()
                            ->maxLength(2000)
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('hzz_legal_notice')
                            ->rows(4)
                            ->columnSpanFull(),
                    ])->columns(2),
                Forms\Components\Section::make(__('jobs.publishing'))
                    ->schema([
                        Forms\Components\Placeholder::make('status_hint')
                            ->label(__('jobs.current_status'))
                            ->content(fn (?Job $record): string => $record ? __('jobs.' . $record->status) : __('jobs.draft_created_automatically')),
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
                Tables\Columns\TextColumn::make('employer.company_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location_city')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->searchable(),
                Tables\Columns\TextColumn::make('source_system')
                    ->badge()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('hzz_is_official')
                    ->label('HZZ')
                    ->boolean(),
                Tables\Columns\TextColumn::make('contract_type')
                    ->label(__('jobs.employment_type'))
                    ->formatStateUsing(fn (?string $state): string => $state ? str($state)->replace(['-', '_'], ' ')->title() : '-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('experience_level')
                    ->formatStateUsing(fn (?string $state): string => $state ? str($state)->replace(['-', '_'], ' ')->title() : '-')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('education_required')
                    ->toggleable(),
                Tables\Columns\TextColumn::make('positions_available')
                    ->label(__('jobs.positions'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->date()
                    ->toggleable(),
                Tables\Columns\IconColumn::make('is_urgent')
                    ->label(__('jobs.urgent'))
                    ->boolean(),
                Tables\Columns\IconColumn::make('is_featured')
                    ->label(__('jobs.featured'))
                    ->boolean(),
                Tables\Columns\BadgeColumn::make('status')
                    ->formatStateUsing(fn (?string $state): string => $state ? __('jobs.' . $state) : '-')
                    ->colors([
                        'secondary' => 'draft',
                        'warning' => 'pending',
                        'success' => 'published',
                        'danger' => fn ($state) => in_array($state, ['delisted', 'expired']),
                    ]),
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
                        'draft' => __('jobs.draft'),
                        'pending' => __('jobs.pending'),
                        'published' => __('jobs.published'),
                        'delisted' => __('jobs.delisted'),
                        'expired' => __('jobs.expired'),
                    ]),
                Tables\Filters\SelectFilter::make('location_city')
                    ->options(fn () => Job::query()->distinct()->pluck('location_city', 'location_city')->toArray()),
                Tables\Filters\SelectFilter::make('category')
                    ->options(fn () => Job::query()->distinct()->pluck('category', 'category')->toArray()),
                Tables\Filters\SelectFilter::make('source_system')
                    ->options([
                        'manual' => 'Manual',
                        'hzz' => 'HZZ',
                    ]),
                Tables\Filters\TernaryFilter::make('hzz_is_official')
                    ->label('Official HZZ'),
                Tables\Filters\SelectFilter::make('contract_type')
                    ->label(__('jobs.employment_type'))
                    ->options(fn () => Job::query()->whereNotNull('contract_type')->distinct()->pluck('contract_type', 'contract_type')->toArray()),
                Tables\Filters\SelectFilter::make('experience_level')
                    ->options(fn () => Job::query()->whereNotNull('experience_level')->distinct()->pluck('experience_level', 'experience_level')->toArray()),
            ])
            ->actions([
                Tables\Actions\Action::make('approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Job $record) {
                        $approvalService = new ApprovalService();
                        $approvalService->publish($record);
                    })
                    ->visible(fn (Job $record) => $record->status === 'pending'),
                Tables\Actions\Action::make('delist')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (Job $record) {
                        $approvalService = new ApprovalService();
                        $approvalService->delist($record);
                    })
                    ->visible(fn (Job $record) => in_array($record->status, ['published', 'pending'])),
                Tables\Actions\Action::make('relist')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function (Job $record) {
                        $approvalService = new ApprovalService();
                        $approvalService->publish($record);
                    })
                    ->visible(fn (Job $record) => $record->status === 'delisted'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('approve')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $approvalService = new ApprovalService();
                            $records->each(fn ($record) => $approvalService->publish($record));
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('delist')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $approvalService = new ApprovalService();
                            $records->each(fn ($record) => $approvalService->delist($record));
                        })
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
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
