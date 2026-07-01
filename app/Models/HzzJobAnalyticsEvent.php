<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HzzJobAnalyticsEvent extends Model
{
    public const EVENT_VIEW = 'view';
    public const EVENT_CTA_CLICK = 'cta_click';
    public const EVENT_EXTERNAL_OPEN = 'external_open';
    public const EVENT_APPLICATION_SENT = 'application_sent';

    protected $fillable = [
        'job_id',
        'user_id',
        'session_id',
        'event_type',
        'is_unique_view',
        'event_at',
        'meta',
    ];

    protected function casts(): array
    {
        return [
            'is_unique_view' => 'boolean',
            'event_at' => 'datetime',
            'meta' => 'array',
        ];
    }

    public function job()
    {
        return $this->belongsTo(Job::class, 'job_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
