<?php

namespace App\Services;

use App\Models\WorkerProfile;
use Illuminate\Support\Collection;

class WorkerProfileCompletenessService
{
    private const MAX_SCORE = 100;

    /**
     * Weighted scoring model.
     *
     * Base profile quality can reach 90 without optional sections.
     * Certifications + references are optional bonus points (up to +10).
     */
    private const WEIGHTS = [
        'first_name' => 5,
        'last_name' => 5,
        'nationality_country_code' => 5,
        'current_country' => 4,
        'current_city' => 4,
        'desired_city' => 4,
        'availability_date' => 4,
        'professional_summary' => 7,
        'salary_expectation' => 5,
        'visa_work_permit_status' => 5,
        'photo_path' => 4,

        'languages_any' => 6,
        'languages_with_level' => 4,

        'education_any' => 6,
        'education_quality' => 4,

        'experience_any' => 7,
        'experience_quality' => 5,

        'skills_any' => 6,

        'certifications_bonus' => 5,
        'references_bonus' => 5,
    ];

    /**
     * @return array{percentage:int,score:int,max_score:int,state_key:string,state_label:string,helper_text:string,missing:array<int,string>,breakdown:array<int,array<string,mixed>>}
     */
    public function calculate(WorkerProfile $profile): array
    {
        $languages = $this->languagesCollection($profile);
        $educations = $this->educationCollection($profile);
        $experiences = $this->experienceCollection($profile);
        $skills = $this->skillsCollection($profile);
        $certifications = $this->certificationCollection($profile);
        $references = $this->referenceCollection($profile);

        $entries = [];
        $missing = [];

        $this->scoreTextField($entries, $missing, 'first_name', $profile->first_name, __('worker_profile.completeness.missing.first_name'));
        $this->scoreTextField($entries, $missing, 'last_name', $profile->last_name, __('worker_profile.completeness.missing.last_name'));
        $this->scoreTextField($entries, $missing, 'nationality_country_code', $profile->nationality_country_code, __('worker_profile.completeness.missing.nationality_country_code'));
        $this->scoreTextField($entries, $missing, 'current_country', $profile->current_country, __('worker_profile.completeness.missing.current_country'));
        $this->scoreTextField($entries, $missing, 'current_city', $profile->current_city, __('worker_profile.completeness.missing.current_city'));
        $this->scoreTextField($entries, $missing, 'desired_city', $profile->desired_city, __('worker_profile.completeness.missing.desired_city'));
        $this->scoreTextField($entries, $missing, 'availability_date', $profile->availability_date?->toDateString(), __('worker_profile.completeness.missing.availability_date'));
        $this->scoreTextField($entries, $missing, 'professional_summary', $profile->professional_summary, __('worker_profile.completeness.missing.professional_summary'));
        $this->scorePresenceField($entries, $missing, 'salary_expectation', $profile->salary_expectation, __('worker_profile.completeness.missing.salary_expectation'));
        $this->scoreTextField($entries, $missing, 'visa_work_permit_status', $profile->visa_work_permit_status, __('worker_profile.completeness.missing.visa_work_permit_status'));
        $this->scoreTextField($entries, $missing, 'photo_path', $profile->photo_path, __('worker_profile.completeness.missing.photo_path'));

        $hasAnyLanguage = $languages->contains(fn (array $row): bool => $this->filled($row['language'] ?? null));
        $hasLanguageWithLevel = $languages->contains(fn (array $row): bool => $this->filled($row['language'] ?? null) && $this->filled($row['level'] ?? null));

        $this->scoreBooleanField($entries, $missing, 'languages_any', $hasAnyLanguage, __('worker_profile.completeness.missing.languages_any'));
        $this->scoreBooleanField($entries, $missing, 'languages_with_level', $hasLanguageWithLevel, __('worker_profile.completeness.missing.languages_with_level'));

        $hasEducation = $educations->isNotEmpty();
        $hasQualityEducation = $educations->contains(function (array $row): bool {
            return $this->filled($row['institution'] ?? null)
                && $this->filled($row['field_of_study'] ?? null)
                && $this->filled($row['degree'] ?? null)
                && $this->filled($row['start_date'] ?? null)
                && $this->filled($row['end_date'] ?? null);
        });

        $this->scoreBooleanField($entries, $missing, 'education_any', $hasEducation, __('worker_profile.completeness.missing.education_any'));
        $this->scoreBooleanField($entries, $missing, 'education_quality', $hasQualityEducation, __('worker_profile.completeness.missing.education_quality'));

        $hasExperience = $experiences->isNotEmpty();
        $hasQualityExperience = $experiences->contains(function (array $row): bool {
            return $this->filled($row['job_title'] ?? null)
                && $this->filled($row['company_name'] ?? null)
                && $this->filled($row['description'] ?? null);
        });

        $this->scoreBooleanField($entries, $missing, 'experience_any', $hasExperience, __('worker_profile.completeness.missing.experience_any'));
        $this->scoreBooleanField($entries, $missing, 'experience_quality', $hasQualityExperience, __('worker_profile.completeness.missing.experience_quality'));

        $hasSkills = $skills->isNotEmpty();
        $this->scoreBooleanField($entries, $missing, 'skills_any', $hasSkills, __('worker_profile.completeness.missing.skills_any'));

        $hasCertifications = $certifications->isNotEmpty();
        $optionalMissing = null;
        $this->scoreBooleanField($entries, $optionalMissing, 'certifications_bonus', $hasCertifications);

        $hasReferences = $references->isNotEmpty();
        $this->scoreBooleanField($entries, $optionalMissing, 'references_bonus', $hasReferences);

        $score = (int) array_sum(array_column($entries, 'score'));
        $score = max(0, min(self::MAX_SCORE, $score));

        $percentage = (int) round(($score / self::MAX_SCORE) * 100);
        $percentage = max(0, min(100, $percentage));

        [$stateKey, $stateLabel, $helperText] = $this->stateForPercentage($percentage);

        return [
            'percentage' => $percentage,
            'score' => $score,
            'max_score' => self::MAX_SCORE,
            'state_key' => $stateKey,
            'state_label' => $stateLabel,
            'helper_text' => $helperText,
            'missing' => array_values(array_unique($missing)),
            'breakdown' => $entries,
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $entries
     * @param array<int, string>|null $missing
     */
    private function scoreTextField(array &$entries, ?array &$missing, string $key, mixed $value, ?string $missingLabel = null): void
    {
        $completed = $this->filled($value);

        if (! $completed && $missing !== null && $missingLabel !== null) {
            $missing[] = $missingLabel;
        }

        $this->appendEntry($entries, $key, $completed);
    }

    /**
     * @param array<int, array<string, mixed>> $entries
     * @param array<int, string>|null $missing
     */
    private function scorePresenceField(array &$entries, ?array &$missing, string $key, mixed $value, ?string $missingLabel = null): void
    {
        $completed = ! is_null($value) && $value !== '';

        if (! $completed && $missing !== null && $missingLabel !== null) {
            $missing[] = $missingLabel;
        }

        $this->appendEntry($entries, $key, $completed);
    }

    /**
     * @param array<int, array<string, mixed>> $entries
     * @param array<int, string>|null $missing
     */
    private function scoreBooleanField(array &$entries, ?array &$missing, string $key, bool $completed, ?string $missingLabel = null): void
    {
        if (! $completed && $missing !== null && $missingLabel !== null) {
            $missing[] = $missingLabel;
        }

        $this->appendEntry($entries, $key, $completed);
    }

    /**
     * @param array<int, array<string, mixed>> $entries
     */
    private function appendEntry(array &$entries, string $key, bool $completed): void
    {
        $max = (int) (self::WEIGHTS[$key] ?? 0);
        $score = $completed ? $max : 0;

        $entries[] = [
            'key' => $key,
            'score' => $score,
            'max' => $max,
            'completed' => $completed,
            'is_bonus' => str_contains($key, 'bonus'),
        ];
    }

    private function filled(mixed $value): bool
    {
        return ! is_null($value) && trim((string) $value) !== '';
    }

    /**
     * @return array{0:string,1:string,2:string}
     */
    private function stateForPercentage(int $percentage): array
    {
        if ($percentage <= 39) {
            return ['starter', __('worker_profile.completeness.states.starter.label'), __('worker_profile.completeness.states.starter.helper')];
        }

        if ($percentage <= 69) {
            return ['good_start', __('worker_profile.completeness.states.good_start.label'), __('worker_profile.completeness.states.good_start.helper')];
        }

        if ($percentage <= 89) {
            return ['almost_done', __('worker_profile.completeness.states.almost_done.label'), __('worker_profile.completeness.states.almost_done.helper')];
        }

        return ['ready', __('worker_profile.completeness.states.ready.label'), __('worker_profile.completeness.states.ready.helper')];
    }

    /**
     * @return Collection<int, array{language:string, level:string}>
     */
    private function languagesCollection(WorkerProfile $profile): Collection
    {
        $source = $profile->relationLoaded('languagesList')
            ? $profile->languagesList
            : $profile->languagesList()->get();

        $rows = $source
            ->map(fn ($row): array => [
                'language' => (string) ($row->language ?? ''),
                'level' => (string) ($row->level ?? ''),
            ])
            ->filter(fn (array $row): bool => $this->filled($row['language']) || $this->filled($row['level']))
            ->values();

        if ($rows->isNotEmpty()) {
            return $rows;
        }

        return collect(is_array($profile->languages) ? $profile->languages : [])
            ->map(fn ($row): array => [
                'language' => (string) (($row['language'] ?? '') ?: ''),
                'level' => (string) (($row['level'] ?? '') ?: ''),
            ])
            ->filter(fn (array $row): bool => $this->filled($row['language']) || $this->filled($row['level']))
            ->values();
    }

    /**
     * @return Collection<int, array<string, string>>
     */
    private function educationCollection(WorkerProfile $profile): Collection
    {
        $source = $profile->relationLoaded('educations')
            ? $profile->educations
            : $profile->educations()->get();

        return $source
            ->map(fn ($row): array => [
                'institution' => (string) ($row->institution ?? ''),
                'degree' => (string) ($row->degree ?? ''),
                'field_of_study' => (string) ($row->field_of_study ?? ''),
                'start_date' => (string) ($row->start_date?->toDateString() ?? ''),
                'end_date' => (string) ($row->end_date?->toDateString() ?? ''),
            ])
            ->filter(fn (array $row): bool => collect($row)->contains(fn (string $value): bool => $this->filled($value)))
            ->values();
    }

    /**
     * @return Collection<int, array<string, string>>
     */
    private function experienceCollection(WorkerProfile $profile): Collection
    {
        $source = $profile->relationLoaded('experiences')
            ? $profile->experiences
            : $profile->experiences()->get();

        return $source
            ->map(fn ($row): array => [
                'job_title' => (string) ($row->job_title ?? ''),
                'company_name' => (string) ($row->company_name ?? ''),
                'description' => (string) ($row->description ?? ''),
            ])
            ->filter(fn (array $row): bool => collect($row)->contains(fn (string $value): bool => $this->filled($value)))
            ->values();
    }

    /**
     * @return Collection<int, string>
     */
    private function skillsCollection(WorkerProfile $profile): Collection
    {
        $source = $profile->relationLoaded('skillsList')
            ? $profile->skillsList
            : $profile->skillsList()->get();

        $rows = $source
            ->pluck('name')
            ->filter(fn ($value): bool => $this->filled($value))
            ->values();

        if ($rows->isNotEmpty()) {
            return $rows;
        }

        return collect(is_array($profile->skills) ? $profile->skills : [])
            ->filter(fn ($value): bool => $this->filled($value))
            ->values();
    }

    /**
     * @return Collection<int, string>
     */
    private function certificationCollection(WorkerProfile $profile): Collection
    {
        $source = $profile->relationLoaded('certificationsList')
            ? $profile->certificationsList
            : $profile->certificationsList()->get();

        return $source
            ->pluck('name')
            ->filter(fn ($value): bool => $this->filled($value))
            ->values();
    }

    /**
     * @return Collection<int, string>
     */
    private function referenceCollection(WorkerProfile $profile): Collection
    {
        $source = $profile->relationLoaded('referencesList')
            ? $profile->referencesList
            : $profile->referencesList()->get();

        return $source
            ->pluck('full_name')
            ->filter(fn ($value): bool => $this->filled($value))
            ->values();
    }
}
