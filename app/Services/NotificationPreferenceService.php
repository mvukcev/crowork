<?php

namespace App\Services;

use App\Models\NotificationPreference;
use App\Models\User;
use Illuminate\Support\Collection;

class NotificationPreferenceService
{
    public const CATEGORY_EMPLOYER_NEW_APPLICATION = 'employer_new_application';
    public const CATEGORY_WORKER_APPLICATION_STATUS = 'worker_application_status';
    public const CATEGORY_ADMIN_MODERATION = 'admin_moderation';
    public const CATEGORY_SYSTEM_NOTICES = 'system_notices';

    /**
     * @return array<string, string>
     */
    public static function categoryLabels(): array
    {
        return [
            self::CATEGORY_EMPLOYER_NEW_APPLICATION => 'Employer: New application alerts',
            self::CATEGORY_WORKER_APPLICATION_STATUS => 'Worker: Application status updates',
            self::CATEGORY_ADMIN_MODERATION => 'Admin: Moderation and approval events',
            self::CATEGORY_SYSTEM_NOTICES => 'System notices',
        ];
    }

    /**
     * @param array<int, string> $fallbackChannels
     * @return array<int, string>
     */
    public function channelsFor(User $user, string $category, array $fallbackChannels = ['mail', 'database']): array
    {
        $preference = $user->notificationPreferences()
            ->where('category', $category)
            ->first();

        if (! $preference) {
            return $fallbackChannels;
        }

        $channels = [];

        if ($preference->email_enabled && in_array('mail', $fallbackChannels, true)) {
            $channels[] = 'mail';
        }

        if ($preference->database_enabled && in_array('database', $fallbackChannels, true)) {
            $channels[] = 'database';
        }

        return $channels;
    }

    /**
     * @return array<string, array{email_enabled: bool, database_enabled: bool, digest_frequency: string}>
     */
    public function preferencesForUser(User $user): array
    {
        $saved = $user->notificationPreferences()
            ->get()
            ->keyBy('category');

        $preferences = [];

        foreach (array_keys(self::categoryLabels()) as $category) {
            /** @var NotificationPreference|null $row */
            $row = $saved->get($category);

            $preferences[$category] = [
                'email_enabled' => $row?->email_enabled ?? true,
                'database_enabled' => $row?->database_enabled ?? true,
                'digest_frequency' => $row?->digest_frequency ?? NotificationPreference::DIGEST_NONE,
            ];
        }

        return $preferences;
    }

    /**
     * @param array<string, array{email_enabled?: mixed, database_enabled?: mixed, digest_frequency?: mixed}> $input
     */
    public function updateForUser(User $user, array $input): void
    {
        foreach (array_keys(self::categoryLabels()) as $category) {
            $data = $input[$category] ?? [];

            $digest = (string) ($data['digest_frequency'] ?? NotificationPreference::DIGEST_NONE);
            if (! in_array($digest, [NotificationPreference::DIGEST_NONE, NotificationPreference::DIGEST_DAILY, NotificationPreference::DIGEST_WEEKLY], true)) {
                $digest = NotificationPreference::DIGEST_NONE;
            }

            NotificationPreference::query()->updateOrCreate(
                [
                    'user_id' => $user->id,
                    'category' => $category,
                ],
                [
                    'email_enabled' => (bool) ($data['email_enabled'] ?? false),
                    'database_enabled' => (bool) ($data['database_enabled'] ?? false),
                    'digest_frequency' => $digest,
                ]
            );
        }
    }

    /**
     * Ensure every user has rows for every notification category.
     *
     * @return int Number of created rows.
     */
    public function ensureDefaultsForAllUsers(): int
    {
        $created = 0;
        $categories = array_keys(self::categoryLabels());

        User::query()
            ->select(['id'])
            ->chunk(200, function (Collection $users) use (&$created, $categories): void {
                foreach ($users as $user) {
                    foreach ($categories as $category) {
                        $row = NotificationPreference::query()->firstOrCreate(
                            [
                                'user_id' => $user->id,
                                'category' => $category,
                            ],
                            [
                                'email_enabled' => true,
                                'database_enabled' => true,
                                'digest_frequency' => NotificationPreference::DIGEST_NONE,
                            ]
                        );

                        if ($row->wasRecentlyCreated) {
                            $created++;
                        }
                    }
                }
            });

        return $created;
    }
}
