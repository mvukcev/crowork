# GDPR Readiness Audit (Pre-Launch Sprint)

Date: 2026-05-17  
Scope: CroWork web platform (worker, employer, admin surfaces)

## 1. Executive Summary

The codebase already includes several GDPR-adjacent building blocks (consent history model, deletion request model, user export endpoint, notification preferences, and worker profile visibility). However, before this sprint these capabilities were incomplete or loosely connected.

This sprint implements the P0 baseline for launch readiness:

- Mandatory terms/privacy acceptance in active registration flow.
- Consent evidence logging with metadata.
- Worker privacy controls page.
- Data export expansion to a practical machine-readable payload.
- Account deletion request workflow with 14-day grace scheduling.
- Queued anonymization job.
- Employer-side masking hardening for deleted/pending-deletion users.
- Coverage for critical GDPR behaviors in feature/unit tests.

## 2. Existing vs Missing (Pre-Sprint)

### Already Present (Pre-Sprint)

- `consent_histories` table and `ConsentHistory` model (minimal fields).
- `account_deletion_requests` table and `AccountDeletionRequest` model (minimal fields).
- `notification_preferences` stack (`NotificationPreference`, service, preferences UI).
- Worker profile visibility domain (`profile_visibility`, visibility service).
- `user/export` endpoint (basic/insufficient payload).
- DB-backed sessions.

### Key Gaps (Pre-Sprint)

- No mandatory consent checkboxes in active `/access` registration flow.
- No structured consent metadata (version/source/ip/user-agent/accepted_at).
- Hard account deletion behavior in profile flow (no grace period request lifecycle).
- No scheduling fields tying user and deletion request state.
- Export endpoint had broken relation risk and incomplete categories.
- No dedicated worker privacy/data self-service page.
- No anonymization job to enforce delayed erasure.
- Incomplete test coverage for GDPR-critical paths.

## 3. Risk Matrix

- High: no enforceable registration consent evidence in active auth funnel.
- High: immediate deletion semantics without grace workflow and auditable schedule.
- High: incomplete export payload for DSAR response quality.
- Medium: visibility leakage risk if deleted/pending users shown with broad employer visibility.
- Medium: inconsistent admin deletion statuses and weak guardrails.
- Medium: sparse automated test coverage for privacy logic.

## 4. Priority Plan

## P0 (Implemented in this sprint)

- Consent enforcement and logging in active registration flow.
- User/deletion/consent schema extensions for GDPR evidence and scheduling.
- Worker Privacy & Data UI and routes.
- Export endpoint expansion.
- Deletion request + delayed anonymization job.
- Employer masking hardening for pending/deleted users.
- Feature/unit tests for export, deletion request flow, anonymization, consent logging, notification preferences, profile visibility, and auth boundary checks.

## P1 (Implemented in this sprint)

- Automated retention runner (`privacy:retention-run`) with dry-run-first safety defaults.
- Admin-managed retention settings for rejected applications, inactive worker screening, inactive employer screening, and notification/log retention windows.
- Rejected application anonymization that keeps aggregate metadata while removing direct personal data.
- Inactive worker handling via existing delayed anonymization flow (`AccountDeletionService`) instead of unsafe hard deletion.
- Inactive employer processing intentionally constrained to reporting/manual legal review (no automatic deletion/anonymization).
- Notification/log retention for old records in `notifications`, `notification_digests`, and `email_send_log`.
- Monthly scheduler wiring in `routes/console.php` gated by `enable_retention_automation`.

## P1 Operational safeguards

- `enable_retention_automation=false` and `dry_run_mode=true` defaults prevent accidental data mutation.
- CLI `--dry-run` always wins.
- CLI `--force` only enables active mode when automation is enabled.
- Section selector (`--only=`) supports scoped runs: `rejected-applications`, `inactive-workers`, `inactive-employers`, `notifications`.
- Structured summary output includes scanned/eligible/processed/skipped/errors per section.
- Logs contain counters and IDs only, without raw profile/application content.

## P1 Consent Versioning and Re-acceptance

### Existing consent storage baseline

- `consent_histories` already stores `consent_type`, `consent_version`, `source`, `given`, `accepted_at`, `ip_address`, and `user_agent`.
- Registration already logs Terms/Privacy consent events through `ConsentHistoryService`.
- Worker Privacy & Data center already exposes privacy controls and consent-adjacent features.

### Gaps before P1 consent versioning

- No enforced check that authenticated users accepted the latest Terms and Privacy versions.
- No version-hash field to bind acceptance evidence to a specific policy revision snapshot.
- No forced re-acceptance route/middleware flow when legal version changes.
- No admin-controlled current Terms/Privacy version and hash settings to trigger re-acceptance safely.

