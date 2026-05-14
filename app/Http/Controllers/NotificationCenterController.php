<?php

namespace App\Http\Controllers;

use App\Services\NotificationPreferenceService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationCenterController extends Controller
{
    public function editPreferences(Request $request, NotificationPreferenceService $preferences): View
    {
        $user = $request->user();

        return view('notifications.preferences', [
            'preferences' => $preferences->preferencesForUser($user),
            'categoryLabels' => NotificationPreferenceService::categoryLabels(),
        ]);
    }

    public function updatePreferences(Request $request, NotificationPreferenceService $preferences): RedirectResponse
    {
        $data = $request->validate([
            'preferences' => ['required', 'array'],
            'preferences.*.email_enabled' => ['nullable', 'boolean'],
            'preferences.*.database_enabled' => ['nullable', 'boolean'],
            'preferences.*.digest_frequency' => ['nullable', 'in:none,daily,weekly'],
        ]);

        /** @var array<string, array{email_enabled?: mixed, database_enabled?: mixed, digest_frequency?: mixed}> $input */
        $input = $data['preferences'];

        // Unchecked checkboxes are absent from request. Normalize booleans by category.
        foreach (array_keys(NotificationPreferenceService::categoryLabels()) as $category) {
            $input[$category] = [
                'email_enabled' => (bool) ($input[$category]['email_enabled'] ?? false),
                'database_enabled' => (bool) ($input[$category]['database_enabled'] ?? false),
                'digest_frequency' => (string) ($input[$category]['digest_frequency'] ?? 'none'),
            ];
        }

        $preferences->updateForUser($request->user(), $input);

        return redirect()
            ->route('notifications.preferences')
            ->with('success', 'Notification preferences updated.');
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $filter = (string) $request->query('filter', 'all');

        $query = $user->notifications()->latest();

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        }

        if ($filter === 'important') {
            $query->where('data', 'like', '%"importance":"high"%');
        }

        return view('notifications.index', [
            'notifications' => $query->paginate(20)->withQueryString(),
            'filter' => $filter,
            'unreadCount' => $user->unreadNotifications()->count(),
        ]);
    }

    public function markRead(Request $request, string $notificationId): RedirectResponse
    {
        $notification = $request->user()
            ->notifications()
            ->whereKey($notificationId)
            ->firstOrFail();

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        return back();
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        $request->user()->unreadNotifications->markAsRead();

        return back();
    }

    public function open(Request $request, string $notificationId): RedirectResponse
    {
        /** @var DatabaseNotification $notification */
        $notification = $request->user()
            ->notifications()
            ->whereKey($notificationId)
            ->firstOrFail();

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        $targetUrl = $notification->data['url'] ?? route('notifications.index');

        return redirect()->to($targetUrl);
    }
}
