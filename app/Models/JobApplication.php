<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobApplication extends Model
{
    protected $fillable = [
        'job_id',
        'worker_id',
        'profile_snapshot',
        'message',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'profile_snapshot' => 'array',
        ];
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }
}
