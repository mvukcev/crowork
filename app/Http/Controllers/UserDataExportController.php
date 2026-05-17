<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class UserDataExportController extends Controller
{
    public function export(Request $request)
    {
        $user = $request->user();

        $applications = $user->jobApplications()
            ->with('job:id,title')
            ->get();

        $sessionMetadata = DB::table('sessions')
            ->where('user_id', $user->id)
            ->orderByDesc('last_activity')
            ->get(['id', 'ip_address', 'user_agent', 'last_activity'])
            ->map(function ($session) {
                return [
                    'id' => $session->id,
                    'ip_address' => $session->ip_address,
                    'user_agent' => $session->user_agent,
                    'last_activity' => (int) $session->last_activity,
                    'last_activity_at' => Carbon::createFromTimestamp((int) $session->last_activity)->toIso8601String(),
                ];
            })
            ->values()
            ->all();

        $data = [
            'exported_at' => now()->toIso8601String(),
            'user' => $user->toArray(),
            'worker_profile' => optional($user->workerProfile)->toArray(),
            'employer_profile' => optional($user->employer)->toArray(),
            'applications' => $applications->map(function ($application) {
                return [
                    'id' => $application->id,
                    'job_id' => $application->job_id,
                    'job_title' => $application->job?->title,
                    'status' => $application->status,
                    'message' => $application->message,
                    'profile_snapshot' => $application->profile_snapshot,
                    'job_snapshot' => $application->job_snapshot,
                    'uploaded_file_metadata' => [
                        'cv_path' => data_get($application->profile_snapshot, 'cv_path'),
                        'photo_path' => data_get($application->profile_snapshot, 'photo_path'),
                    ],
                    'created_at' => optional($application->created_at)?->toIso8601String(),
                ];
            })->values()->all(),
            'messages' => [
                'application_messages' => $applications->pluck('message')->filter()->values()->all(),
                'application_comments' => $user->applicationComments()->latest()->get()->toArray(),
            ],
            'notifications' => $user->notifications()->latest()->get()->toArray(),
            'notification_preferences' => $user->notificationPreferences()->get()->toArray(),
            'consent_history' => $user->consentHistories()->latest('accepted_at')->get()->toArray(),
            'deletion_requests' => $user->accountDeletionRequests()->latest()->get()->toArray(),
            'session_metadata' => $sessionMetadata,
            'saved_jobs' => [
                'items' => [],
                'note' => 'Saved jobs are not currently persisted in this version.',
            ],
        ];

        $fileName = 'user_data_' . $user->id . '_' . now()->format('Ymd_His') . '.json';
        Storage::disk('local')->put($fileName, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return response()->download(storage_path('app/' . $fileName))->deleteFileAfterSend(true);
    }
}