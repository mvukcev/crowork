<?php

namespace App\Services;

use App\Jobs\AnonymizeUserDataJob;
use App\Models\AccountDeletionRequest;
use App\Models\User;

class AccountDeletionService
{
    public function requestDeletion(User $user, ?string $reason = null): AccountDeletionRequest
    {
        if ($user->pending_deletion) {
            return $user->accountDeletionRequests()
                ->latest('id')
                ->firstOrFail();
        }

        $requestedAt = now();
        $scheduledAt = $requestedAt->copy()->addDays(14);

        $deletionRequest = $user->accountDeletionRequests()->create([
            'status' => AccountDeletionRequest::STATUS_PENDING,
            'reason' => $reason,
            'requested_at' => $requestedAt,
            'anonymization_scheduled_at' => $scheduledAt,
        ]);

        $user->forceFill([
            'pending_deletion' => true,
            'deletion_requested_at' => $requestedAt,
            'anonymization_scheduled_at' => $scheduledAt,
        ])->save();

        AnonymizeUserDataJob::dispatch($user->id, $deletionRequest->id)->delay($scheduledAt);

        return $deletionRequest;
    }
}
