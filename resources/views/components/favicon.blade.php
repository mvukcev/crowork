@php
    $faviconPng = cw_asset('assets/CW-Favicon.png');
@endphp

<link rel="icon" type="image/png" sizes="32x32" href="{{ $faviconPng }}">
<link rel="icon" type="image/png" sizes="192x192" href="{{ $faviconPng }}">
<link rel="shortcut icon" type="image/png" href="{{ $faviconPng }}">
<link rel="apple-touch-icon" href="{{ $faviconPng }}">
<meta name="theme-color" content="#fe5000">
