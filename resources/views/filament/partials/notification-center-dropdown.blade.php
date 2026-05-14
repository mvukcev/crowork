@php
    $notificationUser = auth()->user();
@endphp

@if($notificationUser)
    @php
        $notificationUnreadCount = $notificationUser->unreadNotifications()->count();
        $notificationItems = $notificationUser->notifications()->latest()->limit(8)->get();
    @endphp

    <div x-data="{ open: false }" class="relative">
        <button
            type="button"
            class="fi-icon-btn relative"
            @click="open = !open"
            aria-label="Notifications"
        >
            <x-heroicon-o-bell class="h-5 w-5" />
            @if($notificationUnreadCount > 0)
                <span class="absolute -top-1 -right-1 min-w-[16px] h-[16px] px-1 rounded-full bg-danger-600 text-white text-[10px] leading-[16px] text-center font-semibold">{{ min($notificationUnreadCount, 99) }}</span>
            @endif
        </button>

        <div
            x-show="open"
            x-cloak
            @click.outside="open = false"
            class="absolute right-0 mt-2 w-[22rem] max-w-[85vw] rounded-xl border border-gray-200 bg-white shadow-xl overflow-hidden z-50 dark:border-white/10 dark:bg-gray-900"
        >
            <div class="px-3 py-2 border-b border-gray-100 dark:border-white/10 flex items-center justify-between">
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Notifications</p>
                <a href="{{ route('notifications.index') }}" class="text-xs text-gray-600 dark:text-gray-300">View all</a>
            </div>

            @if($notificationItems->isEmpty())
                <p class="px-3 py-5 text-sm text-gray-500 dark:text-gray-400">No notifications yet.</p>
            @else
                <div class="max-h-96 overflow-auto divide-y divide-gray-100 dark:divide-white/10">
                    @foreach($notificationItems as $notification)
                        @php
                            $payload = $notification->data;
                            $title = $payload['title'] ?? class_basename($notification->type);
                            $message = $payload['message'] ?? 'You have a new notification.';
                        @endphp
                        <a href="{{ route('notifications.open', $notification->id) }}" class="block px-3 py-2 hover:bg-gray-50 dark:hover:bg-white/5 {{ $notification->read_at ? '' : 'bg-primary-50/70 dark:bg-primary-900/15' }}">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $title }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2">{{ $message }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif
