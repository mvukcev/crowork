<nav class="mb-6 flex flex-wrap gap-2 text-sm">
    <a href="{{ route('admin.gdpr.index') }}" class="cw-button-secondary">Dashboard</a>
    <a href="{{ route('admin.gdpr.dsar.index') }}" class="cw-button-secondary">DSAR Requests</a>
    <a href="{{ route('admin.gdpr.exports.index') }}" class="cw-button-secondary">Export History</a>
    <a href="{{ route('admin.gdpr.anonymization.index') }}" class="cw-button-secondary">Anonymization Logs</a>
    <a href="{{ route('admin.gdpr.legal-holds.index') }}" class="cw-button-secondary">Legal Holds</a>
    <a href="{{ route('admin.gdpr.breaches.index') }}" class="cw-button-secondary">Breach Incidents</a>
    @if(Route::has('filament.admin.resources.settings.index'))
        <a href="{{ route('filament.admin.resources.settings.index') }}" class="cw-button-secondary">Retention Settings</a>
    @endif
</nav>
