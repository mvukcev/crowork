<?php

namespace App\Console\Commands;

use App\Models\GdprExportLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class CleanupExpiredGdprExportsCommand extends Command
{
    protected $signature = 'gdpr:cleanup-expired-exports';

    protected $description = 'Delete expired GDPR export files and clear file_path references.';

    public function handle(): int
    {
        $logs = GdprExportLog::query()
            ->whereNotNull('file_path')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->orderBy('id')
            ->get(['id', 'file_path']);

        $deleted = 0;

        foreach ($logs as $log) {
            $path = (string) $log->file_path;

            if ($path !== '' && Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
            }

            $log->update([
                'file_path' => null,
                'failure_reason' => trim((string) ($log->failure_reason ?? '') . ' Export file purged after expiry.'),
            ]);

            $deleted++;
        }

        $this->info('Expired GDPR exports cleaned: ' . $deleted);

        return self::SUCCESS;
    }
}
