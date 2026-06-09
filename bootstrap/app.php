<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $trustedProxies = env('TRUSTED_PROXIES');
        if (is_string($trustedProxies) && trim($trustedProxies) !== '') {
            $middleware->trustProxies(at: array_map('trim', explode(',', $trustedProxies)));
        }

        $trustedHosts = env('TRUSTED_HOSTS');
        if (is_string($trustedHosts) && trim($trustedHosts) !== '') {
            $middleware->trustHosts(at: array_map('trim', explode(',', $trustedHosts)), subdomains: true);
        }

        $middleware->web(append: [
            \App\Http\Middleware\ForceHttpsInProduction::class,
            \App\Http\Middleware\SecureHeaders::class,
            \App\Http\Middleware\ComingSoonModeMiddleware::class,
            \App\Http\Middleware\SetFrontendLocale::class,
        ]);

        $middleware->alias([
            'employer.approved' => \App\Http\Middleware\EnsureEmployerIsApproved::class,
            'admin.access' => \App\Http\Middleware\AdminAccessMiddleware::class,
            'admin.modules' => \App\Http\Middleware\AdminModuleAccessMiddleware::class,
            'admin.strict' => \App\Http\Middleware\EnsureStrictAdminRole::class,
            'impersonation.readonly' => \App\Http\Middleware\PreventImpersonatedWrites::class,
            'legal.consent' => \App\Http\Middleware\EnsureLatestLegalConsentAccepted::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
