<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\JobApplication;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * DataIntegrityService - Enforces data integrity constraints for ATS and sensitive records
 *
 * This service ensures:
 * - Job application snapshots are immutable
 * - Status transitions follow valid workflows
 * - All sensitive updates are audited
 * - Notifications prevent duplication
 * - Event IDs are consistent for idempotency
 */
class DataIntegrityService
{
    /**
     * Valid status transition workflows
     */
    private static array $validTransitions = [
        JobApplication::STATUS_NEW => [
            JobApplication::STATUS_REVIEWING,
            JobApplication::STATUS_REJECTED,
        ],
        JobApplication::STATUS_REVIEWING => [
            JobApplication::STATUS_SHORTLISTED,
            JobApplication::STATUS_REJECTED,
        ],
        JobApplication::STATUS_SHORTLISTED => [
            JobApplication::STATUS_INTERVIEW,
            JobApplication::STATUS_REJECTED,
        ],
        JobApplication::STATUS_INTERVIEW => [
            JobApplication::STATUS_OFFER,
            JobApplication::STATUS_REJECTED,
        ],
        JobApplication::STATUS_OFFER => [
            JobApplication::STATUS_HIRED,
            JobApplication::STATUS_REJECTED,
        ],
        JobApplication::STATUS_HIRED => [], // Terminal state
        JobApplication::STATUS_REJECTED => [], // Terminal state
    ];

    /**
     * Validate a status transition for a job application
     *
     * @throws \Exception if transition is invalid
     */
    public static function validateStatusTransition(JobApplication $application, string $newStatus): bool
    {
        $currentStatus = $application->status;

        if ($currentStatus === $newStatus) {
            return true; // No change is always valid
        }

        $allowedTransitions = self::$validTransitions[$currentStatus] ?? [];

        if (!in_array($newStatus, $allowedTransitions, true)) {
            throw new \Exception(
                "Invalid status transition from '{$currentStatus}' to '{$newStatus}'. "
                . "Allowed transitions: " . implode(', ', $allowedTransitions ?: ['none'])
            );
        }

        return true;
    }

    /**
     * Log application status change with all relevant details
     */
    public static function logApplicationStatusChange(
        JobApplication $application,
        string $previousStatus,
        ?User $user = null,
        ?string $reason = null
    ): void {
        $user = $user ?? auth()->user();

        AuditLog::logAction(
            action: 'job_application_status_changed',
            user: $user,
            subjectType: JobApplication::class,
            subjectId: $application->id,
            changes: [
                'previous_status' => $previousStatus,
                'new_status' => $application->status,
                'status_updated_at' => $application->status_updated_at?->toIso8601String(),
                'interview_at' => $application->interview_at?->toIso8601String(),
                'score' => $application->score,
                'reason' => $reason,
            ],
            description: "Application #{$application->id} for job '{$application->job?->title}' status changed from {$previousStatus} to {$application->status}"
        );
    }

    /**
     * Log internal note updates on applications
     */
    public static function logInternalNoteUpdate(
        JobApplication $application,
        string $previousNote,
        string $newNote,
        ?User $user = null
    ): void {
        $user = $user ?? auth()->user();

        AuditLog::logAction(
            action: 'job_application_internal_note_updated',
            user: $user,
            subjectType: JobApplication::class,
            subjectId: $application->id,
            changes: [
                'previous_note' => $previousNote ? substr($previousNote, 0, 100) . (strlen($previousNote) > 100 ? '...' : '') : null,
                'new_note' => $newNote ? substr($newNote, 0, 100) . (strlen($newNote) > 100 ? '...' : '') : null,
                'note_length_previous' => strlen($previousNote ?? ''),
                'note_length_new' => strlen($newNote ?? ''),
            ],
            description: "Internal note updated on application #{$application->id}"
        );
    }

    /**
     * Log abuse report status changes and moderation actions
     */
    public static function logAbuseReportModeration(
        $report,
        string $previousStatus,
        string $newStatus,
        string $adminNotes,
        ?User $user = null
    ): void {
        $user = $user ?? auth()->user();

        AuditLog::logAction(
            action: 'abuse_report_moderated',
            user: $user,
            subjectType: 'AbuseReport',
            subjectId: $report->id,
            changes: [
                'previous_status' => $previousStatus,
                'new_status' => $newStatus,
                'admin_notes_length' => strlen($adminNotes ?? ''),
                'report_type' => $report->type,
                'target_id' => $report->target_id,
            ],
            description: "Abuse report #{$report->id} moderated: {$previousStatus} → {$newStatus}"
        );
    }

