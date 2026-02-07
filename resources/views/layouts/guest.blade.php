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
        <!-- Dynamic Gradient Background -->
        <div class="fixed inset-0 -z-10" style="background: linear-gradient(135deg, #346AF0 0%, #8B5CF6 50%, #10B981 100%);">
            <!-- Animated gradient overlay -->
            <div class="absolute inset-0 opacity-60" style="background: radial-gradient(circle at 20% 50%, rgba(139, 92, 246, 0.4) 0%, transparent 50%), radial-gradient(circle at 80% 80%, rgba(16, 185, 129, 0.4) 0%, transparent 50%); animation: gradientShift 20s ease-in-out infinite alternate;"></div>
            <!-- Soft shapes -->
            <div class="absolute top-0 right-0 w-96 h-96 bg-white/10 rounded-full blur-3xl" style="animation: float1 25s ease-in-out infinite;"></div>
            <div class="absolute bottom-0 left-0 w-80 h-80 bg-white/10 rounded-full blur-3xl" style="animation: float2 30s ease-in-out infinite;"></div>
        </div>
        
        <div class="min-h-full flex flex-col relative z-10">
            {{-- Header with Logo - Acrylic style --}}
            <header class="backdrop-blur-md bg-white/10 border-b border-white/20">
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
            <main class="flex-1 flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8">
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
