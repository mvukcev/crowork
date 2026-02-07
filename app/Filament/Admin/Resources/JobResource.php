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

class JobResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static ?string $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationGroup = 'Job Management';

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
                        Forms\Components\Select::make('employer_id')
                            ->relationship('employer', 'company_name')
                            ->searchable()
                            ->preload(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'pending' => 'Pending',
                                'published' => 'Published',
                                'delisted' => 'Delisted',
                                'expired' => 'Expired',
                            ])
                            ->required()
                            ->default('pending'),
                    ]),
                Forms\Components\Section::make('Job Details')
                    ->schema([
                        Forms\Components\RichEditor::make('description')
                            ->required()
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('location_city')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('category')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TagsInput::make('languages'),
                        Forms\Components\TextInput::make('contract_type')
                            ->maxLength(255),
                    ]),
                Forms\Components\Section::make('Salary')
                    ->schema([
                        Forms\Components\TextInput::make('salary_min')
                            ->numeric(),
                        Forms\Components\TextInput::make('salary_max')
                            ->numeric(),
                        Forms\Components\TextInput::make('salary_currency')
                            ->default('EUR')
                            ->maxLength(3),
                        Forms\Components\Select::make('salary_period')
                            ->options([
                                'hour' => 'Hour',
                                'month' => 'Month',
                            ])
                            ->default('month'),
                    ])->columns(2),
                Forms\Components\Section::make('Accommodation')
                    ->schema([
                        Forms\Components\Toggle::make('accommodation_provided')
                            ->default(false),
                        Forms\Components\Textarea::make('accommodation_details')
                            ->columnSpanFull(),
                    ]),
                Forms\Components\Section::make('Dates')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date'),
                        Forms\Components\DateTimePicker::make('published_at'),
                        Forms\Components\DateTimePicker::make('expires_at'),
                    ])->columns(3),
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
                Tables\Columns\BadgeColumn::make('status')
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
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'published' => 'Published',
                        'delisted' => 'Delisted',
                        'expired' => 'Expired',
                    ]),
                Tables\Filters\SelectFilter::make('location_city')
                    ->options(fn () => Job::query()->distinct()->pluck('location_city', 'location_city')->toArray()),
                Tables\Filters\SelectFilter::make('category')
                    ->options(fn () => Job::query()->distinct()->pluck('category', 'category')->toArray()),
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
