<x-filament-panels::page>
    @php
        $statusCounts = collect($checks)->countBy('status');
    @endphp

    <div class="space-y-4">
        <div class="grid gap-3 sm:grid-cols-3">
            <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 dark:border-emerald-500/30 dark:bg-emerald-500/10">
                <p class="text-xs font-semibold uppercase tracking-wide text-emerald-700 dark:text-emerald-300">OK</p>
                <p class="mt-1 text-2xl font-bold text-emerald-800 dark:text-emerald-200">{{ (int) ($statusCounts['ok'] ?? 0) }}</p>
            </div>
            <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 dark:border-amber-500/30 dark:bg-amber-500/10">
                <p class="text-xs font-semibold uppercase tracking-wide text-amber-700 dark:text-amber-300">Warn</p>
                <p class="mt-1 text-2xl font-bold text-amber-800 dark:text-amber-200">{{ (int) ($statusCounts['warn'] ?? 0) }}</p>
            </div>
            <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 dark:border-rose-500/30 dark:bg-rose-500/10">
                <p class="text-xs font-semibold uppercase tracking-wide text-rose-700 dark:text-rose-300">Fail</p>
                <p class="mt-1 text-2xl font-bold text-rose-800 dark:text-rose-200">{{ (int) ($statusCounts['fail'] ?? 0) }}</p>
            </div>
        </div>

        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-white/10">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50 dark:bg-white/5">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">{{ __('system.check') }}</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">{{ __('system.status') }}</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">{{ __('system.value') }}</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-700 dark:text-gray-200">{{ __('system.details') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($checks as $check)
                        @php
                            $badgeClasses = match ($check['status']) {
                                'ok' => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20 dark:bg-emerald-500/15 dark:text-emerald-300',
                                'warn' => 'bg-amber-50 text-amber-700 ring-amber-600/20 dark:bg-amber-500/15 dark:text-amber-300',
                                default => 'bg-rose-50 text-rose-700 ring-rose-600/20 dark:bg-rose-500/15 dark:text-rose-300',
                            };
                            $rowClasses = match ($check['status']) {
                                'fail' => 'bg-rose-50/50 dark:bg-rose-900/10',
                                'warn' => 'bg-amber-50/40 dark:bg-amber-900/10',
                                default => '',
                            };
                        @endphp
                        <tr class="border-t border-gray-100 dark:border-white/10 align-top {{ $rowClasses }}">
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $check['label'] }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium ring-1 ring-inset {{ $badgeClasses }}">
                                    {{ strtoupper($check['status']) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-900 dark:text-gray-100">{{ $check['value'] }}</td>
                            <td class="px-4 py-3 text-gray-600 dark:text-gray-300 break-all">{{ $check['details'] }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-filament-panels::page>
