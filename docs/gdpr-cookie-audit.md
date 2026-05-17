# GDPR P1 Cookie and Tracking Audit (CroWork)

Date: 2026-05-17
Scope: Public pages, auth/access flow, worker/employer app layouts, tracking scripts, client storage, and server-side conversion tracking.

## 1. Script and Source Inventory

### First-party scripts
- `resources/js/app.js`
  - Central tracking wrapper (`window.cwTrack`), consent checks, event aliasing, payload sanitization.
  - Cookie banner logic, language/theme tracking hooks, form/click tracking hooks.

### Third-party analytics/marketing scripts
- Google Tag Manager (head and noscript)
  - Injected from `resources/views/components/analytics-head.blade.php` and `resources/views/components/analytics-noscript.blade.php`.
  - Source: `https://www.googletagmanager.com/gtm.js` and `https://www.googletagmanager.com/ns.html`.
- Google Analytics 4
  - Injected from `resources/views/components/analytics-head.blade.php`.
  - Source: `https://www.googletagmanager.com/gtag/js`.
- Meta Pixel
  - Injected from `resources/views/components/analytics-head.blade.php` and `resources/views/components/analytics-noscript.blade.php`.
  - Sources: `https://connect.facebook.net/en_US/fbevents.js`, `https://www.facebook.com/tr`.

### Server-to-server tracking
- Meta Conversions API
  - Service: `app/Services/MetaConversionsAPIService.php`.
  - Queue job: `app/Jobs/SendMetaCapiEvent.php`.
  - Trigger points: registration completion, job applications, education applications, job application status changes.

## 2. Cookie Inventory

### Essential cookies
- Laravel session cookie (`<app>_session`)
  - Purpose: authenticated session state.
  - Config: `config/session.php` (`driver=database`, `same_site=lax`, `http_only=true`).
- `XSRF-TOKEN` (Laravel standard)
  - Purpose: CSRF protection for browser requests.
- `remember_web_*` (Laravel standard, optional)
  - Purpose: remember-me authentication persistence.

### Functional/preferences cookies
- `cw_theme`
  - Purpose: UI theme preference (light/dark/system).
  - Writer: `resources/js/app.js`.

### Consent cookies
- `consent_analytics`
  - Purpose: analytics consent state (`1`/`0`).
  - Writers: banner JS and server consent endpoint.
- `consent_marketing`
  - Purpose: marketing consent state (`1`/`0`).
  - Writers: banner JS and server consent endpoint.
- `cw_cookie_choice`
  - Purpose: high-level choice (`required`, `all`, `custom`).
  - Writers: banner JS and server consent endpoint.

## 3. localStorage/sessionStorage Inventory

### Consent-related localStorage
- `cw_cookie_choice`
- `crowork_consent` (JSON with analytics, marketing, timestamp, choice)

### Theme-related localStorage
- `cw-theme`
- `cw_theme_preference` (legacy)
- `theme` (Filament compatibility)

### UX helper localStorage (non-tracking)
- `cw_recent_job_cities`
- `cw_recent_education_cities`

### sessionStorage
- No significant consent/tracking sessionStorage usage discovered.

## 4. Consent Risk Assessment

### High
- Prior to P1 updates, server-side Meta CAPI calls could run on business events even when user marketing consent was not explicitly confirmed in current request context.

### Medium
- Consent state existed in multiple locations (cookies + localStorage + server checks), with limited server persistence of tracking preference changes.
- Cookie banner interactions were binary (`required`/`all`) with no first-class customize path.

### Low
- Legacy/unused `resources/views/components/cookie-banner.blade.php` exists and can cause implementation drift if reused accidentally.

## 5. Pre-Consent Firing Analysis

### Browser-side (GTM/GA4/Pixel)
- Head/noscript injection paths are gated by server consent checks (`ConsentConfigService::isAnalyticsAllowed()` and `isMarketingAllowed()`).
- `cwTrack` additionally blocks dispatch to `gtag`/`fbq` when consent is not granted.

### Server-side (Meta CAPI)
- Registration, application submit, and queued events were previously triggered without robust user-consent gating in every path.
- P1 implementation adds marketing-consent checks before dispatch/calls and in queue handling as a defense-in-depth guard.

## 6. Recommended Gating Strategy

1. Consent categories
- Functional: always enabled (session, security, auth).
- Analytics: GTM/GA4 and analytics events.
- Marketing: Meta Pixel and Meta CAPI.

2. Source of truth model
- Runtime gating: consent cookies (`consent_analytics`, `consent_marketing`).
- Audit trail/history: `consent_histories` entries per consent category and change event.
- Client UX continuity: localStorage mirror for faster UI behavior.

3. Enforcement points
- Blade script injection: consent-aware checks.
- JS event dispatch (`cwTrack`): consent-aware checks.
- Server conversion calls: consent-aware checks by user/request and latest persisted preference.

4. Preference updates
- Public endpoint: `POST /consent/preferences` to persist cookies + optional user history.
- Worker settings: privacy page update action to withdraw/grant tracking preferences.

## 7. Implementation Priority

1. P1-Now (implemented)
- Add consent preference persistence service and endpoint.
- Extend consent helper methods (`hasAnalyticsConsent`, `hasMarketingConsent`, `isFunctionalAllowed`).
- Add customizable banner/modal controls.
- Add worker privacy tracking preference management.
- Gate non-essential server-side Meta events by marketing consent.

2. P1-Next
- Remove or archive legacy unused banner component.
- Expand legal copy to explicitly reflect category-level controls in all locales.
- Add telemetry-free QA checklist for consent regression scenarios.

3. P2 (deferred)
- Enterprise-grade consent ledger UI/reporting.
- Advanced CMP workflows and geo/policy version orchestration.

## 8. Affected Routes, Layouts, Components, Services

### Routes
- `POST /consent/preferences` (`consent.preferences.update`)
- `PATCH /worker/privacy/consent` (`worker.privacy.consent`)

### Layouts and views
- `resources/views/layouts/app.blade.php`
- `resources/views/layouts/guest.blade.php`
- `resources/views/auth/access.blade.php`
- `resources/views/worker/privacy.blade.php`
- `resources/views/components/analytics-head.blade.php`
- `resources/views/components/analytics-noscript.blade.php`

### Services and backend tracking
- `app/Services/ConsentConfigService.php`
- `app/Services/CookieConsentService.php`
- `app/Services/MetaConversionsAPIService.php`
- `app/Jobs/SendMetaCapiEvent.php`
- `app/Http/Controllers/CookieConsentController.php`
- `app/Http/Controllers/WorkerPrivacyController.php`
- `app/Http/Controllers/Auth/AccessController.php`
- `app/Http/Controllers/JobApplicationController.php`
- `app/Http/Controllers/EducationApplicationController.php`

## 9. P1 Outcome Summary

- Non-essential browser and server tracking is now consent-aware by default.
- Consent changes are persisted in cookies and, for authenticated users, in `consent_histories` with metadata (version, source, accepted_at, ip, user_agent).
- Workers can manage and withdraw tracking preferences from privacy settings.
- Existing auth/session behavior remains unchanged (functional cookies unaffected).
