<?php

namespace App\Models;

use App\Services\WorkerProfileCompletenessService;
use App\Support\StructuredCvLegacyFormatter;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class WorkerProfile extends Model
{
    public const VISIBILITY_EMPLOYERS = 'employers';
    public const VISIBILITY_ANONYMOUS = 'anonymous';
    public const VISIBILITY_PRIVATE = 'private';

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'nationality_country_code',
        'current_country',
        'current_city',
        'desired_city',
        'availability_date',
        'languages',
        'birth_year',
        'professional_summary',
        'education_summary',
        'work_experience',
        'certifications',
        'desired_roles',
        'salary_expectation',
        'accommodation_needed',
        'visa_work_permit_status',
        'skills',
        'recommendations',
        'profile_visibility',
        'photo_path',
        'communication_language',
    ];

    protected function casts(): array
    {
        return [
            'availability_date' => 'date',
            'languages' => 'array',
            'skills' => 'array',
            'desired_roles' => 'array',
            'accommodation_needed' => 'boolean',
        ];
    }

    public static function visibilityOptions(): array
    {
        return [
            self::VISIBILITY_EMPLOYERS => __('worker_privacy.visibility_options.employers'),
            self::VISIBILITY_ANONYMOUS => __('worker_privacy.visibility_options.anonymous'),
            self::VISIBILITY_PRIVATE => __('worker_privacy.visibility_options.private'),
        ];
    }

    public function completenessPercent(): int
    {
        return $this->completenessData()['percentage'];
    }

    public function missingFieldChecklist(): array
    {
        return $this->completenessData()['missing'];
    }

    /**
     * @return array{percentage:int,score:int,max_score:int,state_key:string,state_label:string,helper_text:string,missing:array<int,string>,breakdown:array<int,array<string,mixed>>}
     */
    public function completenessData(): array
    {
        return app(WorkerProfileCompletenessService::class)->calculate($this);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(WorkerExperience::class)->orderBy('sort_order');
    }

    public function educations(): HasMany
    {
        return $this->hasMany(WorkerEducation::class)->orderBy('sort_order');
    }

    public function certificationsList(): HasMany
    {
        return $this->hasMany(WorkerCertification::class)->orderBy('sort_order');
    }

    public function referencesList(): HasMany
    {
        return $this->hasMany(WorkerReference::class)->orderBy('sort_order');
    }

    public function skillsList(): HasMany
    {
        return $this->hasMany(WorkerSkill::class)->orderBy('sort_order');
    }

    public function languagesList(): HasMany
    {
        return $this->hasMany(WorkerLanguage::class)->orderBy('sort_order');
    }

    public function toSnapshot(): array
    {
        $skills = $this->skillsArray();
        $languages = $this->languagesArray();
        $experiences = $this->experienceSnapshot();
        $educations = $this->educationSnapshot();
        $certifications = $this->certificationSnapshot();
        $references = $this->referenceSnapshot();

        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'nationality_country_code' => $this->nationality_country_code,
            'current_country' => $this->current_country,
            'current_city' => $this->current_city,
            'desired_city' => $this->desired_city,
            'availability_date' => $this->availability_date?->toDateString(),
            'birth_year' => $this->birth_year,
            'languages' => $languages,
            'professional_summary' => $this->professional_summary,
            'education_summary' => $this->education_summary ?: StructuredCvLegacyFormatter::educationSummary($educations),
            'work_experience' => $this->work_experience ?: StructuredCvLegacyFormatter::experienceSummary($experiences),
            'certifications' => $this->certifications ?: StructuredCvLegacyFormatter::certificationSummary($certifications),
            'desired_roles' => $this->desired_roles,
            'salary_expectation' => $this->salary_expectation,
            'accommodation_needed' => $this->accommodation_needed,
            'visa_work_permit_status' => $this->visa_work_permit_status,
            'skills' => $skills,
            'recommendations' => $this->recommendations ?: StructuredCvLegacyFormatter::referenceSummary($references),
            'structured_experiences' => $experiences,
            'structured_educations' => $educations,
            'structured_certifications' => $certifications,
            'structured_references' => $references,
            'snapshot_version' => 2,
            'profile_visibility' => $this->profile_visibility,
            'photo_path' => $this->photo_path,
            'photo_url' => $this->photoUrl(),
        ];
    }

    public function skillsArray(): array
    {
        $items = $this->relationLoaded('skillsList')
            ? $this->skillsList->pluck('name')->filter()->values()->all()
            : $this->skillsList()->pluck('name')->filter()->values()->all();

        if ($items !== []) {
            return $items;
        }

        return is_array($this->skills) ? array_values(array_filter($this->skills)) : [];
    }

    public function languagesArray(): array
    {
        $languages = $this->relationLoaded('languagesList')
            ? $this->languagesList
            : $this->languagesList()->get();

        $items = $languages
            ->map(fn (WorkerLanguage $language): array => [
                'language' => (string) $language->language,
                'level' => (string) ($language->level ?? ''),
            ])
            ->toArray();

        if ($items !== []) {
            return $items;
        }

        return is_array($this->languages) ? $this->languages : [];
    }

    public function experienceSnapshot(): array
    {
        $experiences = $this->relationLoaded('experiences')
            ? $this->experiences
            : $this->experiences()->get();

        return $experiences
            ->map(fn (WorkerExperience $experience): array => [
                'job_title' => $experience->job_title,
                'company_name' => $experience->company_name,
                'country' => $experience->country,
                'city' => $experience->city,
                'start_date' => $experience->start_date?->toDateString(),
                'end_date' => $experience->end_date?->toDateString(),
                'is_current' => (bool) $experience->is_current,
                'description' => $experience->description,
            ])
            ->toArray();
    }

    public function educationSnapshot(): array
    {
        $educations = $this->relationLoaded('educations')
            ? $this->educations
            : $this->educations()->get();

        return $educations
            ->map(fn (WorkerEducation $education): array => [
                'institution' => $education->institution,
                'degree' => $education->degree,
                'field_of_study' => $education->field_of_study,
                'country' => $education->country,
                'city' => $education->city,
                'start_date' => $education->start_date?->toDateString(),
                'end_date' => $education->end_date?->toDateString(),
                'description' => $education->description,
            ])
            ->toArray();
    }

    public function certificationSnapshot(): array
    {
        $certifications = $this->relationLoaded('certificationsList')
            ? $this->certificationsList
            : $this->certificationsList()->get();

        return $certifications
            ->map(fn (WorkerCertification $certification): array => [
                'name' => $certification->name,
                'issuer' => $certification->issuer,
                'issued_on' => $certification->issued_on?->toDateString(),
                'expires_on' => $certification->expires_on?->toDateString(),
                'credential_id' => $certification->credential_id,
                'credential_url' => $certification->credential_url,
            ])
            ->toArray();
    }

    public function referenceSnapshot(): array
    {
        $references = $this->relationLoaded('referencesList')
            ? $this->referencesList
            : $this->referencesList()->get();

        return $references
            ->map(fn (WorkerReference $reference): array => [
                'full_name' => $reference->full_name,
                'position' => $reference->position,
                'company' => $reference->company,
                'contact_email' => $reference->contact_email,
                'contact_phone' => $reference->contact_phone,
                'notes' => $reference->notes,
            ])
            ->toArray();
    }

    public function photoUrl(): ?string
    {
        if (!$this->photo_path) {
            return null;
        }

        return route('worker.profile.photo.show', ['path' => $this->photo_path]);
    }
}
