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
        $isEmployer = $user->role === User::ROLE_EMPLOYER;
        $scheduledAt = $isEmployer
            ? $requestedAt->copy()
            : $requestedAt->copy()->addDays(14);

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

        if ($isEmployer) {
            // Employer self-deletion should release account identifiers immediately.
            AnonymizeUserDataJob::dispatchSync($user->id, $deletionRequest->id);
        } else {
            AnonymizeUserDataJob::dispatch($user->id, $deletionRequest->id)->delay($scheduledAt);
        }

        return $deletionRequest;
    }
}
