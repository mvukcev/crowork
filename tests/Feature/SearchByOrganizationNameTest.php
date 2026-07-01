<?php

namespace Tests\Feature;

use App\Models\Education;
use App\Models\Employer;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchByOrganizationNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_jobs_search_matches_employer_company_display_name(): void
    {
        $matchingEmployerUser = User::factory()->create(['role' => User::ROLE_EMPLOYER]);
        $matchingEmployer = Employer::query()->create([
            'user_id' => $matchingEmployerUser->id,
            'approved_at' => now(),
            'company_name' => 'Delta Hospitality d.o.o.',
            'company_display_name' => 'Hotel Aurora',
            'city' => 'Split',
        ]);

        Job::query()->create([
            'employer_id' => $matchingEmployer->id,
            'created_by_user_id' => $matchingEmployerUser->id,
            'title' => 'Reception Specialist',
            'description' => 'Front-desk operations and guest support.',
            'location_city' => 'Split',
            'category' => 'Hospitality',
            'status' => 'published',
            'published_at' => now()->subHour(),
            'expires_at' => now()->addDays(7),
        ]);

        $otherEmployerUser = User::factory()->create(['role' => User::ROLE_EMPLOYER]);
        $otherEmployer = Employer::query()->create([
            'user_id' => $otherEmployerUser->id,
            'approved_at' => now(),
            'company_name' => 'Riva Retail d.o.o.',
            'company_display_name' => 'Riva Market',
            'city' => 'Zagreb',
        ]);

        Job::query()->create([
            'employer_id' => $otherEmployer->id,
            'created_by_user_id' => $otherEmployerUser->id,
            'title' => 'Sales Assistant',
            'description' => 'In-store support and checkout duties.',
            'location_city' => 'Zagreb',
            'category' => 'Retail',
            'status' => 'published',
            'published_at' => now()->subHour(),
            'expires_at' => now()->addDays(7),
        ]);

        $response = $this->get(route('jobs.index', ['q' => 'Aurora']));

        $response->assertOk();
        $response->assertSee('Reception Specialist');
        $response->assertDontSee('Sales Assistant');
    }

    public function test_educations_search_matches_institution_name_via_provider(): void
    {
        $matchingProviderUser = User::factory()->create([
            'role' => User::ROLE_EMPLOYER,
            'name' => 'Institut Delta',
        ]);

        Employer::query()->create([
            'user_id' => $matchingProviderUser->id,
            'approved_at' => now(),
            'company_name' => 'Akademija Delta',
            'company_display_name' => 'Delta Learning Center',
            'city' => 'Split',
        ]);

        Education::query()->create([
            'created_by_user_id' => $matchingProviderUser->id,
            'title' => 'Profesionalna komunikacija',
            'description' => 'Program za razvoj komunikacijskih vještina.',
            'city' => 'Split',
            'is_online' => false,
            'status' => 'published',
            'published_at' => now()->subHour(),
            'expires_at' => now()->addDays(30),
        ]);

        $otherProviderUser = User::factory()->create([
            'role' => User::ROLE_EMPLOYER,
            'name' => 'Centar Sjever',
        ]);

        Employer::query()->create([
            'user_id' => $otherProviderUser->id,
            'approved_at' => now(),
            'company_name' => 'Centar Znanja',
            'company_display_name' => 'North Skills Hub',
            'city' => 'Rijeka',
        ]);

        Education::query()->create([
            'created_by_user_id' => $otherProviderUser->id,
            'title' => 'Osnove Excela',
            'description' => 'Uvodni program za tablične kalkulacije.',
            'city' => 'Rijeka',
            'is_online' => true,
            'status' => 'published',
            'published_at' => now()->subHour(),
            'expires_at' => now()->addDays(30),
        ]);

        $response = $this->get(route('educations.index', ['q' => 'Delta']));

        $response->assertOk();
        $response->assertSee('Profesionalna komunikacija');
        $response->assertDontSee('Osnove Excela');
    }
}
