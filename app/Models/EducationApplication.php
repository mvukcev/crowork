<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EducationApplication extends Model
{
    protected $fillable = [
        'education_id',
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

    public function education()
    {
        return $this->belongsTo(Education::class);
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }
}
