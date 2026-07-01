<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertRedirect(route('access.show'));
    }

    public function test_direct_register_without_verified_email_session_is_rejected(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'account_type' => 'worker',
            'accept_terms' => '1',
            'accept_privacy' => '1',
        ]);

        $this->assertGuest();
        $response->assertRedirect(route('access.show'));
    }
}
