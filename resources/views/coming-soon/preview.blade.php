<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CroWork - Coming Soon</title>
    <meta name="description" content="CroWork is getting ready. Private preview access is available for approved partners.">
    <link rel="icon" type="image/svg+xml" href="{{ cw_asset('assets/branding/CW-Favicon.svg') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ cw_asset('assets/branding/CW-Favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ cw_asset('assets/branding/CW-Favicon.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="h-full cw-page cw-brand-display antialiased overflow-x-hidden">
    <div class="min-h-screen flex flex-col cw-page-shell cw-preview-shell">
        <div class="cw-page-ambient cw-organic-bg" aria-hidden="true">
            <span class="cw-orb cw-orb-blue" style="width: 360px; height: 360px; top: -120px; right: -110px;"></span>
            <span class="cw-orb cw-orb-orange" style="width: 300px; height: 300px; top: 16%; left: -120px;"></span>
            <span class="cw-orb cw-orb-cyan" style="width: 260px; height: 260px; bottom: -90px; left: 16%;"></span>
        </div>

        <main class="flex-1 flex items-center justify-center px-4 py-12 sm:px-6 sm:py-16 md:py-20">
            <div class="w-full max-w-2xl cw-preview-card cw-soft-reveal">
                <div class="cw-preview-header">
                    <p class="cw-preview-eyebrow">Private preview</p>
                </div>

                <h1 class="cw-preview-title">CroWork is preparing its public launch.</h1>
                <p class="cw-preview-copy">Approved partners can access the cinematic preview environment using credentials provided by the CroWork team.</p>

                @if($errors->has('preview'))
                    <div class="cw-preview-error">
                        {{ $errors->first('preview') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('coming-soon-preview.login') }}" class="cw-preview-form">
                    @csrf

                    <div class="cw-preview-field-group">
                        <label for="username" class="cw-label">Preview username</label>
                        <input
                            id="username"
                            name="username"
                            type="text"
                            required
                            value="{{ old('username') }}"
                            autocomplete="username"
                            class="cw-input cw-preview-input"
                            placeholder="Enter username"
                        >
                    </div>

                    <div class="cw-preview-field-group">
                        <label for="password" class="cw-label">Preview password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            class="cw-input cw-preview-input"
                            placeholder="Enter password"
                        >
                    </div>

                    <button type="submit" class="w-full cw-button-primary cw-preview-cta">Enter preview</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
