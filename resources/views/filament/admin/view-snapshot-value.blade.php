@php
    $value = $value ?? null;
@endphp

@if(is_array($value))
    @if($value === [])
        <span class="text-gray-500 dark:text-gray-400">N/A</span>
    @else
        <ul class="list-disc list-inside space-y-1">
            @foreach($value as $itemKey => $itemValue)
                <li>
                    @if(is_array($itemValue))
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ is_string($itemKey) ? $itemKey : '#' . ($loop->index + 1) }}</span>
                        <div class="ml-4 mt-1">
                            @include('filament.admin.view-snapshot-value', ['value' => $itemValue])
                        </div>
                    @elseif(is_object($itemValue))
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ is_string($itemKey) ? $itemKey : '#' . ($loop->index + 1) }}</span>
                        <pre class="mt-1 whitespace-pre-wrap break-words rounded bg-gray-50 px-2 py-1 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-200">{{ json_encode($itemValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    @else
                        <span class="font-medium text-gray-700 dark:text-gray-300">{{ is_string($itemKey) ? $itemKey . ':' : '' }}</span>
                        <span>{{ $itemValue ?? 'N/A' }}</span>
                    @endif
                </li>
            @endforeach
        </ul>
    @endif
@elseif(is_object($value))
    <pre class="whitespace-pre-wrap break-words rounded bg-gray-50 px-2 py-1 text-xs text-gray-700 dark:bg-gray-800 dark:text-gray-200">{{ json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
@else
    {{ $value ?? 'N/A' }}
@endif
