<x-filament-panels::page>
    <div class="space-y-6">
        <x-filament::section heading="Quality overview">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="rounded-xl border p-3">
                    <p class="text-xs text-gray-500">Missing employer email</p>
                    <p class="text-2xl font-semibold text-amber-700">{{ $missingEmailCount }}</p>
                </div>
                <div class="rounded-xl border p-3">
                    <p class="text-xs text-gray-500">Missing source URL</p>
                    <p class="text-2xl font-semibold text-rose-700">{{ $missingSourceUrlCount }}</p>
                </div>
                <div class="rounded-xl border p-3">
                    <p class="text-xs text-gray-500">External-only fallback</p>
                    <p class="text-2xl font-semibold text-slate-900">{{ $externalOnlyCount }}</p>
                </div>
            </div>
        </x-filament::section>

        <x-filament::section heading="Latest HZZ records">
            <div class="overflow-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b text-left">
                            <th class="py-2 pr-3">Job</th>
                            <th class="py-2 pr-3">Published</th>
                            <th class="py-2 pr-3">Contact type</th>
                            <th class="py-2 pr-3">Employer email</th>
                            <th class="py-2 pr-3">HZZ URL</th>
                            <th class="py-2 pr-3">Source URL</th>
                            <th class="py-2 pr-3">CroWork apply</th>
                            <th class="py-2 pr-3"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rows as $row)
                            <tr class="border-b align-top">
                                <td class="py-2 pr-3">
                                    <p class="font-medium">{{ $row['title'] }}</p>
                                    <p class="text-xs text-gray-500">#{{ $row['id'] }}</p>
                                </td>
                                <td class="py-2 pr-3">{{ $row['published_at'] ?? '-' }}</td>
                                <td class="py-2 pr-3">{{ $row['contact_type'] ?: '-' }}</td>
                                <td class="py-2 pr-3">{{ $row['hzz_apply_email'] ?: '-' }}</td>
                                <td class="py-2 pr-3 break-all">{{ $row['hzz_apply_url'] ?: '-' }}</td>
                                <td class="py-2 pr-3 break-all">{{ $row['source_url'] ?: '-' }}</td>
                                <td class="py-2 pr-3">
                                    @if($row['can_apply_via_crowork'])
                                        <span class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-xs text-emerald-700">Yes</span>
                                    @else
                                        <span class="inline-flex rounded-full border border-amber-200 bg-amber-50 px-2 py-0.5 text-xs text-amber-700">No</span>
                                    @endif
                                </td>
                                <td class="py-2 pr-3">
                                    <x-filament::button tag="a" size="xs" :href="$row['edit_url']">Edit</x-filament::button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="py-4 text-gray-500">No HZZ records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
