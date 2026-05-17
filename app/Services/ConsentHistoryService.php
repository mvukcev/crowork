<?php

namespace App\Services;

use App\Models\ConsentHistory;
use App\Models\User;
use Illuminate\Http\Request;

class ConsentHistoryService
{
    public const TYPE_TERMS = 'terms_of_service';
    public const TYPE_PRIVACY = 'privacy_policy';

    public function recordRegistrationConsents(User $user, Request $request): void
    {
        $acceptedAt = now();
        $version = $this->consentVersion();

        ConsentHistory::query()->create([
            'user_id' => $user->id,
            'consent_type' => self::TYPE_TERMS,
            'consent_version' => $version,
            'source' => 'registration',
            'given' => true,
            'accepted_at' => $acceptedAt,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        ConsentHistory::query()->create([
            'user_id' => $user->id,
            'consent_type' => self::TYPE_PRIVACY,
            'consent_version' => $version,
            'source' => 'registration',
            'given' => true,
            'accepted_at' => $acceptedAt,
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);
    }

    private function consentVersion(): string
    {
        return (string) config('app.gdpr_consent_version', '2026-05-17');
    }
}
