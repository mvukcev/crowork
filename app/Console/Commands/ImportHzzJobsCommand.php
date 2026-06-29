<?php

namespace App\Console\Commands;

use App\Services\HzzJobImportService;
use Illuminate\Console\Command;

class ImportHzzJobsCommand extends Command
{
    protected $signature = 'crowork:import-hzz-jobs {--deactivate-missing : Mark imported HZZ jobs that no longer exist in feed as delisted}';

    protected $description = 'Import published jobs from HZZ XML feed into local job postings.';

    public function handle(HzzJobImportService $importService): int
    {
        try {
            $result = $importService->import((bool) $this->option('deactivate-missing'));

            $this->info('HZZ import completed successfully.');
            $this->line('Created: '.$result['created']);
            $this->line('Updated: '.$result['updated']);
            $this->line('Skipped: '.$result['skipped']);
            $this->line('Deactivated: '.$result['deactivated']);

            return self::SUCCESS;
        } catch (\Throwable $e) {
            report($e);
            $this->error('HZZ import failed: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
