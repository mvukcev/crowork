<x-filament-panels::page>
    <div class="space-y-4">
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
                        @endphp
                        <tr class="border-t border-gray-100 dark:border-white/10 align-top">
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
