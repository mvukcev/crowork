<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\WorkerProfile;
use App\Services\WorkerProfileCompletenessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkerProfileCompletenessServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_profile_has_zero_percent_and_missing_required_items(): void
    {
        $profile = $this->createBaseProfile();

        $result = app(WorkerProfileCompletenessService::class)->calculate($profile->load([
            'experiences',
            'educations',
            'certificationsList',
            'referencesList',
            'skillsList',
            'languagesList',
        ]));

        $this->assertSame(0, $result['percentage']);
        $this->assertSame('starter', $result['state_key']);
        $this->assertContains('Dodaj ime', $result['missing']);
        $this->assertContains('Dodaj barem jedan jezik', $result['missing']);
        $this->assertContains('Dodaj obrazovanje', $result['missing']);
        $this->assertContains('Dodaj radno iskustvo', $result['missing']);
    }

    public function test_partial_profile_is_in_middle_band_and_has_no_invalid_percentage(): void
    {
        $profile = $this->createBaseProfile();

        $profile->update([
            'first_name' => 'Ana',
            'last_name' => 'Horvat',
            'nationality_country_code' => 'HR',
            'current_country' => 'Croatia',
            'current_city' => 'Split',
            'professional_summary' => 'Konobarica sa iskustvom.',
            'salary_expectation' => 1200,
        ]);

        $profile->skillsList()->create(['name' => 'Hospitality', 'level' => null, 'sort_order' => 0]);
        $profile->languagesList()->create(['language' => 'English', 'level' => '', 'sort_order' => 0]);

        $result = app(WorkerProfileCompletenessService::class)->calculate($profile->fresh()->load([
            'experiences',
            'educations',
            'certificationsList',
            'referencesList',
            'skillsList',
            'languagesList',
        ]));

        $this->assertGreaterThanOrEqual(0, $result['percentage']);
        $this->assertLessThanOrEqual(100, $result['percentage']);
        $this->assertGreaterThan(0, $result['percentage']);
        $this->assertLessThan(70, $result['percentage']);
        $this->assertContains($result['state_key'], ['starter', 'good_start']);
    }

    public function test_full_profile_without_bonus_sections_reaches_ninety_percent(): void
    {
        $profile = $this->createBaseProfile();

        $profile->update([
            'first_name' => 'Ivo',
            'last_name' => 'Kovac',
            'nationality_country_code' => 'HR',
            'current_country' => 'Croatia',
            'current_city' => 'Rijeka',
            'desired_city' => 'Zagreb',
            'availability_date' => now()->addWeek()->toDateString(),
            'professional_summary' => 'Iskusan vozac i skladistar.',
            'salary_expectation' => 1500,
            'visa_work_permit_status' => 'EU citizen',
            'photo_path' => 'worker-photos/test.jpg',
        ]);

        $profile->languagesList()->create(['language' => 'English', 'level' => 'B2', 'sort_order' => 0]);
        $profile->skillsList()->create(['name' => 'Forklift', 'level' => null, 'sort_order' => 0]);
        $profile->educations()->create([
            'institution' => 'Technical School',
            'degree' => 'High school',
            'field_of_study' => 'Logistics',
            'country' => 'Croatia',
            'city' => 'Rijeka',
            'start_date' => '2015-09-01',
            'end_date' => '2019-06-01',
            'description' => 'Program logistike.',
            'sort_order' => 0,
        ]);
        $profile->experiences()->create([
            'job_title' => 'Warehouse Associate',
            'company_name' => 'Cargo d.o.o.',
            'country' => 'Croatia',
            'city' => 'Rijeka',
            'start_date' => '2020-01-01',
            'end_date' => '2024-12-31',
            'is_current' => false,
            'description' => 'Organizacija robe i utovar.',
            'sort_order' => 0,
        ]);

        $result = app(WorkerProfileCompletenessService::class)->calculate($profile->fresh()->load([
            'experiences',
            'educations',
            'certificationsList',
            'referencesList',
            'skillsList',
            'languagesList',
        ]));

        $this->assertSame(90, $result['percentage']);
        $this->assertSame('ready', $result['state_key']);
        $this->assertNotContains('Dodaj obrazovanje', $result['missing']);
        $this->assertNotContains('Dodaj radno iskustvo', $result['missing']);
    }

    public function test_full_profile_with_bonus_sections_reaches_hundred_percent(): void
    {
        $profile = $this->createBaseProfile();

        $profile->update([
            'first_name' => 'Mia',
            'last_name' => 'Novak',
            'nationality_country_code' => 'HR',
            'current_country' => 'Croatia',
            'current_city' => 'Zagreb',
            'desired_city' => 'Split',
            'availability_date' => now()->toDateString(),
            'professional_summary' => 'Turizam i customer care.',
            'salary_expectation' => 1400,
            'visa_work_permit_status' => 'No sponsorship needed',
            'photo_path' => 'worker-photos/test2.jpg',
        ]);

        $profile->languagesList()->create(['language' => 'English', 'level' => 'C1', 'sort_order' => 0]);
        $profile->skillsList()->create(['name' => 'Customer Support', 'level' => null, 'sort_order' => 0]);
        $profile->educations()->create([
            'institution' => 'University',
            'degree' => 'Bachelor',
            'field_of_study' => 'Tourism',
            'country' => 'Croatia',
            'city' => 'Zagreb',
            'start_date' => '2016-10-01',
            'end_date' => '2019-06-01',
            'description' => 'Turism management.',
            'sort_order' => 0,
        ]);
        $profile->experiences()->create([
            'job_title' => 'Guest Relations',
            'company_name' => 'Adria Hotel',
            'country' => 'Croatia',
            'city' => 'Split',
            'start_date' => '2021-01-01',
            'end_date' => '2025-01-01',
            'is_current' => false,
            'description' => 'Support and communication with guests.',
            'sort_order' => 0,
        ]);
        $profile->certificationsList()->create([
            'name' => 'First Aid',
            'issuer' => 'Red Cross',
            'issued_on' => '2024-01-01',
            'expires_on' => null,
            'credential_id' => null,
            'credential_url' => null,
            'sort_order' => 0,
        ]);
        $profile->referencesList()->create([
            'full_name' => 'Ivan Horvat',
            'position' => 'Manager',
            'company' => 'Adria Hotel',
            'contact_email' => null,
            'contact_phone' => null,
            'notes' => null,
            'sort_order' => 0,
        ]);

        $result = app(WorkerProfileCompletenessService::class)->calculate($profile->fresh()->load([
            'experiences',
            'educations',
            'certificationsList',
            'referencesList',
            'skillsList',
            'languagesList',
        ]));

        $this->assertSame(100, $result['percentage']);
        $this->assertSame(100, $result['score']);
        $this->assertSame([], $result['missing']);
    }

    private function createBaseProfile(): WorkerProfile
    {
        $user = User::factory()->create([
            'role' => User::ROLE_WORKER,
        ]);

        return WorkerProfile::create([
            'user_id' => $user->id,
            'first_name' => '',
            'last_name' => '',
            'nationality_country_code' => '',
            'birth_year' => 1995,
            'skills' => [],
            'languages' => [],
            'desired_roles' => [],
            'profile_visibility' => WorkerProfile::VISIBILITY_EMPLOYERS,
        ]);
    }
}
