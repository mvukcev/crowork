@auth
    @php
        $notificationUser = auth()->user();
        $notificationUnreadCount = $notificationUser->unreadNotifications()->count();
        $notificationItems = $notificationUser->notifications()->latest()->limit(8)->get();
    @endphp

    <div class="relative">
        <button
            type="button"
            class="cw-header-icon-button cw-nav-control relative"
            data-cw-track-click="notification_open"
            aria-label="Notifications"
            aria-expanded="false"
            aria-controls="cw-header-notification-menu"
            data-cw-dropdown-trigger="cw-header-notification-menu"
        >
            <svg class="cw-header-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.85" d="M6.5 16.5h11l-1.2-1.2a2.3 2.3 0 01-.68-1.62V11.4a3.6 3.6 0 00-7.2 0v2.28c0 .61-.24 1.2-.67 1.63L6.5 16.5zM10.2 18.2a1.8 1.8 0 003.6 0"/>
            </svg>
            @if($notificationUnreadCount > 0)
                <span class="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] px-1 rounded-full bg-[rgb(255,85,0)] text-white text-[10px] leading-[18px] text-center font-semibold">{{ min($notificationUnreadCount, 99) }}</span>
            @endif
        </button>

        <div
            id="cw-header-notification-menu"
            data-cw-dropdown-panel
            aria-hidden="true"
            style="display: none;"
            class="cw-dropdown-panel absolute right-0 mt-2 z-[130] w-[22rem] max-w-[calc(100vw-2rem)] overflow-hidden max-md:fixed max-md:left-4 max-md:right-4 max-md:top-16 max-md:mt-0"
        >
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <p class="text-sm font-semibold text-slate-900">Notifications</p>
                <a href="{{ route('notifications.index') }}" class="text-xs text-slate-600 hover:text-slate-900" data-cw-track-click="notification_open">View all</a>
            </div>

            @if($notificationItems->isEmpty())
                <p class="px-4 py-6 text-sm text-slate-500">No notifications yet.</p>
            @else
                <div class="max-h-96 overflow-auto divide-y divide-slate-100">
                    @foreach($notificationItems as $notification)
                        @php
                            $payload = $notification->data;
                            $title = $payload['title'] ?? class_basename($notification->type);
                            $message = $payload['message'] ?? 'You have a new notification.';
                            $importance = $payload['importance'] ?? 'normal';
                        @endphp
                        <a href="{{ route('notifications.open', $notification->id) }}" class="block px-4 py-3 hover:bg-slate-50 {{ $notification->read_at ? '' : 'bg-blue-50/40' }}" data-cw-track-click="notification_open">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <p class="text-sm font-semibold text-slate-900 truncate">{{ $title }}</p>
                                @if($importance === 'high')
                                    <span class="text-[10px] uppercase px-1.5 py-0.5 rounded bg-amber-100 text-amber-700">Important</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-600 line-clamp-2">{{ $message }}</p>
                            <p class="text-[11px] text-slate-500 mt-1">{{ $notification->created_at?->diffForHumans() }}</p>
                        </a>
                    @endforeach
                </div>
            @endif

            <div class="p-3 border-t border-slate-100 flex items-center justify-between">
                <form method="POST" action="{{ route('notifications.read-all') }}" data-cw-track-submit="notification_mark_all_read">
                    @csrf
                    <button type="submit" class="text-xs text-slate-600 hover:text-slate-900">Mark all as read</button>
                </form>
                <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="text-xs text-slate-600 hover:text-slate-900">Unread only</a>
            </div>
        </div>
    </div>
@endauth
