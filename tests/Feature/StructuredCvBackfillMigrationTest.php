<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StructuredCvBackfillMigrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_backfill_migration_populates_structured_tables_from_legacy_profile_fields(): void
    {
        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);

        $profile = WorkerProfile::query()->create([
            'user_id' => $worker->id,
            'first_name' => 'Ana',
            'last_name' => 'Horvat',
            'nationality_country_code' => 'HR',
            'birth_year' => 1992,
            'education_summary' => 'University of Split, Hospitality',
            'work_experience' => 'Head Chef at Hotel Adriatic',
            'certifications' => 'Food Safety Level 3',
            'recommendations' => 'Reference from previous manager',
            'skills' => ['Laravel', 'Operations'],
            'languages' => [
                ['language' => 'English', 'level' => 'B2'],
                ['language' => 'German', 'level' => 'A2'],
            ],
            'profile_visibility' => WorkerProfile::VISIBILITY_EMPLOYERS,
        ]);

        DB::table('worker_experiences')->where('worker_profile_id', $profile->id)->delete();
        DB::table('worker_educations')->where('worker_profile_id', $profile->id)->delete();
        DB::table('worker_certifications')->where('worker_profile_id', $profile->id)->delete();
        DB::table('worker_references')->where('worker_profile_id', $profile->id)->delete();
        DB::table('worker_skills')->where('worker_profile_id', $profile->id)->delete();
        DB::table('worker_languages')->where('worker_profile_id', $profile->id)->delete();

        $migration = require base_path('database/migrations/2026_05_17_231000_backfill_structured_worker_cv_tables.php');
        $migration->up();

        $this->assertDatabaseHas('worker_experiences', [
            'worker_profile_id' => $profile->id,
            'job_title' => 'Legacy Experience',
            'description' => 'Head Chef at Hotel Adriatic',
        ]);

        $this->assertDatabaseHas('worker_educations', [
            'worker_profile_id' => $profile->id,
            'institution' => 'Legacy Education',
            'description' => 'University of Split, Hospitality',
        ]);

        $this->assertDatabaseHas('worker_certifications', [
            'worker_profile_id' => $profile->id,
            'name' => 'Legacy Certifications',
        ]);

        $this->assertDatabaseHas('worker_references', [
            'worker_profile_id' => $profile->id,
            'full_name' => 'Legacy Reference',
            'notes' => 'Reference from previous manager',
        ]);

        $this->assertDatabaseHas('worker_skills', [
            'worker_profile_id' => $profile->id,
            'name' => 'Laravel',
        ]);

        $this->assertDatabaseHas('worker_languages', [
            'worker_profile_id' => $profile->id,
            'language' => 'English',
            'level' => 'B2',
        ]);
    }
}
