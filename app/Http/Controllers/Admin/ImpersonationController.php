<?php

namespace App\Http\Controllers\Admin;

use App\Models\AuditLog;
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
        $log = ImpersonationLog::startImpersonation($admin, $employer, [
            'event' => 'impersonation_started',
            'admin_id' => $admin->id,
            'admin_email' => $admin->email,
            'employer_id' => $employer->id,
            'employer_email' => $employer->email,
            'started_from_path' => request()->path(),
            'started_at' => now()->toIso8601String(),
        ]);

        AuditLog::logAction(
            action: 'impersonation_started',
            user: $admin,
            subjectType: User::class,
            subjectId: $employer->id,
            changes: [
                'log_id' => $log->id,
                'admin_email' => $admin->email,
                'impersonated_email' => $employer->email,
                'started_from_path' => request()->path(),
            ],
            description: 'Admin started impersonating employer account.'
        );

        session([
            'impersonation_original_admin_id' => $admin->id,
            'impersonation_admin_email' => $admin->email,
            'impersonation_employer_name' => $employer->email,
            'impersonation_employer_id' => $employer->id,
            'impersonation_started_at' => now()->toIso8601String(),
            'impersonation_log_id' => $log->id,
        ]);

        auth()->loginUsingId($employer->id, remember: false);

        return redirect()->route('employer.dashboard')
            ->with('success', "Logged in as {$employer->email}. You are in impersonation mode.");
    }

    public function end(): RedirectResponse
    {
        $originalAdminId = session('impersonation_original_admin_id');
        $startedAt = session('impersonation_started_at');
        $logId = session('impersonation_log_id');

        if (! $originalAdminId) {
            return back()->with('error', 'No active impersonation session.');
        }

        // End the impersonation log with detail notes
        $log = null;

        if ($logId) {
            $log = ImpersonationLog::whereKey($logId)
                ->where('admin_user_id', $originalAdminId)
                ->whereNull('ended_at')
                ->first();
        }

        if (! $log) {
            $log = ImpersonationLog::where('admin_user_id', $originalAdminId)
                ->whereNull('ended_at')
                ->latest('started_at')
                ->first();
        }

        if ($log) {
            $durationSeconds = $startedAt
                ? now()->diffInSeconds(
                    \Illuminate\Support\Carbon::parse((string) $startedAt),
                    false
                )
                : null;

            $log->appendNotes([
                'event' => 'impersonation_ended',
                'ended_at' => now()->toIso8601String(),
                'ended_from_path' => request()->path(),
                'duration_seconds' => $durationSeconds,
            ]);

            $log->end();

            $adminUser = User::find($originalAdminId);
            if ($adminUser) {
                $employerId = session('impersonation_employer_id');

                AuditLog::logAction(
                    action: 'impersonation_ended',
                    user: $adminUser,
                    subjectType: User::class,
                    subjectId: is_numeric($employerId) ? (int) $employerId : null,
                    changes: [
                        'log_id' => $log->id,
                        'duration_seconds' => $durationSeconds,
                        'ended_from_path' => request()->path(),
                    ],
                    description: 'Admin ended impersonation and returned to admin account.'
                );
            }
        }

        // Return to admin
        session()->forget([
            'impersonation_original_admin_id',
            'impersonation_admin_email',
            'impersonation_employer_name',
            'impersonation_employer_id',
            'impersonation_started_at',
            'impersonation_log_id',
        ]);

        auth()->loginUsingId($originalAdminId, remember: false);

        return redirect()->route('admin.dashboard')
            ->with('success', 'Impersonation ended. Logged back as admin.');
    }
}
