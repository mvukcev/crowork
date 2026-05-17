<?php

namespace Tests\Feature;

use App\Jobs\AnonymizeUserDataJob;
use App\Models\User;
use App\Models\WorkerProfile;
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
}
