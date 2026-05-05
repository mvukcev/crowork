<x-app-layout>
    <x-slot name="title">Pricing - CroWork for Employers</x-slot>
    <x-slot name="description">Flexible pricing plans for Croatian employers. Choose the plan that fits your hiring needs.</x-slot>
    <x-slot name="canonical">{{ route('pricing') }}</x-slot>

    <!-- Hero Section -->
    <div class="bg-gradient-to-br from-primary-light via-background to-background section-spacing">
        <div class="container-base">
            <div class="max-w-4xl mx-auto text-center">
                <h1 class="text-display-lg font-semibold text-text-primary mb-6">
                    Simple, Transparent Pricing
                </h1>
                <p class="text-title-2 text-text-secondary mb-8 leading-relaxed">
                    Choose the plan that fits your hiring needs. All plans include verified candidates, 
                    application management, and compliance tools.
                </p>
            </div>
        </div>
    </div>

    <!-- Pricing Tiers -->
    <div class="section-spacing bg-background">
        <div class="container-base">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto">
                <!-- Starter Plan -->
                <div class="bg-surface rounded-lg border border-border p-8 hover:shadow-hover transition-shadow duration-normal">
                    <div class="text-center mb-6">
                        <h3 class="text-title-1 font-semibold text-text-primary mb-2">Starter</h3>
                        <p class="text-body-sm text-text-secondary mb-6">Perfect for small businesses with occasional hiring needs</p>
                        <div class="mb-4">
                            <span class="text-display-md font-bold text-primary">Coming Soon</span>
                        </div>
                        <p class="text-caption text-text-tertiary">Estimated: €99/job post</p>
                    </div>

                    <div class="border-t border-border pt-6 mb-6">
                        <p class="text-body-sm font-semibold text-text-primary mb-4">What's included:</p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-body-sm text-text-secondary">1 active job posting</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-body-sm text-text-secondary">30-day listing duration</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-body-sm text-text-secondary">Unlimited applications</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-body-sm text-text-secondary">Application dashboard</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-body-sm text-text-secondary">Email support</span>
                            </li>
                        </ul>
                    </div>

                    <x-button variant="secondary" class="w-full" disabled>
                        Coming Soon
                    </x-button>
                </div>

                <!-- Growth Plan (Featured) -->
                <div class="bg-primary-light rounded-lg border-2 border-primary p-8 relative hover:shadow-hover transition-shadow duration-normal">
                    <div class="absolute -top-4 left-1/2 transform -translate-x-1/2">
                        <span class="bg-primary text-white px-4 py-1 rounded-full text-caption font-semibold">MOST POPULAR</span>
                    </div>

                    <div class="text-center mb-6">
                        <h3 class="text-title-1 font-semibold text-text-primary mb-2">Growth</h3>
                        <p class="text-body-sm text-text-secondary mb-6">Best for growing companies with regular hiring</p>
                        <div class="mb-4">
                            <span class="text-display-md font-bold text-primary">Coming Soon</span>
                        </div>
                        <p class="text-caption text-text-tertiary">Estimated: €399/month</p>
                    </div>

                    <div class="border-t border-primary-border pt-6 mb-6">
                        <p class="text-body-sm font-semibold text-text-primary mb-4">Everything in Starter, plus:</p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-body-sm text-text-secondary">5 active job postings</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-body-sm text-text-secondary">60-day listing duration</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-body-sm text-text-secondary">Featured job highlights</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-body-sm text-text-secondary">Advanced analytics</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-body-sm text-text-secondary">Priority support</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-body-sm text-text-secondary">Company profile page</span>
                            </li>
                        </ul>
                    </div>

                    <x-button variant="primary" class="w-full" disabled>
                        Coming Soon
                    </x-button>
                </div>

                <!-- Enterprise Plan -->
                <div class="bg-surface rounded-lg border border-border p-8 hover:shadow-hover transition-shadow duration-normal">
                    <div class="text-center mb-6">
                        <h3 class="text-title-1 font-semibold text-text-primary mb-2">Enterprise</h3>
                        <p class="text-body-sm text-text-secondary mb-6">For large organizations with high-volume hiring</p>
                        <div class="mb-4">
                            <span class="text-display-md font-bold text-primary">Custom</span>
                        </div>
                        <p class="text-caption text-text-tertiary">Tailored to your needs</p>
                    </div>

                    <div class="border-t border-border pt-6 mb-6">
                        <p class="text-body-sm font-semibold text-text-primary mb-4">Everything in Growth, plus:</p>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-body-sm text-text-secondary">Unlimited job postings</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-body-sm text-text-secondary">Dedicated account manager</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-body-sm text-text-secondary">API access</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-body-sm text-text-secondary">Custom integrations</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-body-sm text-text-secondary">Multi-user accounts</span>
                            </li>
                            <li class="flex items-start gap-3">
                                <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                <span class="text-body-sm text-text-secondary">SLA guarantee</span>
                            </li>
                        </ul>
                    </div>

                    <x-button href="{{ url('/contact') }}" variant="secondary" class="w-full">
                        Contact Sales
                    </x-button>
                </div>
            </div>

            <!-- Pricing Note -->
            <div class="max-w-4xl mx-auto mt-12">
                <x-card class="bg-primary-light border border-primary-border text-center">
                    <div class="flex items-start justify-center gap-3 mb-4">
                        <svg class="w-6 h-6 text-primary flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="text-left">
                            <h3 class="text-subtitle font-semibold text-text-primary mb-2">Pricing Launching Soon</h3>
                            <p class="text-body-sm text-text-secondary leading-relaxed">
                                CroWork is currently in Phase 1 with free employer accounts while we build out features 
                                and gather feedback. Paid plans will launch in Phase 2 (estimated Q2 2026). 
                                Early adopters will receive special discounts.
                            </p>
                            <p class="text-body-sm text-text-secondary mt-3">
                                <strong>Want early access?</strong> Contact us to discuss your hiring needs and get notified 
                                when pricing launches.
                            </p>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    </div>

    <!-- FAQ Section -->
    <div class="section-spacing bg-surface">
        <div class="container-base">
            <div class="max-w-4xl mx-auto">
                <h2 class="text-title-1 font-semibold text-text-primary mb-8 text-center">Frequently Asked Questions</h2>
                
                <div class="space-y-6">
                    <!-- FAQ 1 -->
                    <div class="bg-background rounded-lg border border-border p-6">
                        <h3 class="text-subtitle font-semibold text-text-primary mb-2">
                            Is there a free trial?
                        </h3>
                        <p class="text-body-sm text-text-secondary">
                            Currently, all employer accounts are free during our Phase 1 launch period. You can post jobs, 
                            receive applications, and use all features at no cost while we build out the platform.
                        </p>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="bg-background rounded-lg border border-border p-6">
                        <h3 class="text-subtitle font-semibold text-text-primary mb-2">
                            What payment methods do you accept?
                        </h3>
                        <p class="text-body-sm text-text-secondary">
                            When pricing launches, we'll accept major credit cards (Visa, Mastercard, American Express), 
                            bank transfers, and Croatian business accounts. Enterprise customers can arrange invoice billing.
                        </p>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="bg-background rounded-lg border border-border p-6">
                        <h3 class="text-subtitle font-semibold text-text-primary mb-2">
                            Can I cancel or change my plan?
                        </h3>
                        <p class="text-body-sm text-text-secondary">
                            Yes, you'll be able to upgrade, downgrade, or cancel at any time. Active job postings will 
                            remain live until their expiration date even if you cancel your subscription.
                        </p>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="bg-background rounded-lg border border-border p-6">
                        <h3 class="text-subtitle font-semibold text-text-primary mb-2">
                            Do you charge per application received?
                        </h3>
                        <p class="text-body-sm text-text-secondary">
                            No. All plans include unlimited applications. You only pay for job postings or your monthly 
                            subscription, regardless of how many candidates apply.
                        </p>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="bg-background rounded-lg border border-border p-6">
                        <h3 class="text-subtitle font-semibold text-text-primary mb-2">
                            What happens to my data if I cancel?
                        </h3>
                        <p class="text-body-sm text-text-secondary">
                            Your job postings will be unpublished and your account will be deactivated. Application data 
                            is retained for 90 days in case you reactivate, then permanently deleted per GDPR requirements.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="section-spacing bg-gradient-to-br from-primary-light to-primary">
        <div class="container-base">
            <div class="max-w-3xl mx-auto text-center">
                <h2 class="text-display-md font-semibold text-text-primary mb-4">
                    Ready to Start Hiring?
                </h2>
                <p class="text-title-2 text-text-secondary mb-8">
                    Create your free employer account today
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <x-button href="{{ url('/employer/register') }}" variant="primary" size="lg" class="bg-white text-primary hover:bg-gray-50 border-2 border-white">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Create Free Account
                    </x-button>
                    <x-button href="{{ url('/contact') }}" variant="secondary" size="lg" class="bg-transparent text-text-primary border-2 border-text-primary hover:bg-white/10">
                        Contact Sales Team
                    </x-button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
