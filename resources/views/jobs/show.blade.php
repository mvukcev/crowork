<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ Str::limit($job->description, 160) }}">
    <meta name="keywords" content="job, {{ $job->title }}, {{ $job->company_name }}, {{ $job->location }}, croatia">
    <title>{{ $job->title }} - {{ $job->company_name }} | CroWork</title>
    
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

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Back Button -->
        <div class="mb-6">
            <a href="{{ route('jobs.index') }}" class="text-indigo-600 hover:text-indigo-800">
                ← Back to Jobs
            </a>
        </div>

        <!-- Job Details -->
        <div class="bg-white rounded-lg shadow-sm p-8">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">{{ $job->title }}</h1>
            
            <div class="flex items-center gap-6 text-gray-600 mb-6">
                <span class="text-xl font-semibold">{{ $job->company_name }}</span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 py-6 border-y border-gray-200 mb-6">
                <div>
                    <p class="text-sm text-gray-500 mb-1">Location</p>
                    <p class="font-medium">📍 {{ $job->location }}</p>
                </div>
                @if($job->job_type)
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Job Type</p>
                        <p class="font-medium">💼 {{ ucfirst($job->job_type) }}</p>
                    </div>
                @endif
                @if($job->salary_min && $job->salary_max)
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Salary Range</p>
                        <p class="font-medium text-indigo-600">
                            €{{ number_format($job->salary_min) }} - €{{ number_format($job->salary_max) }}
                        </p>
                    </div>
                @endif
            </div>

            <div class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-900 mb-4">Job Description</h2>
                <div class="prose max-w-none text-gray-700">
                    {!! nl2br(e($job->description)) !!}
                </div>
            </div>

            <div class="flex justify-between items-center pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500">Posted {{ $job->created_at->diffForHumans() }}</p>
                
                @auth
                    @if(auth()->user()->isWorker())
                        <a href="#" class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700">
                            Apply Now
                        </a>
                    @endif
                @else
                    <a href="{{ route('register') }}" class="bg-indigo-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-indigo-700">
                        Register to Apply
                    </a>
                @endauth
            </div>
        </div>
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
