<?php

namespace Tests\Feature;

use App\Models\ConsentHistory;
use App\Models\Setting;
use App\Models\User;
use App\Services\ConsentVersionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegalConsentReacceptanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_with_latest_terms_and_privacy_can_access_worker_dashboard(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKER]);
        $service = app(ConsentVersionService::class);

        $this->seedLatestLegalConsents($user, $service, 'registration');

        $this->actingAs($user)
            ->get(route('worker.dashboard'))
            ->assertOk();
    }

    public function test_user_missing_latest_terms_is_redirected_to_reaccept_page(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKER]);
        $service = app(ConsentVersionService::class);

        ConsentHistory::query()->create([
            'user_id' => $user->id,
            'consent_type' => ConsentVersionService::TYPE_PRIVACY,
            'consent_version' => $service->currentPrivacyVersion(),
            'consent_version_hash' => $service->currentPrivacyHash(),
            'source' => 'registration',
            'given' => true,
            'accepted_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('worker.dashboard'))
            ->assertRedirect(route('legal.reaccept.show'));
    }

    public function test_user_missing_latest_privacy_policy_is_redirected_to_reaccept_page(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKER]);
        $service = app(ConsentVersionService::class);

        ConsentHistory::query()->create([
            'user_id' => $user->id,
            'consent_type' => ConsentVersionService::TYPE_TERMS,
            'consent_version' => $service->currentTermsVersion(),
            'consent_version_hash' => $service->currentTermsHash(),
            'source' => 'registration',
            'given' => true,
            'accepted_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('worker.dashboard'))
            ->assertRedirect(route('legal.reaccept.show'));
    }

    public function test_reaccept_submit_logs_both_required_consent_records(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKER]);
        $service = app(ConsentVersionService::class);

        $this->actingAs($user)
            ->post(route('legal.reaccept.store'), [
                'accept_terms' => '1',
                'accept_privacy' => '1',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('consent_histories', [
            'user_id' => $user->id,
            'consent_type' => ConsentVersionService::TYPE_TERMS,
            'consent_version' => $service->currentTermsVersion(),
            'consent_version_hash' => $service->currentTermsHash(),
            'source' => 'reacceptance',
            'given' => 1,
        ]);

        $this->assertDatabaseHas('consent_histories', [
            'user_id' => $user->id,
            'consent_type' => ConsentVersionService::TYPE_PRIVACY,
            'consent_version' => $service->currentPrivacyVersion(),
            'consent_version_hash' => $service->currentPrivacyHash(),
            'source' => 'reacceptance',
            'given' => 1,
        ]);
    }

    public function test_reaccept_submit_redirects_to_intended_url(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKER]);

        $this->actingAs($user)
            ->get(route('worker.dashboard'))
            ->assertRedirect(route('legal.reaccept.show'));

        $this->actingAs($user)
            ->post(route('legal.reaccept.store'), [
                'accept_terms' => '1',
                'accept_privacy' => '1',
            ])
            ->assertRedirect(route('worker.dashboard'));
    }

    public function test_exempt_routes_do_not_redirect_loop(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKER]);

        $this->actingAs($user)
            ->get(route('worker.privacy.show'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('terms'))
            ->assertOk();

        $this->actingAs($user)
            ->get(route('privacy'))
            ->assertOk();
    }

    public function test_admin_is_exempt_from_legal_reaccept_redirect(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get(route('filament.admin.pages.dashboard'))
            ->assertOk();
    }

    public function test_old_consent_records_remain_in_history_after_reacceptance(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKER]);

        ConsentHistory::query()->create([
            'user_id' => $user->id,
            'consent_type' => ConsentVersionService::TYPE_TERMS_LEGACY,
            'consent_version' => '2025-01-terms-v1',
            'consent_version_hash' => hash('sha256', '2025-01-terms-v1|/terms'),
            'source' => 'registration',
            'given' => true,
            'accepted_at' => now()->subYear(),
        ]);

        $this->actingAs($user)
            ->post(route('legal.reaccept.store'), [
                'accept_terms' => '1',
                'accept_privacy' => '1',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('consent_histories', [
            'user_id' => $user->id,
            'consent_type' => ConsentVersionService::TYPE_TERMS_LEGACY,
            'consent_version' => '2025-01-terms-v1',
        ]);

        $this->assertGreaterThanOrEqual(3, ConsentHistory::query()->where('user_id', $user->id)->count());
    }

    public function test_changing_policy_version_forces_reacceptance_again(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKER]);
        $service = app(ConsentVersionService::class);

        $this->seedLatestLegalConsents($user, $service, 'registration');

        Setting::setValue('terms_version', '2026-06-terms-v2');
        Setting::setValue('terms_hash', hash('sha256', '2026-06-terms-v2|https://example.test/terms'));        

        $this->actingAs($user)
            ->get(route('worker.dashboard'))
            ->assertRedirect(route('legal.reaccept.show'));
    }

    private function seedLatestLegalConsents(User $user, ConsentVersionService $service, string $source): void
    {
        ConsentHistory::query()->create([
            'user_id' => $user->id,
            'consent_type' => ConsentVersionService::TYPE_TERMS,
            'consent_version' => $service->currentTermsVersion(),
            'consent_version_hash' => $service->currentTermsHash(),
            'source' => $source,
            'given' => true,
            'accepted_at' => now(),
        ]);

        ConsentHistory::query()->create([
            'user_id' => $user->id,
            'consent_type' => ConsentVersionService::TYPE_PRIVACY,
            'consent_version' => $service->currentPrivacyVersion(),
            'consent_version_hash' => $service->currentPrivacyHash(),
            'source' => $source,
            'given' => true,
            'accepted_at' => now(),
        ]);
    }
}
