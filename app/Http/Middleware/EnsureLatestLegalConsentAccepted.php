<?php

namespace App\Http\Middleware;

use App\Services\ConsentVersionService;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureLatestLegalConsentAccepted
{
    public function __construct(
        private readonly ConsentVersionService $consentVersionService,
    ) {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return $next($request);
        }

        if ($user->role === User::ROLE_ADMIN) {
            return $next($request);
        }

        $routeName = (string) optional($request->route())->getName();

        if ($this->isExemptRoute($request, $routeName)) {
            return $next($request);
        }

        if (! $this->consentVersionService->requiresReacceptance($user)) {
            return $next($request);
        }

        if ($request->isMethod('GET')) {
            $request->session()->put('url.intended', $request->fullUrl());
        }

        return redirect()->route('legal.reaccept.show');
    }

    private function isExemptRoute(Request $request, string $routeName): bool
    {
        if (str_starts_with($routeName, 'filament.admin.')) {
            return true;
        }

        if ($routeName === '') {
            return false;
        }

        $exempt = [
            'logout',
            'legal.reaccept.show',
            'legal.reaccept.store',
            'privacy',
            'privacy-policy',
            'terms',
            'terms-of-service',
            'cookies',
            'cookie-policy',
            'worker.privacy.show',
            'worker.privacy.request-deletion',
            'user.export',
            'profile.destroy',
            'verification.notice',
            'verification.send',
            'verification.verify',
        ];

        if (in_array($routeName, $exempt, true)) {
            return true;
        }

        if (str_starts_with($routeName, 'worker.privacy.')) {
            return true;
        }

        if (str_ends_with($routeName, '.auth.logout')) {
            return true;
        }

        return $request->is('legal/reaccept');
    }
}
