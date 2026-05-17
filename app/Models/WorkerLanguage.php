<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerLanguage extends Model
{
    protected $table = 'worker_languages';

    protected $fillable = [
        'worker_profile_id',
        'language',
        'level',
        'sort_order',
    ];

    public function workerProfile()
    {
        return $this->belongsTo(WorkerProfile::class);
    }
}
