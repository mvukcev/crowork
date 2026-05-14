<?php

namespace App\Http\Controllers\Admin;

use App\Models\ImpersonationLog;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\RedirectResponse;

class ImpersonationController
{
    public function start($userId): RedirectResponse
    {
        $admin = auth()->user();

        if (! $admin || ! ($admin->isAdmin() || $admin->isMod())) {
            throw new AuthenticationException('Only admins can impersonate users.');
        }

        // Check if admin is already impersonating
        $existing = session('impersonation_original_admin_id');
        if ($existing) {
            return back()->with('error', 'Cannot nest impersonations. End current impersonation first.');
        }

        $employer = User::findOrFail($userId);

        // Prevent impersonating admins/mods
        if ($employer->isAdmin() || $employer->isMod()) {
            return back()->with('error', 'Cannot impersonate admin or moderator accounts.');
        }

        // Only allow impersonating employer users
        if ($employer->role !== User::ROLE_EMPLOYER) {
            return back()->with('error', 'Can only impersonate employer accounts.');
        }

        // Start impersonation: store original admin in session, login as employer
        ImpersonationLog::startImpersonation($admin, $employer);

        session([
            'impersonation_original_admin_id' => $admin->id,
            'impersonation_admin_email' => $admin->email,
            'impersonation_employer_name' => $employer->email,
        ]);

        auth()->loginUsingId($employer->id, remember: false);

        return redirect()->route('employer.dashboard')
            ->with('success', "Logged in as {$employer->email}. You are in impersonation mode.");
    }

    public function end(): RedirectResponse
    {
        $originalAdminId = session('impersonation_original_admin_id');

        if (! $originalAdminId) {
            return back()->with('error', 'No active impersonation session.');
        }

        // End the impersonation log
        $log = ImpersonationLog::where('admin_user_id', $originalAdminId)
            ->whereNull('ended_at')
            ->latest('started_at')
            ->first();

        if ($log) {
            $log->end();
        }

        // Return to admin
        session()->forget([
            'impersonation_original_admin_id',
            'impersonation_admin_email',
            'impersonation_employer_name',
        ]);

        auth()->loginUsingId($originalAdminId, remember: false);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Impersonation ended. Logged back as admin.');
    }
}
