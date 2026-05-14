@if(session('impersonation_original_admin_id'))
    <div
        class="fixed left-0 right-0 top-16 md:top-[72px] bg-amber-100/95 border-b border-amber-300 z-40 shadow-sm"
        x-data="{
            startedAt: '{{ session('impersonation_started_at') }}',
            elapsed: '--:--:--',
            tick() {
                if (!this.startedAt) {
                    this.elapsed = '--:--:--';
                    return;
                }

                const start = new Date(this.startedAt);
                const now = new Date();
                const total = Math.max(0, Math.floor((now - start) / 1000));
                const h = String(Math.floor(total / 3600)).padStart(2, '0');
                const m = String(Math.floor((total % 3600) / 60)).padStart(2, '0');
                const s = String(total % 60).padStart(2, '0');
                this.elapsed = `${h}:${m}:${s}`;
            }
        }"
        x-init="tick(); setInterval(() => tick(), 1000)"
    >
        <div class="cw-container py-2.5 px-4">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-700 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M13 16H3V4h10v2a1 1 0 100 2v2a1 1 0 100 2v2a1 1 0 100 2v2a1 1 0 100 2zm5.6-7.8a1 1 0 00-.6-1.9 1 1 0 00-1.4 1.4l1.6 1.6-1.6 1.6a1 1 0 001.4 1.4l2-2a1 1 0 00.2-1.2z" clip-rule="evenodd" />
                    </svg>
                    <div>
                        <p class="text-sm font-semibold text-amber-900 mb-0">
                            Impersonation active: <span class="font-bold">{{ session('impersonation_employer_name') }}</span>
                        </p>
                        <p class="text-xs text-amber-700 mt-0.5 mb-0">
                            Acting as employer account. Elapsed session: <span class="font-semibold" x-text="elapsed"></span>
                        </p>
                    </div>
                </div>

                <form method="POST" action="{{ route('impersonation.end') }}" class="flex-shrink-0">
                    @csrf
                    <button type="submit" class="px-3 py-1.5 bg-amber-700 hover:bg-amber-800 text-white text-sm font-medium rounded whitespace-nowrap transition-colors">
                        Return to Admin
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif
