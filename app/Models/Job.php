<?php

namespace App\Models;

use App\Services\Hzz\HzzApplicationContactParser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Job extends Model
{
    protected $table = 'job_postings';

    protected $fillable = [
        'employer_id',
        'created_by_user_id',
        'source_type',
        'source_external_id',
        'source_url',
        'source_logo_url',
        'source_imported_at',
        'external_company_name',
        'source_system',
        'source_reference',
        'source_payload',
        'hzz_is_official',
        'hzz_apply_email',
        'hzz_apply_contact_type',
        'hzz_apply_contact_raw',
        'hzz_apply_url',
        'hzz_apply_method_available',
        'hzz_legal_notice',
        'title',
        'slug',
        'salary_min',
        'salary_max',
        'salary_currency',
        'salary_period',
        'description',
        'responsibilities',
        'requirements',
        'benefits',
        'location_city',
        'category',
        'languages',
        'accommodation_provided',
        'accommodation_details',
        'visa_support',
        'visa_support_details',
        'contract_type',
        'experience_level',
        'education_required',
        'contract_duration',
        'start_date',
        'start_flexibility',
        'positions_available',
        'working_hours',
        'shift_details',
        'application_instructions',
        'is_featured',
        'is_urgent',
        'expires_at',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'languages' => 'array',
            'accommodation_provided' => 'boolean',
            'visa_support' => 'boolean',
            'is_featured' => 'boolean',
            'is_urgent' => 'boolean',
            'positions_available' => 'integer',
            'start_date' => 'date',
            'expires_at' => 'datetime',
            'published_at' => 'datetime',
            'source_payload' => 'array',
            'source_imported_at' => 'datetime',
            'hzz_is_official' => 'boolean',
            'hzz_apply_method_available' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    protected static function booted(): void
    {
        static::creating(function ($job) {
            if (empty($job->slug)) {
                $job->slug = static::generateUniqueSlug($job->title);
            }
        });

        static::updating(function ($job) {
            if ($job->isDirty('title') && empty($job->slug)) {
                $job->slug = static::generateUniqueSlug($job->title);
            }
        });

        static::saving(function ($job) {
            if (! $job->isHzzOfficial()) {
                return;
            }

            $parser = app(HzzApplicationContactParser::class);

            $parseInput = implode("\n\n", array_filter([
                (string) ($job->application_instructions ?? ''),
                strip_tags((string) ($job->description ?? '')),
                (string) ($job->hzz_legal_notice ?? ''),
            ], fn ($value) => trim((string) $value) !== ''));

            $parsed = $parser->parse($parseInput, $job->source_url ?? null);

            if (empty($job->hzz_apply_email) && ! empty($parsed['email'])) {
                $job->hzz_apply_email = $parsed['email'];
            }

            if (empty($job->hzz_apply_url) && ! empty($parsed['apply_url'])) {
                $job->hzz_apply_url = $parsed['apply_url'];
            }

            $job->hzz_apply_contact_raw = $job->hzz_apply_contact_raw ?: $parsed['contact_raw'];
            $job->hzz_apply_contact_type = $parsed['contact_type'] ?? 'unknown';
            $job->hzz_apply_method_available = ! empty($job->hzz_apply_email);
            $job->source_system = $job->source_system ?: 'hzz';
            $job->hzz_is_official = true;
        });
    }

    protected static function generateUniqueSlug(string $title): string
    {
        $slug = Str::slug($title);
        $originalSlug = $slug;
        $counter = 2;

        while (static::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->whereNotNull('published_at');
    }

    public function scopeActive($query)
    {
        return $query->published()
                    ->where(function($q) {
                        $q->whereNull('expires_at')
                          ->orWhere('expires_at', '>', now());
                    })
                    ->whereNotIn('status', ['delisted', 'expired']);
    }

    public function employer()
    {
        return $this->belongsTo(Employer::class);
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function applications()
    {
        return $this->hasMany(JobApplication::class);
    }

    /**
     * Get formatted salary range for display
     */
    public function getFormattedSalaryAttribute(): string
    {
        $currencySymbol = $this->salary_currency === 'EUR' ? '€' : $this->salary_currency;
        $periodText = $this->salary_period === 'hour' ? 'hour' : 'month';
        
        if ($this->salary_min && $this->salary_max) {
            return $currencySymbol . number_format($this->salary_min, 0, '.', ',') . ' – ' . 
                   $currencySymbol . number_format($this->salary_max, 0, '.', ',') . ' / ' . $periodText;
        } elseif ($this->salary_min) {
            return 'From ' . $currencySymbol . number_format($this->salary_min, 0, '.', ',') . ' / ' . $periodText;
        } elseif ($this->salary_max) {
            return 'Up to ' . $currencySymbol . number_format($this->salary_max, 0, '.', ',') . ' / ' . $periodText;
        }
        
        return 'Salary: Not specified';
    }

    /**
     * Get company name from employer relationship or fallback to direct attribute
     */
    public function getCompanyNameAttribute(): ?string
    {
        return $this->employer?->company_name
            ?? $this->attributes['external_company_name']
            ?? ($this->attributes['company_name'] ?? null);
    }

    public function isImportedFromHzz(): bool
    {
        return $this->source_type === 'hzz';
    }

    public function getLocationAttribute(): ?string
    {
        return $this->location_city;
    }

    public function setLocationAttribute(?string $value): void
    {
        $this->attributes['location_city'] = $value;
    }

    public function getJobTypeAttribute(): ?string
    {
        return $this->contract_type;
    }

    public function setJobTypeAttribute(?string $value): void
    {
        $this->attributes['contract_type'] = $value;
    }

    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'published';
    }

    public function setIsActiveAttribute($value): void
    {
        $isActive = filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false;

        $this->attributes['status'] = $isActive ? 'published' : 'draft';

        if (! $isActive) {
            $this->attributes['published_at'] = null;
        }
    }

    public function isHzzOfficial(): bool
    {
        return (bool) $this->hzz_is_official || strtolower((string) $this->source_system) === 'hzz';
    }

    public function canApplyViaCroWork(): bool
    {
        return $this->isHzzOfficial() && filled((string) $this->hzz_apply_email);
    }
}
