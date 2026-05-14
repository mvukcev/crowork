@props([
    'lines' => 3,
    'type' => 'card',
    'count' => 1,
])

@if($type === 'card')
    @for($i = 0; $i < $count; $i++)
        <div class="cw-surface p-5 mb-4 animate-pulse">
            <div class="h-4 rounded bg-slate-200 mb-3 w-3/4"></div>
            <div class="h-3 rounded bg-slate-200 mb-2 w-full"></div>
            <div class="h-3 rounded bg-slate-200 mb-2 w-5/6"></div>
            <div class="h-3 rounded bg-slate-200 w-4/5"></div>
        </div>
    @endfor
@elseif($type === 'text')
    @for($i = 0; $i < $lines; $i++)
        <div class="h-4 rounded bg-slate-200 mb-3 animate-pulse"></div>
    @endfor
@elseif($type === 'circle')
    <div class="w-12 h-12 rounded-full bg-slate-200 animate-pulse"></div>
@endif
