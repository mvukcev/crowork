<?php

namespace App\Http\Controllers;

use App\Models\WorkerProfile;
use App\Services\AccountDeletionService;
use App\Services\ConsentVersionService;
use App\Services\CookieConsentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkerPrivacyController extends Controller
{
    public function show(Request $request): View
    {
        $this->ensureWorker($request);

        $user = $request->user();
        $profile = WorkerProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => '',
                'last_name' => '',
                'nationality_country_code' => '',
                'birth_year' => 1940,
                'skills' => [],
            ]
        );

        $latestDeletionRequest = $user->accountDeletionRequests()
            ->latest('id')
            ->first();

        $consentService = app(CookieConsentService::class);
        $trackingConsent = $consentService->resolveState($request, $user);
        $consentVersionService = app(ConsentVersionService::class);
        $legalStatus = $consentVersionService->complianceStatus($user);
        $currentLegalVersions = $consentVersionService->currentVersionsAndHashes();
        $legalConsentHistory = $user->consentHistories()
            ->whereIn('consent_type', [
                ConsentVersionService::TYPE_TERMS,
                ConsentVersionService::TYPE_TERMS_LEGACY,
                ConsentVersionService::TYPE_PRIVACY,
            ])
            ->latest('accepted_at')
            ->latest('id')
            ->limit(10)
            ->get();

        return view('worker.privacy', [
            'user' => $user,
            'profile' => $profile,
            'visibilityOptions' => WorkerProfile::visibilityOptions(),
            'latestDeletionRequest' => $latestDeletionRequest,
            'trackingConsent' => $trackingConsent,
            'legalStatus' => $legalStatus,
            'currentLegalVersions' => $currentLegalVersions,
            'legalConsentHistory' => $legalConsentHistory,
        ]);
    }

    public function updateVisibility(Request $request): RedirectResponse
    {
        $this->ensureWorker($request);

        $validated = $request->validate([
            'profile_visibility' => ['required', 'in:' . implode(',', array_keys(WorkerProfile::visibilityOptions()))],
        ]);

        $profile = WorkerProfile::firstOrCreate(
            ['user_id' => $request->user()->id],
            [
                'first_name' => '',
                'last_name' => '',
                'nationality_country_code' => '',
                'birth_year' => 1940,
                'skills' => [],
            ]
        );

        $profile->update([
            'profile_visibility' => $validated['profile_visibility'],
        ]);

        return redirect()
            ->route('worker.privacy.show')
            ->with('success', 'Profile visibility updated.');
    }

    public function requestDeletion(Request $request, AccountDeletionService $deletionService): RedirectResponse
    {
        $this->ensureWorker($request);

        $request->validate([
            'password' => ['required', 'current_password'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $deletionService->requestDeletion(
            $request->user(),
            $request->input('reason')
        );

        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('access.show')
            ->with('status', 'account-deletion-requested');
    }

    public function updateTrackingConsent(Request $request, CookieConsentService $consentService): RedirectResponse
    {
        $this->ensureWorker($request);

        $analytics = $request->boolean('consent_analytics');
        $marketing = $request->boolean('consent_marketing');
        $choice = $consentService->resolveChoice($analytics, $marketing);

        $consentService->persistConsent(
            $request,
            $analytics,
            $marketing,
            $choice,
            CookieConsentService::SOURCE_WORKER_PRIVACY,
            $request->user(),
        );

        $response = redirect()
            ->route('worker.privacy.show')
            ->with('success', 'Tracking preferences updated.');

        foreach ($consentService->buildConsentCookies($analytics, $marketing, $choice) as $cookie) {
            $response->headers->setCookie($cookie);
        }

        return $response;
    }

    private function ensureWorker(Request $request): void
    {
        if (! $request->user()?->isWorker()) {
            abort(403);
        }
    }
}
