<?php

namespace App\Services;

use App\Models\Employer;
use App\Models\Setting;
use Illuminate\Database\Eloquent\Model;

/**
 * Service for managing job and education approval workflows
 * Enforces global settings with per-employer overrides
 */
class ApprovalService
{
    /**
     * Check if a listing type requires approval for an employer
     * 
     * @param Employer|null $employer
     * @param string $type ('job' | 'education')
     * @return bool
     */
    public function requiresApprovalForEmployer(?Employer $employer, string $type = 'job'): bool
    {
        // Validate type
        if (!in_array($type, ['job', 'education'])) {
            throw new \InvalidArgumentException("Invalid type: {$type}");
        }

        // Check for employer-specific override
        if ($employer?->require_approval_override !== null) {
            return $employer->require_approval_override;
        }

        // Fallback to global setting
        $key = match ($type) {
            'job' => 'jobs_require_approval',
            'education' => 'educations_require_approval',
        };

        $globalSetting = Setting::where('key', $key)->first();
        return $globalSetting?->value['value'] ?? true; // Default to true (safer)
    }

    /**
     * Determine the initial status when creating a listing
     * 
     * @param Employer|null $employer
     * @param string $type ('job' | 'education')
     * @return string ('pending' | 'published')
     */
    public function getInitialStatus(?Employer $employer, string $type = 'job'): string
    {
        $requiresApproval = $this->requiresApprovalForEmployer($employer, $type);
        
        if ($requiresApproval) {
            return 'pending';
        }
        
        return 'published';
    }

    /**
     * Publish a listing (set status to published and published_at timestamp)
     * 
     * @param Model $listing (Job or Education)
     * @return void
     */
    public function publish(Model $listing): void
    {
        // Set published_at only if not already set
        if (!$listing->published_at) {
            $listing->published_at = now();
        }

        $listing->status = 'published';
        $listing->save();
    }

    /**
     * Delist a listing (set status to delisted)
     * 
     * @param Model $listing (Job or Education)
     * @return void
     */
    public function delist(Model $listing): void
    {
        $listing->status = 'delisted';
        $listing->save();
    }

    /**
     * Mark a listing as pending (revert to pending for re-review)
     * 
     * @param Model $listing (Job or Education)
     * @return void
     */
    public function markPending(Model $listing): void
    {
        $listing->status = 'pending';
        $listing->published_at = null; // Clear published timestamp
        $listing->save();
    }

    /**
     * Check if a listing is visible to the public
     * (must be published, not expired, not delisted)
     * 
     * @param Model $listing (Job or Education)
     * @return bool
     */
    public function isPubliclyVisible(Model $listing): bool
    {
        // Must be published
        if ($listing->status !== 'published') {
            return false;
        }

        // Must not be delisted
        if ($listing->status === 'delisted') {
            return false;
        }

        // Must not be expired (if expires_at is set)
        if ($listing->expires_at && $listing->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Check if a listing is pending approval
     * 
     * @param Model $listing (Job or Education)
     * @return bool
     */
    public function isPending(Model $listing): bool
    {
        return $listing->status === 'pending';
    }

    /**
     * Check if a listing is expired
     * 
     * @param Model $listing (Job or Education)
     * @return bool
     */
    public function isExpired(Model $listing): bool
    {
        return $listing->expires_at && $listing->expires_at->isPast();
    }

    /**
     * Get a human-readable label for status
     * 
     * @param string $status
     * @return string
     */
    public function getStatusLabel(string $status): string
    {
        return match ($status) {
            'draft' => 'Draft',
            'pending' => 'Pending Approval',
            'published' => 'Published',
            'delisted' => 'Delisted',
            'expired' => 'Expired',
            default => ucfirst($status),
        };
    }

    /**
     * Get color for status badge
     * 
     * @param string $status
     * @return string (Filament color)
     */
    public function getStatusColor(string $status): string
    {
        return match ($status) {
            'draft' => 'gray',
            'pending' => 'warning',
            'published' => 'success',
            'delisted' => 'danger',
            'expired' => 'warning',
            default => 'gray',
        };
    }
}
