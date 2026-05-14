<?php

namespace App\Console\Commands;

use App\Models\Education;
use App\Models\Job;
use App\Models\Setting;
use Illuminate\Console\Command;

class ExpireListings extends Command
{
    protected $signature = 'crowork:expire-listings';

    protected $description = 'Mark jobs and educations as expired when their expiry date has passed.';

    public function handle(): int
    {
        if (! Setting::getBool('auto_expire_jobs_enabled', true)) {
            $this->info('Auto-expire listings is disabled in platform settings.');
            return self::SUCCESS;
        }

        $expiredJobs = Job::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->whereNotIn('status', ['expired', 'delisted'])
            ->update(['status' => 'expired']);

        $expiredEducations = Education::query()
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->whereNotIn('status', ['expired', 'delisted'])
            ->update(['status' => 'expired']);

        $this->info("Expired {$expiredJobs} jobs and {$expiredEducations} educations.");

        return self::SUCCESS;
    }
}
