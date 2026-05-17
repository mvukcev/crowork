<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GdprBreachIncident extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';
    public const STATUS_INVESTIGATING = 'investigating';
    public const STATUS_CONTAINED = 'contained';
    public const STATUS_RESOLVED = 'resolved';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'title',
        'severity',
        'status',
        'detected_at',
        'reported_at',
        'summary',
        'affected_data_categories',
        'affected_user_count',
        'authority_notification_required',
        'users_notification_required',
        'owner_admin_id',
        'internal_notes',
        'resolved_at',
    ];

    protected $casts = [
        'detected_at' => 'datetime',
        'reported_at' => 'datetime',
        'resolved_at' => 'datetime',
        'affected_data_categories' => 'array',
        'authority_notification_required' => 'boolean',
        'users_notification_required' => 'boolean',
    ];

    public function ownerAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_admin_id');
    }
}
