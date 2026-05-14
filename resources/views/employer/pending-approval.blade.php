<x-app-layout>
    <x-slot name="title">Pending Approval</x-slot>

    <section class="cw-section">
        <div class="cw-container max-w-3xl">
            <div class="cw-surface p-7 text-center">
                <p class="cw-kicker mb-2">Employer verification</p>
                <h1 class="cw-display text-4xl md:text-6xl mb-3">Your account is under review.</h1>
                <p class="text-slate-600 mb-6">We review employer accounts before enabling full access to protect workers and hiring quality.</p>
                <div class="flex flex-wrap justify-center gap-2">
                    <a href="{{ url('/for-employers') }}" class="cw-button-secondary">Why verification matters</a>
                    <a href="{{ url('/contact') }}" class="cw-button-secondary">Contact support</a>
                    <form method="POST" action="{{ route('verification.send') }}">@csrf<button class="cw-button-primary">Resend verification email</button></form>
                </div>
            </div>
        </div>
    </section>
</x-app-layout>
