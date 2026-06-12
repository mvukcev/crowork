<?php

namespace App\Http\Middleware;

use App\Models\AuditLog;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogAdminWriteActions
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if (! (bool) setting('audit_log_enabled', true)) {
            return $response;
        }

        $user = $request->user();
        if (! $user || (! $user->isAdmin() && ! $user->isMod())) {
            return $response;
        }

        if (! $this->isMutatingAdminAction($request)) {
            return $response;
        }

        if ($response->getStatusCode() >= 400) {
            return $response;
        }

        $calls = $this->extractLivewireCalls($request);
        $callsText = $calls !== [] ? implode(', ', $calls) : null;
        $description = $callsText
            ? 'Admin write action via Livewire: ' . $callsText
            : 'Admin write action';

        AuditLog::logAction(
            'admin_write_action',
            $user,
            null,
            null,
            [
                'method' => $request->method(),
                'path' => $request->path(),
                'route' => optional($request->route())->getName(),
                'calls' => $calls,
            ],
            $description,
        );

        return $response;
    }

    private function isMutatingAdminAction(Request $request): bool
    {
        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return false;
        }

        if ($request->is('admin*') || $request->routeIs('filament.admin.*')) {
            return true;
        }

        if (! $request->routeIs('livewire.update')) {
            return false;
        }

        $referer = (string) $request->headers->get('referer', '');
        if (! str_contains($referer, '/admin')) {
            return false;
        }

        return $this->extractLivewireCalls($request) !== [];
    }

    /**
     * @return array<int, string>
     */
    private function extractLivewireCalls(Request $request): array
    {
        $components = $request->input('components', []);
        if (! is_array($components)) {
            return [];
        }

        $writeCalls = [];

        foreach ($components as $component) {
            if (! is_array($component)) {
                continue;
            }

            $calls = $component['calls'] ?? [];
            if (! is_array($calls)) {
                continue;
            }

            foreach ($calls as $call) {
                if (! is_array($call)) {
                    continue;
                }

                $method = strtolower((string) ($call['method'] ?? ''));
                if ($method === '') {
                    continue;
                }

                if (preg_match('/save|create|delete|remove|update|attach|detach|sync|submit|publish|unpublish|retry|clear|run/', $method) === 1) {
                    $writeCalls[] = $method;
                }
            }
        }

        return array_values(array_unique($writeCalls));
    }
}
