<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ $title ?? 'Authentication' }} – {{ config('app.name', 'CroWork') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full antialiased overflow-x-hidden">
        <!-- Dynamic Fluent Gradient Background -->
        <div class="fixed inset-0 -z-10 overflow-hidden" style="background: linear-gradient(135deg, #174EA6 0%, #346AF0 34%, #8B5CF6 66%, #00B294 100%);">
            <div class="absolute inset-0 opacity-90" style="background: radial-gradient(circle at 16% 18%, rgba(255, 255, 255, 0.30) 0%, transparent 28%), radial-gradient(circle at 84% 22%, rgba(255, 185, 0, 0.22) 0%, transparent 30%), radial-gradient(circle at 50% 82%, rgba(16, 185, 129, 0.30) 0%, transparent 34%); animation: gradientShift 18s ease-in-out infinite alternate;"></div>
            <div class="auth-gradient-orb top-10 left-[8%] w-72 h-72 bg-white/25"></div>
            <div class="auth-gradient-orb top-[20%] right-[10%] w-96 h-96 bg-fuchsia-300/30" style="animation-delay: 2s;"></div>
            <div class="auth-gradient-orb bottom-[8%] left-[22%] w-80 h-80 bg-emerald-200/30" style="animation-delay: 4s;"></div>
            <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.08)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.08)_1px,transparent_1px)] bg-[size:44px_44px] opacity-25"></div>
        </div>
        
        <div class="min-h-full flex flex-col relative z-10">
            {{-- Header with Logo - Acrylic style --}}
            <header class="backdrop-blur-2xl bg-white/10 border-b border-white/20 shadow-sm">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                    <a href="{{ url('/') }}" class="flex items-center space-x-2 group w-fit">
                        <div class="w-9 h-9 bg-white/90 backdrop-blur-sm rounded-xl flex items-center justify-center group-hover:bg-white transition-all duration-normal shadow-sm">
                            <span class="text-primary font-bold text-lg">C</span>
                        </div>
                        <span class="text-lg font-semibold text-white drop-shadow-sm">CroWork</span>
                    </a>
                </div>
            </header>

            {{-- Main Content --}}
            <main class="flex-1 flex items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
                <div class="w-full max-w-md">
                    {{ $slot }}
                </div>
            </main>

            {{-- Footer --}}
            <footer class="backdrop-blur-md bg-white/10 border-t border-white/20 mt-auto">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-sm text-white/90">
                        <p>&copy; {{ date('Y') }} CroWork. All rights reserved.</p>
                        <div class="flex items-center gap-6">
                            <a href="{{ url('/about') }}" class="hover:text-white transition-colors">About</a>
                            <a href="{{ url('/contact') }}" class="hover:text-white transition-colors">Contact</a>
                            <a href="{{ url('/privacy') }}" class="hover:text-white transition-colors">Privacy</a>
                            <a href="{{ url('/terms') }}" class="hover:text-white transition-colors">Terms</a>
                        </div>
                    </div>
                </div>
            </footer>
        </div>
        
        <style>
            @keyframes gradientShift {
                0%, 100% { opacity: 0.6; transform: scale(1); }
                50% { opacity: 0.8; transform: scale(1.1); }
            }
            @keyframes float1 {
                0%, 100% { transform: translate(0, 0); }
                50% { transform: translate(-30px, -30px); }
            }
            @keyframes float2 {
                0%, 100% { transform: translate(0, 0); }
                50% { transform: translate(40px, -20px); }
            }
        </style>
    </body>
</html>
