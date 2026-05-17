<?php

namespace Tests\Feature;

use App\Models\ConsentHistory;
use App\Models\Employer;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Setting;
use App\Models\User;
use App\Services\ConsentVersionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployerLawfulBasisUxTest extends TestCase
{
    use RefreshDatabase;

    public function test_pipeline_shows_rejected_retention_state_and_deadline(): void
    {
        [$employerUser, $job] = $this->createApprovedEmployerWithJob();

        Setting::setValue('rejected_applications_retention_months', 6);

        JobApplication::query()->create([
            'job_id' => $job->id,
            'worker_id' => User::factory()->create(['role' => User::ROLE_WORKER])->id,
            'status' => JobApplication::STATUS_REJECTED,
            'status_updated_at' => now()->subMonths(2),
            'profile_snapshot' => ['first_name' => 'Ana', 'last_name' => 'Retention'],
            'job_snapshot' => ['title' => $job->title],
            'message' => 'Candidate profile',
        ]);

        $this->actingAs($employerUser)
            ->get(route('employer.applications.pipeline'))
            ->assertOk()
            ->assertSee('Rejected, within retention window')
            ->assertSee('Data available until:');
    }

    public function test_candidate_view_shows_pending_deletion_state_and_date(): void
    {
        [$employerUser, $job] = $this->createApprovedEmployerWithJob();

        $worker = User::factory()->create([
            'role' => User::ROLE_WORKER,
            'pending_deletion' => true,
            'anonymization_scheduled_at' => now()->addDays(10),
        ]);

        $application = JobApplication::query()->create([
            'job_id' => $job->id,
            'worker_id' => $worker->id,
            'status' => JobApplication::STATUS_REVIEWING,
            'profile_snapshot' => ['first_name' => 'Ivana'],
            'job_snapshot' => ['title' => $job->title],
            'message' => 'Candidate profile',
        ]);

        $this->actingAs($employerUser)
            ->get(route('employer.applications.candidate', $application))
            ->assertOk()
            ->assertSee('Candidate requested account deletion')
            ->assertSee('Data available until:');
    }

    public function test_dashboard_candidate_card_and_job_view_show_anonymized_state(): void
    {
        [$employerUser, $job] = $this->createApprovedEmployerWithJob();

        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);

        $application = JobApplication::query()->create([
            'job_id' => $job->id,
            'worker_id' => $worker->id,
            'status' => JobApplication::STATUS_REJECTED,
            'anonymized_at' => now()->subDay(),
            'profile_snapshot' => ['retained_anonymized' => true],
            'job_snapshot' => ['title' => $job->title],
            'message' => null,
        ]);

        $this->actingAs($employerUser)
            ->get(route('employer.dashboard'))
            ->assertOk()
            ->assertSee('GDPR lawful basis for candidate data')
            ->assertSee('Data anonymized');

        $this->actingAs($employerUser)
            ->get(route('employer.jobs.show', $job))
            ->assertOk()
            ->assertSee('Anonymized candidate')
            ->assertSee('Data anonymized');

        $this->assertNotNull($application->anonymized_at);
    }

    /**
     * @return array{0: User, 1: Job}
     */
    private function createApprovedEmployerWithJob(): array
    {
        $employerUser = User::factory()->create([
            'role' => User::ROLE_EMPLOYER,
            'email_verified_at' => now(),
        ]);

        $employer = Employer::query()->create([
            'user_id' => $employerUser->id,
            'company_name' => 'Lawful Basis Employer',
            'approved_at' => now(),
        ]);

        $this->recordLegalConsent($employerUser);

        $job = Job::query()->create([
            'employer_id' => $employer->id,
            'created_by_user_id' => $employerUser->id,
            'title' => 'Backend Chef',
            'slug' => 'backend-chef-' . uniqid(),
            'description' => 'Role description',
            'location_city' => 'Split',
            'category' => 'Hospitality',
            'status' => 'published',
            'published_at' => now()->subDay(),
            'expires_at' => now()->addDays(30),
        ]);

        return [$employerUser, $job];
    }

    private function recordLegalConsent(User $user): void
    {
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
    }
}
