<?php

namespace App\Services;

use App\Models\ConsentHistory;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class ConsentVersionService
{
    public const TYPE_TERMS = 'terms';
    public const TYPE_TERMS_LEGACY = 'terms_of_service';
    public const TYPE_PRIVACY = 'privacy_policy';

    public function currentTermsVersion(): string
    {
        return (string) Setting::getString('terms_version', '2026-05-terms-v1');
    }

    public function currentPrivacyVersion(): string
    {
        return (string) Setting::getString('privacy_policy_version', '2026-05-privacy-v1');
    }

    public function currentTermsHash(): string
    {
        $configured = Setting::getString('terms_hash');

        if (is_string($configured) && trim($configured) !== '') {
            return trim($configured);
        }

        return hash('sha256', $this->currentTermsVersion() . '|' . url('/terms'));
    }

    public function currentPrivacyHash(): string
    {
        $configured = Setting::getString('privacy_policy_hash');

        if (is_string($configured) && trim($configured) !== '') {
            return trim($configured);
        }

        return hash('sha256', $this->currentPrivacyVersion() . '|' . url('/privacy'));
    }

    /**
     * @return array{terms: bool, privacy_policy: bool}
     */
    public function complianceStatus(User $user): array
    {
        $terms = ConsentHistory::query()
            ->where('user_id', $user->id)
            ->whereIn('consent_type', [self::TYPE_TERMS, self::TYPE_TERMS_LEGACY])
            ->where('given', true)
            ->where('consent_version', $this->currentTermsVersion())
            ->where('consent_version_hash', $this->currentTermsHash())
            ->exists();

        $privacy = ConsentHistory::query()
            ->where('user_id', $user->id)
            ->where('consent_type', self::TYPE_PRIVACY)
            ->where('given', true)
            ->where('consent_version', $this->currentPrivacyVersion())
            ->where('consent_version_hash', $this->currentPrivacyHash())
            ->exists();

        return [
            'terms' => $terms,
            'privacy_policy' => $privacy,
        ];
    }

    /**
     * @return array<int, string>
     */
    public function missingRequiredConsents(User $user): array
    {
        $status = $this->complianceStatus($user);
        $missing = [];

        if (! $status['terms']) {
            $missing[] = self::TYPE_TERMS;
        }

        if (! $status['privacy_policy']) {
            $missing[] = self::TYPE_PRIVACY;
        }

        return $missing;
    }

    public function requiresReacceptance(User $user): bool
    {
        return $this->missingRequiredConsents($user) !== [];
    }

    public function recordLatestTermsAcceptance(User $user, Request $request, string $source): ConsentHistory
    {
        return ConsentHistory::query()->create([
            'user_id' => $user->id,
            'consent_type' => self::TYPE_TERMS,
            'consent_version' => $this->currentTermsVersion(),
            'consent_version_hash' => $this->currentTermsHash(),
            'source' => $source,
            'given' => true,
            'accepted_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);
    }

    public function recordLatestPrivacyAcceptance(User $user, Request $request, string $source): ConsentHistory
    {
        return ConsentHistory::query()->create([
            'user_id' => $user->id,
            'consent_type' => self::TYPE_PRIVACY,
            'consent_version' => $this->currentPrivacyVersion(),
            'consent_version_hash' => $this->currentPrivacyHash(),
            'source' => $source,
            'given' => true,
            'accepted_at' => now(),
            'ip_address' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);
    }

    /**
     * @return array<string, string>
     */
    public function currentVersionsAndHashes(): array
    {
        return [
            'terms_version' => $this->currentTermsVersion(),
            'terms_hash' => $this->currentTermsHash(),
            'privacy_policy_version' => $this->currentPrivacyVersion(),
            'privacy_policy_hash' => $this->currentPrivacyHash(),
        ];
    }
}
