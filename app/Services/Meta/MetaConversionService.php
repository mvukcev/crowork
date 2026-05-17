<?php

namespace App\Services\Meta;

use App\DataTransferObjects\MetaEventData;
use App\Models\EducationApplication;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MetaConversionService
{
    public function isEnabled(): bool
    {
        return (bool) config('meta.enabled', false);
    }

    public function isBrowserPixelEnabled(): bool
    {
        return $this->isEnabled()
            && (bool) config('meta.browser_enabled', true)
            && filled(config('meta.pixel_id'));
    }

    public function isCapiEnabled(): bool
    {
        return $this->isEnabled()
            && (bool) config('meta.capi_enabled', true)
            && filled(config('meta.dataset_id'))
            && filled(config('meta.access_token'));
    }

    public function trackCompleteRegistration(
        User $user,
        string $accountType,
        string $eventId,
        ?string $eventSourceUrl = null,
        ?string $clientUserAgent = null,
        ?string $clientIpAddress = null
    ): bool {
        return $this->sendEvent(new MetaEventData(
            eventName: 'CompleteRegistration',
            eventId: $eventId,
            userData: [
                'em' => $user->email,
                'external_id' => (string) $user->id,
            ],
            customData: [
                'currency' => 'EUR',
                'value' => 0,
                'content_name' => 'Account registration completed',
                'content_type' => 'registration',
                'account_type' => $accountType,
                'content_id' => (string) $user->id,
            ],
            eventSourceUrl: $eventSourceUrl,
            clientUserAgent: $clientUserAgent,
            clientIpAddress: $clientIpAddress,
        ));
    }

    public function trackJobApplicationSubmitted(
        JobApplication $application,
        string $eventId,
        ?string $eventSourceUrl = null,
        ?string $clientUserAgent = null,
        ?string $clientIpAddress = null
    ): bool {
        $snapshot = is_array($application->profile_snapshot) ? $application->profile_snapshot : [];

        return $this->sendEvent(new MetaEventData(
            eventName: 'SubmitApplication',
            eventId: $eventId,
            userData: [
                'em' => $application->worker?->email,
                'fn' => $snapshot['first_name'] ?? null,
                'ln' => $snapshot['last_name'] ?? null,
                'country' => $snapshot['nationality_country_code'] ?? null,
                'external_id' => (string) $application->worker_id,
            ],
            customData: [
                'currency' => 'EUR',
                'value' => 0,
                'content_type' => 'job_application',
                'content_id' => (string) $application->id,
                'job_id' => (string) $application->job_id,
            ],
            eventSourceUrl: $eventSourceUrl,
            clientUserAgent: $clientUserAgent,
            clientIpAddress: $clientIpAddress,
        ));
    }

    public function trackEducationApplicationSubmitted(
        EducationApplication $application,
        string $eventId,
        ?string $eventSourceUrl = null,
        ?string $clientUserAgent = null,
        ?string $clientIpAddress = null
    ): bool {
        $snapshot = is_array($application->profile_snapshot) ? $application->profile_snapshot : [];

        return $this->sendEvent(new MetaEventData(
            eventName: 'SubmitApplication',
            eventId: $eventId,
            userData: [
                'em' => $application->worker?->email,
                'fn' => $snapshot['first_name'] ?? null,
                'ln' => $snapshot['last_name'] ?? null,
                'country' => $snapshot['nationality_country_code'] ?? null,
                'external_id' => (string) $application->worker_id,
            ],
            customData: [
                'currency' => 'EUR',
                'value' => 0,
                'content_type' => 'education_application',
                'content_id' => (string) $application->id,
                'education_id' => (string) $application->education_id,
            ],
            eventSourceUrl: $eventSourceUrl,
            clientUserAgent: $clientUserAgent,
            clientIpAddress: $clientIpAddress,
        ));
    }

    public function trackApplicationStatusChanged(
        JobApplication $application,
        string $newStatus,
        string $eventId,
        ?string $eventSourceUrl = null,
        ?string $clientUserAgent = null,
        ?string $clientIpAddress = null
    ): bool {
        return $this->sendEvent(new MetaEventData(
            eventName: 'Contact',
            eventId: $eventId,
            userData: [
                'em' => $application->worker?->email,
                'external_id' => (string) $application->worker_id,
            ],
            customData: [
                'currency' => 'EUR',
                'value' => 0,
                'content_type' => 'application_status',
                'content_id' => (string) $application->id,
                'job_id' => (string) $application->job_id,
                'status' => $newStatus,
            ],
            eventSourceUrl: $eventSourceUrl,
            clientUserAgent: $clientUserAgent,
            clientIpAddress: $clientIpAddress,
        ));
    }

    public function sendEvent(MetaEventData $eventData): bool
    {
        if (! $this->shouldSendServerEvents()) {
            $this->log('info', 'Meta event skipped: service disabled for environment/config', [
                'event_name' => $eventData->eventName,
                'event_id' => $eventData->eventId,
            ]);

            return false;
        }

        $payload = [
            'event_name' => $eventData->eventName,
            'event_time' => time(),
            'event_id' => $eventData->eventId,
            'action_source' => $eventData->actionSource,
            'user_data' => $this->normalizeUserData($eventData->userData, $eventData),
            'custom_data' => $eventData->customData,
        ];

        if ($eventData->eventSourceUrl) {
            $payload['event_source_url'] = $eventData->eventSourceUrl;
        }

        try {
            $response = Http::timeout((int) config('meta.timeout_seconds', 10))
                ->post($this->endpoint(), [
                    'data' => [$payload],
                    'access_token' => (string) config('meta.access_token'),
                    'test_event_code' => config('meta.test_event_code') ?: null,
                ]);

            if ($response->successful() && (($response->json('events_received') ?? 0) > 0)) {
                $this->log('info', 'Meta event sent', [
                    'event_name' => $eventData->eventName,
                    'event_id' => $eventData->eventId,
                    'events_received' => (int) $response->json('events_received', 0),
                ]);

                return true;
            }

            $this->log('warning', 'Meta event rejected', [
                'event_name' => $eventData->eventName,
                'event_id' => $eventData->eventId,
                'status_code' => $response->status(),
                'response' => $response->json(),
            ]);

            return false;
        } catch (\Throwable $exception) {
            $this->log('error', 'Meta event failed', [
                'event_name' => $eventData->eventName,
                'event_id' => $eventData->eventId,
                'error' => $exception->getMessage(),
            ]);

            return false;
        }
    }

    private function shouldSendServerEvents(): bool
    {
        if (! $this->isCapiEnabled()) {
            return false;
        }

        if (app()->environment('local') && ! (bool) config('meta.send_from_local', false)) {
            return false;
        }

        return true;
    }

    private function endpoint(): string
    {
        $apiVersion = (string) config('meta.api_version', 'v20.0');
        $datasetId = (string) config('meta.dataset_id');

        return sprintf('https://graph.facebook.com/%s/%s/events', $apiVersion, $datasetId);
    }

    private function normalizeUserData(array $rawUserData, MetaEventData $eventData): array
    {
        $normalized = [];

        foreach (['em', 'ph', 'fn', 'ln', 'ct', 'country'] as $hashKey) {
            if (! empty($rawUserData[$hashKey])) {
                $normalized[$hashKey] = $this->hashNormalized((string) $rawUserData[$hashKey]);
            }
        }

        if (! empty($rawUserData['external_id'])) {
            $normalized['external_id'] = $this->hashNormalized((string) $rawUserData['external_id']);
        }

        if ($eventData->clientIpAddress) {
            $normalized['client_ip_address'] = $eventData->clientIpAddress;
        }

        if ($eventData->clientUserAgent) {
            $normalized['client_user_agent'] = $eventData->clientUserAgent;
        }

        return $normalized;
    }

    private function hashNormalized(string $value): string
    {
        $normalized = strtolower(trim($value));

        return hash('sha256', $normalized);
    }

    private function log(string $level, string $message, array $context = []): void
    {
        Log::channel((string) config('meta.log_channel', 'meta'))->{$level}($message, $context);
    }
}
