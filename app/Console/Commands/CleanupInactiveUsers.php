<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CleanupInactiveUsers extends Command
{
    protected $signature = 'cleanup:inactive-users';

    protected $description = 'Delete inactive users based on retention settings';

    public function handle()
    {
        $retentionDays = config('retention.user_data.inactive_user_retention_days');
        $thresholdDate = Carbon::now()->subDays($retentionDays);

        User::where('last_active_at', '<', $thresholdDate)->delete();

        $this->info('Inactive users cleaned up successfully.');
    }
}