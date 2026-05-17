<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerExperience extends Model
{
    protected $table = 'worker_experiences';

    protected $fillable = [
        'worker_profile_id',
        'job_title',
        'company_name',
        'country',
        'city',
        'start_date',
        'end_date',
        'is_current',
        'description',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_current' => 'boolean',
        ];
    }

    public function workerProfile()
    {
        return $this->belongsTo(WorkerProfile::class);
    }
}
