<x-app-layout>
    <x-slot name="title">About Us</x-slot>
    <x-slot name="description">Learn about CroWork's mission to connect international talent with Croatian employers and build a thriving community.</x-slot>
    <x-slot name="canonical">{{ route('about') }}</x-slot>

    <x-hero
        size="sm"
        title="About CroWork"
        subtitle="A premium platform built around trust, clarity, and opportunity for international talent in Croatia."
        theme="employers"
    />

    <div class="section-spacing-tight">
        <div class="container-base max-w-6xl space-y-6 md:space-y-8">
            <x-surface variant="base" elevation="1" rounded="3xl" class="premium-glass">
                <x-section-header
                    title="Our Mission"
                    subtitle="Bridge global talent and Croatian employers through a calmer, clearer, and more trusted hiring experience."
                    class="mb-5"
                />
                <p class="text-body-lg text-text-secondary mb-0 max-w-4xl">
                    CroWork helps international professionals discover meaningful opportunities in Croatia while giving employers confidence through verified profiles, structured applications, and high-trust platform standards.
                </p>
            </x-surface>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <x-surface variant="base" elevation="1" rounded="3xl" class="premium-glass">
                    <h2 class="text-title-1 font-semibold text-text-primary mb-4">What We Build</h2>
                    <ul class="space-y-3 text-body text-text-secondary mb-0">
                        <li>Verified jobs and employers for safer decision making.</li>
                        <li>Structured profiles and applications with less friction.</li>
                        <li>Education pathways to support long-term success in Croatia.</li>
                        <li>Guidance for relocation and practical onboarding.</li>
                    </ul>
                </x-surface>

                <x-surface variant="base" elevation="1" rounded="3xl" class="premium-glass">
                    <h2 class="text-title-1 font-semibold text-text-primary mb-4">Our Values</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="rounded-2xl bg-white/70 border border-white/80 p-4">
                            <h3 class="text-subtitle font-semibold text-text-primary mb-1">Transparency</h3>
                            <p class="text-body-sm text-text-secondary mb-0">Clear communication and honest expectations.</p>
                        </div>
                        <div class="rounded-2xl bg-white/70 border border-white/80 p-4">
                            <h3 class="text-subtitle font-semibold text-text-primary mb-1">Quality</h3>
                            <p class="text-body-sm text-text-secondary mb-0">Curated opportunities and verified participants.</p>
                        </div>
                        <div class="rounded-2xl bg-white/70 border border-white/80 p-4">
                            <h3 class="text-subtitle font-semibold text-text-primary mb-1">Diversity</h3>
                            <p class="text-body-sm text-text-secondary mb-0">A workforce enriched by global perspectives.</p>
                        </div>
                        <div class="rounded-2xl bg-white/70 border border-white/80 p-4">
                            <h3 class="text-subtitle font-semibold text-text-primary mb-1">Support</h3>
                            <p class="text-body-sm text-text-secondary mb-0">Hands-on help through every hiring step.</p>
                        </div>
                    </div>
                </x-surface>
            </div>

            <x-cta-panel title="Want to Learn More?" subtitle="Our team can walk you through CroWork and help you choose the right path.">
                <x-slot name="actions">
                    <x-button href="{{ route('contact') }}" variant="primary" size="lg">Contact Us</x-button>
                </x-slot>
            </x-cta-panel>
        </div>
    </div>
</x-app-layout>
