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

## P1 (Recommended next)

- Consent withdrawal and version re-accept flow for policy updates.
- DSAR request ticketing workflow with SLA/status history.
- Admin privacy console UX improvements (filters, audit timeline, actor attribution).
- Cookie consent persistence -> server-side consent history reconciliation.

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
