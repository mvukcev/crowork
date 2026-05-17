<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StructuredCvArchitectureTest extends TestCase
{
    use RefreshDatabase;

    public function test_worker_profile_update_persists_structured_cv_rows(): void
    {
        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);

        WorkerProfile::query()->create([
            'user_id' => $worker->id,
            'first_name' => 'Ana',
            'last_name' => 'Horvat',
            'nationality_country_code' => 'HR',
            'birth_year' => 1994,
            'skills' => [],
            'languages' => [],
            'profile_visibility' => WorkerProfile::VISIBILITY_EMPLOYERS,
        ]);

        $response = $this
            ->actingAs($worker)
            ->put(route('worker.profile.update'), [
                'first_name' => 'Ana',
                'last_name' => 'Horvat',
                'nationality_country_code' => 'HR',
                'birth_year' => 1994,
                'profile_visibility' => WorkerProfile::VISIBILITY_EMPLOYERS,
                'skills' => ['Laravel', 'Docker'],
                'languages' => [
                    ['language' => 'English', 'level' => 'B2'],
                ],
                'experiences' => [
                    [
                        'job_title' => 'Chef',
                        'company_name' => 'Hotel One',
                        'country' => 'HR',
                        'city' => 'Split',
                        'start_date' => '2023-01-01',
                        'end_date' => '2024-02-01',
                        'description' => 'Kitchen operations',
                    ],
                ],
                'educations' => [
                    [
                        'institution' => 'Hospitality School',
                        'degree' => 'Bachelor',
                        'field_of_study' => 'Hospitality',
                    ],
                ],
                'certifications_list' => [
                    [
                        'name' => 'Food Safety',
                        'issuer' => 'EU Board',
                    ],
                ],
                'references_list' => [
                    [
                        'full_name' => 'Ivana Ilic',
                        'company' => 'Hotel One',
                        'contact_email' => 'ivana@gmail.com',
                    ],
                ],
            ]);

        $response->assertRedirect(route('worker.profile.edit'));

        $profile = WorkerProfile::query()->where('user_id', $worker->id)->firstOrFail();

        $this->assertDatabaseHas('worker_experiences', [
            'worker_profile_id' => $profile->id,
            'job_title' => 'Chef',
        ]);

        $this->assertDatabaseHas('worker_educations', [
            'worker_profile_id' => $profile->id,
            'institution' => 'Hospitality School',
        ]);

        $this->assertDatabaseHas('worker_certifications', [
            'worker_profile_id' => $profile->id,
            'name' => 'Food Safety',
        ]);

        $this->assertDatabaseHas('worker_references', [
            'worker_profile_id' => $profile->id,
            'full_name' => 'Ivana Ilic',
        ]);
    }

    public function test_snapshot_contains_structured_sections_and_legacy_fallback_keys(): void
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

        $profile->experiences()->create([
            'job_title' => 'Chef',
            'company_name' => 'Hotel One',
            'sort_order' => 0,
        ]);

        $profile->educations()->create([
            'institution' => 'Hospitality School',
            'degree' => 'Bachelor',
            'sort_order' => 0,
        ]);

        $snapshot = $profile->toSnapshot();

        $this->assertArrayHasKey('structured_experiences', $snapshot);
        $this->assertArrayHasKey('structured_educations', $snapshot);
        $this->assertArrayHasKey('structured_certifications', $snapshot);
        $this->assertArrayHasKey('structured_references', $snapshot);
        $this->assertArrayHasKey('work_experience', $snapshot);
        $this->assertArrayHasKey('education_summary', $snapshot);
        $this->assertSame(2, $snapshot['snapshot_version']);
    }
}
