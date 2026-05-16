<x-app-layout>
    <x-slot name="title">Notifications</x-slot>

    <section class="cw-section">
        <div class="cw-container cw-content-wide">
            <div class="flex flex-col items-start gap-4 mb-6 md:flex-row md:items-center md:justify-between">
                <div class="min-w-0 flex-1">
                    <p class="cw-kicker mb-1">Notification center</p>
                    <h1 class="cw-display text-4xl leading-tight md:text-5xl break-words">Your notifications</h1>
                    <p class="text-sm text-slate-600 mt-2">Unread: {{ $unreadCount }}</p>
                </div>
                <div class="flex w-full flex-col gap-2 md:w-auto md:flex-row md:items-center md:justify-end">
                    <form method="POST" action="{{ route('notifications.read-all') }}" class="w-full md:w-auto" data-cw-track-submit="notification_mark_all_read">
                        @csrf
                        <button type="submit" class="cw-button-secondary w-full md:w-auto">Mark all as read</button>
                    </form>
                    <a href="{{ route('notifications.preferences') }}" class="cw-button-secondary w-full md:w-auto text-center" data-cw-track-click="navigation_click">Preferences</a>
                </div>
            </div>

            <div class="flex gap-2 mb-4">
                <a href="{{ route('notifications.index', ['filter' => 'all']) }}" class="cw-button-secondary {{ $filter === 'all' ? 'border-slate-400' : '' }}">All</a>
                <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" class="cw-button-secondary {{ $filter === 'unread' ? 'border-slate-400' : '' }}">Unread</a>
                <a href="{{ route('notifications.index', ['filter' => 'important']) }}" class="cw-button-secondary {{ $filter === 'important' ? 'border-slate-400' : '' }}">Important notices</a>
            </div>

            <div class="cw-surface overflow-hidden">
                @if($notifications->count() === 0)
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-10 w-10 text-slate-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.41-1.41a2 2 0 0 1-.59-1.42V11a6 6 0 1 0-12 0v3.17a2 2 0 0 1-.59 1.42L4 17h5"></path>
                        </svg>
                        <h2 class="text-lg font-semibold text-slate-900 mt-2">You're all caught up</h2>
                        <p class="text-slate-600 mt-1">No notifications matching this filter.</p>
                        <p class="text-sm text-slate-500 mt-1">When your application status changes, updates will appear here.</p>
                        <div class="mt-4 flex flex-wrap justify-center gap-2">
                            <a href="{{ route('notifications.index', ['filter' => 'all']) }}" class="cw-button-secondary">View all notifications</a>
                            <a href="{{ route('jobs.index') }}" class="cw-button-primary">Browse jobs</a>
                        </div>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach($notifications as $notification)
                            @php
                                $payload = $notification->data;
                                $title = $payload['title'] ?? class_basename($notification->type);
                                $message = $payload['message'] ?? 'You have a new notification.';
                                $url = $payload['url'] ?? route('notifications.index');
                                $category = $payload['category'] ?? 'general';
                                $importance = $payload['importance'] ?? 'normal';
                            @endphp
                            <article class="p-4 {{ $notification->read_at ? 'bg-white' : 'bg-blue-50/40' }}">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <div class="flex items-center gap-2 mb-1">
                                            <h2 class="text-sm font-semibold text-slate-900">{{ $title }}</h2>
                                            @if($importance === 'high')
                                                <span class="inline-flex text-[10px] font-semibold uppercase tracking-wide px-2 py-0.5 rounded-full bg-amber-100 text-amber-700">Important</span>
                                            @endif
                                            <span class="inline-flex text-[10px] uppercase tracking-wide px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">{{ str_replace('_', ' ', $category) }}</span>
                                        </div>
                                        <p class="text-sm text-slate-700">{{ $message }}</p>
                                        <p class="text-xs text-slate-500 mt-1">{{ $notification->created_at?->diffForHumans() }}</p>
                                    </div>
                                    <div class="flex flex-col sm:flex-row gap-2">
                                        <a href="{{ route('notifications.open', $notification->id) }}" class="cw-button-secondary" data-cw-track-click="notification_open">Open</a>
                                        @if($notification->read_at === null)
                                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}">
                                                @csrf
                                                <button type="submit" class="cw-button-secondary">Mark as read</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                    <div class="p-4">{{ $notifications->links() }}</div>
                @endif
            </div>
        </div>
    </section>
</x-app-layout>
