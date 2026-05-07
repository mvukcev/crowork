<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>CroWork - Coming Soon</title>
    <meta name="description" content="CroWork is getting ready. Private preview access is available for approved partners.">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @keyframes previewMeshDrift {
            0% { background-position: 0% 0%; }
            50% { background-position: 100% 100%; }
            100% { background-position: 0% 0%; }
        }

        @keyframes previewOrbFloatA {
            0% { transform: translate3d(0, 0, 0) scale(1); }
            100% { transform: translate3d(20px, -24px, 0) scale(1.06); }
        }

        @keyframes previewOrbFloatB {
            0% { transform: translate3d(0, 0, 0) scale(1); }
            100% { transform: translate3d(-26px, 14px, 0) scale(1.08); }
        }

        .preview-bg {
            background:
                radial-gradient(circle at 14% 20%, rgba(103, 145, 255, 0.32), transparent 34%),
                radial-gradient(circle at 86% 15%, rgba(140, 116, 247, 0.28), transparent 34%),
                radial-gradient(circle at 52% 88%, rgba(41, 186, 170, 0.2), transparent 36%),
                radial-gradient(circle at 78% 70%, rgba(240, 140, 184, 0.16), transparent 38%),
                linear-gradient(160deg, #eaf1ff 0%, #edf2ff 40%, #f6fbff 72%, #ffffff 100%);
            background-size: 160% 160%;
            animation: previewMeshDrift 24s ease-in-out infinite alternate;
        }

        .preview-orb-a {
            animation: previewOrbFloatA 22s ease-in-out infinite alternate;
        }

        .preview-orb-b {
            animation: previewOrbFloatB 26s ease-in-out infinite alternate;
        }

        @media (prefers-reduced-motion: reduce) {
            .preview-bg,
            .preview-orb-a,
            .preview-orb-b {
                animation: none !important;
            }
        }
    </style>
</head>
<body class="h-full antialiased">
    <div class="min-h-screen preview-bg relative overflow-hidden">
        <div class="absolute top-[-8rem] right-[-5rem] w-80 h-80 rounded-full blur-[88px] opacity-70 preview-orb-a" style="background: rgba(126, 154, 255, 0.35);"></div>
        <div class="absolute bottom-[-7rem] left-[-4rem] w-72 h-72 rounded-full blur-[80px] opacity-65 preview-orb-b" style="background: rgba(57, 195, 176, 0.24);"></div>

        <div class="relative z-10 min-h-screen flex flex-col">
            <header class="pt-5 px-4 sm:px-6">
                <div class="container-base flex justify-end">
                    <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 rounded-xl premium-glass text-body-sm font-semibold text-text-primary hover:text-primary transition-colors duration-normal">
                        Admin / Mod Sign in
                    </a>
                </div>
            </header>

            <main class="flex-1 flex items-center justify-center px-4 py-8 sm:px-6">
                <div class="w-full max-w-lg premium-glass rounded-[1.8rem] p-7 sm:p-9 border border-white/80 shadow-elevation-3">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-white/90 border border-white/90 shadow-sm flex items-center justify-center">
                            <span class="text-primary font-extrabold text-2xl">C</span>
                        </div>
                        <div>
                            <p class="text-title-2 font-semibold text-text-primary mb-0">CroWork</p>
                            <p class="text-caption uppercase tracking-[0.11em] text-text-tertiary mb-0">Private Preview</p>
                        </div>
                    </div>

                    <h1 class="text-3xl sm:text-4xl font-semibold text-text-primary tracking-[-0.03em] leading-tight mb-3">
                        CroWork is getting ready.
                    </h1>
                    <p class="text-body text-text-secondary mb-6 max-w-none">
                        We are preparing the next release for public launch. Approved partners can enter the private preview using the credentials provided by the CroWork team.
                    </p>

                    @if($errors->has('preview'))
                        <div class="mb-4 p-3 rounded-xl border border-danger/30 bg-danger/10 text-body-sm text-danger">
                            {{ $errors->first('preview') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('coming-soon-preview.login') }}" class="space-y-4">
                        @csrf

                        <div>
                            <label for="username" class="block text-body-sm font-semibold text-text-primary mb-1.5">Preview Username</label>
                            <input
                                id="username"
                                name="username"
                                type="text"
                                required
                                value="{{ old('username') }}"
                                autocomplete="username"
                                class="w-full rounded-xl border border-border bg-white/90 px-4 py-3 text-body text-text-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                                placeholder="Enter username"
                            >
                        </div>

                        <div>
                            <label for="password" class="block text-body-sm font-semibold text-text-primary mb-1.5">Preview Password</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                class="w-full rounded-xl border border-border bg-white/90 px-4 py-3 text-body text-text-primary focus:outline-none focus:ring-2 focus:ring-primary/30"
                                placeholder="Enter password"
                            >
                        </div>

                        <button type="submit" class="w-full rounded-xl bg-primary px-5 py-3 text-body font-semibold text-white hover:bg-primary-hover transition-colors duration-normal">
                            Enter Preview
                        </button>
                    </form>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
