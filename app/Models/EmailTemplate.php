<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $fillable = [
        'key',
        'locale',
        'subject',
        'body',
        'variables_preview',
    ];

    protected $casts = [
        'variables_preview' => 'array',
    ];
}
