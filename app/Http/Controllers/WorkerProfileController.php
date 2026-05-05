<?php

namespace App\Http\Controllers;

use App\Models\WorkerProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class WorkerProfileController extends Controller
{

    /**
     * Show the profile edit form
     */
    public function edit()
    {
        $this->ensureWorker();

        $profile = WorkerProfile::firstOrCreate(
            ['user_id' => auth()->id()],
            [
                'first_name' => '',
                'last_name' => '',
                'nationality_country_code' => '',
                'birth_year' => 1940,
                'education_summary' => null,
                'work_experience' => null,
                'skills' => [],
                'recommendations' => null,
                'photo_path' => null,
            ]
        );

        return view('worker.profile-edit', compact('profile'));
    }

    /**
     * Update the profile
     */
    public function update(Request $request)
    {
        $this->ensureWorker();

        $profile = WorkerProfile::firstOrCreate(
            ['user_id' => auth()->id()],
            [
                'first_name' => '',
                'last_name' => '',
                'nationality_country_code' => '',
                'birth_year' => 1940,
                'skills' => [],
            ]
        );

        $currentYear = now()->year;
        $minBirthYear = 1940;
        $maxBirthYear = $currentYear - 14; // Must be at least 14 years old

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:80'],
            'last_name' => ['required', 'string', 'max:80'],
            'nationality_country_code' => ['required', 'string', 'size:2', 'regex:/^[A-Z]{2}$/'],
            'birth_year' => ['required', 'integer', 'between:' . $minBirthYear . ',' . $maxBirthYear],
            'education_summary' => ['nullable', 'string', 'max:5000'],
            'work_experience' => ['nullable', 'string', 'max:5000'],
            'skills' => ['nullable', 'array', 'max:30'],
            'skills.*' => ['string', 'max:40'],
            'recommendations' => ['nullable', 'string', 'max:3000'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:2048'], // 2MB max
        ], [
            'nationality_country_code.regex' => 'Nationality must be a valid 2-letter country code (e.g. HR, DE, US).',
            'nationality_country_code.size' => 'Nationality must be exactly 2 characters.',
            'birth_year.between' => 'Birth year must be between ' . $minBirthYear . ' and ' . $maxBirthYear . ' (must be at least 14 years old).',
        ]);

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
            ->route('profile.edit')
            ->with('success', 'Profile updated successfully!');
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
                ->route('profile.edit')
                ->with('success', 'Photo deleted successfully!');
        }

        return redirect()->route('profile.edit');
    }
}
