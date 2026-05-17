<?php

namespace Tests\Feature;

use App\Jobs\AnonymizeUserDataJob;
use App\Models\User;
use App\Models\WorkerProfile;
use App\Services\CookieConsentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class WorkerPrivacyTest extends TestCase
{
    use RefreshDatabase;

    public function test_worker_can_open_privacy_page_and_update_visibility(): void
    {
        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);

        WorkerProfile::query()->create([
            'user_id' => $worker->id,
            'first_name' => 'Marko',
            'last_name' => 'Test',
            'nationality_country_code' => 'HR',
            'birth_year' => 1992,
            'skills' => [],
            'profile_visibility' => WorkerProfile::VISIBILITY_EMPLOYERS,
        ]);

        $this->actingAs($worker)
            ->get(route('worker.privacy.show'))
            ->assertOk();

        $this->actingAs($worker)
            ->patch(route('worker.privacy.visibility'), [
                'profile_visibility' => WorkerProfile::VISIBILITY_ANONYMOUS,
            ])
            ->assertRedirect(route('worker.privacy.show'));

        $this->assertDatabaseHas('worker_profiles', [
            'user_id' => $worker->id,
            'profile_visibility' => WorkerProfile::VISIBILITY_ANONYMOUS,
        ]);
    }

    public function test_non_worker_cannot_access_worker_privacy_routes(): void
    {
        $employer = User::factory()->create(['role' => User::ROLE_EMPLOYER]);

        $this->actingAs($employer)
            ->get(route('worker.privacy.show'))
            ->assertForbidden();
    }

    public function test_worker_can_request_deletion_with_grace_period(): void
    {
        Queue::fake();

        $worker = User::factory()->create([
            'role' => User::ROLE_WORKER,
            'password' => Hash::make('password123'),
        ]);

        $response = $this
            ->actingAs($worker)
            ->post(route('worker.privacy.request-deletion'), [
                'password' => 'password123',
                'reason' => 'No longer needed',
            ]);

        $response->assertRedirect(route('access.show'));
        $this->assertGuest();

        $this->assertDatabaseHas('users', [
            'id' => $worker->id,
            'pending_deletion' => true,
        ]);

        $this->assertDatabaseHas('account_deletion_requests', [
            'user_id' => $worker->id,
            'status' => 'pending',
            'reason' => 'No longer needed',
        ]);

        Queue::assertPushed(AnonymizeUserDataJob::class);
    }

    public function test_worker_deletion_request_route_is_throttled(): void
    {
        Queue::fake();

        $worker = User::factory()->create([
            'role' => User::ROLE_WORKER,
            'password' => Hash::make('password123'),
        ]);

        for ($attempt = 1; $attempt <= 3; $attempt++) {
            $response = $this
                ->actingAs($worker)
                ->post(route('worker.privacy.request-deletion'), [
                    'password' => 'password123',
                    'reason' => 'Throttle test ' . $attempt,
                ]);

            $response->assertRedirect(route('access.show'));

            $this->assertDatabaseHas('account_deletion_requests', [
                'user_id' => $worker->id,
            ]);

            auth()->login($worker);
        }

        $this->actingAs($worker)
            ->post(route('worker.privacy.request-deletion'), [
                'password' => 'password123',
                'reason' => 'Throttle overflow',
            ])
            ->assertStatus(429);
    }

    public function test_worker_can_update_tracking_preferences_and_persist_history(): void
    {
        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);

        $response = $this
            ->actingAs($worker)
            ->patch(route('worker.privacy.consent'), [
                'consent_analytics' => '1',
                // marketing intentionally omitted to verify withdrawal path.
            ]);

        $response->assertRedirect(route('worker.privacy.show'));
        $response->assertCookie('consent_analytics', '1');
        $response->assertCookie('consent_marketing', '0');

        $this->assertDatabaseHas('consent_histories', [
            'user_id' => $worker->id,
            'consent_type' => CookieConsentService::CONSENT_TYPE_ANALYTICS,
            'source' => CookieConsentService::SOURCE_WORKER_PRIVACY,
            'given' => 1,
        ]);

        $this->assertDatabaseHas('consent_histories', [
            'user_id' => $worker->id,
            'consent_type' => CookieConsentService::CONSENT_TYPE_MARKETING,
            'source' => CookieConsentService::SOURCE_WORKER_PRIVACY . ':custom',
            'given' => 0,
        ]);
    }

    public function test_non_worker_cannot_update_tracking_preferences(): void
    {
        $employer = User::factory()->create(['role' => User::ROLE_EMPLOYER]);

        $this->actingAs($employer)
            ->patch(route('worker.privacy.consent'), [
                'consent_analytics' => '1',
                'consent_marketing' => '1',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('consent_histories', [
            'user_id' => $employer->id,
            'consent_type' => CookieConsentService::CONSENT_TYPE_ANALYTICS,
        ]);
    }
}
