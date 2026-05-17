<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GdprDataRequest extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'open';
    public const STATUS_IN_REVIEW = 'in_review';
    public const STATUS_WAITING_FOR_USER = 'waiting_for_user';
    public const STATUS_FULFILLED = 'fulfilled';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_CLOSED = 'closed';

    protected $fillable = [
        'requester_user_id',
        'requester_email',
        'request_type',
        'status',
        'priority',
        'due_at',
        'assigned_admin_id',
        'internal_notes',
        'resolution_summary',
        'fulfilled_at',
        'closed_at',
    ];

    protected $casts = [
        'due_at' => 'datetime',
        'fulfilled_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function requesterUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requester_user_id');
    }

    public function assignedAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_admin_id');
    }
}
