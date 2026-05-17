<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class PrivacyRetentionAutomationTest extends TestCase
{
    use RefreshDatabase;

    public function test_disabled_automation_forces_dry_run_even_with_force_flag(): void
    {
        $application = $this->createRejectedApplication(now()->subMonths(8));

        Setting::setValue('enable_retention_automation', false);
        Setting::setValue('dry_run_mode', false);
        Setting::setValue('rejected_applications_retention_months', 6);

        $exitCode = $this->artisan('privacy:retention-run', [
            '--force' => true,
            '--only' => 'rejected-applications',
        ])->run();

        $this->assertSame(0, $exitCode);

        $application->refresh();
        $this->assertNull($application->anonymized_at);
        $this->assertSame('Sensitive message', $application->message);
    }

    public function test_rejected_applications_dry_run_does_not_mutate(): void
    {
        $application = $this->createRejectedApplication(now()->subMonths(8));

        Setting::setValue('enable_retention_automation', true);
        Setting::setValue('dry_run_mode', true);
        Setting::setValue('rejected_applications_retention_months', 6);

        $this->artisan('privacy:retention-run', [
            '--dry-run' => true,
            '--only' => 'rejected-applications',
        ])->assertExitCode(0);

        $application->refresh();
        $this->assertNull($application->anonymized_at);
        $this->assertSame('Sensitive message', $application->message);
    }

    public function test_rejected_applications_active_mode_anonymizes_old_records(): void
    {
        $application = $this->createRejectedApplication(now()->subMonths(9));

        Setting::setValue('enable_retention_automation', true);
        Setting::setValue('dry_run_mode', true);
        Setting::setValue('rejected_applications_retention_months', 6);

        $this->artisan('privacy:retention-run', [
            '--force' => true,
            '--only' => 'rejected-applications',
        ])
            ->expectsOutputToContain('rejected-applications')
            ->assertExitCode(0);

        $application->refresh();

        $this->assertNotNull($application->anonymized_at);
        $this->assertSame('rejected_application_retention', $application->retention_reason);
        $this->assertNull($application->message);
        $this->assertNull($application->internal_note);
        $this->assertTrue((bool) data_get($application->profile_snapshot, 'retained_anonymized', false));
    }

    public function test_recent_rejected_application_is_not_anonymized(): void
    {
        $application = $this->createRejectedApplication(now()->subMonths(2));

        Setting::setValue('enable_retention_automation', true);
        Setting::setValue('dry_run_mode', false);
        Setting::setValue('rejected_applications_retention_months', 6);

        $this->artisan('privacy:retention-run', [
            '--force' => true,
            '--only' => 'rejected-applications',
        ])->assertExitCode(0);

        $application->refresh();
        $this->assertNull($application->anonymized_at);
        $this->assertSame('Sensitive message', $application->message);
    }

    public function test_inactive_workers_are_queued_for_delayed_deletion(): void
    {
        Queue::fake();

        $worker = User::factory()->create([
            'role' => User::ROLE_WORKER,
        ]);

        $worker->forceFill([
            'last_login_at' => now()->subMonths(30),
            'pending_deletion' => false,
        ])->save();

        Setting::setValue('enable_retention_automation', true);
        Setting::setValue('dry_run_mode', false);
        Setting::setValue('inactive_worker_retention_months', 24);

        $this->artisan('privacy:retention-run', [
            '--force' => true,
            '--only' => 'inactive-workers',
        ])->assertExitCode(0);

        $worker->refresh();

        $this->assertTrue((bool) $worker->pending_deletion);
        $this->assertNotNull($worker->anonymization_scheduled_at);
        $this->assertDatabaseHas('account_deletion_requests', [
            'user_id' => $worker->id,
            'status' => 'pending',
            'reason' => 'gdpr_retention_inactive_worker',
        ]);
    }

    public function test_inactive_employers_with_active_jobs_are_not_processed(): void
    {
        $employerUser = User::factory()->create(['role' => User::ROLE_EMPLOYER]);
        $employerUser->forceFill([
            'last_login_at' => now()->subMonths(40),
            'pending_deletion' => false,
        ])->save();

        $employer = Employer::query()->create([
            'user_id' => $employerUser->id,
            'company_name' => 'Retention Employer',
            'approved_at' => now(),
        ]);

        Job::query()->create([
            'employer_id' => $employer->id,
            'created_by_user_id' => $employerUser->id,
            'title' => 'Active Role',
            'slug' => 'active-role-' . $employer->id,
            'description' => 'Role description',
            'location_city' => 'Zagreb',
            'category' => 'IT',
            'status' => 'published',
            'published_at' => now()->subDays(2),
            'expires_at' => now()->addDays(12),
        ]);

        Setting::setValue('enable_retention_automation', true);
        Setting::setValue('dry_run_mode', false);
        Setting::setValue('inactive_employer_retention_months', 36);

        $this->artisan('privacy:retention-run', [
            '--force' => true,
            '--only' => 'inactive-employers',
        ])
            ->expectsOutputToContain('inactive-employers')
            ->assertExitCode(0);

        $employerUser->refresh();
        $this->assertFalse((bool) $employerUser->pending_deletion);
        $this->assertDatabaseMissing('account_deletion_requests', [
            'user_id' => $employerUser->id,
        ]);
    }

    public function test_notifications_retention_deletes_only_old_records(): void
    {
        $user = User::factory()->create(['role' => User::ROLE_WORKER]);

        DB::table('notifications')->insert([
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'App\\Notifications\\TestOld',
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode(['key' => 'old']),
                'read_at' => null,
                'created_at' => now()->subMonths(18),
                'updated_at' => now()->subMonths(18),
            ],
            [
                'id' => (string) \Illuminate\Support\Str::uuid(),
                'type' => 'App\\Notifications\\TestRecent',
                'notifiable_type' => User::class,
                'notifiable_id' => $user->id,
                'data' => json_encode(['key' => 'recent']),
                'read_at' => null,
                'created_at' => now()->subMonths(3),
                'updated_at' => now()->subMonths(3),
            ],
        ]);

        DB::table('notification_digests')->insert([
            'user_id' => $user->id,
            'period' => 'daily',
            'scheduled_for' => now()->subMonths(18)->toDateString(),
            'status' => 'sent',
            'sent_at' => now()->subMonths(18),
            'created_at' => now()->subMonths(18),
            'updated_at' => now()->subMonths(18),
        ]);

        DB::table('email_send_log')->insert([
            'to_address' => 'old@example.test',
            'template' => 'digest',
            'context_hash' => 'old-hash',
            'message_id' => 'msg-old',
            'sent_at' => now()->subMonths(18),
            'created_at' => now()->subMonths(18),
            'updated_at' => now()->subMonths(18),
        ]);

        Setting::setValue('enable_retention_automation', true);
        Setting::setValue('dry_run_mode', false);
        Setting::setValue('notification_retention_months', 12);

        $this->artisan('privacy:retention-run', [
            '--force' => true,
            '--only' => 'notifications',
        ])->assertExitCode(0);

        $this->assertSame(1, DB::table('notifications')->count());
        $this->assertSame(0, DB::table('notification_digests')->count());
        $this->assertSame(0, DB::table('email_send_log')->count());
    }

    public function test_user_export_includes_retention_fields_after_anonymization(): void
    {
        $application = $this->createRejectedApplication(now()->subMonths(8));

        Setting::setValue('enable_retention_automation', true);
        Setting::setValue('dry_run_mode', false);
        Setting::setValue('rejected_applications_retention_months', 6);

        $this->artisan('privacy:retention-run', [
            '--force' => true,
            '--only' => 'rejected-applications',
        ])->assertExitCode(0);

        $response = $this
            ->actingAs($application->worker)
            ->get(route('user.export'));

        $response->assertOk();

        $download = $response->baseResponse;
        $path = $download->getFile()->getPathname();
        $json = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('applications', $json);
        $this->assertNotEmpty($json['applications']);
        $this->assertArrayHasKey('anonymized_at', $json['applications'][0]);
        $this->assertArrayHasKey('retention_reason', $json['applications'][0]);
        $this->assertArrayHasKey('retention_processed_at', $json['applications'][0]);
    }

    private function createRejectedApplication(\DateTimeInterface $statusUpdatedAt): JobApplication
    {
        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);
        $employerUser = User::factory()->create(['role' => User::ROLE_EMPLOYER]);

        $employer = Employer::query()->create([
            'user_id' => $employerUser->id,
            'company_name' => 'Retention Test Employer',
            'approved_at' => now(),
        ]);

        $job = Job::query()->create([
            'employer_id' => $employer->id,
            'created_by_user_id' => $employerUser->id,
            'title' => 'Retention Role',
            'slug' => 'retention-role-' . $worker->id . '-' . uniqid(),
            'description' => 'Role description',
            'location_city' => 'Split',
            'category' => 'IT',
            'status' => 'published',
            'published_at' => now()->subDays(10),
        ]);

        return JobApplication::query()->create([
            'job_id' => $job->id,
            'worker_id' => $worker->id,
            'status' => JobApplication::STATUS_REJECTED,
            'status_updated_at' => $statusUpdatedAt,
            'profile_snapshot' => [
                'first_name' => 'Ana',
                'last_name' => 'Retention',
                'skills' => ['PHP', 'Laravel', 'SQL'],
                'languages' => [['language' => 'English', 'level' => 'C1']],
                'structured_experiences' => [['job_title' => 'Chef', 'company_name' => 'Private Co']],
                'structured_educations' => [['institution' => 'Private School', 'degree' => 'BA']],
            ],
            'job_snapshot' => ['title' => $job->title],
            'message' => 'Sensitive message',
            'internal_note' => 'Sensitive note',
        ]);
    }
}
