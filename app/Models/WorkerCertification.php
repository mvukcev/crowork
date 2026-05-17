<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkerCertification extends Model
{
    protected $table = 'worker_certifications';

    protected $fillable = [
        'worker_profile_id',
        'name',
        'issuer',
        'issued_on',
        'expires_on',
        'credential_id',
        'credential_url',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'issued_on' => 'date',
            'expires_on' => 'date',
        ];
    }

    public function workerProfile()
    {
        return $this->belongsTo(WorkerProfile::class);
    }
}
