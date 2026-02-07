<x-app-layout>
    <x-slot name="title">Privacy Policy</x-slot>
    <x-slot name="description">CroWork Privacy Policy. Learn how we collect, use, and protect your personal information.</x-slot>

    <!-- Hero Section -->
    <x-hero 
        size="sm" 
        title="Privacy Policy"
        subtitle="Last updated: {{ date('F j, Y') }}"
    />

    <div class="section-spacing">
        <div class="container-base max-w-4xl">
            <!-- Content -->
            <div class="prose prose-neutral max-w-none space-y-8">
                <!-- Introduction -->
                <section class="bg-surface rounded-lg border border-border p-8">
                    <h2 class="text-title-2 font-semibold text-text-primary mb-4">Introduction</h2>
                    <p class="text-body text-text-secondary leading-relaxed">
                        CroWork ("we", "our", or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you use our platform. Please read this policy carefully to understand our practices regarding your personal data.
                    </p>
                </section>

                <!-- Information We Collect -->
                <section class="bg-surface rounded-lg border border-border p-8">
                    <h2 class="text-title-2 font-semibold text-text-primary mb-4">Information We Collect</h2>
                    
                    <h3 class="text-subtitle font-semibold text-text-primary mb-3 mt-6">Personal Information</h3>
                    <p class="text-body text-text-secondary mb-4 leading-relaxed">
                        When you register and use CroWork, we may collect the following information:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-body text-text-secondary mb-6">
                        <li>Name, email address, and contact information</li>
                        <li>Professional information (work experience, education, skills)</li>
                        <li>Resume/CV and profile photo</li>
                        <li>Nationality and language preferences</li>
                        <li>Company information (for employers)</li>
                        <li>Job application data and communications</li>
                    </ul>

                    <h3 class="text-subtitle font-semibold text-text-primary mb-3">Usage Information</h3>
                    <p class="text-body text-text-secondary leading-relaxed">
                        We automatically collect certain information about your device and how you interact with our platform, including IP address, browser type, pages visited, time spent on pages, and referring website addresses.
                    </p>
                </section>

                <!-- How We Use Your Information -->
                <section class="bg-surface rounded-lg border border-border p-8">
                    <h2 class="text-title-2 font-semibold text-text-primary mb-4">How We Use Your Information</h2>
                    <p class="text-body text-text-secondary mb-4 leading-relaxed">
                        We use the collected information for the following purposes:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-body text-text-secondary">
                        <li>To create and manage your account</li>
                        <li>To match job seekers with employers</li>
                        <li>To process job applications and facilitate communications</li>
                        <li>To improve our services and user experience</li>
                        <li>To send service-related notifications and updates</li>
                        <li>To comply with legal obligations</li>
                        <li>To prevent fraud and ensure platform security</li>
                        <li>To analyze usage patterns and optimize our platform</li>
                    </ul>
                </section>

                <!-- Information Sharing -->
                <section class="bg-surface rounded-lg border border-border p-8">
                    <h2 class="text-title-2 font-semibold text-text-primary mb-4">Information Sharing and Disclosure</h2>
                    <p class="text-body text-text-secondary mb-4 leading-relaxed">
                        We may share your information in the following circumstances:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-body text-text-secondary mb-6">
                        <li><strong>With Employers:</strong> When you apply for a job, your profile information is shared with the relevant employer</li>
                        <li><strong>With Service Providers:</strong> Third-party vendors who help us operate our platform</li>
                        <li><strong>For Legal Reasons:</strong> When required by law or to protect our rights</li>
                        <li><strong>Business Transfers:</strong> In connection with a merger, sale, or acquisition</li>
                    </ul>
                    <p class="text-body text-text-secondary leading-relaxed">
                        We do not sell your personal information to third parties.
                    </p>
                </section>

                <!-- Data Security -->
                <section class="bg-surface rounded-lg border border-border p-8">
                    <h2 class="text-title-2 font-semibold text-text-primary mb-4">Data Security</h2>
                    <p class="text-body text-text-secondary leading-relaxed">
                        We implement appropriate technical and organizational measures to protect your personal information against unauthorized access, alteration, disclosure, or destruction. However, no method of transmission over the internet is 100% secure, and we cannot guarantee absolute security.
                    </p>
                </section>

                <!-- Your Rights -->
                <section class="bg-surface rounded-lg border border-border p-8">
                    <h2 class="text-title-2 font-semibold text-text-primary mb-4">Your Rights</h2>
                    <p class="text-body text-text-secondary mb-4 leading-relaxed">
                        Under GDPR and Croatian data protection laws, you have the following rights:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-body text-text-secondary">
                        <li><strong>Access:</strong> Request a copy of your personal data</li>
                        <li><strong>Rectification:</strong> Correct inaccurate or incomplete data</li>
                        <li><strong>Erasure:</strong> Request deletion of your data</li>
                        <li><strong>Restriction:</strong> Limit how we use your data</li>
                        <li><strong>Portability:</strong> Receive your data in a structured format</li>
                        <li><strong>Objection:</strong> Object to certain types of processing</li>
                        <li><strong>Withdraw Consent:</strong> Withdraw previously given consent</li>
                    </ul>
                </section>

                <!-- Data Retention -->
                <section class="bg-surface rounded-lg border border-border p-8">
                    <h2 class="text-title-2 font-semibold text-text-primary mb-4">Data Retention</h2>
                    <p class="text-body text-text-secondary leading-relaxed">
                        We retain your personal information for as long as necessary to fulfill the purposes outlined in this Privacy Policy, unless a longer retention period is required or permitted by law. When you delete your account, we will delete or anonymize your data within 90 days.
                    </p>
                </section>

                <!-- Cookies -->
                <section class="bg-surface rounded-lg border border-border p-8">
                    <h2 class="text-title-2 font-semibold text-text-primary mb-4">Cookies and Tracking</h2>
                    <p class="text-body text-text-secondary mb-4 leading-relaxed">
                        We use cookies and similar tracking technologies to enhance your experience on our platform. You can control cookie preferences through your browser settings. For more information, please see our <a href="{{ route('cookies') }}" class="text-primary hover:text-primary-hover transition-colors duration-normal">Cookie Policy</a>.
                    </p>
                </section>

                <!-- Changes to Policy -->
                <section class="bg-surface rounded-lg border border-border p-8">
                    <h2 class="text-title-2 font-semibold text-text-primary mb-4">Changes to This Policy</h2>
                    <p class="text-body text-text-secondary leading-relaxed">
                        We may update this Privacy Policy from time to time. We will notify you of any changes by posting the new Privacy Policy on this page and updating the "Last updated" date. We encourage you to review this Privacy Policy periodically.
                    </p>
                </section>

                <!-- Contact -->
                <section class="bg-primary-50 rounded-lg border border-primary-200 p-8">
                    <h2 class="text-title-2 font-semibold text-text-primary mb-4">Contact Us</h2>
                    <p class="text-body text-text-secondary mb-4 leading-relaxed">
                        If you have any questions about this Privacy Policy or our data practices, please contact us:
                    </p>
                    <ul class="space-y-2 text-body text-text-secondary">
                        <li><strong>Email:</strong> <a href="mailto:privacy@crowork.hr" class="text-primary hover:text-primary-hover transition-colors duration-normal">privacy@crowork.hr</a></li>
                        <li><strong>Address:</strong> Ilica 242, 10000 Zagreb, Croatia</li>
                    </ul>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
