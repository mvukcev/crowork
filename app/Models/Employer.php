<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Employer extends Model
{
    protected $fillable = [
        'user_id',
        'approved_at',
        'company_name',
        'slug',
        'logo_path',
        'city',
        'country',
        'industry',
        'website',
        'description',
        'relocation_support',
        'accommodation_support',
        'require_approval_override',
        'applications_visibility_override',
        'can_export_applications_override',
        'visible_fields_override',
        'communication_language',
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

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::saving(function (Employer $employer) {
            if (! empty($employer->slug) || empty($employer->company_name)) {
                return;
            }

            $employer->slug = static::generateUniqueSlug($employer->company_name, $employer->id);
        });
    }

    protected static function generateUniqueSlug(string $companyName, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($companyName);
        $slug = $baseSlug !== '' ? $baseSlug : 'company';
        $counter = 2;

        while (static::query()
            ->where('slug', $slug)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists()) {
            $slug = ($baseSlug !== '' ? $baseSlug : 'company') . '-' . $counter;
            $counter++;
        }

        return $slug;
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
            !empty($this->country),
            !empty($this->industry),
            !empty($this->website),
            !empty($this->description),
            !empty($this->logo_path),
        ];

        $completed = count(array_filter($checks));

        return (int) round(($completed / count($checks)) * 100);
    }
}
