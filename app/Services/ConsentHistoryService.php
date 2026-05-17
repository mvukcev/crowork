<?php

namespace App\Services;

use App\Models\ConsentHistory;
use App\Models\User;
use Illuminate\Http\Request;

class ConsentHistoryService
{
    public const TYPE_TERMS = 'terms';
    public const TYPE_PRIVACY = 'privacy_policy';

    public function __construct(
        private readonly ConsentVersionService $consentVersionService,
    ) {
    }

    public function recordRegistrationConsents(User $user, Request $request): void
    {
        $acceptedAt = now();
        $termsVersion = $this->consentVersionService->currentTermsVersion();
        $termsHash = $this->consentVersionService->currentTermsHash();
        $privacyVersion = $this->consentVersionService->currentPrivacyVersion();
        $privacyHash = $this->consentVersionService->currentPrivacyHash();

        ConsentHistory::query()->create([
            'user_id' => $user->id,
            'consent_type' => self::TYPE_TERMS,
            'consent_version' => $termsVersion,
            'consent_version_hash' => $termsHash,
            'source' => 'registration',
            'given' => true,
            'accepted_at' => $acceptedAt,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        ConsentHistory::query()->create([
            'user_id' => $user->id,
            'consent_type' => self::TYPE_PRIVACY,
            'consent_version' => $privacyVersion,
            'consent_version_hash' => $privacyHash,
            'source' => 'registration',
            'given' => true,
            'accepted_at' => $acceptedAt,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);
    }
}
