<?php

namespace App\Services;

use App\Models\Employer;
use App\Models\Setting;
use App\Models\User;

/**
 * Service for managing application visibility and masking rules
 * Enforces global settings with employer-specific overrides
 */
class ApplicationVisibilityService
{
    /**
     * Get the effective visibility level for an employer
     * 
     * @param Employer $employer
     * @return string ('full' | 'limited' | 'anonymous')
     */
    public function getEffectiveVisibility(Employer $employer): string
    {
        // Check for employer-specific override
        if ($employer->applications_visibility_override !== null) {
            return $employer->applications_visibility_override;
        }

        // Fallback to global setting (new key, then legacy key).
        $value = Setting::getString('application_visibility_mode');
        if ($value === null) {
            $value = Setting::getString('employer_application_visibility', 'limited');
        }

        return strtolower($value ?? 'limited'); // Default to 'limited' for safety
    }

    /**
     * Get the effective visible fields for an employer
     * 
     * @param Employer $employer
     * @return array
     */
    public function getEffectiveVisibleFields(Employer $employer): array
    {
        // Check for employer-specific override
        if ($employer->visible_fields_override !== null) {
            return $employer->visible_fields_override;
        }

        // Fallback to global setting.
        $value = Setting::getValue('employer_visible_fields');
        if (! is_array($value)) {
            return $this->getDefaultVisibleFields();
        }

        return $value;
    }

    /**
     * Check if an employer can export applications
     * 
     * @param Employer $employer
     * @return bool
     */
    public function canExportApplications(Employer $employer): bool
    {
        // Check for employer-specific override
        if ($employer->can_export_applications_override !== null) {
            return $employer->can_export_applications_override;
        }

        // Fallback to global setting (new key, then legacy key).
        $value = Setting::getValue('employer_export_allowed');
        if ($value === null) {
            $value = Setting::getValue('employer_can_export_applications', false);
        }

        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false;
    }

    /**
     * Get default visible fields (safe subset)
     * 
     * @return array
     */
    public function getDefaultVisibleFields(): array
    {
        return [
            'first_name',
            'last_name',
            'nationality_country_code',
            'birth_year',
            'education_summary',
            'work_experience',
            'skills',
            'languages',
            'structured_experiences',
            'structured_educations',
            'structured_certifications',
        ];
    }

    /**
     * Mask the profile snapshot based on visibility rules
     * 
     * @param array $profileSnapshot
     * @param Employer $employer
     * @return array Masked snapshot
     */
    public function maskSnapshot(array $profileSnapshot, Employer $employer): array
    {
        if (($profileSnapshot['retained_anonymized'] ?? false) === true) {
            return $this->retentionAnonymizedSnapshot($profileSnapshot);
        }

        $visibility = $this->getEffectiveVisibility($employer);
        
        return match ($visibility) {
            'full' => $profileSnapshot,
            'limited' => $this->applyLimitedVisibility($profileSnapshot, $employer),
            'anonymous' => $this->applyAnonymousVisibility($profileSnapshot),
            default => $this->applyLimitedVisibility($profileSnapshot, $employer),
        };
    }

    public function maskSnapshotForWorker(array $profileSnapshot, Employer $employer, ?User $worker): array
    {
        if (($profileSnapshot['retained_anonymized'] ?? false) === true) {
            return $this->retentionAnonymizedSnapshot($profileSnapshot);
        }

        if ($worker && ($worker->pending_deletion || $worker->trashed())) {
            return $this->applyAnonymousVisibility($profileSnapshot);
        }

        return $this->maskSnapshot($profileSnapshot, $employer);
    }

    /**
     * Apply limited visibility (show only allowed fields)
     * 
     * @param array $snapshot
     * @param Employer $employer
     * @return array
     */
    private function applyLimitedVisibility(array $snapshot, Employer $employer): array
    {
        $visibleFields = $this->getEffectiveVisibleFields($employer);
        $masked = [];

        foreach ($visibleFields as $field) {
            if (isset($snapshot[$field])) {
                $masked[$field] = $snapshot[$field];
            }
        }

        return $masked;
    }

    /**
     * Apply anonymous visibility (hide personal identifiers)
     * 
     * @param array $snapshot
     * @return array
     */
    private function applyAnonymousVisibility(array $snapshot): array
    {
        // Fields to always hide in anonymous mode
        $hiddenFields = [
            'first_name',
            'last_name',
            'photo_path',
            'recommendations',
            'email',
            'phone',
        ];

        // Keep only safe fields
        $safe = [
            'nationality_country_code',
            'birth_year',
            'education_summary',
            'work_experience',
            'skills',
            'languages',
            'structured_experiences',
            'structured_educations',
            'structured_certifications',
        ];

        $masked = [];
        foreach ($safe as $field) {
            if (isset($snapshot[$field])) {
                $masked[$field] = $snapshot[$field];
            }
        }

        return $masked;
    }

    private function retentionAnonymizedSnapshot(array $snapshot): array
    {
        return [
            'retained_anonymized' => true,
            'retention_reason' => $snapshot['retention_reason'] ?? 'rejected_application_retention',
            'retention_processed_at' => $snapshot['retention_processed_at'] ?? null,
            'skills_count' => (int) ($snapshot['skills_count'] ?? 0),
            'languages_count' => (int) ($snapshot['languages_count'] ?? 0),
            'experience_entries_count' => (int) ($snapshot['experience_entries_count'] ?? 0),
            'education_entries_count' => (int) ($snapshot['education_entries_count'] ?? 0),
        ];
    }

    /**
     * Get a human-readable label for visibility level
     * 
     * @param string $visibility
     * @return string
     */
    public function getVisibilityLabel(string $visibility): string
    {
        return match ($visibility) {
            'full' => 'Full Profile',
            'limited' => 'Limited Information',
            'anonymous' => 'Anonymous',
            default => 'Limited Information',
        };
    }

    /**
     * Get a human-readable description of visibility level
     * 
     * @param string $visibility
     * @return string
     */
    public function getVisibilityDescription(string $visibility): string
    {
        return match ($visibility) {
            'full' => 'You can see all profile information including name, contact details, and recommendations.',
            'limited' => 'You can see professional information (skills, experience, education) but not personal details.',
            'anonymous' => 'You can see professional qualifications only, without personal identifiers.',
            default => 'Limited information is shown for worker privacy.',
        };
    }
}
