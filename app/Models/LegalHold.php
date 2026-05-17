<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LegalHold extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE = 'active';
    public const STATUS_RELEASED = 'released';

    protected $fillable = [
        'user_id',
        'target_type',
        'target_id',
        'reason',
        'status',
        'placed_by_admin_id',
        'released_by_admin_id',
        'placed_at',
        'released_at',
        'notes',
    ];

    protected $casts = [
        'placed_at' => 'datetime',
        'released_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function placedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'placed_by_admin_id');
    }

    public function releasedByAdmin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'released_by_admin_id');
    }
}
