@auth
    @php
        $notificationUser = auth()->user();
        $notificationUnreadCount = $notificationUser->unreadNotifications()->count();
        $notificationItems = $notificationUser->notifications()->latest()->limit(8)->get();
    @endphp

    <div class="relative" x-data="{ open: false }" @keydown.escape.window="open = false">
        <button
            type="button"
            class="relative inline-flex items-center justify-center h-10 w-10 rounded-xl border border-slate-200 bg-white text-slate-800"
            @click="open = ! open"
            aria-label="Notifications"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4a2 2 0 01-.6-1.4V11a6 6 0 10-12 0v3.2c0 .53-.21 1.04-.59 1.42L4 17h5m6 0a3 3 0 11-6 0m6 0H9"/>
            </svg>
            @if($notificationUnreadCount > 0)
                <span class="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-600 text-white text-[10px] leading-[18px] text-center font-semibold">{{ min($notificationUnreadCount, 99) }}</span>
            @endif
        </button>

        <div
            x-show="open"
            x-cloak
            @click.outside="open = false"
            x-transition
            class="absolute right-0 mt-2 w-[22rem] max-w-[90vw] rounded-xl border border-slate-200 bg-white shadow-xl overflow-hidden z-50"
        >
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <p class="text-sm font-semibold text-slate-900">Notifications</p>
                <a href="{{ route('notifications.index') }}" class="text-xs text-slate-600 hover:text-slate-900">View all</a>
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
                        <a href="{{ route('notifications.open', $notification->id) }}" class="block px-4 py-3 hover:bg-slate-50 {{ $notification->read_at ? '' : 'bg-blue-50/40' }}">
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
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit" class="text-xs text-slate-600 hover:text-slate-900">Mark all as read</button>
                </form>
                <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="text-xs text-slate-600 hover:text-slate-900">Unread only</a>
            </div>
        </div>
    </div>
@endauth
