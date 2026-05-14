<x-app-layout>
    <x-slot name="title">Cookie Policy</x-slot>
    <x-slot name="description">CroWork Cookie Policy. Learn about how we use cookies and similar technologies on our platform.</x-slot>
    <x-slot name="canonical">{{ route('cookies') }}</x-slot>

    <!-- Hero Section -->
    <x-hero 
        size="sm" 
        title="Cookie Policy"
        subtitle="Last updated: {{ date('F j, Y') }}"
    />

    <section class="cw-section">
        <div class="cw-container max-w-4xl">
            <!-- Content -->
            <div class="prose prose-neutral max-w-none space-y-8">
                <!-- Introduction -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">What Are Cookies?</h2>
                    <p class="text-base text-slate-700 leading-relaxed">
                        Cookies are small text files that are placed on your device (computer, smartphone, or tablet) when you visit a website. They are widely used to make websites work more efficiently, provide a better user experience, and provide information to website owners.
                    </p>
                </section>

                <!-- How We Use Cookies -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">How We Use Cookies</h2>
                    <p class="text-base text-slate-700 mb-4 leading-relaxed">
                        CroWork uses cookies to enhance your experience and improve our services. We use cookies for:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-base text-slate-700">
                        <li>Authentication and security</li>
                        <li>Remembering your preferences and settings</li>
                        <li>Analyzing site usage and performance</li>
                        <li>Personalizing content and job recommendations</li>
                        <li>Providing social media features</li>
                        <li>Delivering relevant advertisements (with your consent)</li>
                    </ul>
                </section>

                <!-- Types of Cookies -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">Types of Cookies We Use</h2>
                    
                    <div class="space-y-6">
                        <!-- Essential Cookies -->
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-3">Essential Cookies</h3>
                            <p class="text-sm text-slate-700 mb-2 leading-relaxed">
                                These cookies are necessary for the Platform to function and cannot be disabled. They enable core functionality such as:
                            </p>
                            <ul class="list-disc list-inside space-y-1 text-sm text-slate-700 ml-4">
                                <li>User authentication and account access</li>
                                <li>Security and fraud prevention</li>
                                <li>Form submission and data processing</li>
                                <li>Session management</li>
                            </ul>
                        </div>

                        <!-- Performance Cookies -->
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-3">Performance Cookies</h3>
                            <p class="text-sm text-slate-700 mb-2 leading-relaxed">
                                These cookies help us understand how visitors interact with our Platform by collecting anonymous information:
                            </p>
                            <ul class="list-disc list-inside space-y-1 text-sm text-slate-700 ml-4">
                                <li>Page visit statistics and popular features</li>
                                <li>Error tracking and debugging</li>
                                <li>Site performance metrics</li>
                                <li>Loading time analysis</li>
                            </ul>
                        </div>

                        <!-- Functionality Cookies -->
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-3">Functionality Cookies</h3>
                            <p class="text-sm text-slate-700 mb-2 leading-relaxed">
                                These cookies enable enhanced functionality and personalization:
                            </p>
                            <ul class="list-disc list-inside space-y-1 text-sm text-slate-700 ml-4">
                                <li>Remembering your preferences (language, location)</li>
                                <li>Saved search filters and job alerts</li>
                                <li>Recently viewed jobs</li>
                                <li>UI customization settings</li>
                            </ul>
                        </div>

                        <!-- Targeting Cookies -->
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-3">Targeting/Advertising Cookies</h3>
                            <p class="text-sm text-slate-700 mb-2 leading-relaxed">
                                These cookies are used to deliver relevant advertisements (only with your consent):
                            </p>
                            <ul class="list-disc list-inside space-y-1 text-sm text-slate-700 ml-4">
                                <li>Personalized job recommendations</li>
                                <li>Targeted advertising based on interests</li>
                                <li>Measuring ad campaign effectiveness</li>
                                <li>Limiting ad frequency</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <!-- Third-Party Cookies -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">Third-Party Cookies</h2>
                    <p class="text-base text-slate-700 mb-4 leading-relaxed">
                        We use services from trusted third parties who may also set cookies. These include:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-base text-slate-700 mb-6">
                        <li><strong>Google Analytics:</strong> For website analytics and usage statistics</li>
                        <li><strong>Social Media Platforms:</strong> For social sharing features (LinkedIn, Facebook)</li>
                        <li><strong>Payment Processors:</strong> For secure payment processing (employers)</li>
                        <li><strong>Email Services:</strong> For communication tracking and engagement</li>
                    </ul>
                    <p class="text-base text-slate-700 leading-relaxed">
                        These third parties have their own privacy policies and cookie policies. We recommend reviewing them to understand how they use cookies.
                    </p>
                </section>

                <!-- Cookie Duration -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">Cookie Duration</h2>
                    
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">Session Cookies</h3>
                            <p class="text-sm text-slate-700 leading-relaxed">
                                These temporary cookies are deleted when you close your browser. They're used for essential functions like maintaining your login session.
                            </p>
                        </div>

                        <div>
                            <h3 class="text-lg font-semibold text-slate-900 mb-2">Persistent Cookies</h3>
                            <p class="text-sm text-slate-700 leading-relaxed">
                                These cookies remain on your device for a set period (ranging from days to years) and are activated each time you visit the Platform. They remember your preferences and improve your experience across sessions.
                            </p>
                        </div>
                    </div>
                </section>

                <!-- Managing Cookies -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">Managing Your Cookie Preferences</h2>
                    
                    <h3 class="text-lg font-semibold text-slate-900 mb-3 mt-6">Browser Settings</h3>
                    <p class="text-base text-slate-700 mb-4 leading-relaxed">
                        Most web browsers allow you to control cookies through their settings:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-sm text-slate-700 mb-6">
                        <li>Block all cookies</li>
                        <li>Allow only first-party cookies</li>
                        <li>Delete cookies when you close your browser</li>
                        <li>View and delete individual cookies</li>
                    </ul>

                    <h3 class="text-lg font-semibold text-slate-900 mb-3">Platform Settings</h3>
                    <p class="text-base text-slate-700 mb-4 leading-relaxed">
                        You can manage your cookie preferences directly on CroWork through your account settings. Note that blocking essential cookies may affect Platform functionality.
                    </p>

                    <h3 class="text-lg font-semibold text-slate-900 mb-3">Opt-Out Tools</h3>
                    <p class="text-base text-slate-700 leading-relaxed">
                        For advertising cookies, you can opt out through industry opt-out tools:
                    </p>
                    <ul class="list-disc list-inside space-y-1 text-sm text-slate-700 ml-4 mt-2">
                        <li>Network Advertising Initiative (NAI)</li>
                        <li>Digital Advertising Alliance (DAA)</li>
                        <li>European Interactive Digital Advertising Alliance (EDAA)</li>
                    </ul>
                </section>

                <!-- Impact of Disabling -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">Impact of Disabling Cookies</h2>
                    <p class="text-base text-slate-700 mb-4 leading-relaxed">
                        While you can block or delete cookies, this may impact your experience:
                    </p>
                    <ul class="list-disc list-inside space-y-2 text-base text-slate-700">
                        <li>You may need to log in repeatedly</li>
                        <li>Preference settings may not be saved</li>
                        <li>Some features may not work properly</li>
                        <li>Job recommendations may be less relevant</li>
                        <li>Page loading may be slower</li>
                    </ul>
                </section>

                <!-- Changes to Policy -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">Changes to This Cookie Policy</h2>
                    <p class="text-base text-slate-700 leading-relaxed">
                        We may update this Cookie Policy from time to time to reflect changes in technology, legislation, or our practices. We encourage you to review this policy periodically. The "Last updated" date at the top indicates when changes were last made.
                    </p>
                </section>

                <!-- More Information -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">More Information</h2>
                    <p class="text-base text-slate-700 mb-4 leading-relaxed">
                        For more information about how we handle your personal data, please see our <a href="{{ route('privacy') }}" class="text-slate-900 hover:text-slate-700 transition-colors duration-150">Privacy Policy</a>.
                    </p>
                    <p class="text-base text-slate-700 leading-relaxed">
                        For general information about cookies, visit <a href="https://www.allaboutcookies.org" target="_blank" rel="noopener noreferrer" class="text-slate-900 hover:text-slate-700 transition-colors duration-150">www.allaboutcookies.org</a>.
                    </p>
                </section>

                <!-- Contact -->
                <section class="cw-surface p-8">
                    <h2 class="text-2xl font-semibold text-slate-900 mb-4">Contact Us</h2>
                    <p class="text-base text-slate-700 mb-4 leading-relaxed">
                        If you have questions about our use of cookies, please contact us:
                    </p>
                    <ul class="space-y-2 text-base text-slate-700">
                        <li><strong>Email:</strong> <a href="mailto:privacy@crowork.hr" class="text-slate-900 hover:text-slate-700 transition-colors duration-150">privacy@crowork.hr</a></li>
                        <li><strong>Address:</strong> Ilica 242, 10000 Zagreb, Croatia</li>
                    </ul>
                </section>
            </div>
        </div>
    </section>
</x-app-layout>
