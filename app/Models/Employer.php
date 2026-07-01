<?php

namespace App\Models;

use App\Services\ImageSanitizerService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Employer extends Model
{
    protected $fillable = [
        'user_id',
        'approved_at',
        'company_name',
        'oib',
        'company_display_name',
        'slug',
        'logo_path',
        'cover_image_path',
        'brand_color',
        'city',
        'country',
        'industry',
        'website',
        'contact_email',
        'contact_phone',
        'company_address',
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

        static::saved(function (Employer $employer): void {
            if ($employer->wasChanged('logo_path')) {
                $logoPath = trim((string) $employer->logo_path);
                if ($logoPath !== '') {
                    app(ImageSanitizerService::class)->sanitizeAndOptimize('public', $logoPath, 1200, 1200);
                }
            }

            if ($employer->wasChanged('cover_image_path')) {
                $coverPath = trim((string) $employer->cover_image_path);
                if ($coverPath !== '') {
                    app(ImageSanitizerService::class)->sanitizeAndOptimize('public', $coverPath, 2200, 1400);
                }
            }
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
            !empty($this->company_display_name),
            !empty($this->brand_color),
            !empty($this->city),
            !empty($this->country),
            !empty($this->industry),
            !empty($this->website),
            !empty($this->contact_email),
            !empty($this->contact_phone),
            !empty($this->company_address),
            !empty($this->description),
            !empty($this->logo_path),
            !empty($this->cover_image_path),
        ];

        $completed = count(array_filter($checks));

        return (int) round(($completed / count($checks)) * 100);
    }

    protected $searchable = [
        'company_name',
        'city',
        'industry',
        'description',
    ];

    public function scopeSearch($query, $term)
    {
        $columns = implode(',', $this->searchable);
        return $query->whereRaw("MATCH ({$columns}) AGAINST (? IN BOOLEAN MODE)", [$term]);
    }

    public function scopeRelevanceSort($query, $term)
    {
        return $query->orderByRaw("MATCH (company_name, city, industry, description) AGAINST (?) DESC", [$term]);
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->company_display_name ?: $this->company_name;
    }
}
