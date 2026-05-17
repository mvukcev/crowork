# Structured CV Architecture

## 1. Current Architecture (before refactor)

The CV layer was centered on `worker_profiles` with mixed freeform fields:

- `education_summary` (text)
- `work_experience` (longText)
- `certifications` (text)
- `recommendations` (longText)
- `skills` (json array)
- `languages` (json array)

Applications persist a profile copy in `job_applications.profile_snapshot` and `education_applications.profile_snapshot`.

### Core touchpoints

- Worker edit/preview: `WorkerProfileController`, worker profile Blade views
- Snapshot creation: `WorkerProfile::toSnapshot()` used by job/education application controllers
- Employer candidate rendering: employer application controller + candidate Blade + Filament employer resource
- Admin rendering: Filament worker/job/education resources + snapshot modal
- Visibility masking: `ApplicationVisibilityService`
- GDPR exports and deletion: `UserDataExportController`, `AnonymizeUserDataJob`

## 2. Problems Identified

- Freeform blobs made data hard to validate, search, and normalize.
- Experience/education/certifications/references could not be modeled as ordered entries.
- Cross-view rendering duplicated ad-hoc parsing logic.
- Snapshot compatibility constraints discouraged structural change.
- GDPR handling for nested CV content was implicit and brittle.

## 3. Target Data Model

Introduce normalized relational tables tied to `worker_profiles`:

- `worker_experiences`
- `worker_educations`
- `worker_certifications`
- `worker_references`
- `worker_skills`
- `worker_languages`

Each table includes:

- FK `worker_profile_id`
- ordered `sort_order`
- timestamps

## 4. Snapshot Compatibility Strategy

`WorkerProfile::toSnapshot()` now emits both:

- legacy keys (`education_summary`, `work_experience`, `certifications`, `recommendations`, `skills`, `languages`)
- structured keys (`structured_experiences`, `structured_educations`, `structured_certifications`, `structured_references`)
- `snapshot_version = 2`

Legacy values are auto-derived from structured records when legacy text fields are empty.

This keeps historical and external consumers stable while enabling progressive migration to structured reads.

## 5. UX Architecture

Worker profile editor migrates from freeform textareas to modular repeatable blocks:

- Education cards
- Experience cards
- Certification cards
- Reference cards

Skills and desired roles remain chip-based.
Languages remain row-based but now persist to `worker_languages` relation in addition to JSON compatibility.

## 6. Visibility/Privacy Rules

`ApplicationVisibilityService` now includes structured keys in default/anonymous safe sets:

- `structured_experiences`
- `structured_educations`
- `structured_certifications`

`structured_references` is only visible where visibility allows legacy recommendations.

## 7. Employer/Admin Rendering

Employer and admin candidate/profile views now prefer structured sections and gracefully fall back to legacy fields.

## 8. GDPR and Lifecycle

- Export now includes `worker_profile_structured` arrays for all relational CV sections.
- Anonymization now deletes structured child records (`worker_experiences`, `worker_educations`, `worker_certifications`, `worker_references`, `worker_skills`, `worker_languages`) in addition to anonymizing profile scalars.

## 9. Rollout Notes

- No destructive migration of legacy columns in this phase.
- Existing application snapshots remain immutable and valid.
- New snapshots include both legacy and structured keys.
- Next phase can retire legacy fields once all consumers are switched and historical/reporting requirements are met.
