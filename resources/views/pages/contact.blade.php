<x-app-layout>
    <x-slot name="title">Contact Us</x-slot>
    <x-slot name="description">Get in touch with the CroWork team. We're here to help with any questions about jobs, education, or our platform.</x-slot>
    <x-slot name="canonical">{{ route('contact') }}</x-slot>

    <x-hero
        size="md"
        title="Contact Us"
        subtitle="We're here to help you succeed"
    />

    <div class="section-spacing bg-surface-2">
        <div class="container-base max-w-4xl">
            <div class="space-y-8">
                <x-surface variant="base" elevation="1" rounded="card" padding="8">
                    <h2 class="text-title-2 font-semibold text-text-primary mb-6">Get in Touch</h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="flex items-start space-x-3">
                            <x-icon-tile tone="primary" size="md" class="flex-shrink-0">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            </x-icon-tile>
                            <div>
                                <h3 class="text-subtitle font-semibold text-text-primary mb-1">Email Us</h3>
                                <p class="text-body-sm text-text-secondary mb-2">For general inquiries and support</p>
                                <a href="mailto:info@crowork.hr" class="text-body text-primary hover:text-primary-hover transition-colors duration-normal">info@crowork.hr</a>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <x-icon-tile tone="primary" size="md" class="flex-shrink-0">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                </svg>
                            </x-icon-tile>
                            <div>
                                <h3 class="text-subtitle font-semibold text-text-primary mb-1">Call Us</h3>
                                <p class="text-body-sm text-text-secondary mb-2">Monday to Friday, 9am - 5pm CET</p>
                                <a href="tel:+38512345678" class="text-body text-primary hover:text-primary-hover transition-colors duration-normal">+385 1 234 5678</a>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <x-icon-tile tone="primary" size="md" class="flex-shrink-0">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </x-icon-tile>
                            <div>
                                <h3 class="text-subtitle font-semibold text-text-primary mb-1">Visit Us</h3>
                                <p class="text-body-sm text-text-secondary mb-0">
                                    Ilica 242<br>
                                    10000 Zagreb<br>
                                    Croatia
                                </p>
                            </div>
                        </div>

                        <div class="flex items-start space-x-3">
                            <x-icon-tile tone="primary" size="md" class="flex-shrink-0">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path>
                                </svg>
                            </x-icon-tile>
                            <div>
                                <h3 class="text-subtitle font-semibold text-text-primary mb-1">Follow Us</h3>
                                <p class="text-body-sm text-text-secondary mb-2">Stay updated with our latest news</p>
                                <div class="flex flex-wrap items-center gap-2 text-body-sm">
                                    <a href="#" class="text-primary hover:text-primary-hover transition-colors duration-normal">LinkedIn</a>
                                    <span class="text-text-tertiary">•</span>
                                    <a href="#" class="text-primary hover:text-primary-hover transition-colors duration-normal">Facebook</a>
                                    <span class="text-text-tertiary">•</span>
                                    <a href="#" class="text-primary hover:text-primary-hover transition-colors duration-normal">Twitter</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-surface>

                <x-surface variant="base" elevation="1" rounded="card" padding="8">
                    <h2 class="text-title-2 font-semibold text-text-primary mb-6">Department Contacts</h2>

                    <div class="space-y-5">
                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 py-4 border-b border-border">
                            <div>
                                <h3 class="text-subtitle font-semibold text-text-primary mb-1">Job Seekers Support</h3>
                                <p class="text-body-sm text-text-secondary mb-0">Questions about applications, profiles, or job opportunities</p>
                            </div>
                            <a href="mailto:jobs@crowork.hr" class="text-body text-primary hover:text-primary-hover transition-colors duration-normal">jobs@crowork.hr</a>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 py-4 border-b border-border">
                            <div>
                                <h3 class="text-subtitle font-semibold text-text-primary mb-1">Employer Services</h3>
                                <p class="text-body-sm text-text-secondary mb-0">Help with posting jobs, managing applications, or account issues</p>
                            </div>
                            <a href="mailto:employers@crowork.hr" class="text-body text-primary hover:text-primary-hover transition-colors duration-normal">employers@crowork.hr</a>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 py-4 border-b border-border">
                            <div>
                                <h3 class="text-subtitle font-semibold text-text-primary mb-1">Technical Support</h3>
                                <p class="text-body-sm text-text-secondary mb-0">Website issues, bugs, or technical problems</p>
                            </div>
                            <a href="mailto:support@crowork.hr" class="text-body text-primary hover:text-primary-hover transition-colors duration-normal">support@crowork.hr</a>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 py-4">
                            <div>
                                <h3 class="text-subtitle font-semibold text-text-primary mb-1">Business Inquiries</h3>
                                <p class="text-body-sm text-text-secondary mb-0">Partnerships, media requests, or business development</p>
                            </div>
                            <a href="mailto:business@crowork.hr" class="text-body text-primary hover:text-primary-hover transition-colors duration-normal">business@crowork.hr</a>
                        </div>
                    </div>
                </x-surface>

                <section class="bg-warning-50 rounded-lg border border-warning-200 p-6">
                    <h3 class="text-subtitle font-semibold text-text-primary mb-2">Before You Contact Us</h3>
                    <p class="text-body-sm text-text-secondary mb-0">
                        Many common questions are answered in our FAQ section. Check there first for quick answers about account management, application processes, and platform features.
                    </p>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
