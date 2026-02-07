<x-app-layout>
    <x-slot name="title">Design System Demo</x-slot>
    <x-slot name="description">CroWork Fluent 2 Design System demonstration page</x-slot>

    <!-- Hero Section -->
    <section class="bg-primary-light section-spacing">
        <div class="container-base">
            <div class="max-w-3xl mx-auto text-center">
                <h1 class="text-large-display font-semibold text-text-primary mb-4">
                    Welcome to CroWork
                </h1>
                <p class="text-body-lg text-text-secondary mb-6">
                    Find your dream career in Croatia. Browse thousands of jobs and education opportunities tailored for international talent.
                </p>
                <div class="flex gap-3 justify-center flex-wrap">
                    <x-button variant="primary" size="lg">Browse Jobs</x-button>
                    <x-button variant="outline" size="lg">Learn More</x-button>
                </div>
            </div>
        </div>
    </section>

    <!-- Components Demo Section -->
    <section class="section-spacing">
        <div class="container-base">
            <x-section-header
                title="Design System Components"
                subtitle="Fluent 2-inspired UI components for CroWork"
                centered
            />

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
                <!-- Buttons Card -->
                <x-card title="Buttons" elevated>
                    <div class="space-y-3">
                        <x-button variant="primary" class="w-full">Primary</x-button>
                        <x-button variant="secondary" class="w-full">Secondary</x-button>
                        <x-button variant="subtle" class="w-full">Subtle</x-button>
                        <x-button variant="ghost" class="w-full">Ghost</x-button>
                        <x-button variant="outline" class="w-full">Outline</x-button>
                    </div>
                </x-card>

                <!-- Badges Card -->
                <x-card title="Badges" elevated>
                    <div class="space-y-3">
                        <div>
                            <x-badge variant="default">Default</x-badge>
                            <x-badge variant="primary">Primary</x-badge>
                        </div>
                        <div>
                            <x-badge variant="success">Active</x-badge>
                            <x-badge variant="warning">Pending</x-badge>
                        </div>
                        <div>
                            <x-badge variant="danger">Closed</x-badge>
                            <x-badge variant="info">New</x-badge>
                        </div>
                    </div>
                </x-card>

                <!-- Forms Card -->
                <x-card title="Form Elements" elevated>
                    <div class="space-y-3">
                        <x-input
                            label="Full Name"
                            name="demo_name"
                            id="demo_name"
                            placeholder="Enter your name"
                        />
                        <x-select
                            label="Country"
                            name="demo_country"
                            id="demo_country"
                            :options="['hr' => 'Croatia', 'si' => 'Slovenia']"
                            placeholder="Select country"
                        />
                    </div>
                </x-card>
            </div>
        </div>
    </section>

    <!-- Typography Section -->
    <section class="bg-surface section-spacing">
        <div class="container-base">
            <x-section-header
                title="Typography Hierarchy"
                subtitle="Fluent 2 type scale with optimal readability"
            />

            <div class="space-y-4 mt-8 max-w-4xl">
                <div>
                    <h1>Heading 1 - Page Titles</h1>
                    <p class="text-caption text-text-tertiary">text-title-1 / 28px</p>
                </div>
                <div>
                    <h2>Heading 2 - Section Titles</h2>
                    <p class="text-caption text-text-tertiary">text-title-2 / 24px</p>
                </div>
                <div>
                    <h3>Heading 3 - Subsection Headers</h3>
                    <p class="text-caption text-text-tertiary">text-title-3 / 20px</p>
                </div>
                <div>
                    <p class="text-body">
                        Body text provides optimal readability with generous line-height and careful spacing. This is the default text style used throughout the application.
                    </p>
                    <p class="text-caption text-text-tertiary">text-body / 14px</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Color Palette Section -->
    <section class="section-spacing">
        <div class="container-base">
            <x-section-header
                title="Color System"
                subtitle="Semantic color tokens based on Fluent 2 design"
            />

            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4 mt-8">
                <div>
                    <div class="h-24 bg-primary rounded-lg mb-2"></div>
                    <p class="text-body-sm font-semibold">Primary</p>
                    <p class="text-caption text-text-tertiary">#346AF0</p>
                </div>
                <div>
                    <div class="h-24 bg-secondary rounded-lg mb-2"></div>
                    <p class="text-body-sm font-semibold">Secondary</p>
                    <p class="text-caption text-text-tertiary">#008272</p>
                </div>
                <div>
                    <div class="h-24 bg-accent rounded-lg mb-2"></div>
                    <p class="text-body-sm font-semibold">Accent</p>
                    <p class="text-caption text-text-tertiary">#00B294</p>
                </div>
                <div>
                    <div class="h-24 bg-success rounded-lg mb-2"></div>
                    <p class="text-body-sm font-semibold">Success</p>
                    <p class="text-caption text-text-tertiary">#107C10</p>
                </div>
                <div>
                    <div class="h-24 bg-warning rounded-lg mb-2"></div>
                    <p class="text-body-sm font-semibold">Warning</p>
                    <p class="text-caption text-text-tertiary">#FFB900</p>
                </div>
                <div>
                    <div class="h-24 bg-danger rounded-lg mb-2"></div>
                    <p class="text-body-sm font-semibold">Danger</p>
                    <p class="text-caption text-text-tertiary">#D13438</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Cards Section -->
    <section class="bg-surface section-spacing">
        <div class="container-base">
            <x-section-header
                title="Interactive Cards"
                subtitle="Hover over cards to see elevation changes"
            />

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">
                <x-card title="Senior PHP Developer" elevated interactive href="#">
                    <div class="space-y-2">
                        <p class="text-body-sm text-text-secondary">Tech Solutions Croatia</p>
                        <p class="text-body-sm text-text-secondary">Zagreb, Croatia</p>
                        <div class="flex gap-2 mt-3">
                            <x-badge variant="primary">Full-time</x-badge>
                            <x-badge variant="success">Remote OK</x-badge>
                        </div>
                    </div>
                </x-card>

                <x-card title="Frontend Developer" elevated interactive href="#">
                    <div class="space-y-2">
                        <p class="text-body-sm text-text-secondary">Digital Agency Croatia</p>
                        <p class="text-body-sm text-text-secondary">Split, Croatia</p>
                        <div class="flex gap-2 mt-3">
                            <x-badge variant="primary">Full-time</x-badge>
                            <x-badge variant="warning">On-site</x-badge>
                        </div>
                    </div>
                </x-card>

                <x-card title="DevOps Engineer" elevated interactive href="#">
                    <div class="space-y-2">
                        <p class="text-body-sm text-text-secondary">Cloud Services Croatia</p>
                        <p class="text-body-sm text-text-secondary">Remote</p>
                        <div class="flex gap-2 mt-3">
                            <x-badge variant="primary">Full-time</x-badge>
                            <x-badge variant="success">Remote OK</x-badge>
                        </div>
                    </div>
                </x-card>
            </div>
        </div>
    </section>
</x-app-layout>
