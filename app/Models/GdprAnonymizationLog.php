<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GdprAnonymizationLog extends Model
{
    use HasFactory;

    public const STATUS_STARTED = 'started';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_BLOCKED = 'blocked';

    protected $fillable = [
        'user_id',
        'target_type',
        'target_id',
        'action',
        'reason',
        'triggered_by',
        'triggered_by_admin_id',
        'status',
        'summary_json',
        'started_at',
        'completed_at',
        'failure_reason',
    ];

    protected $casts = [
        'summary_json' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function triggeredByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'triggered_by_admin_id');
    }
}
