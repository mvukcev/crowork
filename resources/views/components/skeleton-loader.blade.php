@props([
    'lines' => 3,
    'type' => 'card',
    'count' => 1,
])

@if($type === 'card')
    @for($i = 0; $i < $count; $i++)
        <div class="cw-surface p-5 mb-4 animate-pulse">
            <div class="cw-skeleton cw-skeleton-heading mb-3 w-3/4"></div>
            <div class="cw-skeleton cw-skeleton-text mb-2 w-full"></div>
            <div class="cw-skeleton cw-skeleton-text mb-2 w-5/6"></div>
            <div class="cw-skeleton cw-skeleton-text w-4/5"></div>
        </div>
    @endfor
@elseif($type === 'text')
    @for($i = 0; $i < $lines; $i++)
        <div class="cw-skeleton cw-skeleton-text mb-3 animate-pulse"></div>
    @endfor
@elseif($type === 'avatar')
    <div class="cw-skeleton cw-skeleton-avatar animate-pulse"></div>
@elseif($type === 'circle')
    <div class="cw-skeleton cw-skeleton-avatar animate-pulse"></div>
@elseif($type === 'image')
    <div class="cw-skeleton cw-skeleton-card animate-pulse mb-4"></div>
@elseif($type === 'list')
    @for($i = 0; $i < $lines; $i++)
        <div class="flex items-center gap-3 mb-4">
            <div class="cw-skeleton cw-skeleton-avatar animate-pulse"></div>
            <div class="flex-1">
                <div class="cw-skeleton cw-skeleton-text mb-2 w-3/4"></div>
                <div class="cw-skeleton cw-skeleton-text w-2/3"></div>
            </div>
        </div>
    @endfor
@endif
