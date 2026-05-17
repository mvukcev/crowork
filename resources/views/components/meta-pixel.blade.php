@php
    $metaEnabled = (bool) config('meta.enabled', false);
    $pixelEnabled = (bool) config('meta.browser_enabled', true);
    $pixelId = (string) config('meta.pixel_id', '');
    $testEventCode = (string) config('meta.test_event_code', '');
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
@endif
