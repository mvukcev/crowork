<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
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
    ];

    protected function casts(): array
    {
        return [
            'profile_snapshot' => 'array',
            'job_snapshot' => 'array',
            'interview_at' => 'datetime',
            'status_updated_at' => 'datetime',
        ];
    }

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
}
