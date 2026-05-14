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
        $middleware->web(append: [
            \App\Http\Middleware\SecureHeaders::class,
            \App\Http\Middleware\ComingSoonModeMiddleware::class,
        ]);

        $middleware->alias([
            'employer.approved' => \App\Http\Middleware\EnsureEmployerIsApproved::class,
            'admin.access' => \App\Http\Middleware\AdminAccessMiddleware::class,
            'impersonation.readonly' => \App\Http\Middleware\PreventImpersonatedWrites::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
