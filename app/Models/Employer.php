<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employer extends Model
{
    protected $fillable = [
        'user_id',
        'approved_at',
        'company_name',
        'logo_path',
        'city',
        'industry',
        'website',
        'description',
        'relocation_support',
        'accommodation_support',
        'require_approval_override',
        'applications_visibility_override',
        'can_export_applications_override',
        'visible_fields_override',
    ];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'relocation_support' => 'boolean',
            'accommodation_support' => 'boolean',
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

    public function getProfileReadinessAttribute(): int
    {
        $checks = [
            !empty($this->company_name),
            !empty($this->city),
            !empty($this->industry),
            !empty($this->website),
            !empty($this->description),
            !empty($this->logo_path),
        ];

        $completed = count(array_filter($checks));

        return (int) round(($completed / count($checks)) * 100);
    }
}
