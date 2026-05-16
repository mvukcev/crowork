<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpFoundation\Response;

class AdminAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user || (!$user->isAdmin() && !$user->isMod())) {
            Log::warning('Admin panel access denied.', [
                'panel' => 'admin',
                'user_id' => $user?->id,
                'email' => $user?->email,
                'role' => $user?->role,
                'path' => $request->path(),
                'ip' => $request->ip(),
            ]);

            // If a non-admin is already authenticated, clear session and return to admin login.
            if ($user) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $loginUrl = Route::has('filament.admin.auth.login')
                    ? route('filament.admin.auth.login')
                    : url('/admin/login');

                return redirect()->to($loginUrl);
            }

            abort(403, 'Access denied. Admin or moderator role required.');
        }

        return $next($request);
    }
}
