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
        ], [
            'accept_terms.accepted' => __('legal_ui.reaccept.validation_terms_required'),
            'accept_privacy.accepted' => __('legal_ui.reaccept.validation_privacy_required'),
        ]);

        $this->consentVersionService->recordLatestTermsAcceptance($user, $request, 'reacceptance');
        $this->consentVersionService->recordLatestPrivacyAcceptance($user, $request, 'reacceptance');

        return redirect()->intended(route('dashboard'))
            ->with('success', __('legal_ui.reaccept.flash_updated'));
    }
}
