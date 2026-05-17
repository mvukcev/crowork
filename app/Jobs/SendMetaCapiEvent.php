<?php

namespace App\Jobs;

use App\Models\JobApplication;
use App\Services\ConsentConfigService;
use App\Services\MetaConversionsAPIService;
use App\Services\MetaPixelConfigService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMetaCapiEvent implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(
        public string $eventType,
        public int $jobApplicationId,
        public ?string $status = null
    ) {
        $this->onQueue('default');
    }

    public function handle(MetaConversionsAPIService $metaService): void
    {
        if (! MetaPixelConfigService::canUseCAPI()) {
            return;
        }

        $application = JobApplication::query()
            ->with(['worker', 'job.employer'])
            ->find($this->jobApplicationId);

        if (! $application) {
            return;
        }

        if (! ConsentConfigService::hasMarketingConsent(null, $application->worker)) {
            return;
        }

        if ($this->eventType === 'application_submitted') {
            $metaService->trackJobApplicationSubmitted($application);

            return;
        }

        if ($this->eventType === 'application_status_changed') {
            $newStatus = $this->status ?? (string) $application->status;
            $metaService->trackApplicationStatusChange($application, $newStatus);
        }
    }
}
