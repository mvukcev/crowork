<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-700">From</label>
                    <input type="date" wire:model.live="from" class="w-full rounded-lg border-gray-300" />
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-700">To</label>
                    <input type="date" wire:model.live="to" class="w-full rounded-lg border-gray-300" />
                </div>
                <div class="flex items-end gap-2">
                    <x-filament::button wire:click="refresh">Refresh</x-filament::button>
                    <x-filament::button tag="a" color="gray" :href="route('admin.hzz-analytics.export', ['format' => 'csv', 'month' => $this->monthForExport()])">
                        Export CSV
                    </x-filament::button>
                    <x-filament::button tag="a" color="gray" :href="route('admin.hzz-analytics.export', ['format' => 'xlsx', 'month' => $this->monthForExport()])">
                        Export Excel
                    </x-filament::button>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section heading="Overview">
            <div class="grid grid-cols-2 md:grid-cols-4 xl:grid-cols-8 gap-3">
                <div class="rounded-xl border p-3"><p class="text-xs text-gray-500">HZZ jobs</p><p class="text-xl font-semibold">{{ $overview['total_hzz_jobs'] ?? 0 }}</p></div>
                <div class="rounded-xl border p-3"><p class="text-xs text-gray-500">Views</p><p class="text-xl font-semibold">{{ $overview['total_views'] ?? 0 }}</p></div>
                <div class="rounded-xl border p-3"><p class="text-xs text-gray-500">Unique views</p><p class="text-xl font-semibold">{{ $overview['unique_views'] ?? 0 }}</p></div>
                <div class="rounded-xl border p-3"><p class="text-xs text-gray-500">CTA clicks</p><p class="text-xl font-semibold">{{ $overview['cta_clicks'] ?? 0 }}</p></div>
                <div class="rounded-xl border p-3"><p class="text-xs text-gray-500">CTR</p><p class="text-xl font-semibold">{{ $overview['ctr_percent'] ?? 0 }}%</p></div>
                <div class="rounded-xl border p-3"><p class="text-xs text-gray-500">CroWork applications</p><p class="text-xl font-semibold">{{ $overview['applications_sent'] ?? 0 }}</p></div>
                <div class="rounded-xl border p-3"><p class="text-xs text-gray-500">HZZ opens</p><p class="text-xl font-semibold">{{ $overview['external_opens'] ?? 0 }}</p></div>
            </div>
        </x-filament::section>

        <x-filament::section heading="Views by day">
            <div class="overflow-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="py-2 pr-3">Day</th>
                            <th class="py-2 pr-3">Views</th>
                            <th class="py-2 pr-3">Unique views</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($byDayRows as $row)
                            <tr class="border-b">
                                <td class="py-2 pr-3">{{ $row['day'] ?? '' }}</td>
                                <td class="py-2 pr-3">{{ $row['total_views'] ?? 0 }}</td>
                                <td class="py-2 pr-3">{{ $row['unique_views'] ?? 0 }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="py-4 text-gray-500">No data in selected range.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        <x-filament::section heading="Per-job analytics">
            <div class="overflow-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left border-b">
                            <th class="py-2 pr-3">Job</th>
                            <th class="py-2 pr-3">Views</th>
                            <th class="py-2 pr-3">Unique</th>
                            <th class="py-2 pr-3">CTA</th>
                            <th class="py-2 pr-3">CTR</th>
                            <th class="py-2 pr-3">CroWork applications</th>
                            <th class="py-2 pr-3">HZZ opens</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($perJobRows as $row)
                            <tr class="border-b align-top">
                                <td class="py-2 pr-3">
                                    <p class="font-medium">{{ $row['title'] }}</p>
                                    <p class="text-xs text-gray-500">#{{ $row['job_id'] }} · {{ $row['slug'] }}</p>
                                </td>
                                <td class="py-2 pr-3">{{ $row['views'] }}</td>
                                <td class="py-2 pr-3">{{ $row['unique_views'] }}</td>
                                <td class="py-2 pr-3">{{ $row['cta_clicks'] }}</td>
                                <td class="py-2 pr-3">{{ $row['ctr_percent'] }}%</td>
                                <td class="py-2 pr-3">{{ $row['applications_sent'] }}</td>
                                <td class="py-2 pr-3">{{ $row['external_opens'] }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="py-4 text-gray-500">No HZZ job data found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
