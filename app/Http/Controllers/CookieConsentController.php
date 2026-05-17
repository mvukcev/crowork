<?php

namespace App\Http\Controllers;

use App\Services\CookieConsentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CookieConsentController extends Controller
{
    public function update(Request $request, CookieConsentService $consentService): JsonResponse
    {
        $validated = $request->validate([
            'analytics' => ['required', 'boolean'],
            'marketing' => ['required', 'boolean'],
            'choice' => ['nullable', 'in:all,required,custom'],
            'source' => ['nullable', 'string', 'max:64'],
        ]);

        $analytics = (bool) $validated['analytics'];
        $marketing = (bool) $validated['marketing'];
        $choice = (string) ($validated['choice'] ?? $consentService->resolveChoice($analytics, $marketing));
        $source = (string) ($validated['source'] ?? CookieConsentService::SOURCE_BANNER);

        $consentService->persistConsent(
            $request,
            $analytics,
            $marketing,
            $choice,
            $source,
            $request->user(),
        );

        $response = response()->json([
            'saved' => true,
            'consent' => [
                'analytics' => $analytics,
                'marketing' => $marketing,
                'choice' => $choice,
            ],
        ]);

        foreach ($consentService->buildConsentCookies($analytics, $marketing, $choice) as $cookie) {
            $response->headers->setCookie($cookie);
        }

        return $response;
    }
}
