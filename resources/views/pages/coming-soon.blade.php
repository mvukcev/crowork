<x-app-layout>
    <x-slot name="title">Coming Soon</x-slot>
    <x-slot name="description">This CroWork feature is not available yet.</x-slot>

    <div class="section-spacing-tight bg-background min-h-screen">
        <div class="container-base max-w-3xl">
            <x-coming-soon-panel
                :title="$title"
                :description="$description"
            />
        </div>
    </div>
</x-app-layout>
