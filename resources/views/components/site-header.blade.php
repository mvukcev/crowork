<header
    class="fixed top-3 left-0 right-0 z-50"
    x-data="{ scrolled: false, mobileOpen: false }"
    @scroll.window="scrolled = window.scrollY > 10"
>
    <div class="container-base">
        <div class="premium-header-shell px-3.5 md:px-5 py-2.5 md:py-3 transition-all duration-normal" :class="{ 'shadow-elevation-3': scrolled }">
            <nav class="flex items-center justify-between gap-3">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-2xl bg-white/80 border border-white/70 shadow-sm flex items-center justify-center transition-all duration-normal group-hover:translate-y-[-1px]">
                        <span class="text-primary font-extrabold text-lg">C</span>
                    </div>
                    <div class="leading-tight">
                        <span class="block text-base md:text-lg font-semibold text-text-primary">CroWork</span>
                        <span class="hidden md:block text-[11px] uppercase tracking-[0.11em] text-text-tertiary">Premium Work Platform</span>
                    </div>
                </a>

                <div class="hidden lg:flex items-center gap-1.5 rounded-2xl bg-white/46 border border-white/70 px-1.5 py-1">
                    <a href="{{ route('jobs.index') }}" class="px-4 py-2 rounded-xl text-body-sm font-medium text-text-secondary hover:text-text-primary hover:bg-white/78 transition-all duration-normal" @class(['!bg-white !text-text-primary shadow-sm' => request()->routeIs('jobs.*')])>Jobs</a>
                    <a href="{{ route('educations.index') }}" class="px-4 py-2 rounded-xl text-body-sm font-medium text-text-secondary hover:text-text-primary hover:bg-white/78 transition-all duration-normal" @class(['!bg-white !text-text-primary shadow-sm' => request()->routeIs('educations.*')])>Educations</a>
                    <a href="{{ route('about') }}" class="px-4 py-2 rounded-xl text-body-sm font-medium text-text-secondary hover:text-text-primary hover:bg-white/78 transition-all duration-normal" @class(['!bg-white !text-text-primary shadow-sm' => request()->routeIs('about')])>About</a>
                    <a href="{{ route('for-employers') }}" class="px-4 py-2 rounded-xl text-body-sm font-medium text-text-secondary hover:text-text-primary hover:bg-white/78 transition-all duration-normal" @class(['!bg-white !text-text-primary shadow-sm' => request()->routeIs('for-employers')])>For Employers</a>
                </div>

                <div class="hidden md:flex items-center gap-2">
                    @auth
                        @if(auth()->user()->isAdmin() || auth()->user()->isMod())
                            <a href="{{ url('/admin') }}" class="px-3.5 py-2 rounded-xl text-body-sm font-medium text-text-secondary hover:text-text-primary hover:bg-white/75 transition-all duration-normal">Admin</a>
                        @endif
                        @if(auth()->user()->isEmployer())
                            <a href="{{ url('/employer') }}" class="px-3.5 py-2 rounded-xl text-body-sm font-medium text-text-secondary hover:text-text-primary hover:bg-white/75 transition-all duration-normal">Dashboard</a>
                        @endif
                        @if(auth()->user()->isWorker())
                            <a href="{{ route('worker.applications.index') }}" class="px-3.5 py-2 rounded-xl text-body-sm font-medium text-text-secondary hover:text-text-primary hover:bg-white/75 transition-all duration-normal">Dashboard</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button class="px-3.5 py-2 rounded-xl text-body-sm font-medium text-text-secondary hover:text-text-primary hover:bg-white/75 transition-all duration-normal">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="px-3.5 py-2 rounded-xl text-body-sm font-medium text-text-secondary hover:text-text-primary hover:bg-white/75 transition-all duration-normal">Sign in</a>
                        <a href="{{ route('register') }}" class="px-5 py-2.5 rounded-xl text-body-sm font-semibold bg-primary text-white hover:text-white hover:bg-primary-hover transition-all duration-normal shadow-sm">Get started</a>
                    @endauth
                </div>

                <button
                    type="button"
                    class="md:hidden inline-flex items-center justify-center w-10 h-10 rounded-xl bg-white/70 border border-white/80 text-text-primary"
                    @click="mobileOpen = !mobileOpen"
                    aria-label="Toggle navigation"
                >
                    <svg x-show="!mobileOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                    <svg x-show="mobileOpen" x-cloak class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </nav>

            <div x-show="mobileOpen" x-transition.opacity.duration.200ms x-cloak class="md:hidden mt-3 border-t border-white/70 pt-3">
                <div class="grid grid-cols-2 gap-2">
                    <a href="{{ route('jobs.index') }}" class="px-3 py-2 rounded-xl bg-white/68 text-body-sm font-medium text-text-primary">Jobs</a>
                    <a href="{{ route('educations.index') }}" class="px-3 py-2 rounded-xl bg-white/68 text-body-sm font-medium text-text-primary">Educations</a>
                    <a href="{{ route('about') }}" class="px-3 py-2 rounded-xl bg-white/68 text-body-sm font-medium text-text-primary">About</a>
                    <a href="{{ route('for-employers') }}" class="px-3 py-2 rounded-xl bg-white/68 text-body-sm font-medium text-text-primary">Employers</a>
                </div>

                <div class="mt-3 flex items-center gap-2">
                    @auth
                        @if(auth()->user()->isWorker())
                            <a href="{{ route('worker.applications.index') }}" class="flex-1 px-3 py-2 rounded-xl bg-white/68 text-center text-body-sm font-medium text-text-primary">Dashboard</a>
                        @endif
                        <form method="POST" action="{{ route('logout') }}" class="flex-1">
                            @csrf
                            <button class="w-full px-3 py-2 rounded-xl bg-white/68 text-body-sm font-medium text-text-primary">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="flex-1 px-3 py-2 rounded-xl bg-white/68 text-center text-body-sm font-medium text-text-primary">Sign in</a>
                        <a href="{{ route('register') }}" class="flex-1 px-3 py-2 rounded-xl bg-primary text-center text-body-sm font-semibold text-white">Get started</a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</header>
