<?php

namespace Tests\Feature;

use App\Services\ConsentVersionService;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccessConsentLoggingTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_logs_terms_and_privacy_consents(): void
    {
        $email = 'worker@example.test';

        $response = $this
            ->withSession([
                'cw_verified_email' => $email,
                'access_stage' => 'register',
                'access_email' => $email,
            ])
            ->post(route('access.register'), [
                'email' => $email,
                'name' => 'Worker Test',
                'account_type' => User::ROLE_WORKER,
                'password' => 'password',
                'password_confirmation' => 'password',
                'accept_terms' => '1',
                'accept_privacy' => '1',
            ]);

        $response->assertRedirect(route('worker.profile.edit'));

        $user = User::query()->where('email', $email)->firstOrFail();
        $consentVersionService = app(ConsentVersionService::class);

        $this->assertDatabaseCount('consent_histories', 2);
        $this->assertDatabaseHas('consent_histories', [
            'user_id' => $user->id,
            'consent_type' => 'terms',
            'consent_version' => $consentVersionService->currentTermsVersion(),
            'consent_version_hash' => $consentVersionService->currentTermsHash(),
            'source' => 'registration',
            'given' => 1,
        ]);
        $this->assertDatabaseHas('consent_histories', [
            'user_id' => $user->id,
            'consent_type' => 'privacy_policy',
            'consent_version' => $consentVersionService->currentPrivacyVersion(),
            'consent_version_hash' => $consentVersionService->currentPrivacyHash(),
            'source' => 'registration',
            'given' => 1,
        ]);
    }

    public function test_registration_requires_consent_checkboxes(): void
    {
        $email = 'noconsent@example.test';

        $response = $this
            ->from(route('access.show'))
            ->withSession([
                'cw_verified_email' => $email,
                'access_stage' => 'register',
                'access_email' => $email,
            ])
            ->post(route('access.register'), [
                'email' => $email,
                'name' => 'No Consent',
                'account_type' => User::ROLE_WORKER,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

        $response->assertSessionHasErrors(['accept_terms', 'accept_privacy']);
    }

    public function test_pending_deletion_account_cannot_log_in(): void
    {
        $user = User::factory()->create([
            'email' => 'pending@example.test',
            'password' => Hash::make('password123'),
            'pending_deletion' => true,
        ]);

        $response = $this
            ->from(route('access.show'))
            ->post(route('access.login'), [
                'email' => $user->email,
                'password' => 'password123',
            ]);

        $response->assertRedirect(route('access.show'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }
}
