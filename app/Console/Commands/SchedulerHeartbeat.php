<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SchedulerHeartbeat extends Command
{
    protected $signature = 'crowork:scheduler-heartbeat';

    protected $description = 'Write scheduler heartbeat timestamp for System Health checks.';

    public function handle(): int
    {
        $timestamp = now()->toIso8601String();

        Cache::put('scheduler:last_run_at', $timestamp, now()->addHours(24));
        Cache::put('schedule:last_run_at', $timestamp, now()->addHours(24));
        Cache::put('schedule_last_run_at', $timestamp, now()->addHours(24));

        $this->info('Scheduler heartbeat updated at '.$timestamp);

        return self::SUCCESS;
    }
}
