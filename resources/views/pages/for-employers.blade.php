<x-app-layout>
    <x-slot name="title">For Employers - Hire International Talent</x-slot>
    <x-slot name="description">Find qualified international workers for your Croatian business through a premium hiring platform designed for trust and speed.</x-slot>
    <x-slot name="canonical">{{ route('for-employers') }}</x-slot>

    <x-hero
        size="md"
        title="Hire International Talent with Confidence"
        subtitle="CroWork gives Croatian employers verified candidates, structured applications, and a calm, premium hiring workflow from first post to final decision."
        theme="employers"
    >
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <x-button href="{{ url('/employer/register') }}" variant="primary" size="lg">Create Employer Account</x-button>
            <x-button href="{{ url('/contact') }}" variant="outline" size="lg">Contact Sales</x-button>
        </div>
    </x-hero>

    <section class="section-spacing-tight">
        <div class="container-base space-y-10">
            <div class="text-center max-w-3xl mx-auto">
                <h2 class="text-text-primary mb-3">Why Employers Choose CroWork</h2>
                <p class="text-body-lg text-text-secondary mb-0">Designed specifically for Croatian teams hiring internationally, without the usual complexity.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <x-surface variant="base" elevation="1" rounded="3xl" class="premium-glass">
                    <h3 class="text-title-2 font-semibold text-text-primary mb-3">Verified Candidates</h3>
                    <p class="text-body text-text-secondary mb-0">Access standardized worker profiles with clear language skills, role fit, and key qualifications.</p>
                </x-surface>

                <x-surface variant="base" elevation="1" rounded="3xl" class="premium-glass">
                    <h3 class="text-title-2 font-semibold text-text-primary mb-3">Faster Hiring Flow</h3>
                    <p class="text-body text-text-secondary mb-0">Post jobs quickly, review clean applications, and manage candidates in one focused dashboard.</p>
                </x-surface>

                <x-surface variant="base" elevation="1" rounded="3xl" class="premium-glass">
                    <h3 class="text-title-2 font-semibold text-text-primary mb-3">Built-In Trust</h3>
                    <p class="text-body text-text-secondary mb-0">Platform moderation and transparent profiles reduce risk and help teams hire with confidence.</p>
                </x-surface>
            </div>
        </div>
    </section>

    <section class="section-spacing-tight">
        <div class="container-base">
            <x-surface variant="base" elevation="1" rounded="3xl" class="premium-glass max-w-5xl mx-auto">
                <x-section-header
                    title="How It Works"
                    subtitle="From registration to hiring in four clear steps"
                    centered
                    class="mb-8"
                />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="rounded-2xl bg-white/70 border border-white/80 p-5">
                        <p class="text-caption uppercase tracking-[0.1em] text-text-tertiary mb-2">Step 1</p>
                        <h3 class="text-subtitle font-semibold text-text-primary mb-2">Create and verify your account</h3>
                        <p class="text-body-sm text-text-secondary mb-0">Submit company details. We verify employers to maintain marketplace trust.</p>
                    </div>

                    <div class="rounded-2xl bg-white/70 border border-white/80 p-5">
                        <p class="text-caption uppercase tracking-[0.1em] text-text-tertiary mb-2">Step 2</p>
                        <h3 class="text-subtitle font-semibold text-text-primary mb-2">Publish job listings</h3>
                        <p class="text-body-sm text-text-secondary mb-0">Add role details, salary, language requirements, and accommodation information.</p>
                    </div>

                    <div class="rounded-2xl bg-white/70 border border-white/80 p-5">
                        <p class="text-caption uppercase tracking-[0.1em] text-text-tertiary mb-2">Step 3</p>
                        <h3 class="text-subtitle font-semibold text-text-primary mb-2">Review structured applications</h3>
                        <p class="text-body-sm text-text-secondary mb-0">Compare candidates quickly with a consistent profile and application format.</p>
                    </div>

                    <div class="rounded-2xl bg-white/70 border border-white/80 p-5">
                        <p class="text-caption uppercase tracking-[0.1em] text-text-tertiary mb-2">Step 4</p>
                        <h3 class="text-subtitle font-semibold text-text-primary mb-2">Hire with clarity</h3>
                        <p class="text-body-sm text-text-secondary mb-0">Move forward with confidence using verified data and transparent communication.</p>
                    </div>
                </div>
            </x-surface>
        </div>
    </section>

    <section class="section-spacing-tight">
        <div class="container-base max-w-5xl">
            <x-surface variant="base" elevation="2" rounded="3xl" class="premium-glass border-success/20">
                <h2 class="text-title-1 font-semibold text-text-primary mb-3">Transparent Pricing</h2>
                <p class="text-body text-text-secondary mb-6">Phase 2 pricing plans are being finalized with options for occasional hiring and continuous recruitment teams.</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="rounded-2xl bg-white/70 border border-white/80 p-4">
                        <p class="text-caption uppercase tracking-[0.1em] text-text-tertiary mb-2">Starter</p>
                        <p class="text-title-2 font-semibold text-text-primary mb-1">Pay per post</p>
                        <p class="text-body-sm text-text-secondary mb-0">For occasional roles.</p>
                    </div>
                    <div class="rounded-2xl bg-white/70 border border-white/80 p-4">
                        <p class="text-caption uppercase tracking-[0.1em] text-text-tertiary mb-2">Growth</p>
                        <p class="text-title-2 font-semibold text-text-primary mb-1">Monthly plan</p>
                        <p class="text-body-sm text-text-secondary mb-0">For active hiring teams.</p>
                    </div>
                    <div class="rounded-2xl bg-white/70 border border-white/80 p-4">
                        <p class="text-caption uppercase tracking-[0.1em] text-text-tertiary mb-2">Enterprise</p>
                        <p class="text-title-2 font-semibold text-text-primary mb-1">Custom</p>
                        <p class="text-body-sm text-text-secondary mb-0">For large organizations.</p>
                    </div>
                </div>
            </x-surface>
        </div>
    </section>

    <section class="section-spacing-tight pt-0">
        <div class="container-base">
            <x-cta-panel
                title="Ready to Build Your Team?"
                subtitle="Create your employer account and start hiring international talent in Croatia."
                centered
            >
                <x-slot name="actions">
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <x-button href="{{ url('/employer/register') }}" variant="primary" size="lg">Create Employer Account</x-button>
                        <x-button href="{{ url('/contact') }}" variant="outline" size="lg">Talk to Sales</x-button>
                    </div>
                </x-slot>
            </x-cta-panel>
        </div>
    </section>
</x-app-layout>
