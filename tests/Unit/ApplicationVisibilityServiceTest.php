<?php

namespace Tests\Unit;

use App\Models\Employer;
use App\Models\User;
use App\Services\ApplicationVisibilityService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApplicationVisibilityServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_anonymous_visibility_keeps_structured_professional_sections_and_hides_identity(): void
    {
        $employerUser = User::factory()->create(['role' => User::ROLE_EMPLOYER]);

        $employer = Employer::query()->create([
            'user_id' => $employerUser->id,
            'company_name' => 'Demo Employer',
            'approved_at' => now(),
            'applications_visibility_override' => 'anonymous',
        ]);

        $snapshot = [
            'first_name' => 'Ana',
            'last_name' => 'Horvat',
            'email' => 'ana@gmail.com',
            'phone' => '+385123123',
            'nationality_country_code' => 'HR',
            'skills' => ['Laravel'],
            'structured_experiences' => [
                ['job_title' => 'Chef', 'company_name' => 'Hotel'],
            ],
            'structured_educations' => [
                ['institution' => 'School'],
            ],
            'structured_certifications' => [
                ['name' => 'Food Safety'],
            ],
        ];

        $service = app(ApplicationVisibilityService::class);
        $masked = $service->maskSnapshot($snapshot, $employer);

        $this->assertArrayNotHasKey('first_name', $masked);
        $this->assertArrayNotHasKey('last_name', $masked);
        $this->assertArrayNotHasKey('email', $masked);
        $this->assertArrayHasKey('structured_experiences', $masked);
        $this->assertArrayHasKey('structured_educations', $masked);
        $this->assertArrayHasKey('structured_certifications', $masked);
    }
}
