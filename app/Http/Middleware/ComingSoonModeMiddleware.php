<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ComingSoonModeMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! $this->isComingSoonEnabled()) {
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
            'login',
            'register',
            'logout',
            'admin',
            'admin/*',
            'build/*',
            'storage/*',
            'favicon.ico',
            'robots.txt'
        );
    }

    private function isComingSoonEnabled(): bool
    {
        try {
            // Only use DB setting if a record is explicitly stored; otherwise fall back to ENV.
            // This prevents a DB default of false from silently overriding COMING_SOON_ENABLED=true in ENV.
            $record = \App\Models\Setting::query()->where('key', 'coming_soon_enabled')->first();

            if ($record !== null) {
                return \App\Models\Setting::getBool('coming_soon_enabled', false);
            }

            // No DB record – honour ENV / config value
            return (bool) config('crowork.coming_soon.enabled', false);
        } catch (\Throwable) {
            return (bool) config('crowork.coming_soon.enabled', false);
        }
    }
}
