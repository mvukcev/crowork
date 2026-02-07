<x-app-layout>
    <x-slot name="title">About Us</x-slot>
    <x-slot name="description">Learn about CroWork's mission to connect international talent with Croatian employers and build a thriving community.</x-slot>

    <!-- Hero Section with Red Theme -->
    <x-hero 
        size="md" 
        title="About CroWork" 
        subtitle="Connecting international talent with Croatian opportunities"
        theme="employers"
    />

    <div class="section-spacing">
        <div class="container-base max-w-4xl">
            <!-- Content Sections -->
            <div class="prose prose-neutral max-w-none space-y-6">
                <!-- Our Mission -->
                <x-surface variant="base" elevation="1" padding="8">
                    <h2 class="text-title-2 font-semibold text-text-primary mb-4">Our Mission</h2>
                    <p class="text-body text-text-secondary leading-relaxed">
                        CroWork is dedicated to bridging the gap between talented international professionals and innovative Croatian employers. We believe in creating meaningful connections that drive economic growth and cultural exchange throughout Croatia.
                    </p>
                </x-surface>

                <!-- What We Do -->
                <x-surface variant="base" elevation="1" padding="8">
                    <h2 class="text-title-2 font-semibold text-text-primary mb-4">What We Do</h2>
                    <div class="space-y-4">
                        <p class="text-body text-text-secondary leading-relaxed">
                            We provide a comprehensive platform that simplifies the job search and hiring process for both workers and employers:
                        </p>
                        <ul class="list-disc list-inside space-y-2 text-body text-text-secondary">
                            <li>Connect skilled international workers with Croatian companies</li>
                            <li>Offer education and training program opportunities</li>
                            <li>Streamline the application and hiring process</li>
                            <li>Provide resources and support for relocation and integration</li>
                            <li>Foster a diverse and inclusive workforce in Croatia</li>
                        </ul>
                    </div>
                </x-surface>

                <!-- Our Values -->
                <x-surface variant="base" elevation="1" padding="8">
                    <h2 class="text-title-2 font-semibold text-text-primary mb-4">Our Values</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-subtitle font-semibold text-text-primary mb-2">Transparency</h3>
                            <p class="text-body-sm text-text-secondary">
                                Clear communication and honest relationships between all parties.
                            </p>
                        </div>
                        <div>
                            <h3 class="text-subtitle font-semibold text-text-primary mb-2">Quality</h3>
                            <p class="text-body-sm text-text-secondary">
                                Verified employers and curated opportunities for the best matches.
                            </p>
                        </div>
                        <div>
                            <h3 class="text-subtitle font-semibold text-text-primary mb-2">Diversity</h3>
                            <p class="text-body-sm text-text-secondary">
                                Celebrating different cultures and backgrounds in the Croatian workforce.
                            </p>
                        </div>
                        <div>
                            <h3 class="text-subtitle font-semibold text-text-primary mb-2">Support</h3>
                            <p class="text-body-sm text-text-secondary">
                                Dedicated assistance throughout the entire employment journey.
                            </p>
                        </div>
                    </div>
                </x-surface>

                <!-- Get in Touch -->
                <x-cta-panel title="Want to Learn More?" subtitle="We'd love to hear from you. Get in touch with our team to learn more about how CroWork can help you.">
                    <x-slot name="actions">
                        <x-button href="{{ route('contact') }}" variant="primary">
                            Contact Us
                        </x-button>
                    </x-slot>
                </x-cta-panel>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