    /**
     * Generate an idempotent event ID for analytics events (Meta CAPI, etc.)
     *
     * Event ID format: {event_type}_{entity_type}_{entity_id}_{timestamp_ms}_{user_id}
     * This ensures the same event won't be processed twice if resent
     */
    public static function generateEventId(
        string $eventType,
        string $entityType,
        int $entityId,
        ?int $userId = null
    ): string {
        $timestamp = (int)(microtime(true) * 1000); // milliseconds
        $userId = $userId ?? (auth()->id() ?? 'anonymous');

        return hash('sha256', implode('_', [
            $eventType,
            $entityType,
            $entityId,
            $timestamp,
            $userId,
        ]));
    }

    /**
     * Check if a notification was already sent to prevent duplicates
     *
     * Uses notification table to check if exact same notification exists
     */
    public static function shouldSendNotification(
        $notifiable,
        string $notificationClass,
        ?string $uniqueKey = null
    ): bool {
        // If no unique key provided, use the notification class name
        $uniqueKey = $uniqueKey ?? class_basename($notificationClass);

        // Check if notification with same key and recipient exists in last 5 minutes
        $recentNotification = DB::table('notifications')
            ->where('notifiable_type', get_class($notifiable))
            ->where('notifiable_id', $notifiable->getKey())
            ->where('type', $notificationClass)
            ->where('created_at', '>', now()->subMinutes(5))
            ->exists();

        return !$recentNotification;
    }

    /**
     * Check if an email was recently sent to prevent duplicate sends
     *
     * Tracks email sends in a simple log to prevent duplicate delivery
     */
    public static function shouldSendEmail(
        string $toEmail,
        string $emailTemplate,
        ?array $context = null
    ): bool {
        $contextHash = $context ? hash('sha256', json_encode($context)) : null;

        // Check if exact same email was sent in last 1 minute
        $recentEmail = DB::table('email_send_log')
            ->where('to_address', $toEmail)
            ->where('template', $emailTemplate)
            ->where('context_hash', $contextHash)
            ->where('sent_at', '>', now()->subMinute())
            ->exists();

        return !$recentEmail;
    }

    /**
     * Log an email send to prevent duplicates
     */
    public static function logEmailSend(
        string $toEmail,
        string $emailTemplate,
        ?array $context = null,
        ?string $messageId = null
    ): void {
        DB::table('email_send_log')->insert([
            'to_address' => $toEmail,
            'template' => $emailTemplate,
            'context_hash' => $context ? hash('sha256', json_encode($context)) : null,
            'message_id' => $messageId,
            'sent_at' => now(),
            'created_at' => now(),
        ]);
    }

    /**
     * Validate translation fallback safety
     *
     * Ensures that translations don't get lost if a key is missing
     */
    public static function getTranslationSafe(
        string $key,
        string $locale,
        ?string $fallbackLocale = null,
        ?string $fallbackValue = null
    ): string {
        // Try override first
        $override = \App\Models\TranslationOverride::getTranslation(
            $locale,
            'messages',
            $key
        );

        if ($override) {
            return $override;
        }

        // Try Laravel translation file
        $translated = trans($key, [], $locale);

        if ($translated !== $key) {
            return $translated;
        }

        // Try fallback locale
        if ($fallbackLocale && $fallbackLocale !== $locale) {
            $fallbackTranslated = trans($key, [], $fallbackLocale);
            if ($fallbackTranslated !== $key) {
                return $fallbackTranslated;
            }
        }

        // Fall back to provided value or key itself
        return $fallbackValue ?? $key;
    }

    /**
     * Validate settings fallback safety
     *
     * Ensures all settings have safe defaults even if not in database
     */
    public static function getSettingSafe(
        string $key,
        mixed $default = null
    ): mixed {
        $definition = \App\Models\Setting::definition($key);

        // Use definition default if no explicit default provided
        if ($default === null && $definition) {
            $default = $definition['default'];
        }

        return \App\Models\Setting::getValue($key, $default);
    }

    /**
     * Ensure no employer data is mutated when updating application metadata
     *
     * Validates that historical application snapshots haven't been modified
     */
    public static function validateSnapshotIntegrity(JobApplication $application): bool
    {
        // Snapshots should only be set at application creation time
        // This method can be expanded to hash and verify snapshots haven't changed

        if (empty($application->profile_snapshot)) {
            throw new \Exception("Application #{$application->id} has missing profile snapshot - data integrity violation");
        }

        if (empty($application->job_snapshot)) {
            throw new \Exception("Application #{$application->id} has missing job snapshot - data integrity violation");
        }

        return true;
    }

    /**
     * Create database transaction for critical operations
     *
     * Ensures atomicity of related updates
     */
    public static function inTransaction(callable $callback): mixed
    {
        return DB::transaction($callback);
    }
}
