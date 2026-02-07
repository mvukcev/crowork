<div class="space-y-3">
    @foreach($snapshot as $key => $value)
        <div class="border-b pb-2">
            <dt class="font-semibold text-sm text-gray-700 dark:text-gray-300">
                {{ ucwords(str_replace('_', ' ', $key)) }}
            </dt>
            <dd class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                @if(is_array($value))
                    <ul class="list-disc list-inside">
                        @foreach($value as $item)
                            <li>{{ $item }}</li>
                        @endforeach
                    </ul>
                @else
                    {{ $value ?? 'N/A' }}
                @endif
            </dd>
        </div>
    @endforeach
</div>
