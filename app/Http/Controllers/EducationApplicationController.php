<?php

namespace App\Http\Controllers;

use App\Models\Education;
use App\Models\EducationApplication;
use App\Models\WorkerProfile;
use App\Notifications\EducationApplicationSubmitted;
use App\Services\MetaConversionsAPIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EducationApplicationController extends Controller
{

    public function create(Education $education)
    {
        $this->ensureWorker();
        $this->ensureEducationIsAvailable($education);

        $profile = WorkerProfile::where('user_id', Auth::id())->first();

        if (!$profile || !$this->isProfileComplete($profile)) {
            return redirect()
                ->route('worker.profile.edit')
                ->with('warning', 'Please complete your profile before applying to education programs.');
        }

        $existingApplication = EducationApplication::where('education_id', $education->id)
            ->where('worker_id', Auth::id())
            ->first();

        return view('educations.apply', [
            'education' => $education,
            'profile' => $profile,
            'profileSnapshot' => $profile->toSnapshot(),
            'alreadyApplied' => $existingApplication !== null,
            'existingApplication' => $existingApplication,
        ]);
    }

    public function store(Request $request, Education $education)
    {
        $this->ensureWorker();
        $this->ensureEducationIsAvailable($education);

        $profile = WorkerProfile::where('user_id', Auth::id())->first();

        if (!$profile || !$this->isProfileComplete($profile)) {
            return redirect()
                ->route('worker.profile.edit')
                ->with('warning', 'Please complete your profile before applying to education programs.');
        }

        $existingApplication = EducationApplication::where('education_id', $education->id)
            ->where('worker_id', Auth::id())
            ->first();

        if ($existingApplication) {
            return redirect()
                ->route('educations.apply', $education)
                ->with('info', 'You have already applied to this education program.');
        }

        $validated = $request->validate([
            'message' => 'nullable|string|max:1000',
            'consent' => 'accepted',
        ]);

        $application = EducationApplication::create([
            'education_id' => $education->id,
            'worker_id' => Auth::id(),
            'profile_snapshot' => $profile->toSnapshot(),
            'message' => $validated['message'] ?? null,
            'status' => 'new',
        ]);

        $application->loadMissing('worker');
        $application->worker?->notify(new EducationApplicationSubmitted($application));

        try {
            app(MetaConversionsAPIService::class)->trackEducationApplicationSubmitted($application);
        } catch (\Throwable $exception) {
            Log::warning('Meta CAPI education application tracking failed', [
                'application_id' => $application->id,
                'error' => $exception->getMessage(),
            ]);
        }

        return redirect()
            ->route('educations.show', $education)
            ->with('success', 'Your education application has been submitted successfully.');
    }

    private function ensureWorker(): void
    {
        if (Auth::user()->role !== 'worker') {
            abort(403, 'Only workers can apply to education programs.');
        }
    }

    private function ensureEducationIsAvailable(Education $education): void
    {
        if ($education->status !== 'published' || ($education->expires_at && $education->expires_at->isPast())) {
            abort(404, 'This education program is no longer available.');
        }
    }

    private function isProfileComplete(WorkerProfile $profile): bool
    {
        return !empty($profile->first_name)
            && !empty($profile->last_name)
            && !empty($profile->nationality_country_code)
            && !empty($profile->birth_year);
    }
}
