<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecureHeaders
{
    /**
     * Add comprehensive security headers suitable for production.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $isLocalEnvironment = app()->environment(['local', 'testing']);
        $viteScriptSources = $isLocalEnvironment
            ? " 'self' 'unsafe-inline' 'unsafe-eval' cdn.jsdelivr.net http://127.0.0.1:5173 http://localhost:5173"
            : " 'self' 'unsafe-inline' 'unsafe-eval' cdn.jsdelivr.net";
        $viteStyleSources = $isLocalEnvironment
            ? " 'self' 'unsafe-inline' cdn.jsdelivr.net http://127.0.0.1:5173 http://localhost:5173"
            : " 'self' 'unsafe-inline' cdn.jsdelivr.net";
        $viteConnectSources = $isLocalEnvironment
            ? " 'self' api.github.com http://127.0.0.1:5173 http://localhost:5173 ws://127.0.0.1:5173 ws://localhost:5173"
            : " 'self' api.github.com";

        // Prevent clickjacking attacks
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Referrer policy for privacy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Restrict browser features
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), accelerometer=()');

        // Cross-origin opener policy
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');

        // Content Security Policy - restrictive by default
        // Inline scripts disabled except for nonces; external scripts from same origin
        $csp = [
            "default-src 'self'",
            "script-src{$viteScriptSources}",
            "style-src{$viteStyleSources} fonts.googleapis.com",
            "font-src 'self' fonts.gstatic.com cdn.jsdelivr.net",
            "img-src 'self' data: https:",
            "media-src 'self'",
            "connect-src{$viteConnectSources}",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "form-action 'self'",
        ];
        $response->headers->set('Content-Security-Policy', implode('; ', $csp));

        // HSTS for HTTPS connections
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=63072000; includeSubDomains; preload');
        }

        return $response;
    }
}