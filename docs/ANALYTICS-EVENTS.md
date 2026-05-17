# Analytics Events

This document defines the canonical client-side analytics events used across CroWork.

## Principles

- Consent-first: events are dispatched to providers only when consent allows.
- Analytics consent gates analytics providers (`dataLayer`, `gtag`, `plausible`).
- Marketing consent gates Meta Pixel (`fbq`) and marketing-scoped events.
- Event payloads are privacy-safe and do not include raw PII.
- Raw query/message/text values are not sent; only derived metadata (for example `query_length`).
- Event names are normalized through aliases to avoid taxonomy drift.

## Core Payload

All events include:

- `event_id`
- `locale`
- `page_type`
- `role`
- `path`

Optional fields are event-specific, for example:

- `item_type`
- `item_slug`
- `query_length`
- `source`
- `account_type`

## Canonical Events

### Public discovery

- `homepage_search`
- `job_search`
- `education_search`
- `filter_open`
- `filter_apply`
- `filter_clear`
- `language_switch`
- `theme_switch`

### Jobs

- `job_view`
- `job_apply_click`
- `job_apply_submit`
- `company_profile_click`
- `employer_logo_click`

### Educations

- `education_view`
- `education_apply_click`
- `education_apply_submit`

### Resources

- `resource_view`
- `resource_search`
- `faq_open`
- `guide_open`

### Employer conversion

- `employer_cta_click`
- `post_job_click`
- `employer_register_start`
- `employer_register_complete`

### Auth

- `access_start`
- `login_success`
- `register_start`
- `register_complete`
- `verification_code_sent`
- `verification_success`
- `password_reset_request`

### Dashboard

- `worker_profile_update`
- `employer_branding_update`
- `employer_job_create`
- `employer_job_publish`

## Legacy Alias Mapping

Examples of normalized legacy names:

- `language_change` -> `language_switch`
- `theme_change` -> `theme_switch`
- `job_filter_open` -> `filter_open`
- `job_filter_apply` -> `filter_apply`
- `job_filter_reset` -> `filter_clear`
- `education_filter_open` -> `filter_open`
- `education_filter_apply` -> `filter_apply`
- `education_filter_reset` -> `filter_clear`
- `job_application_submit` -> `job_apply_submit`
- `education_application_submit` -> `education_apply_submit`
- `registration_start` -> `register_start`
- `registration_complete` -> `register_complete`
- `email_verification_resend` -> `verification_code_sent`
- `email_verification_completed` -> `verification_success`

## Provider Mapping

### GA4 / GTM

- Sends canonical event names directly.
- Transport: `window.dataLayer.push({ event, ...payload })` and `window.gtag('event', event, payload)` when analytics consent is allowed.

### Meta Pixel

Meta events are sent only when marketing consent is allowed.

- `job_view`, `education_view`, `resource_view`, `guide_open` -> `track('ViewContent')`
- `job_search`, `education_search`, `resource_search` -> `track('Search')`
- `employer_cta_click`, `post_job_click` -> `track('Lead')`
- `register_complete`, `employer_register_complete` -> `track('CompleteRegistration')`
- `job_apply_submit` -> `trackCustom('JobApplySubmit')`
- `education_apply_submit` -> `trackCustom('EducationApplySubmit')`
- `password_reset_request` -> `trackCustom('PasswordResetRequest')`

## Auth Success Semantics

For success-only auth signals (`login_success`, `register_complete`, `employer_register_complete`, `verification_success`, `verification_code_sent`), server-confirmed events are queued in session and emitted on next page load.

This avoids false positives from failed form submissions.

## QA Checklist

- Reject consent and verify providers do not receive analytics/marketing events.
- Accept analytics-only consent and verify analytics events fire.
- Accept full consent and verify marketing events fire where relevant.
- Confirm no duplicate events for single interactions.
- Verify canonical names in `dataLayer` and analytics debug output.
- Check auth flow emits success events only after successful transitions.
- Validate resource interactions (`resource_search`, `faq_open`, `guide_open`).
- Validate dashboard updates (`worker_profile_update`, `employer_branding_update`, `employer_job_create`, `employer_job_publish`).
