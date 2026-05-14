@props([
    'type' => 'info', // 'success', 'error', 'warning', 'info'
    'message' => '',
    'dismissible' => true,
    'title' => null,
])

<div class="cw-alert cw-alert-{{ $type }}" role="alert" x-data="{ show: true }" x-show="show" x-transition>
    <div class="flex-1">
        @if($title)
            <strong class="block">{{ $title }}</strong>
        @endif
        {{ $message ?? $slot }}
    </div>
    
    @if($dismissible)
        <button 
            @click="show = false" 
            class="cw-alert-close"
            aria-label="Close alert"
            type="button"
        >
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
                <path d="m2 2 12 12M14 2 2 14" />
            </svg>
        </button>
    @endif
</div>
