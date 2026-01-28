<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Job Applications') }}: {{ $job->title }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="mb-6">
                <a href="{{ route('employer.jobs.index') }}" class="text-indigo-600 hover:text-indigo-900">
                    ← Back to My Jobs
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <h3 class="text-2xl font-semibold mb-2">{{ $job->title }}</h3>
                    <p class="text-gray-600">{{ $job->company_name }} • {{ $job->location }}</p>
                    <p class="text-sm text-gray-500 mt-2">Posted {{ $job->created_at->diffForHumans() }}</p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-4">Applications ({{ $applications->count() }})</h3>

                    @if($applications->count() > 0)
                        <div class="space-y-4">
                            @foreach($applications as $application)
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <h4 class="text-lg font-semibold">{{ $application->worker->name }}</h4>
                                            <p class="text-gray-600">{{ $application->worker->email }}</p>
                                            <p class="text-sm text-gray-500 mt-2">Applied {{ $application->created_at->diffForHumans() }}</p>
                                            
                                            @if($application->cover_letter)
                                                <div class="mt-4">
                                                    <p class="text-sm font-medium text-gray-700 mb-1">Cover Letter:</p>
                                                    <p class="text-gray-700">{{ $application->cover_letter }}</p>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <span class="px-3 py-1 text-sm rounded-full {{ 
                                                $application->status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                                                ($application->status == 'reviewed' ? 'bg-blue-100 text-blue-800' :
                                                ($application->status == 'accepted' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'))
                                            }}">
                                                {{ ucfirst($application->status) }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-600 text-center py-8">No applications yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
