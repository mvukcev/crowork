<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\Hzz\HzzJobImportService;
use App\Support\HzzUrlGuard;
use Illuminate\Console\Command;

class ImportHzzJobsCommand extends Command
{
    protected $signature = 'crowork:hzz-import
        {--url= : HZZ feed URL in JSON format}
        {--write : Persist new records (default behavior is dry-run)}
        {--allow-updates : Allow updating existing HZZ records matched by source_reference}
        {--deactivate-missing : Backward-compatible no-op option for legacy scheduler usage}';

    protected $aliases = [
        'crowork:import-hzz-jobs',
    ];

    protected $description = 'Import or refresh HZZ listings and parse application contact details.';

    public function handle(HzzJobImportService $service): int
    {
        $url = trim((string) $this->option('url'));
        if ($url === '') {
            $url = trim((string) (Setting::getString('hzz_feed_url', config('services.hzz.feed_url', HzzUrlGuard::defaultFeedUrl())) ?? HzzUrlGuard::defaultFeedUrl()));
        }

        if ($url === '') {
            $this->error('Missing HZZ feed URL configuration.');
            return self::FAILURE;
        }

        if (! HzzUrlGuard::isAllowedFeedUrl($url)) {
            $this->error('Invalid HZZ feed URL. Only official HZZ domains are allowed.');
            return self::FAILURE;
        }

        $write = (bool) $this->option('write');
        $allowUpdates = (bool) $this->option('allow-updates');
        $dryRun = ! $write;

        Setting::setValue('hzz_feed_url', $url);

        if (! $write && $allowUpdates) {
            $this->warn('Ignoring --allow-updates because --write was not provided (dry-run mode).');
            $allowUpdates = false;
        }

        try {
            $summary = $service->importFromUrl($url, $dryRun, $allowUpdates);
        } catch (\Throwable $exception) {
            $this->error('HZZ import failed: ' . $exception->getMessage());
            return self::FAILURE;
        }

        $this->info('HZZ import completed.');
        $this->line('Total feed items: ' . $summary['total_items']);
        $this->line('Created: ' . $summary['created']);
        $this->line('Updated: ' . $summary['updated']);
        $this->line('Skipped existing: ' . $summary['skipped_existing']);
        $this->line('Dry run: ' . ($summary['dry_run'] ? 'yes' : 'no'));
        $this->line('Existing records updated: ' . ($summary['allow_updates'] ? 'yes' : 'no'));

        return self::SUCCESS;
    }
}
