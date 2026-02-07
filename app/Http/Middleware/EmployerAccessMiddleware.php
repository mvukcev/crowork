<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmployerAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || !$user->isEmployer()) {
            abort(403, 'Access denied. Employer role required.');
        }

        // Check if employer profile exists and is approved
        if (!$user->employer || !$user->employer->approved_at) {
            abort(403, 'Access denied. Your employer account is not yet approved.');
        }

        return $next($request);
    }
}
