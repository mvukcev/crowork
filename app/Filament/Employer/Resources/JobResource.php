<?php

namespace App\Filament\Employer\Resources;

use App\Filament\Employer\Resources\JobResource\Pages;
use App\Models\Job;
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
                        Forms\Components\DateTimePicker::make('expires_at'),
                    ])->columns(2),
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
                        'pending' => 'Pending Approval',
                        'published' => 'Published',
                        'delisted' => 'Delisted',
                        'expired' => 'Expired',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
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
