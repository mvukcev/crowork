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
        'admin_notes',
    ];

    public function reportedJob()
    {
        return $this->belongsTo(Job::class, 'target_id');
    }

    public function reportedEmployer()
    {
        return $this->belongsTo(Employer::class, 'target_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
