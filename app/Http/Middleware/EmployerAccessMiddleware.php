<?php

namespace App\Http\Middleware;

use App\Services\ApprovalService;
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

        if (! $user->employer) {
            abort(403, 'Access denied. Employer profile not found.');
        }

        $approvalService = app(ApprovalService::class);
        if ($approvalService->requiresEmployerApproval() && !$user->employer->approved_at) {
            abort(403, 'Access denied. Your employer account is not yet approved.');
        }

        return $next($request);
    }
}
