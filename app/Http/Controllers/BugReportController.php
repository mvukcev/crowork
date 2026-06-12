<?php

namespace App\Http\Controllers;

use App\Models\BugReport;
use App\Models\ErrorLog;
use App\Models\User;
use App\Notifications\AdminNewBugReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class BugReportController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'description' => ['required', 'string', 'max:5000'],
            'page_uri' => ['nullable', 'url', 'max:2048'],
            'screenshot' => ['nullable', 'image', 'mimes:jpeg,png,webp', 'max:5120'],
        ]);

        $pageUri = (string) ($data['page_uri'] ?? '');
        if ($pageUri === '') {
            $pageUri = (string) url()->previous();
        }

        $screenshotPath = null;
        if ($request->hasFile('screenshot')) {
            $screenshotPath = $request->file('screenshot')->store('bug-reports', 'public');
        }

        $windowStart = now()->subMinutes(20);
        $recentErrors = ErrorLog::query()
            ->where('occurred_at', '>=', $windowStart)
            ->orderByDesc('occurred_at')
            ->limit(200)
            ->get([
                'id',
                'occurred_at',
                'level',
                'message',
                'exception_class',
                'uri',
                'method',
                'file',
                'line',
            ]);

        $snapshot = $recentErrors->map(static function (ErrorLog $row): array {
            return [
                'id' => $row->id,
                'occurred_at' => $row->occurred_at?->toIso8601String(),
                'level' => $row->level,
                'message' => $row->message,
                'exception_class' => $row->exception_class,
                'uri' => $row->uri,
                'method' => $row->method,
                'file' => $row->file,
                'line' => $row->line,
            ];
        })->values()->all();

        $bugReport = BugReport::query()->create([
            'user_id' => auth()->id(),
            'reporter_email' => auth()->user()?->email,
            'status' => BugReport::STATUS_OPEN,
            'page_uri' => mb_substr($pageUri, 0, 2048),
            'description' => $data['description'],
            'screenshot_path' => $screenshotPath,
            'error_logs_snapshot' => $snapshot,
            'error_logs_count' => count($snapshot),
            'reported_at' => now(),
        ]);

        try {
            $superAdmins = User::query()
                ->where('role', User::ROLE_ADMIN)
                ->where('is_super_admin', true)
                ->get();

            if ($superAdmins->isNotEmpty()) {
                Notification::send($superAdmins, new AdminNewBugReport($bugReport));
            } else {
                $fallbackEmail = setting('admin_notification_email');

                if (is_string($fallbackEmail) && trim($fallbackEmail) !== '') {
                    Notification::route('mail', trim($fallbackEmail))
                        ->notify(new AdminNewBugReport($bugReport));
                }
            }
        } catch (\Throwable $exception) {
            Log::warning('Failed to send admin new bug report email notification', [
                'bug_report_id' => $bugReport->id,
                'error' => $exception->getMessage(),
            ]);
        }

        return back()->with('success', 'Thanks. Your bug report has been submitted.');
    }
}
