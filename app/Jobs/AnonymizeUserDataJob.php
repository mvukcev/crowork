<?php

namespace App\Jobs;

use App\Models\AccountDeletionRequest;
use App\Models\ApplicationComment;
use App\Models\GdprAnonymizationLog;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\WorkerProfile;
use App\Services\LegalHoldService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class AnonymizeUserDataJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(
        public int $userId,
        public int $deletionRequestId
    ) {
        $this->onQueue('default');
    }

    public function handle(): void
    {
        $deletionRequest = AccountDeletionRequest::query()->find($this->deletionRequestId);
        $user = User::query()->find($this->userId);

        if (! $deletionRequest || ! $user) {
            return;
        }

        if ($deletionRequest->status !== AccountDeletionRequest::STATUS_PENDING) {
            return;
        }

        if ($deletionRequest->anonymization_scheduled_at && now()->lt($deletionRequest->anonymization_scheduled_at)) {
            return;
        }

        $log = GdprAnonymizationLog::query()->create([
            'user_id' => $user->id,
            'target_type' => User::class,
            'target_id' => (string) $user->id,
            'action' => 'user_account_anonymization',
            'reason' => $deletionRequest->reason ?: 'account_deletion_request',
            'triggered_by' => 'account_deletion_job',
            'status' => GdprAnonymizationLog::STATUS_STARTED,
            'started_at' => now(),
        ]);

        $legalHold = app(LegalHoldService::class)->activeHoldForTarget(User::class, $user->id, $user->id);
        if ($legalHold) {
            $log->update([
                'status' => GdprAnonymizationLog::STATUS_BLOCKED,
                'completed_at' => now(),
                'failure_reason' => 'Blocked by active legal hold',
                'summary_json' => [
                    'legal_hold_id' => $legalHold->id,
                    'legal_hold_reason' => $legalHold->reason,
                    'legal_hold_placed_at' => $legalHold->placed_at?->toIso8601String(),
                    'legal_hold_placed_by_admin_id' => $legalHold->placed_by_admin_id,
                ],
            ]);

            return;
        }

        try {
            $profile = WorkerProfile::query()
                ->where('user_id', $user->id)
                ->first(['id', 'photo_path']);

            $applications = JobApplication::query()
                ->where('worker_id', $user->id)
                ->get(['id', 'profile_snapshot']);

            $filePaths = $this->extractUploadPaths(
                $profile?->photo_path,
                $applications->pluck('profile_snapshot')->all(),
            );

            DB::transaction(function () use ($user, $deletionRequest): void {
            WorkerProfile::query()
                ->where('user_id', $user->id)
                ->update($this->profileAnonymizationPayload());

            $profileId = WorkerProfile::query()
                ->where('user_id', $user->id)
                ->value('id');

            if ($profileId) {
                foreach (['worker_experiences', 'worker_educations', 'worker_certifications', 'worker_references', 'worker_skills', 'worker_languages'] as $table) {
                    if (Schema::hasTable($table)) {
                        DB::table($table)->where('worker_profile_id', $profileId)->delete();
                    }
                }
            }

            JobApplication::query()
                ->where('worker_id', $user->id)
                ->update([
                    'profile_snapshot' => json_encode($this->anonymizedSnapshot(), JSON_UNESCAPED_UNICODE),
                    'message' => null,
                    'internal_note' => null,
                ]);

            ApplicationComment::query()
                ->where('user_id', $user->id)
                ->update(['comment' => '[removed by privacy request]']);

            DB::table('notifications')
                ->where('notifiable_type', User::class)
                ->where('notifiable_id', $user->id)
                ->delete();

            $user->anonymize();
            $user->forceFill([
                'pending_deletion' => false,
                'deletion_requested_at' => null,
                'anonymization_scheduled_at' => null,
            ])->save();
            $user->delete();

            $deletionRequest->update([
                'status' => AccountDeletionRequest::STATUS_COMPLETED,
                'completed_at' => now(),
            ]);
            });

            $deletedFiles = $this->deleteKnownUploadPaths($filePaths);

            $log->update([
                'status' => GdprAnonymizationLog::STATUS_COMPLETED,
                'completed_at' => now(),
                'summary_json' => [
                    'deletion_request_id' => $deletionRequest->id,
                    'job_applications_scrubbed' => JobApplication::query()->where('worker_id', $user->id)->count(),
                    'comments_scrubbed' => ApplicationComment::query()->where('user_id', $user->id)->count(),
                    'files_deleted' => $deletedFiles,
                ],
            ]);
        } catch (\Throwable $exception) {
            $log->update([
                'status' => GdprAnonymizationLog::STATUS_FAILED,
                'completed_at' => now(),
                'failure_reason' => 'Anonymization failed. See application logs for details.',
            ]);

            throw $exception;
        }
    }

    /**
     * @param array<int, mixed> $snapshots
     * @return array<int, string>
     */
    private function extractUploadPaths(?string $profilePhotoPath, array $snapshots): array
    {
        $paths = [];

        if (is_string($profilePhotoPath) && trim($profilePhotoPath) !== '') {
            $paths[] = $profilePhotoPath;
        }

        foreach ($snapshots as $snapshot) {
            if (! is_array($snapshot)) {
                continue;
            }

            foreach (['cv_path', 'photo_path'] as $key) {
                $value = $snapshot[$key] ?? null;
                if (is_string($value) && trim($value) !== '') {
                    $paths[] = $value;
                }
            }
        }

        return array_values(array_unique($paths));
    }

    /**
     * @param array<int, string> $paths
     */
    private function deleteKnownUploadPaths(array $paths): int
    {
        $deleted = 0;

        foreach ($paths as $path) {
            if (! $this->isSafeUploadPath($path)) {
                continue;
            }

            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                $deleted++;
                continue;
            }

            if (Storage::disk('local')->exists($path)) {
                Storage::disk('local')->delete($path);
                $deleted++;
            }
        }

        return $deleted;
    }

    private function isSafeUploadPath(string $path): bool
    {
        $normalized = ltrim($path, '/');

        if (str_contains($normalized, '..')) {
            return false;
        }

        foreach (['worker-photos/', 'worker-cv/', 'uploads/cv/', 'applications/cv/'] as $prefix) {
            if (str_starts_with($normalized, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, mixed>
     */
    private function anonymizedSnapshot(): array
    {
        return [
            'retained_anonymized' => true,
            'retention_reason' => 'account_deletion_request',
            'retention_processed_at' => now()->toIso8601String(),
            'skills_count' => 0,
            'languages_count' => 0,
            'experience_entries_count' => 0,
            'education_entries_count' => 0,
        ];
    }

    private function profileAnonymizationPayload(): array
    {
        $payload = [];
        $columns = [
            'first_name' => 'Deleted',
            'last_name' => 'User',
            'education_summary' => null,
            'work_experience' => null,
            'recommendations' => null,
            'professional_summary' => null,
            'certifications' => null,
            'photo_path' => null,
            'skills' => json_encode([]),
            'languages' => json_encode([]),
        ];

        foreach ($columns as $column => $value) {
            if (Schema::hasColumn('worker_profiles', $column)) {
                $payload[$column] = $value;
            }
        }

        return $payload;
    }
}
