<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureLatestLegalConsentAccepted;
use App\Models\Employer;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmployerJobSubmissionPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_submission_defaults_to_pending_and_ignores_malicious_create_flags(): void
    {
        $this->withoutMiddleware(EnsureLatestLegalConsentAccepted::class);

        [$user, $employer] = $this->createEmployerWithApprovalOverride(true);

        $response = $this->actingAs($user)->post(route('employer.jobs.store'), array_merge(
            $this->validPayload(),
            [
                'status' => 'published',
                'slug' => 'attacker-slug',
                'is_featured' => 1,
                'is_urgent' => 1,
                'is_active' => 1,
            ]
        ));

        $job = Job::query()->where('employer_id', $employer->id)->latest('id')->firstOrFail();

        $response->assertRedirect(route('employer.jobs.edit', $job));
        $this->assertSame('pending', $job->status);
        $this->assertFalse((bool) $job->is_featured);
        $this->assertFalse((bool) $job->is_urgent);
        $this->assertNotSame('attacker-slug', (string) $job->slug);
        $this->assertNull($job->published_at);
    }

    public function test_submission_auto_publishes_when_employer_is_auto_approved(): void
    {
        $this->withoutMiddleware(EnsureLatestLegalConsentAccepted::class);

        [$user, $employer] = $this->createEmployerWithApprovalOverride(false);

        $response = $this->actingAs($user)->post(route('employer.jobs.store'), $this->validPayload('Auto Publish Job'));

        $job = Job::query()->where('employer_id', $employer->id)->latest('id')->firstOrFail();

        $response->assertRedirect(route('employer.jobs.edit', $job));
        $this->assertSame('published', $job->status);
        $this->assertNotNull($job->published_at);
    }

    public function test_update_ignores_malicious_status_and_feature_flags(): void
    {
        $this->withoutMiddleware(EnsureLatestLegalConsentAccepted::class);

        [$user, $employer] = $this->createEmployerWithApprovalOverride(true);

        $job = Job::query()->create([
            'employer_id' => $employer->id,
            'created_by_user_id' => $user->id,
            'title' => 'Existing Job',
            'description' => 'Existing description.',
            'location_city' => 'Zagreb',
            'category' => 'General',
            'contract_type' => 'full_time',
            'status' => 'published',
            'published_at' => now(),
            'is_featured' => true,
            'is_urgent' => true,
        ]);

        $response = $this->actingAs($user)->put(route('employer.jobs.update', $job), array_merge(
            $this->validPayload('Updated Title'),
            [
                'status' => 'published',
                'is_featured' => 0,
                'is_urgent' => 0,
                'is_active' => 1,
            ]
        ));

        $job->refresh();

        $response->assertRedirect(route('employer.jobs.edit', $job));
        $this->assertSame('pending', $job->status);
        $this->assertNull($job->published_at);
        $this->assertTrue((bool) $job->is_featured);
        $this->assertTrue((bool) $job->is_urgent);
    }

    public function test_employer_manual_hzz_job_edit_route_returns_404(): void
    {
        $this->withoutMiddleware(EnsureLatestLegalConsentAccepted::class);

        [$user, $employer] = $this->createEmployerWithApprovalOverride(true);

        $hzzJob = Job::query()->create([
            'employer_id' => $employer->id,
            'created_by_user_id' => $user->id,
            'title' => 'HZZ Protected Job',
            'description' => 'Protected HZZ listing.',
            'location_city' => 'Zagreb',
            'category' => 'HZZ',
            'contract_type' => 'full_time',
            'status' => 'published',
            'published_at' => now(),
            'source_system' => 'hzz',
            'hzz_is_official' => true,
        ]);

        $this->actingAs($user)
            ->get(route('employer.jobs.edit', $hzzJob))
            ->assertNotFound();
    }

    /**
     * @return array{0: User, 1: Employer}
     */
    private function createEmployerWithApprovalOverride(bool $requiresApproval): array
    {
        $user = User::factory()->create([
            'role' => User::ROLE_EMPLOYER,
        ]);

        $employer = Employer::query()->create([
            'user_id' => $user->id,
            'company_name' => 'Employer d.o.o.',
            'city' => 'Zagreb',
            'approved_at' => now(),
            'require_approval_override' => $requiresApproval,
        ]);

        return [$user, $employer];
    }

    /**
     * @return array<string, mixed>
     */
    private function validPayload(string $title = 'Test Employer Job'): array
    {
        return [
            'title' => $title,
            'company_name' => 'Employer d.o.o.',
            'description' => 'Job description',
            'location' => 'Zagreb',
            'job_type' => 'full_time',
        ];
    }
}
