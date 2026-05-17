<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerEducation extends Model
{
    protected $table = 'worker_educations';

    protected $fillable = [
        'worker_profile_id',
        'institution',
        'degree',
        'field_of_study',
        'country',
        'city',
        'start_date',
        'end_date',
        'description',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function workerProfile()
    {
        return $this->belongsTo(WorkerProfile::class);
    }
}
