<?php

namespace App\Http\Controllers;

use App\Support\ComingSoonMode;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ComingSoonPreviewController extends Controller
{
    public function show(): View|RedirectResponse
    {
        if (! $this->isComingSoonEnabled()) {
            return redirect()->route('home');
        }

        return view('coming-soon.preview');
    }

    public function login(Request $request): RedirectResponse
    {
        if (! $this->isComingSoonEnabled()) {
            return redirect()->route('home');
        }

        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $expectedUsername = (string) config('crowork.coming_soon.demo_username', '');
        $expectedPassword = (string) config('crowork.coming_soon.demo_password', '');

        $usernameValid = hash_equals($expectedUsername, (string) $credentials['username']);
        $passwordValid = hash_equals($expectedPassword, (string) $credentials['password']);

        if (!$usernameValid || !$passwordValid) {
            return back()
                ->withErrors(['preview' => 'Invalid preview credentials.'])
                ->onlyInput('username');
        }

        $request->session()->put(config('crowork.coming_soon.session_key', 'coming_soon_preview'), true);
        $request->session()->regenerate();

        return redirect()->route('home');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget(config('crowork.coming_soon.session_key', 'coming_soon_preview'));

        return redirect()->route('coming-soon-preview.show');
    }

    private function isComingSoonEnabled(): bool
    {
        return ComingSoonMode::enabled();
    }
}
