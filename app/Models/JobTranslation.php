<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobTranslation extends Model
{
    protected $fillable = [
        'job_id',
        'locale',
        'source_locale',
        'provider',
        'status',
        'source_hash',
        'content',
        'last_error',
        'translated_at',
    ];

    protected function casts(): array
    {
        return [
            'content' => 'array',
            'translated_at' => 'datetime',
        ];
    }

    public function job()
    {
        return $this->belongsTo(Job::class);
    }
}
