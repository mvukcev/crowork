<?php

namespace App\Jobs;

use App\Models\EducationApplication;
use App\Models\JobApplication;
use App\Models\User;
use App\Services\ConsentConfigService;
use App\Services\Meta\MetaConversionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendMetaEventJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public array $backoff = [30, 120, 300];

    public function __construct(
        public string $eventType,
        public array $context,
        public string $eventId,
    ) {
        $this->onQueue((string) config('meta.queue', 'default'));
    }

    public function handle(MetaConversionService $metaService): void
    {
        if (! $metaService->isCapiEnabled()) {
            return;
        }

        match ($this->eventType) {
            'complete_registration' => $this->handleCompleteRegistration($metaService),
            'job_application_submitted' => $this->handleJobApplicationSubmitted($metaService),
            'education_application_submitted' => $this->handleEducationApplicationSubmitted($metaService),
            'application_status_changed' => $this->handleApplicationStatusChanged($metaService),
            default => $this->logSkip('unknown_event_type', ['event_type' => $this->eventType]),
        };
    }

    private function handleCompleteRegistration(MetaConversionService $metaService): void
    {
        $userId = (int) ($this->context['user_id'] ?? 0);
        $accountType = (string) ($this->context['account_type'] ?? '');

        if ($userId <= 0 || $accountType === '') {
            $this->logSkip('invalid_context_complete_registration');

            return;
        }

        $user = User::query()->find($userId);
        if (! $user) {
            $this->logSkip('missing_user', ['user_id' => $userId]);

            return;
        }

        if (! ConsentConfigService::hasMarketingConsent(null, $user)) {
            $this->logSkip('missing_marketing_consent', ['user_id' => $userId]);

            return;
        }

        $metaService->trackCompleteRegistration(
            user: $user,
            accountType: $accountType,
            eventId: $this->eventId,
            eventSourceUrl: $this->context['event_source_url'] ?? null,
            clientUserAgent: $this->context['client_user_agent'] ?? null,
            clientIpAddress: $this->context['client_ip_address'] ?? null,
        );
    }

    private function handleJobApplicationSubmitted(MetaConversionService $metaService): void
    {
        $applicationId = (int) ($this->context['application_id'] ?? 0);
        if ($applicationId <= 0) {
            $this->logSkip('invalid_context_job_application');

            return;
        }

        $application = JobApplication::query()
            ->with(['worker'])
            ->find($applicationId);

        if (! $application || ! $application->worker) {
            $this->logSkip('missing_job_application_or_worker', ['application_id' => $applicationId]);

            return;
        }

        if (! ConsentConfigService::hasMarketingConsent(null, $application->worker)) {
            $this->logSkip('missing_marketing_consent', ['user_id' => $application->worker->id]);

            return;
        }

        $metaService->trackJobApplicationSubmitted(
            application: $application,
            eventId: $this->eventId,
            eventSourceUrl: $this->context['event_source_url'] ?? null,
            clientUserAgent: $this->context['client_user_agent'] ?? null,
            clientIpAddress: $this->context['client_ip_address'] ?? null,
        );
    }

    private function handleEducationApplicationSubmitted(MetaConversionService $metaService): void
    {
        $applicationId = (int) ($this->context['application_id'] ?? 0);
        if ($applicationId <= 0) {
            $this->logSkip('invalid_context_education_application');

            return;
        }

        $application = EducationApplication::query()
            ->with(['worker'])
            ->find($applicationId);

        if (! $application || ! $application->worker) {
            $this->logSkip('missing_education_application_or_worker', ['application_id' => $applicationId]);

            return;
        }

        if (! ConsentConfigService::hasMarketingConsent(null, $application->worker)) {
            $this->logSkip('missing_marketing_consent', ['user_id' => $application->worker->id]);

            return;
        }

        $metaService->trackEducationApplicationSubmitted(
            application: $application,
            eventId: $this->eventId,
            eventSourceUrl: $this->context['event_source_url'] ?? null,
            clientUserAgent: $this->context['client_user_agent'] ?? null,
            clientIpAddress: $this->context['client_ip_address'] ?? null,
        );
    }

    private function handleApplicationStatusChanged(MetaConversionService $metaService): void
    {
        $applicationId = (int) ($this->context['application_id'] ?? 0);
        $status = (string) ($this->context['status'] ?? '');

        if ($applicationId <= 0 || $status === '') {
            $this->logSkip('invalid_context_status_changed');

            return;
        }

        $application = JobApplication::query()
            ->with(['worker'])
            ->find($applicationId);

        if (! $application || ! $application->worker) {
            $this->logSkip('missing_status_application_or_worker', ['application_id' => $applicationId]);

            return;
        }

        if (! ConsentConfigService::hasMarketingConsent(null, $application->worker)) {
            $this->logSkip('missing_marketing_consent', ['user_id' => $application->worker->id]);

            return;
        }

        $metaService->trackApplicationStatusChanged(
            application: $application,
            newStatus: $status,
            eventId: $this->eventId,
            eventSourceUrl: $this->context['event_source_url'] ?? null,
            clientUserAgent: $this->context['client_user_agent'] ?? null,
            clientIpAddress: $this->context['client_ip_address'] ?? null,
        );
    }

    private function logSkip(string $reason, array $context = []): void
    {
        Log::channel((string) config('meta.log_channel', 'meta'))->info('Meta event skipped', [
            'reason' => $reason,
            'event_type' => $this->eventType,
            'event_id' => $this->eventId,
            ...$context,
        ]);
    }
}
