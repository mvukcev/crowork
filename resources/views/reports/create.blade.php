<x-app-layout>
    <x-slot name="title">Report Listing</x-slot>
    <x-slot name="description">Submit a report for unsafe or misleading content.</x-slot>

    <div class="section-spacing-tight bg-background min-h-screen">
        <div class="container-base max-w-2xl">
            <div class="mb-6">
                <h1 class="text-title-1 font-semibold text-text-primary mb-2">Report Listing</h1>
                <p class="text-body text-text-secondary mb-0">
                    Help us keep CroWork safe. This report is reviewed by our moderation team.
                </p>
            </div>

            <x-card class="border border-border/70 shadow-elevation-1">
                <div class="mb-5 p-4 rounded-xl bg-warning-50 border border-warning-200">
                    <p class="text-body-sm text-warning-900 mb-0">
                        <strong>Reporting:</strong> {{ $targetTitle }}
                    </p>
                </div>

                <form method="POST" action="{{ route('reports.store') }}" class="space-y-5">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="id" value="{{ $targetId }}">

                    <div>
                        <label for="reason" class="block text-body-sm font-semibold text-text-primary mb-2">Reason</label>
                        <select id="reason" name="reason" required class="w-full rounded-xl border border-border bg-white px-4 py-3 text-body text-text-primary focus:outline-none focus:ring-2 focus:ring-primary/30">
                            <option value="">Select a reason</option>
                            <option value="spam" @selected(old('reason') === 'spam')>Spam</option>
                            <option value="scam" @selected(old('reason') === 'scam')>Scam / Fraud</option>
                            <option value="fake" @selected(old('reason') === 'fake')>Fake Listing</option>
                            <option value="misleading" @selected(old('reason') === 'misleading')>Misleading Information</option>
                            <option value="inappropriate" @selected(old('reason') === 'inappropriate')>Inappropriate Content</option>
                            <option value="other" @selected(old('reason') === 'other')>Other</option>
                        </select>
                        @error('reason')
                            <p class="mt-2 text-body-sm text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="message" class="block text-body-sm font-semibold text-text-primary mb-2">
                            Additional Details <span class="font-normal text-text-tertiary">(Optional)</span>
                        </label>
                        <textarea id="message" name="message" rows="5" maxlength="2000" class="w-full rounded-xl border border-border bg-white px-4 py-3 text-body text-text-primary focus:outline-none focus:ring-2 focus:ring-primary/30" placeholder="Share details to help our moderation team.">{{ old('message') }}</textarea>
                        @error('message')
                            <p class="mt-2 text-body-sm text-danger">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3">
                        <x-button type="submit" variant="primary" class="sm:w-auto w-full">Submit Report</x-button>
                        <x-button href="{{ url()->previous() }}" variant="outline" class="sm:w-auto w-full">Cancel</x-button>
                    </div>
                </form>
            </x-card>
        </div>
    </div>
</x-app-layout>
