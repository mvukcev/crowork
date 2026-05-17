<?php

namespace App\Http\Controllers;

use App\Jobs\AnonymizeUserDataJob;
use App\Models\AccountDeletionRequest;
use Illuminate\Http\Request;

class AdminPrivacyRequestController extends Controller
{
    public function index()
    {
        $requests = AccountDeletionRequest::with('user')->get();
        return view('admin.privacy_requests.index', compact('requests'));
    }

    public function update(Request $request, AccountDeletionRequest $deletionRequest)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:' . implode(',', [
                AccountDeletionRequest::STATUS_PENDING,
                AccountDeletionRequest::STATUS_COMPLETED,
                AccountDeletionRequest::STATUS_CANCELLED,
            ])],
        ]);

        $status = $validated['status'];

        if ($status === AccountDeletionRequest::STATUS_COMPLETED) {
            AnonymizeUserDataJob::dispatchSync($deletionRequest->user_id, $deletionRequest->id);

            return redirect()->route('admin.privacy_requests.index')->with('success', 'Request updated successfully.');
        }

        $deletionRequest->update([
            'status' => $status,
            'completed_at' => null,
        ]);

        if ($status === AccountDeletionRequest::STATUS_CANCELLED) {
            $deletionRequest->user?->forceFill([
                'pending_deletion' => false,
                'deletion_requested_at' => null,
                'anonymization_scheduled_at' => null,
            ])->save();
        }

        return redirect()->route('admin.privacy_requests.index')->with('success', 'Request updated successfully.');
    }
}