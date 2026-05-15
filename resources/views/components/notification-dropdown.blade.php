@auth
    @php
        $notificationUser = auth()->user();
        $notificationUnreadCount = $notificationUser->unreadNotifications()->count();
        $notificationItems = $notificationUser->notifications()->latest()->limit(8)->get();
    @endphp

    <div
        class="relative"
        x-data="{
            open: false,
            panelStyle: '',
            toggle() {
                this.open = !this.open;
                if (this.open) {
                    this.$nextTick(() => this.positionPanel());
                }
            },
            close() {
                this.open = false;
            },
            positionPanel() {
                const button = this.$refs.button;
                if (!button) {
                    return;
                }

                const rect = button.getBoundingClientRect();
                const viewportWidth = window.innerWidth;
                const panelWidth = Math.min(360, Math.max(280, viewportWidth - 32));
                const left = Math.max(16, Math.min(rect.right - panelWidth, viewportWidth - panelWidth - 16));
                const top = rect.bottom + 12;

                this.panelStyle = `top: ${top}px; left: ${left}px; width: ${panelWidth}px; max-width: calc(100vw - 2rem);`;
            },
        }"
        @keydown.escape.window="close()"
        @click.window="
            if (!open) {
                return;
            }

            const panel = $refs.panel;
            const button = $refs.button;

            if (panel?.contains($event.target) || button?.contains($event.target)) {
                return;
            }

            close();
        "
        @resize.window="open && positionPanel()"
        @scroll.window="open && positionPanel()"
    >
        <a
            href="{{ route('notifications.index') }}"
            x-ref="button"
            class="relative inline-flex items-center justify-center h-10 w-10 rounded-xl border border-slate-200 bg-white text-slate-800 cw-nav-control"
            @click.prevent.stop="toggle()"
            data-cw-track-click="notification_open"
            aria-label="Notifications"
            :aria-expanded="open"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15 17h5l-1.4-1.4a2 2 0 01-.6-1.4V11a6 6 0 10-12 0v3.2c0 .53-.21 1.04-.59 1.42L4 17h5m6 0a3 3 0 11-6 0m6 0H9"/>
            </svg>
            @if($notificationUnreadCount > 0)
                <span class="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] px-1 rounded-full bg-rose-600 text-white text-[10px] leading-[18px] text-center font-semibold">{{ min($notificationUnreadCount, 99) }}</span>
            @endif
        </a>

        <template x-teleport="body">
            <div
                x-show="open"
                x-cloak
                x-ref="panel"
                :style="panelStyle"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="fixed rounded-xl border border-slate-200 bg-white shadow-xl overflow-hidden z-50"
            >
                <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                    <p class="text-sm font-semibold text-slate-900">Notifications</p>
                    <a href="{{ route('notifications.index') }}" class="text-xs text-slate-600 hover:text-slate-900" data-cw-track-click="notification_open" @click="close()">View all</a>
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
                            <a href="{{ route('notifications.open', $notification->id) }}" class="block px-4 py-3 hover:bg-slate-50 {{ $notification->read_at ? '' : 'bg-blue-50/40' }}" data-cw-track-click="notification_open" @click="close()">
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
                    <form method="POST" action="{{ route('notifications.read-all') }}" data-cw-track-submit="notification_mark_all_read" @submit="close()">
                        @csrf
                        <button type="submit" class="text-xs text-slate-600 hover:text-slate-900">Mark all as read</button>
                    </form>
                    <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="text-xs text-slate-600 hover:text-slate-900" @click="close()">Unread only</a>
                </div>
            </div>
        </template>
    </div>
@endauth
