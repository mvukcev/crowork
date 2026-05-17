<?php

namespace App\Services;

use App\Models\LegalHold;

class LegalHoldService
{
    public function hasActiveHoldForUser(int $userId): bool
    {
        return LegalHold::query()
            ->where('status', LegalHold::STATUS_ACTIVE)
            ->where('user_id', $userId)
            ->exists();
    }

    public function hasActiveHoldForTarget(string $targetType, string|int|null $targetId, ?int $userId = null): bool
    {
        return $this->activeHoldForTarget($targetType, $targetId, $userId) !== null;
    }

    public function activeHoldForTarget(string $targetType, string|int|null $targetId, ?int $userId = null): ?LegalHold
    {
        return LegalHold::query()
            ->where('status', LegalHold::STATUS_ACTIVE)
            ->where(function ($query) use ($targetType, $targetId, $userId): void {
                if ($userId !== null) {
                    $query->orWhere('user_id', $userId);
                }

                if ($targetId !== null) {
                    $query->orWhere(function ($targetQuery) use ($targetType, $targetId): void {
                        $targetQuery->where('target_type', $targetType)
                            ->where('target_id', (string) $targetId);
                    });
                }
            })
            ->latest('placed_at')
            ->first();
    }
}
