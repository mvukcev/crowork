<?php

namespace Tests\Feature;

use App\Models\Education;
use App\Models\Employer;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoInfrastructureTest extends TestCase
{
    use RefreshDatabase;

    public function test_sitemap_contains_public_pages_jobs_and_educations(): void
    {
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

        $response = $this->get(route('sitemap'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');
        $response->assertSee(route('home'), false);
        $response->assertSee(route('jobs.show', $job), false);
        $response->assertSee(route('educations.show', $education), false);
    }

    public function test_job_detail_has_canonical_and_job_posting_schema(): void
    {
        $employerUser = User::factory()->create(['role' => User::ROLE_EMPLOYER]);
        $employer = Employer::create([
            'user_id' => $employerUser->id,
            'approved_at' => now(),
            'company_name' => 'Acme Croatia',
            'city' => 'Zagreb',
        ]);
        $job = $this->createPublishedJob($employer, $employerUser, [
            'salary_min' => 1200,
            'salary_max' => 1800,
            'contract_type' => 'full-time',
        ]);

        $response = $this->get(route('jobs.show', $job));

        $response->assertOk();
        $response->assertSee('rel="canonical"', false);
        $response->assertSee(route('jobs.show', $job), false);
        $response->assertSee('"@type":"JobPosting"', false);
        $response->assertSee('"title":"Seasonal Chef"', false);
        $response->assertSee('"hiringOrganization"', false);
        $response->assertSee('"baseSalary"', false);
    }


    public function test_legal_aliases_use_primary_canonical_urls(): void
    {
        $this->get('/privacy-policy')
            ->assertRedirect(route('privacy'));

        $this->get('/terms-of-service')
            ->assertRedirect(route('terms'));

        $this->get('/cookie-policy')
            ->assertRedirect(route('cookies'));
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
