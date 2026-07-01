<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HzzJobShowVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_hzz_application_instructions_are_hidden_for_guests(): void
    {
        $job = $this->createPublishedHzzJob('prijava@example.hr');

        $response = $this->get(route('jobs.show', $job));

        $response->assertOk();
        $response->assertDontSee(__('ui.jobs_show.application_instructions'));
    }

    public function test_hzz_application_instructions_are_visible_for_logged_worker(): void
    {
        $job = $this->createPublishedHzzJob('prijava@example.hr');
        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);

        $response = $this->actingAs($worker)->get(route('jobs.show', $job));

        $response->assertOk();
        $response->assertSee(__('ui.jobs_show.application_instructions'));
        $response->assertSee('prijava@example.hr');
    }

    private function createPublishedHzzJob(string $email): Job
    {
        $employerUser = User::factory()->create(['role' => User::ROLE_EMPLOYER]);
        $employer = Employer::query()->create([
            'user_id' => $employerUser->id,
            'company_name' => 'HZZ Partner d.o.o.',
            'city' => 'Zagreb',
            'approved_at' => now(),
        ]);

        return Job::query()->create([
            'employer_id' => $employer->id,
            'created_by_user_id' => $employerUser->id,
            'title' => 'Operater punionice',
            'description' => 'Opis radnog mjesta.',
            'location_city' => 'Zagreb',
            'category' => 'HZZ',
            'status' => 'published',
            'published_at' => now()->subHour(),
            'expires_at' => now()->addDays(7),
            'source_system' => 'hzz',
            'hzz_is_official' => true,
            'application_instructions' => $email,
            'hzz_apply_email' => $email,
            'source_url' => 'http://burzarada.hzz.hr/RadnoMjesto_Ispis.aspx?WebSifra=1',
        ]);
    }
}
