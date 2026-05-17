<?php

namespace Tests\Unit;

use App\Jobs\AnonymizeUserDataJob;
use App\Models\AccountDeletionRequest;
use App\Models\ApplicationComment;
use App\Models\Employer;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnonymizeUserDataJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_anonymizes_worker_data_and_completes_request(): void
    {
        $worker = User::factory()->create([
            'role' => User::ROLE_WORKER,
            'pending_deletion' => true,
            'deletion_requested_at' => now()->subDays(15),
            'anonymization_scheduled_at' => now()->subDay(),
        ]);

        WorkerProfile::query()->create([
            'user_id' => $worker->id,
            'first_name' => 'Ivana',
            'last_name' => 'Test',
            'nationality_country_code' => 'HR',
            'birth_year' => 1990,
            'education_summary' => 'Private education',
            'skills' => ['PHP'],
        ]);

        $employerUser = User::factory()->create(['role' => User::ROLE_EMPLOYER]);
        $employer = Employer::query()->create([
            'user_id' => $employerUser->id,
            'company_name' => 'Test Employer',
            'approved_at' => now(),
        ]);

        $job = Job::query()->create([
            'employer_id' => $employer->id,
            'created_by_user_id' => $employerUser->id,
            'title' => 'Server',
            'slug' => 'server-' . $worker->id,
            'description' => 'Desc',
            'location_city' => 'Split',
            'category' => 'IT',
            'status' => 'published',
            'published_at' => now(),
        ]);

        $application = JobApplication::query()->create([
            'job_id' => $job->id,
            'worker_id' => $worker->id,
            'profile_snapshot' => ['first_name' => 'Ivana'],
            'job_snapshot' => ['title' => 'Server'],
            'message' => 'Private message',
            'internal_note' => 'Internal note',
        ]);

        ApplicationComment::query()->create([
            'job_application_id' => $application->id,
            'user_id' => $worker->id,
            'comment' => 'Sensitive comment',
        ]);

        $request = AccountDeletionRequest::query()->create([
            'user_id' => $worker->id,
            'status' => AccountDeletionRequest::STATUS_PENDING,
            'requested_at' => now()->subDays(15),
            'anonymization_scheduled_at' => now()->subDay(),
        ]);

        AnonymizeUserDataJob::dispatchSync($worker->id, $request->id);

        $this->assertSoftDeleted('users', ['id' => $worker->id]);

        $workerFresh = User::withTrashed()->findOrFail($worker->id);
        $this->assertStringContainsString('anonymous_', $workerFresh->email);

        $this->assertDatabaseHas('account_deletion_requests', [
            'id' => $request->id,
            'status' => AccountDeletionRequest::STATUS_COMPLETED,
        ]);

        $this->assertDatabaseHas('job_applications', [
            'id' => $application->id,
            'message' => null,
            'internal_note' => null,
        ]);

        $this->assertDatabaseHas('application_comments', [
            'job_application_id' => $application->id,
            'comment' => '[removed by privacy request]',
        ]);
    }
}
