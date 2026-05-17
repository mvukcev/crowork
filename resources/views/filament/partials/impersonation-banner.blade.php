@if(session('impersonation_original_admin_id'))
    <div
        class="mx-2 my-2 rounded-lg border border-amber-200 bg-amber-50 px-3 py-2"
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
        <div class="flex flex-wrap items-center justify-between gap-2">
            <div>
                <p class="text-sm font-semibold text-amber-900">
                    Impersonating {{ session('impersonation_employer_name') }}
                </p>
                <p class="text-xs text-amber-700">
                    Read-only mode enabled. Elapsed: <span class="font-semibold" x-text="elapsed"></span>
                </p>
            </div>
            <form method="POST" action="{{ route('impersonation.end') }}" x-ref="returnForm">
                @csrf
                <button type="button" @click.prevent="$refs.returnForm.submit()" class="inline-flex items-center rounded-md bg-amber-700 px-3 py-1.5 text-xs font-medium text-white hover:bg-amber-800">
                    Return to Admin
                </button>
            </form>
        </div>
    </div>
@endif
