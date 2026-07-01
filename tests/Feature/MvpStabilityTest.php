<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureLatestLegalConsentAccepted;
use App\Models\Education;
use App\Models\EducationApplication;
use App\Models\Employer;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\WorkerProfile;
use App\Notifications\EmployerAccountApproved;
use App\Notifications\JobApplicationSubmitted;
use App\Notifications\JobStatusChanged;
use App\Notifications\NewJobApplicationReceived;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class MvpStabilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_job_application_notifies_worker_and_employer(): void
    {
        $this->withoutMiddleware(EnsureLatestLegalConsentAccepted::class);
        Notification::fake();
        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);
        $employerUser = User::factory()->create(['role' => User::ROLE_EMPLOYER]);
        $employer = Employer::create([
            'user_id' => $employerUser->id,
            'approved_at' => now(),
            'company_name' => 'Acme Croatia',
            'city' => 'Zagreb',
        ]);
        $job = $this->createPublishedJob($employer, $employerUser);
        $this->createWorkerProfile($worker);

        $response = $this->actingAs($worker)->post(route('jobs.apply.store', $job), [
            'message' => 'I am interested.',
            'consent' => '1',
        ]);

        $response->assertRedirect(route('jobs.show', $job->slug));
        $this->assertDatabaseHas('job_applications', [
            'job_id' => $job->id,
            'worker_id' => $worker->id,
            'status' => 'new',
        ]);
        Notification::assertSentTo($worker, JobApplicationSubmitted::class);
        Notification::assertSentTo($employerUser, NewJobApplicationReceived::class);
    }

    public function test_employer_and_job_status_notifications_are_sent(): void
    {
        Notification::fake();

        $employerUser = User::factory()->create(['role' => User::ROLE_EMPLOYER]);
        $employer = Employer::create([
            'user_id' => $employerUser->id,
            'company_name' => 'Acme Croatia',
            'city' => 'Zagreb',
        ]);
        $job = Job::create([
            'employer_id' => $employer->id,
            'created_by_user_id' => $employerUser->id,
            'title' => 'Pending Chef',
            'description' => 'Kitchen work.',
            'location_city' => 'Split',
            'category' => 'Hospitality',
            'status' => 'pending',
        ]);

        $employer->update(['approved_at' => now()]);
        $job->update(['status' => 'published', 'published_at' => now()]);
        $job->update(['status' => 'delisted']);

        Notification::assertSentTo($employerUser, EmployerAccountApproved::class);
        Notification::assertSentTo($employerUser, JobStatusChanged::class, fn ($notification) => $notification->status === 'published');
        Notification::assertSentTo($employerUser, JobStatusChanged::class, fn ($notification) => $notification->status === 'delisted');
    }

    public function test_worker_application_tracking_pages_render(): void
    {
        $this->withoutMiddleware(EnsureLatestLegalConsentAccepted::class);
        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);
        $employerUser = User::factory()->create(['role' => User::ROLE_EMPLOYER]);
        $employer = Employer::create([
            'user_id' => $employerUser->id,
            'approved_at' => now(),
            'company_name' => 'Acme Croatia',
            'city' => 'Zagreb',
        ]);
        $job = $this->createPublishedJob($employer, $employerUser);
        $education = Education::create([
            'created_by_user_id' => $employerUser->id,
            'title' => 'Croatian Basics',
            'description' => 'Language basics.',
            'city' => 'Zagreb',
            'status' => 'published',
            'published_at' => now(),
        ]);

        JobApplication::create([
            'job_id' => $job->id,
            'worker_id' => $worker->id,
            'profile_snapshot' => [],
            'status' => 'new',
        ]);
        EducationApplication::create([
            'education_id' => $education->id,
            'worker_id' => $worker->id,
            'profile_snapshot' => [],
            'status' => 'new',
        ]);

        $this->actingAs($worker)
            ->get(route('worker.applications.index'))
            ->assertOk()
            ->assertSee('Seasonal Chef')
            ->assertSee('Acme Croatia');

        $this->actingAs($worker)
            ->get(route('worker.education-applications.index'))
            ->assertOk()
            ->assertSee('Croatian Basics')
            ->assertSee($employerUser->name);
    }

    public function test_expire_listings_command_marks_past_listings_expired(): void
    {
        $employerUser = User::factory()->create(['role' => User::ROLE_EMPLOYER]);
        $employer = Employer::create([
            'user_id' => $employerUser->id,
            'approved_at' => now(),
            'company_name' => 'Acme Croatia',
            'city' => 'Zagreb',
        ]);
        $job = $this->createPublishedJob($employer, $employerUser, ['expires_at' => now()->subMinute()]);
        $education = Education::create([
            'created_by_user_id' => $employerUser->id,
            'title' => 'Expired Course',
            'description' => 'Past education.',
            'city' => 'Zagreb',
            'status' => 'published',
            'published_at' => now()->subDays(2),
            'expires_at' => now()->subMinute(),
        ]);

        $this->artisan('crowork:expire-listings')
            ->expectsOutput('Expired 1 jobs and 1 educations.')
            ->assertSuccessful();

        $this->assertSame('expired', $job->fresh()->status);
        $this->assertSame('expired', $education->fresh()->status);
    }

    private function createWorkerProfile(User $worker): WorkerProfile
    {
        return WorkerProfile::create([
            'user_id' => $worker->id,
            'first_name' => 'Test',
            'last_name' => 'Worker',
            'nationality_country_code' => 'HR',
            'birth_year' => 1990,
            'skills' => ['Hospitality'],
        ]);
    }

    /**
     * @param array<string, mixed> $overrides
     */
    private function createPublishedJob(Employer $employer, User $employerUser, array $overrides = []): Job
    {
        return Job::create(array_merge([
            'employer_id' => $employer->id,
            'created_by_user_id' => $employerUser->id,
            'title' => 'Seasonal Chef',
            'description' => 'Cook and prepare meals.',
            'location_city' => 'Split',
            'category' => 'Hospitality',
            'status' => 'published',
            'published_at' => now(),
            'expires_at' => now()->addMonth(),
        ], $overrides));
    }
}
