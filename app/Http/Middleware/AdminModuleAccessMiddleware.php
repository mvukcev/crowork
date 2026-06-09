<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminModuleAccessMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || (! $user->isAdmin() && ! $user->isMod())) {
            abort(403, 'Admin or moderator access required.');
        }

        $module = User::resolveAdminModuleFromRouteName($request->route()?->getName());

        if ($module !== null && ! $user->canAccessAdminModule($module)) {
            abort(403, 'You do not have access to this admin module.');
        }

        return $next($request);
    }
}
