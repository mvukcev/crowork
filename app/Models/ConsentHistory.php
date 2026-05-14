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
        'given',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}