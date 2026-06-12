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
