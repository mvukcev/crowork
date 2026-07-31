<?php

namespace Tests\Feature;

use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobCardEmployerNameTest extends TestCase
{
    use RefreshDatabase;

    public function test_external_employer_name_is_shown_on_home_and_jobs_cards_in_all_supported_locales(): void
    {
        Job::query()->create([
            'source_type' => 'external',
            'external_company_name' => 'Dom zdravlja Primjer',
            'title' => 'Medicinska sestra',
            'slug' => 'medicinska-sestra',
            'description' => 'Opis oglasa',
            'location_city' => 'Prelog',
            'category' => 'Zdravstvo',
            'contract_type' => 'full_time',
            'status' => 'published',
            'published_at' => now(),
            'expires_at' => now()->addWeek(),
            'is_featured' => true,
        ]);

        foreach (['hr', 'en'] as $locale) {
            $this->withSession(['locale' => $locale])
                ->get(route('home'))
                ->assertOk()
                ->assertSee('Dom zdravlja Primjer');

            $this->withSession(['locale' => $locale])
                ->get(route('jobs.index'))
                ->assertOk()
                ->assertSee('Dom zdravlja Primjer');
        }
    }
}
