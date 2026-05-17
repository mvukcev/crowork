<?php

namespace App\Http\Controllers;

use App\Models\AccountDeletionRequest;
use App\Models\GdprAnonymizationLog;
use App\Models\GdprBreachIncident;
use App\Models\GdprDataRequest;
use App\Models\GdprExportLog;
use App\Models\LegalHold;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminGdprController extends Controller
{
    public function index(): View
    {
        $openDsarStatuses = [
            GdprDataRequest::STATUS_OPEN,
            GdprDataRequest::STATUS_IN_REVIEW,
            GdprDataRequest::STATUS_WAITING_FOR_USER,
        ];

        $recentEvents = collect()
            ->merge(
                GdprDataRequest::query()->latest()->limit(4)->get()->map(fn (GdprDataRequest $request): array => [
                    'type' => 'dsar',
                    'title' => 'DSAR #' . $request->id . ' ' . $request->request_type,
                    'status' => $request->status,
                    'at' => $request->updated_at,
                    'url' => route('admin.gdpr.dsar.show', $request),
                ])
            )
            ->merge(
                GdprAnonymizationLog::query()->latest()->limit(4)->get()->map(fn (GdprAnonymizationLog $log): array => [
                    'type' => 'anonymization',
                    'title' => 'Anonymization ' . $log->action,
                    'status' => $log->status,
                    'at' => $log->updated_at,
                    'url' => route('admin.gdpr.anonymization.index'),
                ])
            )
            ->merge(
                GdprExportLog::query()->latest()->limit(4)->get()->map(fn (GdprExportLog $log): array => [
                    'type' => 'export',
                    'title' => 'Export #' . $log->id,
                    'status' => $log->status,
                    'at' => $log->updated_at,
                    'url' => route('admin.gdpr.exports.index'),
                ])
            )
            ->sortByDesc('at')
            ->take(10)
            ->values();

        return view('admin.gdpr.index', [
            'cards' => [
                'open_dsar' => GdprDataRequest::query()->whereIn('status', $openDsarStatuses)->count(),
                'pending_deletions' => AccountDeletionRequest::query()->where('status', AccountDeletionRequest::STATUS_PENDING)->count(),
                'anonymizations_scheduled' => User::query()->where('pending_deletion', true)->whereNotNull('anonymization_scheduled_at')->count(),
                'anonymizations_completed' => GdprAnonymizationLog::query()->where('status', GdprAnonymizationLog::STATUS_COMPLETED)->count(),
                'users_under_legal_hold' => LegalHold::query()->where('status', LegalHold::STATUS_ACTIVE)->count(),
                'breach_incidents_open' => GdprBreachIncident::query()->whereIn('status', [
                    GdprBreachIncident::STATUS_OPEN,
                    GdprBreachIncident::STATUS_INVESTIGATING,
                    GdprBreachIncident::STATUS_CONTAINED,
                ])->count(),
            ],
            'retentionStatus' => [
                'enabled' => Setting::getBool('enable_retention_automation', false),
                'dry_run_mode' => Setting::getBool('dry_run_mode', true),
            ],
            'recentEvents' => $recentEvents,
        ]);
    }

    public function dsarIndex(Request $request): View
    {
        $requests = GdprDataRequest::query()
            ->with(['requesterUser:id,email', 'assignedAdmin:id,name'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', (string) $request->string('status')))
            ->when($request->filled('request_type'), fn ($query) => $query->where('request_type', (string) $request->string('request_type')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.gdpr.dsar-index', [
            'requests' => $requests,
            'admins' => User::query()->where('role', User::ROLE_ADMIN)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function dsarStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'requester_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'requester_email' => ['nullable', 'email:rfc,dns', 'max:255'],
            'request_type' => ['required', 'string', 'in:access_export,deletion,rectification,objection_restriction,portability,consent_inquiry,other'],
            'status' => ['required', 'string', 'in:open,in_review,waiting_for_user,fulfilled,rejected,closed'],
            'priority' => ['required', 'string', 'in:low,normal,high,urgent'],
            'due_at' => ['nullable', 'date'],
            'assigned_admin_id' => ['nullable', 'integer', 'exists:users,id'],
            'internal_notes' => ['nullable', 'string', 'max:8000'],
        ]);

        GdprDataRequest::query()->create($validated);

        return redirect()->route('admin.gdpr.dsar.index')->with('success', 'DSAR request created.');
    }

    public function dsarShow(GdprDataRequest $gdprDataRequest): View
    {
        return view('admin.gdpr.dsar-show', [
            'request' => $gdprDataRequest->load(['requesterUser:id,email', 'assignedAdmin:id,name']),
            'admins' => User::query()->where('role', User::ROLE_ADMIN)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function dsarUpdate(Request $request, GdprDataRequest $gdprDataRequest): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:open,in_review,waiting_for_user,fulfilled,rejected,closed'],
            'priority' => ['required', 'string', 'in:low,normal,high,urgent'],
            'due_at' => ['nullable', 'date'],
            'assigned_admin_id' => ['nullable', 'integer', 'exists:users,id'],
            'resolution_summary' => ['nullable', 'string', 'max:8000'],
            'internal_note_append' => ['nullable', 'string', 'max:2000'],
        ]);

        $notes = $gdprDataRequest->internal_notes;
        $append = trim((string) ($validated['internal_note_append'] ?? ''));

        if ($append !== '') {
            $prefix = now()->toDateTimeString() . ' · admin#' . $request->user()->id;
            $notes = trim((string) $notes);
            $notes = $notes === '' ? "[$prefix] $append" : $notes . "\n\n[$prefix] $append";
        }

        $gdprDataRequest->update([
            'status' => $validated['status'],
            'priority' => $validated['priority'],
            'due_at' => $validated['due_at'] ?? null,
            'assigned_admin_id' => $validated['assigned_admin_id'] ?? null,
            'resolution_summary' => $validated['resolution_summary'] ?? null,
            'internal_notes' => $notes,
            'fulfilled_at' => $validated['status'] === GdprDataRequest::STATUS_FULFILLED ? now() : null,
            'closed_at' => $validated['status'] === GdprDataRequest::STATUS_CLOSED ? now() : null,
        ]);

        return redirect()->route('admin.gdpr.dsar.show', $gdprDataRequest)->with('success', 'DSAR request updated.');
    }

    public function exportsIndex(Request $request): View
    {
        $logs = GdprExportLog::query()
            ->with(['user:id,email', 'requestedByUser:id,email', 'requestedByAdmin:id,name'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', (string) $request->string('status')))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.gdpr.exports-index', [
            'logs' => $logs,
        ]);
    }

    public function anonymizationIndex(Request $request): View
    {
        $logs = GdprAnonymizationLog::query()
            ->with(['user:id,email', 'triggeredByAdmin:id,name'])
            ->when($request->filled('status'), fn ($query) => $query->where('status', (string) $request->string('status')))
            ->when($request->filled('target_type'), fn ($query) => $query->where('target_type', (string) $request->string('target_type')))
            ->latest('started_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.gdpr.anonymization-index', [
            'logs' => $logs,
        ]);
    }

    public function legalHoldsIndex(): View
    {
        return view('admin.gdpr.legal-holds-index', [
            'activeHolds' => LegalHold::query()
                ->with(['user:id,email', 'placedByAdmin:id,name'])
                ->where('status', LegalHold::STATUS_ACTIVE)
                ->latest('placed_at')
                ->paginate(20, ['*'], 'active_page'),
            'releasedHolds' => LegalHold::query()
                ->with(['user:id,email', 'releasedByAdmin:id,name'])
                ->where('status', LegalHold::STATUS_RELEASED)
                ->latest('released_at')
                ->paginate(20, ['*'], 'released_page'),
        ]);
    }

    public function legalHoldsStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'target_type' => ['nullable', 'string', 'max:191'],
            'target_id' => ['nullable', 'string', 'max:64'],
            'reason' => ['required', 'string', 'max:191'],
            'notes' => ['nullable', 'string', 'max:3000'],
        ]);

        if (empty($validated['user_id']) && (empty($validated['target_type']) || empty($validated['target_id']))) {
            return back()->withErrors([
                'user_id' => 'Provide user_id or target_type + target_id for a legal hold.',
            ])->withInput();
        }

        LegalHold::query()->create([
            ...$validated,
            'status' => LegalHold::STATUS_ACTIVE,
            'placed_by_admin_id' => $request->user()->id,
            'placed_at' => now(),
        ]);

        return redirect()->route('admin.gdpr.legal-holds.index')->with('success', 'Legal hold placed.');
    }

    public function legalHoldsRelease(Request $request, LegalHold $legalHold): RedirectResponse
    {
        if ($legalHold->status !== LegalHold::STATUS_ACTIVE) {
            return redirect()->route('admin.gdpr.legal-holds.index')->with('success', 'Legal hold is already released.');
        }

        $legalHold->update([
            'status' => LegalHold::STATUS_RELEASED,
            'released_by_admin_id' => $request->user()->id,
            'released_at' => now(),
        ]);

        return redirect()->route('admin.gdpr.legal-holds.index')->with('success', 'Legal hold released.');
    }

    public function breachIncidentsIndex(Request $request): View
    {
        $incidents = GdprBreachIncident::query()
            ->with('ownerAdmin:id,name')
            ->when($request->filled('status'), fn ($query) => $query->where('status', (string) $request->string('status')))
            ->when($request->filled('severity'), fn ($query) => $query->where('severity', (string) $request->string('severity')))
            ->latest('detected_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.gdpr.breaches-index', [
            'incidents' => $incidents,
            'admins' => User::query()->where('role', User::ROLE_ADMIN)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function breachIncidentsStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'severity' => ['required', 'string', 'in:low,medium,high,critical'],
            'status' => ['required', 'string', 'in:open,investigating,contained,resolved,closed'],
            'detected_at' => ['required', 'date'],
            'reported_at' => ['nullable', 'date'],
            'summary' => ['required', 'string', 'max:8000'],
            'affected_data_categories' => ['nullable', 'string', 'max:2000'],
            'affected_user_count' => ['nullable', 'integer', 'min:0'],
            'authority_notification_required' => ['nullable', 'boolean'],
            'users_notification_required' => ['nullable', 'boolean'],
            'owner_admin_id' => ['nullable', 'integer', 'exists:users,id'],
            'internal_notes' => ['nullable', 'string', 'max:8000'],
        ]);

        GdprBreachIncident::query()->create([
            ...$validated,
            'affected_data_categories' => $this->stringListToArray($validated['affected_data_categories'] ?? null),
            'authority_notification_required' => (bool) ($validated['authority_notification_required'] ?? false),
            'users_notification_required' => (bool) ($validated['users_notification_required'] ?? false),
            'resolved_at' => in_array($validated['status'], [GdprBreachIncident::STATUS_RESOLVED, GdprBreachIncident::STATUS_CLOSED], true) ? now() : null,
        ]);

        return redirect()->route('admin.gdpr.breaches.index')->with('success', 'Breach incident created.');
    }

    public function breachIncidentsShow(GdprBreachIncident $gdprBreachIncident): View
    {
        return view('admin.gdpr.breaches-show', [
            'incident' => $gdprBreachIncident->load('ownerAdmin:id,name'),
            'admins' => User::query()->where('role', User::ROLE_ADMIN)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function breachIncidentsUpdate(Request $request, GdprBreachIncident $gdprBreachIncident): RedirectResponse
    {
        $validated = $request->validate([
            'severity' => ['required', 'string', 'in:low,medium,high,critical'],
            'status' => ['required', 'string', 'in:open,investigating,contained,resolved,closed'],
            'reported_at' => ['nullable', 'date'],
            'summary' => ['required', 'string', 'max:8000'],
            'affected_data_categories' => ['nullable', 'string', 'max:2000'],
            'affected_user_count' => ['nullable', 'integer', 'min:0'],
            'authority_notification_required' => ['nullable', 'boolean'],
            'users_notification_required' => ['nullable', 'boolean'],
            'owner_admin_id' => ['nullable', 'integer', 'exists:users,id'],
            'internal_notes' => ['nullable', 'string', 'max:8000'],
        ]);

        $gdprBreachIncident->update([
            ...$validated,
            'affected_data_categories' => $this->stringListToArray($validated['affected_data_categories'] ?? null),
            'authority_notification_required' => (bool) ($validated['authority_notification_required'] ?? false),
            'users_notification_required' => (bool) ($validated['users_notification_required'] ?? false),
            'resolved_at' => in_array($validated['status'], [GdprBreachIncident::STATUS_RESOLVED, GdprBreachIncident::STATUS_CLOSED], true) ? ($gdprBreachIncident->resolved_at ?: now()) : null,
        ]);

        return redirect()->route('admin.gdpr.breaches.show', $gdprBreachIncident)->with('success', 'Breach incident updated.');
    }

    /**
     * @return array<int, string>
     */
    private function stringListToArray(?string $value): array
    {
        if (! is_string($value) || trim($value) === '') {
            return [];
        }

        return collect(explode(',', $value))
            ->map(fn (string $part): string => trim($part))
            ->filter(fn (string $part): bool => $part !== '')
            ->values()
            ->all();
    }
}
