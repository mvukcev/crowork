<x-app-layout>
    <x-slot name="title">Report Listing</x-slot>
    <x-slot name="description">Submit a report for unsafe or misleading content.</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-2xl">
            <div class="mb-6">
                <h1 class="cw-display text-4xl md:text-5xl mb-2">Report listing</h1>
                <p class="text-slate-600">Help us keep CroWork safe. Our moderation team reviews every report.</p>
            </div>

            <div class="cw-surface p-6 md:p-7">
                <div class="mb-5 p-4 rounded-xl bg-amber-50 border border-amber-200">
                    <p class="text-sm text-amber-800 mb-0"><strong>Reporting:</strong> {{ $targetTitle }}</p>
                </div>

                <form method="POST" action="{{ route('reports.store') }}" class="space-y-4">
                    @csrf
                    <input type="hidden" name="type" value="{{ $type }}">
                    <input type="hidden" name="id" value="{{ $targetId }}">

                    <div>
                        <label for="reason" class="cw-label">Reason</label>
                        <select id="reason" name="reason" required class="cw-field">
                            <option value="">Select a reason</option>
                            <option value="spam" @selected(old('reason') === 'spam')>Spam</option>
                            <option value="scam" @selected(old('reason') === 'scam')>Scam / Fraud</option>
                            <option value="fake" @selected(old('reason') === 'fake')>Fake listing</option>
                            <option value="misleading" @selected(old('reason') === 'misleading')>Misleading information</option>
                            <option value="inappropriate" @selected(old('reason') === 'inappropriate')>Inappropriate content</option>
                            <option value="other" @selected(old('reason') === 'other')>Other</option>
                        </select>
                        @error('reason')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="message" class="cw-label">Additional details (optional)</label>
                        <textarea id="message" name="message" rows="5" maxlength="2000" class="cw-field" placeholder="Share details to help our moderation team.">{{ old('message') }}</textarea>
                        @error('message')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div class="flex flex-col sm:flex-row gap-2">
                        <button type="submit" class="cw-button-primary">Submit report</button>
                        <a href="{{ url()->previous() }}" class="cw-button-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
