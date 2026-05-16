<?php

namespace App\Http\Middleware;

use Closure;
use App\Support\ComingSoonMode;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ComingSoonModeMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! ComingSoonMode::enabled()) {
            return $next($request);
        }

        if ($this->isAlwaysAllowedPath($request)) {
            return $next($request);
        }

        $user = $request->user();

        if ($user && ($user->isAdmin() || $user->isMod())) {
            return $next($request);
        }

        if ($request->session()->get(config('crowork.coming_soon.session_key', 'coming_soon_preview'), false)) {
            return $next($request);
        }

        return redirect()->route('coming-soon-preview.show');
    }

    /**
     * Paths that should bypass coming soon restrictions.
     */
    private function isAlwaysAllowedPath(Request $request): bool
    {
        return $request->is(
            'coming-soon-preview',
            'coming-soon-preview/*',
            'access',
            'access/*',
            'preferences/*',
            'login',
            'register',
            'logout',
            'admin',
            'admin/*',
            '_update-crowork',
            '_update-crowork/*',
            '_install-crowork',
            '_install-crowork/*',
            'assets',
            'assets/*',
            'build',
            'build/*',
            'livewire',
            'livewire/*',
            'filament',
            'filament/*',
            'storage/*',
            'favicon.ico',
            'robots.txt'
        );
    }
}
