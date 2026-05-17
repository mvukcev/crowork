<nav class="mb-6 flex flex-wrap gap-2 text-sm">
    <a href="{{ route('admin.gdpr.index') }}" class="cw-button-secondary">{{ __('gdpr_admin.dashboard') }}</a>
    <a href="{{ route('admin.gdpr.dsar.index') }}" class="cw-button-secondary">{{ __('gdpr_admin.dsar_requests') }}</a>
    <a href="{{ route('admin.gdpr.exports.index') }}" class="cw-button-secondary">{{ __('gdpr_admin.export_history') }}</a>
    <a href="{{ route('admin.gdpr.anonymization.index') }}" class="cw-button-secondary">{{ __('gdpr_admin.anonymization_logs') }}</a>
    <a href="{{ route('admin.gdpr.legal-holds.index') }}" class="cw-button-secondary">{{ __('gdpr_admin.legal_holds') }}</a>
    <a href="{{ route('admin.gdpr.breaches.index') }}" class="cw-button-secondary">{{ __('gdpr_admin.breach_incidents') }}</a>
    @if(Route::has('filament.admin.resources.settings.index'))
        <a href="{{ route('filament.admin.resources.settings.index') }}" class="cw-button-secondary">{{ __('gdpr_admin.retention_settings') }}</a>
    @endif
</nav>