### Affected routes and middleware

- Protected areas (`/dashboard`, worker routes, employer routes, authenticated notifications/profile routes, and custom admin web routes) are guarded by `EnsureLatestLegalConsentAccepted` middleware.
- Exemptions prevent lockout and redirect loops for:
  - logout
  - legal re-acceptance GET/POST
  - Terms/Privacy/Cookies pages
  - Worker Privacy page and account deletion request flow
  - data export route

### Re-acceptance flow

- If latest legal acceptance is missing, user is redirected to `/legal/reaccept`.
- Intended URL is preserved via `url.intended` for safe post-accept return.
- Re-accept submit records new Terms + Privacy consent entries with:
  - current version
  - current version hash
  - accepted timestamp
  - source (`reacceptance`)
  - IP and user agent

### Admin configuration approach

- Current legal policy versions/hashes are managed in admin settings (`settings` table):
  - `terms_version`
  - `terms_hash`
  - `privacy_policy_version`
  - `privacy_policy_hash`
- Hash can be explicitly configured, or derived server-side from version + canonical legal URL for stable default behavior.

## P1 Employer Lawful-Basis UX

### Objective

- Make employer-facing candidate data usage transparent and tied to real retention state.
- Prevent ambiguous data exposure by showing whether candidate personal data is active, time-limited, pending deletion, or already anonymized.

### Implemented behavior

- Added centralized access-state computation in `EmployerCandidateDataAccessService`.
- State model per application:
  - active recruitment process
  - rejected within retention window
  - pending deletion (scheduled anonymization date shown)
  - awaiting anonymization (retention window elapsed)
  - anonymized
- Rejected `data_available_until` is calculated from:
  - `status_updated_at` fallback `created_at`
  - plus configured `rejected_applications_retention_months`

### Employer surfaces updated

- Dashboard:
  - GDPR lawful-basis notice card with links to Terms and Privacy pages.
  - recent candidate cards include compact access-state label.
- Pipeline list:
  - GDPR notice block and per-row `Data access` state, including `available until` when applicable.
- Candidate detail:
  - dedicated lawful-basis panel with state label, explanation, legal basis, and availability deadline.
- Job applicants list (`employer.jobs.show`):
  - existing anonymized handling preserved.
  - state and availability display aligned with the same access-state service.

### Admin support visibility

- Admin `JobApplicationResource` now shows GDPR access state and data-available-until values in table and detail form fields.

### Email touchpoint update

- Employer new-application email now includes a GDPR footer clarifying candidate personal data usage and retention-bound anonymization.

## Admin GDPR Console

### Audit of existing admin privacy tooling (before this phase)

- Existing admin GDPR functionality:
  - `AdminPrivacyRequestController` and `admin/privacy_requests` view for account deletion requests only.
  - Admin could mark deletion requests as completed/cancelled.
  - Completed action triggered `AnonymizeUserDataJob::dispatchSync`.
  - Consent history existed but no dedicated admin console workflows.
  - User export endpoint existed (`/user/export`) but had no export history log.
  - Retention automation existed (`privacy:retention-run`) with no legal-hold enforcement.
- Missing controls before this phase:
  - no centralized GDPR admin dashboard
  - no DSAR lifecycle model for non-deletion request types
  - no export audit trail
  - no anonymization operations log
  - no legal hold mechanism to block anonymization/deletion
  - no internal breach incident tracker

### New console routes and pages

- Dashboard: `/admin/gdpr`
- DSAR:
  - `/admin/gdpr/requests`
  - `/admin/gdpr/requests/{gdprDataRequest}`
- Export history: `/admin/gdpr/exports`
- Anonymization logs: `/admin/gdpr/anonymization-logs`
- Legal holds: `/admin/gdpr/legal-holds`
- Breach incidents:
  - `/admin/gdpr/breach-incidents`
  - `/admin/gdpr/breach-incidents/{gdprBreachIncident}`

### Affected models/tables/services/jobs/controllers/views

- New tables/models:
  - `gdpr_data_requests` / `GdprDataRequest`
  - `gdpr_export_logs` / `GdprExportLog`
  - `gdpr_anonymization_logs` / `GdprAnonymizationLog`
  - `legal_holds` / `LegalHold`
  - `gdpr_breach_incidents` / `GdprBreachIncident`
- New services/middleware/controllers:
  - `LegalHoldService`
  - `EnsureStrictAdminRole` (`admin.strict` middleware alias)
  - `AdminGdprController`
- Existing integrations extended:
  - `UserDataExportController` now writes `gdpr_export_logs`
  - `AnonymizeUserDataJob` now writes `gdpr_anonymization_logs` and blocks on legal hold
  - `PrivacyRetentionService` now skips legal-hold targets and writes anonymization logs for retention application anonymization
