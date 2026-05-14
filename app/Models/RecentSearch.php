<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RecentSearch extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'search_term',
        'search_type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}