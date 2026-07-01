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
        {--deactivate-missing : Mark HZZ records missing from current feed as delisted}
        {--force-overwrite : Overwrite existing HZZ fields even when record appears manually edited}';

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
        $deactivateMissing = (bool) $this->option('deactivate-missing');
        $forceOverwrite = (bool) $this->option('force-overwrite');
        $dryRun = ! $write;

        Setting::setValue('hzz_feed_url', $url);

        if (! $write && $allowUpdates) {
            $this->warn('Ignoring --allow-updates because --write was not provided (dry-run mode).');
            $allowUpdates = false;
        }

        if (! $write && $deactivateMissing) {
            $this->warn('Ignoring --deactivate-missing because --write was not provided (dry-run mode).');
            $deactivateMissing = false;
        }

        try {
            $summary = $service->importFromUrl($url, $dryRun, $allowUpdates, $deactivateMissing, $forceOverwrite);
        } catch (\Throwable $exception) {
            $this->error('HZZ import failed: ' . $exception->getMessage());
            return self::FAILURE;
        }

        $this->info('HZZ import completed.');
        $this->line('Total feed items: ' . $summary['total_items']);
        $this->line('Created: ' . $summary['created']);
        $this->line('Updated: ' . $summary['updated']);
        $this->line('Skipped existing: ' . $summary['skipped_existing']);
        $this->line('Skipped invalid: ' . ($summary['skipped_invalid'] ?? 0));
        $this->line('Delisted missing: ' . ($summary['deactivated'] ?? 0));
        $this->line('Preserved manually edited records: ' . ($summary['preserved_manual_records'] ?? 0));
        $this->line('Dry run: ' . ($summary['dry_run'] ? 'yes' : 'no'));
        $this->line('Existing records updated: ' . ($summary['allow_updates'] ? 'yes' : 'no'));
        $this->line('Force overwrite mode: ' . (($summary['force_overwrite'] ?? false) ? 'yes' : 'no'));

        return self::SUCCESS;
    }
}
