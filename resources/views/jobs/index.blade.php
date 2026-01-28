<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Browse all job opportunities in Croatia. Filter by location, job type, and more.">
    <meta name="keywords" content="jobs, croatia, employment, career, search jobs">
    <title>Browse Jobs - CroWork</title>
    
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50">
    <!-- Header -->
    <header class="bg-white shadow-sm">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex justify-between items-center">
                <div class="text-2xl font-bold text-indigo-600">
                    <a href="{{ route('home') }}">CroWork</a>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('jobs.index') }}" class="text-indigo-600 font-semibold">Browse Jobs</a>
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Browse Jobs</h1>

        <!-- Filters -->
        <div class="bg-white rounded-lg shadow-sm p-6 mb-8">
            <form action="{{ route('jobs.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" name="search" id="search" value="{{ request('search') }}" 
                           placeholder="Job title or company" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" name="location" id="location" value="{{ request('location') }}" 
                           placeholder="e.g., Zagreb" 
                           class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>
                <div>
                    <label for="job_type" class="block text-sm font-medium text-gray-700 mb-1">Job Type</label>
                    <select name="job_type" id="job_type" 
                            class="w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">All Types</option>
                        <option value="full-time" {{ request('job_type') == 'full-time' ? 'selected' : '' }}>Full-time</option>
                        <option value="part-time" {{ request('job_type') == 'part-time' ? 'selected' : '' }}>Part-time</option>
                        <option value="contract" {{ request('job_type') == 'contract' ? 'selected' : '' }}>Contract</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Filter Jobs
                    </button>
                </div>
            </form>
        </div>

        <!-- Jobs List -->
        @if($jobs->count() > 0)
            <div class="space-y-4">
                @foreach($jobs as $job)
                    <div class="bg-white rounded-lg shadow-sm p-6 hover:shadow-md transition">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h2 class="text-2xl font-semibold text-gray-900 mb-2">
                                    <a href="{{ route('jobs.show', $job->slug) }}" class="hover:text-indigo-600">
                                        {{ $job->title }}
                                    </a>
                                </h2>
                                <p class="text-gray-600 mb-2">{{ $job->company_name }}</p>
                                <div class="flex gap-4 text-sm text-gray-500 mb-3">
                                    <span>📍 {{ $job->location }}</span>
                                    @if($job->job_type)
                                        <span>💼 {{ ucfirst($job->job_type) }}</span>
                                    @endif
                                </div>
                                <p class="text-gray-700 line-clamp-2">{{ Str::limit($job->description, 200) }}</p>
                            </div>
                            <div class="ml-4 text-right">
                                @if($job->salary_min && $job->salary_max)
                                    <p class="text-indigo-600 font-semibold text-lg">
                                        €{{ number_format($job->salary_min) }} - €{{ number_format($job->salary_max) }}
                                    </p>
                                @endif
                                <p class="text-xs text-gray-500 mt-2">{{ $job->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-8">
                {{ $jobs->links() }}
            </div>
        @else
            <div class="bg-white rounded-lg shadow-sm p-12 text-center">
                <p class="text-gray-600 text-lg">No jobs found matching your criteria. Try adjusting your filters.</p>
            </div>
        @endif
    </div>

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
