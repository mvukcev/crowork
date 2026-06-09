<?php

namespace Tests\Feature;

use App\Models\MarketingImageOverride;
use App\Services\MarketingImageService;
use App\Support\MarketingImageSlots;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MarketingImageSystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_registry_contains_all_expected_slot_keys(): void
    {
        $expected = [
            'home.hero',
            'home.employer_workflow',
            'home.candidate_opportunity',
            'resources.guide_01',
            'resources.guide_02',
            'resources.guide_03',
            'resources.guide_04',
            'resources.guide_05',
            'resources.guide_06',
            'resources.relocation_path',
            'resources.life_work',
            'resources.faq_help',
            'about.fragmented_work',
            'about.workers_card',
            'about.employers_card',
            'about.croatia_modern_work',
            'about.bottom_01',
            'about.bottom_02',
            'for_employers.hero_dashboard',
            'for_employers.hero_onboarding',
            'for_employers.hero_pipeline',
            'for_employers.complexity',
            'for_employers.better_outcomes',
            'for_employers.platform_01',
            'for_employers.platform_02',
            'for_employers.platform_03',
            'for_employers.platform_04',
            'for_employers.platform_05',
            'for_employers.extra_01',
            'social.og_default',
        ];

        $this->assertEqualsCanonicalizing($expected, MarketingImageSlots::keys());
    }

    public function test_marketing_image_url_returns_fallback_when_no_override_exists(): void
    {
        $url = app(MarketingImageService::class)->url('home.hero');

        $this->assertIsString($url);
        $this->assertStringContainsString('assets/placeholders/home/home-hero-1200x900.jpg', $url);
    }

    public function test_marketing_image_url_returns_storage_url_for_active_existing_override(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('marketing-images/home/hero.jpg', 'demo');

        MarketingImageOverride::query()->create([
            'key' => 'home.hero',
            'disk' => 'public',
            'path' => 'marketing-images/home/hero.jpg',
            'is_active' => true,
        ]);

        $service = app(MarketingImageService::class);
        $service->flushCache();

        $this->assertSame(
            url(Storage::disk('public')->url('marketing-images/home/hero.jpg')),
            $service->url('home.hero')
        );
    }

    public function test_inactive_override_falls_back_to_registry_path(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('marketing-images/home/hero.jpg', 'demo');

        MarketingImageOverride::query()->create([
            'key' => 'home.hero',
            'disk' => 'public',
            'path' => 'marketing-images/home/hero.jpg',
            'is_active' => false,
        ]);

        $service = app(MarketingImageService::class);
        $service->flushCache();

        $url = $service->url('home.hero');

        $this->assertIsString($url);
        $this->assertStringContainsString('assets/placeholders/home/home-hero-1200x900.jpg', $url);
    }

    public function test_missing_file_falls_back_to_registry_path(): void
    {
        Storage::fake('public');

        MarketingImageOverride::query()->create([
            'key' => 'home.hero',
            'disk' => 'public',
            'path' => 'marketing-images/home/missing.jpg',
            'is_active' => true,
        ]);

        $service = app(MarketingImageService::class);
        $service->flushCache();

        $url = $service->url('home.hero');

        $this->assertIsString($url);
        $this->assertStringContainsString('assets/placeholders/home/home-hero-1200x900.jpg', $url);
    }

    public function test_invalid_key_returns_safe_values(): void
    {
        $service = app(MarketingImageService::class);

        $this->assertNull($service->url('invalid.slot'));
        $this->assertSame('', $service->alt('invalid.slot'));
    }

    public function test_all_marketing_image_url_keys_in_target_views_exist_in_registry(): void
    {
        $files = [
            resource_path('views/home.blade.php'),
            resource_path('views/pages/resources/index.blade.php'),
            resource_path('views/pages/about.blade.php'),
            resource_path('views/pages/for-employers.blade.php'),
            resource_path('views/layouts/app.blade.php'),
        ];

        $keys = [];

        foreach ($files as $file) {
            $contents = file_get_contents($file);
            if (! is_string($contents)) {
                continue;
            }

            if (preg_match_all("/marketing_image_url\\('([^']+)'\\)/", $contents, $matches)) {
                $keys = array_merge($keys, $matches[1]);
            }
        }

        $keys = array_values(array_unique($keys));
        $registryKeys = MarketingImageSlots::keys();

        foreach ($keys as $key) {
            $this->assertContains($key, $registryKeys, sprintf('Missing registry key: %s', $key));
        }

        // Keys used through view variables should also stay in sync with registry.
        $expectedVariableDrivenKeys = [
            'resources.guide_01',
            'resources.guide_02',
            'resources.guide_03',
            'resources.guide_04',
            'resources.guide_05',
            'resources.guide_06',
            'for_employers.platform_01',
            'for_employers.platform_02',
            'for_employers.platform_03',
            'for_employers.platform_04',
            'for_employers.platform_05',
        ];

        foreach ($expectedVariableDrivenKeys as $key) {
            $this->assertContains($key, $registryKeys, sprintf('Missing registry key for variable map: %s', $key));
        }
    }
}
