<?php

namespace App\Console\Commands;

use App\Jobs\TranslateJobPosting;
use App\Models\Job;
use Illuminate\Console\Command;

class TranslateJobsCommand extends Command
{
    protected $signature = 'crowork:translate-jobs
        {--locale=en : Target locale}
        {--native-only : Queue only native CroWork listings}
        {--hzz-only : Queue only HZZ listings}';

    protected $description = 'Queue missing or outdated job listing translations, with native listings first.';

    public function handle(): int
    {
        if ($this->option('native-only') && $this->option('hzz-only')) {
            $this->error('Use either --native-only or --hzz-only, not both.');
            return self::INVALID;
        }

        $locale = strtolower(trim((string) $this->option('locale')));
        if ($locale !== 'en') {
            $this->error('Only the English target locale is currently supported.');
            return self::INVALID;
        }

        $queued = 0;

        if (! $this->option('hzz-only')) {
            $queued += $this->queueJobs(false, $locale);
        }

        if (! $this->option('native-only')) {
            $queued += $this->queueJobs(true, $locale);
        }

        $this->info("Queued {$queued} job translations. Native listings use the higher-priority queue.");

        return self::SUCCESS;
    }

    private function queueJobs(bool $hzz, string $locale): int
    {
        $count = 0;

        Job::query()
            ->active()
            ->when(
                $hzz,
                fn ($query) => $query->where(function ($query): void {
                    $query->where('source_system', 'hzz')->orWhere('hzz_is_official', true);
                }),
                fn ($query) => $query->where(function ($query): void {
                    $query->where(function ($query): void {
                        $query->whereNull('source_system')->orWhere('source_system', '!=', 'hzz');
                    })->where(function ($query): void {
                        $query->whereNull('hzz_is_official')->orWhere('hzz_is_official', false);
                    });
                }),
            )
            ->orderByDesc('published_at')
            ->chunkById(100, function ($jobs) use (&$count, $locale): void {
                foreach ($jobs as $job) {
                    TranslateJobPosting::dispatch($job->id, $locale)
                        ->onQueue($job->translationQueueName());
                    $count++;
                }
            });

        return $count;
    }
}
