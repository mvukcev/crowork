@php
    use App\Services\ConsentConfigService;
@endphp

@if(ConsentConfigService::isBannerEnabled())
    <div id="cookie-banner" class="fixed bottom-0 left-0 right-0 bg-slate-900 text-white p-4 shadow-lg z-50" style="display:none;">
        <div class="cw-container flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex-1">
                <p class="text-sm">
                    We use cookies to enhance your experience. By continuing, you consent to our use of cookies.
                    @if(ConsentConfigService::getCookieStatementUrl())
                        <a href="{{ ConsentConfigService::getCookieStatementUrl() }}" target="_blank" rel="noopener noreferrer" class="underline hover:text-slate-200">
                            Learn more
                        </a>
                    @endif
                </p>
            </div>
            <div class="flex gap-2 flex-shrink-0">
                <button id="cookie-reject" class="px-4 py-2 bg-slate-700 hover:bg-slate-600 rounded text-sm whitespace-nowrap">
                    Reject
                </button>
                <button id="cookie-accept" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 rounded text-sm whitespace-nowrap font-semibold">
                    Accept All
                </button>
            </div>
        </div>
    </div>

    <script>
        (function() {
            const CONSENT_KEY = 'crowork_consent';
            const banner = document.getElementById('cookie-banner');
            const acceptBtn = document.getElementById('cookie-accept');
            const rejectBtn = document.getElementById('cookie-reject');

            // Check if consent is already given
            if (!localStorage.getItem(CONSENT_KEY)) {
                // Show banner if no consent decision
                if (banner) {
                    banner.style.display = 'block';
                }
            }

            // Accept all cookies
            if (acceptBtn) {
                acceptBtn.addEventListener('click', function() {
                    setConsent(true, true); // analytics=true, marketing=true
                    if (banner) banner.style.display = 'none';
                    window.location.reload(); // Reload to inject tracking
                });
            }

            // Reject non-essential cookies
            if (rejectBtn) {
                rejectBtn.addEventListener('click', function() {
                    setConsent(false, false); // analytics=false, marketing=false
                    if (banner) banner.style.display = 'none';
                    window.location.reload(); // Reload to prevent tracking
                });
            }

            function setConsent(analytics, marketing) {
                localStorage.setItem(CONSENT_KEY, JSON.stringify({
                    analytics: analytics,
                    marketing: marketing,
                    timestamp: new Date().toISOString()
                }));
                
                // Also set cookies for server-side checks
                document.cookie = 'consent_analytics=' + (analytics ? '1' : '0') + '; path=/; max-age=' + (365*24*60*60);
                document.cookie = 'consent_marketing=' + (marketing ? '1' : '0') + '; path=/; max-age=' + (365*24*60*60);
                
                // Dispatch event for custom handling
                window.dispatchEvent(new CustomEvent('consentUpdated', {
                    detail: { analytics, marketing }
                }));
            }
        })();
    </script>
@endif
