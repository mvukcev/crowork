<?php

namespace Tests\Feature;

use App\Http\Controllers\ContentPageController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class LegalCanonicalRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_canonical_public_legal_routes_return_ok(): void
    {
        $this->get('/privacy')->assertOk();
        $this->get('/terms')->assertOk();
        $this->get('/cookies')->assertOk();
    }

    public function test_canonical_public_legal_routes_use_content_page_controller_path(): void
    {
        $privacyRoute = Route::getRoutes()->match(Request::create('/privacy', 'GET'));
        $termsRoute = Route::getRoutes()->match(Request::create('/terms', 'GET'));
        $cookiesRoute = Route::getRoutes()->match(Request::create('/cookies', 'GET'));

        $this->assertSame(ContentPageController::class.'@show', $privacyRoute->getActionName());
        $this->assertSame(ContentPageController::class.'@show', $termsRoute->getActionName());
        $this->assertSame(ContentPageController::class.'@show', $cookiesRoute->getActionName());
    }

    public function test_alias_legal_routes_redirect_to_canonical_urls(): void
    {
        $this->get('/privacy-policy')->assertRedirect(route('privacy'));
        $this->get('/terms-of-service')->assertRedirect(route('terms'));
        $this->get('/cookie-policy')->assertRedirect(route('cookies'));
    }

    public function test_content_preview_route_remains_admin_only(): void
    {
        $this->get('/content/privacy/preview/en')
            ->assertRedirect('/login');

        $admin = User::factory()->create(['role' => User::ROLE_ADMIN]);

        $this->actingAs($admin)
            ->get('/content/privacy/preview/en')
            ->assertNotFound();
    }

    public function test_legal_reaccept_routes_remain_unchanged(): void
    {
        $show = Route::getRoutes()->getByName('legal.reaccept.show');
        $store = Route::getRoutes()->getByName('legal.reaccept.store');

        $this->assertNotNull($show);
        $this->assertNotNull($store);

        $this->assertSame('legal/reaccept', $show->uri());
        $this->assertSame('legal/reaccept', $store->uri());

        $this->assertContains('GET', $show->methods());
        $this->assertContains('POST', $store->methods());

        $this->assertContains('auth', $show->gatherMiddleware());
        $this->assertContains('auth', $store->gatherMiddleware());
    }
}
