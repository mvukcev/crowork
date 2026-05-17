<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerReference extends Model
{
    protected $table = 'worker_references';

    protected $fillable = [
        'worker_profile_id',
        'full_name',
        'position',
        'company',
        'contact_email',
        'contact_phone',
        'notes',
        'sort_order',
    ];

    public function workerProfile()
    {
        return $this->belongsTo(WorkerProfile::class);
    }
}
