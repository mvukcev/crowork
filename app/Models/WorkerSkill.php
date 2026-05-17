<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerSkill extends Model
{
    protected $table = 'worker_skills';

    protected $fillable = [
        'worker_profile_id',
        'name',
        'level',
        'sort_order',
    ];

    public function workerProfile()
    {
        return $this->belongsTo(WorkerProfile::class);
    }
}
