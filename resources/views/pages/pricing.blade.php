<x-app-layout>
    <x-slot name="title">Pricing - CroWork for Employers</x-slot>
    <x-slot name="description">Flexible pricing plans for Croatian employers hiring international workers.</x-slot>
    <x-slot name="canonical">{{ route('pricing') }}</x-slot>

    <x-hero
        size="sm"
        title="Simple, Transparent Pricing"
        subtitle="Choose a plan that matches your hiring pace. Every plan includes verified candidates, structured applications, and platform trust features."
        theme="jobs"
    />

    <section class="section-spacing-tight">
        <div class="container-base max-w-6xl">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-surface variant="base" elevation="1" rounded="3xl" class="premium-glass">
                    <p class="text-caption uppercase tracking-[0.1em] text-text-tertiary mb-2">Starter</p>
                    <h3 class="text-title-1 font-semibold text-text-primary mb-2">Occasional hiring</h3>
                    <p class="text-body-sm text-text-secondary mb-5">Great for teams filling roles periodically.</p>
                    <p class="text-display-sm font-semibold text-text-primary mb-1">Coming Soon</p>
                    <p class="text-caption text-text-tertiary mb-6">Estimated €99 per post</p>
                    <ul class="space-y-2.5 text-body-sm text-text-secondary mb-6">
                        <li>1 active job listing</li>
                        <li>30-day listing duration</li>
                        <li>Unlimited applications</li>
                        <li>Application dashboard</li>
                    </ul>
                    <x-button variant="secondary" class="w-full" disabled>Coming Soon</x-button>
                </x-surface>

                <x-surface variant="base" elevation="2" rounded="3xl" class="premium-glass border-primary/40 relative">
                    <span class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 rounded-full bg-primary text-white text-caption font-semibold">Most Popular</span>
                    <p class="text-caption uppercase tracking-[0.1em] text-text-tertiary mb-2">Growth</p>
                    <h3 class="text-title-1 font-semibold text-text-primary mb-2">Active hiring teams</h3>
                    <p class="text-body-sm text-text-secondary mb-5">Ideal for employers hiring every month.</p>
                    <p class="text-display-sm font-semibold text-primary mb-1">Coming Soon</p>
                    <p class="text-caption text-text-tertiary mb-6">Estimated €399/month</p>
                    <ul class="space-y-2.5 text-body-sm text-text-secondary mb-6">
                        <li>Everything in Starter</li>
                        <li>Featured job placement</li>
                        <li>Extended listing duration</li>
                        <li>Priority support</li>
                    </ul>
                    <x-button variant="primary" class="w-full" disabled>Coming Soon</x-button>
                </x-surface>

                <x-surface variant="base" elevation="1" rounded="3xl" class="premium-glass">
                    <p class="text-caption uppercase tracking-[0.1em] text-text-tertiary mb-2">Enterprise</p>
                    <h3 class="text-title-1 font-semibold text-text-primary mb-2">High-volume hiring</h3>
                    <p class="text-body-sm text-text-secondary mb-5">Custom support for larger organizations.</p>
                    <p class="text-display-sm font-semibold text-text-primary mb-1">Custom</p>
                    <p class="text-caption text-text-tertiary mb-6">Tailored enterprise terms</p>
                    <ul class="space-y-2.5 text-body-sm text-text-secondary mb-6">
                        <li>Unlimited listings</li>
                        <li>Dedicated account manager</li>
                        <li>Custom integrations</li>
                        <li>Multi-user organization controls</li>
                    </ul>
                    <x-button href="{{ url('/contact') }}" variant="outline" class="w-full">Contact Sales</x-button>
                </x-surface>
            </div>

            <x-surface variant="base" elevation="1" rounded="3xl" class="premium-glass mt-8">
                <h2 class="text-title-1 font-semibold text-text-primary mb-3">Pricing Launch Timeline</h2>
                <p class="text-body text-text-secondary mb-0">
                    CroWork is currently in Phase 1 with free employer access while we continue improving platform workflows. Paid plans are planned for Phase 2. Early adopters will receive launch pricing benefits.
                </p>
            </x-surface>
        </div>
    </section>

    <section class="section-spacing-tight pt-0">
        <div class="container-base max-w-4xl">
            <x-section-header
                title="Frequently Asked Questions"
                subtitle="Answers to the most common questions about pricing and billing"
                centered
            />

            <div class="space-y-4">
                <x-surface variant="base" elevation="1" rounded="2xl" class="premium-glass">
                    <h3 class="text-subtitle font-semibold text-text-primary mb-2">Is there a free trial?</h3>
                    <p class="text-body-sm text-text-secondary mb-0">Phase 1 is currently free for verified employers while we finalize paid plans.</p>
                </x-surface>
                <x-surface variant="base" elevation="1" rounded="2xl" class="premium-glass">
                    <h3 class="text-subtitle font-semibold text-text-primary mb-2">Can I change plans later?</h3>
                    <p class="text-body-sm text-text-secondary mb-0">Yes. Plan upgrades and downgrades will be available directly from your employer dashboard.</p>
                </x-surface>
                <x-surface variant="base" elevation="1" rounded="2xl" class="premium-glass">
                    <h3 class="text-subtitle font-semibold text-text-primary mb-2">Do you charge per application?</h3>
                    <p class="text-body-sm text-text-secondary mb-0">No. Pricing is based on posting/subscription model, not on application volume.</p>
                </x-surface>
            </div>
        </div>
    </section>

    <section class="section-spacing-tight pt-0">
        <div class="container-base">
            <x-cta-panel
                title="Ready to Start Hiring?"
                subtitle="Create your employer account today and get notified when pricing launches."
                centered
            >
                <x-slot name="actions">
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <x-button href="{{ url('/employer/register') }}" variant="primary" size="lg">Create Free Account</x-button>
                        <x-button href="{{ url('/contact') }}" variant="outline" size="lg">Contact Sales Team</x-button>
                    </div>
                </x-slot>
            </x-cta-panel>
        </div>
    </section>
</x-app-layout>
