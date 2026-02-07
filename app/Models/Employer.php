<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employer extends Model
{
    protected $fillable = [
        'user_id',
        'approved_at',
        'company_name',
        'city',
        'require_approval_override',
        'applications_visibility_override',
        'can_export_applications_override',
        'visible_fields_override',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'require_approval_override' => 'boolean',
            'can_export_applications_override' => 'boolean',
            'visible_fields_override' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class);
    }
}
