<?php

namespace Tests\Feature;

use App\Models\ContentPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContentPageFallbackTest extends TestCase
{
    use RefreshDatabase;

    public function test_hr_locale_uses_hr_content_when_available(): void
    {
        ContentPage::query()->create([
            'slug' => 'privacy',
            'locale' => 'en',
            'title' => 'Privacy EN',
            'body' => '<p>EN Privacy Body</p>',
            'is_published' => true,
        ]);

        ContentPage::query()->create([
            'slug' => 'privacy',
            'locale' => 'hr',
            'title' => 'Privatnost HR',
            'body' => '<p>HR Privacy Body</p>',
            'is_published' => true,
        ]);

        $this->get('/privacy?lang=hr')
            ->assertOk()
            ->assertSee('Privatnost HR')
            ->assertSee('HR Privacy Body', false)
            ->assertDontSee('EN Privacy Body', false)
            ->assertSee('prose prose-sm', false);
    }

    public function test_hr_locale_falls_back_to_en_content_when_hr_is_missing(): void
    {
        ContentPage::query()->create([
            'slug' => 'terms',
            'locale' => 'en',
            'title' => 'Terms EN',
            'body' => '<p>EN Terms Body</p>',
            'is_published' => true,
        ]);

        $this->get('/terms?lang=hr')
            ->assertOk()
            ->assertSee('Terms EN')
            ->assertSee('EN Terms Body', false)
            ->assertSee('prose prose-sm', false);
    }

    public function test_safe_fallback_is_used_when_neither_current_locale_nor_en_content_exists(): void
    {
        $this->get('/cookies?lang=hr')
            ->assertOk()
            ->assertSee(__('legal_ui.content_unavailable_title', [], 'hr'))
            ->assertSee(__('legal_ui.content_unavailable_body', [], 'hr'))
            ->assertDontSee('Cookie Policy')
            ->assertSee('prose prose-sm', false);
    }

    public function test_canonical_legal_routes_still_return_ok_and_aliases_still_redirect(): void
    {
        $this->get('/privacy')->assertOk();
        $this->get('/terms')->assertOk();
        $this->get('/cookies')->assertOk();

        $this->get('/privacy-policy')->assertRedirect('/privacy');
        $this->get('/terms-of-service')->assertRedirect('/terms');
        $this->get('/cookie-policy')->assertRedirect('/cookies');
    }
}
