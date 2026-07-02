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

    public function test_hzz_source_copy_is_hidden_for_guests_but_contact_is_visible(): void
    {
        $job = $this->createPublishedHzzJob('prijava@example.hr');

        $response = $this->get(route('jobs.show', $job));

        $response->assertOk();
        $response->assertDontSee(__('ui.jobs_show.application_instructions'));
        $response->assertDontSee('HZZ imported listing.');
        $response->assertDontSee('Službeni izvor');
        $response->assertSee(__('ui.jobs_show.contact_title'));
        $response->assertSee('prijava@example.hr');
    }

    public function test_hzz_worker_view_keeps_contact_panel_without_source_banner(): void
    {
        $job = $this->createPublishedHzzJob('prijava@example.hr');
        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);

        $response = $this->actingAs($worker)->get(route('jobs.show', $job));

        $response->assertOk();
        $response->assertDontSee(__('ui.jobs_show.application_instructions'));
        $response->assertDontSee('Službeni izvor');
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
