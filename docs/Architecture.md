# Architecture

## Overview
CroWork is a Laravel 11 platform that combines public SEO pages, authenticated worker and employer workflows, and a Filament-based admin backend.

## Main Layers
- Public web: marketing pages, jobs, educations, resources, legal pages.
- Unified auth/access: OTP-first flow for login/register/verification.
- Worker area: profile/CV, privacy controls, applications tracking.
- Employer area: jobs management and ATS pipeline.
- Admin area (Filament): content, moderation, settings, audit, health, bugs/error logs.

## Core Domains
- Users and roles: worker, employer, admin, moderator.
- Hiring: jobs, applications, status transitions, internal notes.
- Education: education listings and applications.
- Content: legal pages, resource posts, translation overrides.
- Notifications/mail: template-driven messages with queue delivery.
- Governance: audit logs, failed jobs, system health, GDPR/retention.
- HZZ integration: official-source jobs, dual application flow, contractual analytics/reporting.

## HZZ Module (CroWork-first Apply)
- Source metadata on jobs: `source_system`, `source_reference`, `source_url`, parsed apply contact fields.
- Contact parser service: `App\\Services\\Hzz\\HzzApplicationContactParser` extracts employer email and fallback apply URL.
- Import pipeline: `crowork:hzz-import --url=...` maps external feed items into `job_postings` with parser enrichment.
- Dual apply runtime:
	- Scenario 1 (`hzz_apply_email` exists): candidate applies inside CroWork, application sent by email via `HzzApplicationService`.
	- Scenario 2 (no email): candidate completes profile first, then controlled external redirect (`jobs.hzz.open`).
- Application persistence: `job_applications` stores channel (`internal`, `hzz_email`, `hzz_external`), CV metadata, cover-letter metadata, submission status/log.

## HZZ Analytics and Reporting
- Event store table: `hzz_job_analytics_events` for `view`, `cta_click`, `external_open`, `application_sent`.
- Tracker service: `App\\Services\\Hzz\\HzzAnalyticsTracker` records server-side events with session and user context.
- Admin analytics: Filament page `HzzAnalytics` for overview, per-day metrics, per-job metrics.
- Quality control: Filament page `HzzQualityCheck` for missing-email/missing-source validation and quick admin correction.
- Exports:
	- CSV/XLSX monthly detailed report via `admin/hzz-analytics/export/{format}`.
	- Includes job id/title/slug, date/time of views, total & unique views, CTA clicks, external opens, sent-via-CroWork count, CTR.

## Extensibility Path
- Apply channels are explicit (`JobApplication::CHANNEL_*`) to support future modes: Easy Apply, LinkedIn Apply, API apply, ATS connectors.
- Submission statuses are explicit (`JobApplication::SUBMISSION_*`) for future delivery lifecycle (queued, delivered, bounced, etc.).
- HZZ module is service-based (`App\\Services\\Hzz\\*`) so new providers can follow the same parser/tracker/application interfaces.

## Key Technical Decisions
- SSR Blade + Tailwind for predictable performance and SEO.
- Filament resources/pages for admin operations.
- Queue workers for notifications and background processing.
- Settings registry (`Setting::DEFINITIONS`) as centralized runtime config.
- Translation manager supports nested keys via dot notation.

## Operational Components
- Scheduler for periodic jobs and retention automation.
- Queue worker for mails/notifications.
- Email failover strategy (SMTP + log fallback).
- Bug reporting pipeline with error-log snapshot capture.

## Data Stores
- Main relational database (MySQL in production).
- File storage via Laravel disks (`public`, optional S3-compatible backends).

## Security and Reliability Baseline
- Role-based admin module access.
- Consent and legal reacceptance middleware.
- Audit logging for admin write actions.
- Centralized exception capture to `error_logs` table for diagnostics.
