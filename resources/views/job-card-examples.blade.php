<x-app-layout>
    <x-slot name="title">Job Card Examples</x-slot>
    <section class="cw-section">
        <div class="cw-container max-w-4xl">
            <h1 class="cw-display text-4xl md:text-6xl mb-6">Job Card Examples</h1>
            <div class="grid gap-4">
                <x-job-card title="Senior Waiter" company="Adriatic Hotels" city="Dubrovnik" :salary_min="1900" :salary_max="2300" :languages="['English', 'Croatian']" :accommodation_provided="true" />
                <x-job-card title="Front Desk Coordinator" company="Portline Group" city="Split" :salary_min="1700" :salary_max="2100" :languages="['English']" />
            </div>
        </div>
    </section>
</x-app-layout>
