@props([
    'title' => 'No results found',
    'description' => 'Try adjusting your filters or search criteria.',
    'icon' => 'search',
    'actionHref' => null,
    'actionLabel' => 'Go back',
])

<div class="cw-empty-state">
    @if($icon === 'search')
        <svg class="cw-empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="11" cy="11" r="8"></circle>
            <path d="m21 21-4.35-4.35"></path>
        </svg>
    @elseif($icon === 'inbox')
        <svg class="cw-empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polyline points="22 12 18 12 15 21 9 21 6 12 2 12"></polyline>
        </svg>
    @elseif($icon === 'file')
        <svg class="cw-empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"></path>
            <polyline points="13 2 13 9 20 9"></polyline>
        </svg>
    @elseif($icon === 'calendar')
        <svg class="cw-empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
            <line x1="16" y1="2" x2="16" y2="6"></line>
            <line x1="8" y1="2" x2="8" y2="6"></line>
            <line x1="3" y1="10" x2="21" y2="10"></line>
        </svg>
    @elseif($icon === 'star')
        <svg class="cw-empty-state-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <polygon points="12 2 15.09 10.26 23.77 11.36 17.88 17.15 19.54 25.88 12 21.77 4.46 25.88 6.12 17.15 0.23 11.36 8.91 10.26 12 2"></polygon>
        </svg>
    @endif

    <h3 class="cw-empty-state-title">{{ $title }}</h3>
    <p class="cw-empty-state-description">{{ $description }}</p>

    @if($actionHref)
        <a href="{{ $actionHref }}" class="cw-button-secondary" aria-label="{{ $actionLabel }}">
            {{ $actionLabel }}
        </a>
    @endif
</div>
