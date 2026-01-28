<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="CroWork - Find your dream job in Croatia. Browse thousands of job opportunities from top employers.">
    <meta name="keywords" content="jobs, croatia, employment, career, work">
    <title>CroWork - Job Board for Croatia</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div class="text-2xl font-bold text-indigo-600">
                    <a href="{{ route('home') }}">CroWork</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('jobs.index') }}" class="text-gray-700 hover:text-indigo-600">Browse Jobs</a>
                    @auth
                        @if(auth()->user()->isEmployer())
                            <a href="{{ route('employer.jobs.index') }}" class="text-gray-700 hover:text-indigo-600">My Jobs</a>
                        @endif
                        <a href="{{ route('dashboard') }}" class="text-gray-700 hover:text-indigo-600">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-gray-700 hover:text-indigo-600">Login</a>
                        <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Register</a>
                    @endauth
                </div>
            </div>
        </nav>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24">
            <div class="text-center">
                <h1 class="text-5xl font-bold mb-6">Find Your Dream Job in Croatia</h1>
                <p class="text-xl mb-8">Connect with top employers and discover amazing opportunities</p>
                <div class="max-w-2xl mx-auto">
                    <form action="{{ route('jobs.index') }}" method="GET" class="flex gap-2">
                        <input type="text" name="search" placeholder="Job title, keywords, or company" 
                               class="flex-1 px-4 py-3 rounded-lg text-gray-900" value="{{ request('search') }}">
                        <button type="submit" class="bg-white text-indigo-600 px-8 py-3 rounded-lg font-semibold hover:bg-gray-100">
                            Search Jobs
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Jobs -->
    <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <h2 class="text-3xl font-bold text-gray-900 mb-8">Featured Jobs</h2>
        
        @if($featuredJobs->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($featuredJobs as $job)
                    <div class="bg-white border border-gray-200 rounded-lg p-6 hover:shadow-lg transition">
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">
                            <a href="{{ route('jobs.show', $job->slug) }}" class="hover:text-indigo-600">
                                {{ $job->title }}
                            </a>
                        </h3>
                        <p class="text-gray-600 mb-2">{{ $job->company_name }}</p>
                        <p class="text-gray-500 text-sm mb-4">{{ $job->location }}</p>
                        <div class="flex justify-between items-center">
                            @if($job->salary_min && $job->salary_max)
                                <span class="text-indigo-600 font-semibold">
                                    €{{ number_format($job->salary_min) }} - €{{ number_format($job->salary_max) }}
                                </span>
                            @endif
                            <span class="text-xs text-gray-500">{{ $job->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <div class="text-center mt-12">
                <a href="{{ route('jobs.index') }}" class="inline-block bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700">
                    View All Jobs
                </a>
            </div>
        @else
            <p class="text-gray-600 text-center py-12">No jobs available at the moment. Check back soon!</p>
        @endif
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white mt-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <p>&copy; {{ date('Y') }} CroWork. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>
