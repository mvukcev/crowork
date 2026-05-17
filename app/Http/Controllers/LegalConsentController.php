<?php

namespace App\Http\Controllers;

use App\Services\ConsentVersionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LegalConsentController extends Controller
{
    public function __construct(
        private readonly ConsentVersionService $consentVersionService,
    ) {
    }

    public function show(Request $request): View
    {
        $user = $request->user();
        abort_unless($user !== null, 403);

        return view('legal.reaccept', [
            'missingConsents' => $this->consentVersionService->missingRequiredConsents($user),
            'current' => $this->consentVersionService->currentVersionsAndHashes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user !== null, 403);

        $request->validate([
            'accept_terms' => ['accepted'],
            'accept_privacy' => ['accepted'],
        ]);

        $this->consentVersionService->recordLatestTermsAcceptance($user, $request, 'reacceptance');
        $this->consentVersionService->recordLatestPrivacyAcceptance($user, $request, 'reacceptance');

        return redirect()->intended(route('dashboard'))
            ->with('success', 'Legal consents updated successfully.');
    }
}
