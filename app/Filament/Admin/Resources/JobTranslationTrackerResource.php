<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\JobTranslationTrackerResource\Pages;
use App\Jobs\TranslateJobPosting;
use App\Models\Job;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class JobTranslationTrackerResource extends Resource
{
    protected static ?string $model = Job::class;

    protected static ?string $navigationIcon = 'heroicon-o-language';

    protected static ?string $navigationGroup = 'Job Management';

    protected static ?string $navigationLabel = 'Job Translation Tracker';

    protected static ?string $modelLabel = 'Job Translation';

    protected static ?string $pluralModelLabel = 'Job Translation Tracker';

    protected static ?int $navigationSort = 3;

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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('Croatian title')
                    ->searchable()
                    ->wrap()
                    ->description(fn (Job $record): string => $record->employer_display_name ?? 'Employer unavailable'),
                Tables\Columns\TextColumn::make('listing_source')
                    ->label('Source')
                    ->getStateUsing(fn (Job $record): string => $record->isImportedFromHzz() ? 'HZZ' : 'Native')
                    ->badge()
                    ->color(fn (string $state): string => $state === 'Native' ? 'success' : 'info'),
                Tables\Columns\TextColumn::make('translation_status')
                    ->label('EN status')
                    ->getStateUsing(fn (Job $record): string => $record->translationStatus('en'))
                    ->formatStateUsing(fn (string $state): string => str($state)->replace('_', ' ')->title())
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'completed' => 'success',
                        'processing' => 'info',
                        'pending' => 'warning',
                        'failed' => 'danger',
                        'outdated' => 'warning',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('translated_title')
                    ->label('English title')
                    ->getStateUsing(function (Job $record): string {
                        $translation = $record->translations->firstWhere('locale', 'en');
                        return (string) ($translation?->content['title'] ?? '—');
                    })
                    ->wrap()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('translated_at')
                    ->label('Translated')
                    ->getStateUsing(fn (Job $record) => $record->translations->firstWhere('locale', 'en')?->translated_at)
                    ->dateTime()
                    ->placeholder('—')
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        return $query->orderBy(
                            \App\Models\JobTranslation::select('translated_at')
                                ->whereColumn('job_translations.job_id', 'job_postings.id')
                                ->where('locale', 'en')
                                ->limit(1),
                            $direction,
                        );
                    }),
                Tables\Columns\TextColumn::make('translation_error')
                    ->label('Last error')
                    ->getStateUsing(fn (Job $record): ?string => $record->translations->firstWhere('locale', 'en')?->last_error)
                    ->limit(80)
                    ->tooltip(fn (Job $record): ?string => $record->translations->firstWhere('locale', 'en')?->last_error)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('source')
                    ->options([
                        'native' => 'Native',
                        'hzz' => 'HZZ',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'hzz' => $query->where(fn (Builder $q) => $q
                                ->where('source_system', 'hzz')
                                ->orWhere('hzz_is_official', true)),
                            'native' => $query
                                ->where(fn (Builder $q) => $q->whereNull('source_system')->orWhere('source_system', '!=', 'hzz'))
                                ->where(fn (Builder $q) => $q->whereNull('hzz_is_official')->orWhere('hzz_is_official', false)),
                            default => $query,
                        };
                    }),
                Tables\Filters\SelectFilter::make('status')
                    ->label('EN status')
                    ->options([
                        'not_queued' => 'Not queued',
                        'pending' => 'Pending',
                        'processing' => 'Processing',
                        'completed' => 'Completed',
                        'failed' => 'Failed',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        $status = $data['value'] ?? null;
                        if ($status === 'not_queued') {
                            return $query->whereDoesntHave('translations', fn (Builder $q) => $q->where('locale', 'en'));
                        }
                        if (is_string($status) && $status !== '') {
                            return $query->whereHas('translations', fn (Builder $q) => $q
                                ->where('locale', 'en')
                                ->where('status', $status));
                        }
                        return $query;
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('viewCroatian')
                    ->label('HR')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Job $record): string => route('jobs.show', ['job' => $record, 'lang' => 'hr']))
                    ->openUrlInNewTab(),
                Tables\Actions\Action::make('viewEnglish')
                    ->label('EN')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Job $record): string => route('jobs.show', ['job' => $record, 'lang' => 'en']))
                    ->openUrlInNewTab()
                    ->visible(fn (Job $record): bool => $record->translationStatus('en') === 'completed'),
                Tables\Actions\Action::make('translate')
                    ->label(fn (Job $record): string => in_array($record->translationStatus('en'), ['failed', 'outdated'], true) ? 'Retry' : 'Translate')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->disabled(fn (): bool => ! setting('job_translation_enabled', true))
                    ->action(function (Job $record): void {
                        TranslateJobPosting::dispatch($record->id, 'en')
                            ->onQueue($record->translationQueueName());

                        Notification::make()
                            ->title('Translation queued')
                            ->body($record->isImportedFromHzz()
                                ? 'Queued in the lower-priority HZZ translation queue.'
                                : 'Queued in the priority native translation queue.')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkAction::make('translateSelected')
                    ->label('Queue selected translations')
                    ->icon('heroicon-o-language')
                    ->disabled(fn (): bool => ! setting('job_translation_enabled', true))
                    ->action(function (Collection $records): void {
                        $records
                            ->sortBy(fn (Job $record): int => $record->isImportedFromHzz() ? 1 : 0)
                            ->each(function (Job $record): void {
                                TranslateJobPosting::dispatch($record->id, 'en')
                                    ->onQueue($record->translationQueueName());
                            });

                        Notification::make()
                            ->title('Translations queued')
                            ->body($records->count() . ' listing(s) added to the translation queues.')
                            ->success()
                            ->send();
                    })
                    ->deselectRecordsAfterCompletion(),
            ])
            ->defaultSort('published_at', 'desc')
            ->poll('15s');
    }

    public static function getEloquentQuery(): Builder
    {
        return Job::query()
            ->with(['employer', 'translations'])
            ->active();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobTranslations::route('/'),
        ];
    }
}
