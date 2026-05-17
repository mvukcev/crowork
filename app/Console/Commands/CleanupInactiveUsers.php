<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\PrivacyRetentionService;
use Illuminate\Console\Command;

class CleanupInactiveUsers extends Command
{
    protected $signature = 'cleanup:inactive-users {--force : Run in active mode if retention automation is enabled}';

    protected $description = 'Legacy alias for GDPR retention inactive-worker processing (safe, no hard delete).';

    public function __construct(
        private readonly PrivacyRetentionService $retentionService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $enabled = Setting::getBool('enable_retention_automation', false);
        $force = (bool) $this->option('force');
        $dryRun = ! $force || ! $enabled;

        $this->warn('`cleanup:inactive-users` is deprecated. Using privacy retention worker section instead.');
        if (! $enabled) {
            $this->warn('Retention automation is disabled; running in dry-run mode.');
        }

        $summary = $this->retentionService->run([
            PrivacyRetentionService::SECTION_INACTIVE_WORKERS,
        ], $dryRun);

        $section = $summary['sections'][PrivacyRetentionService::SECTION_INACTIVE_WORKERS] ?? [];
        $this->table(
            ['Section', 'Scanned', 'Eligible', 'Processed', 'Skipped', 'Errors', 'Dry-Run'],
            [[
                PrivacyRetentionService::SECTION_INACTIVE_WORKERS,
                (string) ($section['scanned'] ?? 0),
                (string) ($section['eligible'] ?? 0),
                (string) ($section['processed'] ?? 0),
                (string) ($section['skipped'] ?? 0),
                (string) ($section['errors'] ?? 0),
                $dryRun ? 'yes' : 'no',
            ]]
        );

        return ((int) ($section['errors'] ?? 0)) > 0 ? self::FAILURE : self::SUCCESS;
    }
}