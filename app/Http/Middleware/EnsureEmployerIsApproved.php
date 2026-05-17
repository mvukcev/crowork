<?php

namespace App\Http\Middleware;

use App\Services\ApprovalService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployerIsApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only applies to authenticated users
        if (!auth()->check()) {
            return redirect('login');
        }

        // Only check if user is an employer
        if (auth()->user()->role !== 'employer') {
            return response('Unauthorized', 401);
        }

        // Check if email is verified
        if (!auth()->user()->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        // Check if employer is approved only when global policy requires it.
        $approvalService = app(ApprovalService::class);
        if (! $approvalService->requiresEmployerApproval()) {
            return $next($request);
        }

        // Check if employer is approved
        $employer = auth()->user()->employer;
        if (!$employer || !$employer->approved_at) {
            return redirect()->route('employer.pending-approval');
        }

        return $next($request);
    }
}
