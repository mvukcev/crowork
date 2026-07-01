<?php

namespace Tests\Feature;

use App\Models\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class JobPreviewLinkTest extends TestCase
{
    use RefreshDatabase;

    public function test_unpublished_job_has_persistent_shared_preview_link(): void
    {
        $job = Job::query()->create([
            'title' => 'Preview QA Engineer',
            'description' => 'Draft content for internal approval.',
            'location_city' => 'Zagreb',
            'category' => 'IT',
            'status' => 'draft',
        ]);

        $this->assertNotNull($job->preview_token);
        $this->assertSame(64, strlen((string) $job->preview_token));

        $this->get(route('jobs.show', $job))->assertNotFound();

        $this->get(route('jobs.preview.shared', ['token' => $job->preview_token]))
            ->assertOk()
            ->assertSee($job->title);
    }
}
