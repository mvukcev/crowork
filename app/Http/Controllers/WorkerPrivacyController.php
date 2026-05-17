<?php

namespace App\Http\Controllers;

use App\Models\WorkerProfile;
use App\Services\AccountDeletionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WorkerPrivacyController extends Controller
{
    public function show(Request $request): View
    {
        $this->ensureWorker($request);

        $user = $request->user();
        $profile = WorkerProfile::firstOrCreate(
            ['user_id' => $user->id],
            [
                'first_name' => '',
                'last_name' => '',
                'nationality_country_code' => '',
                'birth_year' => 1940,
                'skills' => [],
            ]
        );

        $latestDeletionRequest = $user->accountDeletionRequests()
            ->latest('id')
            ->first();

        return view('worker.privacy', [
            'user' => $user,
            'profile' => $profile,
            'visibilityOptions' => WorkerProfile::visibilityOptions(),
            'latestDeletionRequest' => $latestDeletionRequest,
        ]);
    }

    public function updateVisibility(Request $request): RedirectResponse
    {
        $this->ensureWorker($request);

        $validated = $request->validate([
            'profile_visibility' => ['required', 'in:' . implode(',', array_keys(WorkerProfile::visibilityOptions()))],
        ]);

        $profile = WorkerProfile::firstOrCreate(
            ['user_id' => $request->user()->id],
            [
                'first_name' => '',
                'last_name' => '',
                'nationality_country_code' => '',
                'birth_year' => 1940,
                'skills' => [],
            ]
        );

        $profile->update([
            'profile_visibility' => $validated['profile_visibility'],
        ]);

        return redirect()
            ->route('worker.privacy.show')
            ->with('success', 'Profile visibility updated.');
    }

    public function requestDeletion(Request $request, AccountDeletionService $deletionService): RedirectResponse
    {
        $this->ensureWorker($request);

        $request->validate([
            'password' => ['required', 'current_password'],
            'reason' => ['nullable', 'string', 'max:1000'],
        ]);

        $deletionService->requestDeletion(
            $request->user(),
            $request->input('reason')
        );

        auth()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('access.show')
            ->with('status', 'account-deletion-requested');
    }

    private function ensureWorker(Request $request): void
    {
        if (! $request->user()?->isWorker()) {
            abort(403);
        }
    }
}
