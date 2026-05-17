<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplication extends Model
{
    use SoftDeletes;
    public const STATUS_NEW = 'new';
    public const STATUS_REVIEWING = 'reviewing';
    public const STATUS_SHORTLISTED = 'shortlisted';
    public const STATUS_INTERVIEW = 'interview';
    public const STATUS_OFFER = 'offer';
    public const STATUS_HIRED = 'hired';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'job_id',
        'worker_id',
        'profile_snapshot',
        'job_snapshot',
        'message',
        'status',
        'internal_note',
        'score',
        'interview_at',
        'status_updated_at',
        'anonymized_at',
        'retention_reason',
        'retention_processed_at',
    ];

    protected $casts = [
        'profile_snapshot' => 'array',
        'job_snapshot' => 'array',
        'interview_at' => 'datetime',
        'status_updated_at' => 'datetime',
        'anonymized_at' => 'datetime',
        'retention_processed_at' => 'datetime',
        'candidate_tags' => 'array',
    ];

    public static function statusOptions(): array
    {
        return [
            self::STATUS_NEW => 'New',
            self::STATUS_REVIEWING => 'Reviewing',
            self::STATUS_SHORTLISTED => 'Shortlisted',
            self::STATUS_INTERVIEW => 'Interview',
            self::STATUS_OFFER => 'Offer',
            self::STATUS_HIRED => 'Hired',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }

    /**
     * Boot method to handle data integrity and audit logging
     */
    protected static function booted(): void
    {
        // Prevent snapshot modification after creation
        static::updating(function (self $application) {
            if ($application->isDirty('profile_snapshot') && !$application->wasRecentlyCreated) {
                throw new \Exception(
                    "Application profile snapshot cannot be modified after creation. "
                    . "Application ID: {$application->id}"
                );
            }

            if ($application->isDirty('job_snapshot') && !$application->wasRecentlyCreated) {
                throw new \Exception(
                    "Application job snapshot cannot be modified after creation. "
                    . "Application ID: {$application->id}"
                );
            }

            // Validate status transitions
            if ($application->isDirty('status')) {
                $previousStatus = $application->getOriginal('status');
                $newStatus = $application->status;

                \App\Services\DataIntegrityService::validateStatusTransition($application, $newStatus);
            }
        });

        // Log status changes and internal notes
        static::updated(function (self $application) {
            if ($application->wasChanged('status')) {
                $previousStatus = $application->getOriginal('status');
                \App\Services\DataIntegrityService::logApplicationStatusChange(
                    $application,
                    $previousStatus
                );
            }

            if ($application->wasChanged('internal_note')) {
                $previousNote = $application->getOriginal('internal_note');
                \App\Services\DataIntegrityService::logInternalNoteUpdate(
                    $application,
                    $previousNote ?? '',
                    $application->internal_note ?? ''
                );
            }
        });
    }
}
