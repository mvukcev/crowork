<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SearchAnalyticsEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type',
        'user_id',
        'search_term',
        'search_type',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];
}