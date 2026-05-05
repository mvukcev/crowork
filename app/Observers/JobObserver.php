<?php

namespace App\Observers;

use App\Models\Job;
use App\Notifications\JobStatusChanged;

class JobObserver
{
    public function updated(Job $job): void
    {
        if (!$job->wasChanged('status') || !in_array($job->status, ['published', 'delisted'], true)) {
            return;
        }

        $job->loadMissing('employer.user');
        $job->employer?->user?->notify(new JobStatusChanged($job, $job->status));
    }
}
