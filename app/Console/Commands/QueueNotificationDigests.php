<?php

namespace App\Console\Commands;

use App\Models\NotificationDigest;
use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Console\Command;

class QueueNotificationDigests extends Command
{
    protected $signature = 'crowork:queue-notification-digests {period : daily or weekly}';

    protected $description = 'Create pending notification digest records for users with digest preferences.';

    public function handle(): int
    {
        $period = strtolower((string) $this->argument('period'));

        if (! in_array($period, [NotificationPreference::DIGEST_DAILY, NotificationPreference::DIGEST_WEEKLY], true)) {
            $this->error('Invalid period. Use daily or weekly.');

            return self::FAILURE;
        }

        $scheduledFor = now()->toDateString();

        $userIds = NotificationPreference::query()
            ->where('digest_frequency', $period)
            ->distinct()
            ->pluck('user_id')
            ->all();

        $created = 0;

        foreach (User::query()->whereIn('id', $userIds)->cursor() as $user) {
            $digest = NotificationDigest::query()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'period' => $period,
                    'scheduled_for' => $scheduledFor,
                ],
                [
                    'status' => 'pending',
                    'meta' => [
                        'foundation' => true,
                        'queued_at' => now()->toIso8601String(),
                    ],
                ]
            );

            if ($digest->wasRecentlyCreated) {
                $created++;
            }
        }

        $this->info("Queued {$created} {$period} digest record(s).");

        return self::SUCCESS;
    }
}
