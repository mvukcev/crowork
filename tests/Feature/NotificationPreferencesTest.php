<?php

namespace Tests\Feature;

use App\Services\NotificationPreferenceService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationPreferencesTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_update_notification_preferences(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch(route('notifications.preferences.update'), [
                'preferences' => [
                    NotificationPreferenceService::CATEGORY_WORKER_APPLICATION_STATUS => [
                        'email_enabled' => '1',
                        'database_enabled' => '1',
                        'digest_frequency' => 'daily',
                    ],
                ],
            ]);

        $response->assertRedirect(route('notifications.preferences'));

        $this->assertDatabaseHas('notification_preferences', [
            'user_id' => $user->id,
            'category' => NotificationPreferenceService::CATEGORY_WORKER_APPLICATION_STATUS,
            'email_enabled' => true,
            'database_enabled' => true,
            'digest_frequency' => 'daily',
        ]);
    }

    public function test_guest_cannot_access_notification_preferences(): void
    {
        $this->get(route('notifications.preferences'))->assertRedirect(route('login'));
    }
}
