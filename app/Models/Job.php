<?php

namespace App\Models;

use App\Jobs\TranslateJobPosting;
use App\Services\Hzz\HzzApplicationContactParser;
use App\Services\ImageSanitizerService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Job extends Model
{
    public const TRANSLATABLE_FIELDS = [
        'title',
        'description',
        'responsibilities',
        'requirements',
        'benefits',
        'accommodation_details',
        'visa_support_details',
        'working_hours',
        'shift_details',
        'application_instructions',
        'hzz_legal_notice',
    ];

    protected $table = 'job_postings';

    protected $fillable = [
        'employer_id',
        'created_by_user_id',
        'source_type',
        'source_external_id',
        'source_url',
        'source_logo_url',
        'source_imported_at',
        'preview_token',
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
        'cover_image_path',
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

            if (empty($job->preview_token)) {
                $job->preview_token = static::generateUniquePreviewToken();
            }
        });

        static::updating(function ($job) {
            if ($job->isDirty('title') && empty($job->slug)) {
                $job->slug = static::generateUniqueSlug($job->title);
            }
        });

        static::saving(function ($job) {
            $job->source_system = filled((string) $job->source_system)
                ? trim((string) $job->source_system)
                : null;

            $job->hzz_is_official = (bool) $job->hzz_is_official;
            $job->hzz_apply_contact_type = filled(trim((string) $job->hzz_apply_contact_type))
                ? trim((string) $job->hzz_apply_contact_type)
                : 'unknown';
            $job->hzz_apply_method_available = (bool) $job->hzz_apply_method_available;

            if ($job->status === 'published' && empty($job->published_at)) {
                $job->published_at = now();
            }

            if (! $job->isHzzOfficial()) {
                return;
            }

            $parser = app(HzzApplicationContactParser::class);

            $parseInput = implode("\n\n", array_filter([
                (string) ($job->hzz_apply_contact_raw ?? ''),
                (string) ($job->application_instructions ?? ''),
                strip_tags((string) ($job->description ?? '')),
                strip_tags((string) ($job->responsibilities ?? '')),
                strip_tags((string) ($job->requirements ?? '')),
                strip_tags((string) ($job->benefits ?? '')),
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
            if (filled($job->hzz_apply_email)) {
                $job->hzz_apply_contact_type = 'email';
            } elseif (filled($job->hzz_apply_url)) {
                $job->hzz_apply_contact_type = 'external_url';
            } else {
                $job->hzz_apply_contact_type = $parsed['contact_type'] ?? 'unknown';
            }
            $job->hzz_apply_method_available = ! empty($job->hzz_apply_email);
            $job->source_system = $job->source_system ?: 'hzz';
            $job->hzz_is_official = true;
        });

        static::saved(function (Job $job): void {
            if (! $job->wasChanged('cover_image_path')) {
                return;
            }

            $coverPath = trim((string) $job->cover_image_path);
            if ($coverPath === '') {
                return;
            }

            app(ImageSanitizerService::class)->sanitizeAndOptimize('public', $coverPath, 2200, 1400);
        });

        static::saved(function (Job $job): void {
            if (
                ! config('services.azure_translator.enabled')
                || ! setting('job_translation_enabled', true)
                || $job->status !== 'published'
            ) {
                return;
            }

            $translationRelevantChange = $job->wasRecentlyCreated
                || $job->wasChanged(self::TRANSLATABLE_FIELDS)
                || $job->wasChanged(['status', 'published_at']);

            if (! $translationRelevantChange) {
                return;
            }

            TranslateJobPosting::dispatch($job->id, 'en')
                ->onQueue($job->translationQueueName())
                ->afterCommit();
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

    protected static function generateUniquePreviewToken(): string
    {
        do {
            $token = Str::random(64);
        } while (static::where('preview_token', $token)->exists());

        return $token;
    }

    public function ensurePreviewToken(): string
    {
        if (filled((string) $this->preview_token)) {
            return (string) $this->preview_token;
        }

        $this->preview_token = static::generateUniquePreviewToken();
        $this->save();

        return (string) $this->preview_token;
    }

    public function getPreviewUrlAttribute(): ?string
    {
        $token = trim((string) $this->preview_token);

        if ($token === '') {
            return null;
        }

        return route('jobs.preview.shared', ['token' => $token]);
    }

    public function getEmployerDisplayNameAttribute(): ?string
    {
        foreach ([
            $this->employer?->company_display_name,
            $this->employer?->company_name,
            $this->external_company_name,
        ] as $name) {
            $name = trim((string) $name);

            if ($name !== '') {
                return $name;
            }
        }

        return null;
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

    public function translations()
    {
        return $this->hasMany(JobTranslation::class);
    }

    /**
     * @return array<string, string>
     */
    public function translationSourceContent(): array
    {
        $content = [];

        foreach (self::TRANSLATABLE_FIELDS as $field) {
            $value = trim((string) $this->getAttribute($field));
            if ($value !== '') {
                $content[$field] = $value;
            }
        }

        return $content;
    }

    public function translationSourceHash(): string
    {
        return hash('sha256', json_encode(
            $this->translationSourceContent(),
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        ) ?: '');
    }

    public function translationQueueName(): string
    {
        return $this->isImportedFromHzz() ? 'translations-hzz' : 'translations-native';
    }

    public function localized(string $field, ?string $locale = null): mixed
    {
        $sourceValue = $this->getAttribute($field);
        $locale ??= app()->getLocale();

        if ($locale === 'hr' || ! in_array($field, self::TRANSLATABLE_FIELDS, true)) {
            return $sourceValue;
        }

        $translation = $this->relationLoaded('translations')
            ? $this->translations->firstWhere('locale', $locale)
            : $this->translations()->where('locale', $locale)->first();

        if (
            ! $translation
            || $translation->status !== 'completed'
            || ! hash_equals((string) $translation->source_hash, $this->translationSourceHash())
        ) {
            return $sourceValue;
        }

        return $translation->content[$field] ?? $sourceValue;
    }

    public function hasAutomaticTranslation(?string $locale = null): bool
    {
        $locale ??= app()->getLocale();
        if ($locale === 'hr') {
            return false;
        }

        $translation = $this->relationLoaded('translations')
            ? $this->translations->firstWhere('locale', $locale)
            : $this->translations()->where('locale', $locale)->first();

        return $translation !== null
            && $translation->status === 'completed'
            && hash_equals((string) $translation->source_hash, $this->translationSourceHash());
    }

    public function translationStatus(string $locale = 'en'): string
    {
        $translation = $this->relationLoaded('translations')
            ? $this->translations->firstWhere('locale', $locale)
            : $this->translations()->where('locale', $locale)->first();

        if (! $translation) {
            return 'not_queued';
        }

        if (
            $translation->status === 'completed'
            && ! hash_equals((string) $translation->source_hash, $this->translationSourceHash())
        ) {
            return 'outdated';
        }

        return (string) $translation->status;
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
        return $this->source_type === 'hzz'
            || strtolower((string) $this->source_system) === 'hzz'
            || (bool) $this->hzz_is_official;
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
