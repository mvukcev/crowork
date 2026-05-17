# System Consolidation Report

Date: 2026-05-17
Scope: Design system consolidation, translation governance, dashboard/public alignment, and cleanup hardening

## What Was Consolidated

### Design system primitives
- Added canonical shell spacing utility in [resources/css/app.css](resources/css/app.css): `.cw-shell-spacing`
- Added canonical card utilities in [resources/css/app.css](resources/css/app.css): `.cw-card-shell`, `.cw-card-shell-interactive`
- Added canonical empty state utility in [resources/css/app.css](resources/css/app.css): `.cw-empty-state`
- Added canonical progress utilities in [resources/css/app.css](resources/css/app.css): `.cw-progress-track`, `.cw-progress-fill`
- Added canonical transform utility in [resources/css/app.css](resources/css/app.css): `.cw-ring-rotate-top`

### Dashboard/public shell alignment
- Migrated employer dashboard shell and major cards to canonical spacing/card primitives in [resources/views/employer/dashboard.blade.php](resources/views/employer/dashboard.blade.php)
- Migrated worker dashboard profile-completeness bar and key empty state to canonical primitives in [resources/views/worker/dashboard.blade.php](resources/views/worker/dashboard.blade.php)
- Removed repeated inline transform logic from dashboard ring chart in [resources/views/employer/dashboard.blade.php](resources/views/employer/dashboard.blade.php)

### Translation governance and namespace normalization
- Added missing-translation fallback logging via `Lang::handleMissingKeysUsing(...)` in [app/Providers/AppServiceProvider.php](app/Providers/AppServiceProvider.php)
- Added translation parity checker command in [app/Console/Commands/CheckTranslationParity.php](app/Console/Commands/CheckTranslationParity.php)
  - Command: `php artisan crowork:translations:check --locales=en,hr`
- Migrated hardcoded user-facing copy to namespaced translations on key surfaces:
  - [resources/views/employer/dashboard.blade.php](resources/views/employer/dashboard.blade.php) → `employer.dashboard.*`
  - [resources/views/employer/jobs/show.blade.php](resources/views/employer/jobs/show.blade.php) → `employer.job_applications.*`
  - [resources/views/employer/jobs/create.blade.php](resources/views/employer/jobs/create.blade.php) → `employer.job_form.*`
  - [resources/views/worker/dashboard.blade.php](resources/views/worker/dashboard.blade.php) → `worker.dashboard.*` (first pass)
  - [resources/views/errors/404.blade.php](resources/views/errors/404.blade.php), [resources/views/errors/403.blade.php](resources/views/errors/403.blade.php), [resources/views/errors/500.blade.php](resources/views/errors/500.blade.php) → `errors.*`
- Added/expanded locale files:
  - [lang/en/errors.php](lang/en/errors.php)
  - [lang/hr/errors.php](lang/hr/errors.php)
  - [lang/en/employer.php](lang/en/employer.php)
  - [lang/hr/employer.php](lang/hr/employer.php)
  - [lang/en/worker.php](lang/en/worker.php)
  - [lang/hr/worker.php](lang/hr/worker.php)

## What Was Removed / Reduced

### CSS complexity reductions
- Reduced `!important` usage in dark placeholder overrides in [resources/css/consolidation-overrides.css](resources/css/consolidation-overrides.css)
  - Before scan: 44 matches
  - After scan: 42 matches
- Reduced repeated inline style patterns for dynamic width/rotate on major dashboard surfaces by introducing canonical helpers and moving logic into reusable classes

### Inline style pressure
- Global inline styles remain high (76 matches in Blade templates after this sprint pass), but dashboard hotspots were normalized
- Significant remaining inline style usage is concentrated in ambient/decorative orbs, mobile nav transitional state styles, and email templates

## Translation Parity Status

Command run:
- `php artisan crowork:translations:check --locales=en,hr`

Current parity output:
- Base keys (en): 1435
- hr keys: 1515
- Missing in hr: 14
- Extra in hr: 94
- Placeholder mismatches: 1 (`auth.by_continuing`)

