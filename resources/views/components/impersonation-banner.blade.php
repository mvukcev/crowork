@if(session('impersonation_original_admin_id'))
    <div class="fixed top-0 left-0 right-0 bg-amber-100 dark:bg-amber-900 border-b border-amber-300 dark:border-amber-700 z-50 shadow-md">
        <div class="cw-container py-3 px-4">
            <div class="flex items-center justify-between gap-4">
                <div class="flex items-center gap-3 flex-1">
                    <svg class="w-5 h-5 text-amber-700 dark:text-amber-300 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M13 16H3V4h10v2a1 1 0 100 2v2a1 1 0 100 2v2a1 1 0 100 2v2a1 1 0 100 2zm5.6-7.8a1 1 0 00-.6-1.9 1 1 0 00-1.4 1.4l1.6 1.6-1.6 1.6a1 1 0 001.4 1.4l2-2a1 1 0 00.2-1.2z" clip-rule="evenodd" />
                    </svg>
                    <p class="text-sm font-semibold text-amber-800 dark:text-amber-100">
                        You are viewing as
                        <span class="font-bold">{{ session('impersonation_employer_name') }}</span>
                    </p>
                </div>
                <form method="POST" action="{{ route('impersonation.end') }}" class="flex-shrink-0">
                    @csrf
                    <button type="submit" class="px-3 py-1 bg-amber-600 hover:bg-amber-700 dark:bg-amber-700 dark:hover:bg-amber-600 text-white text-sm font-medium rounded whitespace-nowrap transition-colors">
                        End impersonation
                    </button>
                </form>
            </div>
        </div>
    </div>
@endif
