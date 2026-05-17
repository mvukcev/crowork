<?php

namespace Tests\Unit;

use App\Models\ConsentHistory;
use App\Models\Setting;
use App\Models\User;
use App\Services\ConsentConfigService;
use App\Services\CookieConsentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsentConfigServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_marketing_consent_uses_latest_history_when_no_cookie_is_present(): void
    {
        Setting::setValue('consent_required', true);

        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);

        ConsentHistory::query()->create([
            'user_id' => $worker->id,
            'consent_type' => CookieConsentService::CONSENT_TYPE_MARKETING,
            'consent_version' => '2026-05-17',
            'source' => 'worker_privacy:required',
            'given' => false,
            'accepted_at' => now()->subMinute(),
        ]);

        ConsentHistory::query()->create([
            'user_id' => $worker->id,
            'consent_type' => CookieConsentService::CONSENT_TYPE_MARKETING,
            'consent_version' => '2026-05-17',
            'source' => 'worker_privacy:all',
            'given' => true,
            'accepted_at' => now(),
        ]);

        $this->assertTrue(ConsentConfigService::hasMarketingConsent(null, $worker));
    }

    public function test_analytics_consent_is_false_when_required_and_no_cookie_or_history(): void
    {
        Setting::setValue('consent_required', true);

        $worker = User::factory()->create(['role' => User::ROLE_WORKER]);

        $this->assertFalse(ConsentConfigService::hasAnalyticsConsent(null, $worker));
    }
}
