<?php

namespace App\Jobs;

use App\Models\Job;
use App\Models\JobTranslation;
use App\Services\Translation\AzureTranslator;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Throwable;

class TranslateJobPosting implements ShouldQueue
{
    use Queueable;

    public int $tries = 4;

    public int $timeout = 120;

    /** @var array<int, int> */
    public array $backoff = [60, 300, 900];

    public function __construct(
        public readonly int $jobId,
        public readonly string $targetLocale = 'en',
    ) {
    }

    public function handle(AzureTranslator $translator): void
    {
        if (
            ! config('services.azure_translator.enabled')
            || ! setting('job_translation_enabled', true)
        ) {
            return;
        }

        $job = Job::query()->find($this->jobId);
        if (! $job || $job->status !== 'published') {
            return;
        }

        $content = $job->translationSourceContent();
        if ($content === []) {
            return;
        }

        $sourceHash = $job->translationSourceHash();
        $translation = JobTranslation::query()->firstOrNew([
            'job_id' => $job->id,
            'locale' => $this->targetLocale,
        ]);

        if (
            $translation->exists
            && $translation->status === 'completed'
            && hash_equals((string) $translation->source_hash, $sourceHash)
        ) {
            return;
        }

        $translation->fill([
            'source_locale' => 'hr',
            'provider' => 'azure',
            'status' => 'processing',
            'source_hash' => $sourceHash,
            'last_error' => null,
        ])->save();

        try {
            $translatedContent = $translator->translate($content, 'hr', $this->targetLocale);

            // Do not save a stale response if the Croatian source changed while
            // the API request was running.
            $job->refresh();
            if (! hash_equals($sourceHash, $job->translationSourceHash())) {
                self::dispatch($job->id, $this->targetLocale)
                    ->onQueue($job->translationQueueName());
                return;
            }

            $translation->fill([
                'status' => 'completed',
                'content' => $translatedContent,
                'last_error' => null,
                'translated_at' => now(),
            ])->save();
        } catch (Throwable $exception) {
            $translation->fill([
                'status' => 'failed',
                'last_error' => mb_substr($exception->getMessage(), 0, 4000),
            ])->save();

            throw $exception;
        }
    }
}
