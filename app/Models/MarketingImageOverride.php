<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingImageOverride extends Model
{
    protected $fillable = [
        'key',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'width',
        'height',
        'alt_text',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];
}
