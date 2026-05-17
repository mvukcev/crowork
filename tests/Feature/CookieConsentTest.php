<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\CookieConsentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CookieConsentTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_save_cookie_preferences_and_receive_cookies(): void
    {
        $response = $this->postJson(route('consent.preferences.update'), [
            'analytics' => true,
            'marketing' => false,
            'choice' => 'custom',
            'source' => 'cookie_banner',
        ]);

        $response->assertOk();
        $response->assertJsonPath('saved', true);
        $response->assertJsonPath('consent.analytics', true);
        $response->assertJsonPath('consent.marketing', false);
        $response->assertCookie('consent_analytics', '1');
        $response->assertCookie('consent_marketing', '0');

        $this->assertDatabaseCount('consent_histories', 0);
    }

    public function test_authenticated_user_consent_is_persisted_in_history(): void
    {
        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);

        $response = $this
            ->actingAs($worker)
            ->postJson(route('consent.preferences.update'), [
                'analytics' => true,
                'marketing' => true,
                'choice' => 'all',
                'source' => 'cookie_banner',
            ]);

        $response->assertOk();

        $this->assertDatabaseHas('consent_histories', [
            'user_id' => $worker->id,
            'consent_type' => CookieConsentService::CONSENT_TYPE_ANALYTICS,
            'source' => 'cookie_banner',
            'given' => 1,
        ]);

        $this->assertDatabaseHas('consent_histories', [
            'user_id' => $worker->id,
            'consent_type' => CookieConsentService::CONSENT_TYPE_MARKETING,
            'source' => 'cookie_banner:all',
            'given' => 1,
        ]);
    }
}
