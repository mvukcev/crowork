<?php

namespace App\Http\Controllers;

use App\Models\WorkerProfile;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WorkerProfileController extends Controller
{

    /**
     * Show the profile edit form
     */
    public function edit()
    {
        $this->ensureWorker();

        $profile = $this->firstOrCreateProfile();

        $initialSkills = $this->normalizeSkills(old('skills', $profile->skills ?? []));
        $initialDesiredRoles = $this->normalizeStringList(old('desired_roles', $profile->desired_roles ?? []));

        $languageRows = old('languages');
        if (!is_array($languageRows)) {
            $languageRows = array_map(function ($item) {
                return [
                    'language' => is_array($item) ? ($item['language'] ?? '') : '',
                    'level' => is_array($item) ? ($item['level'] ?? '') : '',
                ];
            }, $profile->languages ?? []);
        }

        if (empty($languageRows)) {
            $languageRows = [['language' => '', 'level' => '']];
        }

        $completeness = $profile->completenessPercent();
        $missingChecklist = $profile->missingFieldChecklist();

        return view('worker.profile-edit', compact(
            'profile',
            'initialSkills',
            'initialDesiredRoles',
            'languageRows',
            'completeness',
            'missingChecklist',
        ));
    }

    public function preview()
    {
        $this->ensureWorker();

        $profile = $this->firstOrCreateProfile();
        $completeness = $profile->completenessPercent();
        $missingChecklist = $profile->missingFieldChecklist();

        return view('worker.profile-preview', compact('profile', 'completeness', 'missingChecklist'));
    }

    public function showPhoto(string $path)
    {
        abort_unless(str_starts_with($path, 'worker-photos/'), 404);
        abort_unless(Storage::disk('public')->exists($path), 404);

        return Storage::disk('public')->response($path);
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
        ]);

        $profile = $this->firstOrCreateProfile();

        $currentYear = now()->year;
        $minBirthYear = 1940;
        $maxBirthYear = $currentYear - 14; // Must be at least 14 years old

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'nationality_country_code' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
            'current_country' => ['nullable', 'string', 'max:100'],
            'current_city' => ['nullable', 'string', 'max:100'],
            'desired_city' => ['nullable', 'string', 'max:100'],
            'availability_date' => ['nullable', 'date'],
            'birth_year' => ['required', 'integer', 'between:' . $minBirthYear . ',' . $maxBirthYear],
            'languages' => ['nullable', 'array'],
            'languages.*.language' => ['nullable', 'string', 'max:60'],
            'languages.*.level' => ['nullable', 'string', 'max:30'],
            'professional_summary' => ['nullable', 'string', 'max:1000'],
            'education_summary' => ['nullable', 'string', 'max:5000'],
            'work_experience' => ['nullable', 'string', 'max:5000'],
            'certifications' => ['nullable', 'string', 'max:5000'],
            'skills' => ['nullable', 'array', 'max:30'],
            'skills.*' => ['string', 'max:40'],
            'desired_roles' => ['nullable', 'array', 'max:20'],
            'desired_roles.*' => ['string', 'max:60'],
            'salary_expectation' => ['nullable', 'integer', 'min:0', 'max:999999'],
            'accommodation_needed' => ['nullable', 'boolean'],
            'visa_work_permit_status' => ['nullable', 'string', 'max:120'],
            'recommendations' => ['nullable', 'string', 'max:3000'],
            'profile_visibility' => ['required', 'in:' . implode(',', array_keys(WorkerProfile::visibilityOptions()))],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'], // 2MB max
        ], [
            'nationality_country_code.regex' => 'Nationality must be a valid 2-letter country code (e.g. HR, DE, US).',
            'nationality_country_code.size' => 'Nationality must be exactly 2 characters.',
            'birth_year.between' => 'Birth year must be between ' . $minBirthYear . ' and ' . $maxBirthYear . ' (must be at least 14 years old).',
        ]);

        $validated['nationality_country_code'] = strtoupper((string) $validated['nationality_country_code']);
        $validated['accommodation_needed'] = filter_var($request->input('accommodation_needed', false), FILTER_VALIDATE_BOOL);

        // Handle photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($profile->photo_path && Storage::disk('public')->exists($profile->photo_path)) {
                Storage::disk('public')->delete($profile->photo_path);
            }

            // Store new photo
            $path = $request->file('photo')->store('worker-photos', 'public');
            $validated['photo_path'] = $path;
        }

        // Update profile
        $profile->update($validated);

        return redirect()
            ->route('worker.profile.edit')
            ->with('success', 'Profile updated successfully!');
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
            abort(403, 'Only workers can access profile management.');
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
            if (Storage::disk('public')->exists($profile->photo_path)) {
                Storage::disk('public')->delete($profile->photo_path);
            }

            // Clear photo_path in database
            $profile->update(['photo_path' => null]);

            return redirect()
                ->route('worker.profile.edit')
                ->with('success', 'Photo deleted successfully!');
        }

        return redirect()->route('worker.profile.edit');
    }
}
