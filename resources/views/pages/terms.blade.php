<x-app-layout>
    <x-slot name="title">Terms of Service</x-slot>
    <x-slot name="description">CroWork Terms of Service. Read our terms and conditions for using the platform.</x-slot>
    <x-slot name="canonical">{{ route('terms') }}</x-slot>

    <!-- Hero Section -->
    <x-hero 
        size="sm" 
        title="Terms of Service"
        subtitle="Last updated: {{ date('F j, Y') }}"
    />

    <section class="cw-section">
        <div class="cw-container max-w-4xl">
            <!-- Content -->
            <div class="prose prose-neutral max-w-none space-y-8">
                <!-- Introduction -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">Agreement to Terms</h2>
                    <p class="text-base text-slate-700 leading-relaxed">
                        By accessing or using CroWork ("Platform", "Service"), you agree to be bound by these Terms of Service ("Terms"). If you do not agree to these Terms, please do not use our Platform. These Terms apply to all users, including job seekers, employers, and visitors.
                    </p>
                </section>

                <!-- Use of Service -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">Use of Service</h2>
                    
                    <h3 class="text-lg font-semibold text-slate-900 mb-3 mt-6">Eligibility</h3>
                    <p class="text-base text-slate-700 mb-4 leading-relaxed">
                        You must be at least 18 years old and capable of forming a binding contract to use our Service. By using the Platform, you represent and warrant that you meet these requirements.
                    </p>

                    <h3 class="text-lg font-semibold text-slate-900 mb-3">Account Registration</h3>
                    <p class="text-base text-slate-700 mb-4 leading-relaxed">
                        To access certain features, you must create an account. You agree to:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-base text-slate-700 mb-6">
                        <li>Provide accurate, current, and complete information</li>
                        <li>Maintain and update your information as needed</li>
                        <li>Keep your password secure and confidential</li>
                        <li>Notify us immediately of any unauthorized access</li>
                        <li>Accept responsibility for all activities under your account</li>
                    </ul>

                    <h3 class="text-lg font-semibold text-slate-900 mb-3">Prohibited Activities</h3>
                    <p class="text-base text-slate-700 mb-4 leading-relaxed">
                        You may not:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-base text-slate-700">
                        <li>Violate any applicable laws or regulations</li>
                        <li>Impersonate another person or entity</li>
                        <li>Post false, misleading, or fraudulent content</li>
                        <li>Harass, abuse, or harm other users</li>
                        <li>Scrape, mine, or collect data from the Platform</li>
                        <li>Interfere with the Platform's operation or security</li>
                        <li>Upload malicious code or viruses</li>
                        <li>Use the Platform for commercial solicitation without permission</li>
                    </ul>
                </section>

                <!-- For Job Seekers -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">Terms for Job Seekers</h2>
                    <p class="text-base text-slate-700 mb-4 leading-relaxed">
                        As a job seeker, you agree to:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-base text-slate-700 mb-6">
                        <li>Provide truthful and accurate information in your profile and applications</li>
                        <li>Only apply for positions you are qualified for and interested in</li>
                        <li>Respect employer communications and respond professionally</li>
                        <li>Not share or sell access to employer information</li>
                        <li>Comply with all applicable work authorization and visa requirements</li>
                    </ul>
                    <p class="text-base text-slate-700 leading-relaxed">
                        CroWork is a platform connecting job seekers and employers. We do not guarantee employment, interviews, or job offers. The hiring decision is solely at the employer's discretion.
                    </p>
                </section>

                <!-- For Employers -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">Terms for Employers</h2>
                    <p class="text-base text-slate-700 mb-4 leading-relaxed">
                        As an employer, you agree to:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-base text-slate-700 mb-6">
                        <li>Post only legitimate job opportunities</li>
                        <li>Provide accurate job descriptions and requirements</li>
                        <li>Comply with all applicable employment laws</li>
                        <li>Not discriminate based on protected characteristics</li>
                        <li>Respect candidate privacy and data protection rights</li>
                        <li>Not use candidate information for unauthorized purposes</li>
                        <li>Honor the terms of any paid services you subscribe to</li>
                    </ul>
                    <p class="text-base text-slate-700 leading-relaxed">
                        We reserve the right to remove job postings that violate these Terms or applicable laws.
                    </p>
                </section>

                <!-- Content and Intellectual Property -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">Content and Intellectual Property</h2>
                    
                    <h3 class="text-lg font-semibold text-slate-900 mb-3 mt-6">Your Content</h3>
                    <p class="text-base text-slate-700 mb-4 leading-relaxed">
                        You retain ownership of content you submit to the Platform. By posting content, you grant us a non-exclusive, worldwide, royalty-free license to use, display, and distribute your content for the purpose of operating and improving the Platform.
                    </p>

                    <h3 class="text-lg font-semibold text-slate-900 mb-3">Our Content</h3>
                    <p class="text-base text-slate-700 leading-relaxed">
                        All Platform content, features, and functionality (including but not limited to text, graphics, logos, and software) are owned by CroWork and protected by international copyright, trademark, and other intellectual property laws.
                    </p>
                </section>

                <!-- Disclaimer -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">Disclaimer of Warranties</h2>
                    <p class="text-base text-slate-700 leading-relaxed">
                        THE PLATFORM IS PROVIDED "AS IS" AND "AS AVAILABLE" WITHOUT WARRANTIES OF ANY KIND, EITHER EXPRESS OR IMPLIED. WE DO NOT WARRANT THAT THE PLATFORM WILL BE UNINTERRUPTED, ERROR-FREE, OR SECURE. WE DO NOT VERIFY ALL INFORMATION PROVIDED BY USERS AND ARE NOT RESPONSIBLE FOR USER-GENERATED CONTENT.
                    </p>
                </section>

                <!-- Limitation of Liability -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">Limitation of Liability</h2>
                    <p class="text-base text-slate-700 leading-relaxed">
                        TO THE MAXIMUM EXTENT PERMITTED BY LAW, CROWORK SHALL NOT BE LIABLE FOR ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR PUNITIVE DAMAGES, OR ANY LOSS OF PROFITS OR REVENUES, WHETHER INCURRED DIRECTLY OR INDIRECTLY, OR ANY LOSS OF DATA, USE, OR GOODWILL ARISING OUT OF YOUR USE OF THE PLATFORM.
                    </p>
                </section>

                <!-- Termination -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">Termination</h2>
                    <p class="text-base text-slate-700 leading-relaxed">
                        We reserve the right to suspend or terminate your account at any time, with or without notice, for conduct that we believe violates these Terms or is harmful to other users, us, or third parties, or for any other reason at our sole discretion. You may terminate your account at any time by contacting us.
                    </p>
                </section>

                <!-- Governing Law -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">Governing Law and Disputes</h2>
                    <p class="text-base text-slate-700 leading-relaxed">
                        These Terms are governed by the laws of the Republic of Croatia. Any disputes arising from these Terms or your use of the Platform shall be resolved in the courts of Zagreb, Croatia. You agree to submit to the personal jurisdiction of these courts.
                    </p>
                </section>

                <!-- Changes to Terms -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">Changes to Terms</h2>
                    <p class="text-base text-slate-700 leading-relaxed">
                        We reserve the right to modify these Terms at any time. We will notify users of material changes via email or Platform notification. Your continued use of the Platform after changes constitutes acceptance of the modified Terms.
                    </p>
                </section>

                <!-- Contact -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">Contact Information</h2>
                    <p class="text-base text-slate-700 mb-4 leading-relaxed">
                        If you have questions about these Terms, please contact us:
                    </p>
                    <ul class="space-y-2 text-base text-slate-700">
                        <li><strong>Email:</strong> <a href="mailto:legal@crowork.hr" class="text-slate-900 hover:text-slate-700 transition-colors duration-150">legal@crowork.hr</a></li>
                        <li><strong>Address:</strong> Ilica 242, 10000 Zagreb, Croatia</li>
                    </ul>
                </section>
            </div>
        </div>
    </section>
</x-app-layout>
