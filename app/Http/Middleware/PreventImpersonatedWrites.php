<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventImpersonatedWrites
{
    /**
     * Block write actions while an admin is impersonating an employer account.
     * This keeps impersonation sessions read-only and avoids destructive changes.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isImpersonating = session()->has('impersonation_original_admin_id');

        if (! $isImpersonating) {
            return $next($request);
        }

        if ($request->isMethodSafe()) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Write actions are disabled while impersonating. Return to admin to continue.',
            ], 423);
        }

        return redirect()
            ->back()
            ->with('warning', 'Changes are blocked while impersonating. Use Return to Admin first.');
    }
}
