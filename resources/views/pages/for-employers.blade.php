<x-app-layout>
    <x-slot name="title">For Employers - Hire International Talent</x-slot>
    <x-slot name="description">Find qualified international workers for your Croatian business. Streamlined hiring process with verified candidates ready to work.</x-slot>

    <!-- Hero Section with Red Theme -->
    <x-hero 
        size="lg" 
        title="Hire International Talent for Your Croatian Business" 
        subtitle="Access a pool of qualified, verified international workers ready to contribute to Croatia's workforce. Streamlined hiring with standardized applications and built-in compliance tools."
        theme="employers">
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <x-button href="{{ url('/employer/register') }}" variant="primary" size="lg" class="text-base">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                </svg>
                Create Employer Account
            </x-button>
            <x-button href="{{ url('/contact') }}" variant="outline" size="lg" class="text-base">
                Contact Sales
            </x-button>
        </div>
    </x-hero>

    <!-- Value Proposition -->
    <div class="section-spacing bg-background">
        <div class="container-base">
            <div class="text-center mb-12">
                <h2 class="text-title-1 font-semibold text-text-primary mb-4">Why Choose CroWork?</h2>
                <p class="text-body text-text-secondary max-w-2xl mx-auto">
                    Built specifically for Croatian employers hiring international talent
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-6xl mx-auto">
                <!-- Verified Candidates -->
                <x-surface variant="base" elevation="1" class="hover:shadow-elevation-2 transition-all duration-200">
                    <x-icon-tile tone="primary" size="md" class="mb-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </x-icon-tile>
                    <h3 class="text-subtitle font-semibold text-text-primary mb-3">Verified Candidates</h3>
                    <p class="text-body-sm text-text-secondary leading-relaxed">
                        All worker profiles are verified with document checks. Access standardized CVs with work permits, 
                        language skills, and qualifications clearly displayed.
                    </p>
                </x-surface>

                <!-- Streamlined Process -->
                <x-surface variant="base" elevation="1" class="hover:shadow-elevation-2 transition-all duration-200">
                    <x-icon-tile tone="secondary" size="md" class="mb-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </x-icon-tile>
                    <h3 class="text-subtitle font-semibold text-text-primary mb-3">Streamlined Hiring</h3>
                    <p class="text-body-sm text-text-secondary leading-relaxed">
                        Post jobs in minutes, receive structured applications, and manage candidates from one dashboard. 
                        No back-and-forth emails or scattered spreadsheets.
                    </p>
                </x-surface>

                <!-- Compliance Built-In -->
                <x-surface variant="base" elevation="1" class="hover:shadow-elevation-2 transition-all duration-200">
                    <x-icon-tile tone="accent" size="md" class="mb-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </x-icon-tile>
                    <h3 class="text-subtitle font-semibold text-text-primary mb-3">Compliance Tools</h3>
                    <p class="text-body-sm text-text-secondary leading-relaxed">
                        Built-in guidance for work permits, labor law compliance, and documentation requirements. 
                        Stay on the right side of Croatian employment regulations.
                    </p>
                </x-surface>
            </div>
        </div>
    </div>

    <!-- How It Works -->
    <div class="section-spacing bg-surface-tinted">
        <div class="container-base">
            <x-section 
                title="How It Works" 
                subtitle="From registration to hiring in four simple steps"
                centered
            />

            <div class="max-w-4xl mx-auto mt-12">
                <!-- Step Rail Container -->
                <div class="relative">
                    <!-- Vertical Rail Line -->
                    <div class="absolute left-6 top-8 bottom-8 w-px bg-border-subtle"></div>
                    
                    <div class="space-y-6">
                        <!-- Step 1 -->
                        <div class="flex gap-6 relative">
                            <div class="flex-shrink-0 relative z-10">
                                <div class="w-12 h-12 rounded-2xl bg-surface-base border border-border-border flex items-center justify-center shadow-elevation-1">
                                    <span class="text-body font-bold text-primary">1</span>
                                </div>
                            </div>
                            <x-surface variant="base" elevation="1" padding="5" class="flex-1">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-primary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                                    </svg>
                                    <div>
                                        <h3 class="text-subtitle font-semibold text-text-primary mb-2">Create Your Employer Account</h3>
                                        <p class="text-body-sm text-text-secondary">
                                            Sign up with your company details. Provide business registration information and wait for approval 
                                            (typically 24-48 hours). We verify all employers to maintain platform trust.
                                        </p>
                                    </div>
                                </div>
                            </x-surface>
                        </div>

                        <!-- Step 2 -->
                        <div class="flex gap-6 relative">
                            <div class="flex-shrink-0 relative z-10">
                                <div class="w-12 h-12 rounded-2xl bg-surface-base border border-border-border flex items-center justify-center shadow-elevation-1">
                                    <span class="text-body font-bold text-primary">2</span>
                                </div>
                            </div>
                            <x-surface variant="base" elevation="1" padding="5" class="flex-1">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-secondary flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    <div>
                                        <h3 class="text-subtitle font-semibold text-text-primary mb-2">Post Your Job Listings</h3>
                                        <p class="text-body-sm text-text-secondary">
                                            Create detailed job postings with salary ranges, requirements, and benefits. Specify language needs, 
                                            work permits, and whether accommodation is provided. Jobs are reviewed before going live.
                                        </p>
                                    </div>
                                </div>
                            </x-surface>
                        </div>

                        <!-- Step 3 -->
                        <div class="flex gap-6 relative">
                            <div class="flex-shrink-0 relative z-10">
                                <div class="w-12 h-12 rounded-2xl bg-surface-base border border-border-border flex items-center justify-center shadow-elevation-1">
                                    <span class="text-body font-bold text-primary">3</span>
                                </div>
                            </div>
                            <x-surface variant="base" elevation="1" padding="5" class="flex-1">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-accent flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                    <div>
                                        <h3 class="text-subtitle font-semibold text-text-primary mb-2">Review Applications</h3>
                                        <p class="text-body-sm text-text-secondary">
                                            Receive structured applications with standardized CVs. Filter by qualifications, language skills, 
                                            and work permit status. Contact candidates directly through the platform.
                                        </p>
                                    </div>
                                </div>
                            </x-surface>
                        </div>

                        <!-- Step 4 -->
                        <div class="flex gap-6 relative">
                            <div class="flex-shrink-0 relative z-10">
                                <div class="w-12 h-12 rounded-2xl bg-surface-base border border-border-border flex items-center justify-center shadow-elevation-1">
                                    <span class="text-body font-bold text-primary">4</span>
                                </div>
                            </div>
                            <x-surface variant="base" elevation="1" padding="5" class="flex-1">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-success flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div>
                                        <h3 class="text-subtitle font-semibold text-text-primary mb-2">Hire with Confidence</h3>
                                        <p class="text-body-sm text-text-secondary">
                                            Make your hiring decision with access to verified credentials and work history. 
                                            Use our resources to navigate employment contracts and work permit processes.
                                        </p>
                                    </div>
                                </div>
                            </x-surface>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Trust & Safety -->
    <div class="section-spacing bg-background">
        <div class="container-base">
            <div class="max-w-4xl mx-auto">
                <x-surface variant="base" elevation="2" class="border-success/20 bg-gradient-to-br from-success-50 to-surface-base">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0">
                            <x-icon-tile tone="success" size="md">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </x-icon-tile>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-title-2 font-semibold text-success-text mb-3">Trust & Safety First</h3>
                            <div class="text-body-sm text-success-text space-y-2">
                                <p>
                                    <strong>All employers are verified</strong> before posting jobs. We check business registration, 
                                    tax ID, and company legitimacy to protect workers from scams.
                                </p>
                                <p>
                                    <strong>Abuse reporting</strong> is built into every job and profile. Suspicious activity is 
                                    reviewed by our moderation team, and problematic accounts are removed.
                                </p>
                                <p>
                                    <strong>Your data is protected.</strong> We comply with GDPR and Croatian data protection laws. 
                                    Candidate information is only shared with approved employers.
                                </p>
                            </div>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    </div>

    <!-- Pricing Teaser -->
    <div class="section-spacing bg-surface">
        <div class="container-base">
            <div class="text-center mb-12">
                <h2 class="text-title-1 font-semibold text-text-primary mb-4">Transparent Pricing (Coming Soon)</h2>
                <p class="text-body text-text-secondary max-w-2xl mx-auto">We're finalizing flexible plans for businesses of all sizes. Phase 2 launch will include pay-per-job and subscription options.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-5xl mx-auto mb-8">
                <!-- Starter Tier -->
                <div class="bg-background rounded-lg border border-border p-8 hover:shadow-hover transition-shadow duration-normal">
                    <h3 class="text-subtitle font-semibold text-text-primary mb-2">Starter</h3>
                    <p class="text-body-xs text-text-tertiary mb-6">Perfect for occasional hiring</p>
                    <div class="mb-6">
                        <span class="text-display-sm font-bold text-text-primary">€99</span>
                        <span class="text-body-sm text-text-secondary">/job post</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start text-body-sm text-text-secondary">
                            <svg class="w-5 h-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>Post 1 job listing</span>
                        </li>
                        <li class="flex items-start text-body-sm text-text-secondary">
                            <svg class="w-5 h-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>View applications</span>
                        </li>
                        <li class="flex items-start text-body-sm text-text-secondary">
                            <svg class="w-5 h-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>30-day listing</span>
                        </li>
                    </ul>
                    <x-button variant="secondary" class="w-full" disabled>
                        Coming Soon
                    </x-button>
                </div>

                <!-- Growth Tier (Featured) -->
                <div class="bg-primary-light rounded-lg border-2 border-primary-border p-8 relative hover:shadow-hover transition-shadow duration-normal">
                    <div class="absolute -top-4 left-1/2 -translate-x-1/2 bg-primary text-white text-body-xs font-bold px-4 py-1 rounded-full">
                        MOST POPULAR
                    </div>
                    <h3 class="text-subtitle font-semibold text-text-primary mb-2">Growth</h3>
                    <p class="text-body-xs text-text-tertiary mb-6">For active hiring</p>
                    <div class="mb-6">
                        <span class="text-display-sm font-bold text-text-primary">€399</span>
                        <span class="text-body-sm text-text-secondary">/month</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start text-body-sm text-text-secondary">
                            <svg class="w-5 h-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>Unlimited job posts</span>
                        </li>
                        <li class="flex items-start text-body-sm text-text-secondary">
                            <svg class="w-5 h-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>Featured placement</span>
                        </li>
                        <li class="flex items-start text-body-sm text-text-secondary">
                            <svg class="w-5 h-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>Priority support</span>
                        </li>
                    </ul>
                    <x-button variant="primary" class="w-full" disabled>
                        Coming Soon
                    </x-button>
                </div>

                <!-- Enterprise Tier -->
                <div class="bg-background rounded-lg border border-border p-8 hover:shadow-hover transition-shadow duration-normal">
                    <h3 class="text-subtitle font-semibold text-text-primary mb-2">Enterprise</h3>
                    <p class="text-body-xs text-text-tertiary mb-6">For large-scale hiring</p>
                    <div class="mb-6">
                        <span class="text-display-sm font-bold text-text-primary">Custom</span>
                    </div>
                    <ul class="space-y-3 mb-8">
                        <li class="flex items-start text-body-sm text-text-secondary">
                            <svg class="w-5 h-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>Unlimited everything</span>
                        </li>
                        <li class="flex items-start text-body-sm text-text-secondary">
                            <svg class="w-5 h-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>Dedicated account manager</span>
                        </li>
                        <li class="flex items-start text-body-sm text-text-secondary">
                            <svg class="w-5 h-5 text-success mr-2 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                            </svg>
                            <span>Custom integrations</span>
                        </li>
                    </ul>
                    <x-button href="{{ url('/contact') }}" variant="secondary" class="w-full">
                        Contact Sales
                    </x-button>
                </div>
            </div>

            <div class="max-w-3xl mx-auto text-center bg-primary-light rounded-lg border border-primary-border p-6">
                <p class="text-body-sm text-text-primary">
                    <strong>Phase 2 Launch:</strong> Complete pricing plans, billing system, and advanced features coming Q2 2026. Early employers will have special pricing locked in.
                </p>
            </div>
        </div>
    </div>

    <!-- CTA Section -->
    <div class="section-spacing">
        <div class="container-base">
            <x-cta-panel 
                title="Ready to Find Your Next Team Member?" 
                subtitle="Join Croatian businesses already hiring through CroWork"
                centered
            >
                <x-slot name="actions">
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <x-button href="{{ url('/employer/register') }}" variant="primary" size="lg">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                            </svg>
                            Create Employer Account
                        </x-button>
                        <x-button href="{{ url('/contact') }}" variant="outline" size="lg">
                            Questions? Contact Us
                        </x-button>
                    </div>
                </x-slot>
            </x-cta-panel>
        </div>
    </div>
</x-app-layout>
