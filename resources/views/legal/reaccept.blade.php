<x-app-layout>
    <x-slot name="title">Updated Terms and Privacy Policy</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-2xl">
            <div class="cw-surface p-6 space-y-5">
                <h1 class="text-2xl font-semibold text-slate-900">Updated Terms and Privacy Policy</h1>
                <p class="text-sm text-slate-600">
                    We updated our legal documents. To continue using protected account features, please review and accept the latest Terms of Service and Privacy Policy.
                </p>

                @if($errors->any())
                    <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700 space-y-1">
                    <p><strong>Terms version:</strong> {{ $current['terms_version'] }}</p>
                    <p><strong>Privacy version:</strong> {{ $current['privacy_policy_version'] }}</p>
                    <p>
                        Review documents:
                        <a href="{{ route('terms') }}" class="underline text-slate-900">Terms</a>
                        and
                        <a href="{{ route('privacy') }}" class="underline text-slate-900">Privacy Policy</a>.
                    </p>
                </div>

                <form method="POST" action="{{ route('legal.reaccept.store') }}" class="space-y-4">
                    @csrf

                    <label class="flex items-start gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="accept_terms" value="1" class="mt-1 rounded border-slate-300" required>
                        <span>I accept the latest Terms of Service.</span>
                    </label>

                    <label class="flex items-start gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="accept_privacy" value="1" class="mt-1 rounded border-slate-300" required>
                        <span>I accept the latest Privacy Policy.</span>
                    </label>

                    <button type="submit" class="cw-button-primary">Accept and continue</button>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
