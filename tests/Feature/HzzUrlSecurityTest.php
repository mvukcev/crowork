<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureLatestLegalConsentAccepted;
use App\Models\Employer;
use App\Models\Job;
use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class HzzUrlSecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_hzz_external_open_rejects_non_hzz_domain_redirect_targets(): void
    {
        $this->withoutMiddleware(EnsureLatestLegalConsentAccepted::class);

        [$worker, $job] = $this->seedWorkerAndHzzJob(
            null,
            'https://evil.example/phish',
            'https://evil.example/phish'
        );

        $response = $this->actingAs($worker)->get(route('jobs.hzz.open', $job));

        $response->assertRedirect(route('jobs.show', $job));
        $response->assertSessionHas('error');
    }

    public function test_hzz_import_command_rejects_non_hzz_feed_urls(): void
    {
        $this->artisan('crowork:hzz-import', [
            '--url' => 'https://evil.example/feed.xml',
            '--write' => true,
        ])
            ->expectsOutput('Invalid HZZ feed URL. Only official HZZ domains are allowed.')
            ->assertFailed();
    }

    /**
     * @return array{0: User, 1: Job}
     */
    private function seedWorkerAndHzzJob(?string $applyEmail, string $sourceUrl, ?string $applyUrl = null): array
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
            'title' => 'HZZ security listing',
            'slug' => 'hzz-security-listing-' . Str::lower(Str::random(8)),
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
            'hzz_apply_url' => $applyUrl,
        ]);

        return [$worker, $job];
    }
}
