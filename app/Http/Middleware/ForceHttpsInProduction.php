<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpsInProduction
{
    /**
     * Enforce HTTPS in production environment.
     * 
     * Respects X-Forwarded-Proto header from Cloudflare/reverse proxies.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // In production, enforce HTTPS
        if (app()->environment('production') && !$request->isSecure() && config('app.url') && str_starts_with(config('app.url'), 'https://')) {
            $url = 'https://' . $request->getHost() . $request->getRequestUri();
            return redirect($url, 301);
        }

        return $next($request);
    }
}