- New views:
  - `resources/views/admin/gdpr/*`

### DSAR lifecycle (implemented)

- Types:
  - `access_export`, `deletion`, `rectification`, `objection_restriction`, `portability`, `consent_inquiry`, `other`
- Statuses:
  - `open`, `in_review`, `waiting_for_user`, `fulfilled`, `rejected`, `closed`
- Core fields:
  - requester user/email, type, status, priority, due date, assigned admin, internal notes, resolution summary, fulfillment/close timestamps
- Admin operations:
  - create, list/filter, detail view, status/assignment updates, note append, fulfill/close transitions

### Export log behavior (implemented)

- Every user data export writes one `gdpr_export_logs` record.
- Logged metadata includes actor, target user, IP/user-agent, status, generated/downloaded timestamps, expiry timestamp.
- No raw export payload is persisted in database.
- File path is metadata only; payload remains in private local storage until expiry cleanup (`gdpr:cleanup-expired-exports`).

### Anonymization log behavior (implemented)

- `AnonymizeUserDataJob` writes started/completed/failed/blocked log entries.
- Retention anonymization of rejected applications writes logs including legal-hold blocked events.
- Logs intentionally contain operation summaries and reasons, not raw sensitive personal payloads.

### Legal hold behavior (implemented)

- Legal hold entries support user scope and generic target scope (`target_type` + `target_id`).
- Active legal holds block:
  - account anonymization in `AnonymizeUserDataJob`
  - rejected-application retention anonymization in `PrivacyRetentionService`
- Hold placement/release is admin-only and includes who placed/released and timestamps.

### Breach incident tracking (implemented, lightweight)

- Internal tracker supports severity/status workflow and owner assignment.
- Tracks detection/reporting timestamps, category list, affected count, and required-notification flags.
- No automated external authority/user notification is triggered by this module.

### Security risks and mitigations

- Risk: unauthorized access to GDPR operations.
  - Mitigation: routes are guarded by `auth`, `legal.consent`, and strict admin-only middleware (`admin.strict`).
- Risk: accidental destructive action.
  - Mitigation: state-changing actions use POST/PATCH forms and explicit UI confirmations on legal-hold release / DSAR and incident updates.
- Risk: overexposure of personal data in audit pages.
  - Mitigation: console focuses on operational metadata and status; does not expose raw export payloads.
- Risk: anonymization while legal review is active.
  - Mitigation: legal hold enforcement before destructive anonymization paths.

### Recommended permissions model

- Current implementation: strict admin role required.
- Recommended next step (if permission matrix is introduced):
  - `manage_gdpr_console`
  - `manage_legal_holds`
  - `manage_breach_incidents`
  - `view_export_history`

### Audit trail requirements (covered by implementation)

- DSAR records include lifecycle transitions and internal notes.
- Export logs include who requested, timing, and status/failure reason.
- Anonymization logs include target/action/reason/status/timestamps.
- Legal holds include placement/release actor and timestamps.
- Breach incidents include owner, status/severity transitions, and internal notes.

### Remaining legal/manual procedures

- Legal/content teams still own formal DSAR response wording and legal review.
- Breach authority/user communications remain manual and policy-driven.
- Periodic review of legal holds and stale incidents should be part of operational runbooks.

## Operational Hardening Sprint (Final)

### System health visibility

- Added `GdprSystemHealthService` and dashboard health card in admin GDPR console.
- Health summary now reports:
  - scheduler heartbeat freshness
  - failed GDPR-related queue jobs
  - stuck anonymization logs (`started` older than 60 minutes)
  - stuck export logs (`pending` older than 30 minutes)
- Admin dashboard now shows a warning panel when any health signal is degraded.

### Endpoint abuse protections

- Added conservative rate limits to privacy-sensitive endpoints:
  - `GET /user/export` -> `throttle:3,1440`
  - `POST /worker/privacy/request-deletion` -> `throttle:3,1440`
  - `POST /consent/preferences` -> `throttle:30,60`

### File lifecycle hardening

- Added scheduled cleanup command `gdpr:cleanup-expired-exports` to purge expired export files and clear stored file references.
- Scheduler now runs:
  - `cleanup:deletion-requests` daily
  - `gdpr:cleanup-expired-exports` daily
- `AnonymizeUserDataJob` now attempts safe deletion of known worker upload paths (`photo_path`/`cv_path`) using strict prefix allow-listing.
- `PrivacyRetentionService` rejected-application anonymization now also removes safe upload references and files before persisting anonymized snapshots.

### Legal hold visibility

