<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BugReport extends Model
{
    public const STATUS_OPEN = 'open';
    public const STATUS_INVESTIGATING = 'investigating';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_REJECTED = 'rejected';

    protected $fillable = [
        'user_id',
        'reporter_email',
        'status',
        'page_uri',
        'description',
        'screenshot_path',
        'error_logs_snapshot',
        'error_logs_count',
        'admin_notes',
        'reported_at',
    ];

    protected function casts(): array
    {
        return [
            'error_logs_snapshot' => 'array',
            'reported_at' => 'datetime',
        ];
    }

    public static function statusOptions(): array
    {
        return [
            self::STATUS_OPEN => 'Open',
            self::STATUS_INVESTIGATING => 'Investigating',
            self::STATUS_RESOLVED => 'Resolved',
            self::STATUS_REJECTED => 'Rejected',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