Status:
- Governance tooling is now in place and operational
- Locale drift still exists (mostly around `resources.guides.*` and several `ui.*` keys)
- Missing-key runtime logging is now active for ongoing detection

## CSS Complexity Improvements

Implemented improvements:
- Canonicalized shell spacing, card surfaces, empty-state treatment, progress bars, and ring transforms
- Reduced duplicate class chains on employer dashboard cards
- Reduced per-view CSS micro-variation by introducing reusable utilities

Observed current metrics:
- `!important` usages across CSS: 42
- Blade inline style attributes: 76

Interpretation:
- Architecture is measurably cleaner in key dashboards
- Full consolidation requires additional passes across auth/header/resources/email templates and listing pages

## Remaining UI Inconsistencies

1. Hardcoded copy remains in additional employer/worker views:
- [resources/views/employer/jobs/edit.blade.php](resources/views/employer/jobs/edit.blade.php)
- [resources/views/employer/jobs/index.blade.php](resources/views/employer/jobs/index.blade.php)
- [resources/views/employer/applications/candidate.blade.php](resources/views/employer/applications/candidate.blade.php)
- [resources/views/employer/applications/pipeline.blade.php](resources/views/employer/applications/pipeline.blade.php)
- [resources/views/worker/profile-edit.blade.php](resources/views/worker/profile-edit.blade.php)
- [resources/views/worker/profile-preview.blade.php](resources/views/worker/profile-preview.blade.php)

2. Inline-style heavy zones still require extraction:
- [resources/views/components/site-header.blade.php](resources/views/components/site-header.blade.php)
- [resources/views/layouts/app.blade.php](resources/views/layouts/app.blade.php)
- [resources/views/layouts/guest.blade.php](resources/views/layouts/guest.blade.php)
- [resources/views/auth/access.blade.php](resources/views/auth/access.blade.php)
- [resources/views/pages/resources/index.blade.php](resources/views/pages/resources/index.blade.php)

3. Dashboard/public family alignment still partial:
- Employer and worker dashboards now closer to shared system primitives
- Several secondary dashboard views still use ad hoc class stacks and local visual patterns

## Remaining Technical Debt

1. Translation drift backlog
- Resolve missing 14 hr keys and 94 extra hr keys against en baseline
- Fix placeholder mismatch in `auth.by_continuing`

2. Hardcoded-string migration backlog
- Continue migrations in employer/worker secondary views and partials
- Apply namespace rules consistently (`employer.*`, `worker.*`, `errors.*`, `ui.*`)

3. CSS consolidation backlog
- Replace remaining inline style usage for decorative and interaction states with tokenized classes
- Reduce `!important` further in dark mode override surfaces

4. Command/process hardening
- Integrate parity command into CI with fail gate: `php artisan crowork:translations:check --locales=en,hr --fail-on-missing`

## Validation Results

Executed successfully:
- `npm run build`
- `composer dump-autoload`
- `php artisan optimize:clear`
- `php artisan view:cache`
- `php artisan crowork:translations:check --locales=en,hr`

Blade / lint errors:
- No workspace errors reported by diagnostics after refactor

Manual QA status:
- Manual browser QA for Home, Jobs, Educations, Resources, About, employer pages, worker dashboard, employer dashboard, mobile menu, dark mode, and translations is still pending and should be run as a dedicated visual pass.

## Architecture Recommendations

1. Enforce translation governance in CI
- Make parity check a required pipeline step with fail-on-missing
- Keep missing-key logging active in non-local environments with log sampling

2. Continue design-system extraction by layer
- Phase A: dashboard secondary views (employer/worker subpages)
- Phase B: auth and global nav components
- Phase C: editorial/resources pages and emails

3. Define and enforce no-hardcoded-copy rule
- Disallow direct user-facing string literals in Blade except approved technical cases

4. Consolidate dynamic style patterns
- Standardize custom-property-driven classes for widths, transforms, and transitions
- Remove repeated inline style snippets from shared components

5. Adopt a rolling cleanup model
- Every feature PR should include at least one local debt cleanup item in touched files (string extraction, class consolidation, or style-token migration)
