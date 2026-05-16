<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\WorkerResource\Pages;
use App\Models\User;
use App\Models\WorkerProfile;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class WorkerResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $modelLabel = 'Worker';

    protected static ?string $pluralModelLabel = 'Workers';

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'Workers';

    protected static ?int $navigationSort = 2;

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() || auth()->user()?->isMod();
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('role', User::ROLE_WORKER)
            ->with(['workerProfile'])
            ->withCount(['applications', 'educationApplications']);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Account')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('communication_language')
                            ->label('Communication language')
                            ->maxLength(32),
                        Forms\Components\DateTimePicker::make('email_verified_at')
                            ->label('Email verified at'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('communication_language')
                    ->label('Language')
                    ->badge()
                    ->formatStateUsing(fn (?string $state): string => $state ?: '-'),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->label('Email status')
                    ->badge()
                    ->formatStateUsing(fn ($state): string => $state ? 'Verified' : 'Unverified')
                    ->color(fn ($state): string => $state ? 'success' : 'warning'),
                Tables\Columns\TextColumn::make('workerProfile.completeness')
                    ->label('Profile completeness')
                    ->getStateUsing(fn (User $record): string => $record->workerProfile ? $record->workerProfile->completenessPercent() . '%' : 'Missing profile')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Missing profile' ? 'gray' : 'primary'),
                Tables\Columns\TextColumn::make('workerProfile.profile_visibility')
                    ->label('Profile visibility')
                    ->getStateUsing(fn (User $record): string => self::profileVisibilityLabel($record))
                    ->badge(),
                Tables\Columns\TextColumn::make('applications_count')
                    ->label('Applications')
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('education_applications_count')
                    ->label('Education applications')
                    ->sortable()
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('email_verified_at')
                    ->label('Verification status')
                    ->trueLabel('Verified')
                    ->falseLabel('Unverified'),
                Tables\Filters\SelectFilter::make('communication_language')
                    ->label('Communication language')
                    ->options(fn (): array => self::communicationLanguageOptions()),
                Tables\Filters\SelectFilter::make('profile_visibility')
                    ->label('Profile visibility')
                    ->options(WorkerProfile::visibilityOptions())
                    ->query(function (Builder $query, array $data): Builder {
                        $value = $data['value'] ?? null;

                        return $query->when(
                            filled($value),
                            fn (Builder $builder) => $builder->whereHas('workerProfile', fn (Builder $profileQuery) => $profileQuery->where('profile_visibility', $value))
                        );
                    }),
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')->label('Created from'),
                        Forms\Components\DatePicker::make('created_until')->label('Created until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['created_from'] ?? null, fn (Builder $builder, $date) => $builder->whereDate('created_at', '>=', $date))
                            ->when($data['created_until'] ?? null, fn (Builder $builder, $date) => $builder->whereDate('created_at', '<=', $date));
                    }),
                Tables\Filters\SelectFilter::make('profile_state')
                    ->label('Profile state')
                    ->options([
                        'has' => 'Has profile',
                        'missing' => 'Missing profile',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'] ?? null,
                            function (Builder $builder, string $value): Builder {
                                return $value === 'has'
                                    ? $builder->whereHas('workerProfile')
                                    : $builder->whereDoesntHave('workerProfile');
                            }
                        );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('previewCv')
                    ->label('Preview CV')
                    ->icon('heroicon-o-eye')
                    ->modalHeading(fn (User $record): string => $record->name . ' - CV Preview')
                    ->modalContent(fn (User $record) => view('filament.admin.worker-profile-preview', [
                        'worker' => $record,
                    ]))
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->visible(fn (User $record): bool => $record->workerProfile !== null),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc')
            ->striped();
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Account')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email'),
                        TextEntry::make('communication_language')
                            ->label('Communication language')
                            ->formatStateUsing(fn (?string $state): string => $state ?: '-'),
                        TextEntry::make('email_verified_at')
                            ->label('Email status')
                            ->state(fn (User $record): string => $record->email_verified_at ? 'Verified' : 'Unverified'),
                    ])
                    ->columns(2),
                Section::make('Worker Profile')
                    ->schema([
                        TextEntry::make('worker_profile_status')
                            ->label('Profile status')
                            ->state(fn (User $record): string => $record->workerProfile ? 'Present' : 'Missing'),
                        TextEntry::make('worker_profile_completeness')
                            ->label('Completeness')
                            ->state(fn (User $record): string => $record->workerProfile ? $record->workerProfile->completenessPercent() . '%' : 'Missing profile'),
                        TextEntry::make('worker_profile_visibility')
                            ->label('Visibility')
                            ->state(fn (User $record): string => self::profileVisibilityLabel($record)),
                        TextEntry::make('worker_profile_location')
                            ->label('Current location')
                            ->state(fn (User $record): string => self::currentLocation($record)),
                        TextEntry::make('worker_profile_desired_city')
                            ->label('Desired city')
                            ->state(fn (User $record): string => $record->workerProfile?->desired_city ?: '-'),
                        TextEntry::make('worker_profile_availability')
                            ->label('Availability date')
                            ->state(fn (User $record): string => $record->workerProfile?->availability_date?->toDateString() ?: '-'),
                        TextEntry::make('worker_profile_languages')
                            ->label('Languages')
                            ->state(fn (User $record): string => self::languageSummary($record)),
                        TextEntry::make('worker_profile_skills')
                            ->label('Skills')
                            ->state(fn (User $record): string => self::listSummary($record->workerProfile?->skills ?? [])),
                        TextEntry::make('worker_profile_desired_roles')
                            ->label('Desired roles')
                            ->state(fn (User $record): string => self::listSummary($record->workerProfile?->desired_roles ?? [])),
                        TextEntry::make('worker_profile_summary')
                            ->label('Professional summary')
                            ->state(fn (User $record): string => $record->workerProfile?->professional_summary ?: '-'),
                    ])
                    ->columns(2),
                Section::make('Applications')
                    ->schema([
                        TextEntry::make('applications_count')
                            ->label('Job applications')
                            ->state(fn (User $record): string => (string) $record->applications_count),
                        TextEntry::make('education_applications_count')
                            ->label('Education applications')
                            ->state(fn (User $record): string => (string) $record->education_applications_count),
                    ])
                    ->columns(2),
                Section::make('Timestamps')
                    ->schema([
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkers::route('/'),
            'view' => Pages\ViewWorker::route('/{record}'),
            'edit' => Pages\EditWorker::route('/{record}/edit'),
        ];
    }

    private static function communicationLanguageOptions(): array
    {
        return User::query()
            ->where('role', User::ROLE_WORKER)
            ->whereNotNull('communication_language')
            ->distinct()
            ->orderBy('communication_language')
            ->pluck('communication_language', 'communication_language')
            ->mapWithKeys(fn (string $value): array => [$value => strtoupper($value)])
            ->all();
    }

    private static function profileVisibilityLabel(User $record): string
    {
        $profile = $record->workerProfile;

        if (! $profile) {
            return 'Missing profile';
        }

        return WorkerProfile::visibilityOptions()[$profile->profile_visibility] ?? ucfirst((string) $profile->profile_visibility);
    }

    private static function currentLocation(User $record): string
    {
        $profile = $record->workerProfile;

        if (! $profile) {
            return 'Missing profile';
        }

        $parts = array_filter([
            $profile->current_city,
            $profile->current_country,
        ]);

        return $parts === [] ? '-' : implode(', ', $parts);
    }

    private static function languageSummary(User $record): string
    {
        $languages = $record->workerProfile?->languages ?? [];

        if (! is_array($languages) || $languages === []) {
            return '-';
        }

        $items = [];
        foreach ($languages as $language) {
            $name = is_array($language) ? trim((string) ($language['language'] ?? '')) : trim((string) $language);
            $level = is_array($language) ? trim((string) ($language['level'] ?? '')) : '';

            if ($name === '') {
                continue;
            }

            $items[] = $level !== '' ? $name . ' (' . $level . ')' : $name;
        }

        return $items === [] ? '-' : implode(', ', $items);
    }

    private static function listSummary(array $items): string
    {
        $values = array_values(array_filter(array_map(
            fn ($item) => is_scalar($item) ? trim((string) $item) : null,
            $items
        )));

        return $values === [] ? '-' : implode(', ', $values);
    }
}