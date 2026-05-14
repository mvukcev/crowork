<?php

namespace App\Console\Commands;

use App\Models\AccountDeletionRequest;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CleanupDeletionRequests extends Command
{
    protected $signature = 'cleanup:deletion-requests';

    protected $description = 'Delete completed deletion requests based on retention settings';

    public function handle()
    {
        $retentionDays = config('retention.user_data.deletion_request_retention_days');
        $thresholdDate = Carbon::now()->subDays($retentionDays);

        AccountDeletionRequest::where('status', 'completed')
            ->where('updated_at', '<', $thresholdDate)
            ->delete();

        $this->info('Completed deletion requests cleaned up successfully.');
    }
}