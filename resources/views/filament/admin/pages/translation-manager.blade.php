<x-filament-panels::page>
    {{-- Group tabs --}}
    <div class="mb-6 flex flex-wrap gap-2">
        @foreach($this->getAvailableGroups() as $group)
            <button
                type="button"
                wire:click="selectGroup('{{ $group }}')"
                @class([
                    'px-4 py-2 rounded-lg text-sm font-medium transition-colors',
                    'bg-primary-600 text-white shadow' => $activeGroup === $group,
                    'bg-white text-gray-700 border border-gray-200 hover:bg-gray-50' => $activeGroup !== $group,
                ])
            >
                {{ ucfirst($group) }}
            </button>
        @endforeach
    </div>

    {{-- Translation table --}}
    <div class="bg-white rounded-xl border border-gray-200 shadow-sm overflow-hidden">
        <div class="grid grid-cols-[200px_1fr_1fr] bg-gray-50 border-b border-gray-200 text-xs font-semibold uppercase tracking-wide text-gray-500">
            <div class="px-4 py-3">Key</div>
            <div class="px-4 py-3 border-l border-gray-200">English (source)</div>
            <div class="px-4 py-3 border-l border-gray-200">
                Croatian ({{ strtoupper($targetLocale) }})
                <span class="ml-2 text-[10px] font-normal normal-case text-gray-400">leave blank to use lang file value</span>
            </div>
        </div>

        @php $rows = $this->getGroupRows(); @endphp

        @forelse($rows as $index => $row)
            <div @class([
                'grid grid-cols-[200px_1fr_1fr] border-b border-gray-100 last:border-b-0 items-start',
                'bg-amber-50/50' => $row['has_override'],
            ])>
                {{-- Key --}}
                <div class="px-4 py-3 text-xs font-mono text-gray-500 self-center">
                    {{ $row['key'] }}
                    @if($row['has_override'])
                        <span class="ml-1 inline-block w-1.5 h-1.5 rounded-full bg-amber-400" title="Has override"></span>
                    @endif
                </div>

                {{-- EN source --}}
                <div class="px-4 py-3 border-l border-gray-100 text-sm text-gray-700 self-center">
                    {{ $row['en'] }}
                </div>

                {{-- HR override --}}
                <div class="px-4 py-2.5 border-l border-gray-100">
                    <textarea
                        wire:model.defer="overrides.{{ $row['key'] }}"
                        rows="1"
                        placeholder="{{ $row['current'] ?: 'Enter translation...' }}"
                        class="w-full rounded-lg border border-gray-200 bg-transparent px-3 py-1.5 text-sm text-gray-900 placeholder-gray-400 focus:border-primary-400 focus:ring-1 focus:ring-primary-400 resize-none"
                        oninput="this.style.height='auto';this.style.height=(this.scrollHeight)+'px'"
                    >{{ $this->overrides[$row['key']] ?? '' }}</textarea>
                </div>
            </div>
        @empty
            <div class="px-4 py-8 text-center text-sm text-gray-400">
                No translations found for group "{{ $activeGroup }}".
            </div>
        @endforelse
    </div>

    {{-- Legend --}}
    <div class="mt-4 flex items-center gap-4 text-xs text-gray-500">
        <span class="flex items-center gap-1.5">
            <span class="inline-block w-2 h-2 rounded-full bg-amber-400"></span>
            Has database override
        </span>
        <span>
            Rows without override use the value from <code class="bg-gray-100 px-1 rounded">lang/hr/{{ $activeGroup }}.php</code>
        </span>
    </div>
</x-filament-panels::page>
