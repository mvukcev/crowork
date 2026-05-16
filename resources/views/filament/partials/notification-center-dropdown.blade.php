@php
    $notificationUser = auth()->user();
@endphp

@if($notificationUser)
    @php
        $notificationUnreadCount = $notificationUser->unreadNotifications()->count();
        $notificationItems = $notificationUser->notifications()->latest()->limit(8)->get();
    @endphp

    <div
        class="relative z-[180] shrink-0"
        x-data="{
            id: 'cw-filament-notifications',
            open: false,
            toggle() {
                const next = !this.open;
                this.open = next;

                if (next) {
                    window.dispatchEvent(new CustomEvent('cw:dropdown-open', { detail: { id: this.id } }));
                }
            }
        }"
        @keydown.escape.window="open = false"
        @click.outside="open = false"
        @cw:dropdown-open.window="if (! $event.detail || $event.detail.id !== id) open = false"
    >
        <button
            type="button"
            x-ref="button"
            class="cw-dashboard-icon-button relative"
            @click.prevent.stop="toggle()"
            aria-label="Notifications"
            aria-haspopup="true"
            :aria-expanded="open.toString()"
        >
            <svg class="cw-dashboard-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.85" d="M6.5 16.5h11l-1.2-1.2a2.3 2.3 0 01-.68-1.62V11.4a3.6 3.6 0 00-7.2 0v2.28c0 .61-.24 1.2-.67 1.63L6.5 16.5zM10.2 18.2a1.8 1.8 0 003.6 0"/>
            </svg>
            @if($notificationUnreadCount > 0)
                <span class="absolute -top-1 -right-1 min-w-[16px] h-[16px] px-1 rounded-full bg-[rgb(255,85,0)] text-white text-[10px] leading-[16px] text-center font-semibold">{{ min($notificationUnreadCount, 99) }}</span>
            @endif
        </button>

        <div
            x-show="open"
            x-cloak
            @click.stop
            style="display: none;"
            x-transition.origin.top.right
            x-transition:enter="transition ease-out duration-180"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-130"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute right-0 top-full mt-2 w-[22rem] max-w-[calc(100vw-2rem)] rounded-xl border border-gray-200 bg-white shadow-xl overflow-hidden z-[220] dark:border-white/10 dark:bg-black max-md:fixed max-md:left-4 max-md:right-4 max-md:top-16 max-md:mt-0"
        >
            <div class="px-3 py-2 border-b border-gray-100 dark:border-white/10 flex items-center justify-between">
                <p class="text-sm font-semibold text-gray-900 dark:text-gray-100">Notifications</p>
                <a href="{{ route('notifications.index') }}" class="text-xs text-gray-600 dark:text-gray-300" @click="open = false">View all</a>
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
                        <a href="{{ route('notifications.open', $notification->id) }}" class="block px-3 py-2 hover:bg-gray-50 dark:hover:bg-white/5 {{ $notification->read_at ? '' : 'bg-primary-50/70 dark:bg-primary-900/15' }}" @click="open = false">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $title }}</p>
                            <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2">{{ $message }}</p>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif
