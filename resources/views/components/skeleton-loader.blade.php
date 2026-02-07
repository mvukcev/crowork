@props([
    'lines' => 3,
    'type' => 'card', // 'card', 'text', 'circle'
    'count' => 1,
])

@if($type === 'card')
    @for($i = 0; $i < $count; $i++)
        <div class="bg-surface rounded-lg border border-border p-6 mb-4 skeleton-shimmer">
            <div class="h-4 skeleton rounded mb-4 w-3/4"></div>
            <div class="h-3 skeleton rounded mb-3 w-full"></div>
            <div class="h-3 skeleton rounded mb-3 w-5/6"></div>
            <div class="h-3 skeleton rounded w-4/5"></div>
        </div>
    @endfor
@elseif($type === 'text')
    @for($i = 0; $i < $lines; $i++)
        <div class="h-4 skeleton rounded mb-3 w-full"></div>
    @endfor
@elseif($type === 'circle')
    <div class="w-12 h-12 skeleton rounded-full skeleton-shimmer"></div>
@else
    <div class="space-y-4">
        @for($i = 0; $i < $lines; $i++)
            <div class="h-4 skeleton rounded w-full skeleton-shimmer"></div>
        @endfor
    </div>
@endif
