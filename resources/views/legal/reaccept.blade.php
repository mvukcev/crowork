<x-app-layout>
    <x-slot name="title">{{ __('legal_ui.reaccept.title') }}</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-2xl">
            <div class="cw-surface p-6 space-y-5">
                <h1 class="text-2xl font-semibold text-slate-900">{{ __('legal_ui.reaccept.heading') }}</h1>
                <p class="text-sm text-slate-600">
                    {{ __('legal_ui.reaccept.intro') }}
                </p>

                @if($errors->any())
                    <div class="rounded-lg border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-900">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="rounded-lg border border-slate-200 bg-slate-50 p-4 text-sm text-slate-700 space-y-1">
                    <p><strong>{{ __('legal_ui.reaccept.terms_version') }}</strong> {{ $current['terms_version'] }}</p>
                    <p><strong>{{ __('legal_ui.reaccept.privacy_version') }}</strong> {{ $current['privacy_policy_version'] }}</p>
                    <p>
                        {{ __('legal_ui.reaccept.review_docs') }}
                        <a href="{{ route('terms') }}" class="underline text-slate-900">{{ __('legal_ui.reaccept.terms') }}</a>
                        {{ app()->isLocale('hr') ? 'i' : 'and' }}
                        <a href="{{ route('privacy') }}" class="underline text-slate-900">{{ __('legal_ui.reaccept.privacy') }}</a>.
                    </p>
                </div>

                <form method="POST" action="{{ route('legal.reaccept.store') }}" class="space-y-4">
                    @csrf

                    <label class="flex items-start gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="accept_terms" value="1" class="mt-1 rounded border-slate-300" required>
                        <span>{{ __('legal_ui.reaccept.accept_terms') }}</span>
                    </label>

                    <label class="flex items-start gap-2 text-sm text-slate-700">
                        <input type="checkbox" name="accept_privacy" value="1" class="mt-1 rounded border-slate-300" required>
                        <span>{{ __('legal_ui.reaccept.accept_privacy') }}</span>
                    </label>

                    <button type="submit" class="cw-button-primary">{{ __('legal_ui.reaccept.submit') }}</button>
                </form>
            </div>
        </div>
    </section>
</x-app-layout>
