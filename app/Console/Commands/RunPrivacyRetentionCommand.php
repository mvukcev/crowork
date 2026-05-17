<?php

namespace App\Console\Commands;

use App\Models\Setting;
use App\Services\PrivacyRetentionService;
use Illuminate\Console\Command;
use InvalidArgumentException;

class RunPrivacyRetentionCommand extends Command
{
    protected $signature = 'privacy:retention-run
        {--dry-run : Force dry-run mode regardless of settings}
        {--force : Allow active mode when automation is enabled}
        {--only= : Comma-separated sections: rejected-applications,inactive-workers,inactive-employers,notifications}';

    protected $description = 'Run GDPR retention automation and report section-level summaries.';

    public function __construct(
        private readonly PrivacyRetentionService $retentionService,
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $enabled = Setting::getBool('enable_retention_automation', false);
        $settingsDryRun = Setting::getBool('dry_run_mode', true);

        $dryRun = $this->resolveDryRun($enabled, $settingsDryRun);
        $onlySections = $this->parseOnlySections();

        $this->info('CroWork Privacy Retention');
        $this->line('Automation enabled: ' . ($enabled ? 'yes' : 'no'));
        $this->line('Effective mode: ' . ($dryRun ? 'dry-run' : 'active'));

        try {
            $summary = $this->retentionService->run($onlySections, $dryRun);
        } catch (InvalidArgumentException $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }

        $rows = [];
        foreach (($summary['sections'] ?? []) as $section => $sectionSummary) {
            $rows[] = [
                'Section' => $section,
                'Scanned' => (string) ($sectionSummary['scanned'] ?? 0),
                'Eligible' => (string) ($sectionSummary['eligible'] ?? 0),
                'Processed' => (string) ($sectionSummary['processed'] ?? 0),
                'Skipped' => (string) ($sectionSummary['skipped'] ?? 0),
                'Errors' => (string) ($sectionSummary['errors'] ?? 0),
            ];
        }

        $this->newLine();
        $this->table(['Section', 'Scanned', 'Eligible', 'Processed', 'Skipped', 'Errors'], $rows);

        foreach (($summary['sections'] ?? []) as $section => $sectionSummary) {
            $notes = $sectionSummary['notes'] ?? null;
            if (! is_array($notes) || $notes === []) {
                continue;
            }

            $this->line($section . ' notes: ' . json_encode($notes, JSON_UNESCAPED_UNICODE));
        }

        $errorCount = collect($summary['sections'] ?? [])->sum(fn ($entry) => (int) ($entry['errors'] ?? 0));

        if ($errorCount > 0) {
            $this->warn('Retention run completed with section errors: ' . $errorCount);
            return self::FAILURE;
        }

        $this->info('Retention run completed successfully.');

        return self::SUCCESS;
    }

    private function resolveDryRun(bool $enabled, bool $settingsDryRun): bool
    {
        $cliDryRun = (bool) $this->option('dry-run');
        $force = (bool) $this->option('force');

        if ($cliDryRun) {
            return true;
        }

        if (! $enabled) {
            $this->warn('Retention automation is disabled by setting. Falling back to dry-run mode.');
            return true;
        }

        if ($force) {
            return false;
        }

        return $settingsDryRun;
    }

    /**
     * @return array<int, string>|null
     */
    private function parseOnlySections(): ?array
    {
        $only = $this->option('only');

        if (! is_string($only) || trim($only) === '') {
            return null;
        }

        return array_values(array_filter(array_map('trim', explode(',', strtolower($only)))));
    }
}
