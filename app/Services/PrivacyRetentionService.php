<?php

namespace App\Services;

use App\Models\Job;
use App\Models\JobApplication;
use App\Models\Setting;
use App\Models\User;
use App\Models\GdprAnonymizationLog;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Throwable;

class PrivacyRetentionService
{
    public const SECTION_REJECTED_APPLICATIONS = 'rejected-applications';
    public const SECTION_INACTIVE_WORKERS = 'inactive-workers';
    public const SECTION_INACTIVE_EMPLOYERS = 'inactive-employers';
    public const SECTION_NOTIFICATIONS = 'notifications';

    public function __construct(
        private readonly AccountDeletionService $accountDeletionService,
        private readonly LegalHoldService $legalHoldService,
    ) {
    }

    /**
     * @param array<int, string>|null $onlySections
     * @return array<string, mixed>
     */
    public function run(?array $onlySections = null, ?bool $dryRun = null): array
    {
        $settings = $this->settings();
        $dryRun = $dryRun ?? $settings['dry_run_mode'];
        $sections = $this->resolveSections($onlySections);

        $summary = [
            'started_at' => now()->toIso8601String(),
            'dry_run' => $dryRun,
            'settings' => $settings,
            'sections' => [],
            'errors' => [],
        ];

        Log::channel('stack')->info('privacy.retention.started', [
            'dry_run' => $dryRun,
            'sections' => $sections,
        ]);

        foreach ($sections as $section) {
            try {
                $summary['sections'][$section] = match ($section) {
                    self::SECTION_REJECTED_APPLICATIONS => $this->processRejectedApplications((int) $settings['rejected_applications_retention_months'], $dryRun),
                    self::SECTION_INACTIVE_WORKERS => $this->processInactiveWorkers((int) $settings['inactive_worker_retention_months'], $dryRun),
                    self::SECTION_INACTIVE_EMPLOYERS => $this->processInactiveEmployers((int) $settings['inactive_employer_retention_months'], $dryRun),
                    self::SECTION_NOTIFICATIONS => $this->processNotifications((int) $settings['notification_retention_months'], $dryRun),
                    default => throw new \InvalidArgumentException('Unsupported retention section: ' . $section),
                };
            } catch (Throwable $exception) {
                $summary['sections'][$section] = [
                    'scanned' => 0,
                    'eligible' => 0,
                    'processed' => 0,
                    'skipped' => 0,
                    'errors' => 1,
                    'dry_run' => $dryRun,
                    'notes' => ['Section failed'],
                ];
                $summary['errors'][] = [
                    'section' => $section,
                    'message' => $exception->getMessage(),
                ];

                Log::channel('stack')->error('privacy.retention.section_failed', [
                    'section' => $section,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        $summary['finished_at'] = now()->toIso8601String();

        Log::channel('stack')->info('privacy.retention.finished', [
            'dry_run' => $dryRun,
            'section_totals' => collect($summary['sections'])->map(fn (array $entry): array => [
                'processed' => (int) ($entry['processed'] ?? 0),
                'errors' => (int) ($entry['errors'] ?? 0),
            ])->toArray(),
            'errors' => count($summary['errors']),
        ]);

        return $summary;
    }

    /**
     * @return array<int, string>
     */
    public function allowedSections(): array
    {
        return [
            self::SECTION_REJECTED_APPLICATIONS,
            self::SECTION_INACTIVE_WORKERS,
            self::SECTION_INACTIVE_EMPLOYERS,
            self::SECTION_NOTIFICATIONS,
        ];
    }

    /**
     * @param array<int, string>|null $onlySections
     * @return array<int, string>
     */
    private function resolveSections(?array $onlySections): array
    {
        $allowed = $this->allowedSections();

        if ($onlySections === null || $onlySections === []) {
            return $allowed;
        }

        $normalized = collect($onlySections)
            ->map(fn (string $value): string => strtolower(trim($value)))
            ->filter(fn (string $value): bool => $value !== '')
            ->unique()
            ->values()
            ->all();

        $invalid = array_values(array_diff($normalized, $allowed));
        if ($invalid !== []) {
            throw new \InvalidArgumentException('Unsupported --only section(s): ' . implode(', ', $invalid));
        }

        return $normalized;
    }

    /**
     * @return array<string, mixed>
     */
    private function settings(): array
    {
        return [
            'enable_retention_automation' => Setting::getBool('enable_retention_automation', false),
            'dry_run_mode' => Setting::getBool('dry_run_mode', true),
            'rejected_applications_retention_months' => Setting::getInt('rejected_applications_retention_months', 6),
            'inactive_worker_retention_months' => Setting::getInt('inactive_worker_retention_months', 24),
            'inactive_employer_retention_months' => Setting::getInt('inactive_employer_retention_months', 36),
            'notification_retention_months' => Setting::getInt('notification_retention_months', 12),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function processRejectedApplications(int $months, bool $dryRun): array
    {
        $now = now();
        $cutoff = now()->subMonthsNoOverflow(max(1, $months))->startOfDay();

        $baseQuery = JobApplication::query()
            ->where('status', JobApplication::STATUS_REJECTED);

        $eligibleQuery = JobApplication::query()
            ->where('status', JobApplication::STATUS_REJECTED)
            ->whereNull('anonymized_at')
            ->where(function ($query) use ($cutoff): void {
                $query->where('status_updated_at', '<=', $cutoff)
                    ->orWhere(function ($fallbackQuery) use ($cutoff): void {
                        $fallbackQuery->whereNull('status_updated_at')
                            ->where('created_at', '<=', $cutoff);
                    });
            });

        $scanned = (clone $baseQuery)->count();
        $eligible = (clone $eligibleQuery)->count();

        $processed = 0;
        $errors = 0;
        $legalHoldSkipped = 0;

        if (! $dryRun && $eligible > 0) {
            (clone $eligibleQuery)
                ->select(['id', 'worker_id', 'profile_snapshot'])
                ->chunkById(100, function ($applications) use (&$processed, &$errors, &$legalHoldSkipped, $now): void {
                    foreach ($applications as $application) {
                        $hasLegalHold = $this->legalHoldService->hasActiveHoldForTarget(
                            JobApplication::class,
                            $application->id,
                            $application->worker_id ? (int) $application->worker_id : null
                        );

                        if ($hasLegalHold) {
                            $legalHoldSkipped++;

                            GdprAnonymizationLog::query()->create([
                                'user_id' => $application->worker_id,
                                'target_type' => JobApplication::class,
                                'target_id' => (string) $application->id,
                                'action' => 'application_retention_anonymization',
                                'reason' => 'rejected_application_retention',
                                'triggered_by' => 'retention_automation',
                                'status' => GdprAnonymizationLog::STATUS_BLOCKED,
                                'started_at' => now(),
                                'completed_at' => now(),
                                'failure_reason' => 'Blocked by active legal hold',
                            ]);

                            continue;
                        }

                        $log = GdprAnonymizationLog::query()->create([
                            'user_id' => $application->worker_id,
                            'target_type' => JobApplication::class,
                            'target_id' => (string) $application->id,
                            'action' => 'application_retention_anonymization',
                            'reason' => 'rejected_application_retention',
                            'triggered_by' => 'retention_automation',
                            'status' => GdprAnonymizationLog::STATUS_STARTED,
                            'started_at' => now(),
                        ]);

                        try {
                            $snapshot = is_array($application->profile_snapshot) ? $application->profile_snapshot : [];

                            DB::table('job_applications')
                                ->where('id', $application->id)
                                ->update([
                                    'profile_snapshot' => json_encode($this->anonymizedRetentionSnapshot($snapshot, $now), JSON_UNESCAPED_UNICODE),
                                    'message' => null,
                                    'internal_note' => null,
                                    'anonymized_at' => $now,
                                    'retention_reason' => 'rejected_application_retention',
                                    'retention_processed_at' => $now,
                                    'updated_at' => $now,
                                ]);

                            $processed++;

                            $log->update([
                                'status' => GdprAnonymizationLog::STATUS_COMPLETED,
                                'completed_at' => now(),
                                'summary_json' => [
                                    'application_id' => $application->id,
                                    'retention_reason' => 'rejected_application_retention',
                                ],
                            ]);
                        } catch (Throwable $exception) {
                            $errors++;

                            $log->update([
                                'status' => GdprAnonymizationLog::STATUS_FAILED,
                                'completed_at' => now(),
                                'failure_reason' => $exception->getMessage(),
                            ]);

                            Log::channel('stack')->warning('privacy.retention.rejected_application_failed', [
                                'application_id' => $application->id,
                                'error' => $exception->getMessage(),
                            ]);
                        }
                    }
                });
        }

        return [
            'scanned' => $scanned,
            'eligible' => $eligible,
            'processed' => $dryRun ? 0 : $processed,
            'skipped' => max(0, $scanned - $eligible) + $legalHoldSkipped,
            'errors' => $errors,
            'dry_run' => $dryRun,
            'notes' => [
                'cutoff' => $cutoff->toDateString(),
                'would_process' => $dryRun ? $eligible : 0,
                'legal_hold_skipped' => $legalHoldSkipped,
            ],
        ];
    }

    /**
     * @param array<string, mixed> $snapshot
     * @return array<string, mixed>
     */
    private function anonymizedRetentionSnapshot(array $snapshot, Carbon $now): array
    {
        return [
            'retained_anonymized' => true,
            'retention_reason' => 'rejected_application_retention',
            'retention_processed_at' => $now->toIso8601String(),
            'skills_count' => count(array_filter(Arr::wrap($snapshot['skills'] ?? []))),
            'languages_count' => count(array_filter(Arr::wrap($snapshot['languages'] ?? []))),
            'experience_entries_count' => count(array_filter(Arr::wrap($snapshot['structured_experiences'] ?? []))),
            'education_entries_count' => count(array_filter(Arr::wrap($snapshot['structured_educations'] ?? []))),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function processInactiveWorkers(int $months, bool $dryRun): array
    {
        $cutoff = now()->subMonthsNoOverflow(max(1, $months))->startOfDay();

        $baseQuery = User::query()
            ->where('role', User::ROLE_WORKER)
            ->whereNull('deleted_at');

        $eligibleQuery = User::query()
            ->where('role', User::ROLE_WORKER)
            ->whereNull('deleted_at')
            ->where(function ($query): void {
                $query->where('pending_deletion', false)
                    ->orWhereNull('pending_deletion');
            })
            ->whereRaw('COALESCE(last_login_at, updated_at) <= ?', [$cutoff]);

        $scanned = (clone $baseQuery)->count();
        $eligible = (clone $eligibleQuery)->count();

        $processed = 0;
        $errors = 0;

        if (! $dryRun && $eligible > 0) {
            (clone $eligibleQuery)
                ->orderBy('id')
                ->chunkById(50, function ($users) use (&$processed, &$errors): void {
                    foreach ($users as $user) {
                        if ($this->legalHoldService->hasActiveHoldForUser($user->id)) {
                            continue;
                        }

                        try {
                            $this->accountDeletionService->requestDeletion($user, 'gdpr_retention_inactive_worker');
                            $processed++;
                        } catch (Throwable $exception) {
                            $errors++;
                            Log::channel('stack')->warning('privacy.retention.inactive_worker_failed', [
                                'user_id' => $user->id,
                                'error' => $exception->getMessage(),
                            ]);
                        }
                    }
                });
        }

        return [
            'scanned' => $scanned,
            'eligible' => $eligible,
            'processed' => $dryRun ? 0 : $processed,
            'skipped' => max(0, $scanned - $eligible),
            'errors' => $errors,
            'dry_run' => $dryRun,
            'notes' => [
                'cutoff' => $cutoff->toDateString(),
                'would_process' => $dryRun ? $eligible : 0,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function processInactiveEmployers(int $months, bool $dryRun): array
    {
        $cutoff = now()->subMonthsNoOverflow(max(1, $months))->startOfDay();

        $baseQuery = User::query()
            ->where('role', User::ROLE_EMPLOYER)
            ->whereNull('deleted_at');

        $eligibleQuery = User::query()
            ->where('role', User::ROLE_EMPLOYER)
            ->whereNull('deleted_at')
            ->where(function ($query): void {
                $query->where('pending_deletion', false)
                    ->orWhereNull('pending_deletion');
            })
            ->whereRaw('COALESCE(last_login_at, updated_at) <= ?', [$cutoff])
            ->with('employer:id,user_id');

        $scanned = (clone $baseQuery)->count();
        $eligible = (clone $eligibleQuery)->count();

        $skipped = 0;
        $requiresManualReview = 0;

        (clone $eligibleQuery)
            ->orderBy('id')
            ->chunkById(50, function ($users) use (&$skipped, &$requiresManualReview): void {
                foreach ($users as $user) {
                    $employer = $user->employer;

                    if (! $employer) {
                        $skipped++;
                        continue;
                    }

                    $hasActiveJobs = Job::query()
                        ->where('employer_id', $employer->id)
                        ->active()
                        ->exists();

                    if ($hasActiveJobs) {
                        $skipped++;
                        continue;
                    }

                    $hasUnresolvedApplications = JobApplication::query()
                        ->whereHas('job', fn ($jobQuery) => $jobQuery->where('employer_id', $employer->id))
                        ->whereNotIn('status', [JobApplication::STATUS_REJECTED, JobApplication::STATUS_HIRED])
                        ->exists();

                    if ($hasUnresolvedApplications) {
                        $skipped++;
                        continue;
                    }

                    $requiresManualReview++;
                }
            });

        return [
            'scanned' => $scanned,
            'eligible' => $eligible,
            'processed' => 0,
            'skipped' => max(0, $scanned - $eligible) + $skipped + $requiresManualReview,
            'errors' => 0,
            'dry_run' => $dryRun,
            'notes' => [
                'cutoff' => $cutoff->toDateString(),
                'manual_review_required' => $requiresManualReview,
                'policy' => 'inactive_employers_are_reported_only; no automated deletion/anonymization is executed.',
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function processNotifications(int $months, bool $dryRun): array
    {
        $cutoff = now()->subMonthsNoOverflow(max(1, $months))->startOfDay();

        $notificationsScanned = Schema::hasTable('notifications')
            ? DB::table('notifications')->where('created_at', '<=', $cutoff)->count()
            : 0;

        $digestsScanned = Schema::hasTable('notification_digests')
            ? DB::table('notification_digests')->whereDate('scheduled_for', '<=', $cutoff->toDateString())->count()
            : 0;

        $emailLogsScanned = Schema::hasTable('email_send_log')
            ? DB::table('email_send_log')->where('sent_at', '<=', $cutoff)->count()
            : 0;

        $eligible = $notificationsScanned + $digestsScanned + $emailLogsScanned;

        $processed = 0;
        if (! $dryRun && $eligible > 0) {
            if (Schema::hasTable('notifications')) {
                $processed += DB::table('notifications')->where('created_at', '<=', $cutoff)->delete();
            }

            if (Schema::hasTable('notification_digests')) {
                $processed += DB::table('notification_digests')->whereDate('scheduled_for', '<=', $cutoff->toDateString())->delete();
            }

            if (Schema::hasTable('email_send_log')) {
                $processed += DB::table('email_send_log')->where('sent_at', '<=', $cutoff)->delete();
            }
        }

        return [
            'scanned' => $eligible,
            'eligible' => $eligible,
            'processed' => $dryRun ? 0 : $processed,
            'skipped' => 0,
            'errors' => 0,
            'dry_run' => $dryRun,
            'notes' => [
                'cutoff' => $cutoff->toDateString(),
                'would_process' => $dryRun ? $eligible : 0,
                'notifications' => $notificationsScanned,
                'notification_digests' => $digestsScanned,
                'email_send_log' => $emailLogsScanned,
            ],
        ];
    }
}