- Blocked anonymization events now include legal hold metadata in `summary_json`:
  - hold id
  - hold reason
  - hold placement timestamp
  - hold placing admin id
- GDPR anonymization logs list now surfaces legal hold details directly.

### Anonymization verification helper

- Added verifier route and page:
  - `GET /admin/gdpr/anonymization-logs/{gdprAnonymizationLog}`
- Verifier provides a lightweight operational check of residual identifier/file-reference risk by target type.

## Upload & File Lifecycle Policy

- Worker profile photos are stored on public disk under `worker-photos/`.
- GDPR exports are stored on local disk under `exports/gdpr/` and purged after expiry by scheduled command.
- Retention/deletion anonymization only deletes files from explicitly allowed upload prefixes:
  - `worker-photos/`
  - `worker-cv/`
  - `uploads/cv/`
  - `applications/cv/`
- Paths containing traversal patterns (`..`) are ignored and never deleted.

## Backup & Restore GDPR Policy

- Backups may contain historical personal data that has since been anonymized/deleted in production.
- Restore policy requirements:
  - do not restore production backups into publicly reachable environments
  - restore only to isolated, access-restricted environments
  - re-run GDPR maintenance tasks after restore (retention + expired export cleanup)
  - ensure restored environments are covered by legal hold and DSAR governance before any operational use
- Retention policy should define backup retention windows and destruction schedule aligned with legal/compliance decisions.

## P2 (Recommended hardening)

- Automated retention enforcement reports and anomaly alerts.
- Data processing inventory and RoPA artifacts stored alongside code/docs.
- Cross-system deletion hooks (if/when third-party processors are added).

## 5. Route / Model / Table Mapping

### Routes

- Registration/auth:
  - `POST /access/register` (`AccessController@register`) now enforces and records consent.
  - `POST /access/login` (`AccessController@login`) blocks pending-deletion accounts.
- Worker privacy:
  - `GET /worker/privacy` (`WorkerPrivacyController@show`)
  - `PATCH /worker/privacy/visibility` (`WorkerPrivacyController@updateVisibility`)
  - `POST /worker/privacy/request-deletion` (`WorkerPrivacyController@requestDeletion`)
- Data export:
  - `GET /user/export` (`UserDataExportController@export`)
- Admin privacy request handling:
  - `GET /admin/privacy-requests`
  - `PUT /admin/privacy-requests/{deletionRequest}`

### Models

- `User`:
  - soft deletes enabled.
  - pending deletion flags/timestamps.
  - GDPR relations (`consentHistories`, `accountDeletionRequests`, `applicationComments`).
- `ConsentHistory`:
  - enriched metadata fields/casts.
- `AccountDeletionRequest`:
  - request/schedule/completion fields + status constants.
- `WorkerProfile`:
  - visibility managed via dedicated worker privacy page.

### Tables

- `users`:
  - `pending_deletion`, `deletion_requested_at`, `anonymization_scheduled_at`, `last_login_at`, `deleted_at`.
- `consent_histories`:
  - `consent_version`, `source`, `accepted_at`, `ip_address`, `user_agent`.
- `account_deletion_requests`:
  - `reason`, `requested_at`, `anonymization_scheduled_at`, `completed_at`.

## 6. Implemented Components

- `App\Services\ConsentHistoryService` for structured consent writes.
- `App\Services\AccountDeletionService` for deletion request + delayed job dispatch.
- `App\Jobs\AnonymizeUserDataJob` for delayed anonymization and request completion.
- `App\Http\Controllers\WorkerPrivacyController` for worker self-service privacy actions.

## 7. Test Coverage Added

- Registration consent logging and required consent validation.
- Pending-deletion login block.
- Worker privacy page + visibility update + deletion request route guards.
- User data export payload shape and key sections.
- Anonymization job behavior and request completion.
- Notification preferences update and guest auth boundary.
- Profile deletion test updated for request-based semantics.

## 8. Known Limitations / Follow-up

- `saved_jobs` export currently returns placeholder metadata because saved-jobs persistence is not present yet.
- Delayed anonymization execution depends on queue worker availability in deployment.
- Worker settings form currently includes an email field that is not fully handled in controller validation logic (non-blocking for this GDPR sprint).

## 9. Validation status

- Migrations: passed (`php artisan migrate`, `php artisan migrate:status`).
- Worker privacy page: passed (link, page load, visibility save, notification preference save).
- Export: passed (`GET /user/export` returns `200` attachment response).
- Deletion request: passed (worker request flow logs out user and creates pending deletion state).
- Employer masking: passed (pending-deletion worker identity masked in dashboard/pipeline candidate labels).
- PHPUnit status: passed (`./vendor/bin/phpunit` targeted GDPR P0 suite), with 2 deprecation notices only.
