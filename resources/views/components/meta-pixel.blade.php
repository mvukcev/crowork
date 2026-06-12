@php
    $metaEnabled = \App\Services\MetaPixelConfigService::isTrackingEnabled();
    $pixelEnabled = \App\Models\Setting::getBool('meta_browser_enabled', true);
    $pixelId = (string) (\App\Services\MetaPixelConfigService::getPixelId() ?? '');
    $testEventCode = (string) (\App\Services\MetaPixelConfigService::getTestEventCode() ?? '');
    $hasConsent = \App\Services\ConsentConfigService::isMarketingAllowed();
@endphp

@if($metaEnabled && $pixelEnabled && $pixelId !== '' && $hasConsent)
    <script>
        (function (w, d) {
            if (w.__cwMetaPixelInitialized) {
                return;
            }

            !function(f,b,e,v,n,t,s)
            {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
            n.callMethod.apply(n,arguments):n.queue.push(arguments)};
            if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
            n.queue=[];t=b.createElement(e);t.async=!0;
            t.src=v;s=b.getElementsByTagName(e)[0];
            s.parentNode.insertBefore(t,s)}(w, d,'script',
            'https://connect.facebook.net/en_US/fbevents.js');

            w.__cwMetaPixelInitialized = true;
            w.fbq('init', @json($pixelId));
            w.fbq('track', 'PageView');

            @if($testEventCode !== '')
            w.fbq('set', 'testEventCode', @json($testEventCode));
            @endif
        })(window, document);
    </script>
@elseif($metaEnabled && $pixelEnabled && $pixelId !== '')
    <!-- Meta Pixel configured but blocked until marketing consent is granted. -->
@endif
