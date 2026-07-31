<?php

namespace Tests\Feature;

use Tests\TestCase;

class MobileNavigationTest extends TestCase
{
    public function test_mobile_navigation_and_login_action_render_in_croatian_and_english(): void
    {
        foreach ([
            'hr' => ['Poslovi', 'Prijava'],
            'en' => ['Jobs', 'Login'],
        ] as $locale => [$jobsLabel, $loginLabel]) {
            $this->withSession(['locale' => $locale])
                ->get(route('home'))
                ->assertOk()
                ->assertSee('data-cw-mobile-toggle', false)
                ->assertSee('data-cw-mobile-panel', false)
                ->assertSee('data-cw-track-click="mobile_login"', false)
                ->assertSee($jobsLabel)
                ->assertSee($loginLabel);
        }
    }
}
