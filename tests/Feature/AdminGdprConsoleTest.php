<?php

namespace Tests\Feature;

use App\Jobs\AnonymizeUserDataJob;
use App\Models\AccountDeletionRequest;
use App\Models\ConsentHistory;
use App\Models\Employer;
use App\Models\GdprBreachIncident;
use App\Models\GdprDataRequest;
use App\Models\GdprExportLog;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\LegalHold;
use App\Models\Setting;
use App\Models\User;
use App\Services\ConsentVersionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminGdprConsoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_is_blocked_from_gdpr_console(): void
    {
        $worker = $this->createUserWithConsent(User::ROLE_WORKER);

        $this->actingAs($worker)
            ->get(route('admin.gdpr.index'))
            ->assertForbidden();
    }

    public function test_admin_can_view_gdpr_dashboard(): void
    {
        $admin = $this->createUserWithConsent(User::ROLE_ADMIN);

        $this->actingAs($admin)
            ->get(route('admin.gdpr.index'))
            ->assertOk()
            ->assertSee('Admin GDPR Console');
    }

    public function test_admin_can_create_and_update_dsar_request(): void
    {
        $admin = $this->createUserWithConsent(User::ROLE_ADMIN);
        $requester = User::factory()->create(['role' => User::ROLE_WORKER]);

        $this->actingAs($admin)
            ->post(route('admin.gdpr.dsar.store'), [
                'requester_user_id' => $requester->id,
                'requester_email' => 'requester@gmail.com',
                'request_type' => 'access_export',
                'status' => 'open',
                'priority' => 'normal',
                'due_at' => now()->addDays(5)->toDateTimeString(),
                'assigned_admin_id' => $admin->id,
                'internal_notes' => 'Initial DSAR entry',
            ])
            ->assertRedirect(route('admin.gdpr.dsar.index'));

        $request = GdprDataRequest::query()->firstOrFail();

        $this->actingAs($admin)
            ->patch(route('admin.gdpr.dsar.update', $request), [
                'status' => 'fulfilled',
                'priority' => 'high',
                'due_at' => now()->addDays(2)->toDateTimeString(),
                'assigned_admin_id' => $admin->id,
                'resolution_summary' => 'Export was delivered to requester.',
                'internal_note_append' => 'Completed and awaiting close.',
            ])
            ->assertRedirect(route('admin.gdpr.dsar.show', $request));

        $request->refresh();
        $this->assertSame('fulfilled', $request->status);
        $this->assertNotNull($request->fulfilled_at);
        $this->assertStringContainsString('Completed and awaiting close.', (string) $request->internal_notes);
    }

    public function test_export_action_creates_gdpr_export_log(): void
    {
        $worker = $this->createUserWithConsent(User::ROLE_WORKER);

        $this->actingAs($worker)
            ->get(route('user.export'))
            ->assertOk();

        $this->assertDatabaseHas('gdpr_export_logs', [
            'user_id' => $worker->id,
            'requested_by_user_id' => $worker->id,
            'export_type' => 'user_full_export',
            'status' => GdprExportLog::STATUS_COMPLETED,
        ]);
    }

    public function test_anonymization_job_creates_anonymization_log(): void
    {
        $worker = User::factory()->create([
            'role' => User::ROLE_WORKER,
            'pending_deletion' => true,
            'deletion_requested_at' => now()->subDays(16),
            'anonymization_scheduled_at' => now()->subDay(),
        ]);

        $request = AccountDeletionRequest::query()->create([
            'user_id' => $worker->id,
            'status' => AccountDeletionRequest::STATUS_PENDING,
            'reason' => 'privacy_request',
            'requested_at' => now()->subDays(16),
            'anonymization_scheduled_at' => now()->subDay(),
        ]);

        AnonymizeUserDataJob::dispatchSync($worker->id, $request->id);

        $this->assertDatabaseHas('gdpr_anonymization_logs', [
            'user_id' => $worker->id,
            'action' => 'user_account_anonymization',
            'status' => 'completed',
        ]);
    }

    public function test_legal_hold_blocks_retention_application_anonymization(): void
    {
        Setting::setValue('enable_retention_automation', true);
        Setting::setValue('dry_run_mode', false);
        Setting::setValue('rejected_applications_retention_months', 6);

        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);
        $employerUser = User::factory()->create(['role' => User::ROLE_EMPLOYER]);
        $employer = Employer::query()->create([
            'user_id' => $employerUser->id,
            'company_name' => 'Hold Employer',
            'approved_at' => now(),
        ]);

        $job = Job::query()->create([
            'employer_id' => $employer->id,
            'created_by_user_id' => $employerUser->id,
            'title' => 'Hold Job',
            'slug' => 'hold-job-' . uniqid(),
            'description' => 'Role',
            'location_city' => 'Split',
            'category' => 'Hospitality',
            'status' => 'published',
            'published_at' => now()->subDay(),
        ]);

        $application = JobApplication::query()->create([
            'job_id' => $job->id,
            'worker_id' => $worker->id,
            'status' => JobApplication::STATUS_REJECTED,
            'status_updated_at' => now()->subMonths(9),
            'profile_snapshot' => ['first_name' => 'Hold', 'last_name' => 'User'],
            'job_snapshot' => ['title' => $job->title],
            'message' => 'Sensitive',
        ]);

        $admin = $this->createUserWithConsent(User::ROLE_ADMIN);

        LegalHold::query()->create([
            'target_type' => JobApplication::class,
            'target_id' => (string) $application->id,
            'reason' => 'litigation_hold',
            'status' => LegalHold::STATUS_ACTIVE,
            'placed_by_admin_id' => $admin->id,
            'placed_at' => now(),
        ]);

        $this->artisan('privacy:retention-run', [
            '--force' => true,
            '--only' => 'rejected-applications',
        ])->assertExitCode(0);

        $application->refresh();
        $this->assertNull($application->anonymized_at);

        $this->assertDatabaseHas('gdpr_anonymization_logs', [
            'target_type' => JobApplication::class,
            'target_id' => (string) $application->id,
            'status' => 'blocked',
        ]);
    }

    public function test_legal_hold_blocks_account_anonymization_job(): void
    {
        $admin = $this->createUserWithConsent(User::ROLE_ADMIN);
        $worker = User::factory()->create([
            'role' => User::ROLE_WORKER,
            'pending_deletion' => true,
            'deletion_requested_at' => now()->subDays(16),
            'anonymization_scheduled_at' => now()->subDay(),
        ]);

        $request = AccountDeletionRequest::query()->create([
            'user_id' => $worker->id,
            'status' => AccountDeletionRequest::STATUS_PENDING,
            'reason' => 'privacy_request',
            'requested_at' => now()->subDays(16),
            'anonymization_scheduled_at' => now()->subDay(),
        ]);

        LegalHold::query()->create([
            'user_id' => $worker->id,
            'reason' => 'legal_dispute',
            'status' => LegalHold::STATUS_ACTIVE,
            'placed_by_admin_id' => $admin->id,
            'placed_at' => now(),
        ]);

        AnonymizeUserDataJob::dispatchSync($worker->id, $request->id);

        $request->refresh();
        $this->assertSame(AccountDeletionRequest::STATUS_PENDING, $request->status);

        $this->assertDatabaseHas('gdpr_anonymization_logs', [
            'user_id' => $worker->id,
            'action' => 'user_account_anonymization',
            'status' => 'blocked',
        ]);
    }

    public function test_admin_can_create_and_update_breach_incident(): void
    {
        $admin = $this->createUserWithConsent(User::ROLE_ADMIN);

        $this->actingAs($admin)
            ->post(route('admin.gdpr.breaches.store'), [
                'title' => 'PII exposure in logs',
                'severity' => 'high',
                'status' => 'open',
                'detected_at' => now()->subHour()->toDateTimeString(),
                'summary' => 'Temporary log leakage under investigation.',
                'affected_data_categories' => 'email,ip_address',
                'affected_user_count' => 11,
                'authority_notification_required' => 1,
                'users_notification_required' => 0,
                'owner_admin_id' => $admin->id,
                'internal_notes' => 'Containment in progress.',
            ])
            ->assertRedirect(route('admin.gdpr.breaches.index'));

        $incident = GdprBreachIncident::query()->firstOrFail();

        $this->actingAs($admin)
            ->patch(route('admin.gdpr.breaches.update', $incident), [
                'severity' => 'critical',
                'status' => 'resolved',
                'reported_at' => now()->toDateTimeString(),
                'summary' => 'Issue mitigated and validated.',
                'affected_data_categories' => 'email,ip_address',
                'affected_user_count' => 11,
                'authority_notification_required' => 1,
                'users_notification_required' => 1,
                'owner_admin_id' => $admin->id,
                'internal_notes' => 'Resolved and documented.',
            ])
            ->assertRedirect(route('admin.gdpr.breaches.show', $incident));

        $incident->refresh();
        $this->assertSame('critical', $incident->severity);
        $this->assertSame('resolved', $incident->status);
        $this->assertNotNull($incident->resolved_at);
    }

    public function test_gdpr_console_requires_auth_and_state_change_routes_reject_get(): void
    {
        $this->get(route('admin.gdpr.index'))->assertRedirect();

        $admin = $this->createUserWithConsent(User::ROLE_ADMIN);
        $hold = LegalHold::query()->create([
            'user_id' => $admin->id,
            'reason' => 'method_test_hold',
            'status' => LegalHold::STATUS_ACTIVE,
            'placed_by_admin_id' => $admin->id,
            'placed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->get(route('admin.gdpr.legal-holds.release', $hold))
            ->assertMethodNotAllowed();
    }

    private function createUserWithConsent(string $role): User
    {
        $user = User::factory()->create([
            'role' => $role,
            'email_verified_at' => now(),
        ]);

        $consentVersionService = app(ConsentVersionService::class);

        ConsentHistory::query()->create([
            'user_id' => $user->id,
            'consent_type' => ConsentVersionService::TYPE_TERMS,
            'consent_version' => $consentVersionService->currentTermsVersion(),
            'consent_version_hash' => $consentVersionService->currentTermsHash(),
            'source' => 'test_setup',
            'given' => true,
            'accepted_at' => now(),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'phpunit',
        ]);

        ConsentHistory::query()->create([
            'user_id' => $user->id,
            'consent_type' => ConsentVersionService::TYPE_PRIVACY,
            'consent_version' => $consentVersionService->currentPrivacyVersion(),
            'consent_version_hash' => $consentVersionService->currentPrivacyHash(),
            'source' => 'test_setup',
            'given' => true,
            'accepted_at' => now(),
            'ip_address' => '127.0.0.1',
            'user_agent' => 'phpunit',
        ]);

        return $user;
    }
}
