<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureLatestLegalConsentAccepted;
use App\Models\Employer;
use App\Models\Job;
use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserFlowSmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_worker_can_apply_to_native_hzz_email_and_hzz_external_jobs(): void
    {
        $this->withoutMiddleware(EnsureLatestLegalConsentAccepted::class);
        Mail::fake();

        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);
        WorkerProfile::query()->create([
            'user_id' => $worker->id,
            'first_name' => 'Iva',
            'last_name' => 'Kovac',
            'nationality_country_code' => 'HR',
            'birth_year' => 1995,
            'profile_visibility' => WorkerProfile::VISIBILITY_EMPLOYERS,
        ]);

        [$employerUser, $employer] = $this->createApprovedEmployer();

        $nativeJob = Job::query()->create([
            'employer_id' => $employer->id,
            'created_by_user_id' => $employerUser->id,
            'title' => 'Native job',
            'description' => 'Native description',
            'location_city' => 'Zagreb',
            'category' => 'General',
            'contract_type' => 'full_time',
            'status' => 'published',
            'published_at' => now()->subHour(),
            'expires_at' => now()->addDays(10),
            'source_system' => 'manual',
        ]);

        $hzzEmailJob = Job::query()->create([
            'employer_id' => $employer->id,
            'created_by_user_id' => $employerUser->id,
            'title' => 'HZZ email job',
            'description' => 'HZZ email description',
            'location_city' => 'Split',
            'category' => 'Hospitality',
            'contract_type' => 'full_time',
            'status' => 'published',
            'published_at' => now()->subHour(),
            'expires_at' => now()->addDays(10),
            'source_system' => 'hzz',
            'hzz_is_official' => true,
            'hzz_apply_email' => 'apply@example.hr',
            'source_url' => 'https://hzz.hr/oglas/111',
            'hzz_apply_url' => 'https://hzz.hr/oglas/111',
        ]);

        $hzzExternalJob = Job::query()->create([
            'employer_id' => $employer->id,
            'created_by_user_id' => $employerUser->id,
            'title' => 'HZZ external job',
            'description' => 'HZZ external description',
            'location_city' => 'Rijeka',
            'category' => 'Hospitality',
            'contract_type' => 'full_time',
            'status' => 'published',
            'published_at' => now()->subHour(),
            'expires_at' => now()->addDays(10),
            'source_system' => 'hzz',
            'hzz_is_official' => true,
            'source_url' => 'https://hzz.hr/oglas/222',
            'hzz_apply_url' => 'https://hzz.hr/oglas/222',
        ]);

        $this->actingAs($worker)
            ->get(route('jobs.apply', $nativeJob))
            ->assertOk();

        $this->actingAs($worker)
            ->post(route('jobs.apply.store', $nativeJob), ['consent' => '1'])
            ->assertRedirect(route('jobs.show', $nativeJob));

        $this->actingAs($worker)
            ->get(route('jobs.apply', $hzzEmailJob))
            ->assertOk();

        $this->actingAs($worker)
            ->post(route('jobs.apply.store', $hzzEmailJob), ['consent' => '1'])
            ->assertRedirect(route('jobs.show', $hzzEmailJob));

        $this->actingAs($worker)
            ->get(route('jobs.apply', $hzzExternalJob))
            ->assertOk();

        $this->actingAs($worker)
            ->get(route('jobs.hzz.open', $hzzExternalJob))
            ->assertRedirect('https://hzz.hr/oglas/222');
    }

    public function test_employer_can_create_update_jobs_and_access_dashboard_analytics_and_pipeline(): void
    {
        $this->withoutMiddleware(EnsureLatestLegalConsentAccepted::class);

        [$employerUser] = $this->createApprovedEmployer();

        $this->actingAs($employerUser)
            ->get(route('employer.dashboard'))
            ->assertOk();

        $this->actingAs($employerUser)
            ->get(route('employer.dashboard', ['tab' => 'analytics']))
            ->assertOk();

        $this->actingAs($employerUser)
            ->get(route('employer.jobs.create'))
            ->assertOk();

        $storeResponse = $this->actingAs($employerUser)->post(route('employer.jobs.store'), [
            'title' => 'Employer managed job',
            'company_name' => 'Employer d.o.o.',
            'description' => 'Description',
            'location' => 'Zagreb',
            'job_type' => 'full_time',
        ]);

        $job = Job::query()->latest('id')->firstOrFail();
        $storeResponse->assertRedirect(route('employer.jobs.edit', $job));

        $this->actingAs($employerUser)
            ->put(route('employer.jobs.update', $job), [
                'title' => 'Employer managed job updated',
                'company_name' => 'Employer d.o.o.',
                'description' => 'Description updated',
                'location' => 'Split',
                'job_type' => 'full_time',
            ])
            ->assertRedirect(route('employer.jobs.edit', $job));

        $this->actingAs($employerUser)
            ->get(route('employer.applications.pipeline'))
            ->assertOk();
    }

    public function test_worker_can_open_and_update_cv_profile_without_forbidden_or_not_found(): void
    {
        $this->withoutMiddleware(EnsureLatestLegalConsentAccepted::class);

        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);

        $this->actingAs($worker)
            ->get(route('worker.profile.edit'))
            ->assertOk();

        $this->actingAs($worker)
            ->put(route('worker.profile.update'), [
                'first_name' => 'Worker',
                'last_name' => 'Test',
                'nationality_country_code' => 'HR',
                'birth_year' => 1990,
                'profile_visibility' => WorkerProfile::VISIBILITY_EMPLOYERS,
            ])
            ->assertRedirect(route('worker.profile.edit'));
    }

    /**
     * @return array{0: User, 1: Employer}
     */
    private function createApprovedEmployer(): array
    {
        $employerUser = User::factory()->create(['role' => User::ROLE_EMPLOYER]);
        $employer = Employer::query()->create([
            'user_id' => $employerUser->id,
            'company_name' => 'Employer d.o.o.',
            'city' => 'Zagreb',
            'approved_at' => now(),
        ]);

        return [$employerUser, $employer];
    }
}
