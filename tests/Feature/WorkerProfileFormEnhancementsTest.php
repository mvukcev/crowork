<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureLatestLegalConsentAccepted;
use App\Models\User;
use App\Models\WorkerEducation;
use App\Models\WorkerExperience;
use App\Models\WorkerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkerProfileFormEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(EnsureLatestLegalConsentAccepted::class);
    }

    public function test_worker_profile_form_renders_country_datalists_visa_dropdown_and_skill_suggestions_in_english(): void
    {
        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);

        WorkerProfile::query()->create([
            'user_id' => $worker->id,
            'first_name' => 'Ana',
            'last_name' => 'Horvat',
            'nationality_country_code' => 'HR',
            'birth_year' => 1994,
            'profile_visibility' => WorkerProfile::VISIBILITY_EMPLOYERS,
        ]);

        $this->actingAs($worker)
            ->get(route('worker.profile.edit'))
            ->assertOk()
            ->assertSee('id="country-options-list"', false)
            ->assertSee('id="skill-suggestions-list"', false)
            ->assertSee('Croatia', false)
            ->assertSee('Visa/work permit status', false)
            ->assertSee('I need a work permit', false)
            ->assertSee('Customer service', false);
    }

    public function test_worker_profile_form_renders_localized_hr_labels_with_diacritics_and_hr_options(): void
    {
        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);

        WorkerProfile::query()->create([
            'user_id' => $worker->id,
            'first_name' => 'Ana',
            'last_name' => 'Horvat',
            'nationality_country_code' => 'HR',
            'birth_year' => 1994,
            'profile_visibility' => WorkerProfile::VISIBILITY_EMPLOYERS,
        ]);

        $this->actingAs($worker)
            ->get(route('worker.profile.edit', ['lang' => 'hr']))
            ->assertOk()
            ->assertSee('Trenutna država', false)
            ->assertSee('Dostupnost / početni datum', false)
            ->assertSee('Kratki stručni sažetak', false)
            ->assertSee('Željeni grad/lokacija u Hrvatskoj', false)
            ->assertSee('Imam važeću radnu dozvolu', false)
            ->assertSee('Korisnička podrška', false)
            ->assertSee('Hrvatska', false);
    }

    public function test_profile_update_normalizes_known_country_and_visa_values_but_keeps_unknown_country_text(): void
    {
        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);

        $profile = WorkerProfile::query()->create([
            'user_id' => $worker->id,
            'first_name' => 'Ana',
            'last_name' => 'Horvat',
            'nationality_country_code' => 'HR',
            'birth_year' => 1994,
            'skills' => [],
            'languages' => [],
            'profile_visibility' => WorkerProfile::VISIBILITY_EMPLOYERS,
        ]);

        $this->actingAs($worker)
            ->put(route('worker.profile.update', ['lang' => 'hr']), [
                'first_name' => 'Ana',
                'last_name' => 'Horvat',
                'nationality_country_code' => 'Hrvatska',
                'current_country' => 'Croatia',
                'birth_year' => 1994,
                'profile_visibility' => WorkerProfile::VISIBILITY_EMPLOYERS,
                'visa_work_permit_status' => 'Trebam radnu dozvolu',
                'experiences' => [
                    [
                        'job_title' => 'Konobar',
                        'company_name' => 'Hotel One',
                        'country' => 'Njemačka',
                        'city' => 'Berlin',
                        'start_date' => '2024-01-01',
                        'end_date' => '2024-12-01',
                        'description' => 'Opis',
                    ],
                ],
                'educations' => [
                    [
                        'institution' => 'School',
                        'degree' => 'BSc',
                        'field_of_study' => 'Tourism',
                        'country' => 'Atlantis',
                    ],
                ],
            ])
            ->assertRedirect(route('worker.profile.edit'));

        $profile->refresh();

        $this->assertSame('HR', $profile->nationality_country_code);
        $this->assertSame('HR', $profile->current_country);
        $this->assertSame('need_permit', $profile->visa_work_permit_status);

        $experience = WorkerExperience::query()->where('worker_profile_id', $profile->id)->firstOrFail();
        $education = WorkerEducation::query()->where('worker_profile_id', $profile->id)->firstOrFail();

        $this->assertSame('DE', $experience->country);
        $this->assertSame('Atlantis', $education->country);
    }
}
