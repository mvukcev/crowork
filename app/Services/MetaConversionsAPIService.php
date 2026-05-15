<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * MetaConversionsAPIService - Handles Meta CAPI event tracking with event_id consistency
 *
 * Ensures each event has a unique, idempotent event_id to prevent duplicate processing
 */
class MetaConversionsAPIService
{
    private string $accessToken;
    private string $datasetId;
    private string $apiVersion;
    private string $testEventCode;
    private bool $debugMode;

    public function __construct()
    {
        $this->accessToken = MetaPixelConfigService::getAccessToken() ?? '';
        $this->datasetId = MetaPixelConfigService::getDatasetId() ?? '';
        $this->apiVersion = MetaPixelConfigService::getApiVersion();
        $this->testEventCode = MetaPixelConfigService::getTestEventCode() ?? '';
        $this->debugMode = MetaPixelConfigService::isDebugMode();
    }

    /**
     * Send event to Meta CAPI with idempotent event_id
     *
     * @param string $eventName - e.g., 'ViewContent', 'AddToCart', 'Purchase'
     * @param array $userData - User identifying data (email, phone, etc.)
     * @param array $customData - Event custom data (value, currency, etc.)
     * @param string|null $eventId - Optional explicit event ID. Auto-generated if not provided.
     *
     * @return array API response
     */
    public function trackEvent(
        string $eventName,
        array $userData = [],
        array $customData = [],
        ?string $eventId = null
    ): array {
        if (!$this->canUseCAPI()) {
            Log::warning('Meta CAPI not configured, skipping event tracking', [
                'event' => $eventName,
            ]);

            return ['success' => false, 'reason' => 'CAPI not configured'];
        }

        // Generate idempotent event_id if not provided
        $eventId = $eventId ?? $this->generateEventId($eventName);

        $event = [
            'event_name' => $eventName,
            'event_time' => (int)(microtime(true)),
            'event_id' => $eventId,
            'event_source_url' => url()->current(),
            'user_data' => $this->normalizeUserData($userData),
            'custom_data' => $customData,
        ];

        // Add test event code if in debug mode
        if ($this->debugMode && $this->testEventCode) {
            $event['test_event_code'] = $this->testEventCode;
        }

        return $this->sendEvent($event);
    }

    /**
     * Track a job application submission event
     */
    public function trackJobApplicationSubmitted(
        $jobApplication,
        ?string $eventId = null
    ): array {
        return $this->trackEvent(
            'Lead', // Meta standard event
            [
                'em' => $jobApplication->worker?->email,
                'ph' => null, // Add if available
            ],
            [
                'value' => 0, // Application submission has no monetary value
                'currency' => 'EUR',
                'content_name' => 'Application submitted',
                'content_type' => 'job_application',
                'content_id' => (string)$jobApplication->id,
            ],
            $eventId
        );
    }

    /**
     * Track an education application submission event.
     */
    public function trackEducationApplicationSubmitted(
        $educationApplication,
        ?string $eventId = null
    ): array {
        return $this->trackEvent(
            'SubmitApplication',
            [
                'em' => $educationApplication->worker?->email,
            ],
            [
                'value' => 0,
                'currency' => 'EUR',
                'content_name' => 'Education application submitted',
                'content_type' => 'education_application',
                'content_id' => (string) $educationApplication->id,
            ],
            $eventId
        );
    }

    /**
     * Track completed registration events.
     */
    public function trackCompleteRegistration(
        $user,
        string $accountType,
        ?string $eventId = null
    ): array {
        return $this->trackEvent(
            'CompleteRegistration',
            [
                'em' => $user->email,
                'external_id' => (string) $user->id,
            ],
            [
                'value' => 0,
                'currency' => 'EUR',
                'content_name' => 'Account registration completed',
                'content_type' => 'registration',
                'account_type' => $accountType,
                'content_id' => (string) $user->id,
            ],
            $eventId
        );
    }

