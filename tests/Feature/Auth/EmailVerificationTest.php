<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_email_verification_screen_is_not_available_in_otp_flow(): void
    {
        $user = User::factory()->unverified()->create();

        $response = $this->actingAs($user)->get('/verify-email');

        $response->assertNotFound();
    }

    public function test_verification_verify_route_is_not_registered(): void
    {
        $this->assertFalse(Route::has('verification.verify'));
    }

    public function test_unverified_user_stays_unverified_without_access_verification_flow(): void
    {
        $user = User::factory()->unverified()->create();

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
