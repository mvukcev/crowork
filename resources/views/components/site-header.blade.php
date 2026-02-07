<!-- Glass Header with Scroll Detection -->
<header class="fixed top-0 left-0 right-0 z-50 transition-all duration-normal"
        x-data="{ scrolled: false }"
        @scroll.window="scrolled = window.scrollY > 8"
        :class="{
            'glass-header shadow-sm': scrolled,
            'bg-transparent': !scrolled
        }">
    
    <nav class="container-base py-4 flex items-center justify-between">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex items-center space-x-2 group">
            <div class="w-9 h-9 bg-primary rounded-xl flex items-center justify-center transition-all duration-normal group-hover:scale-105 shadow-sm">
                <span class="text-white font-bold text-lg">C</span>
            </div>
            <span class="font-semibold text-lg text-text-primary group-hover:text-primary transition-colors duration-normal">
                CroWork
            </span>
        </a>
        
        <!-- Nav Links -->
        <div class="hidden md:flex items-center space-x-1">
            <a href="{{ route('jobs.index') }}" 
               class="px-4 py-2 rounded-lg text-body text-text-secondary hover:text-text-primary hover:bg-control-fill transition-all duration-normal"
               @class(['!text-primary !bg-primary/5' => request()->routeIs('jobs.*')])>
                Jobs
            </a>
            <a href="{{ route('educations.index') }}" 
               class="px-4 py-2 rounded-lg text-body text-text-secondary hover:text-text-primary hover:bg-control-fill transition-all duration-normal"
               @class(['!text-primary !bg-primary/5' => request()->routeIs('educations.*')])>
                Educations
            </a>
            <a href="{{ route('about') }}" 
               class="px-4 py-2 rounded-lg text-body text-text-secondary hover:text-text-primary hover:bg-control-fill transition-all duration-normal"
               @class(['!text-primary !bg-primary/5' => request()->routeIs('about')])>
                About
            </a>
            <a href="{{ route('for-employers') }}" 
               class="ml-2 px-5 py-2.5 rounded-xl text-body font-medium border-2 border-border/60 text-text-secondary hover:text-text-primary hover:bg-control-fill/50 hover:border-border transition-all duration-normal"
               @class(['!text-primary !border-primary/60 !bg-primary/5 hover:!border-primary' => request()->routeIs('for-employers')])>
                For Employers
            </a>
        </div>
        
        <!-- Auth Actions -->
        <div class="flex items-center space-x-3">
            @auth
                @if(auth()->user()->isAdmin() || auth()->user()->isMod())
                    <a href="{{ url('/admin') }}" class="px-4 py-2 rounded-lg text-body text-text-secondary hover:text-text-primary hover:bg-control-fill transition-all duration-normal">
                        Admin
                    </a>
                @endif
                @if(auth()->user()->isEmployer())
                    <a href="{{ url('/employer') }}" class="px-4 py-2 rounded-lg text-body text-text-secondary hover:text-text-primary hover:bg-control-fill transition-all duration-normal">
                        Dashboard
                    </a>
                @endif
                @if(auth()->user()->isWorker())
                    <a href="{{ route('worker.dashboard') }}" class="px-4 py-2 rounded-lg text-body text-text-secondary hover:text-text-primary hover:bg-control-fill transition-all duration-normal">
                        Dashboard
                    </a>
                @endif
                <span class="text-body text-text-secondary px-2">{{ auth()->user()->name }}</span>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button class="px-4 py-2 rounded-lg text-body text-text-secondary hover:text-text-primary hover:bg-control-fill transition-all duration-normal">
                        Logout
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="px-4 py-2 rounded-lg text-body text-text-secondary hover:text-text-primary hover:bg-control-fill transition-all duration-normal">
                    Sign in
                </a>
                <a href="{{ route('register') }}" class="px-6 py-2.5 rounded-full text-body font-semibold bg-primary text-white hover:bg-primary-hover transition-all duration-normal shadow-sm hover:shadow-md active:scale-[0.98]">
                    Get started
                </a>
            @endauth
        </div>
    </nav>
</header>
