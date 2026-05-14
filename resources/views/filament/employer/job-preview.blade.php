<div class="space-y-4 text-sm">
    <div>
        <h3 class="text-base font-semibold text-gray-900">{{ $job->title }}</h3>
        <p class="mt-1 text-gray-600">
            {{ $job->location_city }}
            @if($job->category)
                · {{ $job->category }}
            @endif
            @if($job->contract_type)
                · {{ \Illuminate\Support\Str::headline(str_replace(['-', '_'], ' ', $job->contract_type)) }}
            @endif
        </p>
    </div>

    <div class="rounded-lg bg-gray-50 p-3 text-gray-700 space-y-1">
        <div><strong>Status:</strong> {{ ucfirst($job->status) }}</div>
        <div><strong>Salary:</strong> {{ $job->formatted_salary }}</div>
        <div><strong>Experience:</strong> {{ $job->experience_level ? \Illuminate\Support\Str::headline($job->experience_level) : 'Not specified' }}</div>
        <div><strong>Open positions:</strong> {{ $job->positions_available ?: 'Not specified' }}</div>
        <div><strong>Accommodation:</strong> {{ $job->accommodation_provided ? 'Provided' : 'Not provided' }}</div>
        <div><strong>Visa support:</strong> {{ $job->visa_support ? 'Yes' : 'No' }}</div>
        <div><strong>Expires:</strong> {{ $job->expires_at?->format('M d, Y H:i') ?? 'Not set' }}</div>
    </div>

    <div class="prose prose-sm max-w-none text-gray-800">
        {!! $job->description !!}
    </div>
</div>
