<?php

namespace App\Http\Controllers;

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
        $deletionRequest->update(['status' => $request->input('status')]);
        return redirect()->route('admin.privacy_requests.index')->with('success', 'Request updated successfully.');
    }
}