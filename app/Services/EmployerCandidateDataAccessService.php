<?php

namespace App\Services;

use App\Models\JobApplication;
use App\Models\Setting;
use Illuminate\Support\Carbon;

class EmployerCandidateDataAccessService
{
    public const STATE_ACTIVE_PROCESS = 'active_process';
    public const STATE_REJECTED_RETENTION = 'rejected_retention';
    public const STATE_PENDING_DELETION = 'pending_deletion';
    public const STATE_AWAITING_ANONYMIZATION = 'awaiting_anonymization';
    public const STATE_ANONYMIZED = 'anonymized';

    /**
     * @return array<string, mixed>
     */
    public function forApplication(JobApplication $application): array
    {
        $application->loadMissing('worker');

        $snapshot = is_array($application->profile_snapshot) ? $application->profile_snapshot : [];
        $isRetainedAnonymized = (bool) ($snapshot['retained_anonymized'] ?? false);

        if ($application->anonymized_at !== null || $isRetainedAnonymized) {
            return $this->payload(self::STATE_ANONYMIZED, null, false);
        }

        $worker = $application->worker;
        if ($worker !== null && (bool) $worker->pending_deletion && $worker->anonymization_scheduled_at !== null) {
            $scheduledAt = $this->asCarbon($worker->anonymization_scheduled_at);

            return $this->payload(
                self::STATE_PENDING_DELETION,
                $scheduledAt,
                $scheduledAt?->isFuture() ?? false
            );
        }

        if ($application->status === JobApplication::STATUS_REJECTED) {
            $availableUntil = $this->rejectedDataAvailableUntil($application);

            if ($availableUntil !== null && $availableUntil->isPast()) {
                return $this->payload(self::STATE_AWAITING_ANONYMIZATION, $availableUntil, false);
            }

            return $this->payload(self::STATE_REJECTED_RETENTION, $availableUntil, true);
        }

        return $this->payload(self::STATE_ACTIVE_PROCESS, null, true);
    }

    public function rejectedDataAvailableUntil(JobApplication $application): ?Carbon
    {
        $months = max(1, Setting::getInt('rejected_applications_retention_months', 6));
        $anchor = $this->statusAnchor($application);

        if ($anchor === null) {
            return null;
        }

        return $anchor->copy()->addMonthsNoOverflow($months)->endOfDay();
    }

    public function statusAnchor(JobApplication $application): ?Carbon
    {
        return $this->asCarbon($application->status_updated_at)
            ?? $this->asCarbon($application->created_at);
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(string $state, ?Carbon $availableUntil, bool $personalDataAvailable): array
    {
        return [
            'state' => $state,
            'personal_data_available' => $personalDataAvailable,
            'data_available_until' => $availableUntil,
            'data_available_until_iso' => $availableUntil?->toDateString(),
            'data_available_until_human' => $availableUntil?->translatedFormat('M d, Y'),
            'label' => __('employer.gdpr.states.' . $state . '.label'),
            'description' => __('employer.gdpr.states.' . $state . '.description'),
            'lawful_basis' => __('employer.gdpr.states.' . $state . '.lawful_basis'),
        ];
    }

    private function asCarbon(mixed $value): ?Carbon
    {
        if ($value instanceof Carbon) {
            return $value;
        }

        if ($value === null) {
            return null;
        }

        return Carbon::parse($value);
    }
}
