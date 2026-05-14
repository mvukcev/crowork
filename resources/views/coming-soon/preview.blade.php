<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CroWork - Coming Soon</title>
    <meta name="description" content="CroWork is getting ready. Private preview access is available for approved partners.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

</head>
<body class="h-full cw-page antialiased overflow-x-hidden">
    <div class="cw-orb cw-orb-blue" style="width: 290px; height: 290px; top: -80px; right: -60px;"></div>
    <div class="cw-orb cw-orb-cyan" style="width: 250px; height: 250px; bottom: -90px; left: -50px;"></div>

    <div class="relative z-10 min-h-screen flex flex-col">
        <header class="pt-5 px-4 sm:px-6">
            <div class="cw-container flex justify-end">
                <a href="{{ route('login') }}" class="cw-button-secondary">Admin / Mod Sign in</a>
            </div>
        </header>

        <main class="flex-1 flex items-center justify-center px-4 py-8 sm:px-6">
            <div class="w-full max-w-xl cw-product-window p-7 sm:p-9 cw-soft-reveal">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-11 h-11 rounded-xl bg-white border border-slate-200 flex items-center justify-center">
                        <span class="text-slate-900 font-semibold text-xl">C</span>
                    </div>
                    <div>
                        <p class="text-lg font-semibold text-slate-900 mb-0">CroWork</p>
                        <p class="text-[11px] uppercase tracking-[0.08em] text-slate-500 mb-0">Private preview</p>
                    </div>
                </div>

                <h1 class="cw-display text-3xl md:text-4xl text-slate-900 leading-tight mb-3">CroWork is preparing its public launch.</h1>
                <p class="text-sm md:text-base text-slate-600 mb-6">Approved partners can access the cinematic preview environment using credentials provided by the CroWork team.</p>

                @if($errors->has('preview'))
                    <div class="mb-4 p-3 rounded-xl border border-red-300 bg-red-50 text-sm text-red-700">
                        {{ $errors->first('preview') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('coming-soon-preview.login') }}" class="space-y-4">
                    @csrf

                    <div>
                        <label for="username" class="block text-sm font-semibold text-slate-900 mb-1.5">Preview username</label>
                        <input
                            id="username"
                            name="username"
                            type="text"
                            required
                            value="{{ old('username') }}"
                            autocomplete="username"
                            class="cw-input"
                            placeholder="Enter username"
                        >
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-900 mb-1.5">Preview password</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            required
                            autocomplete="current-password"
                            class="cw-input"
                            placeholder="Enter password"
                        >
                    </div>

                    <button type="submit" class="w-full cw-button-primary py-3">Enter preview</button>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
