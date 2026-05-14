<?php

namespace App\Models;

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
            self::VISIBILITY_EMPLOYERS => 'Visible to employers who receive my applications',
            self::VISIBILITY_ANONYMOUS => 'Anonymous in employer views',
            self::VISIBILITY_PRIVATE => 'Private profile',
        ];
    }

    public function completenessPercent(): int
    {
        $fields = $this->completenessFields();

        $total = count($fields);
        $completed = 0;

        foreach ($fields as $field => $type) {
            $value = $this->{$field};

            if ($type === 'array') {
                if (is_array($value) && count(array_filter($value)) > 0) {
                    $completed++;
                }
                continue;
            }

            if ($type === 'bool') {
                if (!is_null($value)) {
                    $completed++;
                }
                continue;
            }

            if (!is_null($value) && trim((string) $value) !== '') {
                $completed++;
            }
        }

        return (int) round(($completed / max(1, $total)) * 100);
    }

    public function missingFieldChecklist(): array
    {
        $labels = [
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'nationality_country_code' => 'Nationality',
            'current_country' => 'Current country',
            'current_city' => 'Current city',
            'desired_city' => 'Desired city in Croatia',
            'availability_date' => 'Availability date',
            'languages' => 'Languages with levels',
            'skills' => 'Skills',
            'education_summary' => 'Education summary',
            'work_experience' => 'Work experience',
            'desired_roles' => 'Desired roles/categories',
            'professional_summary' => 'Professional summary',
        ];

        $missing = [];

        foreach ($this->completenessFields() as $field => $type) {
            $value = $this->{$field};
            $isMissing = false;

            if ($type === 'array') {
                $isMissing = !is_array($value) || count(array_filter($value)) === 0;
            } elseif ($type === 'bool') {
                $isMissing = is_null($value);
            } else {
                $isMissing = is_null($value) || trim((string) $value) === '';
            }

            if ($isMissing) {
                $missing[] = $labels[$field] ?? $field;
            }
        }

        return $missing;
    }

    private function completenessFields(): array
    {
        return [
            'first_name' => 'text',
            'last_name' => 'text',
            'nationality_country_code' => 'text',
            'current_country' => 'text',
            'current_city' => 'text',
            'desired_city' => 'text',
            'availability_date' => 'text',
            'languages' => 'array',
            'skills' => 'array',
            'education_summary' => 'text',
            'work_experience' => 'text',
            'desired_roles' => 'array',
            'professional_summary' => 'text',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function toSnapshot(): array
    {
        return [
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'nationality_country_code' => $this->nationality_country_code,
            'current_country' => $this->current_country,
            'current_city' => $this->current_city,
            'desired_city' => $this->desired_city,
            'availability_date' => $this->availability_date?->toDateString(),
            'birth_year' => $this->birth_year,
            'languages' => $this->languages,
            'professional_summary' => $this->professional_summary,
            'education_summary' => $this->education_summary,
            'work_experience' => $this->work_experience,
            'certifications' => $this->certifications,
            'desired_roles' => $this->desired_roles,
            'salary_expectation' => $this->salary_expectation,
            'accommodation_needed' => $this->accommodation_needed,
            'visa_work_permit_status' => $this->visa_work_permit_status,
            'skills' => $this->skills,
            'recommendations' => $this->recommendations,
            'profile_visibility' => $this->profile_visibility,
            'photo_path' => $this->photo_path,
        ];
    }
}
