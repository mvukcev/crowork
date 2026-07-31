<?php

namespace Tests\Feature;

use App\Filament\Admin\Resources\JobTranslationTrackerResource;
use App\Jobs\TranslateJobPosting;
use App\Models\Employer;
use App\Models\Job;
use App\Models\JobTranslation;
use App\Models\Setting;
use App\Models\User;
use App\Services\Translation\AzureTranslator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class JobTranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_native_and_hzz_jobs_are_dispatched_to_separate_priority_queues(): void
    {
        Queue::fake();
        config()->set('services.azure_translator.enabled', true);

        $native = $this->createJob();
        $hzz = $this->createJob([
            'slug' => 'hzz-oglas',
            'source_system' => 'hzz',
            'hzz_is_official' => true,
        ]);

        Queue::assertPushedOn('translations-native', TranslateJobPosting::class);
        Queue::assertPushedOn('translations-hzz', TranslateJobPosting::class);
        Queue::assertPushed(TranslateJobPosting::class, function (TranslateJobPosting $queued) use ($native): bool {
            return $queued->jobId === $native->id;
        });
        Queue::assertPushed(TranslateJobPosting::class, function (TranslateJobPosting $queued) use ($hzz): bool {
            return $queued->jobId === $hzz->id;
        });
    }

    public function test_admin_setting_disables_automatic_translation_dispatch(): void
    {
        Queue::fake();
        config()->set('services.azure_translator.enabled', true);
        Setting::setValue('job_translation_enabled', false);

        $this->createJob();

        Queue::assertNothingPushed();
    }

    public function test_admin_can_open_job_translation_tracker(): void
    {
        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);
        $this->createJob();

        $this->actingAs($admin)
            ->get(JobTranslationTrackerResource::getUrl('index'))
            ->assertOk()
            ->assertSee('Job Translation Tracker')
            ->assertSee('Not Queued');
    }

    public function test_azure_client_translates_all_fields_in_one_batch(): void
    {
        config()->set('services.azure_translator', [
            'enabled' => true,
            'key' => 'test-key',
            'region' => 'westeurope',
            'endpoint' => 'https://api.cognitive.microsofttranslator.com',
        ]);

        Http::fake([
            'api.cognitive.microsofttranslator.com/*' => Http::response([
                ['translations' => [['text' => 'Nurse', 'to' => 'en']]],
                ['translations' => [['text' => '<p>Job description</p>', 'to' => 'en']]],
            ]),
        ]);

        $translated = app(AzureTranslator::class)->translate([
            'title' => 'Medicinska sestra',
            'description' => '<p>Opis posla</p>',
        ]);

        $this->assertSame('Nurse', $translated['title']);
        $this->assertSame('<p>Job description</p>', $translated['description']);

        Http::assertSent(fn ($request): bool => $request->hasHeader('Ocp-Apim-Subscription-Key', 'test-key')
            && str_contains($request->url(), 'from=hr')
            && str_contains($request->url(), 'to=en'));
    }

    public function test_english_pages_use_saved_translation_and_show_disclaimer(): void
    {
        $job = $this->createJob();

        JobTranslation::query()->create([
            'job_id' => $job->id,
            'locale' => 'en',
            'source_locale' => 'hr',
            'provider' => 'azure',
            'status' => 'completed',
            'source_hash' => $job->translationSourceHash(),
            'content' => [
                'title' => 'Registered nurse',
                'description' => '<p>English job description.</p>',
            ],
            'translated_at' => now(),
        ]);

        $this->withSession(['locale' => 'en'])
            ->get(route('home'))
            ->assertOk()
            ->assertSee('Registered nurse');

        $this->withSession(['locale' => 'en'])
            ->get(route('jobs.show', $job))
            ->assertOk()
            ->assertSee('Registered nurse')
            ->assertSee('English job description.')
            ->assertSee('translated automatically from Croatian')
            ->assertSee('View the original Croatian listing.');

        $this->withSession(['locale' => 'hr'])
            ->get(route('jobs.show', $job))
            ->assertOk()
            ->assertSee('Medicinska sestra')
            ->assertDontSee('translated automatically from Croatian');
    }

    private function createJob(array $overrides = []): Job
    {
        $user = User::factory()->create(['role' => User::ROLE_EMPLOYER]);
        $employer = Employer::query()->create([
            'user_id' => $user->id,
            'company_name' => 'Test d.o.o.',
            'approved_at' => now(),
        ]);

        return Job::query()->create(array_merge([
            'employer_id' => $employer->id,
            'created_by_user_id' => $user->id,
            'title' => 'Medicinska sestra',
            'slug' => 'medicinska-sestra-' . uniqid(),
            'description' => '<p>Hrvatski opis posla.</p>',
            'location_city' => 'Zagreb',
            'category' => 'Zdravstvo',
            'contract_type' => 'full_time',
            'status' => 'published',
            'published_at' => now(),
            'expires_at' => now()->addWeek(),
            'is_featured' => true,
        ], $overrides));
    }
}
