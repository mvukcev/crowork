@php
    $notificationUser = filament()->auth()->user();
@endphp

@if($notificationUser)
    @php
        $notificationUnreadCount = $notificationUser->unreadNotifications()->count();
        $notificationItems = $notificationUser->notifications()->latest()->limit(8)->get();
    @endphp

    <div
        class="relative z-[180] shrink-0 inline-flex items-center"
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
            class="relative inline-flex h-8 w-8 min-h-8 min-w-8 shrink-0 items-center justify-center rounded-full border-0 bg-transparent p-0 text-gray-700 transition hover:bg-gray-100 hover:text-gray-900 dark:text-gray-200 dark:hover:text-white"
            @click.prevent.stop="toggle()"
            aria-label="Notifications"
            aria-haspopup="true"
            :aria-expanded="open.toString()"
        >
            <svg class="block shrink-0" style="width: 15px; height: 15px;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M15 17h5l-1.41-1.41a2 2 0 0 1-.59-1.42V11a6 6 0 1 0-12 0v3.17a2 2 0 0 1-.59 1.42L4 17h5"/>
                <path d="M9.5 17a2.5 2.5 0 0 0 5 0"/>
            </svg>
            @if($notificationUnreadCount > 0)
                <span class="absolute -top-1 -right-1 min-w-[14px] h-[14px] px-1 rounded-full bg-[rgb(254,80,0)] text-white text-[9px] leading-[14px] text-center font-semibold">{{ min($notificationUnreadCount, 99) }}</span>
            @endif
        </button>

        <div
            x-cloak
            @click.stop
            x-bind:style="open ? 'display: block; position: fixed; top: 4.25rem; right: 1rem; width: min(22rem, calc(100vw - 2rem));' : 'display: none;'"
            class="cw-admin-notification-panel rounded-xl border border-gray-200 bg-white text-gray-900 shadow-xl overflow-hidden z-[260]"
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

        <style>
            .cw-admin-notification-panel {
                max-height: min(32rem, calc(100vh - 5.5rem));
            }

            .dark .cw-admin-notification-panel {
                background-color: rgb(11 34 61);
                border-color: rgba(221, 229, 237, 0.24);
                color: rgb(244 247 250);
            }

            .dark .cw-admin-notification-panel .border-gray-100 {
                border-color: rgba(221, 229, 237, 0.2) !important;
            }

            .dark .cw-admin-notification-panel .text-gray-900 {
                color: rgb(244 247 250) !important;
            }

            .dark .cw-admin-notification-panel .text-gray-600,
            .dark .cw-admin-notification-panel .text-gray-500 {
                color: rgba(221, 229, 237, 0.84) !important;
            }

            .dark .cw-admin-notification-panel a:hover {
                background: rgba(254, 80, 0, 0.1) !important;
            }

            @media (max-width: 767px) {
                .cw-admin-notification-panel {
                    right: 1rem !important;
                    left: 1rem !important;
                    width: auto !important;
                    top: 4rem !important;
                }
            }
        </style>
    </div>
@endif
