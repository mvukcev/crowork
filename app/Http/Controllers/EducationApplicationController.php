<?php

namespace App\Http\Controllers;

use App\Jobs\SendMetaEventJob;
use App\Models\Education;
use App\Models\EducationApplication;
use App\Models\WorkerProfile;
use App\Notifications\EducationApplicationSubmitted;
use App\Services\ConsentConfigService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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
                ->with('warning', __('ui.educations_apply.flash_complete_profile'));
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
                ->with('warning', __('ui.educations_apply.flash_complete_profile'));
        }

        $existingApplication = EducationApplication::where('education_id', $education->id)
            ->where('worker_id', Auth::id())
            ->first();

        if ($existingApplication) {
            return redirect()
                ->route('educations.apply', $education)
                ->with('info', __('ui.educations_apply.flash_already_applied'));
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

        $metaEventId = null;
        if (ConsentConfigService::hasMarketingConsent($request, $request->user())) {
            $metaEventId = (string) Str::uuid();
            SendMetaEventJob::dispatch(
                'education_application_submitted',
                [
                    'application_id' => $application->id,
                    'event_source_url' => $request->fullUrl(),
                    'client_user_agent' => $request->userAgent(),
                    'client_ip_address' => $request->ip(),
                ],
                $metaEventId,
            );
        }

        $this->queueTrackEvent('education_apply_complete', [
            'source' => 'education_apply_form',
            'event_id' => $metaEventId,
            'education_slug' => $education->slug,
            'education_id' => $education->id,
            'application_id' => $application->id,
        ]);

        return redirect()
            ->route('educations.show', $education)
            ->with('success', __('ui.educations_apply.flash_submitted_success'));
    }

    private function ensureWorker(): void
    {
        if (Auth::user()->role !== 'worker') {
            abort(403, __('ui.educations_apply.error_only_workers'));
        }
    }

    private function ensureEducationIsAvailable(Education $education): void
    {
        if ($education->status !== 'published' || ($education->expires_at && $education->expires_at->isPast())) {
            abort(404, __('ui.educations_apply.error_no_longer_available'));
        }
    }

    private function isProfileComplete(WorkerProfile $profile): bool
    {
        return !empty($profile->first_name)
            && !empty($profile->last_name)
            && !empty($profile->nationality_country_code)
            && !empty($profile->birth_year);
    }

    private function queueTrackEvent(string $event, array $payload = []): void
    {
        $queue = session('cw_track_queue', []);
        if (! is_array($queue)) {
            $queue = [];
        }

        $queue[] = [
            'event' => $event,
            'payload' => $payload,
        ];

        session(['cw_track_queue' => $queue]);
    }
}
