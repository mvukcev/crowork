# AUTHENTICATED QA — 2026-05-17

## Scope

Authenticated QA only:
- create/normalize local QA users
- login through real UI flow
- verify worker/employer/admin authenticated routes
- verify mobile authenticated behavior
- run required validation commands

No redesign/refactor performed.

## QA Users Created/Normalized

| Role | Email | Password | State |
|---|---|---|---|
| Admin | qa-admin@crowork.test | Password123! | role=admin, email_verified_at set |
| Employer | qa-employer@crowork.test | Password123! | role=employer, email_verified_at set, employer profile approved |
| Worker | qa-worker@crowork.test | Password123! | role=worker, email_verified_at set, worker profile exists |

Additional normalized local data for QA:
- employer company profile populated (approved, display data present)
- employer job listing ensured: `qa-employer-authenticated-job`
- worker job application seeded to employer job for pipeline interaction checks

## Login Through Real UI

Flow used for each role: `/access` (email step -> password step -> login submit).

Session handling used before each role:
- explicit logout via UI button when present
- fallback POST `/logout` with CSRF
- local/session storage cleared

Initial blocker diagnosed and fixed:
- Symptom: employer login redirected to `/email/verify` (404 in this app)
- Reason: `EnsureEmployerIsApproved` middleware redirects unverified employer users (`hasVerifiedEmail() === false`)
- Route affected: `employer.dashboard`
- Why QA user was "unverified": `email_verified_at` is not mass-assignable on `User`, so initial `updateOrCreate(..., ['email_verified_at' => now()])` did not persist it
- Local QA fix: set `email_verified_at` directly via query update for all three QA users

## Worker Route Matrix (qa-worker@crowork.test)

| Route | HTTP | Final URL | Result | Notes |
|---|---:|---|---|---|
| /worker/dashboard | 200 | /worker/dashboard | Pass | No 403/500 page state, no overflow |
| /worker/profile | 200 | /worker/profile | Pass | No 403/500 page state, no overflow |
| /worker/profile/edit | 404 | /worker/profile/edit | N/A | Route not present in this build |
| /worker/applications | 200 | /worker/applications | Pass | No 403/500 page state, no overflow |
| /worker/settings | 200 | /worker/settings | Pass | No 403/500 page state, no overflow |
| /notifications | 200 | /notifications | Pass | Loads correctly |

Worker UX checks:
- no raw translation keys observed on tested pages
- empty states stable on tested pages
- validation slots stable on tested pages
- forms rendered correctly (full-width layout where applicable)

## Employer Route Matrix (qa-employer@crowork.test)

| Route | HTTP | Final URL | Result | Notes |
|---|---:|---|---|---|
| /employer | 200 | /employer/dashboard | Pass | Correct entry redirect |
| /employer/dashboard | 200 | /employer/dashboard | Pass | No 403/500 |
| /employer/settings/profile | 200 | /employer/settings/profile | Pass | No 403/500 |
| /employer/settings/branding | 404 | /employer/settings/branding | N/A | Route not present in this build |
| /employer/jobs | 200 | /employer/jobs | Pass | No 403/500 |
| /employer/jobs/create | 200 | /employer/jobs/create | Pass | No 403/500 |
| /employer/jobs/qa-employer-authenticated-job/edit | 200 | /employer/jobs/qa-employer-authenticated-job/edit | Pass | Existing-job edit route verified |
| /employer/applications/pipeline | 200 | /employer/applications/pipeline | Pass | Pipeline functional |
| /notifications | 200 | /notifications | Pass | Loads correctly |

Employer UX/resilience checks:
- no raw translation keys observed on tested employer routes
- no stuck loading states observed
- no validation layout jumps observed
- pipeline async status control tested with real status change: control remains enabled (`disabledAfter=false`)
- logo fallback verified on logo-card context (`.cw-employer-logo`) by forcing image error -> fallback initials rendered

## Admin Route Matrix (qa-admin@crowork.test)

| Route | HTTP | Final URL | Result | Notes |
|---|---:|---|---|---|
| /admin | 200 | /admin | Pass | Dashboard loads |
| /admin/system-health | 200 | /admin/system-health | Pass | Loads |
| /admin/translation-manager | 200 | /admin/translation-manager | Pass* | Page intentionally surfaces translation key strings in manager UI |
| /admin/jobs | 200 | /admin/jobs | Pass | Loads |
| /admin/employers | 200 | /admin/employers | Pass | Loads |
| /admin/users | 404 | /admin/users | N/A | Route not present in this build |

Admin checks:
- no 500 pages observed
- no missing critical assets that block panel rendering
- notification control present on available admin panel routes

## Mobile Authenticated QA (390x844)

### Worker

| Route | HTTP | Overflow | Result |
|---|---:|---|---|
| /worker/dashboard | 200 | No | Pass |
| /worker/profile | 200 | No | Pass |
| /worker/settings | 200 | No | Pass |
| /worker/applications | 200 | No | Pass |

### Employer

| Route | HTTP | Overflow | Result |
|---|---:|---|---|
| /employer/dashboard | 200 | No | Pass |
| /employer/settings/profile | 200 | No | Pass |
| /employer/jobs/create | 200 | No | Pass |
| /employer/applications/pipeline | 200 | No | Pass |

Mobile observations:
- no horizontal overflow on tested authenticated pages
- sticky header behavior present on tested pages
- forms usable on worker/employer profile/settings/create flows

## Fixes Applied During This QA

1. Critical authenticated route fix (source code):
- File: `resources/views/employer/applications/pipeline.blade.php`
- Issue: malformed Blade markup causing parse error and HTTP 500
- Result: pipeline route now returns 200 and interactive status updates work

2. Local QA-data fix (no production feature change):
- Set `email_verified_at` for QA users to satisfy verified checks in middleware
- Seeded QA application data to validate pipeline async controls

## Validation Commands

| Command | Result |
|---|---|
| npm run build | Pass |
| php artisan optimize:clear | Pass |
| php artisan view:cache | Pass |

## Exact Failures / Remaining Blockers

No blocking authenticated defects remain for tested flows.

Non-blocking/N-A items:
- `/worker/profile/edit` -> 404 (route not present)
- `/employer/settings/branding` -> 404 (route not present)
- `/admin/users` -> 404 (route not present)
- `/admin/translation-manager` raw-key detector flags key-like strings by design of translation management UI

## Final Verdict

PASS.

Authenticated QA is complete using normalized local QA users and real UI login flow. Worker, employer, and admin authenticated checks are covered, mobile authenticated checks are covered, and required commands pass.