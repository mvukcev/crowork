<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('crowork:scheduler-heartbeat')->everyMinute();
Schedule::command('crowork:expire-listings')->hourly();
Schedule::command('crowork:queue-notification-digests daily')->dailyAt('07:00');
Schedule::command('crowork:queue-notification-digests weekly')->weeklyOn(1, '08:00');
Schedule::command('privacy:retention-run')
    ->monthlyOn(1, '03:30')
    ->when(fn (): bool => (bool) setting('enable_retention_automation', false));
