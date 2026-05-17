<?php

namespace App\Jobs;

use App\Models\AccountDeletionRequest;
use App\Models\ApplicationComment;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\WorkerProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

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

        DB::transaction(function () use ($user, $deletionRequest): void {
            WorkerProfile::query()
                ->where('user_id', $user->id)
                ->update($this->profileAnonymizationPayload());

            JobApplication::query()
                ->where('worker_id', $user->id)
                ->update([
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
