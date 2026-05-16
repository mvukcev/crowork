<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdminPanelSessionIsPrivileged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && ! $user->isAdmin() && ! $user->isMod()) {
            Log::info('Clearing non-admin session before admin panel access.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role,
                'path' => $request->path(),
            ]);

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if (! $request->routeIs('filament.admin.auth.login')) {
                return redirect()->to(url('/admin/login'));
            }
        }

        return $next($request);
    }
}