<?php

namespace App\Http\Controllers;

use App\Models\WorkerProfile;
use App\Support\CvProfileOptions;
use App\Support\StructuredCvLegacyFormatter;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class WorkerProfileController extends Controller
{

    /**
     * Show the profile edit form
     */
    public function edit()
    {
        $this->ensureWorker();

        $profile = $this->firstOrCreateProfile()->load([
            'experiences',
            'educations',
            'certificationsList',
            'referencesList',
            'skillsList',
            'languagesList',
        ]);

        $initialSkills = $this->normalizeSkills(old('skills', $profile->skillsArray()));
        $initialDesiredRoles = $this->normalizeStringList(old('desired_roles', $profile->desired_roles ?? []));
        $experienceRows = $this->normalizeExperiences(old('experiences', $profile->experienceSnapshot()));
        $educationRows = $this->normalizeEducations(old('educations', $profile->educationSnapshot()));
        $certificationRows = $this->normalizeCertifications(old('certifications_list', $profile->certificationSnapshot()));
        $referenceRows = $this->normalizeReferences(old('references_list', $profile->referenceSnapshot()));
        $locale = app()->getLocale();

        $experienceRows = array_map(function (array $row) use ($locale): array {
            $row['country'] = CvProfileOptions::displayCountryName((string) ($row['country'] ?? ''), $locale);

            return $row;
        }, $experienceRows);

        $educationRows = array_map(function (array $row) use ($locale): array {
            $row['country'] = CvProfileOptions::displayCountryName((string) ($row['country'] ?? ''), $locale);

            return $row;
        }, $educationRows);

        $countryOptions = CvProfileOptions::countryOptions($locale);
        $visaStatusOptions = CvProfileOptions::visaStatusOptions();
        $skillSuggestions = CvProfileOptions::skillSuggestions();
        $nationalityDisplayValue = CvProfileOptions::displayCountryName((string) old('nationality_country_code', $profile->nationality_country_code), $locale);
        $currentCountryDisplayValue = CvProfileOptions::displayCountryName((string) old('current_country', $profile->current_country), $locale);
        $visaCurrentValue = (string) old('visa_work_permit_status', $profile->visa_work_permit_status);

        $languageRows = $this->normalizeLanguages(old('languages', $profile->languagesArray()));
        if (empty($languageRows)) {
            $languageRows = [['language' => '', 'level' => '']];
        }

        if (empty($experienceRows)) {
            $experienceRows = [['job_title' => '', 'company_name' => '', 'country' => '', 'city' => '', 'start_date' => '', 'end_date' => '', 'is_current' => false, 'description' => '']];
        }

        if (empty($educationRows)) {
            $educationRows = [['institution' => '', 'degree' => '', 'field_of_study' => '', 'country' => '', 'city' => '', 'start_date' => '', 'end_date' => '', 'description' => '']];
        }

        if (empty($certificationRows)) {
            $certificationRows = [['name' => '', 'issuer' => '', 'issued_on' => '', 'expires_on' => '', 'credential_id' => '', 'credential_url' => '']];
        }

        if (empty($referenceRows)) {
            $referenceRows = [['full_name' => '', 'position' => '', 'company' => '', 'contact_email' => '', 'contact_phone' => '', 'notes' => '']];
        }

        $completenessData = $profile->completenessData();
        $completeness = $completenessData['percentage'];
        $missingChecklist = $completenessData['missing'];
        $completenessStateLabel = $completenessData['state_label'];
        $completenessHelperText = $completenessData['helper_text'];

        return view('worker.profile-edit', compact(
            'profile',
            'initialSkills',
            'initialDesiredRoles',
            'languageRows',
            'experienceRows',
            'educationRows',
            'certificationRows',
            'referenceRows',
            'completeness',
            'missingChecklist',
            'completenessStateLabel',
            'completenessHelperText',
            'countryOptions',
            'visaStatusOptions',
            'skillSuggestions',
            'nationalityDisplayValue',
            'currentCountryDisplayValue',
            'visaCurrentValue',
        ));
    }

    public function preview()
    {
        $this->ensureWorker();

        $profile = $this->firstOrCreateProfile()->load([
            'experiences',
            'educations',
            'certificationsList',
            'referencesList',
            'skillsList',
            'languagesList',
        ]);
        $completenessData = $profile->completenessData();
        $completeness = $completenessData['percentage'];
        $missingChecklist = $completenessData['missing'];
        $completenessStateLabel = $completenessData['state_label'];
        $completenessHelperText = $completenessData['helper_text'];

        return view('worker.profile-preview', compact(
            'profile',
            'completeness',
            'missingChecklist',
            'completenessStateLabel',
            'completenessHelperText',
        ));
    }

    public function showPhoto(string $path)
    {
        $resolvedPath = $this->resolveWorkerPhotoPath($path);
        if ($resolvedPath !== null) {
            return Storage::disk('public')->response($resolvedPath);
        }

        $basename = basename(ltrim($path, '/'));
        if ($basename !== '' && $basename !== '.' && $basename !== '..') {
            $publicCandidates = [
                public_path('storage/worker-photos/' . $basename),
                public_path('storage/' . $basename),
            ];

            foreach ($publicCandidates as $absolutePath) {
                if (is_file($absolutePath)) {
                    return response()->file($absolutePath);
                }
            }
        }

        Log::warning('Worker photo not found for display.', [
            'requested_path' => $path,
            'user_id' => auth()->id(),
            'public_disk_root' => Storage::disk('public')->path(''),
        ]);

        abort(404);
    }

    /**
     * Update the profile
     */
    public function update(Request $request)
    {
        $this->ensureWorker();

        $request->merge([
            'skills' => $this->normalizeSkills($request->input('skills', [])),
            'desired_roles' => $this->normalizeStringList($request->input('desired_roles', [])),
            'languages' => $this->normalizeLanguages($request->input('languages', [])),
            'experiences' => $this->normalizeExperiences($request->input('experiences', [])),
            'educations' => $this->normalizeEducations($request->input('educations', [])),
            'certifications_list' => $this->normalizeCertifications($request->input('certifications_list', [])),
            'references_list' => $this->normalizeReferences($request->input('references_list', [])),
        ]);

        $profile = $this->firstOrCreateProfile();

        $currentYear = now()->year;
        $minBirthYear = 1940;
        $maxBirthYear = $currentYear - 14; // Must be at least 14 years old

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'nationality_country_code' => ['required', 'string', 'max:100'],
            'current_country' => ['nullable', 'string', 'max:100'],
            'current_city' => ['nullable', 'string', 'max:100'],
            'desired_city' => ['nullable', 'string', 'max:100'],
            'availability_date' => ['nullable', 'date'],
            'birth_year' => ['required', 'integer', 'between:' . $minBirthYear . ',' . $maxBirthYear],
            'languages' => ['nullable', 'array'],
            'languages.*.language' => ['nullable', 'string', 'max:60'],
            'languages.*.level' => ['nullable', 'string', 'max:30'],
            'professional_summary' => ['nullable', 'string', 'max:1000'],
            'experiences' => ['nullable', 'array', 'max:30'],
            'experiences.*.job_title' => ['nullable', 'string', 'max:120'],
            'experiences.*.company_name' => ['nullable', 'string', 'max:120'],
            'experiences.*.country' => ['nullable', 'string', 'max:100'],
            'experiences.*.city' => ['nullable', 'string', 'max:100'],
            'experiences.*.start_date' => ['nullable', 'date'],
            'experiences.*.end_date' => ['nullable', 'date'],
            'experiences.*.is_current' => ['nullable', 'boolean'],
            'experiences.*.description' => ['nullable', 'string', 'max:3000'],
            'educations' => ['nullable', 'array', 'max:30'],
            'educations.*.institution' => ['nullable', 'string', 'max:150'],
            'educations.*.degree' => ['nullable', 'string', 'max:120'],
            'educations.*.field_of_study' => ['nullable', 'string', 'max:120'],
            'educations.*.country' => ['nullable', 'string', 'max:100'],
            'educations.*.city' => ['nullable', 'string', 'max:100'],
            'educations.*.start_date' => ['nullable', 'date'],
            'educations.*.end_date' => ['nullable', 'date'],
            'educations.*.description' => ['nullable', 'string', 'max:3000'],
            'certifications_list' => ['nullable', 'array', 'max:30'],
            'certifications_list.*.name' => ['nullable', 'string', 'max:160'],
            'certifications_list.*.issuer' => ['nullable', 'string', 'max:120'],
            'certifications_list.*.issued_on' => ['nullable', 'date'],
            'certifications_list.*.expires_on' => ['nullable', 'date'],
            'certifications_list.*.credential_id' => ['nullable', 'string', 'max:160'],
            'certifications_list.*.credential_url' => ['nullable', 'url', 'max:255'],
            'skills' => ['nullable', 'array', 'max:30'],
            'skills.*' => ['string', 'max:40'],
            'desired_roles' => ['nullable', 'array', 'max:20'],
            'desired_roles.*' => ['string', 'max:60'],
            'salary_expectation' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'accommodation_needed' => ['nullable', 'boolean'],
            'visa_work_permit_status' => ['nullable', 'string', 'max:120'],
            'references_list' => ['nullable', 'array', 'max:30'],
            'references_list.*.full_name' => ['nullable', 'string', 'max:160'],
            'references_list.*.position' => ['nullable', 'string', 'max:120'],
            'references_list.*.company' => ['nullable', 'string', 'max:120'],
            'references_list.*.contact_email' => ['nullable', 'email:rfc,dns', 'max:255'],
            'references_list.*.contact_phone' => ['nullable', 'string', 'max:60'],
            'references_list.*.notes' => ['nullable', 'string', 'max:1000'],
            'profile_visibility' => ['required', 'in:' . implode(',', array_keys(WorkerProfile::visibilityOptions()))],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:12288'], // 12MB max
        ], [
            'birth_year.between' => __('worker_profile.validation.birth_year_between', [
                'min' => $minBirthYear,
                'max' => $maxBirthYear,
            ]),
        ]);

        $locale = app()->getLocale();

        $validated['nationality_country_code'] = CvProfileOptions::normalizeCountryForStorage((string) ($validated['nationality_country_code'] ?? ''), $locale);
        $validated['current_country'] = CvProfileOptions::normalizeCountryForStorage((string) ($validated['current_country'] ?? ''), $locale);
        $validated['visa_work_permit_status'] = CvProfileOptions::normalizeVisaStatusForStorage((string) ($validated['visa_work_permit_status'] ?? ''));
        $validated['accommodation_needed'] = filter_var($request->input('accommodation_needed', false), FILTER_VALIDATE_BOOL);

        if (isset($validated['experiences']) && is_array($validated['experiences'])) {
            $validated['experiences'] = array_map(function (array $row) use ($locale): array {
                $row['country'] = CvProfileOptions::normalizeCountryForStorage((string) ($row['country'] ?? ''), $locale);

                return $row;
            }, $validated['experiences']);
        }

        if (isset($validated['educations']) && is_array($validated['educations'])) {
            $validated['educations'] = array_map(function (array $row) use ($locale): array {
                $row['country'] = CvProfileOptions::normalizeCountryForStorage((string) ($row['country'] ?? ''), $locale);

                return $row;
            }, $validated['educations']);
        }

        $validated['education_summary'] = StructuredCvLegacyFormatter::educationSummary($validated['educations'] ?? []);
        $validated['work_experience'] = StructuredCvLegacyFormatter::experienceSummary($validated['experiences'] ?? []);
        $validated['certifications'] = StructuredCvLegacyFormatter::certificationSummary($validated['certifications_list'] ?? []);
        $validated['recommendations'] = StructuredCvLegacyFormatter::referenceSummary($validated['references_list'] ?? []);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('worker-photos', 'public');

            $optimized = app(\App\Services\ImageSanitizerService::class)
                ->sanitizeAndOptimize('public', $path, 1200, 1200);

            if (! $optimized) {
                Storage::disk('public')->delete($path);

                throw ValidationException::withMessages([
                    'photo' => __('worker_profile.validation.photo_processing_failed'),
                ]);
            }

            // Delete old photo only after a valid optimized replacement is ready.
            $previousPhotoPath = $this->resolveWorkerPhotoPath($profile->photo_path);
            if ($previousPhotoPath) {
                Storage::disk('public')->delete($previousPhotoPath);
            }

            $validated['photo_path'] = $path;
        }

        DB::transaction(function () use ($profile, $validated): void {
            $profile->update(Arr::except($validated, [
                'experiences',
                'educations',
                'certifications_list',
                'references_list',
            ]));

            $this->syncStructuredRows($profile, 'experiences', $validated['experiences'] ?? []);
            $this->syncStructuredRows($profile, 'educations', $validated['educations'] ?? []);
            $this->syncStructuredRows($profile, 'certificationsList', $validated['certifications_list'] ?? []);
            $this->syncStructuredRows($profile, 'referencesList', $validated['references_list'] ?? []);
            $this->syncStructuredRows($profile, 'skillsList', $this->skillsToRows($validated['skills'] ?? []));
            $this->syncStructuredRows($profile, 'languagesList', $validated['languages'] ?? []);
        });

        return redirect()
            ->route('worker.profile.edit')
            ->with('success', __('worker_profile.validation.profile_updated'));
    }

    /**
     * Normalize skills from array, JSON string, or comma/newline separated text.
     */
    private function normalizeSkills(mixed $skills): array
    {
        if (is_null($skills)) {
            return [];
        }

        if (is_string($skills)) {
            $decoded = json_decode($skills, true);
            if (is_array($decoded)) {
                $skills = $decoded;
            } else {
                $skills = preg_split('/[\n,]+/', $skills) ?: [];
            }
        }

        if (!is_array($skills)) {
            return [];
        }

        $normalized = array_values(array_filter(array_map(function ($skill) {
            if (!is_scalar($skill)) {
                return null;
            }

            $value = trim((string) $skill);
            return $value !== '' ? $value : null;
        }, $skills)));

        return array_values(array_unique($normalized));
    }

    private function normalizeStringList(mixed $items): array
    {
        if (is_null($items)) {
            return [];
        }

        if (is_string($items)) {
            $decoded = json_decode($items, true);
            if (is_array($decoded)) {
                $items = $decoded;
            } else {
                $items = preg_split('/[\n,]+/', $items) ?: [];
            }
        }

        if (!is_array($items)) {
            return [];
        }

        $normalized = array_values(array_filter(array_map(function ($item) {
            if (!is_scalar($item)) {
                return null;
            }

            $value = trim((string) $item);
            return $value !== '' ? $value : null;
        }, $items)));

        return array_values(array_unique($normalized));
    }

    private function normalizeLanguages(mixed $languages): array
    {
        if (!is_array($languages)) {
            return [];
        }

        $normalized = [];

        foreach ($languages as $row) {
            $language = trim((string) Arr::get($row, 'language', ''));
            $level = trim((string) Arr::get($row, 'level', ''));

            if ($language === '' && $level === '') {
                continue;
            }

            $normalized[] = [
                'language' => $language,
                'level' => $level,
            ];
        }

        return $normalized;
    }

    private function normalizeExperiences(mixed $rows): array
    {
        if (!is_array($rows)) {
            return [];
        }

        $normalized = [];

        foreach ($rows as $row) {
            $item = [
                'job_title' => trim((string) Arr::get($row, 'job_title', '')),
                'company_name' => trim((string) Arr::get($row, 'company_name', '')),
                'country' => trim((string) Arr::get($row, 'country', '')),
                'city' => trim((string) Arr::get($row, 'city', '')),
                'start_date' => trim((string) Arr::get($row, 'start_date', '')),
                'end_date' => trim((string) Arr::get($row, 'end_date', '')),
                'is_current' => filter_var(Arr::get($row, 'is_current', false), FILTER_VALIDATE_BOOL),
                'description' => trim((string) Arr::get($row, 'description', '')),
            ];

            if (collect($item)->except('is_current')->filter()->isEmpty()) {
                continue;
            }

            if ($item['is_current']) {
                $item['end_date'] = '';
            }

            $item = $this->normalizeEmptyDateFields($item, ['start_date', 'end_date']);

            $normalized[] = $item;
        }

        return $normalized;
    }

    private function normalizeEducations(mixed $rows): array
    {
        if (!is_array($rows)) {
            return [];
        }

        $normalized = [];

        foreach ($rows as $row) {
            $item = [
                'institution' => trim((string) Arr::get($row, 'institution', '')),
                'degree' => trim((string) Arr::get($row, 'degree', '')),
                'field_of_study' => trim((string) Arr::get($row, 'field_of_study', '')),
                'country' => trim((string) Arr::get($row, 'country', '')),
                'city' => trim((string) Arr::get($row, 'city', '')),
                'start_date' => trim((string) Arr::get($row, 'start_date', '')),
                'end_date' => trim((string) Arr::get($row, 'end_date', '')),
                'description' => trim((string) Arr::get($row, 'description', '')),
            ];

            if (collect($item)->filter()->isEmpty()) {
                continue;
            }

            $item = $this->normalizeEmptyDateFields($item, ['start_date', 'end_date']);

            $normalized[] = $item;
        }

        return $normalized;
    }

    private function normalizeCertifications(mixed $rows): array
    {
        if (!is_array($rows)) {
            return [];
        }

        $normalized = [];

        foreach ($rows as $row) {
            $item = [
                'name' => trim((string) Arr::get($row, 'name', '')),
                'issuer' => trim((string) Arr::get($row, 'issuer', '')),
                'issued_on' => trim((string) Arr::get($row, 'issued_on', '')),
                'expires_on' => trim((string) Arr::get($row, 'expires_on', '')),
                'credential_id' => trim((string) Arr::get($row, 'credential_id', '')),
                'credential_url' => trim((string) Arr::get($row, 'credential_url', '')),
            ];

            if (collect($item)->filter()->isEmpty()) {
                continue;
            }

            $item = $this->normalizeEmptyDateFields($item, ['issued_on', 'expires_on']);

            $normalized[] = $item;
        }

        return $normalized;
    }

    private function normalizeReferences(mixed $rows): array
    {
        if (!is_array($rows)) {
            return [];
        }

        $normalized = [];

        foreach ($rows as $row) {
            $item = [
                'full_name' => trim((string) Arr::get($row, 'full_name', '')),
                'position' => trim((string) Arr::get($row, 'position', '')),
                'company' => trim((string) Arr::get($row, 'company', '')),
                'contact_email' => trim((string) Arr::get($row, 'contact_email', '')),
                'contact_phone' => trim((string) Arr::get($row, 'contact_phone', '')),
                'notes' => trim((string) Arr::get($row, 'notes', '')),
            ];

            if (collect($item)->filter()->isEmpty()) {
                continue;
            }

            $normalized[] = $item;
        }

        return $normalized;
    }

    private function syncStructuredRows(WorkerProfile $profile, string $relation, array $rows): void
    {
        $profile->{$relation}()->delete();

        $payload = [];
        foreach (array_values($rows) as $index => $row) {
            $payload[] = array_merge($row, ['sort_order' => $index]);
        }

        if ($payload !== []) {
            $profile->{$relation}()->createMany($payload);
        }
    }

    private function normalizeEmptyDateFields(array $row, array $keys): array
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row) && $row[$key] === '') {
                $row[$key] = null;
            }
        }

        return $row;
    }

    private function skillsToRows(array $skills): array
    {
        return array_map(
            fn (string $skill): array => ['name' => $skill, 'level' => null],
            array_values($skills)
        );
    }

    private function firstOrCreateProfile(): WorkerProfile
    {
        return WorkerProfile::firstOrCreate(
            ['user_id' => auth()->id()],
            [
                'first_name' => '',
                'last_name' => '',
                'nationality_country_code' => '',
                'birth_year' => 1940,
                'skills' => [],
                'languages' => [],
                'desired_roles' => [],
                'profile_visibility' => WorkerProfile::VISIBILITY_EMPLOYERS,
            ]
        );
    }

    private function ensureWorker(): void
    {
        if (!auth()->user()->isWorker()) {
            abort(403, __('worker_profile.validation.only_workers'));
        }
    }

    /**
     * Delete photo
     */
    public function deletePhoto()
    {
        $this->ensureWorker();

        $profile = WorkerProfile::where('user_id', auth()->id())->first();

        if ($profile && $profile->photo_path) {
            // Delete file from storage
            $resolvedPath = $this->resolveWorkerPhotoPath($profile->photo_path);
            if ($resolvedPath) {
                Storage::disk('public')->delete($resolvedPath);
            }

            // Clear photo_path in database
            $profile->update(['photo_path' => null]);

            return redirect()
                ->route('worker.profile.edit')
                ->with('success', __('worker_profile.validation.photo_deleted'));
        }

        return redirect()->route('worker.profile.edit');
    }

    private function resolveWorkerPhotoPath(?string $path): ?string
    {
        $rawPath = trim((string) $path);
        if ($rawPath === '') {
            return null;
        }

        $normalized = ltrim($rawPath, '/');
        $candidates = [];

        if (str_starts_with($normalized, 'worker-photos/')) {
            $candidates[] = $normalized;
        }

        $basename = basename($normalized);
        if ($basename !== '' && $basename !== '.' && $basename !== '..') {
            $candidates[] = 'worker-photos/' . $basename;

            // Backward compatibility: some legacy uploads were stored at disk root.
            if (!str_contains($normalized, '/')) {
                $candidates[] = $basename;
            }

            if (str_starts_with($normalized, 'worker-photos/')) {
                $candidates[] = $basename;
            }
        }

        foreach (array_values(array_unique($candidates)) as $candidate) {
            if (Storage::disk('public')->exists($candidate)) {
                return $candidate;
            }
        }

        return null;
    }
}
