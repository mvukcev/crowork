@props([
    'show' => false,
    'fullWidth' => false,
])

@if($show || $attributes->has('show'))
    <div class="fixed {{ $fullWidth ? 'left-0 right-0' : 'left-4 right-4' }} top-0 z-50 h-1 bg-gradient-to-r from-primary via-accent to-primary progress-bar-loading" style="animation: progressShimmer 1.5s infinite;"></div>
@endif
