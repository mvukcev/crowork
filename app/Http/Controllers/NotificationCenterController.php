<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationCenterController extends Controller
{
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
