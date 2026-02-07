<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Job extends Model
{
    protected $table = 'job_postings';

    protected $fillable = [
        'employer_id',
        'created_by_user_id',
        'title',
        'slug',
        'salary_min',
        'salary_max',
        'salary_currency',
        'salary_period',
        'description',
        'location_city',
        'category',
        'languages',
        'accommodation_provided',
        'accommodation_details',
        'contract_type',
        'start_date',
        'expires_at',
        'status',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'languages' => 'array',
            'accommodation_provided' => 'boolean',
            'start_date' => 'date',
            'expires_at' => 'datetime',
            'published_at' => 'datetime',
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
        return $this->employer?->company_name ?? $this->attributes['company_name'] ?? null;
    }
}
