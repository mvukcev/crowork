<?php

namespace Tests\Feature;

use App\Models\Employer;
use App\Models\HzzJobAnalyticsEvent;
use App\Models\Job;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\WorkerProfile;
use App\Http\Middleware\EnsureLatestLegalConsentAccepted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class HzzApplyFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_hzz_email_apply_stays_in_crowork_and_saves_submission_metadata(): void
    {
        $this->withoutMiddleware(EnsureLatestLegalConsentAccepted::class);
        Mail::fake();
        Storage::fake('local');

        [$worker, $job] = $this->seedWorkerAndHzzJob('apply@hotel-example.hr', 'https://hzz.hr/oglas/123');

        $response = $this->actingAs($worker)->post(route('jobs.apply.store', $job), [
            'cv_choice' => 'upload',
            'cv_file' => UploadedFile::fake()->create('cv-test.pdf', 120, 'application/pdf'),
            'cover_letter_mode' => 'preset',
            'cover_letter_preset' => 'standard',
            'consent' => '1',
        ]);

        $response->assertRedirect(route('jobs.show', $job));

        $application = JobApplication::query()->firstOrFail();
        $this->assertSame(JobApplication::CHANNEL_HZZ_EMAIL, $application->apply_channel);
        $this->assertSame(JobApplication::SUBMISSION_SENT, $application->submission_status);
        $this->assertNotNull($application->submitted_at);
        $this->assertNotNull($application->cv_file_path);
        Storage::disk('local')->assertExists((string) $application->cv_file_path);

        $this->assertDatabaseHas('hzz_job_analytics_events', [
            'job_id' => $job->id,
            'event_type' => HzzJobAnalyticsEvent::EVENT_APPLICATION_SENT,
        ]);
    }

    public function test_hzz_external_open_creates_application_record_and_tracks_event(): void
    {
        $this->withoutMiddleware(EnsureLatestLegalConsentAccepted::class);

        [$worker, $job] = $this->seedWorkerAndHzzJob(null, 'https://hzz.hr/oglas/999');

        $response = $this->actingAs($worker)->get(route('jobs.hzz.open', $job));

        $response->assertRedirect('https://hzz.hr/oglas/999');

        $this->assertDatabaseHas('job_applications', [
            'job_id' => $job->id,
            'worker_id' => $worker->id,
            'apply_channel' => JobApplication::CHANNEL_HZZ_EXTERNAL,
        ]);

        $this->assertDatabaseHas('hzz_job_analytics_events', [
            'job_id' => $job->id,
            'event_type' => HzzJobAnalyticsEvent::EVENT_EXTERNAL_OPEN,
        ]);
    }

    public function test_hzz_primary_cta_click_endpoint_tracks_event(): void
    {
        $this->withoutMiddleware(EnsureLatestLegalConsentAccepted::class);

        [$worker, $job] = $this->seedWorkerAndHzzJob('apply@hotel-example.hr', 'https://hzz.hr/oglas/123');

        $response = $this->actingAs($worker)->post(route('jobs.hzz.cta-click', $job));

        $response->assertNoContent();

        $this->assertDatabaseHas('hzz_job_analytics_events', [
            'job_id' => $job->id,
            'event_type' => HzzJobAnalyticsEvent::EVENT_CTA_CLICK,
        ]);
    }

    /**
     * @return array{0: User, 1: Job}
     */
    private function seedWorkerAndHzzJob(?string $applyEmail, string $sourceUrl): array
    {
        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);

        WorkerProfile::query()->create([
            'user_id' => $worker->id,
            'first_name' => 'Ana',
            'last_name' => 'Horvat',
            'nationality_country_code' => 'HR',
            'birth_year' => 1992,
            'current_city' => 'Split',
            'skills' => ['Hospitality'],
        ]);

        $employerUser = User::factory()->create(['role' => User::ROLE_EMPLOYER]);
        $employer = Employer::query()->create([
            'user_id' => $employerUser->id,
            'approved_at' => now(),
            'company_name' => 'Hotel Partner',
            'city' => 'Split',
        ]);

        $job = Job::query()->create([
            'employer_id' => $employer->id,
            'created_by_user_id' => $employerUser->id,
            'title' => 'HZZ test listing',
            'slug' => 'hzz-test-listing-' . Str::lower(Str::random(8)),
            'description' => 'Imported from HZZ.',
            'location_city' => 'Split',
            'category' => 'Hospitality',
            'status' => 'published',
            'published_at' => now()->subHour(),
            'expires_at' => now()->addDays(10),
            'source_system' => 'hzz',
            'hzz_is_official' => true,
            'hzz_apply_email' => $applyEmail,
            'source_url' => $sourceUrl,
            'hzz_apply_url' => $sourceUrl,
        ]);

        return [$worker, $job];
    }
}
