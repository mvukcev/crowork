<?php

namespace App\Filament\Admin\Resources\JobTranslationTrackerResource\Pages;

use App\Filament\Admin\Resources\JobTranslationTrackerResource;
use App\Jobs\TranslateJobPosting;
use App\Models\Job;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListJobTranslations extends ListRecords
{
    protected static string $resource = JobTranslationTrackerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('queueNative')
                ->label('Queue native first')
                ->icon('heroicon-o-bolt')
                ->color('success')
                ->disabled(fn (): bool => ! setting('job_translation_enabled', true))
                ->requiresConfirmation()
                ->action(fn () => $this->queueListings(false)),
            Action::make('queueHzz')
                ->label('Queue HZZ')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->disabled(fn (): bool => ! setting('job_translation_enabled', true))
                ->requiresConfirmation()
                ->action(fn () => $this->queueListings(true)),
        ];
    }

    private function queueListings(bool $hzz): void
    {
        $count = 0;

        Job::query()
            ->active()
            ->when(
                $hzz,
                fn ($query) => $query->where(fn ($query) => $query
                    ->where('source_system', 'hzz')
                    ->orWhere('hzz_is_official', true)),
                fn ($query) => $query
                    ->where(fn ($query) => $query->whereNull('source_system')->orWhere('source_system', '!=', 'hzz'))
                    ->where(fn ($query) => $query->whereNull('hzz_is_official')->orWhere('hzz_is_official', false)),
            )
            ->each(function (Job $job) use (&$count): void {
                TranslateJobPosting::dispatch($job->id, 'en')->onQueue($job->translationQueueName());
                $count++;
            });

        Notification::make()
            ->title($hzz ? 'HZZ translations queued' : 'Native translations queued')
            ->body($count . ' active listing(s) added to the queue.')
            ->success()
            ->send();
    }
}
