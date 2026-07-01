<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeFeaturedJobsTest extends TestCase
{
    use RefreshDatabase;

    public function test_homepage_featured_section_prioritizes_featured_and_fills_with_active_jobs_only(): void
    {
        [$employerUser, $employer] = $this->createEmployer();

        $featuredA = $this->createJob($employer, $employerUser, [
            'title' => 'Featured A',
            'is_featured' => true,
            'slug' => 'featured-a',
            'published_at' => now()->subMinutes(10),
        ]);

        $featuredB = $this->createJob($employer, $employerUser, [
            'title' => 'Featured B',
            'is_featured' => true,
            'slug' => 'featured-b',
            'published_at' => now()->subMinutes(20),
        ]);

        $fallbackJob = $this->createJob($employer, $employerUser, [
            'title' => 'Fallback Active',
            'is_featured' => false,
            'slug' => 'fallback-active',
            'published_at' => now()->subMinutes(30),
        ]);

        $expiredFeatured = $this->createJob($employer, $employerUser, [
            'title' => 'Expired Featured',
            'is_featured' => true,
            'slug' => 'expired-featured',
            'published_at' => now()->subDay(),
            'expires_at' => now()->subMinute(),
        ]);

        $draftFeatured = $this->createJob($employer, $employerUser, [
            'title' => 'Draft Featured',
            'is_featured' => true,
            'slug' => 'draft-featured',
            'status' => 'draft',
            'published_at' => null,
        ]);

        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertSee(route('jobs.show', $featuredA), false);
        $response->assertSee(route('jobs.show', $featuredB), false);
        $response->assertSee(route('jobs.show', $fallbackJob), false);
        $response->assertDontSee(route('jobs.show', $expiredFeatured), false);
        $response->assertDontSee(route('jobs.show', $draftFeatured), false);
    }

    /**
     * @return array{0: User, 1: Employer}
     */
    private function createEmployer(): array
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

    /**
     * @param array<string, mixed> $overrides
     */
    private function createJob(Employer $employer, User $employerUser, array $overrides = []): Job
    {
        return Job::query()->create(array_merge([
            'employer_id' => $employer->id,
            'created_by_user_id' => $employerUser->id,
            'title' => 'Test job',
            'description' => 'Description',
            'location_city' => 'Zagreb',
            'category' => 'General',
            'contract_type' => 'full_time',
            'status' => 'published',
            'published_at' => now()->subHour(),
            'expires_at' => now()->addDays(7),
            'is_featured' => false,
        ], $overrides));
    }
}
