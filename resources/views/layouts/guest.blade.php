<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Authentication' }} – {{ config('app.name', 'CroWork') }}</title>

        {{--
            Override the global #FAFAFA base background before Vite assets load,
            so the html/body elements never flash white behind the gradient.
        --}}
        <style>
            html, body { background-color: #eef3ff !important; }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <style>
            /* ── Auth background keyframes ─────────────────────────────── */

            @keyframes authGradientDrift {
                0%   { background-position: 0% 0%; }
                50%  { background-position: 100% 100%; }
                100% { background-position: 0% 0%; }
            }

            @keyframes authOverlayPulse {
                0%   { opacity: 0.42; }
                50%  { opacity: 0.56; }
                100% { opacity: 0.42; }
            }

            /* Each orb drifts independently */
            @keyframes authOrb1 {
                0%   { transform: translate3d(0,    0,    0) scale(1.00); }
                50%  { transform: translate3d(28px, -22px, 0) scale(1.10); }
                100% { transform: translate3d(0,    0,    0) scale(1.00); }
            }
            @keyframes authOrb2 {
                0%   { transform: translate3d(0,    0,    0) scale(1.00); }
                50%  { transform: translate3d(-34px, 20px, 0) scale(1.07); }
                100% { transform: translate3d(0,    0,    0) scale(1.00); }
            }
            @keyframes authOrb3 {
                0%   { transform: translate3d(0,    0,    0) scale(1.00); }
                50%  { transform: translate3d(18px, 26px, 0) scale(1.09); }
                100% { transform: translate3d(0,    0,    0) scale(1.00); }
            }
            @keyframes authOrb4 {
                0%   { transform: translate3d(0,    0,    0) scale(1.00); }
                50%  { transform: translate3d(-22px, -18px, 0) scale(1.06); }
                100% { transform: translate3d(0,    0,    0) scale(1.00); }
            }

            @media (prefers-reduced-motion: reduce) {
                .auth-page-bg,
                .auth-bg-overlay,
                .auth-orb { animation: none !important; }
            }
        </style>
    </head>
    <body class="h-full antialiased overflow-x-hidden">

           <div class="auth-page-bg fixed inset-0 -z-10 min-h-screen overflow-hidden pointer-events-none"
               style="background: linear-gradient(142deg,
                        #edf3ff  0%,
                        #dde8ff 24%,
                        #e8e2ff 48%,
                        #dff3f6 72%,
                        #eef6ff 100%);
                    background-size: 170% 170%;
                    animation: authGradientDrift 42s ease-in-out infinite;">

            <div class="auth-bg-overlay absolute inset-0"
                 style="background:
                     radial-gradient(ellipse 74% 62% at 12% 10%, rgba(88, 124, 255, 0.18) 0%, transparent 58%),
                     radial-gradient(ellipse 56% 44% at 88% 14%, rgba(139, 92, 246, 0.16) 0%, transparent 56%),
                     radial-gradient(ellipse 62% 58% at 52% 92%, rgba(45, 212, 191, 0.16) 0%, transparent 56%),
                     radial-gradient(ellipse 44% 40% at 90% 76%, rgba(244, 114, 182, 0.12) 0%, transparent 58%);
                     animation: authOverlayPulse 20s ease-in-out infinite;">
            </div>

            <div class="auth-orb absolute rounded-full"
                 style="width:380px; height:380px; top:-60px; left:-40px;
                        background: radial-gradient(circle, rgba(255,255,255,0.22) 0%, rgba(96,165,250,0.14) 62%, transparent 100%);
                        filter: blur(68px); opacity: 0.48;
                        animation: authOrb1 28s ease-in-out infinite;"></div>

            <div class="auth-orb absolute rounded-full"
                 style="width:480px; height:480px; top:-80px; right:-80px;
                        background: radial-gradient(circle, rgba(168,85,247,0.24) 0%, rgba(139,92,246,0.14) 62%, transparent 100%);
                        filter: blur(84px); opacity: 0.42;
                        animation: authOrb2 34s ease-in-out infinite; animation-delay: -8s;"></div>

            <div class="auth-orb absolute rounded-full"
                 style="width:420px; height:420px; bottom:-80px; left:28%;
                        background: radial-gradient(circle, rgba(45,212,191,0.22) 0%, rgba(20,184,166,0.12) 62%, transparent 100%);
                        filter: blur(76px); opacity: 0.40;
                        animation: authOrb3 30s ease-in-out infinite; animation-delay: -12s;"></div>

            <div class="auth-orb absolute rounded-full"
                 style="width:340px; height:340px; bottom:5%; right:6%;
                        background: radial-gradient(circle, rgba(251,113,133,0.20) 0%, rgba(244,114,182,0.10) 62%, transparent 100%);
                        filter: blur(64px); opacity: 0.34;
                        animation: authOrb4 36s ease-in-out infinite; animation-delay: -16s;"></div>

            <div class="absolute inset-0 opacity-[0.045]"
                 style="background-image: radial-gradient(circle, rgba(255,255,255,0.8) 1px, transparent 1px);
                        background-size: 42px 42px;">
            </div>
        </div>

        <div class="min-h-screen flex flex-col relative z-10">

            <header class="backdrop-blur-2xl bg-white/40 border-b border-white/45 shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <a href="{{ url('/') }}" class="flex items-center space-x-2 group w-fit">
                        <div class="w-10 h-10 bg-white/92 backdrop-blur-sm rounded-2xl flex items-center justify-center group-hover:bg-white transition-all duration-normal shadow-sm border border-white/70">
                            <span class="text-primary font-bold text-lg">C</span>
                        </div>
                        <div>
                            <span class="block text-lg font-semibold text-text-primary">CroWork</span>
                            <span class="block text-[11px] uppercase tracking-[0.1em] text-text-tertiary">Account Access</span>
                        </div>
                    </a>
                </div>
            </header>

            <main class="flex-1 flex items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </main>

            <footer class="backdrop-blur-sm bg-white/8 border-t border-white/20">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-text-primary/80">
                        <p class="m-0">&copy; {{ date('Y') }} CroWork. All rights reserved.</p>
                        <div class="flex items-center gap-6">
                            <a href="{{ url('/about') }}"   class="text-text-primary/80 hover:text-text-primary transition-colors">About</a>
                            <a href="{{ url('/contact') }}" class="text-text-primary/80 hover:text-text-primary transition-colors">Contact</a>
                            <a href="{{ url('/privacy') }}" class="text-text-primary/80 hover:text-text-primary transition-colors">Privacy</a>
                            <a href="{{ url('/terms') }}"   class="text-text-primary/80 hover:text-text-primary transition-colors">Terms</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>

    </body>
</html>
