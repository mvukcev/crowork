# Regression Checkpoint — 2026-05-17

Scope: Core layout + analytics regression QA only.

Constraints respected:

- No new features added
- No performance optimization
- No redesign
- No refactor
- No source code changes made during this checkpoint

## Commands Run

1. `npm run build`
2. `php artisan optimize:clear`
3. `php artisan view:cache`

Result: All passed successfully.

## Tested Pages

- `http://127.0.0.1:8001/` (homepage)
- `http://127.0.0.1:8001/access` (access/login)
- `http://127.0.0.1:8001/jobs` (jobs listing)
- `http://127.0.0.1:8001/resources` (resources)

## Pass/Fail Matrix

| # | Check | Result | Evidence |
|---|---|---|---|
| 1 | `app.blade.php` no longer crashes | PASS | Homepage status 200, no runtime crash markers |
| 2 | `guest.blade.php` no visible PHP/Blade artifacts | PASS | `/access` status 200, no Blade/PHP snippets in body text |
| 3 | Homepage loads cleanly | PASS | `/` status 200, clean title/content |
| 4 | Access/login loads cleanly | PASS | `/access` status 200, clean title/content |
| 5 | Jobs listing loads cleanly | PASS | `/jobs` status 200, clean render |
| 6 | Resources page loads cleanly | PASS | `/resources` status 200, clean render |
| 7 | No visible `@php` / `@endphp` / variable artifacts | PASS | Pattern scan on all tested pages returned no matches |
| 8 | No console errors | PASS | Captured browser `console.error` + `pageerror`: none |
| 9 | `cwTrack` exists | PASS | `typeof window.cwTrack === 'function'` on tested pages |
| 10 | `cw:analytics` bus emits events | PASS | Probe event `qa_event_bus_probe` emitted and captured |
| 11 | Consent gating does not block listener registration | PASS | With missing consent, resources FAQ click emitted `faq_open` |
| 12 | Providers suppressed when consent missing | PASS | Forced provider-enabled state + missing consent => all counters 0 |
| 13 | Providers fire only when consent present | PASS | Forced provider-enabled state + full consent => `dataLayer/gtag/fbq/plausible` counters > 0 |
| 14 | Mobile menu opens at top and sticky state | PASS | Mobile toggle opens panel (`display:flex`, `aria-expanded:true`), header remains `position:fixed`, top `0` after scroll |
| 15 | Theme/language dropdowns still work | PASS | Desktop triggers open both dropdown panels (`aria-hidden:false`, visible) |
| 16 | Dark/light mode still works | PASS | `cwTheme.setPreference('dark'/'light')` updates `documentElement.dataset.theme` accordingly |

## Errors Found

- None during this checkpoint run.

## Fixes Applied During This Checkpoint

- None.

## Final Stability Verdict

PASS — Core layout and analytics regression checkpoint is stable for the validated scope.
