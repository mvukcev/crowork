<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GdprExportLog extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';

    protected $fillable = [
        'user_id',
        'requested_by_user_id',
        'requested_by_admin_id',
        'export_type',
        'status',
        'file_path',
        'generated_at',
        'downloaded_at',
        'expires_at',
        'ip_address',
        'user_agent',
        'failure_reason',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'downloaded_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function requestedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_user_id');
    }

    public function requestedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by_admin_id');
    }
}
