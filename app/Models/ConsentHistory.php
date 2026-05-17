<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsentHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'consent_type',
        'consent_version',
        'source',
        'given',
        'accepted_at',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'given' => 'boolean',
        'accepted_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}