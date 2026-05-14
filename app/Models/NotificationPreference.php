<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationPreference extends Model
{
    public const DIGEST_NONE = 'none';
    public const DIGEST_DAILY = 'daily';
    public const DIGEST_WEEKLY = 'weekly';

    protected $fillable = [
        'user_id',
        'category',
        'email_enabled',
        'database_enabled',
        'digest_frequency',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'database_enabled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
