<?php

namespace Tests\Feature;

use App\Models\ConsentHistory;
use App\Models\Employer;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\NotificationPreference;
use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserDataExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_export_contains_expected_gdpr_sections(): void
    {
        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);

        WorkerProfile::query()->create([
            'user_id' => $worker->id,
            'first_name' => 'Ana',
            'last_name' => 'Horvat',
            'nationality_country_code' => 'HR',
            'birth_year' => 1995,
            'skills' => ['PHP', 'Laravel'],
        ]);

        $employerUser = User::factory()->create(['role' => User::ROLE_EMPLOYER]);
        $employer = Employer::query()->create([
            'user_id' => $employerUser->id,
            'company_name' => 'Test Company',
            'approved_at' => now(),
        ]);

        $job = Job::query()->create([
            'employer_id' => $employer->id,
            'created_by_user_id' => $employerUser->id,
            'title' => 'Backend Developer',
            'slug' => 'backend-developer-' . $worker->id,
            'description' => 'Job description',
            'location_city' => 'Zagreb',
            'category' => 'IT',
            'status' => 'published',
            'published_at' => now(),
        ]);

        JobApplication::query()->create([
            'job_id' => $job->id,
            'worker_id' => $worker->id,
            'profile_snapshot' => ['first_name' => 'Ana', 'cv_path' => 'cv/ana.pdf'],
            'job_snapshot' => ['title' => $job->title],
            'message' => 'I am interested in this role.',
        ]);

        ConsentHistory::query()->create([
            'user_id' => $worker->id,
            'consent_type' => 'privacy_policy',
            'consent_version' => '2026-05-17',
            'source' => 'registration',
            'given' => true,
            'accepted_at' => now(),
        ]);

        NotificationPreference::query()->create([
            'user_id' => $worker->id,
            'category' => 'applications',
            'email_enabled' => true,
            'database_enabled' => true,
            'digest_frequency' => 'daily',
        ]);

        DB::table('sessions')->insert([
            'id' => 'session_export_test',
            'user_id' => $worker->id,
            'ip_address' => '127.0.0.1',
            'user_agent' => 'PHPUnit',
            'payload' => base64_encode(serialize([])),
            'last_activity' => now()->timestamp,
        ]);

        $response = $this
            ->actingAs($worker)
            ->get(route('user.export'));

        $response->assertOk();

        $download = $response->baseResponse;
        $path = $download->getFile()->getPathname();
        $json = json_decode((string) file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);

        $this->assertArrayHasKey('user', $json);
        $this->assertArrayHasKey('worker_profile', $json);
        $this->assertArrayHasKey('applications', $json);
        $this->assertArrayHasKey('messages', $json);
        $this->assertArrayHasKey('notifications', $json);
        $this->assertArrayHasKey('notification_preferences', $json);
        $this->assertArrayHasKey('consent_history', $json);
        $this->assertArrayHasKey('session_metadata', $json);
        $this->assertArrayHasKey('saved_jobs', $json);

        $this->assertSame('Ana', $json['worker_profile']['first_name']);
        $this->assertSame('I am interested in this role.', $json['messages']['application_messages'][0]);
        $this->assertSame('registration', $json['consent_history'][0]['source']);
    }
}
