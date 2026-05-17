<?php

namespace App\Services;

use App\Models\FailedJob;
use App\Models\GdprAnonymizationLog;
use App\Models\GdprExportLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class GdprSystemHealthService
{
    /**
     * @return array<string, mixed>
     */
    public function summary(): array
    {
        $schedulerLastRun = $this->schedulerLastRun();
        $schedulerStale = $schedulerLastRun === null || $schedulerLastRun->lt(now()->subMinutes(10));

        $gdprFailedJobs = $this->gdprFailedJobsCount();
        $stuckAnonymizations = GdprAnonymizationLog::query()
            ->where('status', GdprAnonymizationLog::STATUS_STARTED)
            ->where('started_at', '<', now()->subMinutes(60))
            ->count();

        $stuckExports = GdprExportLog::query()
            ->where('status', GdprExportLog::STATUS_PENDING)
            ->where('created_at', '<', now()->subMinutes(30))
            ->count();

        $warnings = [];

        if ($schedulerStale) {
            $warnings[] = 'Scheduler heartbeat is stale. Scheduled GDPR maintenance may be inactive.';
        }

        if ($gdprFailedJobs > 0) {
            $warnings[] = 'There are failed GDPR-related queue jobs.';
        }

        if ($stuckAnonymizations > 0) {
            $warnings[] = 'There are stuck anonymization operations.';
        }

        if ($stuckExports > 0) {
            $warnings[] = 'There are stuck export operations.';
        }

        return [
            'scheduler_last_run_at' => $schedulerLastRun,
            'scheduler_stale' => $schedulerStale,
            'gdpr_failed_jobs' => $gdprFailedJobs,
            'stuck_anonymizations' => $stuckAnonymizations,
            'stuck_exports' => $stuckExports,
            'warnings' => $warnings,
            'healthy' => $warnings === [],
        ];
    }

    private function schedulerLastRun(): ?Carbon
    {
        $raw = Cache::get('scheduler:last_run_at')
            ?? Cache::get('schedule:last_run_at')
            ?? Cache::get('schedule_last_run_at');

        if (! is_string($raw) || trim($raw) === '') {
            return null;
        }

        return Carbon::parse($raw);
    }

    private function gdprFailedJobsCount(): int
    {
        if (! Schema::hasTable('failed_jobs')) {
            return 0;
        }

        $classes = [
            'AnonymizeUserDataJob',
            'SendMetaEventJob',
            'SendMetaCapiEvent',
            'RunPrivacyRetentionCommand',
            'CleanupExpiredGdprExportsCommand',
        ];

        $query = FailedJob::query();

        foreach ($classes as $index => $class) {
            if ($index === 0) {
                $query->where('payload', 'like', '%' . $class . '%');
                continue;
            }

            $query->orWhere('payload', 'like', '%' . $class . '%');
        }

        return $query->count();
    }
}