    /**
     * Track a job application status change event
     */
    public function trackApplicationStatusChange(
        $jobApplication,
        string $newStatus,
        ?string $eventId = null
    ): array {
        $eventName = match ($newStatus) {
            'hired' => 'Purchase', // Application accepted
            'rejected' => 'Contact', // Contact attempt (they were rejected)
            default => 'ViewContent', // General engagement
        };

        return $this->trackEvent(
            $eventName,
            [
                'em' => $jobApplication->worker?->email,
            ],
            [
                'content_name' => "Application {$newStatus}",
                'content_type' => 'application_status',
                'content_id' => (string)$jobApplication->id,
                'custom_data' => [
                    'previous_status' => $jobApplication->getOriginal('status'),
                    'new_status' => $newStatus,
                ],
            ],
            $eventId
        );
    }

    /**
     * Generate an idempotent event_id
     *
     * Event IDs must be globally unique but deterministic for the same event
     * Format: SHA256(event_name + user_data + timestamp_seconds)
     */
    public function generateEventId(
        string $eventName,
        ?string $userId = null,
        ?string $userEmail = null
    ): string {
        $timestamp = (int)time(); // Use second precision for some flexibility in retries
        $userId = $userId ?? (auth()->id() ?? 'anonymous');

        return hash('sha256', implode('|', [
            $eventName,
            $userId,
            $userEmail ?? '',
            $timestamp,
            config('app.key'),
        ]));
    }

    /**
     * Normalize user data according to Meta CAPI spec
     *
     * All PII must be hashed with SHA256
     */
    private function normalizeUserData(array $userData): array
    {
        $normalized = [];

        // Email
        if (!empty($userData['em'])) {
            $normalized['em'] = $this->hashPII($userData['em']);
        }

        // Phone
        if (!empty($userData['ph'])) {
            $normalized['ph'] = $this->hashPII($userData['ph']);
        }

        // First name
        if (!empty($userData['fn'])) {
            $normalized['fn'] = $this->hashPII($userData['fn']);
        }

        // Last name
        if (!empty($userData['ln'])) {
            $normalized['ln'] = $this->hashPII($userData['ln']);
        }

        // City
        if (!empty($userData['ct'])) {
            $normalized['ct'] = $this->hashPII($userData['ct']);
        }

        // Country
        if (!empty($userData['country'])) {
            $normalized['country'] = $this->hashPII($userData['country']);
        }

        // ZIP
        if (!empty($userData['zp'])) {
            $normalized['zp'] = $this->hashPII($userData['zp']);
        }

        // Date of birth
        if (!empty($userData['db'])) {
            $normalized['db'] = $this->hashPII($userData['db']);
        }

        // External ID (your own user ID)
        if (!empty($userData['external_id'])) {
            $normalized['external_id'] = $userData['external_id']; // Don't hash
        }

        return $normalized;
    }

    /**
     * Hash PII according to Meta spec
     */
    private function hashPII(string $value): string
    {
        // Remove whitespace, convert to lowercase, then hash
        $normalized = strtolower(trim($value));
        return hash('sha256', $normalized);
    }

    /**
     * Send event to Meta CAPI endpoint
     */
    private function sendEvent(array $event): array
    {
        try {
            $endpoint = MetaPixelConfigService::getApiEndpoint();
            $url = "{$endpoint}{$this->datasetId}/events";

            $response = Http::timeout(10)
                ->post($url, [
                    'data' => [$event],
                    'access_token' => $this->accessToken,
                ])
                ->json();

            if ($response['events_received'] ?? 0 > 0) {
                Log::info('Meta CAPI event tracked successfully', [
                    'event_id' => $event['event_id'],
                    'event_name' => $event['event_name'],
                    'response' => $response,
                ]);

                return ['success' => true, 'response' => $response];
            }

            Log::warning('Meta CAPI event not received', [
                'event_id' => $event['event_id'],
                'response' => $response,
            ]);

            return ['success' => false, 'response' => $response];
        } catch (\Exception $e) {
            Log::error('Meta CAPI event send failed', [
                'event_id' => $event['event_id'],
                'error' => $e->getMessage(),
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Check if CAPI is properly configured
     */
    public function canUseCAPI(): bool
    {
        return !empty($this->accessToken) && !empty($this->datasetId);
    }

    /**
     * Get all configured CAPI parameters
     */
    public function getConfiguration(): array
    {
        return [
            'access_token_configured' => !empty($this->accessToken),
            'dataset_id' => $this->datasetId,
            'api_version' => $this->apiVersion,
            'test_event_code' => $this->testEventCode,
            'debug_mode' => $this->debugMode,
            'can_use_capi' => $this->canUseCAPI(),
        ];
    }
}
