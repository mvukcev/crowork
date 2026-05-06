<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AbuseReport extends Model
{
    protected $fillable = [
        'type',
        'target_id',
        'reason',
        'message',
        'user_id',
        'ip_address',
        'user_agent',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
