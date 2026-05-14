<x-app-layout>
    <x-slot name="title">Coming Soon</x-slot>
    <x-slot name="description">This CroWork feature is not available yet.</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-3xl">
            <x-coming-soon-panel
                :title="$title"
                :description="$description"
            />
        </div>
    </section>
</x-app-layout>
