<x-app-layout>
    <x-slot name="title">Job Card Component Examples</x-slot>
    <x-slot name="description">Examples of the job-card component with different configurations</x-slot>

    <section class="section-spacing">
        <div class="container-base">
            <x-section-header
                title="Job Card Component Examples"
                subtitle="Various configurations of the Fluent 2-styled job-card component"
            />

            <!-- Example 1: Full-featured cards -->
            <div class="mb-12">
                <h3 class="text-title-3 font-semibold text-text-primary mb-4">Full-Featured Job Cards</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <x-job-card
                        title="Senior PHP Developer"
                        company="Tech Solutions Croatia"
                        city="Zagreb"
                        :salary_min="3000"
                        :salary_max="5000"
                        salary_currency="EUR"
                        salary_period="month"
                        :accommodation_provided="false"
                        :languages="['EN', 'HR']"
                        :posted_at="now()->subHours(2)"
                        href="#"
                    />

                    <x-job-card
                        title="Hotel Manager"
                        company="Adriatic Hotels Group"
                        city="Split"
                        :salary_min="2500"
                        :salary_max="4000"
                        :accommodation_provided="true"
                        :languages="['EN', 'HR', 'DE', 'IT']"
                        :posted_at="now()->subDays(1)"
                        href="#"
                    />

                    <x-job-card
                        title="Frontend Developer"
                        company="Digital Agency Zagreb"
                        city="Zagreb"
                        :salary_min="2800"
                        :salary_max="4500"
                        :languages="['EN']"
                        :posted_at="now()->subMinutes(45)"
                        href="#"
                    />
                </div>
            </div>

            <!-- Example 2: Various salary configurations -->
            <div class="mb-12">
                <h3 class="text-title-3 font-semibold text-text-primary mb-4">Different Salary Displays</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <x-job-card
                        title="Full-Stack Developer"
                        company="Startup Hub"
                        city="Rijeka"
                        :salary_min="3500"
                        :salary_max="6000"
                        :languages="['EN', 'HR']"
                        :posted_at="now()->subHours(5)"
                        href="#"
                    />

                    <x-job-card
                        title="Junior Designer"
                        company="Creative Studio"
                        city="Dubrovnik"
                        :salary_min="1800"
                        :languages="['EN']"
                        :posted_at="now()->subDays(2)"
                        href="#"
                    />

                    <x-job-card
                        title="Marketing Specialist"
                        company="E-commerce Ltd"
                        city="Osijek"
                        :salary_max="3000"
                        :posted_at="now()->subWeeks(1)"
                        href="#"
                    />

                    <x-job-card
                        title="Content Writer"
                        company="Media Company"
                        city="Remote"
                        :languages="['EN']"
                        :posted_at="now()->subDays(3)"
                        href="#"
                    />
                </div>
            </div>

            <!-- Example 3: Minimal information -->
            <div class="mb-12">
                <h3 class="text-title-3 font-semibold text-text-primary mb-4">Minimal Job Cards</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <x-job-card
                        title="Chef de Cuisine"
                        city="Zadar"
                        :accommodation_provided="true"
                        :posted_at="now()->subDays(4)"
                        href="#"
                    />

                    <x-job-card
                        title="Tour Guide"
                        company="Adventure Tours"
                        :languages="['EN', 'DE', 'FR', 'ES', 'IT']"
                        :posted_at="now()->subHours(12)"
                        href="#"
                    />

                    <x-job-card
                        title="Software Engineer"
                        :salary_min="4000"
                        :posted_at="now()->subMinutes(30)"
                        href="#"
                    />
                </div>
            </div>

            <!-- Example 4: Hourly rates -->
            <div class="mb-12">
                <h3 class="text-title-3 font-semibold text-text-primary mb-4">Hourly Rate Jobs</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <x-job-card
                        title="Freelance Web Developer"
                        company="Freelance Platform"
                        city="Remote"
                        :salary_min="25"
                        :salary_max="50"
                        salary_period="hour"
                        :languages="['EN']"
                        :posted_at="now()->subHours(8)"
                        href="#"
                    />

                    <x-job-card
                        title="Tutor - English Language"
                        company="Language School"
                        city="Zagreb"
                        :salary_min="15"
                        :salary_max="30"
                        salary_period="hour"
                        :languages="['EN', 'HR']"
                        :posted_at="now()->subDays(5)"
                        href="#"
                    />

                    <x-job-card
                        title="Part-time Barista"
                        company="Coffee Culture"
                        city="Split"
                        :salary_min="8"
                        salary_period="hour"
                        :posted_at="now()->subHours(3)"
                        href="#"
                    />
                </div>
            </div>

            <!-- Code Example -->
            <x-card elevated>
                <h3 class="text-title-3 font-semibold text-text-primary mb-4">Usage Example</h3>
                <pre class="bg-surface p-4 rounded-md overflow-x-auto text-body-sm"><code>&lt;x-job-card
    title="Senior PHP Developer"
    company="Tech Solutions Croatia"
    city="Zagreb"
    :salary_min="3000"
    :salary_max="5000"
    salary_currency="EUR"
    salary_period="month"
    :accommodation_provided="false"
    :languages="['EN', 'HR']"
    :posted_at="now()-&gt;subHours(2)"
    href="/jobs/senior-php-developer"
/&gt;</code></pre>
            </x-card>
        </div>
    </section>
</x-app-layout>
